<?php 
	/*
	 *	Definen Variables de Uso en la Vista
	 */
	$trabajador=$ficha->trabajador;
	$params=$ficha->params;
	if(empty($params)){
		throw new \yii\web\HttpException(404, 'Esta evaluación, no contiene la información necesaria para ser mostrada.');
	}
	$data=$params->data;
?>
<!DOCTYPE html>
<html>
<head>
<style type="text/css">p{color:#fff;font-family:Roboto,sans-serif;font-size:x-large}.tab{width:25%;float:left;text-align:center}</style>
</head>
<body style="background-image: url('img/reporte/extintor/escena_01_low.jpg');background-repeat: no-repeat;">
	<div style="padding-top: 280px;padding-left: 170px;">
		<p><?=$trabajador->getNombreCompleto(false) ?><br><small><?=$trabajador->rut ?></small></p>
	</div>
	<div style="padding-top: 100px;">
		<div class="tab"><p><?=$data->facil->fuego ?></p></div>
		<div class="tab"><p><?=$data->facil->escape?"SI":"NO" ?></p></div>
		<div class="tab"><p><?=gmdate('i:s',$data->facil->tiempo) ?></p></div>
		<div class="tab"><p><?=$data->facil->puntaje ?></p></div>		
	</div>	
	<div style="padding-top: 80px;">
		<div class="tab"><p><?=$data->media->fuego ?></p></div>
		<div class="tab"><p><?=$data->media->escape?"SI":"NO" ?></p></div>
		<div class="tab"><p><?=gmdate('i:s',$data->media->tiempo) ?></p></div>
		<div class="tab"><p><?=$data->media->puntaje ?></p></div>	
	</div>
	<div style="padding-top: 80px;">
		<div class="tab"><p><?=$data->dificil->fuego ?></p></div>
		<div class="tab"><p><?=$data->dificil->escape?"SI":"NO" ?></p></div>
		<div class="tab"><p><?=gmdate('i:s',$data->dificil->tiempo) ?></p></div>
		<div class="tab"><p><?=$data->dificil->puntaje ?></p></div>		
	</div>
	<div style="padding-top: 12px;padding-left: 210px;">
		<p style="font-size: 23pt;text-align: center;"><strong><?=$data->total ?></strong></p>
	</div>
</body>
</html>