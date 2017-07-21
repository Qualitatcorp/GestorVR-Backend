<?php
namespace app\modules\report\controllers;
use app\modules\v1\models\RvFicha;
use yii\web\Controller;

class CeimController extends Controller
{
    // public function behaviors()
    // {
    //     return \yii\helpers\ArrayHelper::merge(parent::behaviors(),[
    //         'authenticator'=>[
    //             'class' => \yii\filters\auth\HttpBearerAuth::className()  
    //         ],
    //         'authorization'=>[
    //             'class' => \app\components\Authorization::className(),
    //         ],
    //     ]);
    // }

    public function actionSeguridad($id)
    {   
        // 17543
    	//numero de ficha Parametro
        $ficha = RvFicha::find()->andWhere(["fic_id" => $id, "eva_id" => '50'])->one();
       
       
        if(!empty($ficha) ){
            $head = $this->renderPartial('reporte/_head',array('ficha'=>$ficha),true);
            $style =  file_get_contents( './css/ceim.css');
            $mpdf = new \mPDF();
 
            $mpdf->charset_in = 'utf-8';

            $mpdf->SetTitle('INFORME DE RESULTADOS SISTEMA DE EVALUACIÓN EN SEGURIDAD '.$id);
            $mpdf->WriteHTML($style,1);
            $mpdf->WriteHTML($head);
            $mpdf->Output('ceim-'.$id.'pdf','I');
        }else{
            throw new \yii\web\HttpException(404, 'No existen entradas con los parametros propuestos.');
        }
       
        

     

		  
         
          
           
             
    }
   
}
