<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "dispositivo".
 *
 * @property string $dis_id
 * @property integer $emp_id
 * @property integer $dit_id
 * @property string $nombre
 * @property string $creado
 * @property string $habilitado
 * @property string $activado
 * @property string $keycode
 * @property string $serial
 *
 * @property Empresa $emp
 * @property DispositivoTipo $dit
 * @property EmpresaDispositivo[] $empresaDispositivos
 * @property EmpresaUserDispositivo[] $empresaUserDispositivos
 * @property User[] $usus
 * @property RvFicha[] $rvFichas
 */
class Dispositivo extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'dispositivo';
    }


    public function rules()
    {
        return [
            [['emp_id', 'dit_id'], 'integer'],
            [['dit_id'], 'required'],
            [['creado'], 'safe'],
            [['habilitado', 'activado', 'keycode', 'serial'], 'string'],
            [['nombre'], 'string', 'max' => 150],
            [['emp_id'], 'exist', 'skipOnError' => true, 'targetClass' => Empresa::className(), 'targetAttribute' => ['emp_id' => 'emp_id']],
            [['dit_id'], 'exist', 'skipOnError' => true, 'targetClass' => DispositivoTipo::className(), 'targetAttribute' => ['dit_id' => 'dit_id']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'dis_id' => 'Dis ID',
            'emp_id' => 'Emp ID',
            'dit_id' => 'Dit ID',
            'nombre' => 'Nombre',
            'creado' => 'Creado',
            'habilitado' => 'Habilitado',
            'activado' => 'Activado',
            'keycode' => 'Keycode',
            'serial' => 'Serial',
        ];
    }

    public function getEmpresa()
    {
        return $this->hasOne(Empresa::className(), ['emp_id' => 'emp_id']);
    }

    public function getTipo()
    {
        return $this->hasOne(DispositivoTipo::className(), ['dit_id' => 'dit_id']);
    }

    public function getEmpDisp()
    {
        return $this->hasMany(EmpresaDispositivo::className(), ['dis_id' => 'dis_id']);
    }

    public function getUserDisp()
    {
        return $this->hasMany(EmpresaUserDispositivo::className(), ['dis_id' => 'dis_id']);
    }

    public function getUsuarios()
    {
        return $this->hasMany(User::className(), ['id' => 'usu_id'])->viaTable('empresa_user_dispositivo', ['dis_id' => 'dis_id']);
    }

    public function getFichas()
    {
        return $this->hasMany(RvFicha::className(), ['disp_id' => 'dis_id']);
    }
}
