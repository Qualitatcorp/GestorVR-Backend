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

    public static function validaRUT($rut)
    {
        $rut = preg_replace('/[^k0-9]/i', '', $rut);
        $dv  = substr($rut, -1);
        $numero = substr($rut, 0, strlen($rut)-1);
        $i = 2;
        $suma = 0;
        foreach(array_reverse(str_split($numero)) as $v)
        {
            if($i==8)
                $i = 2;
            $suma += $v * $i;
            ++$i;
        }
        $dvr = 11 - ($suma % 11);
        
        if($dvr == 11)
            $dvr = 0;
        if($dvr == 10)
            $dvr = 'K';
        if($dvr == strtoupper($dv))
            return true;
        else
            return false;
    }

    public static function formatRUT( $rut ) {
        $rut = preg_replace('/[^k0-9]/i', '', $rut);
        return number_format( substr ( $rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $rut, strlen($rut) -1 , 1 );
    }

    public function isRUT() {
        if(static::validaRUT($this->rut)){
            $this->rut = static::formatRUT($this->rut);
        }
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
