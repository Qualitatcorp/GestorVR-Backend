<?php
namespace app\modules\report\controllers;
use app\modules\v1\models\RvFicha;
use yii\web\Controller;

class CeimController extends Controller
{
    public function actionIndex()
    {
    	//numero de ficha Parametro
        $ficha = RvFicha::findOne(17543);
         
        echo 'se';

     	$head = $this->renderPartial('reporte/_head',array('ficha'=>$ficha),true);
     	$style =  file_get_contents( './css/ceim.css');
    	$mpdf = new \mPDF();
    	$mpdf->charset_in = 'utf-8';
    	$mpdf->WriteHTML($style,1);
		$mpdf->WriteHTML($head);
		$mpdf->Output();

		  
         
          
           
             
    }
}
