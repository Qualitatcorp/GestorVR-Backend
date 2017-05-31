<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rv_int_evaluacion".
 *
 * @property string $int_id
 * @property string $eva_id
 * @property string $language
 * @property string $nombre
 * @property string $descripcion
 *
 * @property RvEvaluacion $eva
 */
class RvIntEvaluacion extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'rv_int_evaluacion';
    }


    public function rules()
    {
        return [
            [['eva_id', 'language'], 'required'],
            [['eva_id'], 'integer'],
            [['language', 'nombre', 'descripcion'], 'string'],
            [['language', 'eva_id'], 'unique', 'targetAttribute' => ['language', 'eva_id'], 'message' => 'The combination of Eva ID and Language has already been taken.'],
            [['eva_id'], 'exist', 'skipOnError' => true, 'targetClass' => RvEvaluacion::className(), 'targetAttribute' => ['eva_id' => 'eva_id']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'int_id' => 'Int ID',
            'eva_id' => 'Eva ID',
            'language' => 'Language',
            'nombre' => 'Nombre',
            'descripcion' => 'Descripcion',
        ];
    }

    public function getEva()
    {
        return $this->hasOne(RvEvaluacion::className(), ['eva_id' => 'eva_id']);
    }
}
