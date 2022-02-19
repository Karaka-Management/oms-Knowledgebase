<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   Modules\Knowledgebase
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

$apps = $this->getData('apps');

/** @var \phpOMS\Views\View $this */
echo $this->getData('nav')->render();
