<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rv_client_calificacion".
 *
 * @property string $id
 * @property string $fic_id
 * @property integer $cli_eva_id
 * @property double $calificacion
 * @property string $creado
 *
 * @property RvFicha $fic
 * @property RvClientEvaluacion $cliEva
 */
class RvClientCalificacion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rv_client_calificacion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fic_id', 'cli_eva_id', 'calificacion'], 'required'],
            [['fic_id', 'cli_eva_id'], 'integer'],
            [['calificacion'], 'number'],
            [['creado'], 'safe'],
            [['fic_id', 'cli_eva_id'], 'unique', 'targetAttribute' => ['fic_id', 'cli_eva_id'], 'message' => 'The combination of Fic ID and Cli Eva ID has already been taken.'],
            [['fic_id'], 'exist', 'skipOnError' => true, 'targetClass' => RvFicha::className(), 'targetAttribute' => ['fic_id' => 'fic_id']],
            [['cli_eva_id'], 'exist', 'skipOnError' => true, 'targetClass' => RvClientEvaluacion::className(), 'targetAttribute' => ['cli_eva_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'fic_id' => Yii::t('app', 'Fic ID'),
            'cli_eva_id' => Yii::t('app', 'Cli Eva ID'),
            'calificacion' => Yii::t('app', 'Calificacion'),
            'creado' => Yii::t('app', 'Creado'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFic()
    {
        return $this->hasOne(RvFicha::className(), ['fic_id' => 'fic_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCliEva()
    {
        return $this->hasOne(RvClientEvaluacion::className(), ['id' => 'cli_eva_id']);
    }
}
