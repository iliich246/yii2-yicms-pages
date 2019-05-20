<?php

use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $widget \Iliich246\YicmsPages\Widgets\ModuleDevMenuWidget */

?>

<div class="row link-block">
    <div class="col-xs-12">
        <h2>Pages module</h2>
        <a <?php if ($widget->route == 'pages/dev/list'): ?> class="active" <?php endif; ?>
            href="<?= Url::toRoute('/pages/dev/list') ?>">
            List of pages
        </a>
        <a <?php if (
            ($widget->route == 'pages/dev/create')
            ||
            ($widget->route == 'pages/dev/update')
        ):?> class="active" <?php endif; ?>
            href="<?= Url::toRoute('/pages/dev/create') ?>">
            Create/update page
        </a>
        <a <?php if ($widget->route == 'pages/dev/maintenance'): ?> class="active" <?php endif; ?>
            href="<?= Url::toRoute('/pages/dev/maintenance') ?>">
            Maintenance
        </a>
    </div>
</div>
<hr>
