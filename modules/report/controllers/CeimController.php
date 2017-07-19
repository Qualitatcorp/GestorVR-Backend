<?php
namespace app\modules\report\controllers;

use yii\web\Controller;

class CeimController extends Controller
{
    public function actionIndex()
    {
    	//numero de ficha Parametro
     
     	$head = $this->renderPartial('reporte/_head');
     	$style =  file_get_contents( './css/ceim.css');
    	$mpdf = new \mPDF();
    	$mpdf->charset_in = 'utf-8';
    	$mpdf->WriteHTML($style,1);
		$mpdf->WriteHTML($head);
		$mpdf->Output();

		  
         
          
           
             
    }
}
