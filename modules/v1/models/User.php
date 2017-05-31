<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property string $id
 * @property string $username
 * @property string $rut
 * @property string $nombre
 * @property string $email
 * @property string $password
 * @property string $cargo
 * @property string $nacimiento
 * @property string $estado
 * @property string $tipo
 * @property string $creacion
 * @property string $modificacion
 *
 * @property EmpresaUser[] $empresaUsers
 * @property Empresa[] $emps
 * @property EmpresaUserDispositivo[] $empresaUserDispositivos
 * @property Dispositivo[] $dis
 * @property UserAuthentication[] $userAuthentications
 * @property UserAuthorization[] $userAuthorizations
 */
class User extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'user';
    }


    public function rules()
    {
        return [
            [['nombre', 'password'], 'required'],
            [['nacimiento', 'creacion', 'modificacion'], 'safe'],
            [['estado', 'tipo'], 'string'],
            [['username'], 'string', 'max' => 64],
            [['rut'], 'string', 'max' => 12],
            [['nombre', 'email', 'password', 'cargo'], 'string', 'max' => 255],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'rut' => 'Rut',
            'nombre' => 'Nombre',
            'email' => 'Email',
            'password' => 'Password',
            'cargo' => 'Cargo',
            'nacimiento' => 'Nacimiento',
            'estado' => 'Estado',
            'tipo' => 'Tipo',
            'creacion' => 'Creacion',
            'modificacion' => 'Modificacion',
        ];
    }

    public function getEmpresaUsers()
    {
        return $this->hasMany(EmpresaUser::className(), ['usu_id' => 'id']);
    }

    public function getEmps()
    {
        return $this->hasMany(Empresa::className(), ['emp_id' => 'emp_id'])->viaTable('empresa_user', ['usu_id' => 'id']);
    }

    public function getEmpresaUserDispositivos()
    {
        return $this->hasMany(EmpresaUserDispositivo::className(), ['usu_id' => 'id']);
    }

    public function getDis()
    {
        return $this->hasMany(Dispositivo::className(), ['dis_id' => 'dis_id'])->viaTable('empresa_user_dispositivo', ['usu_id' => 'id']);
    }

    public function getUserAuthentications()
    {
        return $this->hasMany(UserAuthentication::className(), ['user_id' => 'id']);
    }

    public function getUserAuthorizations()
    {
        return $this->hasMany(UserAuthorization::className(), ['user_id' => 'id']);
    }
}
