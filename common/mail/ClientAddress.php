<?php

namespace common\models;

use yii\db\ActiveRecord;

class ClientAddress extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%client_addresses}}';
    }

    // Address → Client
    public function getClient()
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
    }
}
