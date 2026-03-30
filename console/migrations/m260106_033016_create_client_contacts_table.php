<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%client_contacts}}`.
 */
class m260106_033016_create_client_contacts_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%client_contacts}}', [
            'id' => $this->primaryKey(),

            'client_id' => $this->integer()->notNull(),

            'name'        => $this->string(150)->notNull(),
            'designation' => $this->string(100)->null(),

            'email' => $this->string(150)->null(),
            'phone' => $this->string(20)->null(),

            'is_primary' => $this->boolean()->defaultValue(false),

            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk_client_contacts_client_id',
            '{{%client_contacts}}',
            'client_id',
            '{{%clients}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk_client_contacts_client_id',
            '{{%client_contacts}}'
        );

        $this->dropTable('{{%client_contacts}}');
    }
}
