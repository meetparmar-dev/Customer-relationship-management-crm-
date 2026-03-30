<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpBearerAuth;
use common\models\User;
use backend\models\UserSearch;

class UserController extends Controller
{
    public function behaviors()
    {
        return [
            //JWT Authentication
            'authenticator' => [
                'class' => HttpBearerAuth::class,
            ],

            //HTTP Verb rules
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index'        => ['GET'],
                    'view'         => ['GET'],
                    'create'       => ['POST'],
                    'update'       => ['PUT', 'PATCH'],
                    'delete'       => ['DELETE'],
                    'activate-2fa' => ['PATCH'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->headers->set('Content-Type', 'application/json');
        return parent::beforeAction($action);
    }

    /**
     * GET /v1/users?page=1&per_page=10&search=keyword
     */

    public function actionIndex()
    {
        $searchModel = new UserSearch();

        $result = $searchModel->searchApi(
            Yii::$app->request->get()
        );

        return [
            'success' => true,
            'data' => $result['data'],
            'pagination' => $result['pagination'],
        ];
    }


    /**
     * GET /v1/users/{id}
     */
    public function actionView($id)
    {
        $user = User::find()->where(['id' => $id])->asArray()->one();

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        unset(
            $user['password_hash'],
            $user['auth_key'],
            $user['password_reset_token'],
            $user['verification_token']
        );

        return [
            'success' => true,
            'data' => $user,
        ];
    }

    /**
     * POST /v1/users (Signup / Admin create)
     */
    public function actionCreate()
    {
        $model = new User();
        $model->scenario = 'create';
        $model->load(Yii::$app->request->bodyParams, '');

        if (!empty($model->password)) {
            $model->setPassword($model->password);
        }

        $model->generateAuthKey();

        if ($model->save()) {
            Yii::$app->response->statusCode = 201;

            $data = $model->toArray();
            unset(
                $data['password_hash'],
                $data['auth_key'],
                $data['password_reset_token'],
                $data['verification_token']
            );

            return [
                'success' => true,
                'message' => 'User created successfully',
                'data' => $data,
            ];
        }

        Yii::$app->response->statusCode = 422;
        return [
            'success' => false,
            'errors' => $model->errors,
        ];
    }

    /**
     * PUT/PATCH /v1/users/{id}
     */
    public function actionUpdate($id)
    {
        $model = User::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('User not found');
        }

        $model->load(Yii::$app->request->bodyParams, '');

        if (!empty($model->password)) {
            $model->setPassword($model->password);
        }

        if ($model->save()) {
            $data = $model->toArray();
            unset(
                $data['password_hash'],
                $data['auth_key'],
                $data['password_reset_token'],
                $data['verification_token']
            );

            return [
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $data,
            ];
        }

        Yii::$app->response->statusCode = 422;
        return [
            'success' => false,
            'errors' => $model->errors,
        ];
    }

    /**
     * DELETE /v1/users/{id}
     */
    public function actionDelete($id)
    {
        $model = User::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('User not found');
        }

        $model->status = User::STATUS_DELETED;
        $model->save(false);

        return [
            'success' => true,
            'message' => 'User deleted successfully',
        ];
    }

    /**
     * PATCH /v1/users/activate-2fa
     */
    public function actionActivate2fa()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;

        if (!$user) {
            Yii::$app->response->statusCode = 401;
            return [
                'success' => false,
                'message' => 'Unauthorized',
            ];
        }

        if ($user->enable2fa()) {
            return [
                'success' => true,
                'message' => '2FA activated successfully',
            ];
        }

        Yii::$app->response->statusCode = 500;
        return [
            'success' => false,
            'message' => 'Unable to activate 2FA',
        ];
    }

    public function actionDeactivate2fa()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;

        if (!$user) {
            Yii::$app->response->statusCode = 401;
            return [
                'success' => false,
                'message' => 'Unauthorized',
            ];
        }

        if (!$user->twofa_enabled) {
            return [
                'success' => true,
                'message' => '2FA already disabled',
            ];
        }

        $user->disable2fa();

        return [
            'success' => true,
            'message' => '2FA deactivated successfully',
        ];
    }
}
