<?php 

namespace app\modules\v1\controllers;

use yii\rest\Controller;
use app\modules\v1\models\RvFicha;
use app\modules\v1\models\RvClientParams;

class TimsController extends  Controller
{
	public function behaviors()
	{
		return \yii\helpers\ArrayHelper::merge(parent::behaviors(),[
			'authenticator'=>[
				'class' => \yii\filters\auth\HttpBearerAuth::className()  
			],
			'authorization'=>[
				'class' => \app\components\Authorization::className(),
			],
		]);
	}
	public function actions()
	{
	    $actions = parent::actions();

	    // disable the "delete" and "create" actions
	    unset($actions['create']);
	    $actions['put']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
	    return $actions;
	}
	public function prepareDataProvider()
	{
		return 1;
	}	
    public function actionCreate()
	{
		 //paramatro de entrada fic_id
		$post = \Yii::$app->request->post();
		if(isset( $post['fic_id']) ){ // fic 17543
			$id = $post['fic_id'];
			$urlInscripcion = "https://timshr.com/pca2/core/api/WS/AddPca";
			$ficha = RvFicha::find()->andWhere(["fic_id" => $id])->one();
			if($ficha){
				$tra = $ficha->trabajador;
				$sexo = $tra->sexo;

				switch ($sexo) { // que hacer en caso de que el sexo no se encuentre registrado
					case 'MASCULINO':
					$sexoL = 'M';
					break;
					case 'FEMENINO':
					$sexoL = 'F';
					break;
					default:
					$sexoL = 'M'; 
					break;
				}
				$fields = array(	        
					'CoKey'=> "5ccf4857-2691-4eaf-b2e0-f7d42375691c",
					'PerNom'=>  $tra->nombre,
					'PerPriApe'=> $tra->paterno,
					'PerSegApe'=> $tra->materno,
					'PerNumIde'=> $ficha->fic_id,
					'PerGen'=>  $sexoL,
					'CoRegCod'=> "es-cl",
					'PcaTip'=> "D"
				);
				$params = RvClientParams::find()->andWhere(["fic_id" => $id])->one();
				if(!$params){//si no existe registramos una nueva
					$result = $this->curl_post($fields,$urlInscripcion);
					if(gettype($result) === 'object'){// verificar que se devuelva la informacion correpondiente
						$result = json_encode($result);
						$params = new RvClientParams;		
						$params->fic_id = $id;
						$params->cli_eva_id = '1';
						$params->type = 'json';
						$params->content =  $result;
						$params->save();
						return $params->content;
					}else{
						throw new \yii\web\HttpException(500, 'Error interno del sistema.');
					}
				}else{ // si existe la rescatamos
					$result = $params->content;
					$result = json_decode($result); 
                    $result  = $result->PcaLink;
                    $result = array('PcaLink' => $result);
                    return  $result ;
				}
			}else{
				throw new \yii\web\HttpException(404, 'No existen entradas con los parametros propuestos.');
			}
		}else{
			throw new \yii\web\HttpException(404, 'No existen entradas con los parametros propuestos.');
		}
   
	}
 
 
 
	private function curl_post($fields,$url){ //codigo que curl que crea la comunicacion via post
    	$postvars='';
		$sep='';
		foreach($fields as $key=>$value) 
		{
			$postvars.= $sep.urlencode($key).'='.urlencode($value);
			$sep='&';
		}
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST,count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		$result = curl_exec($ch);
		curl_close($ch);
	    $result = json_decode($result);	
		return $result;
    }
}