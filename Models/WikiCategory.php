<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Knowledgebase\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Knowledgebase\Models;

use phpOMS\Localization\BaseStringL11n;
use phpOMS\Localization\ISO639x1Enum;

/**
 * Wiki category class.
 *
 * @package Modules\Knowledgebase\Models
 * @license OMS License 2.2
 * @link    https://jingga.app
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
    public int $id = 0;

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
     * @var string|BaseStringL11n
     * @since 1.0.0
     */
    public string | BaseStringL11n $name = '';

    /**
     * Parent category.
     *
     * @var null|self
     * @since 1.0.0
     */
    public ?self $parent = null;

    /**
     * Path for organizing.
     *
     * @var string
     * @since 1.0.0
     */
    public string $virtualPath = '/';

    /**
     * Constructor
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->app = new NullWikiApp();
        $this->setL11n('');
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
        return $this->name instanceof BaseStringL11n ? $this->name->content : $this->name;
    }

    /**
     * Set name
     *
     * @param string|BaseStringL11n $name Tag article name
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setL11n(string | BaseStringL11n $name, string $lang = ISO639x1Enum::_EN) : void
    {
        if ($name instanceof BaseStringL11n) {
            $this->name = $name;
        } elseif ($this->name instanceof BaseStringL11n) {
            $this->name->content = $name;
        } else {
            $this->name           = new BaseStringL11n();
            $this->name->content  = $name;
            $this->name->language = $lang;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        return [
            'id'          => $this->id,
            'app'         => $this->app,
            'virtualPath' => $this->virtualPath,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize() : mixed
    {
        return $this->toArray();
    }
}
