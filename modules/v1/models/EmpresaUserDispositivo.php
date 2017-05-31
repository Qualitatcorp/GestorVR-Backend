<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "empresa_user_dispositivo".
 *
 * @property string $id
 * @property string $usu_id
 * @property string $dis_id
 *
 * @property Dispositivo $dis
 * @property User $usu
 */
class EmpresaUserDispositivo extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'empresa_user_dispositivo';
    }


    public function rules()
    {
        return [
            [['usu_id', 'dis_id'], 'required'],
            [['usu_id', 'dis_id'], 'integer'],
            [['usu_id', 'dis_id'], 'unique', 'targetAttribute' => ['usu_id', 'dis_id'], 'message' => 'The combination of Usu ID and Dis ID has already been taken.'],
            [['dis_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dispositivo::className(), 'targetAttribute' => ['dis_id' => 'dis_id']],
            [['usu_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['usu_id' => 'id']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'usu_id' => 'Usu ID',
            'dis_id' => 'Dis ID',
        ];
    }

    public function getDis()
    {
        return $this->hasOne(Dispositivo::className(), ['dis_id' => 'dis_id']);
    }

    public function getUsu()
    {
        return $this->hasOne(User::className(), ['id' => 'usu_id']);
    }
}
