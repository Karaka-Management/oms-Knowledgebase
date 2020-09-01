<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   Modules\Knowledgebase
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\Knowledgebase\Controller;

use Modules\Knowledgebase\Models\NullWikiCategory;
use Modules\Knowledgebase\Models\WikiApp;
use Modules\Knowledgebase\Models\WikiAppMapper;
use Modules\Knowledgebase\Models\WikiCategory;
use Modules\Knowledgebase\Models\WikiCategoryMapper;
use Modules\Knowledgebase\Models\WikiDoc;
use Modules\Knowledgebase\Models\WikiDocMapper;
use Modules\Knowledgebase\Models\WikiStatus;
use Modules\Tag\Models\NullTag;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Message\NotificationLevel;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Model\Message\FormValidation;
use phpOMS\Utils\Parser\Markdown\Markdown;

/**
 * Knowledgebase class.
 *
 * @package Modules\Knowledgebase
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 *
 * @todo Orange-Management/Modules#79
 *  Add category management
 *  Categories cannot get managed right (created and edited). Categories need to have a language component.
 */
final class ApiController extends Controller
{
    /**
     * Api method to create a wiki entry
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiWikiDocCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (!empty($val = $this->validateWikiDocCreate($request))) {
            $response->set($request->getUri()->__toString(), new FormValidation($val));

            return;
        }

        $doc = $this->createWikiDocFromRequest($request, $response, $data);
        $this->createModel($request->getHeader()->getAccount(), $doc, WikiDocMapper::class, 'doc', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Wiki', 'Wiki successfully created.', $doc);
    }

    /**
     * Method to create a wiki entry from request.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return WikiDoc
     *
     * @since 1.0.0
     */
    public function createWikiDocFromRequest(RequestAbstract $request, ResponseAbstract $response, $data = null) : WikiDoc
    {
        $doc = new WikiDoc();
        $doc->setName((string) $request->getData('title'));
        $doc->setDoc(Markdown::parse((string) ($request->getData('plain') ?? '')));
        $doc->setDocRaw((string) ($request->getData('plain') ?? ''));
        $doc->setCategory(new NullWikiCategory((int) ($request->getData('category') ?? 1)));
        $doc->setLanguage((string) ($request->getData('language') ?? $request->getHeader()->getL11n()->getLanguage()));
        $doc->setStatus((int) ($request->getData('status') ?? WikiStatus::INACTIVE));

        if (!empty($tags = $request->getDataJson('tags'))) {
            foreach ($tags as $tag) {
                if (!isset($tag['id'])) {
                    $request->setData('title', $tag['title'], true);
                    $request->setData('color', $tag['color'], true);
                    $request->setData('language', $tag['language'], true);

                    $internalResponse = new HttpResponse();
                    $this->app->moduleManager->get('Tag')->apiTagCreate($request, $internalResponse, $data);
                    $doc->addTag($internalResponse->get($request->getUri()->__toString())['response']);
                } else {
                    $doc->addTag(new NullTag((int) $tag['id']));
                }
            }
        }

        return $doc;
    }

    /**
     * Method to validate wiki entry creation from request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateWikiDocCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['title'] = empty($request->getData('title')))
            || ($val['plain'] = empty($request->getData('plain')))
            || ($val['status'] = (
                $request->getData('status') !== null
                && !WikiStatus::isValidValue((int) $request->getData('status'))
            ))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to get a doc
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiWikiDocGet(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $doc = WikiDocMapper::get((int) $request->getData('id'));
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Doc', 'Doc successfully returned', $doc);
    }

    /**
     * Api method to create doc
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiWikiDocUpdate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $old = clone WikiDocMapper::get((int) $request->getData('id'));
        $new = $this->updateDocFromRequest($request);
        $this->updateModel($request->getHeader()->getAccount(), $old, $new, WikiDocMapper::class, 'doc', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Doc', 'Doc successfully updated', $new);
    }

    /**
     * Method to update doc from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return WikiDoc
     *
     * @since 1.0.0
     */
    private function updateDocFromRequest(RequestAbstract $request) : WikiDoc
    {
        $doc = WikiDocMapper::get((int) $request->getData('id'));
        $doc->setName((string) ($request->getData('title') ?? $doc->getName()));

        return $doc;
    }

