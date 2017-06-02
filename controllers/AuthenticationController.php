<?php 

namespace app\controllers;

use yii;
use app\models\User;
use app\models\Client;
use app\models\Authentication;

use yii\rest\Controller;
use yii\web\HttpException;

class AuthenticationController extends Controller
{
	public function actionToken()
	{	
		$request = Yii::$app->request;
		$user = User::findMultipleMethod('ruben',['username','rut','email'])->one();
		switch ($request->post('grant_type')) {
			case 'password':
				if(empty($user)){
                    throw new HttpException(401, "Error en credenciales. Usuario no existe.");
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
		$Authentication = $user->GrantAccess($client,3600,$request->post('refresh')==='true');
		$Auth=[
			'token'=>$Authentication->token,
			'token_type'=>'Bearer',
			'expire_in'=>3600
		];
		if($request->post('refresh')==='true'){
			$Auth['refresh']=$Authentication->refresh;
		}
		return $Auth;
	}

	public function actionRefresh()
	{
        $refresh = Yii::$app->request->post('refresh');
        if(empty($refresh)){
            throw new HttpException(401, "Solicitud Invalida.");
        }
        $auth=Authentication::findActive()->andWhere(['refresh'=>$refresh])->one();
        if(empty($auth)){
            throw new HttpException(401, "Unauthorized.");
        }else{
        	if($auth->Renovate()){
        		return $auth->refresh;
        	}
        }
	}
}