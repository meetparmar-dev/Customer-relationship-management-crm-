<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpBearerAuth;
use common\models\LoginForm;
use common\models\User;

class AuthController extends Controller
{
    public function behaviors()
    {
        return [
            //JWT Auth (login ko open rakha)
            'authenticator' => [
                'class' => HttpBearerAuth::class,
                'except' => ['login'],
            ],

            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'login'  => ['POST'],
                    'logout' => ['POST'],
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
     * POST /v1/login
     * Public API
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        $model->load(Yii::$app->request->bodyParams, '');

        if (!$model->validate() || !$model->login()) {
            Yii::$app->response->statusCode = 401;
            return [
                'success' => false,
                'message' => 'Invalid email or password',
            ];
        }

        /** @var User $user */
        $user = Yii::$app->user->identity;

        //Login event (aapke listeners ke liye)
        $user->trigger(User::EVENT_USER_LOGIN);

        return [
            'success' => true,
            'message' => 'Login successful',
            'token'   => $user->generateJwt(), //JWT issued here
            'user' => [
                'id'    => $user->id,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ];
    }

    /**
     * POST /v1/logout
     * Protected API (JWT required)
     */
    public function actionLogout()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;

        if ($user) {
            //Invalidate current JWT
            $user->jwt_id = null;
            $user->save(false);

            //Fire logout event manually
            $user->trigger(User::EVENT_USER_LOGOUT);
        }

        return [
            'success' => true,
            'message' => 'Logged out successfully',
        ];
    }
}
