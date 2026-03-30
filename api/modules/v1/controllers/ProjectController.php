<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpBearerAuth;
use common\models\Project;
use backend\models\ProjectSearch;

class ProjectController extends Controller
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
     * GET /v1/projects?page=1&per_page=10
     */

    public function actionIndex()
    {
        $searchModel = new ProjectSearch();

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
     * GET /v1/projects/{id}
     */
    public function actionView($id)
    {
        $project = Project::find()
            ->where(['id' => $id])
            ->asArray()
            ->one();

        if (!$project) {
            throw new NotFoundHttpException('Project not found');
        }

        return [
            'success' => true,
            'data' => $project,
        ];
    }

    /**
     * POST /v1/projects
     */
    public function actionCreate()
    {
        $model = new Project();
        $model->load(Yii::$app->request->bodyParams, '');

        if ($model->save()) {
            Yii::$app->response->statusCode = 201;
            return [
                'success' => true,
                'message' => 'Project created successfully',
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
     * PUT /v1/projects/{id}
     */
    public function actionUpdate($id)
    {
        $model = Project::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('Project not found');
        }

        $model->load(Yii::$app->request->bodyParams, '');

        if ($model->save()) {
            return [
                'success' => true,
                'message' => 'Project updated successfully',
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
     * DELETE /v1/projects/{id}
     */
    public function actionDelete($id)
    {
        $model = Project::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('Project not found');
        }

        $model->delete();

        return [
            'success' => true,
            'message' => 'Project deleted successfully',
        ];
    }

    /**
     * PATCH /v1/projects/{id}/status
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

        $model = Project::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('Project not found');
        }

        $model->status = $status;
        $model->save(false);

        return [
            'success' => true,
            'message' => 'Project status updated successfully',
        ];
    }
}
