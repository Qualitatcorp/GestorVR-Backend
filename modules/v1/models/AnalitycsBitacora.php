<?php

namespace app\modules\v1\models;

use Yii;

class AnalitycsBitacora extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'analitycs_bitacora';
    }

    public function rules()
    {
        return [
            [['sys_id', 'dis_id', 'sce_id', 'tiempo'], 'required'],
            [['sys_id', 'dis_id', 'sce_id'], 'integer'],
            [['tiempo'], 'number'],
            [['creado', 'modificado'], 'safe'],
            [['dis_id'], 'exist', 'skipOnError' => true, 'targetClass' => AnalitycsDispositivo::className(), 'targetAttribute' => ['dis_id' => 'id']],
            [['sce_id'], 'exist', 'skipOnError' => true, 'targetClass' => AnalitycsEscena::className(), 'targetAttribute' => ['sce_id' => 'id']],
            [['sys_id'], 'exist', 'skipOnError' => true, 'targetClass' => AnalitycsSystem::className(), 'targetAttribute' => ['sys_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sys_id' => 'Sys ID',
            'dis_id' => 'Dis ID',
            'sce_id' => 'Sce ID',
            'tiempo' => 'Tiempo',
            'creado' => 'Creado',
            'modificado' => 'Modificado',
        ];
    }

    public function extraFields()
    {
        return ['app','dipositivo','escena','system','eventos','objetos','posiciones','trabajadores','empresas','bhe','bht'];
    }

    public function getDispositivo()
    {
        return $this->hasOne(AnalitycsDispositivo::className(), ['id' => 'dis_id']);
    }

    public function getEscena()
    {
        return $this->hasOne(AnalitycsEscena::className(), ['id' => 'sce_id']);
    }

    public function getSystem()
    {
        return $this->hasOne(AnalitycsSystem::className(), ['id' => 'sys_id']);
    }

    public function getBhe()
    {
        return $this->hasMany(AnalitycsBitacoraEmpresa::className(), ['bit_id' => 'id']);
    }

    public function getEmpresas()
    {
        return $this->hasMany(Empresa::className(), ['emp_id' => 'emp_id'])->via('bhe');
    }

    public function getEventos()
    {
        return $this->hasMany(AnalitycsBitacoraEvento::className(), ['bit_id' => 'id']);
    }

    public function getObjetos()
    {
        return $this->hasMany(AnalitycsBitacoraObjeto::className(), ['bit_id' => 'id']);
    }

    public function getPosiciones()
    {
        return $this->hasMany(AnalitycsBitacoraPosicion::className(), ['bit_id' => 'id']);
    }

    public function getBht()
    {
        return $this->hasMany(AnalitycsBitacoraTrabajador::className(), ['bit_id' => 'id']);
    }

    public function getTrabajadores()
    {
        return $this->hasMany(Trabajador::className(), ['tra_id' => 'tra_id'])->via('bht');
    }

    public function getApp()
    {
        return $this->hasOne(AnalitycsApp::className(), ['id' => 'app_id'])->via('escena');
    }
}
