<?php 

namespace app\modules\v1\controllers;

use yii\rest\ActiveController;

use app\modules\v1\models\Dispositivo;
use app\modules\v1\models\RvFicha;
use app\modules\v1\models\RvFichaParams;
use app\modules\v1\models\RvAlternativa;
use app\modules\v1\models\RvRespuesta;

class RvfichaController extends ActiveController
{
	public $modelClass = 'app\modules\v1\models\RvFicha';

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

	public function actionEvaluation()
	{
		$request=\Yii::$app->request;
		if(!$request->isPost)
		{
			throw new \yii\web\HttpException(405, 'Metodo no permitido.');
		}
		/*
		 *	Resuelve restriccion de Dispositivo
		 */
		$Dispositivo=Dispositivo::findOne($request->post("disp_id"));
		if($Dispositivo!==null)
		{
			if(!$Dispositivo->permission)
			{
				throw new \yii\web\HttpException(401, 'Dispositivo no habilitado.');
			}
		}
		else
		{
			throw new \yii\web\HttpException(404, 'No Existe el dispositivo.');
		}
		/*
		 * Verificacion de Respuestas
		 */

		$respuestas=$request->post('respuestas',null);
		if($respuestas!==null)
		{
			if(RvAlternativa::find()->where(['IN','alt_id',array_column($respuestas,'alt_id')])->count()!=count($respuestas)){
				throw new \yii\web\HttpException(404, 'La evaluacion no tiene respuestas validas.'.RvAlternativa::find()->where(['IN','alt_id',array_column($respuestas,'alt_id')])->count().count($respuestas));
			}
		}
		else
		{
			throw new \yii\web\HttpException(404, 'La evaluacion no tiene respuestas.');
		}
		/*
		 * Creacion de Evaluación y respuestas
		 */
		$ficha=new RvFicha();
		$ficha->Attributes=$request->post();
		if($ficha->save())
		{
			$respuestas_save=array();
			foreach ($respuestas as $r) 
			{
				/*
				 *	Se Crean las respuestas de la evaluacion
				 */
				$respuesta=new RvRespuesta();
				$respuesta->Attributes=$r;
				$respuesta->fic_id=$ficha->primaryKey;
				if($respuesta->save())
				{
					$respuestas_save[]=$respuesta;
				}
				else
				{
					return $respuesta;
				}
				/*
				 * Integracion de Servicios y notas especiales
				 */
				switch ($this->eva_id) {
					case 54:
						$this->timsEva1($ficha);
						break;
					default:

						break;
				}
			}
			$response=$ficha->getAttributes();
			$response['respuestas']=$respuestas_save;
			return $response;
		}
		else
		{
			return $ficha;
		}
	}

	public function actionSearch()
	{
		if (!empty($_GET)) {
			$request=\Yii::$app->request;
			$reserve=['per-page','sort','page','expand','expand','fields'];
			$model = new $this->modelClass;
			foreach ($_GET as $key => $value) {
				if (!$model->hasAttribute($key)&&!in_array($key,$reserve)) {
					throw new \yii\web\HttpException(404, 'Atributo invalido :' . $key);
				}
			}
			try {
			   	$query = $model->find();
			   	$range=['id'];
				foreach ($_GET as $key => $value) {
					if(!in_array($key,$reserve)){
						if (in_array($key,$range)) {
							$limit = explode('-',$value);
							$query->andWhere(['between', $key,$limit[0],$limit[1]]);
						}else{
							$query->andWhere(['like', $key, $value]);
						}
					}
				}
				$provider = new \yii\data\ActiveDataProvider(['query' => $query]);
			} catch (Exception $ex) {
				throw new \yii\web\HttpException(500, 'Error interno del sistema.');
			}

			if ($provider->getCount() <= 0) {
				throw new \yii\web\HttpException(404, 'No existen entradas con los parametros propuestos.');
			} else {
				return $provider;
			}
		} else {
			throw new \yii\web\HttpException(400, 'No se puede crear una query a partir de la informacion propuesta.');
		}
	}

	private function timsEva1($model)
	{
		if($model->eva_id==54){
			//Principal
			$Principal=[1135,1136,1137,1138,1147,1148,1161,1162,1163,1173,1174,1175,1176,1185,1186,1187,1193,1194];
			//Secundario
			$Secundario=[1139,1140,1141,1149,1150,1151,1164,1165,1166,1167,1177,1178,1179,1188,1189,1190,1195];
			//Distractor
			$Distractor=[1142,1143,1144,1145,1146,1152,1153,1154,1155,1156,1157,1158,1159,1160,1168,1169,1170,1171,1172,1180,1181,1182,1183,1184,1191,1192,1196,1197,1198,1199];
			//Pregunta
			$Pregunta=[1200,1201,1202,1203,1204,1205,1206,1207,1208,1209,1210,1211,1212,1213,1214,1215,1216,1217,1218,1241];

			/*
			 * Se define el parametro temporal para almacenar la evaluacion
			 */

			$paramsTemp=[
				"percepcion"=>[
					'pri'=>[
						"total"=>0,
						"correcto"=>0
					],
					'sec'=>[
						"total"=>0,
						"correcto"=>0
					],
					'dis'=>[
						"total"=>0,
						"correcto"=>0
					]
				],
				"conocimiento"=>[
					"total"=>0,
					"correcto"=>0
				]
			];

			/*
			 * Contar Correctas en cada item
			 */

			foreach ($model->alternativas as $alt) {
				if(array_search($alt->pre_id,$Principal)!==false){
					$paramsTemp['percepcion']['pri']['total']++;
					if($alt->correcta==='SI')$paramsTemp['percepcion']['pri']['correcto']++;
				}else{
					if(array_search($alt->pre_id,$Secundario)!==false){
						$paramsTemp['percepcion']['sec']['total']++;
						if($alt->correcta==='SI')$paramsTemp['percepcion']['sec']['correcto']++;
					}else{
						if(array_search($alt->pre_id,$Distractor)!==false){
							$paramsTemp['percepcion']['dis']['total']++;
							if($alt->correcta==='SI')$paramsTemp['percepcion']['dis']['correcto']++;
						}else{
							if(array_search($alt->pre_id,$Pregunta)!==false){
								$paramsTemp['conocimiento']['total']++;
								if($alt->correcta==='SI')$paramsTemp['conocimiento']['correcto']++;
							}else{
								return "No Existe pregunta";
							}
						}
					}
				}
			}

			/* 
			 * Calcular Notas
			 */

			$paramsTemp['percepcion']['pri']['nota']=$paramsTemp['percepcion']['pri']['correcto']/$paramsTemp['percepcion']['pri']['total'];
			$paramsTemp['percepcion']['sec']['nota']=$paramsTemp['percepcion']['sec']['correcto']/$paramsTemp['percepcion']['sec']['total'];
			$paramsTemp['percepcion']['dis']['nota']=$paramsTemp['percepcion']['dis']['correcto']/$paramsTemp['percepcion']['dis']['total'];
			$paramsTemp['conocimiento']['nota']=$paramsTemp['conocimiento']['correcto']/$paramsTemp['conocimiento']['total'];

			/*
			 * Almacenar Array de notas
			 */
			
			$params=$model->params;
			if(empty($params))
			{
				$params=new RvFichaParams();
				$params->fic_id=$model->primaryKey;
				$params->data=[];
			}
			$params->data=array_replace_recursive($params->data,$paramsTemp);
			if(!$params->save()){
				return $params;
			}
		}
	}
}