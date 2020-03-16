<?php

namespace micro\models;

use Yii;

/**
 * This is the model class for table "message".
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $file
 * @property string|null $text
 * @property string $date
 * @property int $stream_id
 *
 * @property Stream $stream
 * @property User $user
 */
class Message extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'message';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'date', 'stream_id'], 'required'],
            [['user_id', 'stream_id'], 'integer'],
            [['text'], 'string'],
            [['date'], 'safe'],
            [['file'], 'string', 'max' => 256],
            [['stream_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stream::className(), 'targetAttribute' => ['stream_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'file' => 'File',
            'text' => 'Text',
            'date' => 'Date',
            'stream_id' => 'Stream ID',
        ];
    }

    /**
     * Gets query for [[Stream]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStream()
    {
        return $this->hasOne(Stream::className(), ['id' => 'stream_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
