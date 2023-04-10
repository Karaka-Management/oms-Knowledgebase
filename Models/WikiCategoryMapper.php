<?php
/**
 * Karaka
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

use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Mapper class.
 *
 * @package Modules\Knowledgebase\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of WikiCategory
 * @extends DataMapperFactory<T>
 */
final class WikiCategoryMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'wiki_category_id'      => ['name' => 'wiki_category_id',      'type' => 'int',    'internal' => 'id'],
        'wiki_category_app'     => ['name' => 'wiki_category_app',     'type' => 'int',    'internal' => 'app'],
        'wiki_category_virtual' => ['name' => 'wiki_category_virtual', 'type' => 'string', 'internal' => 'virtualPath'],
        'wiki_category_parent'  => ['name' => 'wiki_category_parent',  'type' => 'int',    'internal' => 'parent'],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:class-string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    public const HAS_MANY = [
        'name' => [
            'mapper'   => WikiCategoryL11nMapper::class,
            'table'    => 'wiki_category_l11n',
            'self'     => 'wiki_category_l11n_category',
            'column'   => 'content',
            'external' => null,
        ],
    ];

    /**
     * Belongs to.
     *
     * @var array<string, array{mapper:class-string, external:string, column?:string, by?:string}>
     * @since 1.0.0
     */
    public const BELONGS_TO = [
        'parent' => [
            'mapper'   => self::class,
            'external' => 'wiki_category_parent',
        ],
        'app' => [
            'mapper'   => WikiAppMapper::class,
            'external' => 'wiki_category_app',
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'wiki_category';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'wiki_category_id';

    /**
     * Parent field name.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $parent = 'wiki_category_parent';
}
