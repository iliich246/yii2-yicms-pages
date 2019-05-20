<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;

/** @var $this \yii\web\View */
/** @var $config \Iliich246\YicmsPages\Base\PagesConfigDb */

$js = <<<JS
;(function(){
    var pjaxContainer = $('#pjax-pages-maintenance-container');

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

$this->registerJs($js);
?>

<div class="col-sm-9 content">
    <div class="row content-block content-header">
        <h1>Pages module maintenance</h1>
    </div>

    <div class="row content-block form-block">
        <div class="col-xs-12">
            <div class="content-block-title">
                <?php $pjax = Pjax::begin([
                    'options' => [
                        'id' => 'pjax-pages-maintenance-container',
                    ],
                    'enablePushState' => false,
                    'enableReplaceState' => false
                ]) ?>
                <?php $form = ActiveForm::begin([
                    'id' => 'maintenance-form',
                    'options' => [
                        'data-pjax' => true,
                    ],
                ]);
                ?>

                <?php if (isset($success) && $success): ?>
                    <div class="alert alert-success alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">?</span></button>
                        <strong>Success!</strong>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-xs-12">
                        <?= $form->field($config, 'isGenerated') ?>
                    </div>
                </div>

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
</div>
