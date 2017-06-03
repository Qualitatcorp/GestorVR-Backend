<?php

namespace app\modules\report\controllers;

use yii\web\Controller;

class DefaultController extends Controller
{
    public function actionIndex()
    {
    	$mpdf = new \mPDF();
		$mpdf->WriteHTML('Hola Mundo');
		$mpdf->Output();
    }
}
