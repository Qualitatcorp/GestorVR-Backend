<?php

namespace app\modules\report\controllers;

use yii\web\Controller;

class EmpresaController extends Controller
{
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
    public function actionIndex()
    {
    	return 'Its Works!';
    }
    public function actionFicha($id)
    {

        $ficha = \app\modules\v1\models\RvFicha::find()->andWhere(["fic_id" => $id])->one();
        if(!$ficha){throw new \yii\web\HttpException(404, 'No existe la ficha de evaluación solicitada.');}
        if($ficha->eva_id == 50){
            $reporte = "SEGURIDAD";
        }else{
            $reporte = $ficha->evaluacion->reporte;
        }
        
        switch($reporte) {
            case 'CERT_1':
            return $this->cert1($ficha,$id);
            break;
            case 'SEGURIDAD':
            return $this->seguridad($ficha,$id);
            break;
            default:
            return $this->normal($id);
            break;
        }
    }

    private function normal($id){ //ficha, id
       
        $model = \app\modules\v1\models\RvFicha::find()
            ->joinWith('dispositivo.empresa.users')
            ->andWhere('empresa_user.usu_id=:usu AND rv_ficha.fic_id=:id',[':usu'=>\Yii::$app->user->identity->primaryKey,':id'=>$id])
            ->andWhere('rv_ficha.fic_id=:id',[':id'=>$id])
            ->one();
        // print_r($model->getPhoto()->One()->src->Url);
        // die();
        if($model!==null)
        {
            $mpdf = new \mPDF('utf-8','Letter');
            $mpdf->title="Ficha de evaluación";
            $mpdf->debug = true; 
            $mpdf->setFooter('Página {PAGENO}');
            $mpdf->WriteHTML($this->renderPartial('cert/normal',['model'=>$model],true));
            $style =  file_get_contents( \Yii::getAlias('@webroot').'/css/bootstrap.cerulean.min.css');
            $mpdf->WriteHTML($style,1);
            // $mpdf->SetProtection(array('print', 'print-highres'), 'asd', md5(time()), 128);
            // $mpdf->autoScriptToLang = true;
            // $mpdf->autoLangToFont = true;
            $mpdf->Output("Ficha de evaluación Nro  ".$model->primaryKey.".pdf","I");
        }
    }
    private function cert1($ficha,$id){
        //18318
        
       
        $head = $this->renderPartial('cert/cert1',array('ficha'=>$ficha),true);
        $style =  file_get_contents( \Yii::getAlias('@webroot').'/css/ceim.css');
        $mpdf = new \mPDF();
        $mpdf->debug = true; 
        $mpdf->charset_in = 'utf-8';
        $mpdf->SetTitle('INFORME DE RESULTADOS SISTEMA DE EVALUACIÓN EN SEGURIDAD '.$id);
        $mpdf->WriteHTML($style,1);
        $mpdf->WriteHTML($head);
        $mpdf->Output('tims-'.$id.'.pdf','I');     
    }
    private function seguridad($ficha,$id) // enviar la ficha,$id
    {   
        // 17543;
        $head = $this->renderPartial('cert/ceim',array('ficha'=>$ficha),true);
        $style =  file_get_contents( \Yii::getAlias('@webroot').'/css/ceim.css');
        $mpdf = new \mPDF();
        $mpdf->debug = true; 
        $mpdf->charset_in = 'utf-8';
        $mpdf->SetTitle('INFORME DE RESULTADOS SISTEMA DE EVALUACIÓN EN SEGURIDAD '.$id);
        $mpdf->WriteHTML($style,1);
        $mpdf->WriteHTML($head);
        $mpdf->Output('ceim-'.$id.'pdf','I');
    }

}


