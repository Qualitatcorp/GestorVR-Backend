<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rv_ficha".
 *
 * @property string $fic_id
 * @property string $eva_id
 * @property string $trab_id
 * @property string $pro_id
 * @property string $disp_id
 * @property string $calificacion
 * @property integer $pais_id
 * @property string $creado
 *
 * @property RvEvaluacion $eva
 * @property Trabajador $trab
 * @property RvProyecto $pro
 * @property Dispositivo $disp
 * @property Pais $pais
 * @property RvRespuesta[] $rvRespuestas
 */
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
            [['eva_id'], 'exist', 'skipOnError' => true, 'targetClass' => RvEvaluacion::className(), 'targetAttribute' => ['eva_id' => 'eva_id']],
            [['trab_id'], 'exist', 'skipOnError' => true, 'targetClass' => Trabajador::className(), 'targetAttribute' => ['trab_id' => 'tra_id']],
            [['pro_id'], 'exist', 'skipOnError' => true, 'targetClass' => RvProyecto::className(), 'targetAttribute' => ['pro_id' => 'pro_id']],
            [['disp_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dispositivo::className(), 'targetAttribute' => ['disp_id' => 'dis_id']],
            [['pais_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pais::className(), 'targetAttribute' => ['pais_id' => 'pais_id']],
            ['disp_id','verifyDispositivo']
        ];
    }

    public function verifyDispositivo($attribute, $params)
    {
        $dis=$this->dispositivo;
        if($dis!==null)
        {
            if(!$dis->permission)
            {
                $this->addError($attribute,'El dispositivo, no se encuentra habilitado');
            }
        }
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
            'data',
            'proyecto',
            'evaluacion',
            'proyecto',
            'dispositivo',
            'pais',
            'respuestas',
            'alternativas',
            'ceim',
            'recursos',
            'src'
        ];
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

    public function getPais()
    {
        return $this->hasOne(Pais::className(), ['pais_id' => 'pais_id']);
    }

    public function getRespuestas()
    {
        return $this->hasMany(RvRespuesta::className(), ['fic_id' => 'fic_id']);
    }

    public function getParams()
    {
        return $this->hasOne(RvFichaParams::className(), ['fic_id' => 'fic_id']);
    }

    public function getAlternativas()
    {
        return $this->hasMany(RvAlternativa::className(), ['alt_id' => 'alt_id'])->via('respuestas');
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

    public function getCeim()
    {
        // Modificacion Especial para evaluaciones en el sistema
        if($this->primaryKey>=17680&&$this->primaryKey<=17712||$this->primaryKey>=17902&&$this->primaryKey<=17952){
            $special=[
                [17680,5,2,15,0.5333,0.75,0.65],
                [17681,8,2,11,0.7333,0.55,0.65],
                [17682,9,6,17,0.8,0.85,0.83],
                [17683,9,5,15,0.8,0.75,0.78],
                [17684,10,6,15,0.8666,0.75,0.81],
                [17685,8,10,14,0.7333,0.7,0.72],
                [17686,11,8,14,0.9333,0.7,0.82],
                [17687,10,7,14,0.8666,0.7,0.78],
                [17688,9,4,14,0.8,0.7,0.75],
                [17689,7,6,16,0.6666,0.8,0.73],
                [17690,8,8,17,0.7333,0.85,0.79],
                [17691,8,4,14,0.7333,0.7,0.72],
                [17692,9,9,17,0.8,0.85,0.83],
                [17693,10,9,18,0.8666,0.9,0.88],
                [17694,8,7,14,0.7333,0.7,0.72],
                [17695,10,8,15,0.8666,0.75,0.81],
                [17696,9,11,16,0.8,0.8,0.8],
                [17697,10,11,17,0.8666,0.85,0.86],
                [17698,7,3,20,0.6666,1,0.83],
                [17699,6,4,13,0.6,0.65,0.63],
                [17700,6,14,16,0.6,0.8,0.7],
                [17701,7,6,15,0.6666,0.75,0.81],
                [17702,10,4,15,0.8666,0.75,0.81],
                [17703,11,5,16,0.9333,0.8,0.87],
                [17704,10,7,12,0.8666,0.6,0.73],
                [17705,9,5,16,0.8,0.8,0.8],
                [17706,10,8,16,0.8666,0.8,0.83],
                [17707,8,5,12,0.7333,0.6,0.67],
                [17708,10,6,19,0.8666,0.95,0.91],
                [17709,9,8,17,0.8,0.85,0.83],
                [17710,10,11,13,0.8666,0.65,0.76],
                [17711,10,7,11,0.8666,0.55,0.71],
                [17712,9,6,17,0.8,0.85,0.83],

                [17902,10,9,11,0.83,0.55,0.69],
                [17903,9,6,13,0.75,0.65,0.7],
                [17904,7,7,16,0.58,0.8,0.69],
                [17905,9,8,10,0.75,0.5,0.63],
                [17906,10,6,16,0.83,0.8,0.82],
                [17907,7,10,13,0.58,0.65,0.62],
                [17908,11,10,17,0.92,0.85,0.88],
                [17909,11,10,17,0.91,0.85,0.88],
                [17910,10,4,19,0.83,0.95,0.89],
                [17911,6,8,17,0.5,0.85,0.68],
                [17912,9,9,13,0.75,0.65,0.7],
                [17913,9,4,17,0.75,0.85,0.8],
                [17914,9,8,17,0.85,0.75,0.8],
                [17915,8,5,15,0.67,0.75,0.71],
                [17916,7,9,11,0.58,0.55,0.57],
                [17917,10,9,15,0.83,0.75,0.79],
                [17918,11,5,18,0.92,0.9,0.91],
                [17919,10,10,15,0.83,0.75,0.79],
                [17920,10,9,16,0.83,0.8,0.82],
                [17921,10,10,18,0.83,0.9,0.87],
                [17922,7,10,17,0.58,0.85,0.72],
                [17923,9,8,16,0.75,0.8,0.78],
                [17924,10,10,17,0.83,0.85,0.84],
                [17925,8,10,13,0.66,0.65,0.66],
                [17926,10,10,12,0.83,0.6,0.72],
                [17927,8,7,13,0.67,0.65,0.66],
                [17928,7,5,13,0.58,0.65,0.62],
                [17929,9,10,17,0.75,0.85,0.8],
                [17930,11,10,15,0.92,0.75,0.83],
                [17931,7,10,17,0.58,0.85,0.72],
                [17932,9,10,17,0.75,0.85,0.8],
                [17933,11,9,16,0.92,0.8,0.86],
                [17934,8,9,16,0.67,0.8,0.73],
                [17935,10,10,15,0.83,0.75,0.79],
                [17936,10,10,11,0.83,0.55,0.69],
                [17937,10,2,17,0.83,0.85,0.84],
                [17938,6,6,17,0.5,0.85,0.68],
                [17939,9,10,13,0.76,0.65,0.71],
                [17940,11,10,13,0.85,0.65,0.75],
                [17941,10,10,14,0.82,0.7,0.76],
                [17942,9,10,16,0.75,0.8,0.78],
                [17943,10,10,15,0.83,0.75,0.79],
                [17944,8,10,15,0.73,0.75,0.74],
                [17945,11,9,11,0.84,0.55,0.69],
                [17946,6,8,16,0.58,0.8,0.69],
                [17947,10,10,16,0.78,0.8,0.79],
                [17948,10,9,14,0.79,0.7,0.74],
                [17949,7,7,13,0.63,0.65,0.64],
                [17950,5,7,17,0.54,0.85,0.69],
                [17951,7,3,10,0.61,0.5,0.56],
                [17952,11,10,16,0.86,0.8,0.83]

            ];
            foreach ($special as $s) {
                if($this->primaryKey==$s[0]){
                    return [
                        "pri_cantidad"=>$s[1],
                        "sec_cantidad"=>$s[2],
                        "pre_cantidad"=>$s[3],
                        "pre_nota"=>$s[4],
                        "dec_nota"=>$s[5],
                        "summary"=>[
                                'nota'=>$s[6]
                            ]
                        ];
                }
            }
        }
        //! Fin de Modificacion especial

        $base=[
            "pregunta"=>[
                "correctas"=>[
                            2556,2558,2560,2562,2564,2566,2568,2570,2572,2574,2576,2578,2580,2582,2584,2586,2588,2590,2592,2594,
                            2940,2942,2944,2946,2948,2950,2952,2954,2956,2958,2960,2962,2964,2966,2968,2970,2972,2974,2976,2978],
                "incorrectas"=>[
                            2557,2559,2561,2563,2565,2567,2569,2571,2573,2575,2577,2579,2581,2583,2585,2587,2589,2591,2593,2595,
                            2941,2943,2945,2947,2949,2951,2953,2955,2957,2959,2961,2963,2965,2967,2969,2971,2973,2975,2977,2979]
            ],
            "primario"=>[
                "decidibles"=>[
                    "correctas"=>[2370,2372,2382,2396,2408,2438,2458,2466,2482,2502,2540,2548],
                    "incorrectas"=>[2371,2373,2383,2397,2409,2439,2459,2467,2483,2503,2541,2549]
                ],
                "distractores"=>[
                    "correctas"=>[2374,2398,2432,2450,2474,2514,2516,2520,2552],
                    "incorrectas"=>[2375,2399,2433,2451,2475,2515,2517,2521,2553]
                ]
            ],
            "secundario"=>[
                "decidibles"=>[
                    "correctas"=>[2376,2386,2390,2394,2402,2422,2428,2436,2442,2444,2476,2478,2486,2496,2506,2510,2522,2526,2532,2546],
                    "incorrectas"=>[2377,2387,2391,2395,2403,2423,2429,2437,2443,2445,2477,2479,2487,2497,2507,2511,2523,2527,2533,2547]
                ],
                "distractores"=>[
                    "correctas"=>[2378,2380,2384,2388,2392,2400,2404,2406,2410,2412,2414,2416,2418,2420,2424,2426,2430,2434,2440,2446,2448,2452,2454,2456,2460,2462,2464,2468,2470,2472,2480,2484,2488,2490,2492,2494,2498,2500,2504,2508,2512,2518,2524,2528,2530,2534,2536,2538,2542,2544,2550,2554],
                    "incorrectas"=>[2379,2381,2385,2389,2393,2401,2405,2407,2411,2413,2415,2417,2419,2421,2425,2427,2431,2435,2441,2447,2449,2453,2455,2457,2461,2463,2465,2469,2471,2473,2481,2485,2489,2491,2493,2495,2499,2501,2505,2509,2513,2519,2525,2529,2531,2535,2537,2539,2543,2545,2551,2555]
                ]
            ]
        ];

        $pre_pond=50;
        $pri_dec_pond=37.5;
        $pri_dis_pond=5.625;
        $sec_dec_pond=5;
        $sec_dis_pond=1.875;
        // $pre_nota=($pre_total!=0)?$pre_correcta/$pre_total:0;

        $respuestas=$this->respuestas;

        $pre_correcta=0;
        $pre_total=0;

        $pri_dec_correcta=0;
        $pri_dec_total=0;        
        $pri_dis_correcta=0;
        $pri_dis_total=0;
        $sec_dec_correcta=0;
        $sec_dec_total=0;        
        $sec_dis_correcta=0;
        $sec_dis_total=0;

        foreach ($respuestas as $value) {
            if(array_search($value->alt_id, $base['pregunta']['correctas'])!==false){
                $pre_correcta+=1;
                $pre_total+=1;
            }else{
                if(array_search($value->alt_id, $base['pregunta']['incorrectas'])!==false){
                    $pre_total+=1;
                }else{
                    if(array_search($value->alt_id, $base['primario']['decidibles']['correctas'])!==false){
                        $pri_dec_correcta+=1;
                        $pri_dec_total+=1;
                    }else{
                        if(array_search($value->alt_id, $base['primario']['decidibles']['incorrectas'])!==false){
                            $pri_dec_total+=1; 
                        }else{
                            if(array_search($value->alt_id, $base['primario']['distractores']['incorrectas'])!==false){
                                $pri_dis_correcta+=1;
                                $pri_dis_total+=1; 
                            }else{
                                if(array_search($value->alt_id, $base['primario']['distractores']['correctas'])!==false){
                                    $pri_dis_total+=1; 
                                }else{
                                    if(array_search($value->alt_id, $base['secundario']['decidibles']['correctas'])!==false){
                                        $sec_dec_correcta+=1;
                                        $sec_dec_total+=1;
                                    }else{
                                        if(array_search($value->alt_id, $base['secundario']['decidibles']['incorrectas'])!==false){
                                            $sec_dec_total+=1; 
                                        }else{
                                            if(array_search($value->alt_id, $base['secundario']['distractores']['incorrectas'])!==false){
                                                $sec_dis_correcta+=1;
                                                $sec_dis_total+=1; 
                                            }else{
                                                if(array_search($value->alt_id, $base['secundario']['distractores']['correctas'])!==false){
                                                    $sec_dis_total+=1; 
                                                }else{
                                                    return ['RESPUESTA INEXISTENTE'];
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }


        $pre_nota=($pre_total!=0)?$pre_correcta/20:0;

        $dec_acierto=
            $pri_dec_correcta*$pri_dec_pond
            +$pri_dis_correcta*$pri_dis_pond
            +$sec_dec_correcta*$sec_dec_pond
            +$sec_dis_correcta*$sec_dis_pond;

        $dec_total=$pri_dec_total*$pri_dec_pond
            +$pri_dis_total*$pri_dis_pond
            +$sec_dec_total*$sec_dec_pond
            +$sec_dis_total*$sec_dis_pond;
        $dec_nota=($dec_total!=0)?$dec_acierto/$dec_total:0;

        $nota=($dec_nota+$pre_nota)/2;
        $nota=(float)number_format($nota,2);
        
        if((float)$this->calificacion!=$nota){
            $this->calificacion=$nota;
            $this->save();
        }

        return [
            "dec_nota"=>(float)number_format($dec_nota,2),
            "pri_cantidad"=>$pri_dec_correcta,
            "sec_cantidad"=>$sec_dec_correcta,
            "pre_nota"=>$pre_nota,
            "pre_cantidad"=>$pre_correcta,
            "summary"=>[
                "pre_correcta"=>$pre_correcta,
                "pre_total"=>$pre_total,
                "pri_dec_correcta"=>$pri_dec_correcta,
                "pri_dec_total"=>$pri_dec_total,        
                "pri_dis_correcta"=>$pri_dis_correcta,
                "pri_dis_total"=>$pri_dis_total,
                "sec_dec_correcta"=>$sec_dec_correcta,
                "sec_dec_total"=>$sec_dec_total,        
                "sec_dis_correcta"=>$sec_dis_correcta,
                "sec_dis_total"=>$sec_dis_total,
                'pre_pond'=>$pre_pond,
                'pri_dec_pond'=>$pri_dec_pond,
                'pri_dis_pond'=>$pri_dis_pond,
                'sec_dec_pond'=>$sec_dec_pond,
                'sec_dis_pond'=>$sec_dis_pond,
                'nota'=>$nota
            ]
        ];
    }
}
