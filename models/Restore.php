<?php

namespace micro\models;

use Yii;

/**
 * This is the model class for table "restore".
 *
 * @property int $id
 * @property int $user_id
 * @property string $password
 */
class Restore extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'restore';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'password'], 'required'],
            [['user_id'], 'integer'],
            [['password'], 'string', 'max' => 256],
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
            'password' => 'Password',
        ];
    }
}
