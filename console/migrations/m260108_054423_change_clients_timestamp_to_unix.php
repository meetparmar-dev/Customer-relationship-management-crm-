<?php

use yii\db\Migration;

class m260108_054423_change_clients_timestamp_to_unix extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // 🔹 Backup existing timestamp data → unix timestamp
        $this->execute("
            UPDATE clients 
            SET 
                created_at = UNIX_TIMESTAMP(created_at),
                updated_at = UNIX_TIMESTAMP(updated_at)
        ");

        // 🔹 Change column types
        $this->alterColumn('clients', 'created_at', $this->integer()->notNull());
        $this->alterColumn('clients', 'updated_at', $this->integer()->notNull());
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // 🔹 Convert unix timestamp back to datetime
        $this->execute("
            UPDATE clients 
            SET 
                created_at = FROM_UNIXTIME(created_at),
                updated_at = FROM_UNIXTIME(updated_at)
        ");

        // 🔹 Revert column types
        $this->alterColumn(
            'clients',
            'created_at',
            $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')
        );

        $this->alterColumn(
            'clients',
            'updated_at',
            $this->timestamp()
                ->notNull()
                ->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260108_054423_change_clients_timestamp_to_unix cannot be reverted.\n";

        return false;
    }
    */
}