    /**
     * Api method to delete doc
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiWikiDocDelete(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $doc = WikiDocMapper::get((int) $request->getData('id'));
        $this->deleteModel($request->getHeader()->getAccount(), $doc, WikiDocMapper::class, 'doc', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Doc', 'Doc successfully deleted', $doc);
    }

    /**
     * Api method to create a wiki category
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiWikiCategoryCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (!empty($val = $this->validateWikiCategoryCreate($request))) {
            $response->set($request->getUri()->__toString(), new FormValidation($val));

            return;
        }

        $category = $this->createWikiCategoryFromRequest($request);
        $this->createModel($request->getHeader()->getAccount(), $category, WikiCategoryMapper::class, 'category', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Category', 'Category successfully created.', $category);
    }

    /**
     * Method to create a wiki category from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return WikiCategory
     *
     * @since 1.0.0
     */
    public function createWikiCategoryFromRequest(RequestAbstract $request) : WikiCategory
    {
        $category = new WikiCategory();
        $category->setName((string) $request->getData('title'));

        if ($request->getData('path') !== null) {
            $category->setPath((string) $request->getData('path'));
        }

        if ($request->getData('parent') !== null) {
            $category->setParent(new NullWikiCategory((int) $request->getData('parent')));
        }

        return $category;
    }

    /**
     * Method to validate wiki category creation from request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateWikiCategoryCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['title'] = empty($request->getData('title')))) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to get a category
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiWikiCategoryGet(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $category = WikiCategoryMapper::get((int) $request->getData('id'));
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Category', 'Category successfully returned', $category);
    }

    /**
     * Api method to create category
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiWikiCategoryUpdate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $old = clone WikiCategoryMapper::get((int) $request->getData('id'));
        $new = $this->updateCategoryFromRequest($request);
        $this->updateModel($request->getHeader()->getAccount(), $old, $new, WikiCategoryMapper::class, 'category', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Category', 'Category successfully updated', $new);
    }

    /**
     * Method to update category from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return WikiCategory
     *
     * @since 1.0.0
     */
    private function updateCategoryFromRequest(RequestAbstract $request) : WikiCategory
    {
        $category = WikiCategoryMapper::get((int) $request->getData('id'));
        $category->setName((string) ($request->getData('title') ?? $category->getName()));

        return $category;
    }

    /**
     * Api method to delete category
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiWikiCategoryDelete(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $category = WikiCategoryMapper::get((int) $request->getData('id'));
        $this->deleteModel($request->getHeader()->getAccount(), $category, WikiCategoryMapper::class, 'category', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Category', 'Category successfully deleted', $category);
    }

    /**
     * Api method to create a wiki app
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiWikiAppCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (!empty($val = $this->validateWikiAppCreate($request))) {
            $response->set($request->getUri()->__toString(), new FormValidation($val));

            return;
        }

        $app = $this->createWikiAppFromRequest($request);
        $this->createModel($request->getHeader()->getAccount(), $app, WikiAppMapper::class, 'app', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'App', 'App successfully created.', $app);
    }

    /**
     * Method to create a wiki app from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return WikiApp
     *
     * @since 1.0.0
     */
    public function createWikiAppFromRequest(RequestAbstract $request) : WikiApp
    {
        $app = new WikiApp();
        $app->setName((string) $request->getData('name'));

        return $app;
    }

    /**
     * Method to validate wiki app creation from request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateWikiAppCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['name'] = empty($request->getData('name')))) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to get a app
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiWikiAppGet(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $app = WikiAppMapper::get((int) $request->getData('id'));
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'App', 'App successfully returned', $app);
    }

    /**
     * Api method to create app
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiWikiAppUpdate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $old = clone WikiAppMapper::get((int) $request->getData('id'));
        $new = $this->updateAppFromRequest($request);
        $this->updateModel($request->getHeader()->getAccount(), $old, $new, WikiAppMapper::class, 'app', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'App', 'App successfully updated', $new);
    }

    /**
     * Method to update app from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return WikiApp
     *
     * @since 1.0.0
     */
    private function updateAppFromRequest(RequestAbstract $request) : WikiApp
    {
        $app = WikiAppMapper::get((int) $request->getData('id'));
        $app->setName((string) ($request->getData('title') ?? $app->getName()));

        return $app;
    }

    /**
     * Api method to delete app
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiWikiAppDelete(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $app = WikiAppMapper::get((int) $request->getData('id'));
        $this->deleteModel($request->getHeader()->getAccount(), $app, WikiAppMapper::class, 'app', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'App', 'App successfully deleted', $app);
    }
}
