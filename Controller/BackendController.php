<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Knowledgebase
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Knowledgebase\Controller;

use Modules\Knowledgebase\Models\NullWikiApp;
use Modules\Knowledgebase\Models\NullWikiCategory;
use Modules\Knowledgebase\Models\NullWikiDoc;
use Modules\Knowledgebase\Models\PermissionCategory;
use Modules\Knowledgebase\Models\WikiAppMapper;
use Modules\Knowledgebase\Models\WikiCategoryMapper;
use Modules\Knowledgebase\Models\WikiDocMapper;
use phpOMS\Account\PermissionType;
use phpOMS\Asset\AssetType;
use phpOMS\Contract\RenderableInterface;
use phpOMS\DataStorage\Database\Query\OrderType;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Views\View;

/**
 * Knowledgebase class.
 *
 * @package Modules\Knowledgebase
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 * @codeCoverageIgnore
 */
final class BackendController extends Controller
{
    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function setUpBackend(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $head = $response->get('Content')->head;
        $head->addAsset(AssetType::CSS, '/Modules/Knowledgebase/Theme/Backend/styles.css?v=1.0.0');
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewKnowledgebaseDashboard(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        // @todo: assign default org app to wiki app and default flag, load the wiki app based on org id and with a default flag set. Use this app in the following line instead of the hardcoded "1"
        $app = $request->getDataInt('app') ?? 1;

        $view->setTemplate('/Modules/Knowledgebase/Theme/Backend/wiki-dashboard');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1005901001, $request, $response);

        /** @var \Modules\Knowledgebase\Models\WikiCategory[] $categories */
        $categories = WikiCategoryMapper::getAll()
            ->with('name')
            ->where('parent', $request->getDataInt('category'))
            ->where('app', $app)
            ->where('name/language', $response->header->l11n->language)
            ->execute();

        $view->data['categories'] = $categories;

        /** @var \Modules\Knowledgebase\Models\WikiDoc[] $documents */
        $documents = WikiDocMapper::getAll()
            ->with('tags')
            ->with('tags/title')
            ->where('app', $app)
            ->where('language', $response->header->l11n->language)
            ->where('tags/title/language', $response->header->l11n->language)
            ->limit(25)->sort('createdAt', OrderType::DESC)
            ->execute();

        $view->data['docs'] = $documents;

        /** @var \Modules\Knowledgebase\Models\WikiApp[] $apps */
        $apps = WikiAppMapper::getAll()->execute();
        $view->data['apps'] = $apps;

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewKnowledgebaseAppList(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/Knowledgebase/Theme/Backend/wiki-app-list');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1005901001, $request, $response);

        /** @var \Modules\Knowledgebase\Models\WikiApp[] $list */
        $list = WikiAppMapper::getAll()->execute();
        $view->data['apps'] = $list;

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewKnowledgebaseApp(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/Knowledgebase/Theme/Backend/wiki-app-single');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1005901001, $request, $response);

        /** @var \Modules\Knowledgebase\Models\WikiApp $app */
        $app = WikiAppMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $view->data['app'] = $app;

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewKnowledgebaseAppCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Knowledgebase/Theme/Backend/wiki-app-single');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1005901001, $request, $response);

        $view->data['app'] = new NullWikiApp();

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewKnowledgebaseCategoryList(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $app = $request->getDataInt('app') ?? $this->app->unitId;

        $view->setTemplate('/Modules/Knowledgebase/Theme/Backend/wiki-category-list');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1005901001, $request, $response);

        /** @var \Modules\Knowledgebase\Models\WikiCategory[] $list */
        $list = WikiCategoryMapper::getAll()->with('name')->where('app', $app)->where('name/language', $response->header->l11n->language)->execute();
        $view->data['categories'] = $list;

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewKnowledgebaseCategory(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/Knowledgebase/Theme/Backend/wiki-category-single');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1005901001, $request, $response);

        /** @var \Modules\Knowledgebase\Models\WikiCategory $category */
        $category = WikiCategoryMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $view->data['category'] = $category;

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewKnowledgebaseCategoryCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/Knowledgebase/Theme/Backend/wiki-category-single');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1005901001, $request, $response);

        $view->data['category'] = new NullWikiCategory();

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewKnowledgebaseDocList(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/Knowledgebase/Theme/Backend/wiki-doc-list');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1005901001, $request, $response);

        /** @var \Modules\Knowledgebase\Models\WikiDoc[] $list */
        $list = WikiDocMapper::getAll()->limit(25)->execute();
        $view->data['docs'] = $list;

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewKnowledgebaseDoc(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $app = $request->getDataInt('app') ?? $this->app->unitId;

        /** @var \Modules\Knowledgebase\Models\WikiDoc $document */
        $document = WikiDocMapper::get()
            ->with('tags')
            ->with('tags/title')
            ->with('media')
            ->where('id', (int) $request->getData('id'))
            ->where('language', $request->header->l11n->language)
            ->where('tags/title/language', $response->header->l11n->language)
            ->execute();

        $accountId = $request->header->account;

        if (!$this->app->accountManager->get($accountId)->hasPermission(
                PermissionType::READ, $this->app->unitId, $this->app->appId, self::NAME, PermissionCategory::WIKI, $document->id)
        ) {
            $view->setTemplate('/Web/Backend/Error/403_inline');
            $response->header->status = RequestStatusCode::R_403;
            return $view;
        }

        $view->setTemplate('/Modules/Knowledgebase/Theme/Backend/wiki-doc-single');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1005901001, $request, $response);

        /** @var \Modules\Knowledgebase\Models\WikiCategory[] $categories */
        $categories = WikiCategoryMapper::getAll()
            ->with('name')
            ->where('parent', $request->getDataInt('category'))
            ->where('app', $app)
            ->where('name/language', $response->header->l11n->language)
            ->execute();

        $view->data['categories'] = $categories;
        $view->data['document'] = $document;
        $view->data['editable'] = $this->app->accountManager->get($accountId)
            ->hasPermission(
                PermissionType::MODIFY,
                $this->app->unitId,
                $this->app->appId,
                self::NAME,
                PermissionCategory::WIKI,
                $document->id
            );

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewKnowledgebaseDocCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/Knowledgebase/Theme/Backend/wiki-doc-create');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1005901001, $request, $response);

        $editor = new \Modules\Editor\Theme\Backend\Components\Editor\BaseView($this->app->l11nManager, $request, $response);
        $view->data['editor'] = $editor;

        $tagSelector = new \Modules\Tag\Theme\Backend\Components\TagSelector\BaseView($this->app->l11nManager, $request, $response);
        $view->data['tagSelector'] = $tagSelector;

        $view->data['doc'] = new NullWikiDoc();

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewKnowledgebaseDocEdit(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/Knowledgebase/Theme/Backend/wiki-doc-create');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1000601001, $request, $response);

        $editor = new \Modules\Editor\Theme\Backend\Components\Editor\BaseView($this->app->l11nManager, $request, $response);
        $view->data['editor'] = $editor;

        $accGrpSelector = new \Modules\Profile\Theme\Backend\Components\AccountGroupSelector\BaseView($this->app->l11nManager, $request, $response);
        $view->data['accGrpSelector'] = $accGrpSelector;

        $tagSelector = new \Modules\Tag\Theme\Backend\Components\TagSelector\BaseView($this->app->l11nManager, $request, $response);
        $view->data['tagSelector'] = $tagSelector;

        $view->data['doc'] = WikiDocMapper::get()->where('id', $request->getDataInt('id') ?? 0)->execute();

        return $view;
    }
}
