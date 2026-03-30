<?php

use yii\db\Migration;

class m260119_114942_add_jwt_id_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%user}}',
            'jwt_id',
            $this->string(64)->null()->after('access_token')
        );
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'jwt_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260119_114942_add_jwt_id_to_user_table cannot be reverted.\n";

        return false;
    }
    */
}
