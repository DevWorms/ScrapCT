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
			<h3>Usuarios<br><br></h3>
			<form id="form-crearUsuario">
				<input type="hidden" name="post" id="post" value="crearUsuario">
				<div class="row">
					<div class="col-md-4" align="left">
						<label for="nombre">
							<b>Nombre de Usuario</b>
							<input type="text" id="nombre" name="nombre" class="form-control" required>
						</label>
						<br>
						<label for="apellido">
							<b>Apellido</b>
							<input type="text" id="apellido" name="apellido" class="form-control" required>
						</label>
					</div>
					<div class="col-md-4" align="left">
						<label for="correo">
							<b>Correo electrónico</b>
							<input type="email" id="correo" name="correo" class="form-control" required>
						</label>
						<br>
						<label for="correo">
							<b>Contrase&ntilde;a</b>
							<input type="password" id="contrasena" name="contrasena" class="form-control" required>
						</label>
					</div>
				</div>
				<br>
				<button class="btn btn-primary" type="submit">
					Guardar
				</button>	
			</form>
			<br>
			<div class="resultados">
				<table class="table table-striped table-condensed" id="tbl-usuarios" >
					<thead>
						<tr>
							<th>Nombre</th>
							<th>Apellido</th>
							<th>Correo</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
			
			<?php include 'footer.php' ?>
		</div>
		
	</div>
	
	<script type="text/javascript" src="js/usuario.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("a[href=usuarios]").addClass("menu-activo");
		});
	</script>
</body>
</html>