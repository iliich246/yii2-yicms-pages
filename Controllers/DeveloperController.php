<?php

namespace Iliich246\YicmsPages\Controllers;

use Iliich246\YicmsPages\Base\PagesConfigDb;
use Yii;
use yii\base\Model;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use Iliich246\YicmsCommon\Base\DevFilter;
use Iliich246\YicmsCommon\Base\CommonHashForm;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\Languages\Language;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\Fields\DevFieldsGroup;
use Iliich246\YicmsCommon\Fields\FieldsDevModalWidget;
use Iliich246\YicmsCommon\Files\FilesBlock;
use Iliich246\YicmsCommon\Files\DevFilesGroup;
use Iliich246\YicmsCommon\Files\FilesDevModalWidget;
use Iliich246\YicmsCommon\Images\ImagesBlock;
use Iliich246\YicmsCommon\Images\DevImagesGroup;
use Iliich246\YicmsCommon\Images\ImagesDevModalWidget;
use Iliich246\YicmsCommon\Conditions\ConditionTemplate;
use Iliich246\YicmsCommon\Conditions\DevConditionsGroup;
use Iliich246\YicmsCommon\Conditions\ConditionsDevModalWidget;
use Iliich246\YicmsPages\Base\Pages;
use Iliich246\YicmsPages\Base\PagesException;
use Iliich246\YicmsPages\Base\PageDevTranslatesForm;

