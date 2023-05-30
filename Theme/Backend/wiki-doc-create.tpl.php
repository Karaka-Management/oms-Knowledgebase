<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\News
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\Knowledgebase\Models\NullWikiDoc;
use Modules\Knowledgebase\Models\WikiStatus;
use phpOMS\Uri\UriFactory;

/** @var \Modules\Knowledgebase\Models\WikiDoc $wiki */
$wiki         = $this->getData('doc') ?? new NullWikiDoc();
$isNewDoc     = $wiki->id === 0;
$languages    = \phpOMS\Localization\ISO639Enum::getConstants();

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
                    <table class="layout wf-100">
                        <tr><td>
                                <select name="status" id="iStatus">
                                    <option value="<?= $this->printHtml((string) WikiStatus::DRAFT); ?>"<?= $wiki->getStatus() === WikiStatus::DRAFT ? ' selected' : ''; ?>><?= $this->getHtml('Draft'); ?>
                                    <option value="<?= $this->printHtml((string) WikiStatus::ACTIVE); ?>"<?= $wiki->getStatus() === WikiStatus::ACTIVE ? ' selected' : ''; ?>><?= $this->getHtml('Active'); ?>
                                </select>
                        <tr><td><label for="iLanguages"><?= $this->getHtml('Language'); ?></label>
                        <tr><td>
                                <select id="iLanguages" name="lang">
                                    <?php foreach ($languages as $code => $language) : $code = \strtolower(\substr($code, 1)); ?>
                                    <option value="<?= $this->printHtml($code); ?>"<?= $this->printHtml($code === $wiki->getLanguage() ? ' selected' : ''); ?>><?= $this->printHtml($language); ?>
                                    <?php endforeach; ?>
                                </select>
                    </table>
                </div>
                <div class="portlet-foot">
                    <table class="layout wf-100">
                        <tr>
                            <td>
                                <?php if ($isNewDoc) : ?>
                                    <a href="<?= UriFactory::build('{/base}//wiki/dashboard'); ?>" class="button"><?= $this->getHtml('Delete', '0', '0'); ?></a>
                                <?php else : ?>
                                    <input type="submit" name="deleteButton" id="iDeleteButton" value="<?= $this->getHtml('Delete', '0', '0'); ?>">
                                <?php endif; ?>
                            <td class="rightText">
                                <input type="submit" name="saveButton" id="iSaveButton" value="<?= $this->getHtml('Save', '0', '0'); ?>">
                    </table>
                </div>
            </form>
        </section>

        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Categories'); ?></div>
            <div class="portlet-body">
                <table class="layout wf-100">
                    <tr><td><label for="iApp"><?= $this->getHtml('App'); ?></label>
                    <tr><td><select id="iApp" name="app"></select>
                    <tr><td><label for="iCategory"><?= $this->getHtml('Category'); ?></label>
                    <tr><td><select id="iCategory" name="category"></select>
                    <tr><td><?= $this->getHtml('Tags', 'Tag'); ?>
                    <tr><td><?= $this->getData('tagSelector')->render('iTag', 'tag', 'fEditor', false); ?>
                </table>
            </div>
        </section>
    </div>
</div>
