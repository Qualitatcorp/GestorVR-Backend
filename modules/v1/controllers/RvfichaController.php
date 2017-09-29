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
						$ficha->timsEva1();
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
}