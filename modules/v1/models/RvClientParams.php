<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rv_client_params".
 *
 * @property string $id
 * @property string $fic_id
 * @property integer $cli_eva_id
 * @property string $type
 * @property string $content
 * @property string $creado
 * @property string $modificado
 *
 * @property RvFicha $fic
 * @property RvClientEvaluacion $cliEva
 */
class RvClientParams extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rv_client_params';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fic_id', 'cli_eva_id', 'type', 'content'], 'required'],
            [['fic_id', 'cli_eva_id'], 'integer'],
            [['type', 'content'], 'string'],
            [['creado', 'modificado'], 'safe'],
            [['fic_id'], 'unique'],
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
            'type' => Yii::t('app', 'Type'),
            'content' => Yii::t('app', 'Content'),
            'creado' => Yii::t('app', 'Creado'),
            'modificado' => Yii::t('app', 'Modificado'),
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
