<?php

namespace backend\controllers;

use Yii;
use common\models\Project;
use backend\models\ProjectSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * ProjectController
 *
 * Handles CRUD operations for Project model
 * and provides AJAX status updates.
 */
class ProjectController extends Controller
{
    /**
     * Controller behaviors
     * - Only logged-in users allowed
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
                        'roles' => ['@'], // authenticated users
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
     * Lists all Project models with search and filters.
     */
    public function actionIndex()
    {
        $searchModel  = new ProjectSearch();                              // search model
        $dataProvider = $searchModel->search($this->request->queryParams); // data provider

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Project record.
     *
     * @param int $id Project ID
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id), // load project
        ]);
    }

    /**
     * Creates a new Project.
     */
    public function actionCreate()
    {
        $model = new Project(); // new project

        if ($this->request->isPost) {

            if ($model->load($this->request->post()) && $model->save()) {

                Yii::$app->session->setFlash(
                    'success',
                    'Project created successfully.'
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
     * Updates an existing Project.
     *
     * @param int $id Project ID
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id); // existing project

        if (
            $this->request->isPost &&
            $model->load($this->request->post()) &&
            $model->save()
        ) {
            Yii::$app->session->setFlash(
                'success',
                'Project updated successfully.'
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
     * Deletes an existing Project.
     *
     * @param int $id Project ID
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        Yii::$app->session->setFlash(
            'success',
            'Client deleted successfully.'
        );

        return $this->redirect(['index']);
    }

    /**
     * Finds the Project model by primary key.
     *
     * @param int $id Project ID
     * @return Project
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Project::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(
            'The requested page does not exist.'
        );
    }

    /**
     * AJAX: Change project status.
     */
    public function actionChangeStatus()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $id     = Yii::$app->request->post('id');     // project id
        $status = Yii::$app->request->post('status'); // new status

        $model = Project::findOne($id);

        if (!$model) {
            return [
                'success' => false,
                'message' => 'Project not found'
            ];
        }

        // Update project status
        $model->status = $status;

        if ($model->save(false)) {
            return [
                'success' => true,
                'message' => 'Status updated successfully'
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to update status'
        ];
    }
}
