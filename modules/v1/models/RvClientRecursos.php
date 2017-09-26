<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rv_client_recursos".
 *
 * @property string $id
 * @property integer $cli_eva_id
 * @property string $fic_id
 * @property string $src_id
 * @property string $nombre
 * @property string $creado
 *
 * @property RvClientEvaluacion $cliEva
 * @property RvFicha $fic
 * @property RecursosSources $src
 */
class RvClientRecursos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rv_client_recursos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cli_eva_id', 'fic_id', 'src_id'], 'required'],
            [['cli_eva_id', 'fic_id', 'src_id'], 'integer'],
            [['creado'], 'safe'],
            [['nombre'], 'string', 'max' => 128],
            [['cli_eva_id', 'fic_id', 'src_id'], 'unique', 'targetAttribute' => ['cli_eva_id', 'fic_id', 'src_id'], 'message' => 'The combination of Cli Eva ID, Fic ID and Src ID has already been taken.'],
            [['cli_eva_id'], 'exist', 'skipOnError' => true, 'targetClass' => RvClientEvaluacion::className(), 'targetAttribute' => ['cli_eva_id' => 'id']],
            [['fic_id'], 'exist', 'skipOnError' => true, 'targetClass' => RvFicha::className(), 'targetAttribute' => ['fic_id' => 'fic_id']],
            [['src_id'], 'exist', 'skipOnError' => true, 'targetClass' => RecursosSources::className(), 'targetAttribute' => ['src_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'cli_eva_id' => Yii::t('app', 'Cli Eva ID'),
            'fic_id' => Yii::t('app', 'Fic ID'),
            'src_id' => Yii::t('app', 'Src ID'),
            'nombre' => Yii::t('app', 'Nombre'),
            'creado' => Yii::t('app', 'Creado'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCliEva()
    {
        return $this->hasOne(RvClientEvaluacion::className(), ['id' => 'cli_eva_id']);
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
    public function getSrc()
    {
        return $this->hasOne(RecursosSources::className(), ['id' => 'src_id']);
    }
}
