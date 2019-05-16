<?php //template

use yii\helpers\Url;

/** @var $this \yii\web\View */
/** @var $pages \Iliich246\YicmsPages\Base\Pages[] */
/** @var $widget \app\yicms\Pages\Widgets\ModuleMenuWidget */

?>

<div class="row link-block">
    <div class="col-xs-12">
        <h2>List of pages </h2>
        <?php foreach ($pages as $page): ?>
            <a <?php if ($widget->isActive($page)): ?> class="active" <?php endif; ?>
                href="<?= Url::toRoute(['/pages/admin/edit-page', 'id' => $page->id]) ?>">
                <?= $page->name() ?>

                <?php if (\Iliich246\YicmsCommon\Base\CommonUser::isDev() && !$page->editable): ?>
                    (dev only)
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>
<hr>
