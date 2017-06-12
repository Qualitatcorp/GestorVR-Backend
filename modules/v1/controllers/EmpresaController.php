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

	public function actionSearch()
	{
		if (!empty($_GET)) {
			$request=Yii::$app->request;
			$reserve=['page','index','order','limit'];
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
				$id=($request->get('index'))?$request->get('index'):'id';
				$sort=($request->get('order')=='asc')?SORT_ASC:SORT_DESC;
				$provider = new \yii\data\ActiveDataProvider([
					'query' => $query,
					'sort' => [
						'defaultOrder' => [
							$id=>$sort
						]
					],
				  	'pagination' => [
						'defaultPageSize' => 20,'page'=>(isset($_GET['page']))?intval($_GET['page'])-1:0
					],
				]);
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

	public function actionIndexficha()
	{
		// $query =\app\modules\v1\models\RvFicha::find();
		$query =\app\modules\v1\models\RvFicha::find()->joinWith('dispositivo.empresa.users')->where('empresa_user.id=:id',[':id'=>Yii::$app->user->identity->primaryKey]);
		// return $query->createCommand()->rawSql;
		$data = new \yii\data\ActiveDataProvider([
			'query' => $query,
			// 'sort' => [
			// 	"attributes"=>[
			// 		'-fic_id'
			// 	]
			// ],
		  	'pagination' => [
				'defaultPageSize' => (int)Yii::$app->request->get('perPage',20),
				// 'page'=>(int)$request->get('page',0)
			],
		]);
		return $data;
	}

	public function actionViewficha($id)
	{
		$model = \app\modules\v1\models\RvFicha::find()
			->joinWith('dispositivo.empresa.users')
			->andWhere('empresa_user.id=:usu AND rv_ficha.fic_id=:id',[':usu'=>Yii::$app->user->identity->primaryKey,':id'=>$id])
			->one();
			// ->createCommand()->rawSql;
		if($model!==null){
			return $model;
		}else{
			throw new \yii\web\HttpException(401, 'No esta autorizado a acceder a la información del trabajador, este debe estar relacionado a traves de una evaluación.');
		}
	}

	public function actionIndextrabajador()
	{
		$query =\app\modules\v1\models\Trabajador::find()
		->distinct()
		->joinWith('fichas.dispositivo.empresa.users')
		->where('empresa_user.id=:id',[':id'=>Yii::$app->user->identity->primaryKey]);
		$data = new \yii\data\ActiveDataProvider([
			'query' => $query,
		  	'pagination' => [
				'defaultPageSize' => (int)Yii::$app->request->get('perPage',20),
			],
		]);
		return $data;
	}



	public function actionViewtrabajador($id)
	{		
		$model = \app\modules\v1\models\Trabajador::find()->distinct()->joinWith('fichas.dispositivo.empresa.users')->where('empresa_user.id=:usu AND trabajador.tra_id=:id',[':usu'=>Yii::$app->user->identity->primaryKey,':id'=>$id])->one();
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
			->andWhere('empresa_user.id=:usu AND rv_ficha.trab_id=:id',[':usu'=>Yii::$app->user->identity->primaryKey,':id'=>$id]);
			
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
			$model=\app\modules\v1\models\Trabajador::findOne(['rut'=>$request->post('rut')]);
			if($model===null){
				$model=new \app\modules\v1\models\Trabajador();
			}
			$model->attributes=$request->post();
			if($model->save()){
				return $model;
			}else{
				return $model;
			}
		}
	}
}