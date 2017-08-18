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
		$user = User::findMultipleMethod($request->post('username'),['username','rut','email'])->one();
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
		Authentication::UpdateHistory();
		return $Auth;
	}

	public function actionRefresh()
	{
		$request = Yii::$app->request;
		// tipo de metodo
		switch ($request->post('grant_type')) {
			case 'refresh_token':
				break;		
			default:
                throw new HttpException(405, "Method Not Allowed.");
				break;
		}
		//Busca el cliente
		$client=Client::findOne(['name'=>$request->post('client_id'),'secret'=>$request->post('client_secret')]);
		if(empty($client)){
            throw new HttpException(401, "Error en credenciales. Cliente invalido.");
		}

        $refresh = Yii::$app->request->post('refresh_token');
        if(empty($refresh)){
            throw new HttpException(401, "Solicitud Invalida.");
        }

        $auth=Authentication::findActive()->andWhere(['refresh'=>$refresh,'client_id'=>$client->id])->one();
        if(empty($auth)){
            throw new HttpException(401, "Unauthorized.");
        }else{
        	if($auth->Renovate()){
        		return [
        			'refresh'=>$auth->refresh,
        			'expire_in'=>3600
        		];
        	}
        }
	}

	public function actionRevoke()
	{
		$request = Yii::$app->request;
		//Busca el cliente
		$client=Client::findOne(['name'=>$request->post('client_id'),'secret'=>$request->post('client_secret')]);
		if(empty($client)){
            throw new HttpException(401, "Error en credenciales. Cliente invalido.");
		}
        $token = Yii::$app->request->post('token');
        if(empty($token)){
            throw new HttpException(401, "Solicitud Invalida.");
        }

        $auth=Authentication::findActive()->andWhere(['token'=>$token,'client_id'=>$client->id])->one();
        if(empty($auth)){
            throw new HttpException(401, "Unauthorized.");
        }else{
        	$auth->status="DENY";
        	if($auth->save(false,['status'])){
        		return ['status'=>true];
        	}
        }
	}
}