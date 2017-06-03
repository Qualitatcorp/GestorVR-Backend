<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rv_pregunta".
 *
 * @property string $pre_id
 * @property string $eva_id
 * @property integer $ite_id
 * @property string $descripcion
 * @property string $comentario
 * @property string $imagen
 * @property string $creado
 * @property string $modificado
 * @property string $habilitado
 *
 * @property RvAlternativa[] $rvAlternativas
 * @property RvIntPregunta[] $rvIntPreguntas
 * @property RvEvaluacion $eva
 * @property RvItem $ite
 */
class RvPregunta extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'rv_pregunta';
    }

    public function rules()
    {
        return [
            [['eva_id', 'descripcion'], 'required'],
            [['eva_id', 'ite_id'], 'integer'],
            [['descripcion', 'comentario', 'imagen', 'habilitado'], 'string'],
            [['creado', 'modificado'], 'safe'],
            [['eva_id'], 'exist', 'skipOnError' => true, 'targetClass' => RvEvaluacion::className(), 'targetAttribute' => ['eva_id' => 'eva_id']],
            [['ite_id'], 'exist', 'skipOnError' => true, 'targetClass' => RvItem::className(), 'targetAttribute' => ['ite_id' => 'ite_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'pre_id' => 'Pre ID',
            'eva_id' => 'Eva ID',
            'ite_id' => 'Ite ID',
            'descripcion' => 'Descripcion',
            'comentario' => 'Comentario',
            'imagen' => 'Imagen',
            'creado' => 'Creado',
            'modificado' => 'Modificado',
            'habilitado' => 'Habilitado',
        ];
    }

    public function getAlternativas()
    {
        return $this->hasMany(RvAlternativa::className(), ['pre_id' => 'pre_id']);
    }

    public function getIntPreguntas()
    {
        return $this->hasMany(RvIntPregunta::className(), ['pre_id' => 'pre_id']);
    }

    public function getEvaluacion()
    {
        return $this->hasOne(RvEvaluacion::className(), ['eva_id' => 'eva_id']);
    }

    public function getItem()
    {
        return $this->hasOne(RvItem::className(), ['ite_id' => 'ite_id']);
    }
}
