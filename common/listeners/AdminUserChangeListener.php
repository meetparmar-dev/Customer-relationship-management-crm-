<?php

namespace common\listeners;

use Yii;
use common\models\User;

class AdminUserChangeListener
{
    // 🔐 Email changed by admin
    public static function onEmailChange($event)
    {
        /** @var User $user */
        $user = $event->sender;

        self::sendMail($user, 'admin-email-changed-html', 'Your email was changed by Admin');
        self::audit($user, 'ADMIN_EMAIL_CHANGED');
    }

    // 🔐 Role changed
    public static function onRoleChange($event)
    {
        $user = $event->sender;

        self::sendMail($user, 'admin-role-changed-html', 'Your account role was updated');
        self::audit($user, 'ADMIN_ROLE_CHANGED');
    }

    // 🔐 Status changed (blocked / activated)
    public static function onStatusChange($event)
    {
        $user = $event->sender;

        self::sendMail($user, 'admin-status-changed-html', 'Your account status was changed');

        // If blocked → force logout
        if ($user->status == User::STATUS_INACTIVE) {
            Yii::$app->db->createCommand()
                ->delete('{{%user_session}}', ['user_id' => $user->id])
                ->execute();
        }

        self::audit($user, 'ADMIN_STATUS_CHANGED');
    }

    // ===============================
    // Central mail sender (Mailtrap safe)
    // ===============================
    private static function sendMail(User $user, $view, $subject)
    {
        try {
            if (!empty($user->email) && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                Yii::$app->mailer
                    ->compose($view, ['user' => $user])
                    ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->params['senderName']])
                    ->setTo($user->email)
                    ->setSubject($subject)
                    ->send();
            }
        } catch (\Throwable $e) {
            Yii::error('Admin mail failed for user ' . $user->id . ': ' . $e->getMessage(), 'mail');
        }
    }

    // ===============================
    // Audit logger
    // ===============================
    private static function audit(User $user, $action)
    {
        Yii::$app->db->createCommand()->insert('{{%audit_log}}', [
            'user_id'    => $user->id,
            'action'     => $action,
            'ip'         => Yii::$app->request->userIP ?? 'CLI',
            'user_agent' => Yii::$app->request->userAgent ?? 'CLI',
            'created_at' => time(),
        ])->execute();
    }

    public static function onUserDelete($event)
    {
        $user = $event->sender;

        // Audit
        Yii::$app->db->createCommand()->insert('{{%audit_log}}', [
            'user_id' => $user->id,
            'action' => 'ADMIN_USER_DELETED',
            'ip' => Yii::$app->request->userIP,
            'user_agent' => Yii::$app->request->userAgent,
            'created_at' => time(),
        ])->execute();
    }
}
