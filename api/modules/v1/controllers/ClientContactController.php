<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\ClientContact;
use yii\filters\auth\HttpBearerAuth;

class ClientContactController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'authenticator' => [
                'class' => HttpBearerAuth::class,
                'except' => [], // allow none publicly
            ],
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
        ]);
    }


    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    /**
     * GET /v1/client-contacts?client_id=5
     */
    public function actionIndex($client_id = null)
    {
        if (!$client_id) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'message' => 'Client ID is required',
            ];
        }

        $contacts = ClientContact::find()
            ->where(['client_id' => $client_id])
            ->asArray()
            ->all();

        return [
            'success' => true,
            'data' => $contacts,
        ];
    }

    /**
     * GET /v1/client-contacts/{id}
     */
    public function actionView($id)
    {
        $contact = ClientContact::find()
            ->where(['id' => $id])
            ->asArray()
            ->one();

        if (!$contact) {
            throw new NotFoundHttpException('Contact not found');
        }

        return [
            'success' => true,
            'data' => $contact,
        ];
    }

    /**
     * POST /v1/client-contacts
     */
    public function actionCreate()
    {
        $model = new ClientContact();
        $model->load(Yii::$app->request->bodyParams, '');

        if ($model->save()) {
            Yii::$app->response->statusCode = 201;
            return [
                'success' => true,
                'message' => 'Contact created successfully',
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
     * PUT /v1/client-contacts/{id}
     */
    public function actionUpdate($id)
    {
        $model = ClientContact::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('Contact not found');
        }

        $model->load(Yii::$app->request->bodyParams, '');

        if ($model->save()) {
            return [
                'success' => true,
                'message' => 'Contact updated successfully',
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
     * DELETE /v1/client-contacts/{id}
     */
    public function actionDelete($id)
    {
        $model = ClientContact::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('Contact not found');
        }

        $model->delete();

        return [
            'success' => true,
            'message' => 'Contact deleted successfully',
        ];
    }
}
