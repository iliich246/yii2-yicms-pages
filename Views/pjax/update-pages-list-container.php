<?php

use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $pages \Iliich246\YicmsPages\Base\Pages[] */

?>
<?php Pjax::begin([
    'options' => [
        'id' => 'update-pages-list-container'
    ],
    'linkSelector' => '.pj',
]) ?>
<div class="list-block">
    <?php foreach($pages as $page): ?>
        <div class="row list-items">
            <div class="col-xs-10 list-title">
                <a href="<?= Url::toRoute(['update', 'id' => $page->id]) ?>">
                    <p>
                        <?= $page->program_name ?>
                    </p>
                </a>
            </div>
            <div class="col-xs-2 list-controls">
                <?php if ($page->visible): ?>
                    <span class="glyphicon glyphicon-eye-open"></span>
                <?php else: ?>
                    <span class="glyphicon glyphicon-eye-close"></span>
                <?php endif; ?>
                <?php if ($page->editable): ?>
                    <span class="glyphicon glyphicon-pencil"></span>
                <?php endif; ?>
                <?php if ($page->canUpOrder()): ?>
                    <span class="glyphicon glyphicon-arrow-up"
                          data-page-id="<?= $page->id ?>"></span>
                <?php endif; ?>
                <?php if ($page->canDownOrder()): ?>
                    <span class="glyphicon glyphicon-arrow-down"
                          data-page-id="<?= $page->id ?>"></span>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php Pjax::end() ?>
