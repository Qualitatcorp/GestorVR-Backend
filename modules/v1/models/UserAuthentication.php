<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "user_authentication".
 *
 * @property string $token
 * @property string $refresh
 * @property string $created
 * @property string $expire
 * @property string $user_id
 * @property integer $client_id
 *
 * @property User $user
 * @property UserClient $client
 */
class UserAuthentication extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'user_authentication';
    }


    public function rules()
    {
        return [
            [['token', 'created', 'expire', 'user_id', 'client_id'], 'required'],
            [['created', 'expire', 'user_id', 'client_id'], 'integer'],
            [['token', 'refresh'], 'string', 'max' => 32],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserClient::className(), 'targetAttribute' => ['client_id' => 'id']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'token' => 'Token',
            'refresh' => 'Refresh',
            'created' => 'Created',
            'expire' => 'Expire',
            'user_id' => 'User ID',
            'client_id' => 'Client ID',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getClient()
    {
        return $this->hasOne(UserClient::className(), ['id' => 'client_id']);
    }
}
