<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\News
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\Knowledgebase\Models\NullWikiDoc;
use Modules\Knowledgebase\Models\WikiStatus;
use phpOMS\Uri\UriFactory;

/** @var \Modules\Knowledgebase\Models\WikiDoc $wiki */
$wiki      = $this->getData('doc') ?? new NullWikiDoc();
$isNewDoc  = $wiki->id === 0;
$languages = \phpOMS\Localization\ISO639Enum::getConstants();

/** @var \phpOMS\Views\View $this */
echo $this->data['nav']->render(); ?>

<div class="row">
    <div class="col-xs-12 col-md-9">
        <div id="testEditor" class="m-editor">
            <section class="portlet">
                <div class="portlet-body">
                    <input id="iTitle" type="text" name="title" form="docForm" value="<?= $wiki->name; ?>">
                </div>
            </section>

            <section class="portlet">
                <div class="portlet-body">
                    <?= $this->getData('editor')->render('iWiki'); ?>
                </div>
            </section>

            <div class="box wf-100">
            <?= $this->getData('editor')->getData('text')->render('iWiki', 'plain', 'docForm', $wiki->docRaw, $wiki->doc); ?>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-md-3">
        <section class="portlet">
            <form id="docForm" method="<?= $isNewDoc ? 'PUT' : 'POST'; ?>" action="<?= UriFactory::build('{/api}wiki/doc?' . ($isNewDoc ? '' : 'id={?id}&') . 'csrf={$CSRF}'); ?>">
                <div class="portlet-head"><?= $this->getHtml('Status'); ?></div>
                <div class="portlet-body">
                    <div class="form-group">
                        <select name="status" id="iStatus">
                            <option value="<?= WikiStatus::DRAFT; ?>"<?= $wiki->status === WikiStatus::DRAFT ? ' selected' : ''; ?>><?= $this->getHtml('Draft'); ?>
                            <option value="<?= WikiStatus::ACTIVE; ?>"<?= $wiki->status === WikiStatus::ACTIVE ? ' selected' : ''; ?>><?= $this->getHtml('Active'); ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="iLanguages"><?= $this->getHtml('Language'); ?></label>
                        <select id="iLanguages" name="lang">
                            <?php foreach ($languages as $code => $language) : $code = \strtolower(\substr($code, 1)); ?>
                            <option value="<?= $this->printHtml($code); ?>"<?= $code === $wiki->language ? ' selected' : ''; ?>><?= $this->printHtml($language); ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="portlet-foot">
                    <table class="layout wf-100">
                        <tr>
                            <td>
                                <?php if (!$isNewDoc) : ?>
                                    <input type="submit" formmethod="DELETE" name="deleteButton" id="iDeleteButton" value="<?= $this->getHtml('Delete', '0', '0'); ?>">
                                <?php endif; ?>
                            <td class="rT">
                                <input type="submit" name="saveButton" id="iSaveButton" value="<?= $this->getHtml('Save', '0', '0'); ?>">
                    </table>
                </div>
            </form>
        </section>

        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Categories'); ?></div>
            <div class="portlet-body">
                <div class="form-group">
                    <label for="iApp"><?= $this->getHtml('App'); ?></label>
                    <select id="iApp" form="docForm" name="app">
                        <?php foreach ($this->data['apps'] as $app) : ?>
                            <option value="<?= $app->id; ?>"<?= $app->id === $wiki->app?->id ? ' selected' : ''; ?>><?= $this->printHtml($app->name); ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="iCategory"><?= $this->getHtml('Category'); ?></label>
                    <select id="iCategory" form="docForm" name="category">
                        <?php foreach ($this->data['categories'] as $category) : ?>
                            <option value="<?= $category->id; ?>"<?= $category->id === $wiki->category?->id ? ' selected' : ''; ?>><?= $this->printHtml($category->name); ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!--
                <div class="form-group">
                    <?= $this->getHtml('Tags', 'Tag'); ?>
                    <?= $this->getData('tagSelector')->render('iTag', 'tag', 'fEditor', false); ?>
                </div>
                -->
            </div>
        </section>
    </div>
</div>