/**
 * Class DeveloperController
 *
 * Controller for developer section in pages module
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class DeveloperController extends Controller
{
    /** @inheritdoc */
    public $layout = '@yicms-common/Views/layouts/developer';
    /** @inheritdoc */
    public $defaultAction = 'list';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'dev' => [
                'class' => DevFilter::class,
                'redirect' => function() {
                    return $this->redirect(Url::home());
                }
            ],
        ];
    }

    /**
     * Render list of all pages
     * @return string
     */
    public function actionList()
    {
        $pages = Pages::find()->orderBy([
            'pages_order' => SORT_ASC
        ])->all();

        return $this->render('/developer/list', [
            'pages' => $pages,
        ]);
    }

    /**
     * Creates new page essence
     * @return string|\yii\web\Response
     * @throws PagesException
     */
    public function actionCreate()
    {
        $page = new Pages();
        $page->scenario = Pages::SCENARIO_CREATE;

        if ($page->load(Yii::$app->request->post()) && $page->validate()) {

            if ($page->save()) {
                return $this->redirect(Url::toRoute(['update', 'id' => $page->id]));
            } else {
                //TODO: add bootbox error
            }
        }

        return $this->render('/developer/create_update', [
            'page' => $page,
        ]);
    }

    /**
     * Updates page
     * @param $id
     * @return string
     * @throws CommonException
     * @throws NotFoundHttpException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function actionUpdate($id)
    {
        /** @var Pages $page */
        $page = Pages::findOne($id);

        if (!$page)
            throw new NotFoundHttpException('Wrong page ID');

        $page->scenario = Pages::SCENARIO_UPDATE;

        //update page via pjax
        if ($page->load(Yii::$app->request->post()) && $page->validate()) {

            if ($page->save()) {
                $success = true;
            } else {
                $success = false;
            }

            return $this->render('/developer/create_update', [
                'page' => $page,
                'success' => $success
            ]);
        }

        //initialize fields group
        $devFieldGroup = new DevFieldsGroup();
        $devFieldGroup->setFieldTemplateReference($page->getFieldTemplateReference());
        $devFieldGroup->initialize(Yii::$app->request->post('_fieldTemplateId'));

        //try to load validate and save field via pjax
        if ($devFieldGroup->load(Yii::$app->request->post()) && $devFieldGroup->validate()) {

            if (!$devFieldGroup->save()) {
                //TODO: bootbox error
            }

            $page->annotate();

            return FieldsDevModalWidget::widget([
                'devFieldGroup' => $devFieldGroup,
                'dataSaved' => true,
            ]);
        }

        $devFilesGroup = new DevFilesGroup();
        $devFilesGroup->setFilesTemplateReference($page->getFileTemplateReference());
        $devFilesGroup->initialize(Yii::$app->request->post('_fileTemplateId'));

        //try to load validate and save field via pjax
        if ($devFilesGroup->load(Yii::$app->request->post()) && $devFilesGroup->validate()) {

            if (!$devFilesGroup->save()) {
                //TODO: bootbox error
            }

            $page->annotate();

            return FilesDevModalWidget::widget([
                'devFilesGroup' => $devFilesGroup,
                'dataSaved' => true,
            ]);
        }

        $devImagesGroup = new DevImagesGroup();
        $devImagesGroup->setImagesTemplateReference($page->getImageTemplateReference());
        $devImagesGroup->initialize(Yii::$app->request->post('_imageTemplateId'));

        //try to load validate and save image block via pjax
        if ($devImagesGroup->load(Yii::$app->request->post()) && $devImagesGroup->validate()) {

            if (!$devImagesGroup->save()) {
                //TODO: bootbox error
            }

            $page->annotate();

            return ImagesDevModalWidget::widget([
                'devImagesGroup' => $devImagesGroup,
                'dataSaved' => true,
            ]);
        }

        $devConditionsGroup = new DevConditionsGroup();
        $devConditionsGroup->setConditionsTemplateReference($page->getConditionTemplateReference());
        $devConditionsGroup->initialize(Yii::$app->request->post('_conditionTemplateId'));

        //try to load validate and save image block via pjax
        if ($devConditionsGroup->load(Yii::$app->request->post()) && $devConditionsGroup->validate()) {

            if (!$devConditionsGroup->save()) {
                //TODO: bootbox error
            }

            $page->annotate();

            return ConditionsDevModalWidget::widget([
                'devConditionsGroup' => $devConditionsGroup,
                'dataSaved' => true,
            ]);
        }

        $fieldTemplatesTranslatable = FieldTemplate::getListQuery($page->getFieldTemplateReference())
                                        ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE])
                                        ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
                                        ->all();

        $fieldTemplatesSingle = FieldTemplate::getListQuery($page->getFieldTemplateReference())
                                        ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_SINGLE])
                                        ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
                                        ->all();

        $filesBlocks = FilesBlock::getListQuery($page->getFileTemplateReference())
                                        ->orderBy([FilesBlock::getOrderFieldName() => SORT_ASC])
                                        ->all();

        $imagesBlocks = ImagesBlock::getListQuery($page->getImageTemplateReference())
                                        ->orderBy([ImagesBlock::getOrderFieldName() => SORT_ASC])
                                        ->all();

        $conditionTemplates = ConditionTemplate::getListQuery($page->getConditionTemplateReference())
                                        ->orderBy([ConditionTemplate::getOrderFieldName() => SORT_ASC])
                                        ->all();

        $page->annotate();

        return $this->render('/developer/create_update', [
            'page'                       => $page,
            'devFieldGroup'              => $devFieldGroup,
            'fieldTemplatesTranslatable' => $fieldTemplatesTranslatable,
            'fieldTemplatesSingle'       => $fieldTemplatesSingle,
            'devFilesGroup'              => $devFilesGroup,
            'filesBlocks'                => $filesBlocks,
            'devImagesGroup'             => $devImagesGroup,
            'imagesBlocks'               => $imagesBlocks,
            'devConditionsGroup'         => $devConditionsGroup,
            'conditionTemplates'         => $conditionTemplates
        ]);
    }

    /**
     * Display page for work with admin translations of page
     * @param $id
     * @return string
     * @throws CommonException
     * @throws NotFoundHttpException
     */
    public function actionPageTranslates($id)
    {
        /** @var Pages $page */
        $page = Pages::findOne($id);

        if (!$page) throw new NotFoundHttpException('Wrong page id');

        $languages = Language::getInstance()->usedLanguages();

        $translateModels = [];

        foreach($languages as $key => $language) {
            $pageTranslate = new PageDevTranslatesForm();
            $pageTranslate->setLanguage($language);
            $pageTranslate->setPage($page);
            $pageTranslate->loadFromDb();

            $translateModels[$key] = $pageTranslate;
        }

        if (Model::loadMultiple($translateModels, Yii::$app->request->post()) &&
            Model::validateMultiple($translateModels)) {

            /** @var PageDevTranslatesForm $translateModel */
            foreach($translateModels as $key=>$translateModel) {
                $translateModel->save();
            }

            return $this->render('/developer/page_translates', [
                'page'            => $page,
                'translateModels' => $translateModels,
                'success'         => true,
            ]);
        }

        return $this->render('/developer/page_translates', [
            'page'            => $page,
            'translateModels' => $translateModels,
        ]);
    }

    /**
     * Action for delete page
     * @param $id
     * @param bool $deletePass
     * @return \yii\web\Response
     * @throws CommonException
     * @throws NotFoundHttpException
     * @throws PagesException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeletePage($id, $deletePass = false)
    {
        /** @var Pages $page */
        $page = Pages::findOne($id);

        if (!$page) throw new NotFoundHttpException('Wrong page id');

        if ($page->isConstraints())
            if (!Yii::$app->security->validatePassword($deletePass, CommonHashForm::DEV_HASH))
                throw new PagesException('Wrong dev password');

        if ($page->delete())
            return $this->redirect(Url::toRoute(['list']));

        throw new PagesException('Delete error');

    }

    /**
     * Action for up page order
     * @param $pageId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionPageUpOrder($pageId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException();

        /** @var Pages $page */
        $page = Pages::findOne($pageId);

        if (!$page) throw new NotFoundHttpException('Wrong pageId = ' . $pageId);

        $page->configToChangeOfOrder();
        $page->upOrder();

        $pages = Pages::find()->orderBy([
            'pages_order' => SORT_ASC
        ])->all();

        return $this->render('/pjax/update-pages-list-container', [
            'pages' => $pages
        ]);
    }

    /**
     * Action for down page order
     * @param $pageId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionPageDownOrder($pageId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException();

        /** @var Pages $page */
        $page = Pages::findOne($pageId);

        if (!$page) throw new NotFoundHttpException('Wrong pageId = ' . $pageId);

        $page->configToChangeOfOrder();
        $page->downOrder();

        $pages = Pages::find()->orderBy([
            'pages_order' => SORT_ASC
        ])->all();

        return $this->render('/pjax/update-pages-list-container', [
            'pages' => $pages
        ]);
    }

    /**
     * Maintenance action for pages module
     * @return string
     * @throws PagesException
     */
    public function actionMaintenance()
    {
        $config = PagesConfigDb::getInstance();

        if ($config->load(Yii::$app->request->post()) && $config->validate()) {
            if ($config->save()) {
                return $this->render('/developer/maintenance', [
                    'config'  => $config,
                    'success' => true,
                ]);
            }

            throw new PagesException('Can`t save data in database');
        }

        return $this->render('/developer/maintenance', [
            'config' => $config
        ]);
    }
}
