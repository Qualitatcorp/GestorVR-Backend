<?php 

namespace app\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;

class EmpresaController extends ActiveController
{
	public $modelClass = 'app\modules\v1\models\Empresa';

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

	public function actionViewidentity(){
		return $this->modelClass::find()
				->joinWith('users')
				->where('empresa_user.usu_id=:id',[':id'=>Yii::$app->user->identity->primaryKey])->One();
	}

	public function actionFindidentity()
	{
		$post=\Yii::$app->request->post();
		$model=$this->modelClass::findOne($post);
		if($model===null)
		{
			$model=new $this->modelClass();
			$model->attributes=$post;
			$model->save();
		}
		return $model;
	}

	public function actionSearch()
	{
		if (!empty($_GET)) {
			$request=Yii::$app->request;
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


/**
 *
 * Ficha de perteneciente a la emrpesa
 *
 */
	public function actionIndexficha()
	{
		$request=Yii::$app->request;
		$reserve=['per-page','sort','page','expand','expand','fields'];
		$model = new \app\modules\v1\models\RvFicha;
		foreach ($_GET as $key => $value) {
			if (!$model->hasAttribute($key)&&!in_array($key,$reserve)) {
				throw new \yii\web\HttpException(404, 'Atributo invalido :' . $key);
			}
		}
		try {
		   	$query = $model->find()
		   		->joinWith('dispositivo.empresa.users')
		   		->where('empresa_user.usu_id=:id',[':id'=>Yii::$app->user->identity->primaryKey]);
		   	$range=['id','trab_id'];
			foreach ($_GET as $key => $value) {
				if(!in_array($key,$reserve)){
					if (in_array($key,$range)) {
						$limit = explode('-',$value);
						if(count($limit)===1){
							$limit = explode(',',$value);
							$query->andWhere(['in','trab_id',$limit]);
						}else{
							$query->andWhere(['between', 'trabajador.'.$key,$limit[0],$limit[1]]);
						}
					}else{
						$query->andWhere(['like', 'trabajador.'.$key, $value]);
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
	}

	public function actionViewficha($id)
	{
		$model = \app\modules\v1\models\RvFicha::find()
			->joinWith('dispositivo.empresa.users')
			->andWhere('empresa_user.usu_id=:usu AND rv_ficha.fic_id=:id',[':usu'=>Yii::$app->user->identity->primaryKey,':id'=>$id])
			->one();
			// ->createCommand()->rawSql;
		if($model!==null){
			return $model;
		}else{
			throw new \yii\web\HttpException(401, 'No esta autorizado a acceder a la información del trabajador, este debe estar relacionado a traves de una evaluación.');
		}
	}

/**
 *
 * Trabajador de la empresa asignada
 *
 */

	public function actionIndextrabajador()
	{
		$request=Yii::$app->request;
		$reserve=['per-page','sort','page','expand','expand','fields'];
		$model = new \app\modules\v1\models\Trabajador;
		foreach ($_GET as $key => $value) {
			if (!$model->hasAttribute($key)&&!in_array($key,$reserve)) {
				throw new \yii\web\HttpException(404, 'Atributo invalido :' . $key);
			}
		}
		try {
		   	$query = $model->find()
		   		->distinct()
				->joinWith('fichas.dispositivo.empresa.users')
				->where('empresa_user.usu_id=:id',[':id'=>Yii::$app->user->identity->primaryKey]);
		   	$range=['id'];
			foreach ($_GET as $key => $value) {
				if(!in_array($key,$reserve)){
					if (in_array($key,$range)) {
						$limit = explode('-',$value);
						$query->andWhere(['between', 'trabajador.'.$key,$limit[0],$limit[1]]);
					}else{
						$query->andWhere(['like', 'trabajador.'.$key, $value]);
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
	}



	public function actionViewtrabajador($id)
	{		
		$model = \app\modules\v1\models\Trabajador::find()->distinct()->joinWith('fichas.dispositivo.empresa.users')->where('empresa_user.usu_id=:usu AND trabajador.tra_id=:id',[':usu'=>Yii::$app->user->identity->primaryKey,':id'=>$id])->one();
		if($model!==null){
			return $model;
		}else{
			throw new \yii\web\HttpException(401, 'No esta autorizado a acceder a la información del trabajador, este debe estar relacionado a traves de una evaluación.');
		}
	}

	public function actionViewtrabajadorfichas($id)
	{		
		$model = \app\modules\v1\models\RvFicha::find()
			->joinWith('dispositivo.empresa.users')
			->andWhere('empresa_user.usu_id=:usu AND rv_ficha.trab_id=:id',[':usu'=>Yii::$app->user->identity->primaryKey,':id'=>$id]);
			
		$data = new \yii\data\ActiveDataProvider([
			'query' => $model,
		  	'pagination' => [
				'defaultPageSize' => (int)Yii::$app->request->get('perPage',20),
			],
		]);
		return $data;
	}

	public function actionCreatetrabajador()
	{
		$request = Yii::$app->request;
		if($request->post()){
			$rut=$request->post('rut');
			if(\app\modules\v1\models\Trabajador::validaRUT($rut)){
				$_POST['rut']=\app\modules\v1\models\Trabajador::formatRUT($rut);
				$rut=$_POST['rut'];
			}
			$model=\app\modules\v1\models\Trabajador::findOne(['rut'=>$rut]);
			if($model===null){
				$model=new \app\modules\v1\models\Trabajador();
			}
			$model->attributes=$request->post();
			$model->isRUT();
			if($model->save()){
				return $model;
			}else{
				return $model;
			}
		}
	}
}