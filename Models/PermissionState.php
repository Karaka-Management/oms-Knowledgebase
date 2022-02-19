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

use phpOMS\Stdlib\Base\Enum;

/**
 * Permision state enum.
 *
 * @package Modules\Knowledgebase\Models
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
abstract class PermissionState extends Enum
{
    public const CATEGORY = 1;

    public const WIKI = 2;

    public const APP = 3;
}
