<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\Cors;
use app\models\BackendUser;
use yii\helpers\ArrayHelper;
use yii\web\UnauthorizedHttpException;
use yii\web\BadRequestHttpException;
use yii\data\Pagination;

class BackendUserController extends BaseController
{
    public function actionBackendLogin()
    {
        $params = Yii::$app->request->post();
        if (empty($params['userName']) && empty($params['password'])) {
            throw new BadRequestHttpException('miss parameter');
        }

        $user = BackendUser::findOne(['name' => $params['userName']]);
        if ( $user ) {
            if( $user->password == $params['password']){
                return [
                    'code' =>200,
                    'data' => [
                        'token' => $user->name
                    ],
                    'msg' => 'success'
                ];
            }
            return [
                'code' =>200,
                'msg' => 'failed'
            ];
        }
        if($params['userName'] == 'admin' && $params['password'] == 'abc123_'){
            $user = new BackendUser;
            $user->name = $params['userName'];
            if (!$user->save()) {
                throw new ServerErrorHttpException('create user failed');
            }
            return [
                'code' =>200,
                'data' => [
                    'token' => 'admin'
                ],
                'msg' => 'success'
            ];
        } else {
            return [
                'code' =>200,
                'msg' => 'failed'
            ];
        }
    }

    public function actionUserInfo()
    {
        $params = Yii::$app->request->get();

        $user = BackendUser::findOne(['name' => $params['token']]);
        if($user){
            return [
                'code' => 200,
                'data' => [
                    'name' => $user->name,
                    'user_id' => (string)$user->_id,
                    'access' => [$user->name],
                    'token' => $user->name,
                    'avator' => $user->avator,
                    'accountRouter' => $user->accountRouter
                ],
                'msg' => ''
            ];
        }
        return [
            'code' => 200,
            'msg' => 'noUser'
        ];
    }

    public function actionLogout()
    {
        return [
            'code' => 200,
            'data' => null,
            'msg' => ''
        ];
    }

    public function actionCreateUser()
    {
        $params = Yii::$app->request->post();
        if (empty($params['userName'])) {
            throw new BadRequestHttpException('miss userName');
        }

        $user = BackendUser::findOne(['name' => $params['userName']]);
        if ( $user ) {
            return [
                'code' => 200,
                'msg' => 'hasUserName'
            ];
        }

        $user = new BackendUser;
        $user->name = $params['userName'];

        if (!$user->save()) {
            throw new ServerErrorHttpException('create user failed');
        }

        return [
           'code' => 200,
           'msg' => 'ok'
        ];

    }

    public function actionGetUserList()
    {
        $query = BackendUser::find()->where(['isDeleted' => BackendUser::NOT_DELETED, 'name' => [ '$ne' => 'admin' ]]);
        $count = $query->count();
        $orderBy['createdAt'] = SORT_ASC;
        $query = $query->orderBy($orderBy);
        $pagination = new Pagination(['totalCount' => $count]);
        $userList = $query->offset($pagination->offset)->limit($pagination->limit)->all();

        return [
            'code' => 200,
            'data' => [
                'items' => $userList,
                'currentPage' => $pagination->getPage(),
                'pageCount' => $pagination->getPageCount(),
                'perPage' => $pagination->getPageSize(),
                'totalCount' => $count

            ],
            'msg' => 'ok'
        ];
    }

    public function actionChangePassword()
    {
        $params = Yii::$app->request->post();
        if (empty($params['userName']) && empty($params['origin']) && empty($params['password'])) {
            throw new BadRequestHttpException('miss userName');
        }

        $user = BackendUser::findOne(['name' => $params['userName']]);
        if ( $user ) {
            if ( $user->password == $params['origin']) {
                $user->password = $params['password'];
                if (!$user->save()) {
                    throw new ServerErrorHttpException('create user failed');
                }

                return [
                   'code' => 200,
                   'msg' => 'ok'
                ];
            }
            return [
                'code' => 200,
                'msg' => 'wrongPassword'
             ];
        }
        return [
            'code' => 200,
            'msg' => 'noUser'
         ];
    }

    public function actionChangeRouter()
    {
        $params = Yii::$app->request->post();
        if (empty($params['id']) && empty($params['router'])) {
            return [
                'code' => 200,
                'msg' => 'error'
            ];
        }

        $user = BackendUser::findOne(['_id' => $params['id']]);
        if ( $user ) {
            $user->accountRouter = $params['router'];
            if (!$user->save()) {
                return [
                    'code' => 200,
                    'msg' => 'error'
                ];
            }

            return [
                'code' => 200,
                'msg' => 'ok'
            ];
        }
        return [
            'code' => 200,
            'msg' => 'noUser'
         ];
    }

    public function actionResetUser()
    {
        $params = Yii::$app->request->post();
        if (empty($params['id'])) {
            return [
                'code' => 200,
                'msg' => 'error'
            ];
        }

        $user = BackendUser::findOne(['_id' => $params['id']]);
        if ( $user ) {
            $user->password = 'abc123_';
            $user->accountRouter = [];
            if (!$user->save()) {
                return [
                    'code' => 200,
                    'msg' => 'error'
                ];
            }

            return [
                'code' => 200,
                'msg' => 'ok'
            ];
        }
        return [
            'code' => 200,
            'msg' => 'noUser'
         ];
    }

    public function actionDeleteUser()
    {
        $params = Yii::$app->request->post();
        if (empty($params['id'])) {
            throw new ServerErrorHttpException('delete user failed');
        }

        $user = BackendUser::findOne(['_id' => $params['id']]);
        if ( !$user->delete() ) {
            throw new ServerErrorHttpException('deleted project failed');
        }

        return ['code' => 200];
    }
}