<?php

use yii\widgets\Pjax;
use Iliich246\YicmsCommon\Fields\FieldTemplate;

/* @var $page \Iliich246\YicmsPages\Base\Pages */
/* @var $devFieldGroup \Iliich246\YicmsCommon\Fields\DevFieldsGroup */
/* @var $fieldTemplatesTranslatable FieldTemplate[] */
/* @var $fieldTemplatesSingle FieldTemplate[] */

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
        <?php if (isset($fieldTemplatesTranslatable) || isset($fieldTemplatesSingle)): ?>
            <?php Pjax::begin([
                'options' => [
                    'id' => 'update-fields-list-container'
                ]
            ]) ?>
            <div class="list-block">
                <?php if (isset($fieldTemplatesTranslatable)): ?>
                    <div class="row content-block-title">
                        <h4>Translatable fields:</h4>
                    </div>

                    <?php foreach ($fieldTemplatesTranslatable as $fieldTemplate): ?>
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
                <?php endif ?>
                <?php if (isset($fieldTemplatesSingle)): ?>
                    <div class="row content-block-title">
                        <br>
                        <h4>Single fields:</h4>
                    </div>
                    <?php foreach ($fieldTemplatesSingle as $fieldTemplate): ?>
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
                <?php endif; ?>
            </div>
            <?php Pjax::end() ?>
        <?php endif; ?>
    </div>
</div>
