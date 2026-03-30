<?php

namespace api\modules\v1\filters;

use Yii;
use yii\filters\auth\AuthMethod;
use yii\web\UnauthorizedHttpException;
use Lcobucci\JWT\Parser;
use common\models\User;
use common\components\JwtHelper;

class JwtAuth extends AuthMethod
{
    public function authenticate($user, $request, $response)
    {
        $header = $request->getHeaders()->get('Authorization');

        if (!$header || !preg_match('/^Bearer\s+(.*?)$/', $header, $matches)) {
            throw new UnauthorizedHttpException('Missing Authorization header');
        }

        $token = (new Parser())->parse($matches[1]);

        if (!JwtHelper::validate($token)) {
            throw new UnauthorizedHttpException('Invalid or expired token');
        }

        $identity = User::findOne($token->getClaim('uid'));
        if (!$identity) {
            throw new UnauthorizedHttpException('User not found');
        }

        Yii::$app->user->setIdentity($identity);
        return $identity;
    }
}
