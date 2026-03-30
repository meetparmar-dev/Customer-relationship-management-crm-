<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Task extends ActiveRecord
{

    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_ON_HOLD = 'now on hold';

    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';

    public static function tableName()
    {
        return '{{%tasks}}';
    }

    public function rules()
    {
        return [

            [['project_id', 'title'], 'required'],

            [['project_id', 'assigned_to', 'estimated_hours'], 'integer'],

            [['description'], 'string'],

            [['start_date', 'due_date', 'completed_at'], 'safe'],

            [['title'], 'string', 'max' => 255],

            [['status', 'priority'], 'string', 'max' => 50],

            ['status', 'default', 'value' => 'pending'],
            ['priority', 'default', 'value' => 'medium'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => 'Project',
            'assigned_to' => 'Assigned To',
            'title' => 'Task Title',
            'description' => 'Description',
            'status' => 'Status',
            'priority' => 'Priority',
            'start_date' => 'Start Date',
            'due_date' => 'Due Date',
            'completed_at' => 'Completed At',
            'estimated_hours' => 'Estimated Hours',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($insert) {
            $this->created_at = time();
        }

        $this->updated_at = time();

        //completed_at logic (datetime safe)
        if ($this->status === 'completed' && empty($this->completed_at)) {
            $this->completed_at = date('Y-m-d H:i:s');
        }

        // optional: reset completed_at if status changed back
        if ($this->status !== 'completed') {
            $this->completed_at = null;
        }

        return true;
    }

    public static function statusList()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_ON_HOLD => 'On Hold',
        ];
    }

    public static function priorityList()
    {
        return [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH => 'High',
        ];
    }

    /* ================= RELATIONS ================= */

    public function getProject()
    {
        return $this->hasOne(Project::class, ['id' => 'project_id']);
    }

    public function getAssignee()
    {
        return $this->hasOne(User::class, ['id' => 'assigned_to']);
    }
}
