<?php
/**
 * Jingga
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

use Modules\Admin\Models\AccountMapper;
use Modules\Admin\Models\NullAccount;
use Modules\Editor\Models\EditorDocHistoryMapper;
use Modules\Knowledgebase\Models\NullWikiApp;
use Modules\Knowledgebase\Models\NullWikiCategory;
use Modules\Knowledgebase\Models\WikiApp;
use Modules\Knowledgebase\Models\WikiAppMapper;
use Modules\Knowledgebase\Models\WikiCategory;
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
use phpOMS\Localization\BaseStringL11n;
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
 * @license OMS License 2.0
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
            $response->data[$request->uri->__toString()] = new FormValidation($val);
            $response->header->status                    = RequestStatusCode::R_400;

            return;
        }

        $doc = $this->createWikiDocFromRequest($request, $response, $data);
        $this->createModel($request->header->account, $doc, WikiDocMapper::class, 'doc', $request->getOrigin());

        if (!empty($request->files)
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
        $path = $this->createWikiDir($doc);

        /** @var \Modules\Admin\Models\Account $account */
        $account = AccountMapper::get()->where('id', $request->header->account)->execute();

        if (!empty($uploadedFiles = $request->files)) {
            $uploaded = $this->app->moduleManager->get('Media')->uploadFiles(
                names: [],
                fileNames: [],
                files: $uploadedFiles,
                account: $request->header->account,
                basePath: __DIR__ . '/../../../Modules/Media/Files' . $path,
                virtualPath: $path,
            );

            $collection = null;
            foreach ($uploaded as $media) {
                $this->createModelRelation(
                    $request->header->account,
                    $doc->id,
                    $media->id,
                    WikiDocMapper::class,
                    'media',
                    '',
                    $request->getOrigin()
                );

                $accountPath = '/Accounts/' . $account->id . ' ' . $account->login
                    . '/Knowledgebase/' . ($doc->category?->id ?? '0')
                    . '/' . $doc->id;

                $ref            = new Reference();
                $ref->name      = $media->name;
                $ref->source    = new NullMedia($media->id);
                $ref->createdBy = new NullAccount($request->header->account);
                $ref->setVirtualPath($accountPath);

                $this->createModel($request->header->account, $ref, ReferenceMapper::class, 'media_reference', $request->getOrigin());

                if ($collection === null) {
                    $collection = $this->app->moduleManager->get('Media')->createRecursiveMediaCollection(
                        $accountPath,
                        $request->header->account,
                        __DIR__ . '/../../../Modules/Media/Files/Accounts/' . $account->id . '/Knowledgebase/' . ($doc->category?->id ?? '0') . '/' . $doc->id
                    );
                }

                $this->createModelRelation(
                    $request->header->account,
                    $collection->id,
                    $ref->id,
                    CollectionMapper::class,
                    'sources',
                    '',
                    $request->getOrigin()
                );
            }
        }

        if (!empty($mediaFiles = $request->getDataJson('media'))) {
            $collection = null;

            foreach ($mediaFiles as $file) {
                /** @var \Modules\Media\Models\Media $media */
                $media = MediaMapper::get()->where('id', (int) $file)->limit(1)->execute();
                $this->createModelRelation(
                    $request->header->account,
                    $doc->id,
                    $media->id,
                    WikiDocMapper::class,
                    'media',
                    '',
                    $request->getOrigin()
                );

                $ref            = new Reference();
                $ref->name      = $media->name;
                $ref->source    = new NullMedia($media->id);
                $ref->createdBy = new NullAccount($request->header->account);
                $ref->setVirtualPath($path);

                $this->createModel($request->header->account, $ref, ReferenceMapper::class, 'media_reference', $request->getOrigin());

                if ($collection === null) {
                    $collection = $this->app->moduleManager->get('Media')->createRecursiveMediaCollection(
                        $path,
                        $request->header->account,
                        __DIR__ . '/../../../Modules/Media/Files' . $path
                    );
                }

                $this->createModelRelation(
                    $request->header->account,
                    $collection->id,
                    $ref->id,
                    CollectionMapper::class,
                    'sources',
                    '',
                    $request->getOrigin()
                );
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
            . ($doc->category?->id ?? '0') . '/'
            . $doc->id;
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
        $doc->doc         = Markdown::parse($request->getDataString('plain') ?? '');
        $doc->docRaw      = $request->getDataString('plain') ?? '';
        $doc->isVersioned = $request->getDataBool('versioned') ?? false;
        $doc->category    = new NullWikiCategory($request->getDataInt('category') ?? 1);
        $doc->app         = new NullWikiApp($request->getDataInt('app') ?? 1);
        $doc->version     = $request->getDataString('version') ?? '';
        $doc->setLanguage((string) ($request->getDataString('language') ?? $request->header->l11n->language));
        $doc->setStatus($request->getDataInt('status') ?? WikiStatus::INACTIVE);

        if (!empty($tags = $request->getDataJson('tags'))) {
            foreach ($tags as $tag) {
                if (!isset($tag['id'])) {
                    $request->setData('title', $tag['title'], true);
                    $request->setData('color', $tag['color'], true);
                    $request->setData('icon', $tag['icon'] ?? null, true);
                    $request->setData('language', $tag['language'], true);

                    $internalResponse = new HttpResponse();
                    $this->app->moduleManager->get('Tag')->apiTagCreate($request, $internalResponse, null);

                    if (!\is_array($data = $internalResponse->get($request->uri->__toString()))) {
                        continue;
                    }

                    $doc->addTag($data['response']);
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
        if (($val['title'] = !$request->hasData('title'))
            || ($val['plain'] = !$request->hasData('plain'))
            || ($val['status'] = (
                $request->hasData('status')
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
        if (($val['name'] = !$request->hasData('name'))
            || ($val['category'] = !$request->hasData('category'))
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
            $response->data['wiki_category_l11n_create'] = new FormValidation($val);
            $response->header->status                    = RequestStatusCode::R_400;

            return;
        }

        $l11nWikiCategory = $this->createWikiCategoryL11nFromRequest($request);
        $this->createModel($request->header->account, $l11nWikiCategory, WikiCategoryL11nMapper::class, 'wiki_category_l11n', $request->getOrigin());

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Localization', 'Localization successfully created', $l11nWikiCategory);
    }

    /**
     * Method to create tag localization from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return BaseStringL11n
     *
     * @since 1.0.0
     */
    private function createWikiCategoryL11nFromRequest(RequestAbstract $request) : BaseStringL11n
    {
        $l11nWikiCategory      = new BaseStringL11n();
        $l11nWikiCategory->ref = $request->getDataInt('category') ?? 0;
        $l11nWikiCategory->setLanguage(
            $request->getDataString('language') ?? $request->header->l11n->language
        );
        $l11nWikiCategory->content = $request->getDataString('name') ?? '';

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
        /** @var \Modules\Knowledgebase\Models\WikiDoc $doc */
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
        /** @var \Modules\Knowledgebase\Models\WikiDoc $old */
        $old = WikiDocMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $old = clone $old;
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
        /** @var \Modules\Knowledgebase\Models\WikiDoc $doc */
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
            $response->data[$request->uri->__toString()] = new FormValidation($val);
            $response->header->status                    = RequestStatusCode::R_400;

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
        $category->app = new NullWikiApp($request->getDataInt('app') ?? 1);
        $category->setL11n(
            $request->getDataString('name') ?? '',
            $request->getDataString('language') ?? $request->header->l11n->language
        );

        if ($request->hasData('parent')) {
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
        if (($val['name'] = !$request->hasData('name'))) {
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
        /** @var \Modules\Knowledgebase\Models\WikiCategory $category */
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
        /** @var \Modules\Knowledgebase\Models\WikiCategory $old */
        $old = WikiCategoryMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $old = clone $old;
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
        /** @var \Modules\Knowledgebase\Models\WikiCategory $category */
        $category = WikiCategoryMapper::get()->where('id', (int) $request->getData('id'))->execute();

        $category->setL11n(
            $request->getDataString('name') ?? $category->getL11n(),
            $request->getDataString('language') ?? $request->header->l11n->language
        );

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
        /** @var \Modules\Knowledgebase\Models\WikiCategory $category */
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
            $response->data[$request->uri->__toString()] = new FormValidation($val);
            $response->header->status                    = RequestStatusCode::R_400;

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
        $app->unit = $request->getDataInt('unit');

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
        if (($val['name'] = !$request->hasData('name'))) {
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
        /** @var \Modules\Knowledgebase\Models\WikiApp $app */

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
        /** @var \Modules\Knowledgebase\Models\WikiApp $old */
        $old = WikiAppMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $old = clone $old;
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
        /** @var \Modules\Knowledgebase\Models\WikiApp $app */
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
        /** @var \Modules\Knowledgebase\Models\WikiApp $app */
        $app = WikiAppMapper::get()->where('id', (int) $request->getData('id'))->execute();

        $this->deleteModel($request->header->account, $app, WikiAppMapper::class, 'app', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'App', 'App successfully deleted', $app);
    }
}
