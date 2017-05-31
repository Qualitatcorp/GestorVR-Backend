<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rv_evaluacion".
 *
 * @property string $eva_id
 * @property integer $tev_id
 * @property string $nombre
 * @property string $descripcion
 * @property string $orden
 * @property string $creado
 * @property string $habilitado
 *
 * @property RvTipo $tev
 * @property RvFicha[] $rvFichas
 * @property RvIntEvaluacion[] $rvIntEvaluacions
 * @property RvPregunta[] $rvPreguntas
 */
class RvEvaluacion extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'rv_evaluacion';
    }


    public function rules()
    {
        return [
            [['tev_id', 'nombre'], 'required'],
            [['tev_id'], 'integer'],
            [['nombre', 'descripcion', 'orden', 'habilitado'], 'string'],
            [['creado'], 'safe'],
            [['tev_id'], 'exist', 'skipOnError' => true, 'targetClass' => RvTipo::className(), 'targetAttribute' => ['tev_id' => 'tev_id']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'eva_id' => 'Eva ID',
            'tev_id' => 'Tev ID',
            'nombre' => 'Nombre',
            'descripcion' => 'Descripcion',
            'orden' => 'Orden',
            'creado' => 'Creado',
            'habilitado' => 'Habilitado',
        ];
    }

    public function getTev()
    {
        return $this->hasOne(RvTipo::className(), ['tev_id' => 'tev_id']);
    }

    public function getRvFichas()
    {
        return $this->hasMany(RvFicha::className(), ['eva_id' => 'eva_id']);
    }

    public function getRvIntEvaluacions()
    {
        return $this->hasMany(RvIntEvaluacion::className(), ['eva_id' => 'eva_id']);
    }

    public function getRvPreguntas()
    {
        return $this->hasMany(RvPregunta::className(), ['eva_id' => 'eva_id']);
    }
}
