<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rv_ficha".
 *
 * @property string $fic_id
 * @property string $eva_id
 * @property string $trab_id
 * @property string $pro_id
 * @property string $disp_id
 * @property string $calificacion
 * @property integer $pais_id
 * @property string $creado
 *
 * @property RvEvaluacion $eva
 * @property Trabajador $trab
 * @property RvProyecto $pro
 * @property Dispositivo $disp
 * @property Pais $pais
 * @property RvRespuesta[] $rvRespuestas
 */
class RvFicha extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'rv_ficha';
    }


    public function rules()
    {
        return [
            [['eva_id', 'trab_id', 'disp_id'], 'required'],
            [['eva_id', 'trab_id', 'pro_id', 'disp_id', 'pais_id'], 'integer'],
            [['calificacion'], 'number'],
            [['creado'], 'safe'],
            [['eva_id'], 'exist', 'skipOnError' => true, 'targetClass' => RvEvaluacion::className(), 'targetAttribute' => ['eva_id' => 'eva_id']],
            [['trab_id'], 'exist', 'skipOnError' => true, 'targetClass' => Trabajador::className(), 'targetAttribute' => ['trab_id' => 'tra_id']],
            [['pro_id'], 'exist', 'skipOnError' => true, 'targetClass' => RvProyecto::className(), 'targetAttribute' => ['pro_id' => 'pro_id']],
            [['disp_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dispositivo::className(), 'targetAttribute' => ['disp_id' => 'dis_id']],
            [['pais_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pais::className(), 'targetAttribute' => ['pais_id' => 'pais_id']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'fic_id' => 'Fic ID',
            'eva_id' => 'Eva ID',
            'trab_id' => 'Trab ID',
            'pro_id' => 'Pro ID',
            'disp_id' => 'Disp ID',
            'calificacion' => 'Calificacion',
            'pais_id' => 'Pais ID',
            'creado' => 'Creado',
        ];
    }

    public function extraFields()
    {
        return [
            'trabajador',
            'proyecto',
            'evaluacion',
            'proyecto',
            'dispositivo',
            'pais',
            'respuestas'
        ];
    }

    public function getEvaluacion()
    {
        return $this->hasOne(RvEvaluacion::className(), ['eva_id' => 'eva_id']);
    }

    public function getTrabajador()
    {
        return $this->hasOne(Trabajador::className(), ['tra_id' => 'trab_id']);
    }

    public function getProyecto()
    {
        return $this->hasOne(RvProyecto::className(), ['pro_id' => 'pro_id']);
    }

    public function getDispositivo()
    {
        return $this->hasOne(Dispositivo::className(), ['dis_id' => 'disp_id']);
    }

    public function getPais()
    {
        return $this->hasOne(Pais::className(), ['pais_id' => 'pais_id']);
    }

    public function getRespuestas()
    {
        return $this->hasMany(RvRespuesta::className(), ['fic_id' => 'fic_id']);
    }
}
