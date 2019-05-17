<?php

namespace app\yicms\Pages\Controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\AdminFilter;
use Iliich246\YicmsCommon\Files\FilesBlock;
use Iliich246\YicmsCommon\Fields\FieldsGroup;
use Iliich246\YicmsCommon\Images\ImagesBlock;
use Iliich246\YicmsCommon\Conditions\ConditionsGroup;
use Iliich246\YicmsPages\Base\Pages;

/**
 * Class AdminController
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class AdminController extends Controller
{
    /** @inheritdoc */
    public $defaultAction = 'edit-page';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->layout = CommonModule::getInstance()->yicmsLocation . '/Common/Views/layouts/admin';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'admin' => [
                'class' => AdminFilter::class,
                'redirect' => function() {
                    return $this->redirect(Url::toRoute('/common/admin/login'));
                }
            ],
        ];
    }

    /**
     * Action for edit page
     * @param null $id
     * @return string
     * @throws NotFoundHttpException
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     * @throws \Iliich246\YicmsPages\Base\PagesException
     */
    public function actionEditPage($id = null)
    {
        if (!is_null($id)) {
            /** @var Pages $page */
            $page = Pages::getInstanceById($id);

            //TODO: make correct translates of error messages
            if (!$page) throw new NotFoundHttpException('Wrong page ID');

            if (!$page->editable && !CommonModule::isUnderDev())
                throw new NotFoundHttpException('Wrong page ID');
        } else {
            $pageQuery = Pages::find();

            if (!CommonModule::isUnderDev())
                $pageQuery->where([
                    'editable' => true,
                ]);

            $page = $pageQuery->one();

            if (!$page)
                throw new NotFoundHttpException('No pages');
        }

        $page->offAnnotation();

        $fieldsGroup = new FieldsGroup();
        $fieldsGroup->setFieldsReferenceAble($page);
        $fieldsGroup->initialize();

        //try to load validate and save field via pjax
        if ($fieldsGroup->load(Yii::$app->request->post()) && $fieldsGroup->validate()) {

            if (!$fieldsGroup->save()) {
                //TODO: bootbox error
            }

            return $this->render(CommonModule::getInstance()->yicmsLocation  . '/Common/Views/pjax/fields', [
                'fieldsGroup'            => $fieldsGroup,
                'fieldTemplateReference' => $page->getFieldTemplateReference(),
                'success'                => true,
            ]);
        }

        $conditionsGroup = new ConditionsGroup();
        $conditionsGroup->setConditionsReferenceAble($page);
        $conditionsGroup->initialize();

        if ($conditionsGroup->load(Yii::$app->request->post()) && $conditionsGroup->validate()) {
            $conditionsGroup->save();

            return $this->render(CommonModule::getInstance()->yicmsLocation  . '/Common/Views/conditions/conditions', [
                'conditionsGroup'            => $conditionsGroup,
                'conditionTemplateReference' => $page->getConditionTemplateReference(),
                'success'                    => true,
            ]);
        }

        /** @var FilesBlock $filesBlocks */
        $filesBlocksQuery = FilesBlock::find()->where([
            'file_template_reference' => $page->getFileTemplateReference(),
        ])->orderBy([
            FilesBlock::getOrderFieldName() => SORT_ASC
        ]);

        if (CommonModule::isUnderAdmin())
            $filesBlocksQuery->andWhere([
                'editable' => true,
            ]);

        $filesBlocks = $filesBlocksQuery->all();

        foreach ($filesBlocks as $fileBlock)
            $fileBlock->setFileReference($page->getFileReference());

        /** @var ImagesBlock $imagesBlock */
        $imagesBlockQuery = ImagesBlock::find()->where([
            'image_template_reference' => $page->getImageTemplateReference()
        ])->orderBy([
            ImagesBlock::getOrderFieldName() => SORT_ASC
        ]);

        if (CommonModule::isUnderAdmin())
            $imagesBlockQuery->andWhere([
                'editable' => true,
            ]);

        $imagesBlocks = $imagesBlockQuery->all();

        foreach ($imagesBlocks as $imagesBlock)
            $imagesBlock->setImageReference($page->getImageReference());

        return $this->render(CommonModule::getInstance()->yicmsLocation  . '/Pages/Views/admin/edit-page', [
            'page'            => $page,
            'fieldsGroup'     => $fieldsGroup,
            'filesBlocks'     => $filesBlocks,
            'imagesBlocks'    => $imagesBlocks,
            'conditionsGroup' => $conditionsGroup
        ]);
    }
}
