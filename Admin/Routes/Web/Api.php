<?php declare(strict_types=1);

use Modules\Knowledgebase\Controller\ApiController;
use Modules\Knowledgebase\Models\PermissionState;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^.*/wiki/doc.*$' => [
        [
            'dest' => '\Modules\Knowledgebase\Controller\ApiController:apiWikiDocCreate',
            'verb' => RouteVerb::PUT,
            'permission' => [
                'module' => ApiController::MODULE_NAME,
                'type'  => PermissionType::CREATE,
                'state' => PermissionState::WIKI,
            ],
        ],
        [
            'dest' => '\Modules\Knowledgebase\Controller\ApiController:apiWikiDocUpdate',
            'verb' => RouteVerb::SET,
            'permission' => [
                'module' => ApiController::MODULE_NAME,
                'type'  => PermissionType::MODIFY,
                'state' => PermissionState::WIKI,
            ],
        ],
        [
            'dest' => '\Modules\Knowledgebase\Controller\ApiController:apiWikiDocGet',
            'verb' => RouteVerb::GET,
            'permission' => [
                'module' => ApiController::MODULE_NAME,
                'type'  => PermissionType::READ,
                'state' => PermissionState::WIKI,
            ],
        ],
        [
            'dest' => '\Modules\Knowledgebase\Controller\ApiController:apiWikiDocDelete',
            'verb' => RouteVerb::DELETE,
            'permission' => [
                'module' => ApiController::MODULE_NAME,
                'type'  => PermissionType::DELETE,
                'state' => PermissionState::WIKI,
            ],
        ],
    ],

    '^.*/wiki/category.*$' => [
        [
            'dest' => '\Modules\Knowledgebase\Controller\ApiController:apiWikiCategoryCreate',
            'verb' => RouteVerb::PUT,
            'permission' => [
                'module' => ApiController::MODULE_NAME,
                'type'  => PermissionType::CREATE,
                'state' => PermissionState::CATEGORY,
            ],
        ],
        [
            'dest' => '\Modules\Knowledgebase\Controller\ApiController:apiWikiCategoryUpdate',
            'verb' => RouteVerb::SET,
            'permission' => [
                'module' => ApiController::MODULE_NAME,
                'type'  => PermissionType::MODIFY,
                'state' => PermissionState::CATEGORY,
            ],
        ],
        [
            'dest' => '\Modules\Knowledgebase\Controller\ApiController:apiWikiCategoryGet',
            'verb' => RouteVerb::GET,
            'permission' => [
                'module' => ApiController::MODULE_NAME,
                'type'  => PermissionType::READ,
                'state' => PermissionState::CATEGORY,
            ],
        ],
        [
            'dest' => '\Modules\Knowledgebase\Controller\ApiController:apiWikiCategoryDelete',
            'verb' => RouteVerb::DELETE,
            'permission' => [
                'module' => ApiController::MODULE_NAME,
                'type'  => PermissionType::DELETE,
                'state' => PermissionState::CATEGORY,
            ],
        ],
    ],

    '^.*/wiki/app.*$' => [
        [
            'dest' => '\Modules\Knowledgebase\Controller\ApiController:apiWikiAppCreate',
            'verb' => RouteVerb::PUT,
            'permission' => [
                'module' => ApiController::MODULE_NAME,
                'type'  => PermissionType::CREATE,
                'state' => PermissionState::APP,
            ],
        ],
        [
            'dest' => '\Modules\Knowledgebase\Controller\ApiController:apiWikiAppUpdate',
            'verb' => RouteVerb::SET,
            'permission' => [
                'module' => ApiController::MODULE_NAME,
                'type'  => PermissionType::MODIFY,
                'state' => PermissionState::APP,
            ],
        ],
        [
            'dest' => '\Modules\Knowledgebase\Controller\ApiController:apiWikiAppGet',
            'verb' => RouteVerb::GET,
            'permission' => [
                'module' => ApiController::MODULE_NAME,
                'type'  => PermissionType::READ,
                'state' => PermissionState::APP,
            ],
        ],
        [
            'dest' => '\Modules\Knowledgebase\Controller\ApiController:apiWikiAppDelete',
            'verb' => RouteVerb::DELETE,
            'permission' => [
                'module' => ApiController::MODULE_NAME,
                'type'  => PermissionType::DELETE,
                'state' => PermissionState::APP,
            ],
        ],
    ],
];
