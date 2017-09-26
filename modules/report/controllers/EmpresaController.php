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
    		$mpdf->setFooter('Página {PAGENO}');
        	$mpdf->showImageErrors = true;
    		$mpdf->WriteHTML(file_get_contents(\Yii::getAlias('@webroot').DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'bootstrap.cerulean.min.css'),1);
        	$mpdf->WriteHTML($this->renderPartial('ficha/pdf',['model'=>$model],true));
    		// $mpdf->SetProtection(array('print', 'print-highres'), 'asd', md5(time()), 128);
    		// $mpdf->autoScriptToLang = true;
    		// $mpdf->autoLangToFont = true;
    		$mpdf->Output("Ficha de evaluación Nro  ".$model->primaryKey.".pdf","I");
        }
        else
        {
          throw new \yii\web\HttpException(404, 'No existe la ficha de evaluación solicitada.');
        }
    }

}