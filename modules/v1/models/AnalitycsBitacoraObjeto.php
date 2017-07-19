<?php

namespace app\modules\v1\models;

use Yii;

class AnalitycsBitacoraObjeto extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'analitycs_bitacora_objeto';
    }


    public function rules()
    {
        return [
            [['id', 'bit_id', 'nombre', 'veces'], 'required'],
            [['id', 'bit_id', 'veces'], 'integer'],
            [['nombre'], 'string', 'max' => 128],
            [['bit_id'], 'exist', 'skipOnError' => true, 'targetClass' => AnalitycsBitacora::className(), 'targetAttribute' => ['bit_id' => 'id']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bit_id' => 'Bit ID',
            'nombre' => 'Nombre',
            'veces' => 'Veces',
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
