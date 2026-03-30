<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%clients}}`.
 */
class m260106_032723_create_clients_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%clients}}', [
            'id' => $this->primaryKey(),

            'client_code' => $this->string(20)->unique(),

            'type' => "ENUM('individual','company') NOT NULL DEFAULT 'individual'",

            'company_name' => $this->string(150)->null(),

            'first_name' => $this->string(100)->notNull(),
            'last_name'  => $this->string(100)->null(),

            'email' => $this->string(150)->notNull(),
            'phone' => $this->string(20)->notNull(),

            'status' => "ENUM('lead','active','inactive') NOT NULL DEFAULT 'lead'",

            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()
                ->defaultExpression('CURRENT_TIMESTAMP')
                ->append('ON UPDATE CURRENT_TIMESTAMP'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%clients}}');
    }
}
