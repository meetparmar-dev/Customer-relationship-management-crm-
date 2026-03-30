<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_session}}`.
 */
class m260113_055300_create_user_session_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_session}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'session_id' => $this->string(255)->notNull(),
            'ip' => $this->string(45),
            'user_agent' => $this->text(),
            'created_at' => $this->integer()->notNull(),
            'last_activity' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-session-user', '{{%user_session}}', 'user_id');
        $this->createIndex('idx-session-id', '{{%user_session}}', 'session_id', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_session}}');
    }
}
