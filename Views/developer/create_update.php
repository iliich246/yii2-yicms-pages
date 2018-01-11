<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use Iliich246\YicmsPages\Base\Pages;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\Fields\FieldsDevModalWidget;
use Iliich246\YicmsCommon\Files\FilesDevModalWidget;
use Iliich246\YicmsCommon\Images\ImagesDevModalWidget;
use Iliich246\YicmsCommon\Conditions\ConditionsDevModalWidget;

/** @var $this \yii\web\View */
/** @var $page \Iliich246\YicmsPages\Base\Pages */
/** @var $devFieldGroup \Iliich246\YicmsCommon\Fields\DevFieldsGroup */
/** @var $fieldTemplatesTranslatable FieldTemplate[] */
/** @var $fieldTemplatesSingle FieldTemplate[] */
/** @var $filesBlocks \Iliich246\YicmsCommon\Files\FilesBlock[] */
/** @var $devFilesGroup \Iliich246\YicmsCommon\Files\DevFilesGroup */
/** @var $imagesBlocks \Iliich246\YicmsCommon\Images\ImagesBlock[] */
/** @var $devImagesGroup \Iliich246\YicmsCommon\Images\DevImagesGroup */
/** @var $devConditionsGroup Iliich246\YicmsCommon\Conditions\DevConditionsGroup */
/** @var $conditionTemplates Iliich246\YicmsCommon\Conditions\ConditionTemplate[] */
/** @var $success bool */

$js = <<<JS
;(function() {
    var pjaxContainer = $('#update-page-container');

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
        <?php if ($page->scenario == Pages::SCENARIO_CREATE): ?>
            <h1>Create Page</h1>
        <?php else: ?>
            <h1>Update Page</h1>
            <h2>IMPORTANT! Do not change page names in production without serious reason!</h2>
        <?php endif; ?>
    </div>

    <div class="row content-block breadcrumbs">
        <a href="<?= Url::toRoute(['list']) ?>"><span>Pages list</span></a> <span> / </span>
        <?php if ($page->scenario == Pages::SCENARIO_CREATE): ?>
            <span>Create page</span>
        <?php else: ?>
            <span>Update page</span>
        <?php endif; ?>
    </div>

    <div class="row content-block form-block">
        <div class="col-xs-12">
            <div class="content-block-title">
                <?php if ($page->scenario == Pages::SCENARIO_CREATE): ?>
                    <h3>Create page essence</h3>
                <?php else: ?>
                    <h3>Update page essence</h3>
                <?php endif; ?>
            </div>
            <?php if ($page->scenario == Pages::SCENARIO_UPDATE): ?>
                <div class="row control-buttons">
                    <div class="col-xs-12">

                        <a href="<?= Url::toRoute(['page-translates', 'id' => $page->id]) ?>"
                           class="btn btn-primary">
                            Page name translates
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <?php Pjax::begin([
                'options' => [
                    'id' => 'update-page-container',
                ]
            ]) ?>
            <?php $form = ActiveForm::begin([
                'id' => 'create-update-page-form',
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

            <div class="row">
                <div class="col-xs-12">
                    <?= $form->field($page, 'program_name') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <?= $form->field($page, 'system_route') ?>
                </div>
            </div>

            <?php if ($page->scenario == Pages::SCENARIO_CREATE): ?>
                <div class="row">
                    <div class="col-xs-12">
                        <?= $form->field($page, 'standardFields')->checkbox() ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-xs-12">
                    <?= $form->field($page, 'editable')->checkbox() ?>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <?= $form->field($page, 'visible')->checkbox() ?>
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

    <?php if ($page->scenario == Pages::SCENARIO_CREATE): return; endif;?>

    <?= $this->render('@yicms-common/views/pjax/update-fields-list-container', [
        'fieldTemplateReference' => $page->getFieldTemplateReference(),
        'fieldTemplatesTranslatable' => $fieldTemplatesTranslatable,
        'fieldTemplatesSingle' => $fieldTemplatesSingle
    ]) ?>

    <?= FieldsDevModalWidget::widget([
        'devFieldGroup' => $devFieldGroup,
    ])
    ?>

    <?= $this->render('@yicms-common/Views/pjax/update-files-list-container', [
        'fileTemplateReference' => $page->getFileTemplateReference(),
        'filesBlocks' => $filesBlocks,
    ]) ?>

    <?= FilesDevModalWidget::widget([
        'devFilesGroup' => $devFilesGroup,
        'action' => Url::toRoute(['/pages/dev/update', 'id' => $page->id])
    ]) ?>

    <?= $this->render('@yicms-common/Views/pjax/update-images-list-container', [
        'imageTemplateReference' => $page->getImageTemplateReference(),
        'imagesBlocks' => $imagesBlocks,
    ]) ?>

    <?= ImagesDevModalWidget::widget([
        'devImagesGroup' => $devImagesGroup,
        'action' => Url::toRoute(['/pages/dev/update', 'id' => $page->id])
    ]) ?>

    <?= $this->render('@yicms-common/Views/pjax/update-conditions-list-container', [
        'conditionTemplateReference' => $page->getConditionTemplateReference(),
        'conditionsTemplates' => $conditionTemplates,
    ]) ?>

    <?= ConditionsDevModalWidget::widget([
        'devConditionsGroup' => $devConditionsGroup,
        'action' => Url::toRoute(['/pages/dev/update', 'id' => $page->id])
    ]) ?>
