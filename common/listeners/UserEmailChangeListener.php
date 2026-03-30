<?php

namespace common\listeners;

use Yii;
use yii\helpers\Url;
use common\models\User;

class UserEmailChangeListener
{
    public static function sendVerification($event)
    {
        /** @var User $user */
        $user = $event->sender;

        $link = Url::to([
            '/site/verify-new-email',
            'token' => $user->verification_token
        ], true);

        Yii::$app->mailer
            ->compose('verify-new-email', ['user' => $user, 'link' => $link])
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
            ->setTo($user->pending_email)
            ->setSubject('Verify your new email')
            ->send();
    }
}
