<?php

namespace app\modules\report\controllers;

use yii\web\Controller;
use app\modules\v1\models\RvFicha;
// use yii\filters\auth\CompositeAuth;
// use yii\filters\auth\HttpBearerAuth;
// use yii\filters\auth\QueryParamAuth;

class DataController extends Controller
{
    // public function behaviors()
    // {
    //     return \yii\helpers\ArrayHelper::merge(parent::behaviors(),[
    //         'authenticator'=>[
    //             // 'class' => \yii\filters\auth\HttpBearerAuth::className() 
    //             'class' => CompositeAuth::className(),
    //             'authMethods' => [
    //                 HttpBearerAuth::className(),
    //                 QueryParamAuth::className(),
    //             ]
    //         ],
    //         'authorization'=>[
    //             'class' => \app\components\Authorization::className()
    //         ],        
    //      //    'corsFilter'  => [
	   //      //     'class' => \yii\filters\Cors::className(),
	   //      //     'cors'  => [
	   //      //         'Origin'                           => ['*'],
	   //      //         'Access-Control-Request-Method'    => ['GET'],
	   //      //         'Access-Control-Allow-Credentials' => true,
	   //      //         'Access-Control-Max-Age'           => 3600,// Cache (seconds)
	   //      //     ],
	   //      // ],
    //     ]);
    // }

    public function actionIndex()
    {
        return 'Its Works!';
    }

    public function actionMel($id)
    {
        $trabajadores=\app\modules\v1\models\Trabajador::find()
            ->joinWith(['fichas.params'])
            ->where(['eva_id'=>$id])
            ->asArray(true)
            ->all();
        echo json_encode($trabajadores);
        // $trabajadores=\app\modules\v1\models\Trabajador::findAll(array_values(array_unique(array_column($fichas, 'trab_id'))));
        // echo json_encode($trabajadores);
        // $reacreditacion=array();
        // foreach ($models as $key => $value) {
        //     $reacreditacion[$value->primaryKey]=$value->reacreditacion;
        // }
        // echo json_encode($reacreditacion);

        // $model=RvFicha::findOne(18333);
        // var_dump($model->getAttributes(['eva_id','trab_id']));
        // $compare=RvFicha::find()->where($model->getAttributes(['eva_id','trab_id']))->min('fic_id');

        // var_dump($compare);
        // $objPHPExcel = new \PHPExcel();
        // $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
        //                      ->setLastModifiedBy("Maarten Balliauw")
        //                      ->setTitle("PHPExcel Test Document")
        //                      ->setSubject("PHPExcel Test Document")
        //                      ->setDescription("Test document for PHPExcel, generated using PHP classes.")
        //                      ->setKeywords("office PHPExcel php")
        //                      ->setCategory("Test result file");
        //  $objPHPExcel->setActiveSheetIndex(0)
        //     ->setCellValue('A1', 'Hello')
        //     ->setCellValue('B2', 'world!')
        //     ->setCellValue('C1', 'Hello')
        //     ->setCellValue('D2', 'world!');
        // $objPHPExcel->setActiveSheetIndex(0)
        //             ->setCellValue('A4', 'Miscellaneous glyphs')
        //             ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');
        // $objPHPExcel->getActiveSheet()->setTitle('Simple');
        // $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment;filename="reports.xlsx"');
        // header('Cache-Control: max-age=0');
        // ob_end_clean();
        // $objWriter->save('php://output');
    }
}