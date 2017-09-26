<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rv_client_tipo".
 *
 * @property integer $id
 * @property integer $cli_id
 * @property string $nombre
 * @property string $creado
 * @property string $modificado
 *
 * @property RvClientEvaluacion[] $rvClientEvaluacions
 * @property RvEvaluacion[] $evas
 * @property RvClient $cli
 */
class RvClientTipo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rv_client_tipo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cli_id', 'nombre'], 'required'],
            [['cli_id'], 'integer'],
            [['creado', 'modificado'], 'safe'],
            [['nombre'], 'string', 'max' => 128],
            [['cli_id'], 'exist', 'skipOnError' => true, 'targetClass' => RvClient::className(), 'targetAttribute' => ['cli_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'cli_id' => Yii::t('app', 'Cli ID'),
            'nombre' => Yii::t('app', 'Nombre'),
            'creado' => Yii::t('app', 'Creado'),
            'modificado' => Yii::t('app', 'Modificado'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRvClientEvaluacions()
    {
        return $this->hasMany(RvClientEvaluacion::className(), ['clit_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvas()
    {
        return $this->hasMany(RvEvaluacion::className(), ['eva_id' => 'eva_id'])->viaTable('rv_client_evaluacion', ['clit_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCli()
    {
        return $this->hasOne(RvClient::className(), ['id' => 'cli_id']);
    }
}
