<?php

namespace app\modules\v1\models;

use Yii;

class RecursosSources extends \yii\db\ActiveRecord
{
    public $file;

    public static function tableName()
    {
        return 'recursos_sources';
    }

    public function rules()
    {
        return [
            [['src', 'type'], 'required'],
            [['type'], 'string'],
            [['src', 'title'], 'string', 'max' => 255],
            [['src'], 'unique'],
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'apk,txt,htm,html,php,css,js,json,xml,swf,flv,png,jpe,jpeg,jpg,gif,bmp,ico,tiff,tif,svg,svgz,zip,rar,exe,msi,cab,mp3,qt,mov,pdf,psd,ai,eps,ps,doc,docx,rtf,xls,xlsx,ppt,pptx,odt,ods'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'src' => 'Src',
            'type' => 'Type',
            'title' => 'Title',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();
        unset($fields['src']);
        $fields[]='url';
        $fields[]='dir';
        return $fields;
    }

    public function getTrabajadorRecursos()
    {
        return $this->hasMany(TrabajadorRecursos::className(), ['src_id' => 'id']);
    }

    public function getUrl()
    {
        return Yii::$app->params['baseUrlFront'].$this->src;
    }

    public function getDir()
    {
        return Yii::$app->params['baseDirFront'].$this->src;
    }

    // public function upload()
    // {
    //     $file = $this->file[0]; //el parametro esta definido para recibir mas de un archivo, pero trabajaremos con uno 
    //     $this->src = \Yii::$app->security->generateRandomString(); 
    //     $this->type =  $file->type;
    //     $this->title = $file->baseName.'.'.$file->extension;
    //     if($this->save()){
    //         $nombre = $this->id.'-'.\Yii::$app->security->generateRandomString(). '.' . $file->extension;
    //         $ruta_guardado  = '../..'.\Yii::$app->params['url_frontend'].'/src/' . $nombre;
    //         $src = \Yii::$app->params['url_frontend'].'/src/' . $nombre;
    //         $file->saveAs($ruta_guardado);
    //         $this->src = $src;
    //         $this->save();
    //         return true;
    //     }else{
    //         return false;
    //     }
    // }

    public function beforeValidate()
    {
        $this->file=\yii\web\UploadedFile::getInstanceByName('file');
        if($this->file!==null){
            $this->title = $this->file->baseName.'.'.$this->file->extension;
            $this->type =  $this->file->type;
            $this->src = \Yii::$app->security->generateRandomString().'.'.$this->file->extension;
        }
        return parent::beforeValidate();
    }

    public function afterValidate()
    {
        if(!$this->hasErrors()&&$this->file!==null)
        {
            $this->file->saveAs(Yii::$app->params['baseDirFront'].DIRECTORY_SEPARATOR.$this->src);
        } 
    }

    public function upload()
    {
        if ($this->validate()) {
            $this->file->saveAs('uploads/' . $this->file->baseName . '.' . $this->file->extension);
            return true;
        } else {
            return false;
        }
    }

}
