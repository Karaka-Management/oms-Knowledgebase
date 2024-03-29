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

use phpOMS\Uri\UriFactory;

/** @var \Modules\Knowledgebase\Models\WikiCategory */
$category = $this->data['category'];

$isNew = $category->id === 0;

/** @var \phpOMS\Views\View $this */
echo $this->data['nav']->render(); ?>

<div class="row">
    <div class="col-xs-12 col-md-6">
        <section class="portlet">
            <form method="<?= $isNew ? 'PUT' : 'POST'; ?>" action="<?= UriFactory::build('{/api}wiki/category?csrf={$CSRF}'); ?>">
                <div class="portlet-head"><?= $this->getHtml('Category'); ?></div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label for="iId"><?= $this->getHtml('ID', '0', '0'); ?></label>
                        <input type="text" name="id" id="iId" value="<?= $category->id; ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label for="iName"><?= $this->getHtml('Name'); ?></label>
                        <input type="text" name="name" id="iName" value="<?= $this->printHtml($category->getL11n()); ?>"<?= $isNew ? '' : ' disabled'; ?>>
                    </div>

                    <div class="form-group">
                        <label for="iApp"><?= $this->getHtml('App'); ?></label>
                        <select name="app" id="iApp">
                            <?php foreach ($this->data['apps'] as $app) : ?>
                                <option value="<?= $app->id; ?>"<?= $app->id === $category->app->id ? ' selected': ''; ?>><?= $this->printHtml($app->name); ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="iParent"><?= $this->getHtml('Parent'); ?></label>
                        <select name="parent" id="iParent">
                            <?php foreach ($this->data['parents'] as $parent) : ?>
                                <option value="<?= $parent->id; ?>"<?= $parent->id === $category->parent?->id ? ' selected': ''; ?>><?= $this->printHtml($parent->getL11n()); ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="portlet-foot">
                    <?php if ($isNew) : ?>
                        <input id="iCreateSubmit" type="Submit" value="<?= $this->getHtml('Create', '0', '0'); ?>">
                    <?php else : ?>
                        <input id="iSaveSubmit" type="Submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
                    <?php endif; ?>
                </div>
            </form>
        </section>
    </div>
</div>

<?php if (!$isNew) : ?>
<div class="row">
    <?= $this->data['l11nView']->render(
        $this->data['l11nValues'],
        [],
        '{/api}wiki/category/l11n?csrf={$CSRF}'
    );
    ?>
</div>
<?php endif; ?>