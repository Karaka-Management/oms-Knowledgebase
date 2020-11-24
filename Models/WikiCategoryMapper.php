<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   Modules\Knowledgebase\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\Knowledgebase\Models;

use phpOMS\DataStorage\Database\DataMapperAbstract;
use phpOMS\DataStorage\Database\RelationType;

/**
 * Mapper class.
 *
 * @package Modules\Knowledgebase\Models
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
final class WikiCategoryMapper extends DataMapperAbstract
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    protected static array $columns = [
        'wiki_category_id'     => ['name' => 'wiki_category_id',     'type' => 'int',    'internal' => 'id'],
        'wiki_category_app'    => ['name' => 'wiki_category_app',    'type' => 'int',    'internal' => 'app'],
        'wiki_category_virtual'    => ['name' => 'wiki_category_virtual',    'type' => 'string',    'internal' => 'virtualPath'],
        'wiki_category_parent' => ['name' => 'wiki_category_parent', 'type' => 'int',    'internal' => 'parent'],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    protected static array $hasMany = [
        'name' => [
            'mapper'            => WikiCategoryL11nMapper::class,
            'table'             => 'wiki_category_l11n',
            'self'              => 'wiki_category_l11n_category',
            'column'            => 'name',
            'conditional'       => true,
            'external'          => null,
        ],
    ];

    /**
     * Has owns one relation.
     *
     * @var array<string, array<string, null|string>>
     * @since 1.0.0
     */
    protected static array $belongsTo = [
        'parent' => [
            'mapper'     => self::class,
            'external'   => 'wiki_category_parent',
        ],
        'app' => [
            'mapper'     => WikiAppMapper::class,
            'external'   => 'wiki_category_app',
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $table = 'wiki_category';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $primaryField = 'wiki_category_id';

    /**
     * Parent field name.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $parent = 'wiki_category_parent';

    /**
     * Get by parent.
     *
     * @param mixed $value Parent value id
     * @param int   $app   App
     * @param int   $depth Relation depth
     *
     * @return array
     *
     * @since 1.0.0
     */
    public static function getByParentAndApp($value, int $app = 1, int $depth = 3) : array
    {
        $query = self::getQuery();
        $query->where(static::$table . '_' . $depth . '.' . static::$parent, '=', $value)
            ->andWhere(static::$table . '_' . $depth . '.wiki_category_app', '=', $app);

        return self::getAllByQuery($query, RelationType::ALL, $depth);
    }

    /**
     * Get by app.
     *
     * @param int   $app   App
     * @param int   $depth Relation depth
     *
     * @return array
     *
     * @since 1.0.0
     */
    public static function getByApp(int $app, int $depth = 3) : array
    {
        $query = self::getQuery();
        $query->where(static::$table . '_' . $depth . '.wiki_category_app', '=', $app);

        return self::getAllByQuery($query, RelationType::ALL, $depth);
    }
}
