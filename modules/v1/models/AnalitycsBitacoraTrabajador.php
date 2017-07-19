<?php

namespace app\modules\v1\models;

use Yii;

class AnalitycsBitacoraTrabajador extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'analitycs_bitacora_trabajador';
    }

    public function rules()
    {
        return [
            [['bit_id', 'tra_id'], 'required'],
            [['bit_id', 'tra_id'], 'integer'],
            [['bit_id', 'tra_id'], 'unique', 'targetAttribute' => ['bit_id', 'tra_id'], 'message' => 'The combination of Bit ID and Tra ID has already been taken.'],
            [['bit_id'], 'exist', 'skipOnError' => true, 'targetClass' => AnalitycsBitacora::className(), 'targetAttribute' => ['bit_id' => 'id']],
            [['tra_id'], 'exist', 'skipOnError' => true, 'targetClass' => Trabajador::className(), 'targetAttribute' => ['tra_id' => 'tra_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bit_id' => 'Bit ID',
            'tra_id' => 'Tra ID',
        ];
    }

    public function extraFields()
    {
        return ['bitacora','trabajador'];
    }

    public function getBitacora()
    {
        return $this->hasOne(AnalitycsBitacora::className(), ['id' => 'bit_id']);
    }

    public function getTrabajador()
    {
        return $this->hasOne(Trabajador::className(), ['tra_id' => 'tra_id']);
    }
}
