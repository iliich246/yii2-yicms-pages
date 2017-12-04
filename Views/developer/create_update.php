<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use Iliich246\YicmsPages\Base\Pages;
use Iliich246\YicmsCommon\Widgets\FieldsDevInputWidget;
use Iliich246\YicmsCommon\Fields\FieldTemplate;

/* @var $this \yii\web\View */
/* @var $page \Iliich246\YicmsPages\Base\Pages */
/* @var $devFieldGroup \Iliich246\YicmsCommon\Fields\DevFieldsGroup */
/* @var $fieldTemplatesTranslatable FieldTemplate[] */
/* @var $fieldTemplatesSingle FieldTemplate[] */
/* @var $success bool */

$bundle = \Iliich246\YicmsCommon\Assets\DeveloperAsset::register($this);

$modalName = FieldsDevInputWidget::getModalWindowName();
$formName  = FieldsDevInputWidget::getFormName();
$pjaxName  = FieldsDevInputWidget::getPjaxContainerId();
$url = Url::toRoute([
    'update', 'id' => $page->id
]);

$urlFieldOrderUp = Url::toRoute([
    'field-template-up-order',
    'id' => $page->id,
]);

$urlFieldOrderDown = Url::toRoute([
    'field-template-down-order',
    'id' => $page->id,
]);



$src = $bundle->baseUrl . '/loader.svg';

$js = <<<JS

$('#{$pjaxName}').on('pjax:send', function() {
  $('#{$modalName}').find('.modal-content').empty().append('<img src="{$src}" style="text-align:center">');
});

$('#{$pjaxName}').on('pjax:success', function(event) {

    if (!$(event.target).find('form').is('[data-yicms-saved]')) return false;

    $.pjax({
        url: '{$url}',
        container: '#update-fields-list-container',
        scrollTo: false,
        push: false,
        type: "POST",
        timeout: 2500
    });

    $('#{$modalName}').modal('hide');
});

//$('#{$pjaxName}')
//  .on('pjax:start', function() { $('#{$pjaxName}').fadeOut(200); })
//  .on('pjax:end',   function() { $('#{$pjaxName}').fadeIn(200); })

$(document).on('click', '.field-item p', function(event) {

    console.log($(this).data('field-template-id'));

    var templateData = $(this).data('field-template-id');

    $.pjax({
        url: '{$url}&fieldTemplateReference=' + templateData,
        container: '#{$pjaxName}',
        scrollTo: false,
        push: false,
        type: "POST",
        timeout: 2500
    });

    $('#{$modalName}').modal('show');
});

$('.add-field').on('click', function() {
    $.pjax({
        url: '{$url}',
        container: '#{$pjaxName}',
        scrollTo: false,
        push: false,
        type: "POST",
        timeout: 2500
    });
});

$(document).on('click', '.glyphicon-arrow-up', function() {
    $.pjax({
        url: '{$urlFieldOrderUp}&fieldTemplateId=' + $(this).data('fieldTemplateId'),
        container: '#update-fields-list-container',
        scrollTo: false,
        push: false,
        type: "POST",
        timeout: 2500
    });
});

$(document).on('click', '.glyphicon-arrow-down', function() {
    $.pjax({
        url: '{$urlFieldOrderDown}&fieldTemplateId=' + $(this).data('fieldTemplateId'),
        container: '#update-fields-list-container',
        scrollTo: false,
        push: false,
        type: "POST",
        timeout: 2500
    });
});

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

            <?php $pjax = Pjax::begin([
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

    <?= $this->render('/pjax/update-fields-list-container', [
        'page' => $page,
        'fieldTemplatesTranslatable' => $fieldTemplatesTranslatable,
        'fieldTemplatesSingle' => $fieldTemplatesSingle
    ]) ?>

    <?= FieldsDevInputWidget::widget([
        'devFieldGroup' => $devFieldGroup,
        'deleteLink' => Url::toRoute(['delete-field-template', 'id' => $page->id])
    ])
    ?>
