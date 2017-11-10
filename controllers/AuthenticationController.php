<?php 

namespace app\controllers;

use yii;
use app\models\User;
use app\models\Client;

use yii\rest\Controller;
use yii\web\HttpException;

class AuthenticationController extends Controller
{
	public function actionToken()
	{
		$request = Yii::$app->request;
		$user = User::findMultipleMethod($request->post('username'),['username','rut','email'])->one();
		switch ($request->post('grant_type')) {
			case 'password':
				if(empty($user)){
                    throw new HttpException(401, "Error en credenciales. El usuario no existe.");
				}else{
					if(!$user->validatePassword($request->post('password'))){
						throw new HttpException(401, "Error en credenciales. Contraseña invalida.");
					}
				}
				break;		
			default:
                throw new HttpException(405, "Method Not Allowed.");
				break;
		}
		$client=Client::findOne(['name'=>$request->post('client_id'),'secret'=>$request->post('client_secret')]);
		if(empty($client)){
            throw new HttpException(401, "Error en credenciales. Cliente invalido.");
		}
		$token = $user->Token($client);
		$Auth=[
			'token'=>$token,
			'token_type'=>'Bearer',
			'expire_in'=>3600
		];
		return $Auth;
	}
}