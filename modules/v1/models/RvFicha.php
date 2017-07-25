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
            'proyecto',
            'evaluacion',
            'proyecto',
            'dispositivo',
            'pais',
            'respuestas',
            'ceim'
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

    public function getCeim()
    {
        $base=[
            "pregunta"=>[
                "correctas"=>[2556,2558,2560,2562,2564,2566,2568,2570,2572,2574,2576,2578,2580,2582,2584,2586,2588,2590,2592,2594],
                "incorrectas"=>[2557,2559,2561,2563,2565,2567,2569,2571,2573,2575,2577,2579,2581,2583,2585,2587,2589,2591,2593,2595]
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

        $pre_pond=50;
        $pri_dec_pond=37.5;
        $pri_dis_pond=5.625;
        $sec_dec_pond=5;
        $sec_dis_pond=1.875;
        // $pre_nota=($pre_total!=0)?$pre_correcta/$pre_total:0;
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
