<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "licencia_registro".
 *
 * @property integer $lig_id
 * @property integer $lic_id
 * @property integer $iduser
 * @property integer $cantidad
 * @property string $tipo
 * @property string $descripcion
 * @property string $habilitado
 *
 * @property Licencia $lic
 * @property UsuarioUser $iduser0
 */
class LicenciaRegistro extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'licencia_registro';
    }


    public function rules()
    {
        return [
            [['lic_id', 'iduser', 'descripcion'], 'required'],
            [['lic_id', 'iduser', 'cantidad'], 'integer'],
            [['tipo', 'descripcion', 'habilitado'], 'string'],
            [['lic_id'], 'exist', 'skipOnError' => true, 'targetClass' => Licencia::className(), 'targetAttribute' => ['lic_id' => 'lic_id']],
            [['iduser'], 'exist', 'skipOnError' => true, 'targetClass' => UsuarioUser::className(), 'targetAttribute' => ['iduser' => 'iduser']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'lig_id' => 'Lig ID',
            'lic_id' => 'Lic ID',
            'iduser' => 'Iduser',
            'cantidad' => 'Cantidad',
            'tipo' => 'Tipo',
            'descripcion' => 'Descripcion',
            'habilitado' => 'Habilitado',
        ];
    }

    public function getLicencia()
    {
        return $this->hasOne(Licencia::className(), ['lic_id' => 'lic_id']);
    }

    public function getUsuario()
    {
        return $this->hasOne(UsuarioUser::className(), ['iduser' => 'iduser']);
    }
}
