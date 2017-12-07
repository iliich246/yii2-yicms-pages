<?php

namespace Iliich246\YicmsPages\Controllers;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Iliich246\YicmsCommon\Languages\Language;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\Fields\DevFieldsGroup;
use Iliich246\YicmsCommon\Widgets\FieldsDevInputWidget;
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
//            'root' => [
//                'class' => DevFilter::className(),
//                'except' => ['login-as-root'],
//            ],
        ];
    }

    /**
     * Render list of all pages
     * @return string
     */
    public function actionList()
    {
        $pages = Pages::find()->all();

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
     * @param $id *
     * @param null|string $fieldTemplateReference
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id, $fieldTemplateReference = null)
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
        $devFieldGroup->setFieldsReferenceAble($page);
        $devFieldGroup->initialize($fieldTemplateReference);

        //try to load validate and save field via pjax
        if ($devFieldGroup->load(Yii::$app->request->post()) && $devFieldGroup->validate()) {

            if (!$devFieldGroup->save()) {
                //TODO: bootbox error
            }

            return FieldsDevInputWidget::widget([
                'devFieldGroup' => $devFieldGroup,
                'dataSaved' => true,
            ]);
        }

        //Need to refresh fields modal window via pjax
        if (Yii::$app->request->isPjax &&
            Yii::$app->request->post('_pjax') == '#'.FieldsDevInputWidget::getPjaxContainerId())
        {
            return FieldsDevInputWidget::widget([
                'devFieldGroup' => $devFieldGroup
            ]);
        }

        //Need to update fields list vie Pjax
        if (Yii::$app->request->isPjax &&
            Yii::$app->request->post('_pjax') == '#update-fields-list-container') {

            $fieldTemplatesTranslatable = FieldTemplate::getListQuery($page->field_template_reference)
                ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE])
                ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
                ->all();

            $fieldTemplatesSingle = FieldTemplate::getListQuery($page->field_template_reference)
                ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_SINGLE])
                ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
                ->all();

            return $this->render('/pjax/update-fields-list-container', [
                'page' => $page,
                'fieldTemplatesTranslatable' => $fieldTemplatesTranslatable,
                'fieldTemplatesSingle' => $fieldTemplatesSingle
            ]);
        }

        $fieldTemplatesTranslatable = FieldTemplate::getListQuery($page->field_template_reference)
                                        ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE])
                                        ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
                                        ->all();

        $fieldTemplatesSingle = FieldTemplate::getListQuery($page->field_template_reference)
                                        ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_SINGLE])
                                        ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
                                        ->all();

        return $this->render('/developer/create_update', [
            'page' => $page,
            'devFieldGroup' => $devFieldGroup,
            'fieldTemplatesTranslatable' => $fieldTemplatesTranslatable,
            'fieldTemplatesSingle' => $fieldTemplatesSingle
        ]);
    }

    /**
     * Display page for work with admin translations of page
     * @param $id
     * @return string
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
        }

        return $this->render('/developer/page_translates', [
            'page' => $page,
            'translateModels' => $translateModels,
        ]);
    }

    /**
     * Action for up field template order
     * @param $id
     * @param $fieldTemplateId
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionFieldTemplateUpOrder($id, $fieldTemplateId)
    {
        /** @var Pages $page */
        $page = Pages::findOne($id);

        if (!$page) throw new NotFoundHttpException('Wrong page id');

        /** @var FieldTemplate $fieldTemplate */
        $fieldTemplate = FieldTemplate::findOne($fieldTemplateId);

        if (!$fieldTemplate) throw new NotFoundHttpException('Wrong fieldTemplateId');
        //throw new Exception(print_r($fieldTemplate, true));

        $fieldTemplate->upOrder();


        $fieldTemplatesTranslatable = FieldTemplate::getListQuery($page->field_template_reference)
                                            ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE])
                                            ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
                                            ->all();

        $fieldTemplatesSingle = FieldTemplate::getListQuery($page->field_template_reference)
                                            ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_SINGLE])
                                            ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
                                            ->all();

        if (Yii::$app->request->isPjax)
            return $this->render('/pjax/update-fields-list-container', [
                'page' => $page,
                'fieldTemplatesTranslatable' => $fieldTemplatesTranslatable,
                'fieldTemplatesSingle' => $fieldTemplatesSingle
            ]);
        else return $this->redirect(Url::toRoute(['update', 'id' => $id]));
    }

    /**
     * Action for up field template order
     * @param $id
     * @param $fieldTemplateId
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionFieldTemplateDownOrder($id, $fieldTemplateId)
    {
        /** @var Pages $page */
        $page = Pages::findOne($id);

        if (!$page) throw new NotFoundHttpException('Wrong page id');

        /** @var FieldTemplate $fieldTemplate */
        $fieldTemplate = FieldTemplate::findOne($fieldTemplateId);

        if (!$fieldTemplate) throw new NotFoundHttpException('Wrong fieldTemplateId');
        //throw new Exception(print_r($fieldTemplate, true));
        $fieldTemplate->downOrder();


        $fieldTemplatesTranslatable = FieldTemplate::getListQuery($page->field_template_reference)
                                            ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE])
                                            ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
                                            ->all();

        $fieldTemplatesSingle = FieldTemplate::getListQuery($page->field_template_reference)
                                        ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_SINGLE])
                                        ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
                                        ->all();

        if (Yii::$app->request->isPjax)
            return $this->render('/pjax/update-fields-list-container', [
                'page' => $page,
                'fieldTemplatesTranslatable' => $fieldTemplatesTranslatable,
                'fieldTemplatesSingle' => $fieldTemplatesSingle
            ]);
        else return $this->redirect(Url::toRoute(['update', 'id' => $id]));
    }

    /**
     * Delete page field template
     * @param $id
     * @param $fieldTemplateId
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDeleteFieldTemplate($id, $fieldTemplateId)
    {
        if (!Yii::$app->request->isPjax) throw new NotFoundHttpException();

        /** @var Pages $page */
        $page = Pages::findOne($id);

        if (!$page) throw new NotFoundHttpException('Wrong page id');

        /** @var FieldTemplate $fieldTemplate */
        $fieldTemplate = FieldTemplate::findOne($fieldTemplateId);

        if (!$fieldTemplate) throw new NotFoundHttpException('Wrong fieldTemplateId');

        //TODO: for field templates with constraints makes request of root password
//        if ($fieldTemplate->isConstraints())
//            return $this->redirect(Url::toRoute(['xxx', 'id' => $id]));

        $fieldTemplate->delete();

        $fieldTemplatesTranslatable = FieldTemplate::getListQuery($page->field_template_reference)
                                            ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE])
                                            ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
                                            ->all();

        $fieldTemplatesSingle = FieldTemplate::getListQuery($page->field_template_reference)
                                        ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_SINGLE])
                                        ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
                                        ->all();

        return $this->render('/pjax/update-fields-list-container', [
            'page' => $page,
            'fieldTemplatesTranslatable' => $fieldTemplatesTranslatable,
            'fieldTemplatesSingle' => $fieldTemplatesSingle
        ]);
    }
}
