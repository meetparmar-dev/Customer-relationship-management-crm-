<?php

namespace common\listeners;

use Yii;
use yii\base\Event;
use common\models\User;

class TwoFactorChangedListener
{
    public static function handle(Event $event)
    {
        /** @var User $user */
        $user = $event->sender;

        $status = $user->twofa_enabled ? 'enabled' : 'disabled';

        // 🔐 Security log
        Yii::info("2FA {$status} for user {$user->id}", 'security');

        // 📧 Email alert
        Yii::$app->mailer->compose(
            'twofaChanged',
            ['user' => $user, 'status' => $status]
        )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
            ->setTo($user->email)
            ->setSubject("Two-Factor Authentication {$status}")
            ->send();
    }
}
