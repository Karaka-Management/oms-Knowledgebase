<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Knowledgebase
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use \phpOMS\Uri\UriFactory;

/** @var \phpOMS\Views\View $this */
/** @var \Modules\Knowledgebase\Models\WikiCategory[] $categories */
$categories = $this->data['categories'] ?? [];

/** @var \Modules\Knowledgebase\Models\WikiCategory $category */
$category = $this->data['category'] ?? [];

/** @var \Modules\Knowledgebase\Models\WikiDoc[] $documents */
$documents = $this->data['docs'] ?? [];

/** @var \Modules\Knowledgebase\Models\WikiApp[] $apps */
$apps = $this->data['apps'] ?? [];

echo $this->data['nav']->render(); ?>
<div class="row">
    <div class="col-xs-12 col-md-8 col-lg-9">
        <div class="row">
            <div class="col-xs-12">
                <div class="portlet">
                    <div class="portlet-head">
                        <?= $this->getHtml('Docs'); ?>
                        <i class="g-icon download btn end-xs">download</i>
                    </div>
                    <div class="slider">
                    <table class="default sticky">
                        <thead>
                        <tr>
                            <td class="wf-100"><?= $this->getHtml('Name'); ?>
                        <tbody>
                        <?php foreach ($documents as $key => $value) :
                                $url = UriFactory::build('{/base}/wiki/doc/view?id=' . $value->id); ?>
                        <tr tabindex="0" data-href="<?= $url; ?>">
                            <td data-label="<?= $this->getHtml('Name'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->name); ?></a>
                        <?php endforeach; ?>
                        <?php if (empty($documents)) : ?>
                        <tr><td colspan="1" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                        <?php endif; ?>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-md-4 col-lg-3">
        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('App'); ?></div>
            <div class="portlet-body">
                <form>
                    <select id="iApp" name="app" data-action='[{"listener": "change", "action": [{"key": 1, "type": "redirect", "uri": "{%}&wiki={#iApp}", "target": "self"}]}]'>
                        <?php foreach ($apps as $app) : ?>
                            <option value="<?= $app->id; ?>"<?= $this->request->getDataInt('wiki') === $app->id ? ' selected' : ''; ?>><?= $this->printHtml($app->name); ?>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </section>

        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Categories'); ?></div>
            <div class="portlet-body">
                <ul>
                    <li><a href="<?= UriFactory::build('{/base}/wiki/doc/list?{?}&category=' . $category->parent?->id); ?>"><?= $this->printHtml('..'); ?></a>
                    <?php foreach ($categories as $category) : ?>
                        <li><a href="<?= UriFactory::build('{/base}/wiki/doc/list?{?}&category=' . $category->id); ?>"><?= $this->printHtml($category->getL11n()); ?></a>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>
    </div>
</div>