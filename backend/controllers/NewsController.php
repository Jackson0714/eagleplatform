<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\Cors;
use app\models\News;
use app\utils\MongodbUtil;
use yii\helpers\ArrayHelper;
use yii\web\UnauthorizedHttpException;
use yii\web\BadRequestHttpException;
use yii\data\Pagination;

class NewsController extends BaseController
{
    public function actionGetNews()
    {
        return News::find()->all();
    }

    public function actionCreateNews()
    {
        $params = Yii::$app->request->post();
        $news = new News();
        $news->title = $params['title'];
        $news->contentUrl = $params['contentUrl'];
        $news->imgs = $params['imgs'];
        $news->startDate = MongodbUtil::convertToMongoDate($params['startDate']);
        $news->endDate = MongodbUtil::convertToMongoDate($params['endDate']);

        $news->save();

        return ['status' => 'ok'];
    }
}
