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
			FECHA ACREDITACIÓN: <?=  $trabajador->creacion ?>
		</div> 
		<!-- aprobado -->
		<div  class = "margin-top-15">
			<div class="resultado aprobado floatL">
				<div class = "porcentaje ">
					30%
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
					30%
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
					30%
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
				NOTA: <?= '36%' ?>  
			</div>
			<div class=" Width40 ">
				<?= $bp ?> 
			</div>
			<div class="margin-top-15">
				Detalle de Informe comparativo sobre el óptimo de:
				<br>
				<div class="margin-left-20"> &#8226; Número de detección de errores del ambiente: 5 (12)</div>
				<div class="margin-left-20"> &#8226; Número de visualización de errores externos al evento: 2 (10) </div>
			</div>
		</div>
		<div class = "margin-top-15 padding-top-5">
			<div class="bold">  2.- Informe de Conocimiento Estándares MEL   </div>
			<br>
			<div class="bold floatL Width20">
				NOTA: <?= '36%' ?>  
			</div>
			<div class="Width40 ">
				<?= $me ?> 
			</div>
			<div class="margin-top-15">
				Detalle de Informe comparativo sobre el óptimo de:
				<br>
				<div class="margin-left-20">&#8226; Número de detección de errores del ambiente: 5 (12)</div>
				<div class="margin-left-20">&#8226; Número de visualización de errores externos al evento: 2 (10) </div>
			</div>

		</div>


	</div
<pre>
<?php 
 //var_dump($trabajador);
?>
</pre>
</body>
