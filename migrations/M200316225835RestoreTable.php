<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200316225835RestoreTable
 */
class M200316225835RestoreTable extends Migration
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
        echo "M200316225835RestoreTable cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('restore', [
            'id' => $this->primaryKey(),
            'user_id' => 'INT NOT NULL',
            'password' => 'VARCHAR(256) NOT NULL',
        ]);
    }

    public function down()
    {
        $this->dropTable('message');
    }
    
}
