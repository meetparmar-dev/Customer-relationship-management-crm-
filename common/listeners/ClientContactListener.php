<?php

namespace common\listeners;

use Yii;
use common\models\ClientContact;

class ClientContactListener
{
    public static function onCreate($event)
    {
        self::log($event->sender, 'CONTACT_CREATED');
        self::mail($event->sender, 'created');
    }

    public static function onUpdate($event)
    {
        self::log($event->sender, 'CONTACT_UPDATED');
        self::mail($event->sender, 'updated');
    }

    public static function onDelete($event)
    {
        self::log($event->sender, 'CONTACT_DELETED');
        self::mail($event->sender, 'deleted');
    }

    private static function log(ClientContact $contact, string $action)
    {
        try {
            Yii::$app->db->createCommand()->insert('{{%audit_log}}', [
                'user_id'   => Yii::$app->user->id ?? null,
                'action'    => $action,
                'entity'    => 'client_contact',
                'entity_id' => $contact->id,
                'ip'        => Yii::$app->request->userIP ?? 'CLI',
                'user_agent' => Yii::$app->request->userAgent ?? 'CLI',
                'created_at' => time(),
            ])->execute();
        } catch (\Throwable $e) {
            Yii::error('ClientContact audit failed: ' . $e->getMessage(), 'security');
        }
    }

    private static function mail(ClientContact $contact, string $type)
    {
        try {
            Yii::$app->mailer
                ->compose('client-contact-' . $type, [
                    'contact' => $contact
                ])
                ->setFrom([Yii::$app->params['supportEmail'] => 'CRM System'])
                ->setTo(Yii::$app->params['adminEmail'])
                ->setSubject('Client contact ' . ucfirst($type))
                ->send();
        } catch (\Throwable $e) {
            Yii::error('ClientContact mail failed: ' . $e->getMessage(), 'mail');
        }
    }
}
