<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tasks}}`.
 */
class m260106_065151_create_tasks_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tasks}}', [

            'id' => $this->primaryKey(),

            // Relations
            'project_id' => $this->integer()->notNull(),
            'assigned_to' => $this->integer()->null(),

            // Task Info
            'title' => $this->string(255)->notNull(),
            'description' => $this->text(),

            // Status & Priority
            'status' => "ENUM('pending','in_progress','completed') NOT NULL DEFAULT 'pending'",
            'priority' => "ENUM('low','medium','high') NOT NULL DEFAULT 'medium'",

            // Dates
            'start_date' => $this->date(),
            'due_date' => $this->date(),
            'completed_at' => $this->dateTime()->null(),

            // Optional
            'estimated_hours' => $this->integer(),

            // System
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // Foreign key: Project → Task
        $this->addForeignKey(
            'fk-tasks-project_id',
            '{{%tasks}}',
            'project_id',
            '{{%projects}}',
            'id',
            'CASCADE'
        );

        // Foreign key: User → Task (assigned)
        $this->addForeignKey(
            'fk-tasks-assigned_to',
            '{{%tasks}}',
            'assigned_to',
            '{{%user}}',
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-tasks-project_id', '{{%tasks}}');
        $this->dropForeignKey('fk-tasks-assigned_to', '{{%tasks}}');
        $this->dropTable('{{%tasks}}');
    }
}
