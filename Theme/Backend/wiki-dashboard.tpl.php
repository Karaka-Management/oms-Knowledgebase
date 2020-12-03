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

use \phpOMS\Uri\UriFactory;
use phpOMS\Utils\Parser\Markdown\Markdown;

/** @var \phpOMS\Views\View $this */
/** @var \Modules\Knowledgebase\Models\WikiCategory[] $categories */
$categories = $this->getData('categories') ?? [];

/** @var \Modules\Knowledgebase\Models\WikiDoc[] $documents */
$documents = $this->getData('docs') ?? [];

/** @var \Modules\Knowledgebase\Models\WikiApp[] $apps */
$apps = $this->getData('apps') ?? [];

echo $this->getData('nav')->render(); ?>
<div class="row">
    <div class="col-xs-12 col-md-8 col-lg-9">
        <div class="row">
            <?php foreach ($documents as $doc) : $url = UriFactory::build('{/prefix}wiki/doc/single?id=' . $doc->getId()); ?>
            <div class="col-xs-12 plain-grid">
                <div class="portlet">
                    <div class="portlet-head"><a href="<?= $url; ?>"><?= $this->printHtml($doc->name); ?></a></div>
                    <div class="portlet-body">
                        <article>
                            <?= Markdown::parse(\substr($doc->docRaw, 0, 500)); ?>
                        </article>
                    </div>
                    <div class="portlet-foot">
                        <div class="overflowfix">
                            <?php $tags = $doc->getTags(); foreach ($tags as $tag) : ?>
                                <span class="tag" style="background: <?= $this->printHtml($tag->getColor()); ?>"><?= $this->printHtml($tag->getTitle()); ?></span>
                            <?php endforeach; ?>
                            <a href="<?= $url; ?>" class="button floatRight">More</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="col-xs-12 col-md-4 col-lg-3">
        <section class="portlet">
            <div class="portlet-head">App</div>
            <div class="portlet-body">
                <form>
                    <select>
                        <?php foreach ($apps as $app) : ?>
                            <option><?= $this->printHtml($app->name); ?>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </section>

        <section class="portlet">
            <div class="portlet-head">Categories</div>
            <div class="portlet-body">
                <ul>
                    <?php foreach ($categories as $category) : ?>
                        <li><a href="<?= UriFactory::build('{/prefix}wiki/doc/list?{?}&id=' . $category->getId()); ?>"><?= $this->printHtml($category->getName()); ?></a>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>
    </div>
</div>