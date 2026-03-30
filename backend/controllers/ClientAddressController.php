<?php

namespace backend\controllers;

use Yii;
use common\models\ClientAddress;
use backend\models\ClientAddressSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * ClientAddressController
 *
 * Handles CRUD operations for ClientAddress model.
 */
class ClientAddressController extends Controller
{
    /**
     * Controller behaviors
     * - Access control (login required)
     * - Delete only via POST
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
                        'roles' => ['@'], // only authenticated users
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
     * Lists all ClientAddress records with search + filters.
     */
    public function actionIndex()
    {
        $searchModel  = new ClientAddressSearch();                  // search model
        $dataProvider = $searchModel->search($this->request->queryParams); // data provider

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ClientAddress.
     *
     * @param int $id Address ID
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id), // load address
        ]);
    }

    /**
     * Creates a new ClientAddress.
     * - Client ID is required
     * - Auto-selects next available address type
     */
    public function actionCreate($client_id = null, $type = null)
    {
        /* ================= CLIENT ID CHECK ================= */
        if ($client_id === null) {
            Yii::$app->session->setFlash(
                'error',
                'Client ID is required to add an address.'
            );

            return $this->redirect(['client/index']);
        }

        /* ================= ALLOWED TYPES ================= */
        $allTypes = ['billing', 'office', 'shipping'];

        /* ================= EXISTING TYPES ================= */
        $existingTypes = ClientAddress::find()
            ->select('address_type')
            ->where(['client_id' => $client_id])
            ->column();

        /* ================= REMAINING TYPES ================= */
        $remainingTypes = array_values(array_diff($allTypes, $existingTypes));

        if (empty($remainingTypes)) {
            Yii::$app->session->setFlash(
                'info',
                'All address types are already added.'
            );

            return $this->redirect([
                'client/view',
                'id' => $client_id,
            ]);
        }

        /* ================= CREATE MODEL ================= */
        $model = new ClientAddress();
        $model->client_id   = $client_id;         // assign client
        $model->address_type = $remainingTypes[0]; // auto-pick first free type

        /* ================= SAVE ================= */
        if ($model->load(Yii::$app->request->post())) {

            if ($model->save()) {

                Yii::$app->session->setFlash(
                    'success',
                    ucfirst($model->address_type) . ' address added successfully.'
                );

                return $this->redirect([
                    'client/view',
                    'id' => $client_id,
                ]);
            }

            Yii::$app->session->setFlash(
                'error',
                'Failed to add address. Please check the form.'
            );
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ClientAddress.
     *
     * @param int $id Address ID
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id); // fetch address

        if ($model->load(Yii::$app->request->post())) {

            if ($model->save()) {

                Yii::$app->session->setFlash(
                    'success',
                    'Record updated successfully.'
                );

                return $this->redirect([
                    'client/view',
                    'id' => $model->client_id,
                ]);
            }

            Yii::$app->session->setFlash(
                'error',
                'Failed to update record. Please check the form.'
            );
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ClientAddress.
     *
     * @param int $id Address ID
     */
    public function actionDelete($id)
    {
        $project = $this->findModel($id); // address record

        // Save client_id before delete for redirect
        $clientId = $project->client_id;

        try {

            if ($project->delete() !== false) {

                Yii::$app->session->setFlash(
                    'success',
                    'Project deleted successfully.'
                );
            } else {

                Yii::$app->session->setFlash(
                    'error',
                    'Failed to delete project.'
                );
            }
        } catch (\Throwable $e) {

            Yii::$app->session->setFlash(
                'error',
                'Something went wrong while deleting the project.'
            );
        }

        // Redirect back to client view
        return $this->redirect([
            '/client/view',
            'id' => $clientId,
        ]);
    }

    /**
     * Finds ClientAddress by primary key.
     *
     * @param int $id Address ID
     * @return ClientAddress
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = ClientAddress::findOne(['id' => $id])) !== null) {
            return $model; // return found record
        }

        throw new NotFoundHttpException(
            'The requested page does not exist.'
        );
    }
}
