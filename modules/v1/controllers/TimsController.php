<?php 

namespace app\modules\v1\controllers;

use yii\rest\Controller;
use app\modules\v1\models\RvFicha;
use app\modules\v1\models\RvClientParams;
use app\modules\v1\models\RvClientCalificacion;

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
 

	public function actionIndex()
	{
		$ficha = RvFicha::findOne(18378);
		
	   


	}
 
    public function actionCreate()
	{   //paramatro de entrada fic_id
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
				$ClientParam = RvClientParams::find()->andWhere(["fic_id" => $id])->one();
				if(!$ClientParam){//si no existe registramos una nueva
					$result = $this->curl_post($fields,$urlInscripcion);
					if(gettype($result) === 'object'){// verificar que se devuelva la informacion correpondiente
						//  Al inscribir retorna  dos codigos
						$params = array( //seteamos un array que sera nuestro objeto en params
							'fic_id'=>$ficha->fic_id,
							'id' =>$result->PcaCod,
							'pdf' => null,
							'nota'=>null,
							'url' =>$result->PcaLink, 
							'PcaCod' =>$result->PcaCod,  
						);
						$params = json_encode($params);
						$ClientParam = new RvClientParams;		
						$ClientParam->fic_id = $id;
						$ClientParam->cli_eva_id = '1';
						$ClientParam->type = 'json';
						$ClientParam->content =  $params;
						$ClientParam->save();
						$result = json_decode($ClientParam->content); 
	                    $result = array('id' => $result->id,'url' =>$result->url);
	                    return  $result ;
					}else{
						throw new \yii\web\HttpException(500, 'Error interno del sistema.');
					}
				}else{ // si existe la rescatamos
					$result = json_decode($ClientParam->content); 
				    $result = array(
				    	'id' => $result->id,
				    	'url' =>$result->url,
				    	'pdf'=>$result->pdf,
				    	'nota'=>$result->nota
				    );
                    return  $result ;
				}
			}else{
				throw new \yii\web\HttpException(404, 'No existe ficha para la inscripción de la evaluación psicologica.');
			}
		}else{
			throw new \yii\web\HttpException(422, 'No se encuentran los parametros para la inscripción de la ficha.');
		}
   
	}
	public function actionView($id){
		

		$urlResult = 'https://timshr.com/pca2/core/api/WS/GetPcaVsJcaResult';
		$ClientParam = RvClientParams::find()->andWhere(["fic_id" => $id])->one();
      

		if($ClientParam){ //verificamos que exista
			  $results = json_decode($ClientParam->content); //convertimos los datos array
			  if($results->nota and $results->pdf){ // si existe nota, eval finalizada y actualizada
			  	$result = json_decode($ClientParam->content);
				$result = array(
					'id' => $result->id,
					'pdf' =>  $result->pdf,
					'nota' => $result->nota ,
				);
				 
				 
		 		$this->saveRiesgo(($result['nota']/100),$id);
				return $result;   	
			  }else{ //Aca actualiza el codigo desde la db
		  	    $result = json_decode($ClientParam->content);
		  		$fields = array(
		  			 'PcaCod'=> '6f9004ac-264a-4aff-9900-947ab6e11987', //parametrisamos la consulta curl
				    //'PcaCod'=> $result->PcaCod,
					'JcaCods'=> 'f727b6d9-1f65-4daf-9783-44efe154b4db',
					'RepCod'=> "qua",
				);
				$curl_result = $this->curl_post($fields,$urlResult);  //obtenemos los datos de la consulta via post
				if(gettype($curl_result) === 'object'){
					$params = array( //seteamos un array que sera nuestro objeto en params
					'id' =>$curl_result->PerIde,
					'pdf' => $curl_result->Jca[0]->RepLink,
					'nota'=> $curl_result->Jca[0]->PjeCom,
					'url' => $result->url, 
					'PcaCod' =>$result->PcaCod,  
					);
					// $params  = json_encode($params); //codificamos los datos a json y lo setiamos
					$ClientParam->content = json_encode($params);
					if (!$ClientParam->update()) {    //guardamos en la db, si ocurre algun error, lo mostramos	  				 
						throw new \yii\web\HttpException(500, 'Error interno del sistema.');
					}
					else{
						$calificacion = RvClientCalificacion::find()->andWhere(["fic_id" => $id])->one();
						if(!$calificacion){
							
							$nota = intval($curl_result->Jca[0]->PjeCom)/100;
						 	$calificacion = new  RvClientCalificacion; //guardamos la nota
							$calificacion->fic_id = $id;
							$calificacion->cli_eva_id = 1 ;
							$calificacion->calificacion =  $nota;
							$calificacion->save();
							// se guardan los datos en la ficha 


							
						}
						$this->saveRiesgo(($params['nota']/100),$id);
						//return $nombre_server =  $this->existInExternalServer($result->url); //rescatamos el archivo desde el  
						return $params;
						//definir la carpeta donde se guardaran los pdf
						
					}

				

				}else{//evaluacion  
					throw new \yii\web\HttpException(404, 'No existen entradas con los parametros propuestos.');
				}
			  }

 
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
    	$cl = curl_init($file); 
    	$ret = curl_setopt($cl, CURLOPT_RETURNTRANSFER, 1); 
    	$ret = curl_setopt($cl, CURLOPT_TIMEOUT, 30); 
    	$ret = curl_exec($cl); 
    	$info = curl_getinfo($cl, CURLINFO_HTTP_CODE); 


    	//curl_close($cl); 
    	if($info == 404){ 
    		return "documento no  existe";
    	}else{ 
    		return $this->getFile($cl);
    	}
    } 
    private function  getFile($cl){ // toma el archivo y lo descarga
    	//podemos reservar el nombre en la base de datos
    	 
    	$server_name = $this->exist();
    	//$cl = curl_init($file); 
    	$fp = fopen($server_name, "w"); 
    	curl_setopt($cl, CURLOPT_FILE, $fp); 
    	curl_setopt($cl, CURLOPT_HEADER, array('Content-Encoding: none','Content-Type: application/pdf')); 
    	curl_exec($cl); 
    	curl_close($cl); 
    	fclose($fp); 
    	return  $server_name;
    }
    private function  exist(){	 //asigna un nombre al archivo que se ecuentre disponible
    	$pat = \Yii::$app->params['baseDirFront'] ;
    	$server_name =  $pat.uniqid().'.pdf';
    	if (file_exists($server_name)) { // verificar la ruta
    		$this->exist();
    	} else {
    		return  $server_name ; //'59cbbbafbff58'; //
    	}
    }
    private function saveRiesgo($nota,$id){
		$ficha = RvFicha::find()->andWhere(["fic_id" => $id])->one();
	    $params = $ficha->params;
	    $data = $params->data;
	    $data['riesgo']['nota']=$nota;

	    $notaInfo1 = $data['percepcion']['nota'];
	    $notaInfo2 = $data['conocimiento']['nota'];
	    $data['nota'] = ($notaInfo1 +  $notaInfo2 +  $nota)/3;
	    $ficha->calificacion = $data['nota'];
	    $ficha->save();
 	    $params->data=$data ;
	    
    }
}