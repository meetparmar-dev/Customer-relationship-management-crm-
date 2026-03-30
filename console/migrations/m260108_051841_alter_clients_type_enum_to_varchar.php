<?php

use yii\db\Migration;

class m260108_051841_alter_clients_type_enum_to_varchar extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn(
            '{{%clients}}',
            'type',
            $this->string(30)->notNull()->defaultValue('individual')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn(
            '{{%clients}}',
            'type',
            "ENUM('individual','company') NOT NULL DEFAULT 'individual'"
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260108_051841_alter_clients_type_enum_to_varchar cannot be reverted.\n";

        return false;
    }
    */
}
