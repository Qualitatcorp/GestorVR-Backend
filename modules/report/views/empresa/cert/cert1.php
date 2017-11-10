<?php 
	/*
	 * Configruacion de Variables Globales
	 */
	setlocale(LC_ALL, 'es_CL.utf8');
	/*
	 * Preprar Variables
	 */
	$trabajador=$ficha->trabajador;
	$empresa=$ficha->empresa;
	$params=$ficha->params;
	$data=$params->data;
 ?>
<!DOCTYPE html>
<html>
<head>
<style type="text/css">
.container{font-family:arial;font-size:16;letter-spacing:1px}.aprobado{background-image:url("<?=\Yii::$app->params['BasePathImage'] ?>aprobado.png")}.noaprobado{background-image:url("<?=\Yii::$app->params['BasePathImage'] ?>noaprobado.png")}.recomendaciones{background-image:url("<?=\Yii::$app->params['BasePathImage'] ?>recomendaciones.png")}.floatL{float:left}.porcentaje{text-align:center;padding:15px;width:50px}.resultado{background-repeat:no-repeat;width:110px;height:48px}.descripcion{width:200px;padding-top:15dpi;padding-left:120px}
</style>
</head>
<body>
	<img src="<?=\Yii::$app->params['BasePathImage'].'logo.png'?>" style="margin-left: 220; width: 200px">
	<div class="container">
		<h4 align="center" style="text-decoration: underline;">
			INFORME DE RESULTADOS SISTEMA DE EVALUACIÓN EN SEGURIDAD
		</h4>
		<div>
			NOMBRE: <?=strtoupper($trabajador->getNombreCompleto(false)) ?><br>
			RUT: <?=$trabajador->rut ?><br>
			EMPRESA: <?=$trabajador->gerencia ?><br>
			FECHA <?=($ficha->reacreditacion) ? "REACREDITACION" : "ACREDITACIÓN"; ?>: <?=strtoupper(strftime("%e de %B del %Y",strtotime($ficha->creado))) ?><br>
		</div>
		<div  style="margin-top: 15px" >
			<div>
				<div class="resultado aprobado floatL">
					<div class = "porcentaje ">
						<?php if ($data['nota']>=0.9): ?>
							<?=number_format($data['nota']*100) ?>%
						<?php endif ?>
					</div>
				</div>
				<div class = "descripcion">APROBADO</div>
			</div>
			<div>
				<div class="resultado recomendaciones floatL">
					<div class = "porcentaje ">
						<?php if ($data['nota']>=0.70&&$data['nota']<0.9): ?>
							<?=number_format($data['nota']*100) ?>%
						<?php endif ?>
					</div>
				</div>
				<div class = "descripcion">RECOMENDACIONES</div>
			</div>
			<div>
				<div class="resultado noaprobado floatL">
					<div class = "porcentaje ">
						<?php if ($data['nota']<0.70): ?>
							<?=number_format($data['nota']*100) ?>%
						<?php endif ?>
					</div>
				</div>
				<div class = "descripcion">NO APROBADO</div>
			</div>
		</div>
		<div style="margin-top: 15px">
			<p><strong>1.- Informe de Percepción del Riesgo Trabajador</strong></p>
			<div style="margin-left: 20px;">
				<div class="floatL" style="width: 20%";>
					<p><strong>NOTA <?=number_format($data['dec_nota']*100) ?>%</strong></p>
				</div>
				<div style="width: 50%;text-justify: inter-word;">
					<p>El trabajador presenta una <strong><?= ($data['dec_nota'] >= 0.9)?"Alta":($data['dec_nota'] >= 0.70)?"Media":"Baja"?></strong> percepción del riesgo.</p>
				</div>
				<p>Detalle de informe comparativo sobre el óptimo de:</p>
				<ul style="list-style-type: disc;">
					<li>Número de detección de errores del ambiente: <?=$data['PRI_DEC']['acierto']." (".$data['PRI_DEC']['total']?>).</li>
					<li>Número de visualización de errores externos al evento: <?=($data['SEC_DEC']['acierto']>=10)?10:$data['SEC_DEC']['acierto'] ?> (10).</li>
				</ul>
			</div>
		</div>
		<div>
			<p><strong>2.- Informe de Conocimiento Estándares <?=$empresa->nombre?></strong></p>
			<div style="margin-left: 20px;">
				<div class="floatL" style="width: 20%";>
					<p><strong>NOTA <?=number_format($data['pre_nota']*100) ?>%</strong></p>
				</div>
				<div style="width: 50%;text-justify: inter-word;">
					<p>El trabajador presenta un <strong><?= ($data['pre_nota'] >= 0.9)?"Alto":($data['pre_nota'] >= 0.70)?"Moderado":"Bajo"?></strong> nivel  de conocimientos de seguridad en estándares <?=$empresa->nombre ?>.</p>
				</div>
				<p>Detalle de informe comparativo sobre el óptimo de:</p>
				<ul style="list-style-type: disc;">
					<li>Respuestas correctas de conocimiento: <?=$data['PREGUNTA']['acierto']?> (20).</li>
				</ul>
			</div>
		</div>
	</div>
</body>
</html>