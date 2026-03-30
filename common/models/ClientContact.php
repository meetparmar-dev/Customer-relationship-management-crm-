<?php

namespace common\models;

use yii\db\ActiveRecord;

class ClientContact extends ActiveRecord
{

    const EVENT_CREATED = 'created';
    const EVENT_UPDATED = 'updated';
    const EVENT_DELETED = 'deleted';


    public static function tableName()
    {
        return '{{%client_contacts}}';
    }

    public function rules()
    {
        return [
            [['client_id', 'name'], 'required'],
            [['client_id'], 'integer'],
            [['name', 'designation'], 'string', 'max' => 150],
            [['email'], 'email'],
            [['phone'], 'string', 'max' => 20],
            [['is_primary'], 'boolean'],
            [['is_primary'], 'default', 'value' => 0],
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->hasAttribute('updated_at')) {
            $this->updated_at = date('Y-m-d H:i:s');
        }

        return true;
    }

    public function init()
    {
        parent::init();

        $this->on(self::EVENT_CREATED, ['common\listeners\ClientContactListener', 'onCreate']);
        $this->on(self::EVENT_UPDATED, ['common\listeners\ClientContactListener', 'onUpdate']);
        $this->on(self::EVENT_DELETED, ['common\listeners\ClientContactListener', 'onDelete']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            $this->trigger(self::EVENT_CREATED);
        } else {
            $this->trigger(self::EVENT_UPDATED);
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();
        $this->trigger(self::EVENT_DELETED);
    }



    public function getClient()
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
    }
}
