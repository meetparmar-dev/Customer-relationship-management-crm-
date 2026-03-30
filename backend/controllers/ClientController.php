<?php

namespace backend\controllers;

use Yii;
use common\models\Client;
use backend\models\ClientSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * ClientController
 *
 * Handles CRUD operations for Client model
 * and also provides AJAX status update.
 */
class ClientController extends Controller
{
    /**
     * Controller behaviors
     * - Only logged-in users can access
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
                        'roles' => ['@'], // authenticated users only
                    ],
                ],
            ],

            /* ================= VERB FILTER ================= */
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'], // delete via POST only
                ],
            ],
        ];
    }

    /**
     * Lists all Client models with search and filters.
     */
    public function actionIndex()
    {
        $searchModel  = new ClientSearch();                            // search model
        $dataProvider = $searchModel->search($this->request->queryParams); // data provider

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider'=> $dataProvider,
        ]);
    }

    /**
     * Displays a single Client record.
     *
     * @param int $id Client ID
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id), // load client
        ]);
    }

    /**
     * Creates a new Client.
     */
    public function actionCreate()
    {
        $model = new Client(); // new client instance

        if ($this->request->isPost) {

            if ($model->load($this->request->post()) && $model->save()) {

                Yii::$app->session->setFlash(
                    'success',
                    'Client created successfully.'
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
     * Updates an existing Client.
     *
     * @param int $id Client ID
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id); // existing client

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
     * Deletes a Client.
     *
     * @param int $id Client ID
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        Yii::$app->session->setFlash(
            'success',
            'Record deleted successfully.'
        );

        return $this->redirect(['index']);
    }

    /**
     * Finds Client model by primary key.
     *
     * @param int $id Client ID
     * @return Client
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Client::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(
            'The requested page does not exist.'
        );
    }

    /**
     * AJAX: Change client status.
     */
    public function actionChangeStatus()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $id     = Yii::$app->request->post('id');      // client id
        $status = Yii::$app->request->post('status');  // new status

        $model = Client::findOne($id);

        if (!$model) {
            return [
                'success' => false,
                'message' => 'Client not found'
            ];
        }

        // Update status
        $model->status = $status;

        if ($model->save(false)) {
            return [
                'success' => true,
                'message' => 'Client status updated successfully'
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to update client status'
        ];
    }
}
