<?php

namespace app\modules\v1\models;

use Yii;

class RvFichaRecursos extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'rv_ficha_recursos';
    }

    public function rules()
    {
        return [
            [['fic_id', 'src_id', 'tipo'], 'required'],
            [['fic_id', 'src_id'], 'integer'],
            [['tipo'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['fic_id'], 'exist', 'skipOnError' => true, 'targetClass' => RvFicha::className(), 'targetAttribute' => ['fic_id' => 'fic_id']],
            [['src_id'], 'exist', 'skipOnError' => true, 'targetClass' => RecursosSources::className(), 'targetAttribute' => ['src_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fic_id' => 'Fic ID',
            'src_id' => 'Src ID',
            'tipo' => 'Tipo',
            'name' => 'Name',
        ];
    }

    public function getFicha()
    {
        return $this->hasOne(RvFicha::className(), ['fic_id' => 'fic_id']);
    }

    public function getSrc()
    {
        return $this->hasOne(RecursosSources::className(), ['id' => 'src_id']);
    }
}
