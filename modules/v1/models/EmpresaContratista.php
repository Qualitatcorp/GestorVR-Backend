<?php

namespace app\modules\v1\models;

use Yii;

class EmpresaContratista extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'empresa_contratista';
    }

    public function rules()
    {
        return [
            [['com_id'], 'integer'],
            [['razon_social', 'nombre_corto'], 'required'],
            [['creado', 'modificado'], 'safe'],
            [['rut'], 'string', 'max' => 12],
            [['razon_social', 'nombre_corto', 'mail'], 'string', 'max' => 127],
            [['direccion', 'web'], 'string', 'max' => 255],
            [['fono'], 'string', 'max' => 64],
            [['rut'], 'unique'],
            [['com_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comuna::className(), 'targetAttribute' => ['com_id' => 'com_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rut' => 'Rut',
            'com_id' => 'Com ID',
            'razon_social' => 'Razon Social',
            'nombre_corto' => 'Nombre Corto',
            'direccion' => 'Direccion',
            'fono' => 'Fono',
            'mail' => 'Mail',
            'web' => 'Web',
            'creado' => 'Creado',
            'modificado' => 'Modificado',
        ];
    }

    public function fields()
    {
        return [
            'id',
            'rut',
            'razon_social',
            'nombre_corto',
        ];
    }

    public function extraFields()
    {
        return [
            'com_id',
            'direccion',
            'fono',
            'mail',
            'web',
            'creado',
            'modificado',
            'comuna',
            'fichas'
        ];
    }

    public function getComuna()
    {
        return $this->hasOne(Comuna::className(), ['com_id' => 'com_id']);
    }

    public function getFichas()
    {
        return $this->hasMany(RvFicha::className(), ['con_id' => 'id']);
    }
}
