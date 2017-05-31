<?php 

namespace app\controllers;

use yii;
use app\models\user\User;
use yii\rest\Controller;

class AuthenticationController extends Controller
{
	public function actionToken()
	{
		return [
			'POST'=>$_POST,
			'FILES'=>$_FILES,
			'GET'=>$_GET,
			'COOKIE'=>$_COOKIE,
			'REQUEST'=>$_REQUEST
		];

		return User::TokenByCredentials();
	}

	public function actionRefresh()
	{
		return User::RefreshToken();
	}
}