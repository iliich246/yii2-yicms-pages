<?php

namespace Iliich246\YicmsPages\Controllers;


use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Iliich246\YicmsPages\Base\Pages;
use Iliich246\YicmsPages\Base\PagesException;


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

        return $this->render('/developer/create-update', [
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

        return $this->render('/developer/create-update', [
            'model' => $model,
        ]);
    }
}
