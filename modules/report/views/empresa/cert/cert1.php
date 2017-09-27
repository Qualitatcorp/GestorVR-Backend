<style type="text/css">
.aprobado{background-image: url('<?=\Yii::$app->params['BasePathImage']?>aprobado.png');}
.noaprobado{background-image: url('<?=\Yii::$app->params['BasePathImage']?>noaprobado.png');}
</style>
<?php 
// variables 
	$aprobado = '';
	$noaprobado = '';
	$textInfo1 = '';
	$nota_test = 30;
	$logo =  \Yii::$app->params['BasePathImage'].'logo.png';
	function upper($string){return strtoupper($string);}
	// persepcion de riesgo
	$bp ="El trabajador presenta una <strong>Baja</strong> Percepción del riesgo";
	$ap ="El trabajador presenta una <strong>alta</strong> Percepción del Riesgo";
	//concimientos de seguridad
	$be ="El trabajador presenta un <strong>Bajo</strong> nivel de Conocimiento  en Estándares ENAP";
	$ae ="El trabajador presenta un nivel <strong>Adecuado</strong> de Conocimientos en Estándares ENAP ";
	//perfil psioclogico
	$bps ="El trabajador presenta un perfil psicológico <strong>No Adecuado</strong> de conducta asociada al riego";  //el trabajador presenta
	$aps ="El trabajador presenta un perfil <strong> No Adecuado </strong> de conducta asociada al riego";

 	if($ficha->params->data['riesgo']['nota'] <>null){
 		$notaInfo3 =number_format( $ficha->params->data['riesgo']['nota']*100);
 	}else{
 		$notaInfo3 = null; 
 	}
	$trabajador = $ficha->trabajador;
	$nombreCompleto = $trabajador->nombre . ' ' . $trabajador->paterno. ' ' . $trabajador->materno;
	$evaluacion = $ficha->evaluacion;
	$ceim = $ficha->ceim;
	$calificacion = number_format( $ficha->params->data['nota']*100);
	 
	// semaforo
	if($calificacion > 69){
		$aprobado  = $calificacion . '%';
	}else{
		$noaprobado = $calificacion . '%';
	}

	$notaInfo1 =   number_format($ficha->params->data['percepcion']['nota']*100); //tomar nota de controlador
	// $notaInfo1 = 89;
	if($notaInfo1 > 89){
		$textInfo1 = $ap;
	}else{
		$textInfo1 = $bp;
	}
	//$informe 2
	 
	$notaInfo2 = number_format($ficha->params->data['conocimiento']['nota']*100);// tomar prenota de controlador;
	if($notaInfo2 > 69){
		$textInfo2 = $ae;
	}else{
		$textInfo2 = $be;
	}
	//$informe 3
 
	
	//$notaInfo3 = $nota_test; //tomar nota de controlador psicológico

	$textInfo3 = '';

	if($notaInfo3 === null){
		$textInfo3 = "la nota psicologica no se encuentra disponible en estos momentos";
	}else if($notaInfo3 > 69){
		$textInfo3 = $aps;
	}
	else{
		$textInfo3 = $bps;
	}
	 
//fecha
setlocale(LC_TIME, "C");
$date = new DateTime($ficha->creado);
$mes = $date->format('F');
$dia = $date->format('d');
$anio = $date->format('Y');
switch ($mes) {
	case 'January':
	    $mes="Enero";
		break;
	case 'February':
	   $mes="Febrero";
		break;
	case 'March':
	   $mes="Marzo";
		break;
	case 'April':
	    $mes="Abril";
		break;
	case 'May':
	    $mes="Mayo";
		break;
	case 'June':
	    $mes="Junio";
		break;
	case 'July':
	    $mes="Julio";
	break;	
	case 'August':
	    $mes="Agosto";
		break;	
	case 'September':
	    $mes="Setiembre";
	break;	
	case 'October':
	    $mes="Octubre";
	break;
	case 'November':
	    $mes="Noviembre";
	break;
	case 'December':
	    $mes="Diciembre";
	break;
	
	default:
		# code...
		break;
};
 
?>
<body>
<img src="<?=$logo ?>" style="margin-left: 220; width: 200px">
	<div class="container">
		<h4 class = "margin-top-30 underline margin-left-20 "  > INFORME DE RESULTADOS SISTEMA DE EVALUACIÓN EN SEGURIDAD</h4>
		<div  >
			NOMBRE: <?=upper($nombreCompleto) ?> 
			<br>
			RUT: <?= $trabajador->rut ?>
			<br>
			EMPRESA: <?= upper($trabajador->gerencia) ?>
			<br>
			FECHA <?=($ficha->reacreditacion) ? "REACREDITACION" : "REACREDITACIÓN"; ?> : <?= $dia .' '.$mes . ' '. $anio?>


		</div> 
		<!-- aprobado -->
		<div  class = "margin-top-15">
			<div class="resultado aprobado floatL">
				<div class = "porcentaje ">
					<?=$aprobado?>
				</div>
			</div>
			<div class = "descripcion">
				APROBADO
			</div>
		</div>
		<div>
			<div class="resultado noaprobado floatL">
				<div class = "porcentaje ">
					<?= $noaprobado ?>
				</div>
			</div>
			<div class = "descripcion">
				NO APROBADO
			</div>
		</div>
		<div class = "margin-top-15 padding-top-5">
			<div class="bold">  1.- Informe Percepción del Riesgo Trabajado </div>
			<br>
			<div class="bold floatL Width20">
				NOTA: <?= $notaInfo1 ?>  %
			</div>
			<div class=" Width40 ">
				<?= $textInfo1; ?> 
			</div>
			<div class="margin-top-15">
				Detalle de Informe comparativo sobre el óptimo de:
				<br>
				<div class="margin-left-30">   Número de detección de errores del ambiente: <?=$ficha->params->data['percepcion']['pri']['correcto']?>
					(<?=$ficha->params->data['percepcion']['pri']['total']?>)</div>
				<div class="margin-left-30">   Número de visualización de errores externos al evento:
				 <?=$ficha->params->data['percepcion']['sec']['total']?> 
				 (<?=$ficha->params->data['percepcion']['sec']['total']?>) </div>
			</div>
		</div>
		<div class = "margin-top-15 padding-top-5">
			<div class="bold">  2.- Informe Conocimiento Estándares ENAP   </div>
			<br>
			<div class="bold floatL Width20">
				NOTA: <?= $notaInfo2 ?> %
			</div>
			<div class="Width40 ">
				<?= $textInfo2 ?> 
			</div>
			<div class="margin-top-15">
				Detalle de Informe comparativo sobre el óptimo de:
				<br>
				<div class="margin-left-30"> 	Respuestas Correctas Conocimiento:  
					<?=$ficha->params->data['conocimiento']['correcto']?> 
					(<?=$ficha->params->data['conocimiento']['total']?>)</div>
			</div>
		</div>
		<div class = "margin-top-15 padding-top-5">
			<div class="bold">  3.- Informe Adecuación al Perfil   </div>
			<br>
			<div class="bold floatL Width20">
				NOTA: <?= $notaInfo3 ?> %
			</div>
			<div class="Width40 ">
				  <?=$textInfo3  ?>  
			</div>
			 
			 
		</div>
		<!-- informe 3  fin-->
	</div

</body>
