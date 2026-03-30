<?php

namespace common\listeners;

use Yii;
use common\models\Client;

class ClientListener
{
    public static function onCreate($e)
    {
        self::log($e->sender, 'CLIENT_CREATED');
    }
    public static function onUpdate($e)
    {
        self::log($e->sender, 'CLIENT_UPDATED');
    }
    public static function onDelete($e)
    {
        self::log($e->sender, 'CLIENT_DELETED');
    }
    public static function onStatusChange($e)
    {
        self::log($e->sender, 'CLIENT_STATUS_CHANGED');
    }

    private static function log(Client $client, $action)
    {
        Yii::$app->db->createCommand()->insert('{{%audit_log}}', [
            'user_id' => Yii::$app->user->id ?? null,
            'action' => $action,
            'entity' => 'client',
            'entity_id' => $client->id,
            'ip' => Yii::$app->request->userIP,
            'user_agent' => Yii::$app->request->userAgent,
            'created_at' => time(),
        ])->execute();
    }
}
