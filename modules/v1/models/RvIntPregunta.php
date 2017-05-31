<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rv_int_pregunta".
 *
 * @property string $int_id
 * @property string $pre_id
 * @property string $language
 * @property string $descripcion
 * @property string $comentario
 *
 * @property RvPregunta $pre
 */
class RvIntPregunta extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'rv_int_pregunta';
    }


    public function rules()
    {
        return [
            [['pre_id', 'language'], 'required'],
            [['pre_id'], 'integer'],
            [['language', 'descripcion', 'comentario'], 'string'],
            [['language', 'pre_id'], 'unique', 'targetAttribute' => ['language', 'pre_id'], 'message' => 'The combination of Pre ID and Language has already been taken.'],
            [['pre_id'], 'exist', 'skipOnError' => true, 'targetClass' => RvPregunta::className(), 'targetAttribute' => ['pre_id' => 'pre_id']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'int_id' => 'Int ID',
            'pre_id' => 'Pre ID',
            'language' => 'Language',
            'descripcion' => 'Descripcion',
            'comentario' => 'Comentario',
        ];
    }

    public function getPre()
    {
        return $this->hasOne(RvPregunta::className(), ['pre_id' => 'pre_id']);
    }
}
