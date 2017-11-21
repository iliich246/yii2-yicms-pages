<?php

namespace Iliich246\YicmsPages\Controllers;

use Yii;
use yii\base\Model;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Iliich246\YicmsPages\Base\Pages;
use Iliich246\YicmsPages\Base\PagesException;
use Iliich246\YicmsCommon\Languages\Language;
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
        $model = new Pages();
        $model->scenario = Pages::SCENARIO_CREATE;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if ($model->save()) {
                return $this->redirect(Url::toRoute(['update', 'id' => $model->id]));
            } else {
                //TODO: add bootbox error
            }
        }

        return $this->render('/developer/create_update', [
            'model' => $model,
        ]);
    }

    /**
     * Updates page essence
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        /** @var Pages $model */
        $model = Pages::findOne($id);

        if (!$model)
            throw new NotFoundHttpException('Wrong page ID');

        $model->scenario = Pages::SCENARIO_UPDATE;

//        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
//            if ($model->save()) {
//                //return $this->redirect(Url::toRoute(['update', 'id' => $model->id]));
//            } else {
//                //TODO: add bootbox error
//            }
//        }

        return $this->render('/developer/create_update', [
            'model' => $model,
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
}
