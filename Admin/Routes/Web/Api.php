<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\Knowledgebase\Controller\ApiController;
use Modules\Knowledgebase\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^.*/wiki/doc(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Knowledgebase\Controller\ApiController:apiWikiDocCreate',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::WIKI,
            ],
        ],
        [
            'dest'       => '\Modules\Knowledgebase\Controller\ApiController:apiWikiDocUpdate',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::WIKI,
            ],
        ],
        [
            'dest'       => '\Modules\Knowledgebase\Controller\ApiController:apiWikiDocGet',
            'verb'       => RouteVerb::GET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::WIKI,
            ],
        ],
        [
            'dest'       => '\Modules\Knowledgebase\Controller\ApiController:apiWikiDocDelete',
            'verb'       => RouteVerb::DELETE,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::DELETE,
                'state'  => PermissionCategory::WIKI,
            ],
        ],
    ],

    '^.*/wiki/category(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Knowledgebase\Controller\ApiController:apiWikiCategoryCreate',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::CATEGORY,
            ],
        ],
        [
            'dest'       => '\Modules\Knowledgebase\Controller\ApiController:apiWikiCategoryUpdate',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::CATEGORY,
            ],
        ],
        [
            'dest'       => '\Modules\Knowledgebase\Controller\ApiController:apiWikiCategoryGet',
            'verb'       => RouteVerb::GET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::CATEGORY,
            ],
        ],
        [
            'dest'       => '\Modules\Knowledgebase\Controller\ApiController:apiWikiCategoryDelete',
            'verb'       => RouteVerb::DELETE,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::DELETE,
                'state'  => PermissionCategory::CATEGORY,
            ],
        ],
    ],

    '^.*/wiki/app(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Knowledgebase\Controller\ApiController:apiWikiAppCreate',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::APP,
            ],
        ],
        [
            'dest'       => '\Modules\Knowledgebase\Controller\ApiController:apiWikiAppUpdate',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::APP,
            ],
        ],
        [
            'dest'       => '\Modules\Knowledgebase\Controller\ApiController:apiWikiAppGet',
            'verb'       => RouteVerb::GET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::APP,
            ],
        ],
        [
            'dest'       => '\Modules\Knowledgebase\Controller\ApiController:apiWikiAppDelete',
            'verb'       => RouteVerb::DELETE,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::DELETE,
                'state'  => PermissionCategory::APP,
            ],
        ],
    ],

    '^.*/wiki/category/l11n$' => [
        [
            'dest'       => '\Modules\Knowledgebase\Controller\ApiController:apiWikiCategoryL11nCreate',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::CATEGORY,
            ],
        ],
        [
            'dest'       => '\Modules\Knowledgebase\Controller\ApiController:apiWikiCategoryL11nUpdate',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::CATEGORY,
            ],
        ],
    ],
];
