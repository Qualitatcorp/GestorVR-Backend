<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "analitycs_dispositivo".
 *
 * @property string $id
 * @property string $model
 * @property string $name
 * @property string $type
 * @property string $keycode
 *
 * @property AnalitycsBitacora[] $analitycsBitacoras
 */
class AnalitycsDispositivo extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'analitycs_dispositivo';
    }


    public function rules()
    {
        return [
            [['model', 'type', 'keycode'], 'required'],
            [['type'], 'string'],
            [['model', 'name', 'keycode'], 'string', 'max' => 128],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model' => 'Model',
            'name' => 'Name',
            'type' => 'Type',
            'keycode' => 'Keycode',
        ];
    }

    public function extraFields()
    {
        return ['bitacoras'];
    }

    public function getBitacoras()
    {
        return $this->hasMany(AnalitycsBitacora::className(), ['dis_id' => 'id']);
    }
}
