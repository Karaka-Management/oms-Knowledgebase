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
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Knowledgebase\Models;

use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;
use phpOMS\Localization\BaseStringL11n;

/**
 * Category mapper class.
 *
 * @package Modules\Knowledgebase\Models
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class WikiCategoryL11nMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'wiki_category_l11n_id'            => ['name' => 'wiki_category_l11n_id',       'type' => 'int',    'internal' => 'id'],
        'wiki_category_l11n_name'          => ['name' => 'wiki_category_l11n_name',    'type' => 'string', 'internal' => 'content', 'autocomplete' => true],
        'wiki_category_l11n_category'      => ['name' => 'wiki_category_l11n_category',      'type' => 'int',    'internal' => 'ref'],
        'wiki_category_l11n_language'      => ['name' => 'wiki_category_l11n_language', 'type' => 'string', 'internal' => 'language'],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'wiki_category_l11n';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'wiki_category_l11n_id';

    /**
     * Model to use by the mapper.
     *
     * @var class-string
     * @since 1.0.0
     */
    public const MODEL = BaseStringL11n::class;
}
