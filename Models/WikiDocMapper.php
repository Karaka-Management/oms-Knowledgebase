<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   Modules\Knowledgebase\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\Knowledgebase\Models;

use Modules\Media\Models\MediaMapper;
use Modules\Tag\Models\TagMapper;
use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Mapper class.
 *
 * @package Modules\Knowledgebase\Models
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
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
        'wiki_article_id'          => ['name' => 'wiki_article_id',       'type' => 'int',    'internal' => 'id'],
        'wiki_article_app'         => ['name' => 'wiki_article_app',      'type' => 'int',    'internal' => 'app'],
        'wiki_article_title'       => ['name' => 'wiki_article_title',    'type' => 'string', 'internal' => 'name'],
        'wiki_article_language'    => ['name' => 'wiki_article_language', 'type' => 'string', 'internal' => 'language'],
        'wiki_article_doc'         => ['name' => 'wiki_article_doc',      'type' => 'string', 'internal' => 'doc'],
        'wiki_article_docraw'      => ['name' => 'wiki_article_docraw',      'type' => 'string', 'internal' => 'docRaw'],
        'wiki_article_status'      => ['name' => 'wiki_article_status',   'type' => 'int',    'internal' => 'status'],
        'wiki_article_category'    => ['name' => 'wiki_article_category', 'type' => 'int',    'internal' => 'category'],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    public const HAS_MANY = [
        'tags' => [
            'mapper'   => TagMapper::class,
            'table'    => 'wiki_tag',
            'self'     => 'wiki_tag_dst',
            'external' => 'wiki_tag_src',
        ],
        'media'        => [
            'mapper'   => MediaMapper::class,
            'table'    => 'wiki_article_media',
            'external' => 'wiki_article_media_dst',
            'self'     => 'wiki_article_media_src',
        ],
    ];

    /**
     * Has owns one relation.
     *
     * @var array<string, array<string, null|string>>
     * @since 1.0.0
     */
    public const BELONGS_TO = [
        'category' => [
            'mapper'     => WikiCategoryMapper::class,
            'external'   => 'wiki_article_category',
        ],
        'app' => [
            'mapper'     => WikiAppMapper::class,
            'external'   => 'wiki_article_app',
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
    public const PRIMARYFIELD ='wiki_article_id';
}
