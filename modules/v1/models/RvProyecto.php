<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rv_proyecto".
 *
 * @property string $pro_id
 * @property string $nombre
 * @property string $descripcion
 *
 * @property RvFicha[] $rvFichas
 */
class RvProyecto extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'rv_proyecto';
    }


    public function rules()
    {
        return [
            [['nombre'], 'required'],
            [['descripcion'], 'string'],
            [['nombre'], 'string', 'max' => 300],
        ];
    }


    public function attributeLabels()
    {
        return [
            'pro_id' => 'Pro ID',
            'nombre' => 'Nombre',
            'descripcion' => 'Descripcion',
        ];
    }

    public function getRvFichas()
    {
        return $this->hasMany(RvFicha::className(), ['pro_id' => 'pro_id']);
    }
}
