<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\Knowledgebase
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\Knowledgebase\Controller;

use Modules\Knowledgebase\Models\NullWikiApp;
use Modules\Knowledgebase\Models\NullWikiCategory;
use Modules\Knowledgebase\Models\WikiApp;
use Modules\Knowledgebase\Models\WikiAppMapper;
use Modules\Knowledgebase\Models\WikiCategory;
use Modules\Knowledgebase\Models\WikiCategoryL11n;
use Modules\Knowledgebase\Models\WikiCategoryL11nMapper;
use Modules\Knowledgebase\Models\WikiCategoryMapper;
use Modules\Knowledgebase\Models\WikiDoc;
use Modules\Knowledgebase\Models\WikiDocMapper;
use Modules\Knowledgebase\Models\WikiStatus;
use Modules\Tag\Models\NullTag;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Message\Http\RequestStatusCode;
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
            $response->set($request->uri->__toString(), new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $doc = $this->createWikiDocFromRequest($request, $response, $data);
        $this->createModel($request->header->account, $doc, WikiDocMapper::class, 'doc', $request->getOrigin());
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
        $doc           = new WikiDoc();
        $doc->name     = (string) $request->getData('title');
        $doc->doc      = Markdown::parse((string) ($request->getData('plain') ?? ''));
        $doc->docRaw   = (string) ($request->getData('plain') ?? '');
        $doc->category = new NullWikiCategory((int) ($request->getData('category') ?? 1));
        $doc->setLanguage((string) ($request->getData('language') ?? $request->getLanguage()));
        $doc->setStatus((int) ($request->getData('status') ?? WikiStatus::INACTIVE));
        $doc->app = new NullWikiApp((int) ($request->getData('app') ?? 1));

        if (!empty($tags = $request->getDataJson('tags'))) {
            foreach ($tags as $tag) {
                if (!isset($tag['id'])) {
                    $request->setData('title', $tag['title'], true);
                    $request->setData('color', $tag['color'], true);
                    $request->setData('icon', $tag['icon'] ?? null, true);
                    $request->setData('language', $tag['language'], true);

                    $internalResponse = new HttpResponse();
                    $this->app->moduleManager->get('Tag')->apiTagCreate($request, $internalResponse, $data);
                    $doc->addTag($internalResponse->get($request->uri->__toString())['response']);
                } else {
                    $doc->addTag(new NullTag((int) $tag['id']));
                }
            }
        }

        if (!empty($uploadedFiles = $request->getFiles() ?? [])) {
            $uploaded = $this->app->moduleManager->get('Media')->uploadFiles(
                [''],
                $uploadedFiles,
                $request->header->account,
                __DIR__ . '/../../../Modules/Media/Files/Modules/Knowledgebase',
                '/Modules/Knowledgebase',
            );

            foreach ($uploaded as $media) {
                $doc->addMedia($media);
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
     * Validate tag l11n create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateWikiCategoryL11nCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['name'] = empty($request->getData('name')))
            || ($val['category'] = empty($request->getData('category')))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create tag localization
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
    public function apiWikiCategoryL11nCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (!empty($val = $this->validateWikiCategoryL11nCreate($request))) {
            $response->set('wiki_category_l11n_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $l11nWikiCategory = $this->createWikiCategoryL11nFromRequest($request);
        $this->createModel($request->header->account, $l11nWikiCategory, WikiCategoryL11nMapper::class, 'wiki_category_l11n', $request->getOrigin());

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Localization', 'Category localization successfully created', $l11nWikiCategory);
    }

    /**
     * Method to create tag localization from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return WikiCategoryL11n
     *
     * @since 1.0.0
     */
    private function createWikiCategoryL11nFromRequest(RequestAbstract $request) : WikiCategoryL11n
    {
        $l11nWikiCategory = new WikiCategoryL11n();
        $l11nWikiCategory->setCategory((int) ($request->getData('category') ?? 0));
        $l11nWikiCategory->setLanguage((string) (
            $request->getData('language') ?? $request->getLanguage()
        ));
        $l11nWikiCategory->name = (string) ($request->getData('name') ?? '');

        return $l11nWikiCategory;
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
        $this->updateModel($request->header->account, $old, $new, WikiDocMapper::class, 'doc', $request->getOrigin());
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
        $this->deleteModel($request->header->account, $doc, WikiDocMapper::class, 'doc', $request->getOrigin());
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
            $response->set($request->uri->__toString(), new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $category = $this->createWikiCategoryFromRequest($request);
        $category->setL11n($request->getData('name'), $request->getData('language') ?? $request->getLanguage());
        $this->createModel($request->header->account, $category, WikiCategoryMapper::class, 'category', $request->getOrigin());

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
        $category      = new WikiCategory();
        $category->app = new NullWikiApp((int) ($request->getData('app') ?? 1));

        if ($request->getData('parent') !== null) {
            $category->parent = new NullWikiCategory((int) $request->getData('parent'));
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
        if (($val['name'] = empty($request->getData('name')))) {
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
        $this->updateModel($request->header->account, $old, $new, WikiCategoryMapper::class, 'category', $request->getOrigin());
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
        $this->deleteModel($request->header->account, $category, WikiCategoryMapper::class, 'category', $request->getOrigin());
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
            $response->set($request->uri->__toString(), new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $app = $this->createWikiAppFromRequest($request);
        $this->createModel($request->header->account, $app, WikiAppMapper::class, 'app', $request->getOrigin());
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
        $app       = new WikiApp();
        $app->name = (string) $request->getData('name');

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
        $this->updateModel($request->header->account, $old, $new, WikiAppMapper::class, 'app', $request->getOrigin());
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
        $this->deleteModel($request->header->account, $app, WikiAppMapper::class, 'app', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'App', 'App successfully deleted', $app);
    }
}
