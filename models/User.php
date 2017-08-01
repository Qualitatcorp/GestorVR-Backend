<?php

namespace app\models;

use Yii;

class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    const STATUS_ACTIVE = "ACTIVADO";
    const STATUS_DELETED = "DESACTIVADO";

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
        // $auth=Authentication::findActive()->andWhere(['token'=>$token])->one();
        // if(!empty($auth)){
        //     return $auth->user;
        // }
        // return null;
        

        return static::find()->
        joinWith('authentications')
            ->andWhere(['status'=>'ALLOW'])
            ->andWhere(['token'=>$token])
            ->andWhere(['>','expire',time()])
            ->one();
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
        foreach ($attributes as $attribute) {
            $query->orWhere([$attribute=>$identity]);
        }
        return $query;
    }

    public function validatePassword($pass)
    {
        return $this->password === $pass;
    }

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

    public function GrantAccess($client, $timeOut = 3600,$refresh = false)
    {
        $Authentication = new Authentication();
        $Authentication->attributes=[
            'token'=>\Yii::$app->security->generateRandomString(),
            'refresh'=>($refresh)?\Yii::$app->security->generateRandomString():null,
            'created'=>time(),
            'expire'=>time()+$timeOut,
            'user_id'=>$this->primaryKey,
            'client_id'=>$client->primaryKey
        ];
        
        if($Authentication->save()){
            return $Authentication;       
        }else{
            return $Authentication;       
        }
    }

    public function has($resource)
    {

        return $this->findBySQl("SELECT  user_authorization.* FROM user_authorization LEFT OUTER JOIN user_resource ON (user_authorization.res_id = user_resource.id) LEFT OUTER JOIN user_resource_children ON (user_resource.id = user_resource_children.parent_id) LEFT OUTER JOIN user_resource c ON (user_resource_children.child_id = c.id) WHERE (user_resource.resource = :res OR  c.resource = :res) AND  user_authorization.user_id =:id",[':id'=>$this->primaryKey,':res'=>$resource])->exists();
    }

    


    // public function validatePassword($password)
    // {
    //     return Yii::$app->security->validatePassword($password, $this->password);
    // }

    // public function setPassword($password)
    // {
    //     $this->password = Yii::$app->security->generatePasswordHash($password);
    // }
}