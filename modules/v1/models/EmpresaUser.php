<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "empresa_user".
 *
 * @property string $id
 * @property integer $emp_id
 * @property string $usu_id
 *
 * @property Empresa $emp
 * @property User $usu
 */
class EmpresaUser extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'empresa_user';
    }


    public function rules()
    {
        return [
            [['emp_id', 'usu_id'], 'required'],
            [['emp_id', 'usu_id'], 'integer'],
            [['emp_id', 'usu_id'], 'unique', 'targetAttribute' => ['emp_id', 'usu_id'], 'message' => 'The combination of Emp ID and Usu ID has already been taken.'],
            [['emp_id'], 'exist', 'skipOnError' => true, 'targetClass' => Empresa::className(), 'targetAttribute' => ['emp_id' => 'emp_id']],
            [['usu_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['usu_id' => 'id']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'emp_id' => 'Emp ID',
            'usu_id' => 'Usu ID',
        ];
    }

    public function getEmp()
    {
        return $this->hasOne(Empresa::className(), ['emp_id' => 'emp_id']);
    }

    public function getUsu()
    {
        return $this->hasOne(User::className(), ['id' => 'usu_id']);
    }
}
