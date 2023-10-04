<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\Knowledgebase\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Knowledgebase\Models;

use Modules\Admin\Models\Account;
use Modules\Admin\Models\NullAccount;
use Modules\Media\Models\Media;
use Modules\Tag\Models\Tag;
use phpOMS\Localization\ISO639x1Enum;

/**
 * Wiki document class.
 *
 * @package Modules\Knowledgebase\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class WikiDoc implements \JsonSerializable
{
    /**
     * ID.
     *
     * @var int
     * @since 1.0.0
     */
    public int $id = 0;

    /**
     * Version.
     *
     * @var string
     * @since 1.0.0
     */
    public string $version = '';

    /**
     * App id.
     *
     * There can be different wikis
     *
     * @var null|WikiApp
     * @since 1.0.0
     */
    public ?WikiApp $app = null;

    /**
     * Name.
     *
     * @var string
     * @since 1.0.0
     */
    public string $name = '';

    /**
     * Article status.
     *
     * @var int
     * @since 1.0.0
     */
    public int $status = WikiStatus::ACTIVE;

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
     * Category.
     *
     * @var null|WikiCategory
     * @since 1.0.0
     */
    public ?WikiCategory $category = null;

    /**
     * Language.
     *
     * @var string
     * @since 1.0.0
     */
    public string $language = ISO639x1Enum::_EN;

    /**
     * Tags.
     *
     * @var Tag[]
     * @since 1.0.0
     */
    public array $tags = [];

    /**
     * Media files
     *
     * @var array
     * @since 1.0.0
     */
    public array $media = [];

    /**
     * Is versioned
     *
     * @var bool
     * @since 1.0.0
     */
    public bool $isVersioned = false;

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
     * Get status
     *
     * @return int
     *
     * @since 1.0.0
     */
    public function getStatus() : int
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param int $status Status
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setStatus(int $status) : void
    {
        $this->status = $status;
    }

    /**
     * Get tags
     *
     * @return array
     *
     * @since 1.0.0
     */
    public function getTags() : array
    {
        return $this->tags;
    }

    /**
     * Add tag
     *
     * @param Tag $tag Tag
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function addTag(Tag $tag) : void
    {
        $this->tags[] = $tag;
    }

    /**
     * Get all media
     *
     * @return Media[]
     *
     * @since 1.0.0
     */
    public function getMedia() : array
    {
        return $this->media;
    }

    /**
     * Add media
     *
     * @param Media $media Media to add
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function addMedia(Media $media) : void
    {
        $this->media[] = $media;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        return [
            'id'        => $this->id,
            'app'       => $this->app,
            'name'      => $this->name,
            'status'    => $this->status,
            'doc'       => $this->doc,
            'docRaw'    => $this->docRaw,
            'language'  => $this->language,
            'tags'      => $this->tags,
            'media'     => $this->media,
            'createdAt' => $this->createdAt,
            'createdBy' => $this->createdBy,
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
