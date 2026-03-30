<?php

namespace common\listeners;

use Yii;
use yii\base\Event;
use common\models\User;

class PasswordChangedListener
{
    public static function handle(\yii\base\Event $event)
    {
        /** @var \common\models\User $user */
        $user = $event->sender;

        Yii::$app->mailer->compose(
            'passwordChanged',
            ['user' => $user]
        )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
            ->setTo($user->email)
            ->setSubject('Your password was changed')
            ->send();
    }
}
