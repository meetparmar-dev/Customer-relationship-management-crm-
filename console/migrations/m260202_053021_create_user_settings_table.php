<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_settings}}`.
 */
class m260202_053021_create_user_settings_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_settings}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'theme' => $this->string(20)->notNull()->defaultValue('light'),
            'language' => $this->string(10)->notNull()->defaultValue('en'),
            'created_at' => $this->integer()->notNull(),
        ]);

        // Index for performance
        $this->createIndex(
            'idx-user_settings-user_id',
            '{{%user_settings}}',
            'user_id'
        );

        // Foreign key
        $this->addForeignKey(
            'fk-user_settings-user_id',
            '{{%user_settings}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-user_settings-user_id',
            '{{%user_settings}}'
        );

        $this->dropIndex(
            'idx-user_settings-user_id',
            '{{%user_settings}}'
        );

        $this->dropTable('{{%user_settings}}');
    }
}
