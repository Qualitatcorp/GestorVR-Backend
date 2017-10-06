<?php

namespace app\modules\v1\models;

use Yii;

class RvFicha extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'rv_ficha';
    }

    public function rules()
    {
        return [
            [['eva_id', 'trab_id', 'disp_id'], 'required'],
            [['eva_id', 'trab_id', 'pro_id', 'disp_id', 'pais_id'], 'integer'],
            [['calificacion'], 'number'],
            [['creado'], 'safe'],
            [['creado'], 'date', 'format' => 'yyyy-M-d H:m:s'],
            [['eva_id'], 'exist', 'skipOnError' => true, 'targetClass' => RvEvaluacion::className(), 'targetAttribute' => ['eva_id' => 'eva_id']],
            [['trab_id'], 'exist', 'skipOnError' => true, 'targetClass' => Trabajador::className(), 'targetAttribute' => ['trab_id' => 'tra_id']],
            [['pro_id'], 'exist', 'skipOnError' => true, 'targetClass' => RvProyecto::className(), 'targetAttribute' => ['pro_id' => 'pro_id']],
            [['disp_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dispositivo::className(), 'targetAttribute' => ['disp_id' => 'dis_id']],
            [['pais_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pais::className(), 'targetAttribute' => ['pais_id' => 'pais_id']],
            ['disp_id','verifyDispositivo']
        ];
    }

    public function attributeLabels()
    {
        return [
            'fic_id' => 'Fic ID',
            'eva_id' => 'Eva ID',
            'trab_id' => 'Trab ID',
            'pro_id' => 'Pro ID',
            'disp_id' => 'Disp ID',
            'calificacion' => 'Calificacion',
            'pais_id' => 'Pais ID',
            'creado' => 'Creado',
            
        ];
    }

    public function extraFields()
    {
        return [
            'trabajador',
            'params',
            'proyecto',
            'evaluacion',
            'proyecto',
            'dispositivo',
            'empresa',
            'pais',
            'preguntas',
            'alternativas',
            'respuestas',
            'items',
            'ceim',
            'recursos',
            'src',
            'reacreditacion',
            'clientscalificacion',
            'clientsparams',
            'summary'
        ];
    }

    public function verifyDispositivo($attribute, $params)
    {
        $dispositivo=$this->dispositivo;
        if($dispositivo!==null)
        {
            if(!$dispositivo->permission)
            {
                $this->addError($attribute,'El dispositivo, no se encuentra habilitado');
            }
        }
    }

    public function getEvaluacion()
    {
        return $this->hasOne(RvEvaluacion::className(), ['eva_id' => 'eva_id']);
    }

    public function getTrabajador()
    {
        return $this->hasOne(Trabajador::className(), ['tra_id' => 'trab_id']);
    }

    public function getProyecto()
    {
        return $this->hasOne(RvProyecto::className(), ['pro_id' => 'pro_id']);
    }

    public function getDispositivo()
    {
        return $this->hasOne(Dispositivo::className(), ['dis_id' => 'disp_id']);
    }

    public function getEmpresa()
    {
        return $this->hasOne(Empresa::className(), ['emp_id' => 'emp_id'])->via('dispositivo');
    }

    public function getPais()
    {
        return $this->hasOne(Pais::className(), ['pais_id' => 'pais_id']);
    }

    public function getClientscalificacion(){
          return $this->hasMany(RvClientCalificacion::className(), ['fic_id' => 'fic_id']);
    }

    public function getClientsparams(){
          return $this->hasMany(RvClientParams::className(), ['fic_id' => 'fic_id']);
    }

    public function getRespuestas()
    {
        return $this->hasMany(RvRespuesta::className(), ['fic_id' => 'fic_id']);
    }

    public function getAlternativas()
    {
        return $this->hasMany(RvAlternativa::className(), ['alt_id' => 'alt_id'])->via('respuestas');
    }
    
    public function getPreguntas()
    {
        return $this->hasMany(RvPregunta::className(), ['pre_id' => 'pre_id'])->via('alternativas');
    }

    public function getItems()
    {
        return $this->hasMany(RvItem::className(), ['ite_id' => 'ite_id'])->via('preguntas');
    }

    public function getParams()
    {
        return $this->hasOne(RvFichaParams::className(), ['fic_id' => 'fic_id']);
    }

    public function getRecursos()
    {
        return $this->hasMany(RvFichaRecursos::className(), ['fic_id' => 'fic_id']);
    }

    public function getSrc()
    {
        return $this->hasMany(RecursosSources::className(), ['id' => 'src_id'])->via('recursos');
    }

    public function getPhoto()
    {
        return $this->getRecursos()->andWhere(['tipo'=>'PERFIL']);
    }

    public function getReacreditacion()
    {
        $query = new \yii\db\Query;
        $query->select('min(creado) as creado')
            ->from('rv_ficha')
            ->where(['trab_id' =>  $this->trab_id, 'eva_id' => $this->eva_id ]);        
        $command = $query->createCommand();
        $rows = $command->queryAll();
        if($rows[0]['creado'] == $this->creado){
            return false;
        }else{
            return true;
        }      
    }

    /*
     * Trabajo con la nota de la ficha de evaluacion
     */

    public function Resolve()
    {
        $evaluacion=$this->evaluacion;
        switch ($evaluacion->nota) {
            case 'INTERNA_PLANA':
            case 'EXTERNA_SIMPLE':
            case 'EXTERNA_COMPLEJA':
            case 'COMPUESTA_SIMPLE':
                break;
            case 'INTERNA_SIMPLE':
                $this->NotaSimple();
                break;
            case 'INTERNA_COMPLEJA':
            case 'COMPUESTA_COMPLEJA':
                $this->NotaCompleja();
                break;
            default:
                break;
        }
    }

    public function getSummary()
    {
        $evaluacion=$this->evaluacion;
        if($evaluacion->getItems()->exists())
        {
            $items=$evaluacion->items;
            $criteria = [];
            $preguntas=$evaluacion->preguntas;
            foreach ($items as $item) {
                $criteria[$item->nombre]=array_column(array_filter($preguntas,function($pregunta) use ($item)
                {
                    return $pregunta->ite_id==$item->primaryKey;
                }),'primaryKey');
            }
            $params=[];
            $alternativas=$this->alternativas;
            foreach ($criteria as $key => $preguntas) {
                $params[$key]=[
                    'total'=>0,
                    'acierto'=>0
                ];
                foreach ($alternativas as $a) {
                    if(in_array($a->pre_id, $preguntas)){
                        $params[$key]['total']++;
                        if($a->correcta==='SI')$params[$key]['acierto']++;
                    }
                }
            }
            return $params;
        }
    }

    public function NotaCompleja()
    {
        /*
         * Se almacenan los parametros de conteno de los items en un params
         */
        $params=$this->params;
        if($params===null){
            $params=new RvFichaParams();
            $params->fic_id=$this->primaryKey;
            $params->data=$this->summary;
            $params->save();
        }
        $data=$params->data;
        /*
         * Se calcula la nota de le evaluacion INTERNA COMPLEJA
         */
        $flag=true;
        foreach ($data as $value) {
            if($value['total']==0)$flag=false;
        }
        if($flag){
            switch ($this->eva_id) {
                case 50:
                    $dec_nota=($data['PRI_DEC']['acierto']/$data['PRI_DEC']['total'])*0.75
                             +($data['PRI_DIS']['acierto']/$data['PRI_DIS']['total'])*0.1125
                             +($data['SEC_DEC']['acierto']/$data['SEC_DEC']['total'])*0.1
                             +($data['SEC_DIS']['acierto']/$data['SEC_DIS']['total'])*0.0375;
                    // $pre_nota=$data['PREGUNTA']['acierto']/$data['PREGUNTA']['total'];
                    $pre_nota=$data['PREGUNTA']['acierto']/20;
                    $nota=(float)number_format(($dec_nota+$pre_nota)/2,2);
                    $data['dec_nota']=(float)number_format($dec_nota,2);
                    $data['pre_nota']=(float)number_format($pre_nota,2);
                    $data['nota']=(float)number_format($nota,2);
                    $params->data=$data;
                    $params->save();
                    $this->calificacion=$nota;
                    $this->save();
                    break;
                case 53:
                case 54:
                    $dec_nota=($data['PRINCIPAL']['acierto']/$data['PRINCIPAL']['total'])*0.75
                             +($data['SECUNDARIO']['acierto']/$data['SECUNDARIO']['total'])*0.15
                             +($data['DISTRACTOR']['acierto']/$data['DISTRACTOR']['total'])*0.1;                             
                    $pre_nota=$data['PREGUNTA']['acierto']/$data['PREGUNTA']['total'];
                    $nota=(float)number_format(($dec_nota+$pre_nota)/2,2);
                    $data['dec_nota']=(float)number_format($dec_nota,2);
                    $data['pre_nota']=(float)number_format($pre_nota,2);
                    $data['nota']=(float)number_format($nota,2);
                    $params->data=$data;
                    $params->save();
                    $this->calificacion=$nota;
                    $this->save();
                    break;
                default:
                    break;
            }
        }
    }

    public function NotaSimple()
    {
        $alternativas=$this->alternativas;
        $total=0;
        $acierto=0;
        foreach ($alternativas as $alternativa) {
            $total+=$alternativa->ponderacion;
            if($alternativa->correcta=='SI')$acierto+=$alternativa->ponderacion;
        }
        $this->calificacion=$acierto/$total;
        $this->save();
    }

    public function getCeim()
    {
        if($this->eva_id==50)
        {
            $params=$this->params;
            if($params===null){
                $this->NotaCompleja();
            }else{
                // return $params->data;
                $data=$params->data;
                if(!isset($data['dec_nota']))
                    return "SIN NOTA DEC_NOTA";
                if(!isset($data['dec_nota']))
                    return "SIN NOTA DEC_NOTA";
                if(!isset($data['dec_nota']))
                    return "SIN NOTA DEC_NOTA";
                else{
                    return 
                    [
                        "pri_cantidad"=>$data['PRI_DEC']['acierto'],
                        "sec_cantidad"=>$data['SEC_DEC']['acierto'],
                        "pre_cantidad"=>$data['PREGUNTA']['acierto'],
                        "pre_nota"=>$data['pre_nota'],
                        "dec_nota"=>$data['dec_nota'],
                        "summary"=>
                        [
                            'nota'=>$data['nota']
                        ]
                    ];
                    
                }
            }
        }
    }
}