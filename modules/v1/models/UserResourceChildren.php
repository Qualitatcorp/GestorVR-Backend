<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "user_resource_children".
 *
 * @property string $id
 * @property integer $parent_id
 * @property integer $child_id
 *
 * @property UserResource $parent
 * @property UserResource $child
 */
class UserResourceChildren extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'user_resource_children';
    }


    public function rules()
    {
        return [
            [['parent_id', 'child_id'], 'required'],
            [['parent_id', 'child_id'], 'integer'],
            [['child_id'], 'unique'],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserResource::className(), 'targetAttribute' => ['parent_id' => 'id']],
            [['child_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserResource::className(), 'targetAttribute' => ['child_id' => 'id']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'child_id' => 'Child ID',
        ];
    }

    public function getParent()
    {
        return $this->hasOne(UserResource::className(), ['id' => 'parent_id']);
    }

    public function getChild()
    {
        return $this->hasOne(UserResource::className(), ['id' => 'child_id']);
    }
}
