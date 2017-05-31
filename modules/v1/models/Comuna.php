<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "comuna".
 *
 * @property integer $com_id
 * @property string $com_nombre
 * @property string $reg_nombre
 *
 * @property Empresa[] $empresas
 */
class Comuna extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'comuna';
    }


    public function rules()
    {
        return [
            [['com_id', 'com_nombre', 'reg_nombre'], 'required'],
            [['com_id'], 'integer'],
            [['reg_nombre'], 'string'],
            [['com_nombre'], 'string', 'max' => 150],
        ];
    }


    public function attributeLabels()
    {
        return [
            'com_id' => 'Com ID',
            'com_nombre' => 'Com Nombre',
            'reg_nombre' => 'Reg Nombre',
        ];
    }

    public function getEmpresas()
    {
        return $this->hasMany(Empresa::className(), ['com_id' => 'com_id']);
    }
}
