<?php

namespace app\modules\v1\models;

class AnalitycsAppParams extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'analitycs_app_params';
    }

    public function rules()
    {
        return [
            [['app_id', 'name', 'type', 'content'], 'required'],
            [['app_id'], 'integer'],
            [['type', 'content'], 'string'],
            [['creado', 'modificado'], 'safe'],
            [['name', 'className'], 'string', 'max' => 128],
            [['app_id', 'name'], 'unique', 'targetAttribute' => ['app_id', 'name'], 'message' => 'The combination of App ID and Name has already been taken.'],
            [['app_id'], 'exist', 'skipOnError' => true, 'targetClass' => AnalitycsApp::className(), 'targetAttribute' => ['app_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'app_id' => 'App ID',
            'name' => 'Name',
            'className' => 'ClassName',
            'type' => 'Type',
            'content' => 'Content',
            'creado' => 'Creado',
            'modificado' => 'Modificado',
        ];
    }

    public function fields()
    {
        return [
            'modificado'
        ];
    }

    public function extraFields()
    {
        return [
            'creado',
            'id',
            'app_id',
            'name',
            'className',
            'type',
            'content',
            'data'
        ];
    }

    public function getApp()
    {
        return $this->hasOne(AnalitycsApp::className(), ['id' => 'app_id']);
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