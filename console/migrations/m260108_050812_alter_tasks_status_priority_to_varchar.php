<?php

use yii\db\Migration;

class m260108_050812_alter_tasks_status_priority_to_varchar extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // status column ENUM -> VARCHAR
        $this->alterColumn(
            '{{%tasks}}',
            'status',
            $this->string(50)->notNull()->defaultValue('pending')
        );

        // priority column ENUM -> VARCHAR
        $this->alterColumn(
            '{{%tasks}}',
            'priority',
            $this->string(50)->notNull()->defaultValue('medium')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // rollback (optional but safe)
        $this->alterColumn(
            '{{%tasks}}',
            'status',
            "ENUM('pending','in_progress','completed') NOT NULL DEFAULT 'pending'"
        );

        $this->alterColumn(
            '{{%tasks}}',
            'priority',
            "ENUM('low','medium','high') NOT NULL DEFAULT 'medium'"
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260108_050812_alter_tasks_status_priority_to_varchar cannot be reverted.\n";

        return false;
    }
    */
}
