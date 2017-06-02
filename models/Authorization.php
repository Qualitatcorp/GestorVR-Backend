<?php

namespace app\models;

use Yii;

class Authorization extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'user_authorization';
    }

    public function rules()
    {
        return [
            [['user_id', 'res_id'], 'required'],
            [['user_id', 'res_id'], 'integer'],
            [['res_id'], 'exist', 'skipOnError' => true, 'targetClass' => Resource::className(), 'targetAttribute' => ['res_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'res_id' => 'Res ID',
        ];
    }

    public function getResource()
    {
        return $this->hasOne(Resource::className(), ['id' => 'res_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
