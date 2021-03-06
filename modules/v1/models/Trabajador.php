<?php

namespace app\modules\v1\models;

use Yii;

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
            [['sexo'], 'string'],
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
            'sexo' => 'Sexo',
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

    public function extraFields()
    {
        return ['fichas','src','rs'];
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

    public function getNombreCompleto($sort=true)
    {

        return implode(" ",$sort?array($this->paterno,$this->materno,$this->nombre):array($this->nombre,$this->paterno,$this->materno));
    }

    public function getRs()
    {
        return $this->hasMany(TrabajadorRecursos::className(), ['tra_id' => 'tra_id']);
    }    

    public function getSrc()
    {
        return $this->hasMany(RecursosSources::className(), ['id' => 'src_id'])->via('rs');
    }

    public static function findIdentity($search)
    {
        $model=array();
        if(isset($search['rut'])){
            $model=static::findOne(['rut'=>$search['rut']]);
        }
        else
        {
            $model=static::findOne($search);
        }        
        return $model;
    }
}