<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "empresa_usuario".
 *
 * @property string $emu_id
 * @property integer $emp_id
 * @property integer $usu_id
 * @property string $nombres
 * @property string $paterno
 * @property string $materno
 * @property string $fono
 * @property string $clasificacion
 *
 * @property EmpresaDispositivo[] $empresaDispositivos
 * @property Empresa $emp
 * @property UsuarioUser $usu
 */
class EmpresaUsuario extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'empresa_usuario';
    }


    public function rules()
    {
        return [
            [['emp_id', 'usu_id', 'nombres', 'paterno', 'materno', 'fono'], 'required'],
            [['emp_id', 'usu_id'], 'integer'],
            [['clasificacion'], 'string'],
            [['nombres', 'paterno', 'materno', 'fono'], 'string', 'max' => 150],
            [['emp_id'], 'exist', 'skipOnError' => true, 'targetClass' => Empresa::className(), 'targetAttribute' => ['emp_id' => 'emp_id']],
            [['usu_id'], 'exist', 'skipOnError' => true, 'targetClass' => UsuarioUser::className(), 'targetAttribute' => ['usu_id' => 'iduser']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'emu_id' => 'Emu ID',
            'emp_id' => 'Emp ID',
            'usu_id' => 'Usu ID',
            'nombres' => 'Nombres',
            'paterno' => 'Paterno',
            'materno' => 'Materno',
            'fono' => 'Fono',
            'clasificacion' => 'Clasificacion',
        ];
    }

    public function getEmpresaDispositivos()
    {
        return $this->hasMany(EmpresaDispositivo::className(), ['emu_id' => 'emu_id']);
    }

    public function getEmp()
    {
        return $this->hasOne(Empresa::className(), ['emp_id' => 'emp_id']);
    }

    public function getUsu()
    {
        return $this->hasOne(UsuarioUser::className(), ['iduser' => 'usu_id']);
    }
}
