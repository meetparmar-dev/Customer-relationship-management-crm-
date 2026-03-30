<?php

use yii\db\Migration;

class m260105_093910_add_pending_email_to_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%user}}',
            'pending_email',
            $this->string()->null()->after('email')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'pending_email');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260105_093910_add_pending_email_to_user cannot be reverted.\n";

        return false;
    }
    */
}
