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

use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * WikiApp mapper class.
 *
 * @package Modules\Knowledgebase\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of WikiApp
 * @extends DataMapperFactory<T>
 */
final class WikiAppMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'wiki_app_id'   => ['name' => 'wiki_app_id',   'type' => 'int',    'internal' => 'id'],
        'wiki_app_unit' => ['name' => 'wiki_app_unit', 'type' => 'int', 'internal' => 'unit'],
        'wiki_app_name' => ['name' => 'wiki_app_name', 'type' => 'string', 'internal' => 'name'],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'wiki_app';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'wiki_app_id';
}
