<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "trabajador".
 *
 * @property string $tra_id
 * @property string $rut
 * @property string $nombre
 * @property string $paterno
 * @property string $materno
 * @property string $nacimiento
 * @property string $fono
 * @property string $mail
 * @property string $gerencia
 * @property string $cargo
 * @property integer $antiguedad
 * @property string $estado_civil
 * @property integer $hijos
 * @property string $creacion
 * @property string $modificado
 *
 * @property RvFicha[] $rvFichas
 */
class Trabajador extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'trabajador';
    }


    public function rules()
    {
        return [
            [['nacimiento', 'creacion', 'modificado'], 'safe'],
            [['antiguedad', 'hijos'], 'integer'],
            [['rut'], 'string', 'max' => 255],
            [['nombre'], 'string', 'max' => 150],
            [['paterno', 'materno'], 'string', 'max' => 100],
            [['fono'], 'string', 'max' => 50],
            [['mail', 'gerencia', 'cargo'], 'string', 'max' => 128],
            [['estado_civil'], 'string', 'max' => 64],
            [['rut'], 'unique'],
            [['mail'], 'unique'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'tra_id' => 'Tra ID',
            'rut' => 'Rut',
            'nombre' => 'Nombre',
            'paterno' => 'Paterno',
            'materno' => 'Materno',
            'nacimiento' => 'Nacimiento',
            'fono' => 'Fono',
            'mail' => 'Mail',
            'gerencia' => 'Gerencia',
            'cargo' => 'Cargo',
            'antiguedad' => 'Antiguedad',
            'estado_civil' => 'Estado Civil',
            'hijos' => 'Hijos',
            'creacion' => 'Creacion',
            'modificado' => 'Modificado',
        ];
    }

    public function getFichas()
    {
        return $this->hasMany(RvFicha::className(), ['trab_id' => 'tra_id']);
    }

    public function getNombreCompleto()
    {
        return implode(" ", array($this->paterno,$this->materno,$this->nombre));
    }
}
