<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpBearerAuth;
use common\models\ClientAddress;
use backend\models\ClientAddressSearch;

class ClientAddressController extends Controller
{
    public function behaviors()
    {
        return [
            // 🔐 JWT Authentication
            'authenticator' => [
                'class' => HttpBearerAuth::class,
            ],

            // 🔒 HTTP Verb rules
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index'  => ['GET'],
                    'view'   => ['GET'],
                    'create' => ['POST'],
                    'update' => ['PUT'],
                    'delete' => ['DELETE'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    /**
     * GET /v1/client-addresses?client_id=1
     */
    public function actionIndex()
    {
        $searchModel = new ClientAddressSearch();

        $params = Yii::$app->request->get();

        // 🔐 FORCE client_id from JWT
        $params['client_id'] = Yii::$app->user->id;

        $result = $searchModel->searchApi($params);

        return [
            'success' => true,
            'data' => $result['data'],
            'pagination' => $result['pagination'],
        ];
    }


    /**
     * GET /v1/client-addresses/{id}
     */
    public function actionView($id)
    {
        $model = ClientAddress::find()
            ->where(['id' => $id])
            ->asArray()
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('Address not found');
        }

        return [
            'success' => true,
            'data' => $model,
        ];
    }

    /**
     * POST /v1/client-addresses
     */
    public function actionCreate()
    {
        $model = new ClientAddress();
        $model->load(Yii::$app->request->bodyParams, '');

        if ($model->save()) {
            Yii::$app->response->statusCode = 201;
            return [
                'success' => true,
                'message' => 'Address created successfully',
                'data' => $model,
            ];
        }

        Yii::$app->response->statusCode = 422;
        return [
            'success' => false,
            'errors' => $model->errors,
        ];
    }

    /**
     * PUT /v1/client-addresses/{id}
     */
    public function actionUpdate($id)
    {
        $model = ClientAddress::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('Address not found');
        }

        $model->load(Yii::$app->request->bodyParams, '');

        if ($model->save()) {
            return [
                'success' => true,
                'message' => 'Address updated successfully',
                'data' => $model,
            ];
        }

        Yii::$app->response->statusCode = 422;
        return [
            'success' => false,
            'errors' => $model->errors,
        ];
    }

    /**
     * DELETE /v1/client-addresses/{id}
     */
    public function actionDelete($id)
    {
        $model = ClientAddress::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('Address not found');
        }

        $model->delete();

        return [
            'success' => true,
            'message' => 'Address deleted successfully',
        ];
    }
}
