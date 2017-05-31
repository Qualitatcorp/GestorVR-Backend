<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rv_tipo".
 *
 * @property integer $tev_id
 * @property string $nombre
 * @property string $descripcion
 * @property string $activo
 *
 * @property RvEvaluacion[] $rvEvaluacions
 * @property RvIntTipo[] $rvIntTipos
 */
class RvTipo extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'rv_tipo';
    }


    public function rules()
    {
        return [
            [['nombre'], 'required'],
            [['descripcion', 'activo'], 'string'],
            [['nombre'], 'string', 'max' => 250],
        ];
    }


    public function attributeLabels()
    {
        return [
            'tev_id' => 'Tev ID',
            'nombre' => 'Nombre',
            'descripcion' => 'Descripcion',
            'activo' => 'Activo',
        ];
    }

    public function getRvEvaluacion()
    {
        return $this->hasMany(RvEvaluacion::className(), ['tev_id' => 'tev_id']);
    }

    public function getRvIntTipo()
    {
        return $this->hasMany(RvIntTipo::className(), ['tev_id' => 'tev_id']);
    }
}
