<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rv_client".
 *
 * @property integer $id
 * @property string $nombre
 * @property string $creado
 *
 * @property RvClientTipo[] $rvClientTipos
 */
class RvClient extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rv_client';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre'], 'required'],
            [['creado'], 'safe'],
            [['nombre'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'nombre' => Yii::t('app', 'Nombre'),
            'creado' => Yii::t('app', 'Creado'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRvClientTipos()
    {
        return $this->hasMany(RvClientTipo::className(), ['cli_id' => 'id']);
    }
}
