<?php

use yii\helpers\Url;
use Iliich246\YicmsPages\Assets\PageDevAsset;

/* @var $this \yii\web\View */
/* @var $pages \Iliich246\YicmsPages\Base\Pages[] */

PageDevAsset::register($this);

?>
<div class="col-sm-9 content">
    <div class="row content-block content-header">
        <h1>List of pages</h1>
    </div>
    <div class="row content-block">
        <div class="col-xs-12">
            <div class="row control-buttons">
                <div class="col-xs-12">
                    <a href="<?= Url::toRoute(['create']) ?>"
                       class="btn btn-primary create-page-button"
                       data-home-url="<?= Url::base() ?>">
                        Create new page
                    </a>
                </div>
            </div>

            <?= $this->render('@yicms-pages/Views/pjax/update-pages-list-container', [
                'pages' => $pages
            ]) ?>

        </div>
    </div>
</div>
