<?php

use yii\db\Migration;

class m260107_055731_add_updated_at_to_client_addresses extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%client_addresses}}',
            'updated_at',
            $this->timestamp()
                ->notNull()
                ->defaultExpression('CURRENT_TIMESTAMP')
                ->append('ON UPDATE CURRENT_TIMESTAMP')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%client_addresses}}', 'updated_at');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260107_055731_add_updated_at_to_client_addresses cannot be reverted.\n";

        return false;
    }
    */
}
