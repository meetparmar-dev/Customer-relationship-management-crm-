<?php

namespace backend\controllers;

use common\models\LoginForm;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use common\models\Client;
use common\models\Project;
use common\models\Task;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use backend\models\PasswordResetRequestForm;
use backend\models\ResetPasswordForm;
use backend\models\SignupForm;
use backend\models\VerifyEmailForm;
use backend\models\ResendVerificationEmailForm;
use yii\web\ErrorAction;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [

                    // Error page MUST be public
                    [
                        'actions' => ['error'],
                        'allow' => true,
                    ],
                    // Public auth pages
                    [
                        'actions' => [
                            'login',
                            'signup',
                            'verify-email',
                            'request-password-reset',
                            'verify-otp',
                            'resend-otp',
                        ],
                        'allow' => true,
                    ],
                    // Logout – any logged in user
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    //Admin-only (everything else)
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return Yii::$app->user->identity->role == 1;
                        },
                    ],
                ],
            ],

            'verbs' => [
                'class' => \yii\filters\VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        // ================= BASIC COUNTS (INITIAL LOAD) =================
        $clientCount    = Client::find()->count();
        $projectCount   = Project::find()->count();
        $totalTaskCount = Task::find()->count();

        // ================= TASK STATUS COUNTS (SINGLE QUERY) =================
        $taskStatusRows = Task::find()
            ->select(['status', 'COUNT(*) AS total'])
            ->groupBy('status')
            ->indexBy('status')
            ->asArray()
            ->all();

        $pendingTaskCount   = $taskStatusRows['pending']['total'] ?? 0;
        $inProgressTaskCount = $taskStatusRows['in_progress']['total'] ?? 0;
        $completedTotalTaskCount = $taskStatusRows['completed']['total'] ?? 0;

        // Chart data
        $taskData = [
            'pending'     => $pendingTaskCount,
            'in_progress' => $inProgressTaskCount,
            'completed'   => $completedTotalTaskCount,
        ];

        // ================= COMPLETED TASKS (LAST 6 MONTHS CARD) =================
        $fromDate = date('Y-m-d H:i:s', strtotime('-6 months'));

        $completedTaskCount = Task::find()
            ->where(['status' => 'completed'])
            ->andWhere(['IS NOT', 'completed_at', null])
            ->andWhere(['>=', 'completed_at', $fromDate])
            ->count();

        // ================= PROJECT GROWTH (INITIAL 6 MONTHS CHART) =================
        $projectLabels = [];
        $projectData   = [];

        for ($i = 5; $i >= 0; $i--) {
            $projectLabels[] = date('M Y', strtotime("-$i months"));

            $projectData[] = Project::find()
                ->where([
                    'between',
                    'created_at',
                    strtotime("first day of -$i months"),
                    strtotime("last day of -$i months"),
                ])
                ->count();
        }

        // ================= RECENT CLIENTS (INDEX ONLY) =================
        $recentClients = Client::find()
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(5)
            ->all();

        // ================= UPCOMING TASKS (INDEX ONLY) =================
        $upcomingTasks = Task::find()
            ->where(['status' => ['pending', 'in_progress']])
            ->orderBy(['due_date' => SORT_ASC])
            ->limit(3)
            ->all();

        return $this->render('index', compact(
            'clientCount',
            'projectCount',
            'totalTaskCount',
            'pendingTaskCount',
            'completedTaskCount',
            'projectLabels',
            'projectData',
            'taskData',
            'recentClients',
            'upcomingTasks'
        ));
    }


    public function actionGetDashboardStats($period)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $months = match ($period) {
            'Last 1 Month'  => 1,
            'Last 3 Months' => 3,
            'Last 6 Months' => 6,
            'Last 12 Months' => 12,
            default => 6,
        };

        $fromTs   = strtotime("-{$months} months");
        $fromDate = date('Y-m-d H:i:s', $fromTs);

        // ================= CLIENTS =================
        $clientCount = Client::find()
            ->where(['>=', 'created_at', $fromTs])
            ->count();

        // ================= PROJECTS =================
        $projectCount = Project::find()
            ->where(['>=', 'created_at', $fromTs])
            ->count();

        // ================= TASKS =================
        $totalTaskCount = Task::find()
            ->where(['>=', 'created_at', $fromTs])
            ->count();

        // ================= TASK STATUS (PERIOD-WISE) =================
        $taskStatusRows = Task::find()
            ->select(['status', 'COUNT(*) AS total'])
            ->where(['>=', 'created_at', $fromTs])
            ->groupBy('status')
            ->indexBy('status')
            ->asArray()
            ->all();

        $pendingTaskCount   = $taskStatusRows['pending']['total'] ?? 0;
        $inProgressCount    = $taskStatusRows['in_progress']['total'] ?? 0;

        // ================= COMPLETED TASKS (USE completed_at) =================
        $completedTaskCount = Task::find()
            ->where(['status' => 'completed'])
            ->andWhere(['IS NOT', 'completed_at', null])
            ->andWhere(['>=', 'completed_at', $fromDate])
            ->count();

        // ================= PROJECT GROWTH =================
        $labels = [];
        $projectData = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $labels[] = date('M Y', strtotime("-$i months"));

            $projectData[] = Project::find()
                ->where([
                    'between',
                    'created_at',
                    strtotime("first day of -$i months"),
                    strtotime("last day of -$i months"),
                ])
                ->count();
        }

        return [
            'cards' => [
                'clients'   => (int) $clientCount,
                'projects'  => (int) $projectCount,
                'tasks'     => (int) $totalTaskCount,
                'pending'   => (int) $pendingTaskCount,
                'completed' => (int) $completedTaskCount,
            ],
            'projects' => [
                'labels' => $labels,
                'data'   => $projectData,
            ],
            'tasks' => [
                'pending'     => (int) $pendingTaskCount,
                'in_progress' => (int) $inProgressCount,
                'completed'   => (int) $completedTaskCount,
            ],
        ];
    }



    /**
     * Login action.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        $this->layout = 'blank';

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post())) {

            // Attempt login
            if ($model->login()) {

                $user = Yii::$app->user->identity;

                // Admin only
                if (!YII_ENV_TEST && $user->role != 1) {
                    Yii::$app->user->logout(false);
                    Yii::$app->session->setFlash('error', 'Access denied.');
                    return $this->redirect(['login']);
                }

                return $this->goHome();
            }

            // If login failed BUT 2FA started go to OTP page
            if (Yii::$app->session->has('2fa_user')) {
                return $this->redirect(['site/verify-otp']);
            }
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionVerifyNewEmail($token)
    {
        $user = \common\models\User::find()
            ->where(['verification_token' => $token])
            ->andWhere(['IS NOT', 'pending_email', null])
            ->one();

        if (!$user) {
            throw new \yii\web\NotFoundHttpException('Invalid or expired link.');
        }

        $user->confirmEmailChange();

        Yii::$app->session->setFlash('success', 'Your new email has been verified successfully.');
        return $this->redirect(['user/profile']);
    }

    public function actionVerifyOtp()
    {
        $this->layout = 'blank';

        $userId = Yii::$app->session->get('2fa_user');
        if (!$userId) {
            return $this->redirect(['login']);
        }

        $user = \common\models\User::findOne($userId);
        if (!$user) {
            Yii::$app->session->remove('2fa_user');
            return $this->redirect(['login']);
        }

        $model = new \backend\models\TwoFactorForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if ($user->validateTwoFactorCode($model->otp)) {

                // 🔐 Login user (controller responsibility)
                Yii::$app->user->login($user, 3600 * 24 * 30);
                Yii::$app->session->regenerateID(true);

                // 🔥 Model handles login side-effects
                $user->onSuccessfulLogin();

                // Cleanup session
                Yii::$app->session->remove('2fa_user');

                return $this->redirect(['site/index']);
            }

            Yii::$app->session->setFlash('error', 'Invalid or expired OTP');
        }

        return $this->render('verify-otp', [
            'model' => $model
        ]);
    }

    /**
     * Get completed task count based on selected time period.
     *
     * @return Response
     */

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        if (!Yii::$app->user->isGuest) {

            /** @var \common\models\User $user */
            $user = Yii::$app->user->identity;

            // Remove active session (controller responsibility)
            Yii::$app->db->createCommand()
                ->delete('{{%user_session}}', [
                    'session_id' => Yii::$app->session->id
                ])->execute();

            // 🔐 Model handles logout side-effects
            $user->onSuccessfulLogout();
        }

        Yii::$app->user->logout();
        return $this->goHome();
    }


    public function actionResendOtp()
    {
        if (!Yii::$app->session->has('2fa_user')) {
            return $this->redirect(['login']);
        }

        $userId = Yii::$app->session->get('2fa_user');
        $user = \common\models\User::findOne($userId);

        if (!$user) {
            return $this->redirect(['login']);
        }

        // Generate new OTP
        $user->generateTwoFactorCode();

        // Send email via model
        if ($user->sendTwoFactorOtp()) {
            Yii::$app->session->setFlash('success', 'New OTP sent to your email.');
        } else {
            Yii::$app->session->setFlash('error', 'Unable to send OTP email.');
        }

        return $this->redirect(['verify-otp']);
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $this->layout = 'blank';
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            return $this->redirect(['site/login']);
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $this->layout = 'blank';
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        $this->layout = 'blank';
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        $this->layout = 'blank';

        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        //Email verification success
        if ($user = $model->verifyEmail()) {

            Yii::$app->mailer
                ->compose(
                    'welcome-html',
                    ['user' => $user]
                )
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                ->setTo($user->email)
                ->setSubject('Welcome to ' . Yii::$app->name)
                ->send();

            Yii::$app->session->setFlash(
                'success',
                'Your email has been confirmed. Welcome to our platform!'
            );

            return $this->redirect(['site/login']);
        }

        Yii::$app->session->setFlash(
            'error',
            'Verification link is invalid or expired.'
        );

        return $this->goHome();
    }


    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $this->layout = 'blank';
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }
}
