<?php 

namespace app\components;

use Yii;
use yii\base\Action;
use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;

/**
* Access
*/
class Authorization extends ActionFilter
{
		public function beforeAction($action)
		{
				if(static::CheckAccess()){
					return true;
				}else{
					throw new ForbiddenHttpException(Yii::t('yii', 'No tienes acceso a este recurso del api rest'));
				}
		}
		public static function CheckAccess($user_id=null,$resource=null)
		{
			if(empty($resource)){
				if(empty($user_id)){
					$user_id=Yii::$app->user->id;
					$resource[]=Yii::$app->controller->module->id;
					$resource[]=Yii::$app->controller->id;
					$resource[]=Yii::$app->controller->action->id;
				}else{
					$resource=$user_id;
					$user_id=Yii::$app->user->identity->getId();
				}
			}
			static::registerResource($resource);
			return Yii::$app->db->createCommand("CALL sp_access_resource_user(:user_id, :resource)")
							->bindValue(':user_id' , $user_id)
							->bindValue(':resource',static::SerializeResource($resource))
							->queryOne()['PERMISSION']==='ALLOW';
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