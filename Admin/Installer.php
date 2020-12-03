<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\Knowledgebase\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\Knowledgebase\Admin;

use Modules\Knowledgebase\Models\NullWikiApp;
use Modules\Knowledgebase\Models\WikiApp;
use Modules\Knowledgebase\Models\WikiAppMapper;
use Modules\Knowledgebase\Models\WikiCategory;
use Modules\Knowledgebase\Models\WikiCategoryMapper;
use Modules\Organization\Models\UnitMapper;
use phpOMS\Config\SettingsInterface;
use phpOMS\DataStorage\Database\DatabasePool;
use phpOMS\Module\InstallerAbstract;
use phpOMS\Module\ModuleInfo;

/**
 * Installer class.
 *
 * @package Modules\Knowledgebase\Admin
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
final class Installer extends InstallerAbstract
{
    /**
     * {@inheritdoc}
     */
    public static function install(DatabasePool $dbPool, ModuleInfo $info, SettingsInterface $cfgHandler) : void
    {
        parent::install($dbPool, $info, $cfgHandler);

        $app       = new WikiApp();
        $app->name = 'Default';

        $id = WikiAppMapper::create($app);

        $category      = new WikiCategory();
        $category->app = new NullWikiApp($id);
        $category->setName('Default');

        WikiCategoryMapper::create($category);

        // @todo: create hook for when a new unit is created
        $units = UnitMapper::getAll();
        foreach ($units as $unit) {
            $app       = new WikiApp();
            $app->name = $unit->name;

            $id = WikiAppMapper::create($app);

            $category      = new WikiCategory();
            $category->app = new NullWikiApp($id);
            $category->setName('Default');
        }
    }
}
