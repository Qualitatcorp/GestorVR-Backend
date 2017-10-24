<?php

namespace app\modules\v1\models;

use Yii;

class RvClientParams extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'rv_client_params';
    }

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

    public function fields()
    {
        $fields = parent::fields();
        unset($fields['content']);
        $fields[]='data';
        return $fields;
    }

    public function getFicha()
    {
        return $this->hasOne(RvFicha::className(), ['fic_id' => 'fic_id']);
    }

    public function getCliEva()
    {
        return $this->hasOne(RvClientEvaluacion::className(), ['id' => 'cli_eva_id']);
    }

    public function getData()
    {
        switch ($this->type) {
            case 'array':
                return json_decode($this->content,true);
            case 'object':
                return json_decode($this->content);
            default:
                $valor=$this->content;
                if(settype($valor,$this->type)){
                    return $valor;
                }else{
                    return null;
                }
        }
    }

    public function setData($val)
    {
        $this->type=gettype($val);
        switch ($this->type) {
            case 'array':
            case 'object':
                $this->content=json_encode($val);
                break;
            default:
                settype($val, "string");
                $this->content=$val;
                break;
        }
    }
}
