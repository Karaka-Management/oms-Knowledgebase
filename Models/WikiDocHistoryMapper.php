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

use Modules\Admin\Models\AccountMapper;
use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Mapper class.
 *
 * @package Modules\Knowledgebase\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of WikiDocHistory
 * @extends DataMapperFactory<T>
 */
final class WikiDocHistoryMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'wiki_article_versioned_id'       => ['name' => 'wiki_article_versioned_id',       'type' => 'int',    'internal' => 'id'],
        'wiki_article_versioned_version'  => ['name' => 'wiki_article_versioned_version',    'type' => 'string', 'internal' => 'version'],
        'wiki_article_versioned_title'    => ['name' => 'wiki_article_versioned_title',    'type' => 'string', 'internal' => 'name'],
        'wiki_article_versioned_language' => ['name' => 'wiki_article_versioned_language', 'type' => 'string', 'internal' => 'language'],
        'wiki_article_versioned_doc'      => ['name' => 'wiki_article_versioned_doc',      'type' => 'string', 'internal' => 'doc'],
        'wiki_article_versioned_docraw'   => ['name' => 'wiki_article_versioned_docraw',      'type' => 'string', 'internal' => 'docRaw'],
        'wiki_article_versioned_article'  => ['name' => 'wiki_article_versioned_article', 'type' => 'int', 'internal' => 'article'],
        'wiki_article_versioned_at'       => ['name' => 'wiki_article_versioned_at', 'type' => 'DateTimeImmutable', 'internal' => 'createdAt'],
        'wiki_article_versioned_by'       => ['name' => 'wiki_article_versioned_by', 'type' => 'int',               'internal' => 'createdBy'],
    ];

    /**
     * Belongs to.
     *
     * @var array<string, array{mapper:class-string, external:string, column?:string, by?:string}>
     * @since 1.0.0
     */
    public const BELONGS_TO = [
        'createdBy' => [
            'mapper'   => AccountMapper::class,
            'external' => 'wiki_article_versioned_by',
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'wiki_article_versioned';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'wiki_article_versioned_id';
}
