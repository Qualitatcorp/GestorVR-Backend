<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "empresa_dispositivo".
 *
 * @property string $emd_id
 * @property string $dis_id
 * @property string $emu_id
 *
 * @property Dispositivo $dis
 * @property EmpresaUsuario $emu
 */
class EmpresaDispositivo extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'empresa_dispositivo';
    }


    public function rules()
    {
        return [
            [['dis_id', 'emu_id'], 'required'],
            [['dis_id', 'emu_id'], 'integer'],
            [['dis_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dispositivo::className(), 'targetAttribute' => ['dis_id' => 'dis_id']],
            [['emu_id'], 'exist', 'skipOnError' => true, 'targetClass' => EmpresaUsuario::className(), 'targetAttribute' => ['emu_id' => 'emu_id']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'emd_id' => 'Emd ID',
            'dis_id' => 'Dispositivo',
            'emu_id' => 'Usuario',
        ];
    }

    public function getDispositivo()
    {
        return $this->hasOne(Dispositivo::className(), ['dis_id' => 'dis_id']);
    }

    public function getUsuario()
    {
        return $this->hasOne(EmpresaUsuario::className(), ['emu_id' => 'emu_id']);
    }
}
