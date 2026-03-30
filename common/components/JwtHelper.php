<?php

namespace common\components;

use Yii;
use Lcobucci\JWT\Token;

class JwtHelper
{
    public static function generateToken($user)
    {
        $jwt = Yii::$app->jwt;

        return $jwt->getBuilder()
            ->setIssuer('crm-api')
            ->setAudience('crm-client')
            ->setId((string)$user->id, true)
            ->setIssuedAt(time())
            ->setExpiration(time() + 3600)
            ->set('uid', $user->id)
            ->set('role', $user->role)
            ->sign($jwt->getSigner('HS256'), $jwt->getKey())
            ->getToken();
    }

    public static function validate(Token $token)
    {
        return $token->verify(
            Yii::$app->jwt->getSigner('HS256'),
            Yii::$app->jwt->getKey()
        ) && !$token->isExpired();
    }
}
