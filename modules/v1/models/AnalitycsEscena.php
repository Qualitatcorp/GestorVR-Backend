<?php

namespace app\modules\v1\models;

use Yii;

class AnalitycsEscena extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'analitycs_escena';
    }


    public function rules()
    {
        return [
            [['app_id', 'name', 'indice'], 'required'],
            [['app_id', 'indice'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['app_id'], 'exist', 'skipOnError' => true, 'targetClass' => AnalitycsApp::className(), 'targetAttribute' => ['app_id' => 'id']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'app_id' => 'App ID',
            'name' => 'Name',
            'indice' => 'Indice',
        ];
    }

    public function extraFields()
    {
        return ['bitacoras','app'];
    }

    public function getBitacoras()
    {
        return $this->hasMany(AnalitycsBitacora::className(), ['sce_id' => 'id']);
    }

    public function getApp()
    {
        return $this->hasOne(AnalitycsApp::className(), ['id' => 'app_id']);
    }
}
