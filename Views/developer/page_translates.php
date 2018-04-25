<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use Iliich246\YicmsPages\Base\Pages;
use Iliich246\YicmsPages\Base\PageDevTranslatesForm;
use Iliich246\YicmsCommon\Widgets\SimpleTabsTranslatesWidget;

//use app\modules\common\widgets\TabsTranslatesWidget;

/** @var $this \yii\web\View */
/** @var $page Pages */
/** @var $translateModels PageDevTranslatesForm[] */

$this->title = "Translations of page names";

$js = <<<JS
;(function() {
    var pjaxContainer   = $('#edit-page-names-container');
    var pjaxContainerId = '#edit-page-names-container';

    $(pjaxContainer).on('pjax:success', function() {
        $(".alert").hide().slideDown(500).fadeTo(500, 1);

        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove();
            });
        }, 3000);
    });

    $(pjaxContainer).on('pjax:error', function(xhr, textStatus) {
        bootbox.alert({
            size: 'large',
            title: "There are some error on ajax request!",
            message: textStatus.responseText,
            className: 'bootbox-error'
        });
    });
})();
JS;

$this->registerJs($js, $this::POS_READY);

?>
<div class="col-sm-9 content">
    <div class="row content-block content-header">
        <h1>Translations of page names</h1>
    </div>

    <div class="row content-block breadcrumbs">
        <a href="<?= Url::toRoute(['list']) ?>"><span>Pages list</span></a>
        <span> / </span>
        <a href="<?= Url::toRoute(['update', 'id' => $page->id]) ?>">
            <span>Update page (<?= $page->program_name ?>)</span>
        </a>
        <span> / </span>
        <span>Translations of page names</span>
    </div>

    <div class="row content-block form-block">
        <div class="col-xs-12">

            <div class="content-block-title">
                <h3>Pages names form</h3>
                <h4>Here are edited names of pages that admin see in the admin panel</h4>
            </div>
            <?php $pjax = Pjax::begin([
                'options' => [
                    'id' => 'edit-page-names-container',
                ]
            ]) ?>
            <?php $form = ActiveForm::begin([
                'id' => 'edit-page-names-form',
                'options' => [
                    'data-pjax' => true,
                ],
            ]);
            ?>

            <?php if (isset($success) && $success): ?>
                <div class="alert alert-success alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <strong>Success!</strong> Page data updated.
                </div>
            <?php endif; ?>

            <?= SimpleTabsTranslatesWidget::widget([
                'form' => $form,
                'translateModels' => $translateModels,
            ])
            ?>

            <div class="row control-buttons">
                <div class="col-xs-12">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                    <?= Html::resetButton('Cancel', ['class' => 'btn btn-default cancel-button']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
            <?php Pjax::end() ?>
        </div>
    </div>
</div>
