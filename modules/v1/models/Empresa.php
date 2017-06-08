<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "empresa".
 *
 * @property integer $emp_id
 * @property string $nombre
 * @property string $rut
 * @property integer $com_id
 * @property string $razon_social
 * @property string $giro
 * @property string $fono
 * @property string $mail
 * @property string $clasificacion
 * @property string $creado
 * @property string $activa
 *
 * @property Dispositivo[] $dispositivos
 * @property Comuna $com
 * @property EmpresaUser[] $empresaUsers
 * @property User[] $usus
 * @property EmpresaUsuario[] $empresaUsuarios
 * @property Licencia[] $licencias
 */
class Empresa extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'empresa';
    }


    public function rules()
    {
        return [
            [['nombre', 'razon_social'], 'required'],
            [['nombre', 'razon_social', 'mail', 'clasificacion', 'activa'], 'string'],
            [['com_id', 'giro'], 'integer'],
            [['creado'], 'safe'],
            [['rut'], 'string', 'max' => 12],
            [['fono'], 'string', 'max' => 50],
            [['com_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comuna::className(), 'targetAttribute' => ['com_id' => 'com_id']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'emp_id' => 'Emp ID',
            'nombre' => 'Nombre',
            'rut' => 'Rut',
            'com_id' => 'Com ID',
            'razon_social' => 'Razon Social',
            'giro' => 'Giro',
            'fono' => 'Fono',
            'mail' => 'Mail',
            'clasificacion' => 'Clasificacion',
            'creado' => 'Creado',
            'activa' => 'Activa',
        ];
    }

    public function getDispositivos()
    {
        return $this->hasMany(Dispositivo::className(), ['emp_id' => 'emp_id']);
    }

    public function getComuna()
    {
        return $this->hasOne(Comuna::className(), ['com_id' => 'com_id']);
    }

    public function getUsers()
    {
        return $this->hasMany(EmpresaUser::className(), ['emp_id' => 'emp_id']);
    }

    public function getUsuarios()
    {
        return $this->hasMany(User::className(), ['id' => 'usu_id'])->viaTable('empresa_user', ['emp_id' => 'emp_id']);
    }

    public function getEmpresaUsuarios()
    {
        return $this->hasMany(EmpresaUsuario::className(), ['emp_id' => 'emp_id']);
    }

    public function getLicencias()
    {
        return $this->hasMany(Licencia::className(), ['emp_id' => 'emp_id']);
    }
}
