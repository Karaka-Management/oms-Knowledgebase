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

use Modules\Knowledgebase\Controller\BackendController;
use Modules\Knowledgebase\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^/wiki(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Knowledgebase\Controller\BackendController:setUpBackend',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::WIKI,
            ],
        ],
    ],
    '^/wiki/dashboard(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Knowledgebase\Controller\BackendController:viewKnowledgebaseDashboard',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::WIKI,
            ],
        ],
    ],

    '^/wiki/category/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Knowledgebase\Controller\BackendController:viewKnowledgebaseCategoryList',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::CATEGORY,
            ],
        ],
    ],
    '^/wiki/category/view(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Knowledgebase\Controller\BackendController:viewKnowledgebaseCategory',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::CATEGORY,
            ],
        ],
    ],
    '^/wiki/category/create(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Knowledgebase\Controller\BackendController:viewKnowledgebaseCategoryCreate',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::CATEGORY,
            ],
        ],
    ],

    '^/wiki/doc/view(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Knowledgebase\Controller\BackendController:viewKnowledgebaseDoc',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::WIKI,
            ],
        ],
    ],
    '^/wiki/doc/create(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Knowledgebase\Controller\BackendController:viewKnowledgebaseDocCreate',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::WIKI,
            ],
        ],
    ],
    '^/wiki/doc/edit(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Knowledgebase\Controller\BackendController:viewKnowledgebaseDocEdit',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::WIKI,
            ],
        ],
    ],
    '^/wiki/doc/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Knowledgebase\Controller\BackendController:viewKnowledgebaseDocList',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::WIKI,
            ],
        ],
    ],

    '^/wiki/app/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Knowledgebase\Controller\BackendController:viewKnowledgebaseAppList',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::APP,
            ],
        ],
    ],
    '^/wiki/app/view(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Knowledgebase\Controller\BackendController:viewKnowledgebaseApp',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::APP,
            ],
        ],
    ],
    '^/wiki/app/create(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Knowledgebase\Controller\BackendController:viewKnowledgebaseAppCreate',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::APP,
            ],
        ],
    ],
];
