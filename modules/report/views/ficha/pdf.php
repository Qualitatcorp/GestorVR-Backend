<!DOCTYPE html>
<html>
<head>
</head>
<body>
<div class="container">
	<div class="row">
		<div class="col-xs-2">
		<?php 
			$photo=$model->getPhoto()->One();
		 ?>
		<?php if ($photo!==null): ?>
			<?php $src=$photo->src; ?>
			<?php if ($src->exists): ?>
				<img src="<?= $src->Url?>" alt="" class="img-thumbnail">
			<?php else: ?>			
				<img src="<?= \Yii::$app->params['BasePathImage'].'ficha/logo.png'?>" alt="" class="img-responsive">
			<?php endif ?>
		<?php else: ?>
			<img src="<?= \Yii::$app->params['BasePathImage'].'ficha/logo.png'?>" alt="" class="img-responsive">
		<?php endif ?>
		</div>
		<div class="col-xs-6">
			<?php  
				$pageHeader=array();
				$pageHeader[]=$model->dispositivo->empresa->nombre;
				$pageHeader[]=$model->evaluacion->nombre;
				$trabajador=$model->trabajador;
				if(!empty($trabajador->rut)){
					$pageHeader[]=$trabajador->rut;
				}
				if(!empty($trabajador->nombreCompleto)){
					$pageHeader[]=$trabajador->nombreCompleto;	
				}
				if(!empty($trabajador->gerencia)){
					$pageHeader[]=$trabajador->gerencia;
				}
				$pageHeader=implode("<br>", $pageHeader);
			?>
			<div><h2>Ficha de evaluación<br><small><?=$pageHeader ?></small></h2></div>
		</div>
		<div class="col-xs-2 pull-right" >
			<div class="panel">
				<div class="panel-heading">
					<div class="panel-title text-center">NOTA</div>
				</div>
				<div class="panel-body">
					<p class="text-center">7.0</p>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th style="width: 20px">#</th>
					<th align="center" style="width: 100px">Imagen</th>
					<th align="center">Descripción</th>
					<th align="center">Respuesta</th>
					<th align="center">Resultado</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($model->respuestas as $key => $value): ?>
					<tr>
						<td align="center"><?=$key+1?></td>
						<?php 
							$alternativa=$value->alternativa;
							$pregunta=$alternativa->pregunta;
						 ?>
						 <td><img  style="height: 100px;width: 100px" class="img-rounded img-responsive" src="<?=$pregunta->urlImagen ?>" alt="">
						 </td>
						<td style="padding-right: 10px;padding-left: 10px;">
							<p class="text-justify" ><strong><?=$pregunta->descripcion ?></strong><br><em><?=$pregunta->comentario ?></em></p>
						</td>
						<td align="center"><?=$alternativa->descripcion ?></td>
						<td align="center"><img style="width: 50px" class="img-circle" src="<?=$alternativa->UrlRespuesta ?>" alt=""></td>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	</div>
	<p align="right"><?=$model->creado ?></p>
</div>
</body>
</html>