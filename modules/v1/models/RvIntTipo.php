<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rv_int_tipo".
 *
 * @property string $int_id
 * @property integer $tev_id
 * @property string $language
 * @property string $nombre
 * @property string $descripcion
 *
 * @property RvTipo $tev
 */
class RvIntTipo extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'rv_int_tipo';
    }


    public function rules()
    {
        return [
            [['tev_id', 'language'], 'required'],
            [['tev_id'], 'integer'],
            [['language', 'nombre', 'descripcion'], 'string'],
            [['language', 'tev_id'], 'unique', 'targetAttribute' => ['language', 'tev_id'], 'message' => 'The combination of Tev ID and Language has already been taken.'],
            [['tev_id'], 'exist', 'skipOnError' => true, 'targetClass' => RvTipo::className(), 'targetAttribute' => ['tev_id' => 'tev_id']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'int_id' => 'Int ID',
            'tev_id' => 'Tev ID',
            'language' => 'Language',
            'nombre' => 'Nombre',
            'descripcion' => 'Descripcion',
        ];
    }

    public function getTev()
    {
        return $this->hasOne(RvTipo::className(), ['tev_id' => 'tev_id']);
    }
}
