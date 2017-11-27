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
/* @var $fieldTemplates FieldTemplate[] */

$modalName = FieldsDevInputWidget::getModalWindowName();
$formName  = FieldsDevInputWidget::getFormName();
$url = Url::toRoute([
    'update', 'id' => $page->id
]);

$js = <<<EOT


    $('.field-item').on('click', function(event) {

    //var container = $(this).closest('[data-pjax-container]')
    //var containerSelector = '#' + container.id
    //$.pjax.click(event, {container: '#test-pjax'})

    console.log($(this).find('p').data('field-template-id'));

    var templateData = $(this).find('p').data('field-template-id');

    $.pjax.defaults.timeout = 20000;
    $.pjax({
        url: '{$url}&fieldTemplateId=' + templateData,
        container: '#test-pjax',
        scrollTo: false,
    });

    $('#{$modalName}').modal('show');

    });
EOT;

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

            <?php $pjax = Pjax::begin() ?>
            <?php $form = ActiveForm::begin([
                'id' => 'create-update-page-form',
                'options' => [
                    'data-pjax' => true,
                ],
            ]);
            ?>

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

    <div class="row content-block form-block">
        <div class="col-xs-12">
            <div class="content-block-title">
                <h3>List of page fields</h3>
            </div>
            <div class="row control-buttons">
                <div class="col-xs-12">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#fieldsDevModal">
                        <span class="glyphicon glyphicon-plus-sign"></span> Add new field
                    </button>
                </div>
            </div>
            <?php if (isset($fieldTemplates)): ?>
            <div class="list-block">
                <?php foreach ($fieldTemplates as $fieldTemplate): ?>
                    <div class="row list-items field-item">
                        <div class="col-xs-10 list-title">
                            <p data-field-template="<?= $fieldTemplate->field_template_reference ?>"
                               data-field-template-id="<?= $fieldTemplate->id ?>"
                            >
                                <?= $fieldTemplate->program_name ?> (<?= $fieldTemplate->getTypeName() ?>)
                            </p>
                        </div>
                        <div class="col-xs-2 list-controls">

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?= FieldsDevInputWidget::widget([
        'devFieldGroup' => $devFieldGroup
    ])
    ?>
