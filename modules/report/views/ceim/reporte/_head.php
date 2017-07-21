<?php 
$logo = \Yii::getAlias('@webroot').'/img/logo.png' ;
$aprobado = \Yii::getAlias('@webroot').'/img/aprobado.png' ;
function upper($string){
	return strtoupper($string);
}
$bp =  "El trabajador presenta una <strong>Baja</strong> Percepción del riesgo";
$mp =  "El trabajador presenta una Percepción del riesgo de nivel <strong>Medio</strong>";
$ap =  "El trabajador presenta una <strong>Adecuada</strong> Percepción del Riesgo";

$be =  "El trabajador presenta un <strong>Bajo</strong> nivel de Conocimiento de seguridad de los estándares MEL";
$me =  "El trabajador presenta un nivel <strong>Medio</strong> de Conocimientos de seguridad de los estándares MEL";
$ae =  "El trabajador presenta un nivel <strong>Adecuado</strong> de Conocimientos de Seguridad de los estándares ";
$trabajador = $ficha->trabajador;
$nombreCompleto = $trabajador->nombre . ' ' . $trabajador->paterno. ' ' . $trabajador->materno;
$evaluacion = $ficha->evaluacion;
$ceim = $ficha->ceim;
$aprobado = '';
$recomendaciones = '';
$noaprobado = '';

$calificacion =number_format($ficha->calificacion * 100);
//$calificacion = 89;
// semaforo
if($calificacion > 89){
	$aprobado  = $calificacion . '%';
}else if($calificacion >=70 && $calificacion < 90){
	$recomendaciones = $calificacion . '%';
}else{
	$noaprobado = $calificacion . '%';
}
//end semaforo

// informe 1
$textInfo1 = '';
$notaInfo1 = number_format($ceim['dec_nota'] *100);
// $notaInfo1 = 89;
if($notaInfo1 > 89){
	$textInfo1 = $ap;
}else if($notaInfo1 >=70 && $notaInfo1 < 90){
	$textInfo1 = $mp;
}else{
	$textInfo1 = $bp;
}

//$informe 2
$textInfo2 = '';
$notaInfo2 =number_format($ceim['pre_nota'] *100);
// $notaInfo2 = 90;
if($notaInfo2 > 89){
	$textInfo2 = $ae;
}else if($notaInfo2 >=70 && $notaInfo2 < 90){
	$textInfo2 = $me;
}else{
	$textInfo2 = $be;
}

//Sec_cantidad
 
$sec_cantidad = $ceim['sec_cantidad'];
if($sec_cantidad > 9){
	$sec_cantidad = 10;
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
			FECHA ACREDITACIÓN: <?= $dia .' '.$mes . ' '. $anio?>
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
		<!-- fin aprobado -->

		<!--  recomendaciones  -->
		<div>
			<div class="resultado recomendaciones floatL">
				<div class = "porcentaje ">
					 <?= $recomendaciones?>
				</div>
			</div>
			<div class = "descripcion">
				RECOMENDACIONES
			</div>
		</div>
		<!-- fin recomendaciones -->

		<!-- no aprobado -->
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
		<!-- fin no aprobado -->
		<div class = "margin-top-15 padding-top-5">
			<div class="bold">  1.- Informe de y Percepción del Riesgo Trabajador   </div>
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
				<div class="margin-left-20"> &#8226; Número de detección de errores del ambiente: <?= $ceim['pri_cantidad']?> (12)</div>
				<div class="margin-left-20"> &#8226; Número de visualización de errores externos al evento: <?= $sec_cantidad ?> (10) </div>
			</div>
		</div>
		<div class = "margin-top-15 padding-top-5">
			<div class="bold">  2.- Informe de Conocimiento Estándares MEL   </div>
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
				<div class="margin-left-20">&#8226;	Respuestas Correctas Conocimiento: <?=$ceim['pre_cantidad']?> (20)</div>
				 
			</div>

		</div>

<pre>
<?php 
 
?>
</pre>
	</div

</body>
