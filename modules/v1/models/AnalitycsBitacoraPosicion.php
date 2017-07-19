<?php

namespace app\modules\v1\models;

use Yii;

class AnalitycsBitacoraPosicion extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'analitycs_bitacora_posicion';
    }


    public function rules()
    {
        return [
            [['bit_id', 'x', 'y', 'z'], 'required'],
            [['bit_id'], 'integer'],
            [['x', 'y', 'z'], 'number'],
            [['bit_id'], 'exist', 'skipOnError' => true, 'targetClass' => AnalitycsBitacora::className(), 'targetAttribute' => ['bit_id' => 'id']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bit_id' => 'Bit ID',
            'x' => 'X',
            'y' => 'Y',
            'z' => 'Z',
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
