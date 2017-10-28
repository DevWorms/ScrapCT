<html>
	<head>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
		<link href="https://fonts.googleapis.com/css?family=Bree+Serif|Work+Sans" rel="stylesheet"> 
		<link rel="stylesheet" type="text/css" href="css/estilos.css">
		<title>ScrapCT</title>
	</head>
	<body>
		<header class="titulos" style="color: black;box-shadow: none;">
			<h1>Administrador - Web scraping</h1>
		</header>
		<br><br>
		<div class="row">
			<div class="col-md-3 col-sm-12"></div>
			<div class="card col-md-6 col-sm-12 login">
				<form method="post" id="form-login" class="cuerpo">
					<br>
					<h3>Iniciar sesión</h3>
					<label>
						Nombre de usario o correo
						<input type="text" name="correo" id="correo" class="form-control">
					</label>
					<br>
					<label>
						Contraseña
						<input type="password" name="contrasena" id="contrasena" class="form-control">
					</label>
					<br>
					<input type="hidden" name="post" id="post" value="login">
					<button type="button" class="btn btn-primary" id="button-login">Ingresar</button>
					<p id="confirmacion"></p>
					<br>
				</form>
			</div>
		</div>
		<br>
		<?php include 'footer.php' ?>
	    <script type="text/javascript" src="js/usuario.js"></script>
	</body>
</html>