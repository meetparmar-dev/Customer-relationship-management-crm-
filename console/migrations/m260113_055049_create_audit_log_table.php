<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%audit_log}}`.
 */
class m260113_055049_create_audit_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%audit_log}}', [
            'id' => $this->primaryKey(),

            'user_id' => $this->integer()->null(),

            'action' => $this->string(100)->notNull(),

            'entity' => $this->string(100)->null(),
            'entity_id' => $this->integer()->null(),

            'ip' => $this->string(50)->null(),
            'user_agent' => $this->string(255)->null(),

            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx_audit_user', '{{%audit_log}}', 'user_id');
        $this->createIndex('idx_audit_action', '{{%audit_log}}', 'action');
        $this->createIndex('idx_audit_entity', '{{%audit_log}}', 'entity');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%audit_log}}');
    }
}
