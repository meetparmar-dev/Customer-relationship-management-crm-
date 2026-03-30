<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%queue}}`.
 */
class m260201_153942_create_queue_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%queue}}', [
            'id' => $this->primaryKey(),
            'channel' => $this->string()->notNull(),
            'job' => $this->binary()->notNull(),
            'pushed_at' => $this->integer()->notNull(),
            'ttr' => $this->integer()->notNull(),
            'delay' => $this->integer()->notNull()->defaultValue(0),
            'priority' => $this->integer()->unsigned()->notNull()->defaultValue(1024),
            'reserved_at' => $this->integer(),
            'attempt' => $this->integer(),
            'done_at' => $this->integer(),
        ]);

        $this->createIndex('idx-queue-channel-priority', '{{%queue}}', ['channel', 'priority']);
        $this->createIndex('idx-queue-reserved-at', '{{%queue}}', ['reserved_at']);
        $this->createIndex('idx-queue-done-at', '{{%queue}}', ['done_at']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%queue}}');
    }
}
