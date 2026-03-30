<?php

use yii\db\Migration;

class m260106_114146_convert_project_enum_to_varchar extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // STATUS
        $this->alterColumn(
            '{{%projects}}',
            'status',
            $this->string(20)->notNull()->defaultValue('planned')
        );

        // PRIORITY
        $this->alterColumn(
            '{{%projects}}',
            'priority',
            $this->string(10)->notNull()->defaultValue('medium')
        );

        // BILLING TYPE
        $this->alterColumn(
            '{{%projects}}',
            'billing_type',
            $this->string(10)->notNull()->defaultValue('fixed')
        );

        // Optional indexes (recommended)
        $this->createIndex('idx_projects_status', '{{%projects}}', 'status');
        $this->createIndex('idx_projects_priority', '{{%projects}}', 'priority');
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "This migration cannot be reverted safely.\n";
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260106_114146_convert_project_enum_to_varchar cannot be reverted.\n";

        return false;
    }
    */
}
