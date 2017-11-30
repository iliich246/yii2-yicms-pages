<?php

use yii\widgets\Pjax;
use Iliich246\YicmsCommon\Fields\FieldTemplate;

/* @var $devFieldGroup \Iliich246\YicmsCommon\Fields\DevFieldsGroup */
/* @var $fieldTemplates FieldTemplate[] */
/* @var $page \Iliich246\YicmsPages\Base\Pages */

?>

<div class="row content-block form-block">
    <div class="col-xs-12">
        <div class="content-block-title">
            <h3>List of page fields</h3>
        </div>
        <div class="row control-buttons">
            <div class="col-xs-12">
                <button class="btn btn-primary add-field" data-toggle="modal" data-target="#fieldsDevModal">
                    <span class="glyphicon glyphicon-plus-sign"></span> Add new field
                </button>
            </div>
        </div>
        <?php if (isset($fieldTemplates)): ?>
            <?php Pjax::begin([
                'options' => [
                    'id' => 'update-fields-list-container'
                ]
            ]) ?>
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
                            <?php if ($fieldTemplate->visible): ?>
                                <span class="glyphicon glyphicon-eye-open"></span>
                            <?php else: ?>
                                <span class="glyphicon glyphicon-eye-close"></span>
                            <?php endif; ?>
                            <?php if ($fieldTemplate->editable): ?>
                                <span class="glyphicon glyphicon-pencil"></span>
                            <?php endif; ?>
                            <?php if ($fieldTemplate->is_main): ?>
                                <span class="glyphicon glyphicon-tower"></span>
                            <?php endif; ?>
                            <?php if ($fieldTemplate->canUpOrder()): ?>
                                <span class="glyphicon glyphicon-arrow-up"
                                      data-page-id="<?= $page->id ?>"
                                      data-field-template-id="<?= $fieldTemplate->id ?>">
                                </span>
                            <?php endif; ?>
                            <?php if ($fieldTemplate->canDownOrder()): ?>
                                <span class="glyphicon glyphicon-arrow-down"
                                      data-page-id="<?= $page->id ?>"
                                      data-field-template-id="<?= $fieldTemplate->id ?>">
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php Pjax::end() ?>
        <?php endif; ?>
    </div>
</div>
