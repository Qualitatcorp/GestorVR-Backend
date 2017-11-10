<?php 

namespace app\components;

use Yii;
use yii\base\Action;
use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;

/**
* 	Acceso a los recursos
*/
class Authorization extends ActionFilter
{
		public function beforeAction($action)
		{
			if(static::CheckAccess()){
				return true;
			}else{
				throw new ForbiddenHttpException(Yii::t('yii', 'No estas autorizado : ').static::Resource());
			}
		}

		public static function Resource()
		{
			return static::SerializeResource([
				Yii::$app->controller->module->id,
				Yii::$app->controller->id,
				Yii::$app->controller->action->id
			]);
		}

		public static function CheckAccess($resource=null,$user_id=null)
		{
			if(empty($user_id))
			{
				$user_id=Yii::$app->user->id;
			}

			if(empty($resource))
			{
				$resource=static::Resource();
			}
			else
			{
				$resource=static::SerializeResource($resource);
			}
			static::registerResource($resource);
			return Yii::$app->user->identity->has($resource);

			// if(Yii::$app->db->createCommand("CALL sp_access_resource_user(:user_id, :resource)")
			// 				->bindValue(':user_id' , $user_id)
			// 				->bindValue(':resource',static::SerializeResource($resource))
			// 				->queryOne()['PERMISSION']==='ALLOW')
		}

		public static function SerializeResource($resource)
		{
			return (is_array($resource))?implode("_", $resource):$resource;
		}

		public static function registerResource($resource)
		{
			$new_resource=static::SerializeResource($resource);
			Yii::$app->db->createCommand("CALL sp_resource_register(:new_resource);")->bindValue(':new_resource',$new_resource)->execute();
			Yii::$app->db->createCommand("CALL sp_access_permission('RESOURCE',:new_resource,'ALLOW');")->bindValue(':new_resource',$new_resource)->execute();
		}
}