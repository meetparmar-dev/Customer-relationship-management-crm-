<?php

use yii\db\Migration;

class m260112_060812_add_twofa_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'twofa_enabled', $this->boolean()->defaultValue(0)->after('status'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'twofa_enabled');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260112_060812_add_twofa_to_user_table cannot be reverted.\n";

        return false;
    }
    */
}
