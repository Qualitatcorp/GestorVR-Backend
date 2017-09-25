<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rv_client_evaluacion".
 *
 * @property integer $id
 * @property integer $clit_id
 * @property string $eva_id
 * @property string $obligatorio
 * @property double $ponderacion
 * @property string $creado
 *
 * @property RvClientCalificacion[] $rvClientCalificacions
 * @property RvFicha[] $fics
 * @property RvClientTipo $clit
 * @property RvEvaluacion $eva
 * @property RvClientParams[] $rvClientParams
 * @property RvClientRecursos[] $rvClientRecursos
 */
class RvClientEvaluacion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rv_client_evaluacion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['clit_id', 'eva_id'], 'required'],
            [['clit_id', 'eva_id'], 'integer'],
            [['obligatorio'], 'string'],
            [['ponderacion'], 'number'],
            [['creado'], 'safe'],
            [['clit_id', 'eva_id'], 'unique', 'targetAttribute' => ['clit_id', 'eva_id'], 'message' => 'The combination of Clit ID and Eva ID has already been taken.'],
            [['clit_id'], 'exist', 'skipOnError' => true, 'targetClass' => RvClientTipo::className(), 'targetAttribute' => ['clit_id' => 'id']],
            [['eva_id'], 'exist', 'skipOnError' => true, 'targetClass' => RvEvaluacion::className(), 'targetAttribute' => ['eva_id' => 'eva_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'clit_id' => Yii::t('app', 'Clit ID'),
            'eva_id' => Yii::t('app', 'Eva ID'),
            'obligatorio' => Yii::t('app', 'Obligatorio'),
            'ponderacion' => Yii::t('app', 'Ponderacion'),
            'creado' => Yii::t('app', 'Creado'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRvClientCalificacions()
    {
        return $this->hasMany(RvClientCalificacion::className(), ['cli_eva_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFics()
    {
        return $this->hasMany(RvFicha::className(), ['fic_id' => 'fic_id'])->viaTable('rv_client_calificacion', ['cli_eva_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClit()
    {
        return $this->hasOne(RvClientTipo::className(), ['id' => 'clit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEva()
    {
        return $this->hasOne(RvEvaluacion::className(), ['eva_id' => 'eva_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRvClientParams()
    {
        return $this->hasMany(RvClientParams::className(), ['cli_eva_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRvClientRecursos()
    {
        return $this->hasMany(RvClientRecursos::className(), ['cli_eva_id' => 'id']);
    }
}
