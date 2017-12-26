<?php

namespace Iliich246\YicmsPages\Controllers;

use Yii;
use yii\base\Model;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use Iliich246\YicmsCommon\Languages\Language;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\Fields\DevFieldsGroup;
use Iliich246\YicmsCommon\Fields\FieldsDevModalWidget;
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
     * @param $id *
     * @return string
     * @throws NotFoundHttpException
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

            return FieldsDevModalWidget::widget([
                'devFieldGroup' => $devFieldGroup,
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

    public function actionDeletePage()
    {
        //TODO: implement page delete action
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
}
