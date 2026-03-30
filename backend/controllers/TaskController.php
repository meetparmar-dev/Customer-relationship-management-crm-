<?php

namespace backend\controllers;

use Yii;
use common\models\Task;
use backend\models\TaskSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * TaskController
 *
 * Handles CRUD operations for Task model
 * and provides AJAX based status updates.
 */
class TaskController extends Controller
{
    /**
     * Controller behaviors
     * - Only authenticated users allowed
     * - Delete allowed only via POST
     */
    public function behaviors()
    {
        return [
            /* ================= ACCESS CONTROL ================= */
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // logged-in users
                    ],
                ],
            ],

            /* ================= VERB FILTER ================= */
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'], // delete only via POST
                ],
            ],
        ];
    }

    /**
     * Lists all Task models with search and filters.
     */
    public function actionIndex()
    {
        $searchModel  = new TaskSearch();                               // search model
        $dataProvider = $searchModel->search($this->request->queryParams); // data provider

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Task record.
     *
     * @param int $id Task ID
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id), // load task
        ]);
    }

    /**
     * Creates a new Task.
     */
    public function actionCreate()
    {
        $model = new Task(); // new task

        if ($this->request->isPost) {

            if ($model->load($this->request->post()) && $model->save()) {

                Yii::$app->session->setFlash(
                    'success',
                    'Task created successfully.'
                );

                return $this->redirect([
                    'view',
                    'id' => $model->id,
                ]);
            }
        } else {
            // load default values
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Task.
     *
     * @param int $id Task ID
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id); // existing task

        if (
            $this->request->isPost &&
            $model->load($this->request->post()) &&
            $model->save()
        ) {
            Yii::$app->session->setFlash(
                'success',
                'Record updated successfully.'
            );

            return $this->redirect([
                'view',
                'id' => $model->id,
            ]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Task.
     *
     * @param int $id Task ID
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        Yii::$app->session->setFlash(
            'success',
            'Project deleted successfully.'
        );

        return $this->redirect(['index']);
    }

    /**
     * Finds the Task model by primary key.
     *
     * @param int $id Task ID
     * @return Task
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Task::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(
            'The requested page does not exist.'
        );
    }

    /**
     * AJAX: Change task status.
     */
    public function actionChangeStatus()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $id     = Yii::$app->request->post('id');     // task id
        $status = Yii::$app->request->post('status'); // new status

        $model = Task::findOne($id);

        if (!$model) {
            return [
                'success' => false,
                'message' => 'Task not found'
            ];
        }

        // Update status
        $model->status = $status;

        if ($model->save(false)) {
            return [
                'success' => true,
                'message' => 'Task status updated successfully.'
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to update task status.'
        ];
    }
}
