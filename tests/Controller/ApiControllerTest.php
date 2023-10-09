<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Knowledgebase\tests\Controller;

use Model\CoreSettings;
use Modules\Admin\Models\AccountPermission;
use phpOMS\Account\Account;
use phpOMS\Account\AccountManager;
use phpOMS\Account\PermissionType;
use phpOMS\Application\ApplicationAbstract;
use phpOMS\DataStorage\Session\HttpSession;
use phpOMS\Dispatcher\Dispatcher;
use phpOMS\Event\EventManager;
use phpOMS\Localization\L11nManager;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Module\ModuleAbstract;
use phpOMS\Module\ModuleManager;
use phpOMS\Router\WebRouter;
use phpOMS\System\MimeType;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\TestUtils;

/**
 * @testdox Modules\Knowledgebase\tests\Controller\ApiControllerTest: Knowledgebase api controller
 *
 * @internal
 */
final class ApiControllerTest extends \PHPUnit\Framework\TestCase
{
    protected ApplicationAbstract $app;

    /**
     * @var \Modules\Knowledgebase\Controller\ApiController
     */
    protected ModuleAbstract $module;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->app = new class() extends ApplicationAbstract
        {
            protected string $appName = 'Api';
        };

        $this->app->dbPool          = $GLOBALS['dbpool'];
        $this->app->unitId          = 1;
        $this->app->accountManager  = new AccountManager($GLOBALS['session']);
        $this->app->appSettings     = new CoreSettings();
        $this->app->moduleManager   = new ModuleManager($this->app, __DIR__ . '/../../../../Modules/');
        $this->app->dispatcher      = new Dispatcher($this->app);
        $this->app->eventManager    = new EventManager($this->app->dispatcher);
        $this->app->eventManager->importFromFile(__DIR__ . '/../../../../Web/Api/Hooks.php');
        $this->app->sessionManager = new HttpSession(36000);
        $this->app->l11nManager    = new L11nManager();

        $account = new Account();
        TestUtils::setMember($account, 'id', 1);

        $permission       = new AccountPermission();
        $permission->unit = 1;
        $permission->app  = 2;
        $permission->setPermission(
            PermissionType::READ
            | PermissionType::CREATE
            | PermissionType::MODIFY
            | PermissionType::DELETE
            | PermissionType::PERMISSION
        );

        $account->addPermission($permission);

        $this->app->accountManager->add($account);
        $this->app->router = new WebRouter();

        $this->module = $this->app->moduleManager->get('Knowledgebase');

        TestUtils::setMember($this->module, 'app', $this->app);
    }

    /**
     * @covers Modules\Knowledgebase\Controller\ApiController
     * @group module
     */
    public function testApiAppCRU() : void
    {
        // create
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('name', 'Test App');

        $this->module->apiWikiAppCreate($request, $response);
        self::assertGreaterThan(0, $aId = $response->getDataArray('')['response']->id);

        //read
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('id', $aId);

        $this->module->apiWikiAppGet($request, $response);
        self::assertEquals('Test App', $response->getDataArray('')['response']->name);

        // update
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('id', $aId);
        $request->setData('name', 'New title');

        $this->module->apiWikiAppUpdate($request, $response);
        self::assertEquals('New title', $response->getDataArray('')['response']->name);
    }

    /**
     * @covers Modules\Knowledgebase\Controller\ApiController
     * @group module
     */
    public function testApiWikiAppCreateInvalidData() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('invalid', '1');

        $this->module->apiWikiAppCreate($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }

    /**
     * @covers Modules\Knowledgebase\Controller\ApiController
     * @group module
     */
    public function testWikiCategoryCRU() : void
    {
        // create
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('app', '1');
        $request->setData('name', 'Test Category');

        $this->module->apiWikiCategoryCreate($request, $response);
        self::assertGreaterThan(0, $cId = $response->getDataArray('')['response']->id);

        //read
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('id', $cId);

        $this->module->apiWikiCategoryGet($request, $response);
        self::assertEquals('Test Category', $response->getDataArray('')['response']->getL11n());

        // update
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('id', $cId);
        $request->setData('name', 'New title');

        $this->module->apiWikiCategoryUpdate($request, $response);
        self::assertEquals('New title', $response->getDataArray('')['response']->getL11n());
    }

    /**
     * @covers Modules\Knowledgebase\Controller\ApiController
     * @group module
     */
    public function testApiWikiCategoryCreateInvalidData() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('invalid', '1');

        $this->module->apiWikiCategoryCreate($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }

    /**
     * @covers Modules\Knowledgebase\Controller\ApiController
     * @group module
     */
    public function testWikiCategoryL11nCRU() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('category', '1');
        $request->setData('name', 'New Test Category');

        $this->module->apiWikiCategoryL11nCreate($request, $response);
        self::assertGreaterThan(0, $response->getDataArray('')['response']->id);
    }

    /**
     * @covers Modules\Knowledgebase\Controller\ApiController
     * @group module
     */
    public function testApiWikiCategoryL11nCreateInvalidData() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('invalid', '1');

        $this->module->apiWikiCategoryL11nCreate($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }

    /**
     * @covers Modules\Knowledgebase\Controller\ApiController
     * @group module
     */
    public function testWikiDocCRU() : void
    {
        // create
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('title', 'Test Doc');
        $request->setData('plain', 'Plain text');
        $request->setData('tags', '[{"title": "TestTitle", "color": "#f0f", "language": "en"}, {"id": 1}]');

        if (!\is_file(__DIR__ . '/test_tmp.md')) {
            \copy(__DIR__ . '/test.md', __DIR__ . '/test_tmp.md');
        }

        TestUtils::setMember($request, 'files', [
            'file1' => [
                'name'     => 'test.md',
                'type'     => MimeType::M_TXT,
                'tmp_name' => __DIR__ . '/test_tmp.md',
                'error'    => \UPLOAD_ERR_OK,
                'size'     => \filesize(__DIR__ . '/test_tmp.md'),
            ],
        ]);

        $request->setData('media', \json_encode([1]));

        $this->module->apiWikiDocCreate($request, $response);
        self::assertGreaterThan(0, $cId = $response->getDataArray('')['response']->id);

        //read
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('id', $cId);

        $this->module->apiWikiDocGet($request, $response);
        self::assertEquals('Test Doc', $response->getDataArray('')['response']->name);

        // update
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('id', $cId);
        $request->setData('title', 'New title');

        $this->module->apiWikiDocUpdate($request, $response);
        self::assertEquals('New title', $response->getDataArray('')['response']->name);
    }

    /**
     * @covers Modules\Knowledgebase\Controller\ApiController
     * @group module
     */
    public function testApiWikiDocCreateInvalidData() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('invalid', '1');

        $this->module->apiWikiDocCreate($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }
}
