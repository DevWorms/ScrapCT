<!DOCTYPE html>
<html>
<head>
	<title>Tec-Check Web scraping</title>
	<meta charset="UTF-8"/>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link href="https://fonts.googleapis.com/css?family=Bree+Serif|Work+Sans" rel="stylesheet"> 
	<link rel="stylesheet" type="text/css" href="css/estilos.css">
	<title>Tec-Check</title>
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
			<h3>Usuarios<br><br></h3>
			<form>
				<div class="row">
					<div class="col-md-4" align="left">
						<label for="nombre">
							<b>Nombre de Usuario</b>
							<input type="text" id="nombre" name="nombre" class="form-control">
						</label>
						<br>
						<label for="apellido">
							<b>Apellido</b>
							<input type="text" id="apellido" name="apellido" class="form-control">
						</label>
					</div>
					<div class="col-md-4" align="left">
						<label for="correo">
							<b>Correo electrónico</b>
							<input type="email" id="correo" name="correo" class="form-control">
						</label>
						<br>
						<label for="correo">
							<b>Contrase&ntilde;a</b>
							<input type="password" id="contrasena" name="contrasena" class="form-control">
						</label>
					</div>
				</div>
				<br>
				<button class="btn btn-primary" type="button">
					Guardar
				</button>	
			</form>
			<br>
			<table class="table">
				<thead>
					<tr>
						<th>Nombre</th>
						<th>Apellido</th>
						<th>Correo</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
</body>
</html>