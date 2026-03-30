<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%projects}}`.
 */
class m260106_061353_create_projects_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%projects}}', [

            'id' => $this->primaryKey(),

            'project_code' => $this->string(50)->unique(),
            'project_name' => $this->string(255)->notNull(),
            'description' => $this->text(),

            'client_id' => $this->integer()->notNull(),
            'project_manager_id' => $this->integer(),

            'status' => "ENUM('planned','active','on_hold','completed','cancelled') NOT NULL DEFAULT 'planned'",
            'priority' => "ENUM('low','medium','high') NOT NULL DEFAULT 'medium'",

            'start_date' => $this->date(),
            'end_date' => $this->date(),
            'completed_at' => $this->dateTime()->null(),

            'budget' => $this->decimal(10, 2),
            'billing_type' => "ENUM('fixed','hourly')",
            'estimated_hours' => $this->integer(),

            'notes' => $this->text(),

            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // Foreign Keys (optional but recommended)
        $this->addForeignKey(
            'fk-projects-client_id',
            '{{%projects}}',
            'client_id',
            '{{%clients}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-projects-manager_id',
            '{{%projects}}',
            'project_manager_id',
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
        $this->dropForeignKey('fk-projects-client_id', '{{%projects}}');
        $this->dropForeignKey('fk-projects-manager_id', '{{%projects}}');
        $this->dropTable('{{%projects}}');
    }
}
