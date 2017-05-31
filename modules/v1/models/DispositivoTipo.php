<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "dispositivo_tipo".
 *
 * @property integer $dit_id
 * @property string $nombre
 * @property string $modelo
 * @property string $descripcion
 *
 * @property Dispositivo[] $dispositivos
 */
class DispositivoTipo extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'dispositivo_tipo';
    }


    public function rules()
    {
        return [
            [['nombre'], 'required'],
            [['descripcion'], 'string'],
            [['nombre'], 'string', 'max' => 200],
            [['modelo'], 'string', 'max' => 150],
        ];
    }


    public function attributeLabels()
    {
        return [
            'dit_id' => 'Dit ID',
            'nombre' => 'Nombre',
            'modelo' => 'Modelo',
            'descripcion' => 'Descripcion',
        ];
    }

    public function getDispositivos()
    {
        return $this->hasMany(Dispositivo::className(), ['dit_id' => 'dit_id']);
    }
}
