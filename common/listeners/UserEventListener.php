<?php

namespace common\listeners;

use common\models\User;
use Yii;
use common\jobs\SendEmailJob;

class UserEventListener
{
    public static function sendWelcomeEmail($event)
    {
        /** @var User $user */
        $user = $event->sender;

        // Safety checks
        if (empty($user->email) || $user->status !== User::STATUS_ACTIVE) {
            return;
        }

        Yii::$app->queue->push(new SendEmailJob([
            'from' => [Yii::$app->params['supportEmail'] => Yii::$app->name],
            'to' => $user->email,
            'subject' => 'Welcome to ' . Yii::$app->name,
            'htmlBody' => 'welcome-html',
            'textBody' => 'welcome-text',
            'userId' => $user->id,
        ]));
    }

    /**
     * 🔥 Fired when user is created (admin or signup)
     */
    public static function onUserCreated($event)
    {
        /** @var User $user */
        $user = $event->sender;

        // -----------------------------
        // 1) Create default user settings
        // -----------------------------
        try {
            Yii::$app->db->createCommand()->insert('{{%user_settings}}', [
                'user_id'    => $user->id,
                'theme'      => 'light',
                'language'   => 'en',
                'created_at' => time(),
            ])->execute();
        } catch (\Throwable $e) {
            Yii::error(
                'User settings insert failed for user ' . $user->id . ': ' . $e->getMessage(),
                'db'
            );
        }

        // -----------------------------
        // 2) Security audit log
        // -----------------------------
        try {
            Yii::$app->db->createCommand()->insert('{{%audit_log}}', [
                'user_id'    => $user->id,
                'action'     => 'USER_CREATED',
                'ip'         => Yii::$app->request->userIP ?? 'CLI',
                'user_agent' => Yii::$app->request->userAgent ?? 'CLI',
                'created_at' => time(),
            ])->execute();
        } catch (\Throwable $e) {
            Yii::error(
                'Audit log failed for user ' . $user->id . ': ' . $e->getMessage(),
                'security'
            );
        }

        // -----------------------------
        // 3) Log to Yii
        // -----------------------------
        Yii::info(
            'New user created: ID ' . $user->id . ' Email: ' . ($user->email ?? 'N/A'),
            'user'
        );
    }


    public static function onUserLogin($event)
    {
        /** @var User $user */
        $user = $event->sender;

        // -----------------------------
        // 1) Save session (safe)
        // -----------------------------
        try {
            Yii::$app->db->createCommand()->insert('{{%user_session}}', [
                'user_id'      => $user->id,
                'session_id'   => Yii::$app->session->id,
                'ip'           => Yii::$app->request->userIP ?? 'CLI',
                'user_agent'   => Yii::$app->request->userAgent ?? 'CLI',
                'created_at'   => time(),
                'last_activity' => time(),
            ])->execute();
        } catch (\Throwable $e) {
            Yii::error('User session save failed for user ' . $user->id . ': ' . $e->getMessage(), 'security');
        }

        // -----------------------------
        // 2) Audit login (safe)
        // -----------------------------
        try {
            Yii::$app->db->createCommand()->insert('{{%audit_log}}', [
                'user_id'    => $user->id,
                'action'     => 'LOGIN',
                'ip'         => Yii::$app->request->userIP ?? 'CLI',
                'user_agent' => Yii::$app->request->userAgent ?? 'CLI',
                'created_at' => time(),
            ])->execute();
        } catch (\Throwable $e) {
            Yii::error('Login audit failed for user ' . $user->id . ': ' . $e->getMessage(), 'security');
        }
    }

    public static function onUserLogout($event)
    {
        $user = $event->sender;

        Yii::$app->db->createCommand()->insert('{{%audit_log}}', [
            'user_id'    => $user->id,
            'action'     => 'LOGOUT',
            'ip'         => Yii::$app->request->userIP ?? 'CLI',
            'user_agent' => Yii::$app->request->userAgent ?? 'CLI',
            'created_at' => time(),
        ])->execute();
    }
}
