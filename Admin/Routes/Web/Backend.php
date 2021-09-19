<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

use Modules\Knowledgebase\Controller\BackendController;
use Modules\Knowledgebase\Models\PermissionState;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^.*/wiki.*$' => [
        [
            'dest'       => '\Modules\Knowledgebase\Controller\BackendController:setUpBackend',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::MODULE_NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionState::WIKI,
            ],
        ],
    ],
    '^.*/wiki/dashboard.*$' => [
        [
            'dest'       => '\Modules\Knowledgebase\Controller\BackendController:viewKnowledgebaseDashboard',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::MODULE_NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionState::WIKI,
            ],
        ],
    ],
    '^.*/wiki/category/list.*$' => [
        [
            'dest'       => '\Modules\Knowledgebase\Controller\BackendController:viewKnowledgebaseCategoryList',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::MODULE_NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionState::CATEGORY,
            ],
        ],
    ],
    '^.*/wiki/category/single.*$' => [
        [
            'dest'       => '\Modules\Knowledgebase\Controller\BackendController:viewKnowledgebaseCategory',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::MODULE_NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionState::CATEGORY,
            ],
        ],
    ],
    '^.*/wiki/category/create.*$' => [
        [
            'dest'       => '\Modules\Knowledgebase\Controller\BackendController:viewKnowledgebaseCategoryCreate',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::MODULE_NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionState::CATEGORY,
            ],
        ],
    ],
    '^.*/wiki/doc/single.*$' => [
        [
            'dest'       => '\Modules\Knowledgebase\Controller\BackendController:viewKnowledgebaseDoc',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::MODULE_NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionState::WIKI,
            ],
        ],
    ],
    '^.*/wiki/doc/create.*$' => [
        [
            'dest'       => '\Modules\Knowledgebase\Controller\BackendController:viewKnowledgebaseDocCreate',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::MODULE_NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionState::WIKI,
            ],
        ],
    ],
    '^.*/wiki/doc/edit.*$' => [
        [
            'dest'       => '\Modules\Knowledgebase\Controller\BackendController:viewKnowledgebaseDocEdit',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::MODULE_NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionState::WIKI,
            ],
        ],
    ],
    '^.*/wiki/doc/list.*$' => [
        [
            'dest'       => '\Modules\Knowledgebase\Controller\BackendController:viewKnowledgebaseDocList',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::MODULE_NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionState::WIKI,
            ],
        ],
    ],
];
