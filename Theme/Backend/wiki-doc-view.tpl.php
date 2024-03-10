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
use Modules\Knowledgebase\Models\NullWikiDoc;

/**
 * @var \Modules\Knowledgebase\Models\WikiCategory[] $categories
 * @var \Modules\Knowledgebase\Models\WikiDoc        $doc
 */
$categories = $this->data['categories'] ?? [];

/** @var \Modules\Knowledgebase\Models\WikiDoc $doc */
$doc = $this->getData('document') ?? new NullWikiDoc();

/** @var bool $editable */
$editable = $this->data['editable'];

/** @var \phpOMS\Views\View $this */
echo $this->data['nav']->render();
?>

<div class="row">
    <div class="col-xs-12 col-md-8 col-lg-9">
        <section class="portlet">
            <div class="portlet-head"><?= $this->printHtml($doc->name); ?></div>
            <div class="portlet-body">
                <article><?= $doc->doc; ?></article>

                <?php foreach ($doc->tags as $tag) : ?>
                    <span class="tag" style="background: <?= $this->printHtml($tag->color); ?>">
                        <?= empty($tag->icon) ? '' : '<i class="g-icon">' . $this->printHtml($tag->icon) . '</i>'; ?>
                        <?= $this->printHtml($tag->getL11n()); ?>
                    </span>
                <?php endforeach; ?>

                <?php $files = $doc->files; foreach ($files as $file) : ?>
                        <span><a class="content" href="<?= UriFactory::build('{/base}/media/view?id=' . $file->id);?>"><?= $file->name; ?></a></span>
                <?php endforeach; ?>
            </div>
            <?php if ($editable || !empty($doc->tags)) : ?>
            <div class="portlet-foot">
                <a tabindex="0" class="button" href="<?= \phpOMS\Uri\UriFactory::build('wiki/doc/edit?id=' . $doc->id); ?>"><?= $this->getHtml('Edit', '0', '0'); ?></a>
            </div>
            <?php endif; ?>
        </section>
    </div>

    <div class="col-xs-12 col-md-4 col-lg-3">
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