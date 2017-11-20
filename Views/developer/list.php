<?php

use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $pages \app\modules\pages\models\PagesDb[] */

?>
<div class="col-sm-9 content">
    <div class="row content-block content-header">
        <h1>List of page essences</h1>
    </div>
    <div class="row content-block">
        <div class="col-xs-12">
            <div class="row control-buttons">
                <div class="col-xs-12">
                    <a href="<?= Url::toRoute(['create']) ?>" class="btn btn-primary">
                        Create new page essence
                    </a>
                </div>
            </div>

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
                            <?php if ($page->editable): ?>
                                <span class="glyphicon glyphicon-eye-open"></span>
                            <?php else: ?>
                                <span class="glyphicon glyphicon-eye-close"></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

