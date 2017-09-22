<?php

namespace app\modules\v1\models;

use Yii;

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
            ['password','string','min'=>6],
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

    public function fields()
    {
        $fields = parent::fields();
        unset($fields['password']);
        return $fields;
    }

    public function extraFields()
    {
        return ['empresas','dispositivos','authentications','authorizations'];
    }

    public function getEmpresaUsers()
    {
        return $this->hasMany(EmpresaUser::className(), ['usu_id' => 'id']);
    }

    public function getEmpresas()
    {
        return $this->hasMany(Empresa::className(), ['emp_id' => 'emp_id'])->viaTable('empresa_user', ['usu_id' => 'id']);
    }

    public function getEmpresaDispositivos()
    {
        return $this->hasMany(EmpresaUserDispositivo::className(), ['usu_id' => 'id']);
    }

    public function getDispositivos()
    {
        return $this->hasMany(Dispositivo::className(), ['dis_id' => 'dis_id'])->viaTable('empresa_user_dispositivo', ['usu_id' => 'id']);
    }

    public function getAuthentications()
    {
        return $this->hasMany(UserAuthentication::className(), ['user_id' => 'id']);
    }

    public function getAuthorizations()
    {
        return $this->hasMany(UserAuthorization::className(), ['user_id' => 'id']);
    }
}
