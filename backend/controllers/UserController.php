<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use backend\models\UserSearch;
use backend\models\ProfileForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\ChangePasswordForm;

/**
 * UserController
 *
 * Handles CRUD operations for users,
 * profile management, avatar upload,
 * and role management.
 */
class UserController extends Controller
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
                        'roles' => ['@'], // logged-in users only
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
     * Lists all User records with search and filters.
     */
    public function actionIndex()
    {
        $searchModel  = new UserSearch();                               // search model
        $dataProvider = $searchModel->search($this->request->queryParams); // data provider

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User record.
     *
     * @param int $id User ID
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id), // load user
        ]);
    }

    /**
     * Creates a new User.
     */
    public function actionCreate()
    {
        $model = new User(['scenario' => 'create']);

        if ($model->load(Yii::$app->request->post())) {

            if (!empty($model->password)) {
                $model->setPassword($model->password);
                $model->generateAuthKey();
            }

            if ($model->save()) {
                Yii::$app->session->setFlash(
                    'success',
                    'User created successfully.'
                );

                return $this->redirect(['view', 'id' => $model->id]);
            }

            Yii::$app->session->setFlash(
                'error',
                'Failed to create user.'
            );
        }

        return $this->render('create', compact('model'));
    }


    /**
     * Updates an existing User.
     *
     * @param int $id User ID
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {

            Yii::$app->session->setFlash('success', 'User updated successfully.');

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', compact('model'));
    }



    /**
     * Deletes an existing User.
     *
     * @param int $id User ID
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        // Prevent self delete (controller responsibility)
        if ($model->id == Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'You cannot delete your own account.');
            return $this->redirect(['index']);
        }

        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'User deleted successfully.');
        }

        return $this->redirect(['index']);
    }



    /**
     * Finds the User model by primary key.
     *
     * @param int $id User ID
     * @return User
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(
            'The requested page does not exist.'
        );
    }

    /**
     * User profile update (self).
     */
    public function actionProfile()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;
        $tab = Yii::$app->request->get('tab', 'profile');

        $profileModel = new ProfileForm();
        $profileModel->scenario = 'profile';

        $passwordModel = new ChangePasswordForm();

        // Prefill form
        $profileModel->first_name = $user->first_name;
        $profileModel->last_name  = $user->last_name;
        $profileModel->username   = $user->username;
        $profileModel->email      = $user->email;

        if ($tab === 'profile' && $profileModel->load(Yii::$app->request->post()) && $profileModel->validate()) {

            // Update only normal profile fields
            $user->first_name = $profileModel->first_name;
            $user->last_name  = $profileModel->last_name;
            $user->username   = $profileModel->username;
            $user->save(false);

            // Handle email change (event-driven)
            if ($profileModel->email !== $user->email) {
                $user->requestEmailChange($profileModel->email);

                Yii::$app->session->setFlash(
                    'info',
                    'Verification email sent to your new address. Please verify to complete email change.'
                );
            } else {
                Yii::$app->session->setFlash('success', 'Profile updated.');
            }

            return $this->redirect(['profile', 'tab' => 'profile']);
        }

        return $this->render('profile', [
            'model' => $tab === 'profile' ? $profileModel : $passwordModel,
            'tab' => $tab
        ]);
    }

    /**
     * Upload and update user avatar.
     */
    public function actionUploadAvatar()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;

        // Get uploaded file
        $user->avatarFile = UploadedFile::getInstance($user, 'avatarFile');

        if ($user->avatarFile && $user->validate(['avatarFile'])) {

            $uploadDir = Yii::getAlias('@backend/web/uploads/avatars');

            // Create directory if missing
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            // Delete old avatar if exists
            if (!empty($user->avatar) && file_exists($uploadDir . '/' . $user->avatar)) {
                @unlink($uploadDir . '/' . $user->avatar);
            }

            // Generate new filename
            $fileName = time() . '.' . $user->avatarFile->extension;

            // Save file
            $user->avatarFile->saveAs($uploadDir . '/' . $fileName);

            // Update DB
            $user->avatar = $fileName;
            $user->save(false);

            Yii::$app->session->setFlash('success', 'Profile picture updated.');
        } else {
            Yii::$app->session->setFlash('error', 'Invalid image file.');
        }

        return $this->redirect(['profile']);
    }

    /**
     * Delete current user's avatar.
     */
    public function actionDeleteAvatar()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;

        if (!$user) {
            throw new NotFoundHttpException('User not found.');
        }

        if (!empty($user->avatar)) {

            // Same path as upload
            $uploadDir = Yii::getAlias('@backend/web/uploads/avatars');
            $filePath  = $uploadDir . '/' . $user->avatar;

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Remove avatar from DB
            $user->avatar = null;
            $user->save(false);
        }

        Yii::$app->session->setFlash(
            'success',
            'Profile photo deleted successfully.'
        );

        return $this->redirect(['profile']);
    }

    public function actionChangePassword()
    {
        $model = new \backend\models\ChangePasswordForm();
        $user = Yii::$app->user->identity;

        if ($model->load(Yii::$app->request->post()) && $model->changePassword($user)) {
            Yii::$app->session->setFlash('success', 'Password changed successfully.');
        } else {
            Yii::$app->session->setFlash('error', 'Current password is incorrect.');
        }

        return $this->redirect(['profile', 'tab' => 'reset']);
    }

    public function actionEnable2fa()
    {
        /** @var \common\models\User $user */
        $user = Yii::$app->user->identity;

        if ($user->enable2fa()) {
            Yii::$app->session->setFlash('success', 'Two Factor Authentication Enabled');
        }

        return $this->redirect(['user/profile', 'tab' => 'security']);
    }

    public function actionDisable2fa()
    {
        /** @var \common\models\User $user */
        $user = Yii::$app->user->identity;

        if ($user->disable2fa()) {
            Yii::$app->session->setFlash('success', 'Two Factor Authentication Disabled');
        }

        return $this->redirect(['user/profile', 'tab' => 'security']);
    }
}
