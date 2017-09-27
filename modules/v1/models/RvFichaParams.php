<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rv_ficha_params".
 *
 * @property string $id
 * @property string $fic_id
 * @property string $type
 * @property string $content
 * @property string $creado
 * @property string $modificado
 *
 * @property RvFicha $fic
 */
class RvFichaParams extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'rv_ficha_params';
    }

    public function rules()
    {
        return [
            [['fic_id', 'type', 'content'], 'required'],
            [['fic_id'], 'integer'],
            [['type', 'content'], 'string'],
            [['creado', 'modificado'], 'safe'],
            [['fic_id'], 'unique'],
            [['fic_id'], 'exist', 'skipOnError' => true, 'targetClass' => RvFicha::className(), 'targetAttribute' => ['fic_id' => 'fic_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fic_id' => 'Fic ID',
            'type' => 'Type',
            'content' => 'Content',
            'creado' => 'Creado',
            'modificado' => 'Modificado',
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
