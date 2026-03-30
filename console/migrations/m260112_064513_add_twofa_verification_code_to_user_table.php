<?php

use yii\db\Migration;

class m260112_064513_add_twofa_verification_code_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'twofa_verification_code', $this->string(6)->after('twofa_enabled'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'twofa_verification_code');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260112_064513_add_twofa_verification_code_to_user_table cannot be reverted.\n";

        return false;
    }
    */
}
