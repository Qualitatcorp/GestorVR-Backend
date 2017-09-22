<?php

namespace app\models;

use Yii;

class Authentication extends \yii\db\ActiveRecord
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
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'id']],
            ['refresh','unique'],
            ['token', 'unique']

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
            'client_id' => 'Client ID'
        ];
    }

    public static function findActive()
    {
        return static::find()
            ->andWhere(['>','expire',time()])
            ->andWhere(["status"=>"ALLOW"]);
    }

    public static function UpdateHistory()
    {
       Yii::$app->db
            ->createCommand('call sp_access_update_history(:time)')
            ->bindValue(":time",time())
            ->noCache()
            ->execute();
    }

    public function Renovate($timeOut=3600){
        $this->refresh=\Yii::$app->security->generateRandomString();
        $this->expire+=$timeOut;
        return $this->save();
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }
}
