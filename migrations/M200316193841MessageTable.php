<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200316193841MessageTable
 */
class M200316193841MessageTable extends Migration
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
        echo "M200316193841MessageTable cannot be reverted.\n";

        return false;
    }

    public function up()
    {
        $this->createTable('message', [
            'id' => $this->primaryKey(),
            'user_id' => 'INTEGER NOT NULL',
            'file' => 'VARCHAR(256)',
            'text' => 'TEXT',
            'date' => 'DATETIME NOT NULL',
            'stream_id' => 'INTEGER NOT NULL'
        ]);
    }

    public function down()
    {
        $this->dropTable('message');
    }
}
