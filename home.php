<!DOCTYPE html>
<html>
<head>
	<title>Tec-Check Web scraping</title>
	<meta charset="UTF-8"/>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link href="https://fonts.googleapis.com/css?family=Bree+Serif|Work+Sans" rel="stylesheet"> 
	<link rel="stylesheet" type="text/css" href="css/estilos.css">
	<title>ScrapCT</title>
</head>
<body>
	<?php 
		include 'header.php';
	?>
	<div class="row body">

		<?php 
			include 'menu.php';
		?>
		<div class="col-md-9 box-container">
			<h3>Tiendas<br><br></h3>
			<form id="form-crearTienda">
				<input type="hidden" name="post" id="post" value="crearUsuario">
				<div class="row">
					<div class="col-md-4" align="left">
						<label for="nombre">
							<b>Nombre de la Tienda</b>
							<input type="text" id="nombre" name="nombre" class="form-control">
						</label>
					</div>
					<div class="col-md-4" align="left">
						<label for="apellido">
							<b>URL</b>
							<input type="text" id="url" name="url" class="form-control">
						</label>
					</div>
					<div class="col-md-4" align="left">
						<label for="correo">
							<b>Clase</b>
							<input type="text" id="clase" name="clase" class="form-control">
						</label>
					</div>
				</div>
				<br>
				<button class="btn btn-primary" type="button" onclick="crearTienda()">
					Guardar
				</button>	
			</form>
			<br>
			<div class="resultados">
				<table class="table table-striped table-condensed" id="tbl-tiendas">
					<thead>
						<tr>
							<th>Tienda</th>
							<th>URL</th>
							<th>Clase</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
			<?php include 'footer.php'; ?>
		</div>
	</div>
	<script type="text/javascript" src="js/tiendas.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("a[href=tiendas]").addClass("menu-activo");
		});
	</script>
</body>
</html>