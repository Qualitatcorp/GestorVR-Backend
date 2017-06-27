<?php

namespace app\modules\v1\models;

use Yii;

class AnalitycsApp extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'analitycs_app';
    }


    public function rules()
    {
        return [
            [['company', 'producto', 'version'], 'required'],
            [['company', 'producto'], 'string', 'max' => 128],
            [['version'], 'string', 'max' => 16],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company' => 'Company',
            'producto' => 'Producto',
            'version' => 'Version',
        ];
    }

    public function extraFields()
    {
        return ['escenas'];
    }

    public function getEscenas()
    {
        return $this->hasMany(AnalitycsEscena::className(), ['app_id' => 'id']);
    }
}
