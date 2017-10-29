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
								Ejecutar proces Amazon
							</button>
							<br><br>
						</li>
					</ol>
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
		
	</div>
	
	<script type="text/javascript" src="js/amazon.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("a[href=amazon]").addClass("menu-activo");
		});
	</script>
</body>
</html>