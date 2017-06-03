<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rv_item".
 *
 * @property integer $ite_id
 * @property string $nombre
 *
 * @property RvPregunta[] $rvPreguntas
 */
class RvItem extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'rv_item';
    }


    public function rules()
    {
        return [
            [['nombre'], 'required'],
            [['nombre'], 'string', 'max' => 150],
        ];
    }


    public function attributeLabels()
    {
        return [
            'ite_id' => 'Ite ID',
            'nombre' => 'Nombre',
        ];
    }

    public function getPreguntas()
    {
        return $this->hasMany(RvPregunta::className(), ['ite_id' => 'ite_id']);
    }
}
