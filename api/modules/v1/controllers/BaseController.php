<?php

namespace api\modules\v1\controllers;

use yii\rest\Controller;
use yii\web\Response;
use yii\filters\RateLimiter;
use yii\filters\auth\HttpBearerAuth;

class BaseController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // JSON response
        $behaviors['contentNegotiator']['formats']['application/json']
            = Response::FORMAT_JSON;

        // Global Rate Limiter
        $behaviors['rateLimiter'] = [
            'class' => RateLimiter::class,
            'enableRateLimitHeaders' => true,
        ];

        // JWT Auth (login open)
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['login'],
        ];

        return $behaviors;
    }
}
