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
        return ['dipositivo','escenas','system','eventos','objetos','posiciones'];
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
}
