<?php

use yii\db\Migration;

class m260106_055739_update_client_status_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn(
            '{{%clients}}',
            'status',
            $this->string(30)->notNull()->defaultValue('lead')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn(
            '{{%clients}}',
            'status',
            "ENUM('lead','active','inactive') NOT NULL DEFAULT 'lead'"
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260106_055739_update_client_status_column cannot be reverted.\n";

        return false;
    }
    */
}
