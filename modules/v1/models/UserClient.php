<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "user_client".
 *
 * @property integer $id
 * @property string $name
 * @property string $secret
 * @property string $redirect
 *
 * @property UserAuthentication[] $userAuthentications
 */
class UserClient extends \yii\db\ActiveRecord
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
        return $this->hasMany(UserAuthentication::className(), ['client_id' => 'id']);
    }
}
