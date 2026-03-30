<?php

use yii\db\Migration;

/**
 * Class m260109_050000_add_updated_at_to_client_contacts
 */
class m260109_050000_add_updated_at_to_client_contacts extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%client_contacts}}', 'updated_at', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE CURRENT_TIMESTAMP'));
    }

    /**
     * {@inheritdoc}
    */
    public function safeDown()
    {
        $this->dropColumn('{{%client_contacts}}', 'updated_at');
    }
}