<?php

namespace app\models;

use Yii;

class Resource extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'user_resource';
    }

    public function rules()
    {
        return [
            [['resource'], 'required'],
            [['resource'], 'string', 'max' => 64],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'resource' => 'Resource',
        ];
    }

    public function getAuthorizations()
    {
        return $this->hasMany(Authorization::className(), ['res_id' => 'id']);
    }

    public function getParents()
    {
        return $this->hasMany(ResourceChildren::className(), ['parent_id' => 'id']);
    }

    public function getChildren()
    {
        return $this->hasOne(ResourceChildren::className(), ['child_id' => 'id']);
    }
}
