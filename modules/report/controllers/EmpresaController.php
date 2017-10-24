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
        /*
         * Busca la fichas de evaluacion relacionada con la empresa
         */
        $ficha = \app\modules\v1\models\RvFicha::find()
            // ->joinWith('dispositivo.empresa.users')
            // ->andWhere('empresa_user.usu_id=:usu AND rv_ficha.fic_id=:id',[':usu'=>\Yii::$app->user->identity->primaryKey,':id'=>$id])
            ->andWhere('rv_ficha.fic_id=:id',[':id'=>$id])
            ->one();

        if(!$ficha)
        {
            throw new \yii\web\HttpException(404, 'No existe la ficha de evaluación solicitada o no tiene los permisos para ver esta evaluación.');
        }
        else
        {
            /*
             * Delega dependiendo el tipo de reporte cual mostrara
             */
            $reporte = $ficha->evaluacion->reporte;
            switch($reporte) {
                case 'NORMAL':
                    $this->normal($ficha);
                    break;
                case 'CERT_1':
                    $this->cert1($ficha);
                    break;
                break;
                case 'CERT_2':
                    $this->cert2($ficha);
                    break;
                case 'CERT_3':
                    $this->cert3($ficha);
                    break;
                break;
                case 'NO':
                default:
                    throw new \yii\web\HttpException(404, 'Esta evaluación, no tiene reporte.');
                break;
            }
        }
    }

    private function normal($ficha)
    {
        /*
         * Evaluación NORMAL 
         */       
        $mpdf = new \mPDF('utf-8','Letter');
        $mpdf->title="Ficha de evaluación";
        $mpdf->charset_in = 'utf-8';
        // $mpdf->debug = true; 
        $mpdf->setFooter('Página {PAGENO}');
        $mpdf->WriteHTML(file_get_contents( \Yii::getAlias('@webroot').'/css/bootstrap.cerulean.min.css'),1);
        $mpdf->WriteHTML($this->renderPartial('ficha/normal',['model'=>$ficha],true));
        // $mpdf->SetProtection(array('print', 'print-highres'), 'contraseña', md5(time()), 128);
        // $mpdf->autoScriptToLang = true;
        // $mpdf->autoLangToFont = true;
        $mpdf->Output('FICHA Nro. '.$ficha->primaryKey.'.pdf','I');
    }

    private function cert1($ficha)
    {
        /*
         * Evaluación 50  
         */
        // $style =  file_get_contents( \Yii::getAlias('@webroot').'/css/ceim.css');
        // $mpdf->WriteHTML($style,1);
        
        $mpdf = new \mPDF();
        // $mpdf->debug = true; 
        $mpdf->charset_in = 'utf-8';
        $mpdf->SetTitle('INFORME DE RESULTADOS SISTEMA DE EVALUACIÓN EN SEGURIDAD '.$ficha->primaryKey);
        $mpdf->WriteHTML($this->renderPartial('cert/cert1',array('ficha'=>$ficha),true));
        $mpdf->Output('FICHA Nro. '.$ficha->primaryKey.'.pdf','I');
    }

    private function cert2($ficha)
    {   
        /*
         * Evaluación 54
         */
        if(!$ficha->getParams()->exists()){
            $ficha->resolve();
        }
        
        if($ficha->calificacion===null){
                throw new \yii\web\HttpException(404, 'Esta evaluación, no tiene reporte o no a finalizado el proceso.');
        }
        $mpdf = new \mPDF();
        // $mpdf->debug = true;
        $mpdf->charset_in = 'utf-8';
        $mpdf->SetTitle('INFORME DE RESULTADOS SISTEMA DE EVALUACIÓN EN SEGURIDAD '.$ficha->primaryKey);
        $mpdf->WriteHTML($this->renderPartial('cert/cert2',array('ficha'=>$ficha),true));
        $mpdf->Output('FICHA Nro. '.$ficha->primaryKey.'.pdf','I');
    }


    private function cert3($ficha)
    {
        /*
         * Evaluación 53
         */
        if(!$ficha->getParams()->exists())
        {
            $ficha->resolve();
        }     
        $mpdf = new \mPDF();
        // $mpdf->debug = true;
        $mpdf->charset_in = 'utf-8';
        $mpdf->SetTitle('INFORME DE RESULTADOS SISTEMA DE EVALUACIÓN EN SEGURIDAD '.$ficha->primaryKey);
        $mpdf->WriteHTML($this->renderPartial('cert/cert3',array('ficha'=>$ficha),true));
        $mpdf->Output('FICHA Nro. '.$ficha->primaryKey.'.pdf','I');
    }
}