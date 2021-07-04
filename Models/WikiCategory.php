<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\Knowledgebase\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\Knowledgebase\Models;

use phpOMS\Localization\ISO639x1Enum;

/**
 * Wiki category class.
 *
 * @package Modules\Knowledgebase\Models
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
class WikiCategory implements \JsonSerializable
{
    /**
     * ID.
     *
     * @var int
     * @since 1.0.0
     */
    protected int $id = 0;

    /**
     * App id.
     *
     * There can be different wikis
     *
     * @var WikiApp
     * @since 1.0.0
     */
    public WikiApp $app;

    /**
     * Name.
     *
     * @var string|WikiCategoryL11n
     * @since 1.0.0
     */
    private $name = '';

    /**
     * Parent category.
     *
     * @var self
     * @since 1.0.0
     */
    public self $parent;

    /**
     * Path for organizing.
     *
     * @var string
     * @since 1.0.0
     */
    private string $virtualPath = '/';

    /**
     * Cosntructor
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->app    = new NullWikiApp();
        $this->parent = new NullWikiCategory();
    }

    /**
     * Get id.
     *
     * @return int Model id
     *
     * @since 1.0.0
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Get name
     *
     * @return string
     *
     * @since 1.0.0
     */
    public function getL11n() : string
    {
        return $this->name instanceof WikiCategoryL11n ? $this->name->name : $this->name;
    }

    /**
     * Set name
     *
     * @param string|WikiCategoryL11n $name Tag article name
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setL11n(string|WikiCategoryL11n $name, string $lang = ISO639x1Enum::_EN) : void
    {
        if ($name instanceof WikiCategoryL11n) {
            $this->name = $name;
        } elseif (isset($this->name) && $this->name instanceof WikiCategoryL11n) {
            $this->name->name = $name;
        } else {
            $this->name       = new WikiCategoryL11n();
            $this->name->name = $name;
            $this->name->setLanguage($lang);
        }
    }

    /**
     * Get the path
     *
     * @return string
     *
     * @since 1.0.0
     */
    public function getVirtualPath() : string
    {
        return $this->virtualPath;
    }

    /**
     * Set the path if file
     *
     * @param string $path Path to file
     *
     * @return mixed
     *
     * @since 1.0.0
     */
    public function setVirtualPath(string $path)
    {
        $this->virtualPath = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        return [
            'id'   => $this->id,
            'app'  => $this->app,
            'name' => $this->name,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
