<?php

namespace app\modules\v1\models;

use Yii;

class AnalitycsBitacoraEvento extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'analitycs_bitacora_evento';
    }


    public function rules()
    {
        return [
            [['bit_id', 'visto', 'tocado', 'time'], 'required'],
            [['bit_id'], 'integer'],
            [['time'], 'number'],
            [['visto', 'tocado'], 'string', 'max' => 255],
            [['bit_id'], 'exist', 'skipOnError' => true, 'targetClass' => AnalitycsBitacora::className(), 'targetAttribute' => ['bit_id' => 'id']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bit_id' => 'Bit ID',
            'visto' => 'Visto',
            'tocado' => 'Tocado',
            'time' => 'Time',
        ];
    }

    public function extraFields()
    {
        return ['bitacora'];
    }

    public function getBitacora()
    {
        return $this->hasOne(AnalitycsBitacora::className(), ['id' => 'bit_id']);
    }
}
