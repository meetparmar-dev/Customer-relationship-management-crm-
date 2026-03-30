<?php

use yii\db\Migration;

class m260105_055218_add_role_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'role', $this->tinyInteger()->defaultValue(0)->after('status'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'role');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260105_055218_add_role_to_user_table cannot be reverted.\n";

        return false;
    }
    */
}
