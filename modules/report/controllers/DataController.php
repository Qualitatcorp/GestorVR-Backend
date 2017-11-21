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

    public function actionEnap($id)
    {
                $trabajadoresData=\app\modules\v1\models\Trabajador::find()
            ->where("tra_id IN (SELECT DISTINCT trab_id FROM rv_ficha WHERE rv_ficha.eva_id=:id)",[':id'=>$id])
            ->all();        
        $fichasData=\app\modules\v1\models\RvFicha::find()
            ->where(['eva_id'=>$id])
            ->orderBy(['creado'=>SORT_DESC])
            ->all();
        $paramsData=\app\modules\v1\models\RvFichaParams::find()
            ->where("fic_id IN (SELECT fic_id FROM rv_ficha WHERE rv_ficha.eva_id=:id)",[':id'=>$id])
            ->all();
            // ->createCommand()->rawSql;
        $trabajadores=\yii\helpers\ArrayHelper::index($trabajadoresData, 'tra_id');
        $fichas=\yii\helpers\ArrayHelper::index($fichasData, null, 'trab_id');
        $params=\yii\helpers\ArrayHelper::index($paramsData, 'fic_id');

        /*
         *  Cross data & headers
         */
        $data=[[
            '#',
            'RUT',
            'Nombre',
            'Paterno',
            'Materno',
            'Empresa',

            'Ficha',
            'Fecha Acreditación',
            'Percepción',
            'Conocimiento',
            'Psicologico',
            'Nota Final',
            // 'Califica',
        ]];
        $i=0;
        foreach ($trabajadores as $tra_id => $trabajador) {
            $column=[
                ++$i,                
                $trabajador->rut,
                $trabajador->nombre,
                $trabajador->paterno,
                $trabajador->materno,
                $trabajador->gerencia
            ];
            foreach ($fichas[$tra_id] as $ficha) 
            {
                    $d=$params[(int)$ficha->fic_id]->data;
                    array_push($column,
                        $ficha->fic_id,
                        $ficha->creado,
                        isset($d["dec_nota"])?$d["dec_nota"]:"N/A",
                        isset($d["pre_nota"])?$d["pre_nota"]:"N/A",
                        isset($d["psi_nota"])?$d["psi_nota"]:"N/A",
                        isset($d["nota"])?$d["nota"]:"N/A"
                    );
            }
            $data[]=$column;
        }

        /*
         *  Create Sheet with data
         */
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Ruben Eduardo Tejeda Roa")
                             ->setLastModifiedBy("Ruben Eduardo Tejeda Roa")
                             ->setTitle("Evaluaciones ENAP 54 - GestorVR")
                             ->setSubject("Evaluaciones ENAP 54 - GestorVR")
                             ->setDescription("Detalle de evaluacion ENAP")
                             ->setKeywords("gestorvr qualitatcorp chile enap")
                             ->setCategory("resultado informe enap");
        $objPHPExcel->getActiveSheet()->fromArray($data,null,'A1');
        foreach (range('I', 'L') as $columnID)
        {
            $objPHPExcel->getActiveSheet()
                ->getStyle($columnID)
                ->getNumberFormat()
    ->setFormatCode('0.000%;[Red]-0.000%');        }
        /*
         *  expand celds 
         */
        foreach(range('A','L') as $columnID)
        {
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }

        $objPHPExcel->getActiveSheet()->setTitle('Informe');

        /*
         *  Call services for downloader
         */
        $this->DownloadServicesExcel($objPHPExcel,'Informe');
    }

    public function actionMel($id)
    {
        $trabajadoresData=\app\modules\v1\models\Trabajador::find()
            ->where("tra_id IN (SELECT DISTINCT trab_id FROM rv_ficha WHERE rv_ficha.eva_id=:id)",[':id'=>$id])
            ->all();        
        $fichasData=\app\modules\v1\models\RvFicha::find()
            ->where(['eva_id'=>$id])
            ->orderBy(['creado'=>SORT_DESC])
            ->all();
        $paramsData=\app\modules\v1\models\RvFichaParams::find()
            ->where("fic_id IN (SELECT fic_id FROM rv_ficha WHERE rv_ficha.eva_id=:id)",[':id'=>$id])
            ->all();
            // ->createCommand()->rawSql;
        $trabajadores=\yii\helpers\ArrayHelper::index($trabajadoresData, 'tra_id');
        $fichas=\yii\helpers\ArrayHelper::index($fichasData, null, 'trab_id');
        $params=\yii\helpers\ArrayHelper::index($paramsData, 'fic_id');

        /*
         *  Cross data & headers
         */
        $data=[[
            '#',
            'RUT',
            'Nombre',
            'Paterno',
            'Materno',
            'Empresa',

            'Ficha',
            'Fecha Acreditación',
            // 'Percepción',
            // 'Conocimiento',
            'Nota Final',
            // 'Califica',

            'Ficha',
            'Fecha Recreditación 1',
            // 'Percepción',
            // 'Conocimiento',
            'Nota Final',
            // 'Califica',

            'Ficha',
            'Fecha Acreditación 2',
            // 'Percepción',
            // 'Conocimiento',
            'Nota Final',
            // 'Califica'
        ]];
        $i=0;
        foreach ($trabajadores as $tra_id => $trabajador) {
            $column=[
                ++$i,                
                $trabajador->rut,
                $trabajador->nombre,
                $trabajador->paterno,
                $trabajador->materno,
                $trabajador->gerencia
            ];
            foreach ($fichas[$tra_id] as $ficha) 
            {
                    $d=$params[(int)$ficha->fic_id]->data;
                    array_push($column,
                        $ficha->fic_id,
                        $ficha->creado,
                        isset($d["nota"])?$d["nota"]:"N/A"
                    );
            }
            $data[]=$column;
        }

        /*
         *  Create Sheet with data
         */
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Ruben Eduardo Tejeda Roa")
                             ->setLastModifiedBy("Ruben Eduardo Tejeda Roa")
                             ->setTitle("Planilla Masiva - GestorVR")
                             ->setSubject("Documento toda la información del proceso de MEL")
                             ->setDescription("Resumen de datos MEL.")
                             ->setKeywords("gestorvr qualitatcorp chile")
                             ->setCategory("resultado informe");
        $objPHPExcel->getActiveSheet()->fromArray($data,null,'A1');
        
        /*
         *  expand celds 
         */
        foreach(range('a','o') as $columnID)
        {
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }

        $objPHPExcel->getActiveSheet()->setTitle('Informe');

        /*
         *  Call services for downloader
         */
        $this->DownloadServicesExcel($objPHPExcel,'Informe');
    }

    public function DownloadServicesExcel(\PHPExcel $objPHPExcel,string $name="reports",$type="xlsx")
    {
        // .ods    OpenDocuemnt spreadsheet document   application/vnd.oasis.opendocument.spreadsheet
        // .pdf    Adobe Portable Document Format (PDF)    application/pdf
        // .xls    Microsoft Excel application/vnd.ms-excel
        // .xlsx     application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
        $request=\yii::$app->request;
        $type= $request->get('type',$type);
        $name= $request->get('name',$name);
        $accept=['ods','xls','xlsx'];
        if(in_array($type, $accept))
        {
            $mimeType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
            $render="Excel2007";
            switch ($type) {
                case 'ods':
                    $mimeType="application/vnd.oasis.opendocument.spreadsheet";
                    $render="OpenDocument";
                    break;
                case 'xls':
                    $mimeType="application/vnd.ms-excel";
                    $render="Excel5";
                    break;
            }
            header('Content-Type: '.$mimeType);
            header('Content-Disposition: attachment;filename="'.$name.'.'.$type.'"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, $render);
            $objWriter->save('php://output');
        }
    }
}