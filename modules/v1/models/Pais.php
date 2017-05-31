<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pais".
 *
 * @property integer $pais_id
 * @property string $codigo
 * @property string $nombre
 *
 * @property RvFicha[] $rvFichas
 */
class Pais extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'pais';
    }


    public function rules()
    {
        return [
            [['codigo'], 'required'],
            [['codigo'], 'string', 'max' => 3],
            [['nombre'], 'string', 'max' => 50],
            [['codigo'], 'unique'],
            [['nombre'], 'unique'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'pais_id' => 'Pais ID',
            'codigo' => 'Codigo',
            'nombre' => 'Nombre',
        ];
    }

    public function getRvFichas()
    {
        return $this->hasMany(RvFicha::className(), ['pais_id' => 'pais_id']);
    }
}
