<?php

namespace common\models;

use yii\db\ActiveRecord;

class ClientAddress extends ActiveRecord
{

    const EVENT_ADDRESS_CREATED = 'addressCreated';
    const EVENT_ADDRESS_UPDATED = 'addressUpdated';
    const EVENT_ADDRESS_DELETED = 'addressDeleted';

    const TYPE_BILLING  = 'billing';
    const TYPE_SHIPPING = 'shipping';
    const TYPE_OFFICE   = 'office';


    public static function tableName()
    {
        return '{{%client_addresses}}';
    }

    public function rules()
    {
        return [
            [['client_id', 'address_type', 'address', 'city', 'state', 'pincode'], 'required'],

            [['client_id'], 'integer'],
            [['address'], 'string'],

            [['city', 'state'], 'string', 'max' => 100],
            [['pincode'], 'string', 'max' => 10],

            [
                'address_type',
                'in',
                'range' => [
                    self::TYPE_BILLING,
                    self::TYPE_SHIPPING,
                    self::TYPE_OFFICE,
                ]
            ],

            [
                ['address_type'],
                'unique',
                'targetAttribute' => ['client_id', 'address_type'],
                'message' => 'This address type already exists for this client.'
            ],

        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        // only handle updated_at from PHP
        $this->updated_at = date('Y-m-d H:i:s');

        return true;
    }

    public function init()
    {
        parent::init();

        $this->on(self::EVENT_ADDRESS_CREATED, ['common\listeners\ClientAddressListener', 'onCreate']);
        $this->on(self::EVENT_ADDRESS_UPDATED, ['common\listeners\ClientAddressListener', 'onUpdate']);
        $this->on(self::EVENT_ADDRESS_DELETED, ['common\listeners\ClientAddressListener', 'onDelete']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            $this->trigger(self::EVENT_ADDRESS_CREATED);
        } else {
            $this->trigger(self::EVENT_ADDRESS_UPDATED);
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();
        $this->trigger(self::EVENT_ADDRESS_DELETED);
    }



    public function getClient()
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
    }

    public function getOfficeAddress()
    {
        return $this->hasOne(ClientContact::class, ['client_id' => 'id'])
            ->andWhere(['type' => 'office']);
    }
}
