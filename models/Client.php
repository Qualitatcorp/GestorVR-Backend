<?php

namespace app\models;

use Yii;

class Client extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'user_client';
    }

    public function rules()
    {
        return [
            [['name', 'secret'], 'required'],
            [['name', 'secret'], 'string', 'max' => 64],
            [['redirect'], 'string', 'max' => 512],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'secret' => 'Secret',
            'redirect' => 'Redirect',
        ];
    }

    public function getAuthentications()
    {
        return $this->hasMany(Authentication::className(), ['client_id' => 'id']);
    }
}
