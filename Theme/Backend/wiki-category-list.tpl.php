<?php
/**
 * Karaka
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

use phpOMS\Uri\UriFactory;

/** @var \Modules\Knowledgebase\Models\WikiCategory[] $categories */
$categories = $this->data['categories'] ?? [];

/** @var \phpOMS\Views\View $this */
echo $this->data['nav']->render();
?>

<div class="row">
    <div class="col-xs-12">
        <div class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Categories'); ?><i class="lni lni-download download btn end-xs"></i></div>
            <div class="slider">
            <table class="default sticky">
                <thead>
                <tr>
                    <td><?= $this->getHtml('ID', '0', '0'); ?>
                    <td class="wf-100"><?= $this->getHtml('Name'); ?>
                    <td><?= $this->getHtml('Parent'); ?>
                <tbody>
                <?php foreach ($categories as $key => $value) :
                        $url = UriFactory::build('{/base}/admin/account/settings?{?}&id=' . $value->id); ?>
                <tr tabindex="0" data-href="<?= $url; ?>">
                    <td data-label="<?= $this->getHtml('ID', '0', '0'); ?>"><a href="<?= $url; ?>"><?= $value->id; ?></a>
                    <td data-label="<?= $this->getHtml('Name'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->getL11n()); ?></a>
                    <td data-label="<?= $this->getHtml('Parent'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->parent !== null ? $value->parent->getL11n() : ''); ?></a>
                <?php endforeach; ?>
                <?php if (empty($categories)) : ?>
                <tr><td colspan="3" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                <?php endif; ?>
            </table>
            </div>
            <div class="portlet-foot"></div>
        </div>
    </div>
</div>