<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8"/>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link href="https://fonts.googleapis.com/css?family=Bree+Serif|Work+Sans" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="css/estilos.css">
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/start/jquery-ui.css">
	<title>Teccheck</title>
</head>
<body>
	<?php 
		include 'header.php';
	?>
	<div class="row body">

		<?php 
			include 'menu.php';
		?>

		<div class="col-md-9 box-container cuerpo">
			<h3>Proceso Amazon<br><br></h3>
			<div class="row">
				<div class="col-md-12" align="center">
					<ol class="lista_amazon">
						<li id="primer_amazon">
							<button class="btn btn-info" onclick="iniciarProceso();">
								Iniciar
							</button>
							<br><br>
						</li>
						<li style="display: none" id="segundo_amazon">
							<button class="btn btn-primary" onclick="obtenerCategorias();">
								Obtener categorias
							</button>
							<br><br>
						</li>
						<li style="display: none" id="tercero_amazon">
							<button class="btn btn-success" onclick="ejecutarProceso();">
								Ejecutar proceso Amazon
							</button>
							<br><br>
						</li>
						<li style="display: none;" id="cuarto_amazon">
							<h4 id='antes_bd'>Productos previos en la base de daos 4500</h4>
							<h4 id='despues_bd'>Productos en la base de datos actualmente 4500</h4>
							<a class="btn btn-primary" href='amazon' target="_self">
								Finalizar
							</a>
							<br><br>
						</li>
					</ol>

					<div class="bar_fondo" style="display: none">
					  <div id="bar_amazon" class="bar_liquid">
					  	0
					  </div>
					</div>

				</div>
				<div class="col-md-12">
					<label for="consola_amazon">
						<b>Consola</b> 
					</label>
					<div id="consola_amazon" name="consola_amazon" class="form-control consola"></div>	
				</div>
			</div>
			<?php include 'footer.php' ?>
		</div>
		<div id="modal-avisoAmazon" title="x" style="overflow-x:hidden;display: none" align="left">
			<h3 style="color: red">
				<b>Atención ! </b>
				<img src="img/warning.png">
			</h3 style="color: black">
			<h4>Antes y durante el proceso es importante no apagar el equipo, seguir los pasos, no cerrar el navegador, y verificar que se tienen una buena conexión a internet.</h4>
		</div>
	</div>
	
	<script type="text/javascript" src="js/amazon.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("a[href=amazon]").addClass("menu-activo");
		});
	</script>
</body>
</html>