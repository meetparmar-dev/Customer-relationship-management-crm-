<?php

namespace common\listeners;

use Yii;
use common\models\ClientAddress;

class ClientAddressListener
{
    public static function onCreate($event)
    {
        self::handle($event->sender, 'ADDRESS_CREATED');
    }

    public static function onUpdate($event)
    {
        self::handle($event->sender, 'ADDRESS_UPDATED');
    }

    public static function onDelete($event)
    {
        self::handle($event->sender, 'ADDRESS_DELETED');
    }

    private static function handle(ClientAddress $address, string $action)
    {
        // ================== AUDIT LOG ==================
        try {
            Yii::$app->db->createCommand()->insert('{{%audit_log}}', [
                'user_id'    => Yii::$app->user->id ?? null,
                'action'     => $action,
                'entity'     => 'client_address',
                'entity_id'  => $address->id,
                'ip'         => Yii::$app->request->userIP ?? 'CLI',
                'user_agent' => Yii::$app->request->userAgent ?? 'CLI',
                'created_at' => time(),
            ])->execute();
        } catch (\Throwable $e) {
            Yii::error('ClientAddress audit failed: ' . $e->getMessage(), 'security');
        }

        // ================== EMAIL (MAILTRAP SAFE) ==================
        try {
            Yii::$app->mailer
                ->compose('client-address-change-html', [
                    'address' => $address,
                    'action'  => $action,
                    'user'    => Yii::$app->user->identity,
                ])
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->params['senderName']])
                ->setTo(Yii::$app->params['adminEmail'])   // goes to admin
                ->setSubject('Client Address ' . str_replace('_', ' ', $action))
                ->send();
        } catch (\Throwable $e) {
            Yii::error('ClientAddress mail failed: ' . $e->getMessage(), 'mail');
        }
    }
}
