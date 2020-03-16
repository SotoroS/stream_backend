<?php

namespace micro\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $patronymic
 * @property string|null $password
 * @property string $email
 * @property string $access_token
 * @property int|null $status
 * @property int|null $role
 * @property int|null $university_id
 *
 * @property Message[] $messages
 * @property Stream[] $streams
 * @property University $university
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'access_token'], 'required'],
            [['status', 'role', 'university_id'], 'integer'],
            [['first_name', 'last_name', 'patronymic', 'password', 'email', 'access_token'], 'string', 'max' => 256],
            [['university_id'], 'exist', 'skipOnError' => true, 'targetClass' => University::className(), 'targetAttribute' => ['university_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'patronymic' => 'Patronymic',
            'password' => 'Password',
            'email' => 'Email',
            'access_token' => 'Access Token',
            'status' => 'Status',
            'role' => 'Role',
            'university_id' => 'University ID',
        ];
    }

    /**
     * Gets query for [[Messages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(Message::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Streams]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStreams()
    {
        return $this->hasMany(Stream::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[University]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUniversity()
    {
        return $this->hasOne(University::className(), ['id' => 'university_id']);
    }
}
