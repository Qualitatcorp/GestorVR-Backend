<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "licencia_tipo".
 *
 * @property string $lit_id
 * @property string $nombre
 * @property string $descripcion
 * @property integer $cantidad
 * @property string $disponible
 *
 * @property Licencia[] $licencias
 */
class LicenciaTipo extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'licencia_tipo';
    }


    public function rules()
    {
        return [
            [['nombre', 'cantidad'], 'required'],
            [['nombre', 'descripcion', 'disponible'], 'string'],
            [['cantidad'], 'integer'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'lit_id' => 'Lit ID',
            'nombre' => 'Nombre',
            'descripcion' => 'Descripcion',
            'cantidad' => 'Cantidad',
            'disponible' => 'Disponible',
        ];
    }

    public function getLicencias()
    {
        return $this->hasMany(Licencia::className(), ['lit_id' => 'lit_id']);
    }
}
