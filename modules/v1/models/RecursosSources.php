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
            [['file'], 'file', 'skipOnEmpty' => true, 'mimeTypes' => 'application/*,audio/*,video/*,text/*,image/*'],
            // [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => ['7z','ai','apk','avi','bmp','cab','css','doc','docx','eps','exe','flv','gif','htm','html','ico','jpe','jpeg','jpg','js','json','mov','mp3','mp4','msi','ods','odt','pdf','php','png','ppt','pptx','ps','psd','qt','rar','rtf','svg','svgz','swf','tif','tiff','txt','xls','xlsx','xml','zip']],
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
        unset($fields['src'],$fields['type']);
        // $fields[]='url';
        $fields[]='mimeType';
        // $fields[]='dir';
        return $fields;
    }

    public function extraFields()
    {
        return ['src','size','dir','url','exists'];
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

    public function beforeValidate()
    {
        $this->file=\yii\web\UploadedFile::getInstanceByName('file');
        if($this->file!==null){
            $this->title = $this->file->baseName.'.'.$this->file->extension;
            $this->type =  $this->file->type;
            if($this->isNewRecord){
                do {
                    $this->src = \Yii::$app->security->generateRandomString().'.'.$this->file->extension;
                } while ($this->exists);
            }
            // var_dump($this->file->size);
            // exit;
        }
        return parent::beforeValidate();
    }

    public function afterValidate()
    {
        if(!$this->hasErrors()&&$this->file!==null)
        {
            $this->file->saveAs($this->dir);
        } 
    }

    public function getSize()
    {
        if($this->exists){
            return filesize($this->dir);           
        }
    }

    public function getExists()
    {
        return file_exists($this->dir);
    }

    public function getMimeType()
    {        
        if($this->exists){
            $type=mime_content_type ($this->dir);
            if($type!==$this->type){
                $this->type=$type;
                $this->updateAttributes(['type']);
            }
            return $type;
        }
    }

}
