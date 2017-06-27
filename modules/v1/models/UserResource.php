<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "user_resource".
 *
 * @property integer $id
 * @property string $resource
 *
 * @property UserAuthorization[] $userAuthorizations
 * @property UserResourceChildren[] $userResourceChildrens
 * @property UserResourceChildren $userResourceChildren
 */
class UserResource extends \yii\db\ActiveRecord
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

    public function extraFields()
    {
        return ['children','resourceChildren','parents','authorization'];
    }

    public function getAuthorizations()
    {
        return $this->hasMany(UserAuthorization::className(), ['res_id' => 'id']);
    }

    public function getChildren()
    {
        return $this->hasMany(UserResourceChildren::className(), ['parent_id' => 'id']);
    }

    public function getResourceChildren()
    {
        return $this->hasMany(UserResource::className(),['id'=>'child_id'])->via('children');
    }

    public function getParents()
    {
        return $this->hasOne(UserResourceChildren::className(), ['child_id' => 'id']);
    }
}
