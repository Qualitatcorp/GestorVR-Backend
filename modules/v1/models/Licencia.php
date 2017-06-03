<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "licencia".
 *
 * @property integer $lic_id
 * @property integer $emp_id
 * @property string $lit_id
 * @property string $descripcion
 * @property string $creado
 * @property string $modificado
 * @property integer $cantidad
 *
 * @property Empresa $emp
 * @property LicenciaTipo $lit
 * @property LicenciaRegistro[] $licenciaRegistros
 */
class Licencia extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'licencia';
    }


    public function rules()
    {
        return [
            [['emp_id', 'descripcion'], 'required'],
            [['emp_id', 'lit_id', 'cantidad'], 'integer'],
            [['descripcion'], 'string'],
            [['creado', 'modificado'], 'safe'],
            [['emp_id'], 'exist', 'skipOnError' => true, 'targetClass' => Empresa::className(), 'targetAttribute' => ['emp_id' => 'emp_id']],
            [['lit_id'], 'exist', 'skipOnError' => true, 'targetClass' => LicenciaTipo::className(), 'targetAttribute' => ['lit_id' => 'lit_id']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'lic_id' => 'Lic ID',
            'emp_id' => 'Emp ID',
            'lit_id' => 'Lit ID',
            'descripcion' => 'Descripcion',
            'creado' => 'Creado',
            'modificado' => 'Modificado',
            'cantidad' => 'Cantidad',
        ];
    }

    public function getEmpresa()
    {
        return $this->hasOne(Empresa::className(), ['emp_id' => 'emp_id']);
    }

    public function getTipo()
    {
        return $this->hasOne(LicenciaTipo::className(), ['lit_id' => 'lit_id']);
    }

    public function getRegistros()
    {
        return $this->hasMany(LicenciaRegistro::className(), ['lic_id' => 'lic_id']);
    }
}
