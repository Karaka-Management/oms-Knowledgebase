<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Knowledgebase\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\Knowledgebase\Models;

use Modules\Admin\Models\Account;
use Modules\Admin\Models\NullAccount;
use phpOMS\Localization\ISO639x1Enum;

/**
 * Wiki document class.
 *
 * @package Modules\Knowledgebase\Models
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
class WikiDocHistory implements \JsonSerializable
{
    /**
     * ID.
     *
     * @var int
     * @since 1.0.0
     */
    protected int $id = 0;

    /**
     * Article ID.
     *
     * @var int
     * @since 1.0.0
     */
    public int $article = 0;

    /**
     * Version.
     *
     * @var string
     * @since 1.0.0
     */
    public string $version = '';

    /**
     * Name.
     *
     * @var string
     * @since 1.0.0
     */
    public string $name = '';

    /**
     * Document content.
     *
     * @var string
     * @since 1.0.0
     */
    public string $doc = '';

    /**
     * Document raw content.
     *
     * @var string
     * @since 1.0.0
     */
    public string $docRaw = '';

    /**
     * Created.
     *
     * @var \DateTimeImmutable
     * @since 1.0.0
     */
    public \DateTimeImmutable $createdAt;

    /**
     * Creator.
     *
     * @var Account
     * @since 1.0.0
     */
    public Account $createdBy;

    /**
     * Language.
     *
     * @var string
     * @since 1.0.0
     */
    private string $language = ISO639x1Enum::_EN;

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->createdBy = new NullAccount();
        $this->createdAt = new \DateTimeImmutable('now');
    }

    /**
     * Create history form model
     *
     * @param WikiDoc $doc Document
     *
     * @return self
     *
     * @since 1.0.0
     */
    public static function createFromDoc(WikiDoc $doc) : self
    {
        $hist            = new self();
        $hist->article   = $doc->getId();
        $hist->createdBy = $doc->createdBy;
        $hist->name      = $doc->name;
        $hist->doc       = $doc->doc;
        $hist->docRaw    = $doc->docRaw;
        $hist->version   = $doc->version;

        return $hist;
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
     * Get language
     *
     * @return string
     *
     * @since 1.0.0
     */
    public function getLanguage() : string
    {
        return $this->language;
    }

    /**
     * Set language
     *
     * @param string $language Language
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setLanguage(string $language) : void
    {
        $this->language = $language;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'doc'       => $this->doc,
            'docRaw'    => $this->docRaw,
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
