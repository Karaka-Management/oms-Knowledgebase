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

use phpOMS\Stdlib\Base\Enum;

/**
 * Wiki status enum.
 *
 * @package Modules\Knowledgebase\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
abstract class WikiStatus extends Enum
{
    public const ACTIVE = 1;

    public const INACTIVE = 2;

    public const DRAFT = 3;
}
