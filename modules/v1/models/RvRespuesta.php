<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rv_respuesta".
 *
 * @property string $res_id
 * @property string $alt_id
 * @property string $fic_id
 * @property string $creado
 *
 * @property RvAlternativa $alt
 * @property RvFicha $fic
 */
class RvRespuesta extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'rv_respuesta';
    }


    public function rules()
    {
        return [
            [['alt_id', 'fic_id'], 'required'],
            [['alt_id', 'fic_id'], 'integer'],
            [['creado'], 'safe'],
            [['alt_id'], 'exist', 'skipOnError' => true, 'targetClass' => RvAlternativa::className(), 'targetAttribute' => ['alt_id' => 'alt_id']],
            [['fic_id'], 'exist', 'skipOnError' => true, 'targetClass' => RvFicha::className(), 'targetAttribute' => ['fic_id' => 'fic_id']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'res_id' => 'Res ID',
            'alt_id' => 'Alt ID',
            'fic_id' => 'Fic ID',
            'creado' => 'Creado',
        ];
    }

    public function getAlternativa()
    {
        return $this->hasOne(RvAlternativa::className(), ['alt_id' => 'alt_id']);
    }

    public function getFicha()
    {
        return $this->hasOne(RvFicha::className(), ['fic_id' => 'fic_id']);
    }
}
