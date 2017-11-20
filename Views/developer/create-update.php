<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use Iliich246\YicmsPages\Base\Pages;

/* @var $this \yii\web\View */
/* @var $model \Iliich246\YicmsPages\Base\Pages */

?>

<div class="col-sm-9 content">
    <div class="row content-block content-header">
        <?php if ($model->scenario == Pages::SCENARIO_CREATE): ?>
            <h1>Create Page</h1>
        <?php else: ?>
            <h1>Update Page</h1>
            <h2>IMPORTANT! Do not change page names in production without serious reason!</h2>
        <?php endif; ?>
    </div>

    <div class="row content-block breadcrumbs">
        <a href="<?= Url::toRoute(['list']) ?>"><span>Pages list</span></a> <span> / </span>
        <?php if ($model->scenario == Pages::SCENARIO_CREATE): ?>
            <span>create page</span>
        <?php else: ?>
            <span>update page</span>
        <?php endif; ?>
    </div>

    <div class="row content-block form-block">
        <div class="col-xs-12">
            <div class="content-block-title">
                <?php if ($model->scenario == Pages::SCENARIO_CREATE): ?>
                    <h3>Create page essence</h3>
                <?php else: ?>
                    <h3>Update page essence</h3>
                <?php endif; ?>
            </div>
            <?php if ($model->scenario == Pages::SCENARIO_UPDATE): ?>
                <div class="row control-buttons">
                    <div class="col-xs-12">
                        <a href="<?= Url::toRoute(['page-translates', 'id' => $model->id]) ?>"
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
                    <?= $form->field($model, 'program_name') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <?= $form->field($model, 'system_route') ?>
                </div>
            </div>

            <?php if ($model->scenario == Pages::SCENARIO_CREATE): ?>
                <div class="row">
                    <div class="col-xs-12">
                        <?= $form->field($model, 'standardFields')->checkbox() ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-xs-12">
                    <?= $form->field($model, 'editable')->checkbox() ?>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <?= $form->field($model, 'visible')->checkbox() ?>
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
<?php /* ?>
    <?php if ($pagesModel->scenario !== PagesModel::SCENARIO_CREATE): ?>

        <?= $this->render('createUpdateField', [
            'fieldsModel' => $fieldsModel
        ]) ?>

        <?php if ($fields): ?>
            <div class="row content-block form-block">
                <div class="col-xs-12">
                    <div class="content-block-title">
                        <h3>List of page fields</h3>
                    </div>
                    <div class="list-block">
                        <?php foreach ($fields as $field): ?>
                            <div class="row list-items">
                                <div class="col-xs-10 list-title">
                                    <a href="<?= Url::toRoute(['update-field', 'id' => $field->id]) ?>">
                                        <p>
                                            <?= $field->program_name ?> (<?= $field->getTypeName() ?>)
                                        </p>
                                    </a>
                                </div>
                                <div class="col-xs-2 list-controls">

                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($pagesModel->scenario !== PagesModel::SCENARIO_CREATE): ?>

        <?= $this->render('createUpdateFileBlock', [
            'fileBlockModel' => $fileBlockModel,
        ]) ?>

        <?php if ($filesBlocks): ?>
            <div class="row content-block form-block">
                <div class="col-xs-12">
                    <div class="content-block-title">
                        <h3>List of file blocks</h3>
                    </div>
                    <div class="list-block">
                        <?php foreach ($filesBlocks as $fileBlock): ?>
                            <div class="row list-items">
                                <div class="col-xs-10 list-title">
                                    <a href="<?= Url::toRoute(['update-file-block', 'id' => $fileBlock->id]) ?>">
                                        <p>
                                            <?= $fileBlock->program_name ?> (<?= $fileBlock->getTypeName() ?>)
                                        </p>
                                    </a>
                                </div>
                                <div class="col-xs-2 list-controls">

                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    <?php endif; ?>

    <?php if ($pagesModel->scenario !== PagesModel::SCENARIO_CREATE): ?>

        <?= $this->render('createUpdateGalleryBlock', [
            'galleryBlockModel' => $galleryBlockModel,
        ]) ?>

        <?php if ($galleryBlocks): ?>
            <div class="row content-block form-block">
                <div class="col-xs-12">
                    <div class="content-block-title">
                        <h3>List of file blocks</h3>
                    </div>
                    <div class="list-block">
                        <?php foreach ($galleryBlocks as $galleryBlock): ?>
                            <div class="row list-items">
                                <div class="col-xs-10 list-title">
                                    <a href="<?= Url::toRoute(['update-gallery-block', 'id' => $galleryBlock->id]) ?>">
                                        <p>
                                            <?= $galleryBlock->program_name ?> (<?= $galleryBlock->getTypeName() ?>)
                                        </p>
                                    </a>
                                </div>
                                <div class="col-xs-2 list-controls">

                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>
*/ ?>