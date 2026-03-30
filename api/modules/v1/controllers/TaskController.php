<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpBearerAuth;
use yii\data\Pagination;
use common\models\Task;
use backend\models\TaskSearch;

class TaskController extends Controller
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
     * GET /v1/tasks?page=1&per_page=10
     */
    public function actionIndex()
    {
        $searchModel = new TaskSearch();

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
     * GET /v1/tasks/{id}
     */
    public function actionView($id)
    {
        $task = Task::find()
            ->where(['id' => $id])
            ->asArray()
            ->one();

        if (!$task) {
            throw new NotFoundHttpException('Task not found');
        }

        return [
            'success' => true,
            'data' => $task,
        ];
    }

    /**
     * POST /v1/tasks
     */
    public function actionCreate()
    {
        $model = new Task();
        $model->load(Yii::$app->request->bodyParams, '');

        if ($model->save()) {
            Yii::$app->response->statusCode = 201;
            return [
                'success' => true,
                'message' => 'Task created successfully',
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
     * PUT /v1/tasks/{id}
     */
    public function actionUpdate($id)
    {
        $model = Task::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('Task not found');
        }

        $model->load(Yii::$app->request->bodyParams, '');

        if ($model->save()) {
            return [
                'success' => true,
                'message' => 'Task updated successfully',
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
     * DELETE /v1/tasks/{id}
     */
    public function actionDelete($id)
    {
        $model = Task::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('Task not found');
        }

        $model->delete();

        return [
            'success' => true,
            'message' => 'Task deleted successfully',
        ];
    }

    /**
     * PATCH /v1/tasks/{id}/status
     */
    public function actionStatus($id)
    {
        $status = Yii::$app->request->bodyParams['status'] ?? null;

        if (!$status) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'message' => 'Status is required',
            ];
        }

        $model = Task::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('Task not found');
        }

        $model->status = $status;
        $model->save(false);

        return [
            'success' => true,
            'message' => 'Task status updated successfully',
        ];
    }
}
