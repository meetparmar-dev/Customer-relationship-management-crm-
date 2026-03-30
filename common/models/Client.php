<?php

namespace common\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Client extends ActiveRecord
{

    const EVENT_CREATED = 'created';
    const EVENT_UPDATED = 'updated';
    const EVENT_DELETED = 'deleted';
    const EVENT_STATUS_CHANGED = 'statusChanged';

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => time(),
            ],
        ];
    }

    const TYPE_INDIVIDUAL = 'individual';
    const TYPE_COMPANY = 'company';

    const STATUS_LEAD = 'lead';
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_ON_HOLD = 'on_hold';
    const STATUS_BLOCKED = 'blocked';


    public static function tableName()
    {
        return '{{%clients}}';
    }

    /* ================= VALIDATION RULES ================= */

    public function rules()
    {
        return [

            /* ================= REQUIRED ================= */
            [['type', 'first_name', 'email', 'phone'], 'required'],

            /* ================= DEFAULTS ================= */
            ['status', 'default', 'value' => self::STATUS_LEAD],

            /* ================= TRIM ================= */
            [['client_code', 'first_name', 'last_name', 'company_name', 'email', 'phone'], 'trim'],

            /* ================= STRING LENGTH ================= */
            [['client_code'], 'string', 'max' => 50],
            [['first_name', 'last_name', 'company_name'], 'string', 'max' => 100],
            [['phone'], 'string', 'max' => 20],

            /* ================= EMAIL ================= */
            ['email', 'email'],

            /* ================= UNIQUE ================= */
            ['client_code', 'unique'],

            /* ================= TYPE ================= */
            [['type'], 'string', 'max' => 30],
            ['type', 'default', 'value' => self::TYPE_INDIVIDUAL],


            /* ================= STATUS ================= */
            ['status', 'in', 'range' => array_keys(self::statusList())],

            /* ================= COMPANY NAME CONDITIONAL ================= */
            [
                'company_name',
                'required',
                'when' => function ($model) {
                    return $model->type === 'company';
                },
                'whenClient' => "function () {
                return $('#client-type').val() === 'company';
            }"
            ],
        ];
    }

    /* ================= ATTRIBUTE LABELS ================= */

    public function attributeLabels()
    {
        return [
            'client_code'  => 'Client Code',
            'type'         => 'Client Type',
            'company_name' => 'Company Name',
            'first_name'   => 'First Name',
            'last_name'    => 'Last Name',
            'email'        => 'Email Address',
            'phone'        => 'Phone Number',
            'status'       => 'Status',
        ];
    }

    public static function statusList()
    {
        return [
            self::STATUS_LEAD => 'Lead',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_ON_HOLD => 'On Hold',
            self::STATUS_BLOCKED => 'Blocked',
        ];
    }

    public static function typeList()
    {
        return [
            self::TYPE_INDIVIDUAL => 'Individual',
            self::TYPE_COMPANY => 'Company',
        ];
    }

    public function init()
    {
        parent::init();

        $this->on(self::EVENT_CREATED, ['common\listeners\ClientListener'::class, 'onCreate']);
        $this->on(self::EVENT_UPDATED, ['common\listeners\ClientListener'::class, 'onUpdate']);
        $this->on(self::EVENT_DELETED, ['common\listeners\ClientListener'::class, 'onDelete']);
        $this->on(self::EVENT_STATUS_CHANGED, ['common\listeners\ClientListener'::class, 'onStatusChange']);
    }

    public function afterSave($insert, $changed)
    {
        parent::afterSave($insert, $changed);

        if ($insert) {
            $this->trigger(self::EVENT_CREATED);
        } else {
            if (isset($changed['status'])) {
                $this->trigger(self::EVENT_STATUS_CHANGED);
            }
            $this->trigger(self::EVENT_UPDATED);
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();
        $this->trigger(self::EVENT_DELETED);
    }


    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($insert && empty($this->client_code)) {
            $this->client_code = 'CLT-' . date('Y') . '-' . strtoupper(substr(uniqid(), -4));
        }

        return true;
    }




    /* ================= RELATIONS ================= */

    // Client → Addresses
    public function getAddresses()
    {
        return $this->hasMany(ClientAddress::class, ['client_id' => 'id']);
    }

    // Client → Contacts
    public function getContacts()
    {
        return $this->hasMany(ClientContact::class, ['client_id' => 'id']);
    }

    public function getProjects()
    {
        return $this->hasMany(Project::class, ['client_id' => 'id']);
    }

    public function getContactPerson()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getCreatedAtTimestamp()
    {
        return strtotime($this->created_at);
    }

    // Client model
    public function getPrimaryContact()
    {
        return $this->hasOne(ClientContact::class, ['client_id' => 'id'])
            ->andWhere(['is_primary' => 1]);
    }

    public function getOfficeAddress()
    {
        return $this->hasOne(ClientAddress::class, ['client_id' => 'id'])
            ->andWhere(['address_type' => 'office']);
    }
}
