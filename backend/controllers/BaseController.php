<?php

namespace app\controllers;

use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;

class BaseController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return ArrayHelper::merge([
            [
                'class' => Cors::className(),
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                    'Access-Control-Allow-Credentials' => false,
                    'Access-Control-Request-Headers' => ['*'],
                ],
            ],
        ], parent::behaviors());
    }
}
