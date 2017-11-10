<?php

namespace app\modules\report\controllers;

use yii\web\Controller;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

class EmpresaController extends Controller
{
    public function behaviors()
    {
        return \yii\helpers\ArrayHelper::merge(parent::behaviors(),[
            'authenticator'=>[
    	        'class' => CompositeAuth::className(),
		        'authMethods' => [
		            HttpBearerAuth::className(),
		            QueryParamAuth::className(),
		        ]
                // 'class' => \yii\filters\auth\HttpBearerAuth::className() 
            ],
            'authorization'=>[
                'class' => \app\components\Authorization::className()
            ],        
         //    'corsFilter'  => [
	        //     'class' => \yii\filters\Cors::className(),
	        //     'cors'  => [
	        //         'Origin'                           => ['*'],
	        //         'Access-Control-Request-Method'    => ['GET'],
	        //         'Access-Control-Allow-Credentials' => true,
	        //         'Access-Control-Max-Age'           => 3600,// Cache (seconds)
	        //     ],
	        // ],
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
                case 'EXTINTOR_1':
                    $this->extintor1($ficha);
                    break;
                case 'NO':
                default:
                    throw new \yii\web\HttpException(404, 'Esta evaluación, no tiene reporte.');
                break;
            }
        }
    }

    public function extintor1($ficha)
    {
        /*
         * Evaluación 57 | 18891 
         */
        
        // $mpdf = new \mPDF('utf-8', array(148,297),0,'',6.8,6.5);
        $mpdf = new \Mpdf\Mpdf([
            'mode'=>'utf-8',
            'format'=>[148,297],
            'margin_left'=>6.8,
            'margin_right'=>6.5
        ]);
        $mpdf->debug = false;
        $mpdf->SetTitle('Reporte VR Firex '.$ficha->primaryKey);
        $mpdf->WriteHTML($this->renderPartial('extintor/reporte1',array('ficha'=>$ficha),true));
        $mpdf->Output('FICHA Nro. '.$ficha->primaryKey.'.pdf','I');
    }

    private function normal($ficha)
    {
        /*
         * Evaluación NORMAL | 16400
         */       
        $mpdf = new \Mpdf\Mpdf([
            'mode'=>'utf-8',
            'format'=>'Letter'
        ]);
        $mpdf->title="Ficha de evaluación";
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
         * Evaluación 50  | 17537
         */
        // $style =  file_get_contents( \Yii::getAlias('@webroot').'/css/ceim.css');
        // $mpdf->WriteHTML($style,1);
        
        $mpdf = new \Mpdf\Mpdf(['mode'=>'utf-8']);
        $mpdf->SetTitle('INFORME DE RESULTADOS SISTEMA DE EVALUACIÓN EN SEGURIDAD '.$ficha->primaryKey);
        $mpdf->WriteHTML($this->renderPartial('cert/cert1',array('ficha'=>$ficha),true));
        $mpdf->Output('FICHA Nro. '.$ficha->primaryKey.'.pdf','I');
    }

    private function cert2($ficha)
    {   
        /*
         * Evaluación 54 | 18659
         */
        if(!$ficha->getParams()->exists()){
            $ficha->resolve();
        }        
        if($ficha->calificacion===null){
                throw new \yii\web\HttpException(404, 'Esta evaluación, no tiene reporte o no a finalizado el proceso.');
        }
        $mpdf = new \Mpdf\Mpdf(['mode'=>'utf-8']);
        $mpdf->SetTitle('INFORME DE RESULTADOS SISTEMA DE EVALUACIÓN EN SEGURIDAD '.$ficha->primaryKey);
        $mpdf->WriteHTML($this->renderPartial('cert/cert2',array('ficha'=>$ficha),true));
        $mpdf->Output('FICHA Nro. '.$ficha->primaryKey.'.pdf','I');
    }


    private function cert3($ficha)
    {
        /*
         * Evaluación 53 | 18586
         */
        if(!$ficha->getParams()->exists())
        {
            $ficha->resolve();
        }     
        $mpdf = new \Mpdf\Mpdf(['mode'=>'utf-8']);
        $mpdf->SetTitle('INFORME DE RESULTADOS SISTEMA DE EVALUACIÓN EN SEGURIDAD '.$ficha->primaryKey);
        $mpdf->WriteHTML($this->renderPartial('cert/cert3',array('ficha'=>$ficha),true));
        $mpdf->Output('FICHA Nro. '.$ficha->primaryKey.'.pdf','I');
    }
}