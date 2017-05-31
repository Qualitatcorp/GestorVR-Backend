<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rv_alternativa".
 *
 * @property string $alt_id
 * @property string $pre_id
 * @property string $alternativa
 * @property string $descripcion
 * @property integer $ponderacion
 * @property string $correcta
 *
 * @property RvPregunta $pre
 * @property RvIntAlternativa[] $rvIntAlternativas
 * @property RvRespuesta[] $rvRespuestas
 */
class RvAlternativa extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'rv_alternativa';
    }


    public function rules()
    {
        return [
            [['pre_id', 'alternativa', 'ponderacion', 'correcta'], 'required'],
            [['pre_id', 'ponderacion'], 'integer'],
            [['descripcion', 'correcta'], 'string'],
            [['alternativa'], 'string', 'max' => 10],
            [['pre_id'], 'exist', 'skipOnError' => true, 'targetClass' => RvPregunta::className(), 'targetAttribute' => ['pre_id' => 'pre_id']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'alt_id' => 'Alt ID',
            'pre_id' => 'Pre ID',
            'alternativa' => 'Alternativa',
            'descripcion' => 'Descripcion',
            'ponderacion' => 'Ponderacion',
            'correcta' => 'Correcta',
        ];
    }

    public function getPregunta()
    {
        return $this->hasOne(RvPregunta::className(), ['pre_id' => 'pre_id']);
    }

    public function getRvIntAlternativas()
    {
        return $this->hasMany(RvIntAlternativa::className(), ['alt_id' => 'alt_id']);
    }

    public function getRespuestas()
    {
        return $this->hasMany(RvRespuesta::className(), ['alt_id' => 'alt_id']);
    }
}
