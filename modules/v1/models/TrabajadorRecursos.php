<?php

namespace app\modules\v1\models;

use Yii;

class TrabajadorRecursos extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'trabajador_recursos';
    }

    public function rules()
    {
        return [
            [['tra_id', 'src_id', 'tipo'], 'required'],
            [['tra_id', 'src_id'], 'integer'],
            [['tipo'], 'string'],
            [['creado'], 'safe'],
            [['tra_id'], 'exist', 'skipOnError' => true, 'targetClass' => Trabajador::className(), 'targetAttribute' => ['tra_id' => 'tra_id']],
            [['src_id'], 'exist', 'skipOnError' => true, 'targetClass' => RecursosSources::className(), 'targetAttribute' => ['src_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tra_id' => 'Tra ID',
            'src_id' => 'Src ID',
            'tipo' => 'Tipo',
            'creado' => 'Creado',
        ];
    }

    public function getTrabajador()
    {
        return $this->hasOne(Trabajador::className(), ['tra_id' => 'tra_id']);
    }

    public function getSrc()
    {
        return $this->hasOne(RecursosSources::className(), ['id' => 'src_id']);
    }
}
