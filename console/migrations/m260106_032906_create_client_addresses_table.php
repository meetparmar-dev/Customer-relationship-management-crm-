<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%client_addresses}}`.
 */
class m260106_032906_create_client_addresses_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%client_addresses}}', [
            'id' => $this->primaryKey(),

            'client_id' => $this->integer()->notNull(),

            'address_type' => "ENUM('billing','shipping','office') NOT NULL DEFAULT 'billing'",

            'address' => $this->text()->notNull(),
            'city'    => $this->string(100)->notNull(),
            'state'   => $this->string(100)->notNull(),
            'country' => $this->string(100)->defaultValue('India'),
            'pincode' => $this->string(10)->notNull(),

            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk_client_addresses_client_id',
            '{{%client_addresses}}',
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
            'fk_client_addresses_client_id',
            '{{%client_addresses}}'
        );

        $this->dropTable('{{%client_addresses}}');
    }
}
