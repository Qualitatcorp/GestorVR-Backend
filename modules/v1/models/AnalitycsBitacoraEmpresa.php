<?php

namespace app\modules\v1\models;

use Yii;

class AnalitycsBitacoraEmpresa extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'analitycs_bitacora_empresa';
    }

    public function rules()
    {
        return [
            [['bit_id', 'emp_id'], 'required'],
            [['bit_id', 'emp_id'], 'integer'],
            [['bit_id', 'emp_id'], 'unique', 'targetAttribute' => ['bit_id', 'emp_id'], 'message' => 'The combination of Bit ID and Emp ID has already been taken.'],
            [['bit_id'], 'exist', 'skipOnError' => true, 'targetClass' => AnalitycsBitacora::className(), 'targetAttribute' => ['bit_id' => 'id']],
            [['emp_id'], 'exist', 'skipOnError' => true, 'targetClass' => Empresa::className(), 'targetAttribute' => ['emp_id' => 'emp_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bit_id' => 'Bit ID',
            'emp_id' => 'Emp ID',
        ];
    }

    public function extraFields()
    {
        return ['bitacora','empresa'];
    }
    
    public function getBitacora()
    {
        return $this->hasOne(AnalitycsBitacora::className(), ['id' => 'bit_id']);
    }

    public function getEmpresa()
    {
        return $this->hasOne(Empresa::className(), ['emp_id' => 'emp_id']);
    }
}
