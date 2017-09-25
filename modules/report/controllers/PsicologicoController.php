<?php

namespace app\modules\report\controllers;
use yii\web\Controller;
use app\modules\v1\models\RvFicha;

class PsicologicoController extends Controller
{
	public function actionIndex()
	{
     	// 17543
    	//numero de ficha Parametro
		$id = 17543;
		$ficha = RvFicha::find()->andWhere(["fic_id" => $id, "eva_id" => '50'])->one();
		if(!empty($ficha) ){
			$head = $this->renderPartial('tims/report',array('ficha'=>$ficha),true);
			$style =  file_get_contents( \Yii::getAlias('@webroot').'/css/tims.css');
			$mpdf = new \mPDF();
			$mpdf->debug = true; 
			$mpdf->charset_in = 'utf-8';
			$mpdf->SetTitle('INFORME DE RESULTADOS SISTEMA DE EVALUACIÓN EN SEGURIDAD '.$id);
			$mpdf->WriteHTML($style,1);
			$mpdf->WriteHTML($head);
			$mpdf->Output('tims-'.$id.'.pdf','I');
		}else{
			throw new \yii\web\HttpException(404, 'No existen entradas con los parametros propuestos.');
		}
	}
	public function actionPonderacion(){
		$id = 17545;
		$ficha = RvFicha::find()->andWhere(["fic_id" => $id, "eva_id" => '50'])->one();
		$r = $ficha->alternativas;
		$pondera = 0 ; 
		$nopondera = 0;
		echo "<pre>";
		print_r($r);
		echo "</pre>";
		foreach ($r as $alternativa) {
			 if($alternativa->ecorrecta=='SI'){
			 	 // echo $alternativa->ponderacion . '+';
			 	  $pondera += $alternativa->ponderacion;
			 }else{
			 	$nopondera+=$alternativa->ponderacion;
			 }
		}
		echo $pondera, ' ' , $nopondera+$pondera, '</br>', $pondera/($nopondera+$pondera);
	}
	public function actionInscribirtims(){
    	//parametro de ingreso para el registro es el numero de ficha.
		$id = 17543;
		$urlInscripcion = "https://timshr.com/pca2/core/api/WS/AddPca";
		$ficha = RvFicha::find()->andWhere(["fic_id" => $id])->one();
		$tra = $ficha->trabajador;
		$sexo = $tra->sexo;
		switch ($sexo) {
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
		$result = $this->curl_post($fields,$urlInscripcion);
		echo $result->PcaLink, "<br>" ,$result->PcaCod;
	}
	public function actionGeteval(){
		//obtener Codigopca desde db o url
		$urlEval = 'https://timshr.com/pca2/core/api/WS/GetPcaLink';
        $fields = array(	  //finalizada = 6f9004ac-264a-4aff-9900-947ab6e11987  || no finalizada = ec85f08f-f805-449c-93f7-14a6471bf0a2       
        	'CodigoPca'=> "ec85f08f-f805-449c-93f7-14a6471bf0a2",
        	'CoRegCod'=> 'es-cl',
        	'PcaTip'=>'D'
        );
		$result = $this->curl_post($fields,$urlEval);
		print_r($result);
	}
    public function actionResultado(){ // revisar los datos que se tomaran para obtener los resultado de evaluacion
    	//NOTA comprobar si existe registro en DB,  verificar si el reporte se encuentra descargado
    	$urlResult = 'https://timshr.com/pca2/core/api/WS/GetPcaVsJcaResult';
    	//nota se agrega fields y fields2 para comprobacoion de ambos estados de evaluacion
        $fields = array(	  //finalizada = 6f9004ac-264a-4aff-9900-947ab6e11987  || no finalizada = ec85f08f-f805-449c-93f7-14a6471bf0a2       
        	'PcaCod'=> "6f9004ac-264a-4aff-9900-947ab6e11987",
        	'JcaCods'=> 'f727b6d9-1f65-4daf-9783-44efe154b4db',
        	'RepCod'=> "qua",
        );
		$fields2 = array(	  //finalizada = 6f9004ac-264a-4aff-9900-947ab6e11987  || no finalizada = ec85f08f-f805-449c-93f7-14a6471bf0a2       
			'PcaCod'=> "ec85f08f-f805-449c-93f7-14a6471bf0a2",
			'JcaCods'=> 'f727b6d9-1f65-4daf-9783-44efe154b4db',
			'RepCod'=> "qua",
		);
		$result = $this->curl_post($fields,$urlResult);
		if (gettype($result) === 'object')
		{
			$result->PerIde; 
			$result->PerNom;
			$result->Jca[0]->JcaDes;
			$result->Jca[0]->PjeCom;
			$result->Jca[0]->RepLink;
			$this->existInExternalServer($result->Jca[0]->RepLink);
		}else{
			echo "eval no finalizada";
		}
    } //fin de resultados controller
    private function existInExternalServer($file){//verifica que el archivo este disponible
    	$fp = curl_init($file); 
    	$ret = curl_setopt($fp, CURLOPT_RETURNTRANSFER, 1); 
    	$ret = curl_setopt($fp, CURLOPT_TIMEOUT, 30); 
    	$ret = curl_exec($fp); 
    	$info = curl_getinfo($fp, CURLINFO_HTTP_CODE); 
    	curl_close($fp); 
    	if($info == 404){ 
    		return "documento no $this->existe";
    	}else{ 
    		$this->getFile($file);
    	}
    }  
    private function  getFile($file){ // toma el archivo y lo descarga
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
    }
    private function  exist($nombre_fichero,$ext_file){	 //asigna un nombre al archivo que se ecuentre disponible
    	if (file_exists($nombre_fichero.$ext_file)) {
    		$this->exist(uniqid(),$ext_file);
    	} else {
    		return  $nombre_fichero.$ext_file;
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