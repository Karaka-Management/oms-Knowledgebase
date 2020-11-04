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

use Modules\Tag\Models\TagMapper;
use phpOMS\DataStorage\Database\DataMapperAbstract;
use phpOMS\DataStorage\Database\RelationType;
use phpOMS\DataStorage\Database\Query\Builder;

/**
 * Mapper class.
 *
 * @package Modules\Knowledgebase\Models
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
final class WikiDocMapper extends DataMapperAbstract
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    protected static array $columns = [
        'wiki_article_id'       => ['name' => 'wiki_article_id',       'type' => 'int',    'internal' => 'id'],
        'wiki_article_app'      => ['name' => 'wiki_article_app',      'type' => 'int',    'internal' => 'app'],
        'wiki_article_title'    => ['name' => 'wiki_article_title',    'type' => 'string', 'internal' => 'name'],
        'wiki_article_language' => ['name' => 'wiki_article_language', 'type' => 'string', 'internal' => 'language'],
        'wiki_article_doc'      => ['name' => 'wiki_article_doc',      'type' => 'string', 'internal' => 'doc'],
        'wiki_article_docraw'      => ['name' => 'wiki_article_docraw',      'type' => 'string', 'internal' => 'docRaw'],
        'wiki_article_status'   => ['name' => 'wiki_article_status',   'type' => 'int',    'internal' => 'status'],
        'wiki_article_category' => ['name' => 'wiki_article_category', 'type' => 'int',    'internal' => 'category'],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    protected static array $hasMany = [
        'tags' => [
            'mapper'   => TagMapper::class,
            'table'    => 'wiki_tag',
            'self'     => 'wiki_tag_dst',
            'external' => 'wiki_tag_src',
        ],
    ];

    /**
     * Has owns one relation.
     *
     * @var array<string, array<string, null|string>>
     * @since 1.0.0
     */
    protected static array $belongsTo = [
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
    protected static string $table = 'wiki_article';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $primaryField = 'wiki_article_id';

    /**
     * Get newest.
     *
     * This will fall back to the insert id if no datetime column is present.
     *
     * @param int     $app       App
     * @param int     $limit     Newest limit
     * @param Builder $query     Pre-defined query
     * @param int     $relations Load relations
     * @param int     $depth     Relation depth
     *
     * @return array
     *
     * @since 1.0.0
     */
    public static function getNewestByApp(int $app, int $limit = 1, Builder $query = null, int $relations = RelationType::ALL, int $depth = 3) : array
    {
        $query ??= self::getQuery(null, [], $relations, $depth);

        $query->where(static::$table . '_' . $depth . '.' . 'wiki_article_app', '=', $app)
            ->limit($limit);

        if (!empty(static::$createdAt)) {
            $query->orderBy(static::$table  . '_' . $depth . '.' . static::$columns[static::$createdAt]['name'], 'DESC');
        } else {
            $query->orderBy(static::$table  . '_' . $depth . '.' . static::$columns[static::$primaryField]['name'], 'DESC');
        }

        return self::getAllByQuery($query, $relations, $depth);
    }
}
