<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200316193944Realationships
 */
class M200316193944Realationships extends Migration
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
        echo "M200316193944Realationships cannot be reverted.\n";

        return false;
    }

    
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        // ---USER--- //

        // creates index for column `university_id` in table `user`
        $this->createIndex(
            'idx-user-university_id',
            'user',
            'university_id'
        );
        // add foreign key for table `university`
        $this->addForeignKey(
            'fk-user-university_id',
            'user',
            'university_id',
            'university',
            'id'
        );

        // ---STREAM--- //

        // creates index for column `user_id` in table `stream`
        $this->createIndex(
            'idx-stream-user_id',
            'stream',
            'user_id'
        );
        // add foreign key for table `university`
        $this->addForeignKey(
            'fk-stream-user_id',
            'stream',
            'user_id',
            'user',
            'id'
        );

        // ---MESSAGE--- //

        // creates index for column `user_id` in table `message`
        $this->createIndex(
            'idx-message-user_id',
            'message',
            'user_id'
        );
        // add foreign key for table `university`
        $this->addForeignKey(
            'fk-message-user_id',
            'message',
            'user_id',
            'user',
            'id'
        );

        // creates index for column `stream_id` in table `message`
        $this->createIndex(
            'idx-message-stream_id',
            'message',
            'stream_id'
        );
        // add foreign key for table `university`
        $this->addForeignKey(
            'fk-message-stream_id',
            'message',
            'stream_id',
            'stream',
            'id'
        );
    }

    public function down()
    {
        // ---USER--- //

        // drops foreign key for table `university`
        $this->dropForeignKey(
            'fk-user-university_id',
            'user'
        );
        // drops index for column `university_id`
        $this->dropIndex(
            'idx-user-university_id',
            'user'
        );

        // ---STREAM--- //

        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-stream-user_id',
            'stream'
        );
        // drops index for column `user_id`
        $this->dropIndex(
            'idx-stream-user_id',
            'stream'
        );

        // ---MESSAGE--- //

        // drops foreign key for table `message`
        $this->dropForeignKey(
            'fk-message-user_id',
            'message'
        );
        // drops index for column `user_id`
        $this->dropIndex(
            'idx-message-user_id',
            'message'
        );
        
        // drops foreign key for table `message`
        $this->dropForeignKey(
            'fk-message-stream_id',
            'message'
        );
        // drops index for column `stream_id`
        $this->dropIndex(
            'idx-message-stream_id',
            'message'
        );
    }
}
