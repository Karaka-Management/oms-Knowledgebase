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

use \phpOMS\Uri\UriFactory;
use phpOMS\Utils\Parser\Markdown\Markdown;

/** @var \phpOMS\Views\View $this */
/** @var \Modules\Knowledgebase\Models\WikiCategory[] $categories */
$categories = $this->data['categories'] ?? [];

/** @var \Modules\Knowledgebase\Models\WikiDoc[] $documents */
$documents = $this->data['docs'] ?? [];

/** @var \Modules\Knowledgebase\Models\WikiApp[] $apps */
$apps = $this->data['apps'] ?? [];

echo $this->data['nav']->render(); ?>
<div class="row">
    <div class="col-xs-12 col-md-8 col-lg-9">
        <div class="row">
            <?php foreach ($documents as $doc) : $url = UriFactory::build('{/base}/wiki/doc/single?id=' . $doc->id); ?>
            <div class="col-xs-12 plain-grid">
                <div class="portlet">
                    <div class="portlet-head"><a href="<?= $url; ?>"><?= $this->printHtml($doc->name); ?></a></div>
                    <div class="portlet-body">
                        <article>
                            <?= Markdown::parse(\substr($doc->docRaw, 0, 500)); ?>
                        </article>
                        <?php $tags = $doc->getTags(); foreach ($tags as $tag) : ?>
                            <span class="tag" style="background: <?= $this->printHtml($tag->color); ?>"><?= empty($tag->icon) ? '' : ''; ?><?= $this->printHtml($tag->getL11n()); ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div class="portlet-foot">
                        <a href="<?= $url; ?>" class="button floatRight"><?= $this->getHtml('More', '0', '0'); ?></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($documents)) : ?>
            <div class="emptyPage"></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-xs-12 col-md-4 col-lg-3">
        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('App'); ?></div>
            <div class="portlet-body">
                <form>
                    <select name="app">
                        <?php foreach ($apps as $app) : ?>
                            <option><?= $this->printHtml($app->name); ?>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </section>

        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Categories'); ?></div>
            <div class="portlet-body">
                <ul>
                    <?php foreach ($categories as $category) : ?>
                        <li><a href="<?= UriFactory::build('{/base}/wiki/doc/list?{?}&id=' . $category->id); ?>"><?= $this->printHtml($category->getL11n()); ?></a>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>
    </div>
</div>