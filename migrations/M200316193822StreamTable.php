<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200316193822StreamTable
 */
class M200316193822StreamTable extends Migration
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
        echo "M200316193822StreamTable cannot be reverted.\n";

        return false;
    }

    public function up()
    {
        $this->createTable('stream', [
            'id' => $this->primaryKey(),
            'user_id' => 'INTEGER NOT NULL',
            'name' => 'VARCHAR(256) NOT NULL',
            'date' => 'DATETIME NOT NULL'
        ]);
    }

    public function down()
    {
        $this->dropTable('stream');
    }
}
