<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200316193753UserTable
 */
class M200316193753UserTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "M200316193753UserTable cannot be reverted.\n";

        return false;
    }

    
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'first_name' => 'VARCHAR(256)',
            'last_name' => 'VARCHAR(256)',
            'patronymic' => 'VARCHAR(256)',
            'password' => 'VARCHAR(256)',
            'email' => 'VARCHAR(256) NOT NULL',
            'status' => 'BOOLEAN DEFAULT false',
            'role' => 'BOOLEAN DEFAULT false',
            'university_id' => 'INTEGER'
        ]);
    }

    public function down()
    {
        $this->dropTable('user');
    }
    
}
