<?php

namespace backend\controllers;

use Yii;
use common\models\ClientContact;
use backend\models\ClientContactSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * ClientContactController
 *
 * Handles CRUD operations for ClientContact model.
 */
class ClientContactController extends Controller
{
    /**
     * Controller behaviors
     * - Login required
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
                        'roles' => ['@'], // only authenticated users
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
     * Lists all ClientContact records.
     */
    public function actionIndex()
    {
        $searchModel  = new ClientContactSearch();                  // search model
        $dataProvider = $searchModel->search($this->request->queryParams); // filtered data

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ClientContact.
     *
     * @param int $id Contact ID
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id), // load contact
        ]);
    }

    /**
     * Creates a new ClientContact.
     *
     * @param int|null $client_id Client ID (optional)
     */
    public function actionCreate($client_id = null)
    {
        $model = new ClientContact();

        // Pre-fill client_id if provided
        if ($client_id) {
            $model->client_id = $client_id;
        }

        if ($model->load(Yii::$app->request->post())) {

            if ($model->save()) {

                Yii::$app->session->setFlash(
                    'success',
                    'Contact added successfully.'
                );

                // Redirect back to client view
                return $this->redirect([
                    'client/view',
                    'id' => $model->client_id,
                ]);
            }

            Yii::$app->session->setFlash(
                'error',
                'Failed to add contact. Please check the form.'
            );
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ClientContact.
     *
     * @param int $id Contact ID
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id); // fetch contact

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            Yii::$app->session->setFlash(
                'success',
                'Client updated successfully.'
            );

            // Redirect to same contact view
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
     * Deletes an existing ClientContact.
     *
     * @param int $id Contact ID
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id); // fetch contact

        // Save client_id before delete
        $clientId = $model->client_id;

        if ($model->delete()) {

            Yii::$app->session->setFlash(
                'success',
                'Record deleted successfully.'
            );
        } else {

            Yii::$app->session->setFlash(
                'error',
                'Unable to delete the record.'
            );
        }

        // Redirect back to client view
        return $this->redirect([
            '/client/view',
            'id' => $clientId,
        ]);
    }

    /**
     * Finds ClientContact by primary key.
     *
     * @param int $id Contact ID
     * @return ClientContact
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = ClientContact::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(
            'The requested page does not exist.'
        );
    }
}
