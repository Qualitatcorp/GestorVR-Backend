<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rv_int_alternativa".
 *
 * @property string $int_id
 * @property string $alt_id
 * @property string $language
 * @property string $descripcion
 *
 * @property RvAlternativa $alt
 */
class RvIntAlternativa extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'rv_int_alternativa';
    }


    public function rules()
    {
        return [
            [['alt_id', 'language', 'descripcion'], 'required'],
            [['alt_id'], 'integer'],
            [['language', 'descripcion'], 'string'],
            [['language', 'alt_id'], 'unique', 'targetAttribute' => ['language', 'alt_id'], 'message' => 'The combination of Alt ID and Language has already been taken.'],
            [['alt_id'], 'exist', 'skipOnError' => true, 'targetClass' => RvAlternativa::className(), 'targetAttribute' => ['alt_id' => 'alt_id']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'int_id' => 'Int ID',
            'alt_id' => 'Alt ID',
            'language' => 'Language',
            'descripcion' => 'Descripcion',
        ];
    }

    public function getAlternativa()
    {
        return $this->hasOne(RvAlternativa::className(), ['alt_id' => 'alt_id']);
    }
}
