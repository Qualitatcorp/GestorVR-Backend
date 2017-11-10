<?php

namespace app\models;

use Yii;
use \Firebase\JWT\JWT;

class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    const STATUS_ACTIVE = "ACTIVADO";    
    const STATUS_DELETED = "DESACTIVADO";
    const KEYCODE = "A1D6ACE3543F7A962FF5538E52B27";

    public $data;

    public static function tableName()
    {
        return 'user';
    }

    public function rules()
    {
        return [
            [['nombre', 'password'], 'required'],
            [['nacimiento', 'creacion', 'modificacion'], 'safe'],
            [['estado', 'tipo'], 'string'],
            [['username'], 'string', 'max' => 64],
            [['rut'], 'string', 'max' => 12],
            [['nombre', 'email', 'password', 'cargo'], 'string', 'max' => 255],
            [['username'], 'unique'],
            [['mail'], 'unique'],
            [['rut'], 'unique'],
        ];
    }

    public function fields()
    {
        return [
            'username',
            'rut',
            'nombre',
            'email',
            'cargo',
            'nacimiento',
            'creacion',
            'modificacion'
        ];
    }

    public function extraFields()
    {
        return ['data','now'=>function(){return time();}];
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id'=>$id,'estado'=>self::STATUS_ACTIVE]);
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'estado' => self::STATUS_ACTIVE]);
    }

    public static function findIdentityByAccessToken($token,$type = null)
    {
        try
        {
            if(strlen($token)>32)
            {
                $decoded = JWT::decode($token, static::KEYCODE, array('HS256'));
                $model=static::findOne($decoded->uid);
                if($model)
                {
                    $model->data=$decoded;
                }
                return $model;
            }
            else
            {
                return static::find()
                    ->joinWith('authentications')
                    ->andWhere(['status'=>'ALLOW'])
                    ->andWhere(['token'=>$token])
                    ->andWhere(['>','expire',time()])
                    ->one();
            }
        }
        catch(yii\UnexpectedValueException $e){}
        catch(\Firebase\JWT\BeforeValidException $e){}
        catch(\Firebase\JWT\ExpiredException $e){}
        catch(\Firebase\JWT\SignatureInvalidException $e){}

    }

    public function getId()
    {
        return $this->primaryKey;
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function findMultipleMethod($identity,$attributes){
        $query=static::find();
        foreach ($attributes as $attribute){
            $query->orWhere([$attribute=>$identity]);
        }
        return $query;
    }

    public function validatePassword($pass)
    {
        return $this->password === $pass;
    }

    // public function validatePassword($password)
    // {
    //     return Yii::$app->security->validatePassword($password, $this->password);
    // }

    // public function setPassword($password)
    // {
    //     $this->password = Yii::$app->security->generatePasswordHash($password);
    // }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    public function getAuthentications()
    {
        return $this->hasMany(Authentication::className(), ['user_id' => 'id']);
    }

    public function getAuthorizations()
    {
        return $this->hasMany(Authorization::className(), ['user_id' => 'id']);
    }

    public function Token($client, $timeOut = 3600,$type='pwd')
    {
        $token = [
            "uid" => intval($this->primaryKey),
            "cli" => $client->primaryKey,
            "typ" => $type,
            'sub' => Yii::$app->security->generateRandomString(4),
            "iat" => time(),
            "exp" => time()+$timeOut
        ];
        return JWT::encode($token, self::KEYCODE);
    }

    public function has($resource)
    {
        return $this->findBySQl("SELECT * FROM user_authorization a LEFT OUTER JOIN user_resource b ON (a.res_id=b.id) LEFT OUTER JOIN user_resource_children c ON (b.id=c.parent_id) LEFT OUTER JOIN user_resource d ON (c.child_id=d.id) WHERE a.user_id=:id AND (d.resource=:res OR b.resource=:res)",[':id'=>$this->primaryKey,':res'=>$resource])->exists(); 
        // ->createCommand()
        // ->rawSQl;
            // preg_replace('/\s+/', ' ',
    }

}