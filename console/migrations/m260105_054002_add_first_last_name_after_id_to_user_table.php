<?php

use yii\db\Migration;

class m260105_054002_add_first_last_name_after_id_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%user}}',
            'first_name',
            $this->string(100)->notNull()->after('id')
        );

        $this->addColumn(
            '{{%user}}',
            'last_name',
            $this->string(100)->notNull()->after('first_name')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'last_name');
        $this->dropColumn('{{%user}}', 'first_name');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260105_054002_add_first_last_name_after_id_to_user_table cannot be reverted.\n";

        return false;
    }
    */
}
