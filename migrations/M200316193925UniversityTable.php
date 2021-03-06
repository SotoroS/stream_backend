<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200316193925UniversityTable
 */
class M200316193925UniversityTable extends Migration
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
        echo "M200316193925UniversityTable cannot be reverted.\n";

        return false;
    }

    public function up()
    {
        $this->createTable('university', [
            'id' => $this->primaryKey(),
            'name' => 'VARCHAR(256) NOT NULL',
            'email' => 'VARCHAR(256) NOT NULL',
            'password' => 'VARCHAR(256) NOT NULL',
            'access_token' => 'VARCHAR(256) NOT NULL',
            'verified' => 'BOOLEAN DEFAULT 0'
        ]);
    }

    public function down()
    {
        $this->dropTable('university');
    }
}
