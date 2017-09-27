<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rv_evaluacion".
 *
 * @property string $eva_id 
 * @property integer $tev_id 
 * @property string $nombre 
 * @property string $descripcion 
 * @property string $nota '' //nuevo
 * @property string $orden 
 * @property string $reporte '' //nuevo
 * @property string $creado 
 * @property string $habilitado 
 *
 * @property RvClientEvaluacion[] $rvClientEvaluacions
 * @property RvClientTipo[] $clits
 * @property RvTipo $tev
 * @property RvFicha[] $rvFichas
 * @property RvIntEvaluacion[] $rvIntEvaluacions
 * @property RvPregunta[] $rvPreguntas
 */
class RvEvaluacion extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'rv_evaluacion';
    }

    public function rules()
    {
        return [
            [['tev_id', 'nombre'], 'required'],
            [['tev_id'], 'integer'],
            [['nombre', 'descripcion', 'nota', 'orden', 'reporte', 'habilitado'], 'string'],
            [['creado'], 'safe'],
            [['tev_id'], 'exist', 'skipOnError' => true, 'targetClass' => RvTipo::className(), 'targetAttribute' => ['tev_id' => 'tev_id']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'eva_id' => Yii::t('app', 'Eva ID'),
            'tev_id' => Yii::t('app', 'Tev ID'),
            'nombre' => Yii::t('app', 'Nombre'),
            'descripcion' => Yii::t('app', 'Descripcion'),
            'nota' => Yii::t('app', 'Nota'),
            'orden' => Yii::t('app', 'Orden'),
            'reporte' => Yii::t('app', 'Reporte'),
            'creado' => Yii::t('app', 'Creado'),
            'habilitado' => Yii::t('app', 'Habilitado'),
        ];
    }

    public function extraFields()
    {
        return [
            'preguntas',
            'items',
            'alternativas',
            'clients',
            'clientsTipo',
            'clientsEva'
        ];
    }

    public function getClientsEva()
    {
        return $this->hasMany(RvClientEvaluacion::className(), ['eva_id' => 'eva_id']);
    }
   
    public function getClientsTipo()
    {
        return $this->hasMany(RvClientTipo::className(), ['id' => 'clit_id'])->via('clientsEva');
    }

    public function getClients()
    {
        return $this->hasMany(RvClient::className(), ['id' => 'cli_id'])->via('clientsTipo');
    }

    public function getTipo()
    {
        return $this->hasOne(RvTipo::className(), ['tev_id' => 'tev_id']);
    } 
    public function getFichas()
    {
        return $this->hasMany(RvFicha::className(), ['eva_id' => 'eva_id']);
    }

    public function getInternacional()
    {
        return $this->hasMany(RvIntEvaluacion::className(), ['eva_id' => 'eva_id']);
    }
 
    public function getPreguntas()
    {
        return $this->hasMany(RvPregunta::className(), ['eva_id' => 'eva_id']);
    }

    public function getItems()
    {
        return $this->hasMany(RvItem::className(),['ite_id'=>'ite_id'])->via('preguntas');
    }
    
    public function getAlternativas()
    {
        return $this->hasMany(RvAlternativa::className(),['pre_id'=>'pre_id'])->via('preguntas');
    }
}
