<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpBearerAuth;
use common\models\Client;
use backend\models\ClientSearch;

class ClientController extends Controller
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
                    'index'  => ['GET'],
                    'view'   => ['GET'],
                    'create' => ['POST'],
                    'update' => ['PUT'],
                    'delete' => ['DELETE'],
                    'status' => ['PATCH'],
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
     * GET /v1/clients?page=1&per_page=10
     */
    public function actionIndex()
    {
        $searchModel = new ClientSearch();

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
     * GET /v1/clients/{id}
     */
    public function actionView($id)
    {
        $client = Client::find()
            ->where(['id' => $id])
            ->asArray()
            ->one();

        if (!$client) {
            throw new NotFoundHttpException('Client not found');
        }

        return [
            'success' => true,
            'data' => $client,
        ];
    }

    /**
     * POST /v1/clients
     */
    public function actionCreate()
    {
        $model = new Client();
        $model->load(Yii::$app->request->bodyParams, '');

        if ($model->save()) {
            Yii::$app->response->statusCode = 201;
            return [
                'success' => true,
                'message' => 'Client created successfully',
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
     * PUT /v1/clients/{id}
     */
    public function actionUpdate($id)
    {
        $model = Client::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('Client not found');
        }

        $model->load(Yii::$app->request->bodyParams, '');

        if ($model->save()) {
            return [
                'success' => true,
                'message' => 'Client updated successfully',
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
     * DELETE /v1/clients/{id}
     */
    public function actionDelete($id)
    {
        $model = Client::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('Client not found');
        }

        $model->delete();

        return [
            'success' => true,
            'message' => 'Client deleted successfully',
        ];
    }

    /**
     * PATCH /v1/clients/{id}/status
     */
    public function actionStatus($id)
    {
        $status = Yii::$app->request->bodyParams['status'] ?? null;

        $allowedStatuses = ['active', 'inactive', 'blocked'];

        if (!$status || !in_array($status, $allowedStatuses, true)) {
            Yii::$app->response->statusCode = 422;
            return [
                'success' => false,
                'message' => 'Invalid status value',
            ];
        }

        $model = Client::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('Client not found');
        }

        $model->status = $status;
        $model->save(false);

        return [
            'success' => true,
            'message' => 'Client status updated successfully',
        ];
    }
}
