<html>
	<head>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
		<link href="https://fonts.googleapis.com/css?family=Bree+Serif|Work+Sans" rel="stylesheet"> 
		<link rel="stylesheet" type="text/css" href="css/estilos.css">
		
		<title>ScrapCT</title>
	</head>
	<body>
		<header class="titulos">
			
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
		<footer class="cuerpo">
			<img src="img/logo-teCheck-home.png">
			<br>
			Copyright 2017 |  Todos los derechos reservados  |  Aviso Legal

		</footer>
		<script>
			window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.min.js"><\/script>')
		</script>
	    <script type="text/javascript" src="js/vendor/bootstrap.min.js"></script>
	    <script type="text/javascript" src="js/notify.min.js"></script>
	    <script type="text/javascript" src="js/usuario.js"></script>
	</body>
</html>