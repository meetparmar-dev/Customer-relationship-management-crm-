<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user}}`.
 */
class m260112_071701_add_twofa_columns_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'twofa_secret', $this->string(10)->null()->after('twofa_enabled'));
        $this->addColumn('{{%user}}', 'twofa_expires', $this->integer()->null()->after('twofa_secret'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'twofa_expires');
        $this->dropColumn('{{%user}}', 'twofa_secret');
    }
}
