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
use Modules\Knowledgebase\Models\NullWikiDoc;

/**
 * @var \Modules\Knowledgebase\Models\WikiCategory[] $categories
 * @var \Modules\Knowledgebase\Models\WikiDoc        $doc
 */
$categories = $this->getData('categories') ?? [];

/** @var \Modules\Knowledgebase\Models\WikiDoc $doc */
$doc = $this->getData('document') ?? new NullWikiDoc();

/** @var \Modules\Tag\Models\Tag[] $tag */
$tags = $doc->getTags();

/** @var bool $editable */
$editable = $this->getData('editable');

/** @var \phpOMS\Views\View $this */
echo $this->getData('nav')->render();
?>

<div class="row">
    <div class="col-xs-12 col-md-8 col-lg-9">
        <section class="portlet">
            <div class="portlet-head"><?= $this->printHtml($doc->name); ?></div>
            <div class="portlet-body">
                <article><?= $doc->doc; ?></article>
            </div>
            <?php if ($editable || !empty($tags)) : ?>
            <div class="portlet-foot">
                <div class="row">
                    <div class="col-xs-6 overflowfix">
                        <?php foreach ($tags as $tag) : ?>
                            <span class="tag" style="background: <?= $this->printHtml($tag->color); ?>"><?= $tag->icon !== null ? '<i class="' . $this->printHtml($tag->icon ?? '') . '"></i>' : ''; ?><?= $this->printHtml($tag->getTitle()); ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php if ($editable) : ?>
                    <div class="col-xs-6 rightText">
                        <a tabindex="0" class="button" href="<?= \phpOMS\Uri\UriFactory::build('{/prefix}wiki/doc/edit?id=' . $doc->getId()); ?>">Edit</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </section>
    </div>

    <div class="col-xs-12 col-md-4 col-lg-3">
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