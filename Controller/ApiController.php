<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Knowledgebase
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Knowledgebase\Controller;

use Modules\Admin\Models\AccountMapper;
use Modules\Admin\Models\NullAccount;
use Modules\Editor\Models\EditorDocHistoryMapper;
use Modules\Knowledgebase\Models\NullWikiApp;
use Modules\Knowledgebase\Models\NullWikiCategory;
use Modules\Knowledgebase\Models\WikiApp;
use Modules\Knowledgebase\Models\WikiAppMapper;
use Modules\Knowledgebase\Models\WikiCategory;
use Modules\Knowledgebase\Models\WikiCategoryL11n;
use Modules\Knowledgebase\Models\WikiCategoryL11nMapper;
use Modules\Knowledgebase\Models\WikiCategoryMapper;
use Modules\Knowledgebase\Models\WikiDoc;
use Modules\Knowledgebase\Models\WikiDocHistory;
use Modules\Knowledgebase\Models\WikiDocMapper;
use Modules\Knowledgebase\Models\WikiStatus;
use Modules\Media\Models\CollectionMapper;
use Modules\Media\Models\MediaMapper;
use Modules\Media\Models\NullMedia;
use Modules\Media\Models\Reference;
use Modules\Media\Models\ReferenceMapper;
use Modules\Tag\Models\NullTag;
use phpOMS\Localization\ISO639x1Enum;
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
 * @link    https://jingga.app
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
    public function apiWikiDocCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateWikiDocCreate($request))) {
            $response->set($request->uri->__toString(), new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $doc = $this->createWikiDocFromRequest($request, $response, $data);
        $this->createModel($request->header->account, $doc, WikiDocMapper::class, 'doc', $request->getOrigin());

        if (!empty($request->getFiles())
            || !empty($request->getDataJson('media'))
        ) {
            $this->createWikiMedia($doc, $request);
        }

        if ($doc->isVersioned) {
            $history = $this->createHistory($doc);
            $this->createModel($request->header->account, $history, EditorDocHistoryMapper::class, 'doc_history', $request->getOrigin());
        }

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Wiki', 'Wiki successfully created.', $doc);
    }

    /**
     * Create media files for wiki document
     *
     * @param WikiDoc         $doc     Wiki document
     * @param RequestAbstract $request Request incl. media do upload
     *
     * @return void
     *
     * @since 1.0.0
     */
    private function createWikiMedia(WikiDoc $doc, RequestAbstract $request) : void
    {
        $path    = $this->createWikiDir($doc);
        $account = AccountMapper::get()->where('id', $request->header->account)->execute();

        if (!empty($uploadedFiles = $request->getFiles())) {
            $uploaded = $this->app->moduleManager->get('Media')->uploadFiles(
                [],
                [],
                $uploadedFiles,
                $request->header->account,
                __DIR__ . '/../../../Modules/Media/Files' . $path,
                $path,
            );

            $collection = null;

            foreach ($uploaded as $media) {
                MediaMapper::create()->execute($media);
                WikiDocMapper::writer()->createRelationTable('media', [$media->getId()], $doc->getId());

                $accountPath = '/Accounts/' . $account->getId() . ' ' . $account->login . '/Knowledgebase/' . ($doc->category?->getId() ?? '0') . '/' . $doc->getId();

                $ref            = new Reference();
                $ref->name      = $media->name;
                $ref->source    = new NullMedia($media->getId());
                $ref->createdBy = new NullAccount($request->header->account);
                $ref->setVirtualPath($accountPath);

                ReferenceMapper::create()->execute($ref);

                if ($collection === null) {
                    $collection = $this->app->moduleManager->get('Media')->createRecursiveMediaCollection(
                        $accountPath,
                        $request->header->account,
                        __DIR__ . '/../../../Modules/Media/Files/Accounts/' . $account->getId() . '/Knowledgebase/' . ($doc->category?->getId() ?? '0') . '/' . $doc->getId()
                    );
                }

                CollectionMapper::writer()->createRelationTable('sources', [$ref->getId()], $collection->getId());
            }
        }

        if (!empty($mediaFiles = $request->getDataJson('media'))) {
            $collection = null;

            foreach ($mediaFiles as $media) {
                WikiDocMapper::writer()->createRelationTable('media', [(int) $media], $doc->getId());

                $ref            = new Reference();
                $ref->source    = new NullMedia((int) $media);
                $ref->createdBy = new NullAccount($request->header->account);
                $ref->setVirtualPath($path);

                ReferenceMapper::create()->execute($ref);

                if ($collection === null) {
                    $collection = $this->app->moduleManager->get('Media')->createRecursiveMediaCollection(
                        $path,
                        $request->header->account,
                        __DIR__ . '/../../../Modules/Media/Files' . $path
                    );
                }

                CollectionMapper::writer()->createRelationTable('sources', [$ref->getId()], $collection->getId());
            }
        }
    }

    /**
     * Create media directory path
     *
     * @param WikiDoc $doc Doc
     *
     * @return string
     *
     * @since 1.0.0
     */
    private function createWikiDir(WikiDoc $doc) : string
    {
        return '/Modules/Knowledgebase/'
            . ($doc->category?->getId() ?? '0') . '/'
            . $doc->getId();
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
        $doc              = new WikiDoc();
        $doc->createdBy   = new NullAccount($request->header->account);
        $doc->name        = (string) $request->getData('title');
        $doc->doc         = Markdown::parse((string) ($request->getData('plain') ?? ''));
        $doc->docRaw      = (string) ($request->getData('plain') ?? '');
        $doc->isVersioned = (bool) ($request->getData('versioned') ?? false);
        $doc->category    = new NullWikiCategory((int) ($request->getData('category') ?? 1));
        $doc->app         = new NullWikiApp((int) ($request->getData('app') ?? 1));
        $doc->version     = (string) ($request->getData('version') ?? '');
        $doc->setLanguage((string) ($request->getData('language') ?? $request->getLanguage()));
        $doc->setStatus((int) ($request->getData('status') ?? WikiStatus::INACTIVE));

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

        return $doc;
    }

    /**
     * Create history from document
     *
     * @param WikiDoc $doc Document
     *
     * @return WikiDocHistory
     *
     * @since 1.0.0
     */
    private function createHistory(WikiDoc $doc) : WikiDocHistory
    {
        $history = WikiDocHistory::createFromDoc($doc);

        return $history;
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
    public function apiWikiCategoryL11nCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
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
        $l11nWikiCategory           = new WikiCategoryL11n();
        $l11nWikiCategory->category = (int) ($request->getData('category') ?? 0);
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
    public function apiWikiDocGet(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $doc = WikiDocMapper::get()->where('id', (int) $request->getData('id'))->execute();
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
    public function apiWikiDocUpdate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $old = clone WikiDocMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $new = $this->updateDocFromRequest($request);
        $this->updateModel($request->header->account, $old, $new, WikiDocMapper::class, 'doc', $request->getOrigin());

        if ($new->isVersioned
            && ($old->docRaw !== $new->docRaw
                || $old->name !== $new->name
            )
        ) {
            $history = $this->createHistory($new);
            $this->createModel($request->header->account, $history, EditorDocHistoryMapper::class, 'doc_history', $request->getOrigin());
        }

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
        /** @var WikiDoc $doc */
        $doc              = WikiDocMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $doc->isVersioned = (bool) ($request->getData('versioned') ?? $doc->isVersioned);
        $doc->name        = (string) ($request->getData('title') ?? $doc->name);
        $doc->docRaw      = (string) ($request->getData('plain') ?? $doc->docRaw);
        $doc->doc         = Markdown::parse((string) ($request->getData('plain') ?? $doc->docRaw));
        $doc->version     = (string) ($request->getData('version') ?? $doc->version);

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
    public function apiWikiDocDelete(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $doc = WikiDocMapper::get()->where('id', (int) $request->getData('id'))->execute();
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
    public function apiWikiCategoryCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateWikiCategoryCreate($request))) {
            $response->set($request->uri->__toString(), new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $category = $this->createWikiCategoryFromRequest($request);
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
        $category->setL11n($request->getData('name'), $request->getData('language') ?? $request->getLanguage());

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
    public function apiWikiCategoryGet(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $category = WikiCategoryMapper::get()
            ->with('name')
            ->where('id', (int) $request->getData('id'))
            ->where('name/language', ISO639x1Enum::_EN)
            ->execute();

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
    public function apiWikiCategoryUpdate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $old = clone WikiCategoryMapper::get()->where('id', (int) $request->getData('id'))->execute();
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
        $category = WikiCategoryMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $category->setL11n($request->getData('name') ?? $category->getL11n(), $request->getData('language') ?? $request->getLanguage());

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
    public function apiWikiCategoryDelete(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $category = WikiCategoryMapper::get()->where('id', (int) $request->getData('id'))->execute();
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
    public function apiWikiAppCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
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
    public function apiWikiAppGet(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $app = WikiAppMapper::get()->where('id', (int) $request->getData('id'))->execute();
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
    public function apiWikiAppUpdate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $old = clone WikiAppMapper::get()->where('id', (int) $request->getData('id'))->execute();
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
        $app       = WikiAppMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $app->name = (string) ($request->getData('name') ?? $app->name);

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
    public function apiWikiAppDelete(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $app = WikiAppMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->deleteModel($request->header->account, $app, WikiAppMapper::class, 'app', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'App', 'App successfully deleted', $app);
    }
}
