<?php 

namespace app\modules\v1\controllers;

use yii\rest\ActiveController;

use app\modules\v1\models\Dispositivo;
use app\modules\v1\models\RvFicha;
use app\modules\v1\models\RvFichaParams;
use app\modules\v1\models\RvAlternativa;
use app\modules\v1\models\RvRespuesta;
use app\modules\v1\models\Trabajador;

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
		$ficha=new RvFicha();
		$ficha->Attributes=$request->post();
		$respuestas=$request->post('respuestas');
		$params=$request->post('params');

		/*
		 *	Resuelve Trabajador
		 */
		$postTrabajador=$request->post('trabajador');
		if($ficha->trab_id)
		{
			/*
			 *	Si se concoce al trabajador se actualiza
			 */
			if($postTrabajador){
				$trabajador=$ficha->trabajador;
				$trabajador->Attributes=$postTrabajador;
				$trabajador->save();
			}
		}
		else
		{
			/*
			 *	Si no se le a asignado un trabajador pero tiene informacion del trabajador
			 */
			if($postTrabajador)
			{
				$trabajador=Trabajador::findIdentity($postTrabajador);
				if(!$trabajador)
				{
					$trabajador=new Trabajador();
				}
				$trabajador->Attributes=$postTrabajador;
				$trabajador->save();
				$ficha->trab_id=$trabajador->primaryKey;
			}
		}

		/*
		 *	Validacion de Ficha de evaluacion
		 */
		$ficha->validate();
		
		/*
		 *	Validacion del contenido de la ficha de evaluacion
		 */
		$evaluacion=$ficha->evaluacion;
		if($evaluacion)
		{
			switch ($evaluacion->nota)
			{
				case 'INTERNA_SIMPLE':
				case 'INTERNA_COMPLEJA':
				case 'COMPUESTA_SIMPLE':
				case 'COMPUESTA_COMPLEJA':
					if(empty($respuestas))
						$ficha->addError('respuestas','La evaluacion no tiene respuestas.');
				break;
				case 'EXTERNA_SIMPLE':
				case 'EXTERNA_COMPLEJA':
					if(empty($params))
						$ficha->addError('params','La evaluacion no tiene parametros para generar la evaluacion.');
				break;
				case 'EXTERNA_PLANA':
				break;
			}
		}

		/*
		 *	Validacion de las respuestas en caso de que tenga
		 */
		if($respuestas)
		{
			$alternativas=$evaluacion->alternativas;
			$result=array_diff(array_column($respuestas, 'alt_id'), array_column($alternativas, 'alt_id'));
			if($result)
			{
				$ficha->addError('respuestas','Las alternativas : ('.implode(',',$result).') son invalidas.');
			}
		}

		/*
		 *	Procede a la creacion de una evaluacion en caso de no contenga ningun error
		 */

		if($ficha->hasErrors())
		{
			return $ficha;
		}
		else
		{
			/*
			 * Creacion de la ficha
			 */
			if($ficha->save())
			{

				/*
				 *	Se Crean las respuestas de la evaluacion si existen
				 */
				if($respuestas){
					foreach ($respuestas as $r) 
					{
						$respuesta=new RvRespuesta();
						$respuesta->Attributes=$r;
						$respuesta->fic_id=$ficha->primaryKey;
						if(!$respuesta->save())
						{
							return $respuesta;
						}
					}
				}

				/*
				 *	Se Crean los parametros si existen
				 */
				if($params)
				{
					$parametros=new RvFichaParams();
					$parametros->Attributes=$params;
					$parametros->fic_id=$ficha->primaryKey;
					if(!$parametros->save())
					{
						return $parametros;
					}
				}

				/*
				 * Calcular la nota o agregacion de dependencias por tipo de evaluacion
				 */
				$ficha->Resolve();
				/*
				 * Termino de rutina
				 */
				return $ficha;
			}
			else
			{
				return $ficha;
			}
		}
	}

	public function actionSearch()
	{
		if (!empty($_GET)) {
			$request=\Yii::$app->request;
			$reserve=[
				'per-page',
				'sort',
				'page',
				'expand',
				'expand',
				'fields'
			];
			$extraJoin=[
				'emp_id'
			];
			$model = new $this->modelClass;
			foreach ($_GET as $key => $value) {
				if (!$model->hasAttribute($key)&&!in_array($key,$reserve)&&!in_array($key,$extraJoin)) {
					throw new \yii\web\HttpException(404, 'Atributo invalido :' . $key);
				}
			}
			try {
			   	$query = $model->find();
			   	$range=['id'];
				foreach ($_GET as $key => $value) {
					if(!in_array($key,$reserve)){
						if (in_array($key,$extraJoin)) {
							switch ($key) {
								case 'emp_id':
									$query->joinWith('dispositivo')->andWhere(['dispositivo.emp_id'=>$value]);
									break;
							}
						}else{
							if (in_array($key,$range)) {
								$limit = explode('-',$value);
								$query->andWhere(['between', $key,$limit[0],$limit[1]]);
							}else{
								$query->andWhere(['like', $key, $value]);
							}	
						}
					}
				}
				// return $query->createCommand()->rawSql;
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