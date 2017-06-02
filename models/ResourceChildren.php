<?php

namespace app\models;

use Yii;

class ResourceChildren extends \yii\db\ActiveRecord
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
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Resource::className(), 'targetAttribute' => ['parent_id' => 'id']],
            [['child_id'], 'exist', 'skipOnError' => true, 'targetClass' => Resource::className(), 'targetAttribute' => ['child_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Recurso Padre',
            'child_id' => 'Recurso Hijo',
        ];
    }

    public function getParent()
    {
        return $this->hasOne(Resource::className(), ['id' => 'parent_id']);
    }

    public function getChild()
    {
        return $this->hasOne(Resource::className(), ['id' => 'child_id']);
    }
}
