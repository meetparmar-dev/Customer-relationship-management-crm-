<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "projects".
 *
 * @property int $id
 * @property string|null $project_code
 * @property string $project_name
 * @property string|null $description
 * @property int $client_id
 * @property int|null $project_manager_id
 * @property string $status
 * @property string $priority
 * @property string|null $start_date
 * @property string|null $end_date
 * @property string|null $completed_at
 * @property float|null $budget
 * @property string|null $billing_type
 * @property int|null $estimated_hours
 * @property string|null $notes
 * @property int $created_at
 * @property int $updated_at
 */
class Project extends ActiveRecord
{


    const EVENT_CREATED        = 'projectCreated';
    const EVENT_UPDATED        = 'projectUpdated';
    const EVENT_STATUS_CHANGED = 'projectStatusChanged';

    const PRIORITY_LOW    = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH   = 'high';


    const STATUS_PLANNED   = 'planned';
    const STATUS_ACTIVE    = 'active';
    const STATUS_ON_HOLD   = 'on_hold';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';


    const BILLING_FIXED  = 'fixed';
    const BILLING_HOURLY = 'hourly';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%projects}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

            /* ================= REQUIRED ================= */
            [['project_name', 'client_id'], 'required'],

            /* ================= STRING ================= */
            [['description', 'notes'], 'string'],
            [['project_name'], 'string', 'max' => 255],
            [['project_code'], 'string', 'max' => 50],

            /* ================= INTEGER ================= */
            [['client_id', 'project_manager_id', 'estimated_hours'], 'integer'],

            /* ================= NUMBER ================= */
            [['budget'], 'number'],

            /* ================= DATES ================= */
            [['start_date', 'end_date', 'completed_at'], 'safe'],

            /* ================= ENUM / FIXED VALUES ================= */
            [
                ['status'],
                'in',
                'range' => array_keys(self::statusList())
            ],
            [
                ['priority'],
                'in',
                'range' => array_keys(self::priorityList())
            ],
            [
                ['billing_type'],
                'in',
                'range' => array_keys(self::billingTypeList())
            ],

            /* ================= UNIQUE ================= */
            [['project_code'], 'unique'],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_code' => 'Project Code',
            'project_name' => 'Project Name',
            'description' => 'Description',
            'client_id' => 'Client',
            'project_manager_id' => 'Project Manager',
            'status' => 'Status',
            'priority' => 'Priority',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'completed_at' => 'Completed At',
            'budget' => 'Budget',
            'billing_type' => 'Billing Type',
            'estimated_hours' => 'Estimated Hours',
            'notes' => 'Notes',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function priorityList(): array
    {
        return [
            self::PRIORITY_LOW    => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH   => 'High',
        ];
    }

    public static function statusList(): array
    {
        return [
            self::STATUS_PLANNED   => 'Planned',
            self::STATUS_ACTIVE    => 'Active',
            self::STATUS_ON_HOLD   => 'On Hold',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public static function billingTypeList(): array
    {
        return [
            self::BILLING_FIXED  => 'Fixed',
            self::BILLING_HOURLY => 'Hourly',
        ];
    }

    /**
     * Auto timestamps + project code
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_at = time();

            if (empty($this->project_code)) {
                $this->project_code = 'PRJ-' . date('Y') . '-' . strtoupper(substr(uniqid(), -4));
            }
        }

        $this->updated_at = time();

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            $this->trigger(self::EVENT_CREATED);
        } else {
            $this->trigger(self::EVENT_UPDATED);

            if (isset($changedAttributes['status'])) {
                $this->trigger(self::EVENT_STATUS_CHANGED);
            }
        }
    }

    /* ================= RELATIONS ================= */

    public function getClient()
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
    }

    public function getProjectManager()
    {
        return $this->hasOne(User::class, ['id' => 'project_manager_id']);
    }

    public function getTasks()
    {
        return $this->hasMany(Task::class, ['project_id' => 'id']);
    }

    public function getOfficeAddress()
    {
        return $this->hasOne(ClientContact::class, ['client_id' => 'id'])
            ->andWhere(['type' => 'office']);
    }

    /**
     * Virtual property to allow accessing project_name as name for backward compatibility
     */
    public function getName()
    {
        return $this->project_name;
    }

    public function setName($value)
    {
        $this->project_name = $value;
    }
}
