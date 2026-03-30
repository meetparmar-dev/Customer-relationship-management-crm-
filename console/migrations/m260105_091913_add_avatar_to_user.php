<?php

use yii\db\Migration;

class m260105_091913_add_avatar_to_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'avatar', $this->string()->null()->after('username'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'avatar');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260105_091913_add_avatar_to_user cannot be reverted.\n";

        return false;
    }
    */
}
