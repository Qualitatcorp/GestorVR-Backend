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
	    return $actions;
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
						$result = json_decode($params->content); 
	                    $result = $result->PcaLink;
	                    $result = array('PcaLink' => $result);
	                    return  $result ;
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
	public function actionEstado(){
		 
		$urlResult = 'https://timshr.com/pca2/core/api/WS/GetPcaVsJcaResult';
		$request = \Yii::$app->request;
		$id = $request->get('id');
		if(ctype_digit($id)){
			$params = RvClientParams::find()->andWhere(["fic_id" => $id])->one();
			if($params){ //verificamos que exista
				$result = json_decode($params->content); //convertimos los datos array
				if(!array_key_exists('Jca', $result)) { //verificamos que no exista Jca en el array, eso significa que no se ha finalizado o actualizado los datos

					$fields = array('PcaCod'=> '6f9004ac-264a-4aff-9900-947ab6e11987', //parametrisamos la consulta curl
					//'PcaCod'=> $result->PcaCod,
					'JcaCods'=> 'f727b6d9-1f65-4daf-9783-44efe154b4db',
					'RepCod'=> "qua",
					);
					$result = $this->curl_post($fields,$urlResult);  //obtenemos los datos de la consulta via post
					
					if(gettype($result) === 'object'){
						$params->content = json_encode($result); //codificamos los datos a json y lo setiamos

				    	if (!$params->update()) {    //guardamos en la db, si ocurre algun error, lo mostramos	  				 
   	  						throw new \yii\web\HttpException(500, 'Error interno del sistema.');
						}else{
							//definir la carpeta donde se guardaran los pdf
							$nombre_server =  $this->existInExternalServer($result->Jca[0]->RepLink); //rescatamos el archivo desde el  
						}
						// $result->PerIde; 
						// $result->PerNom;
						// $result->Jca[0]->JcaDes;
						// $result->Jca[0]->PjeCom;
						// $result->Jca[0]->RepLink;
						
					}else{
						return "eval no finalizada";
					} 
				}else{

					return 'los datos se encuentran registrados';
				}
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
    private function existInExternalServer($file){//verifica que el archivo este disponible
    	$fp = curl_init($file); 
    	$ret = curl_setopt($fp, CURLOPT_RETURNTRANSFER, 1); 
    	$ret = curl_setopt($fp, CURLOPT_TIMEOUT, 30); 
    	$ret = curl_exec($fp); 
    	$info = curl_getinfo($fp, CURLINFO_HTTP_CODE); 
    	curl_close($fp); 
    	if($info == 404){ 
    		return "documento no  existe";
    	}else{ 
    		return $this->getFile($file);
    	}
    } 
    private function  getFile($file){ // toma el archivo y lo descarga
    	//podemos reservar el nombre en la base de datos
    	$pre_file = 'eval_sicologica_';
    	$ext_file = '.pdf';
    	$server_name = $this->exist(uniqid(),$ext_file);
    	$cl = curl_init($file); 
    	$fp = fopen($server_name, "w"); 
    	curl_setopt($cl, CURLOPT_FILE, $fp); 
    	curl_setopt($cl, CURLOPT_HEADER, 0); 
    	curl_exec($cl); 
    	curl_close($cl); 
    	fclose($fp); 
    	return $server_name;
    }
    private function  exist($nombre_fichero,$ext_file){	 //asigna un nombre al archivo que se ecuentre disponible
    	if (file_exists($nombre_fichero.$ext_file)) {
    		$this->exist(uniqid(),$ext_file);
    	} else {
    		return  $nombre_fichero.$ext_file;
    	}
    }
}