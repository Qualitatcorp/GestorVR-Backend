<?php 

namespace app\modules\v1\controllers;

use yii\rest\ActiveController;
use yii\helpers\Json;

use app\modules\v1\models\Dispositivo;
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
		/*
		 *	Definicion de Dispositivo
		 */
		$Dispositivo=Dispositivo::findOne($request->post("disp_id"));
		if($Dispositivo!==null)
		{
			if($Dispositivo->permission)
			{

			}
		}
		/*
		 * Verificacion de Respuestas
		 */

		$Prev_Respuestas=Dispositivo::findOne($request->post("respuestas"));
		$Respuestas=array();
		$valid=true;
		if($Prev_Respuestas!==null)
		{
			foreach ($Prev_Respuestas as $a) 
			{
				$alt=RvAlternativa::find()
					->where(['alternativa'=>$a['alternativa']])
					->andWhere(['pre_id'=>$a['pregunta']])
					->One();
				if($alt!==null)
				{
					http_response_code(422);
					echo "Faltan pregunta por definir :";
					var_dump($alt['alternativa'],$alt['pregunta']);
					exit;
				}
				$respuesta=new RvRespuesta();
				$respuesta->alt_id=$alt->primaryKey;
				$respuesta->creado=$a['creacion'];
				$valid=$model->validate()&&$valid;
				$Respuestas[]=$respuesta;
			}
		}
		return $Dispositivo;
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
}