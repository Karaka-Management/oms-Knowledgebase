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

use phpOMS\Uri\UriFactory;

/** @var \Modules\Knowledgebase\Models\WikiCategory[] $categories */
$categories = $this->getData('categories') ?? [];

/** @var \phpOMS\Views\View $this */
echo $this->getData('nav')->render();
?>

<div class="row">
    <div class="col-xs-12">
        <div class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Categories'); ?><i class="fa fa-download floatRight download btn"></i></div>
            <table class="default sticky">
                <thead>
                <tr>
                    <td><?= $this->getHtml('ID', '0', '0'); ?>
                    <td class="wf-100"><?= $this->getHtml('Name'); ?>
                    <td><?= $this->getHtml('Parent'); ?>
                <tbody>
                <?php foreach ($categories as $key => $value) :
                        $url = UriFactory::build('{/prefix}admin/account/settings?{?}&id=' . $value->getId()); ?>
                <tr tabindex="0" data-href="<?= $url; ?>">
                    <td data-label="<?= $this->getHtml('ID', '0', '0'); ?>"><a href="<?= $url; ?>"><?= $value->getId(); ?></a>
                    <td data-label="<?= $this->getHtml('Name'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->getL11n()); ?></a>
                    <td data-label="<?= $this->getHtml('Parent'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($value->parent !== null ? $value->parent->getL11n() : ''); ?></a>
                <?php endforeach; ?>
                <?php if (empty($categories)) : ?>
                <tr><td colspan="3" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                <?php endif; ?>
            </table>
            <div class="portlet-foot"></div>
        </div>
    </div>
</div>