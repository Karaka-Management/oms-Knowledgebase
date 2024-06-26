<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Knowledgebase\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Knowledgebase\Models;

use Modules\Admin\Models\AccountMapper;
use Modules\Media\Models\MediaMapper;
use Modules\Tag\Models\TagMapper;
use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * WikiDoc mapper class.
 *
 * @package Modules\Knowledgebase\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of WikiDoc
 * @extends DataMapperFactory<T>
 */
final class WikiDocMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'wiki_article_id'         => ['name' => 'wiki_article_id',       'type' => 'int',    'internal' => 'id'],
        'wiki_article_version'    => ['name' => 'wiki_article_version',    'type' => 'string', 'internal' => 'version'],
        'wiki_article_app'        => ['name' => 'wiki_article_app',      'type' => 'int',    'internal' => 'app'],
        'wiki_article_title'      => ['name' => 'wiki_article_title',    'type' => 'string', 'internal' => 'name'],
        'wiki_article_language'   => ['name' => 'wiki_article_language', 'type' => 'string', 'internal' => 'language'],
        'wiki_article_doc'        => ['name' => 'wiki_article_doc',      'type' => 'string', 'internal' => 'doc'],
        'wiki_article_docraw'     => ['name' => 'wiki_article_docraw',      'type' => 'string', 'internal' => 'docRaw'],
        'wiki_article_versioned'  => ['name' => 'wiki_article_versioned',      'type' => 'bool', 'internal' => 'isVersioned'],
        'wiki_article_status'     => ['name' => 'wiki_article_status',   'type' => 'int',    'internal' => 'status'],
        'wiki_article_category'   => ['name' => 'wiki_article_category', 'type' => 'int',    'internal' => 'category'],
        'wiki_article_created_at' => ['name' => 'wiki_article_created_at', 'type' => 'DateTimeImmutable', 'internal' => 'createdAt'],
        'wiki_article_created_by' => ['name' => 'wiki_article_created_by', 'type' => 'int',               'internal' => 'createdBy'],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:class-string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    public const HAS_MANY = [
        'tags' => [
            'mapper'   => TagMapper::class,
            'table'    => 'wiki_tag',
            'self'     => 'wiki_tag_dst',
            'external' => 'wiki_tag_src',
        ],
        'files' => [
            'mapper'   => MediaMapper::class,
            'table'    => 'wiki_article_media',
            'external' => 'wiki_article_media_dst',
            'self'     => 'wiki_article_media_src',
        ],
    ];

    /**
     * Belongs to.
     *
     * @var array<string, array{mapper:class-string, external:string, column?:string, by?:string}>
     * @since 1.0.0
     */
    public const BELONGS_TO = [
        'category' => [
            'mapper'   => WikiCategoryMapper::class,
            'external' => 'wiki_article_category',
        ],
        'app' => [
            'mapper'   => WikiAppMapper::class,
            'external' => 'wiki_article_app',
        ],
        'createdBy' => [
            'mapper'   => AccountMapper::class,
            'external' => 'wiki_article_created_by',
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'wiki_article';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'wiki_article_id';
}
