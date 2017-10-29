<?php 
	if (!isset($_SESSION)) {

    	session_start();
	}

	if(!isset($_SESSION['id'] , $_SESSION['usuario'], $_SESSION['correo'])){
		echo "SI SESSION";
		session_destroy();
		header('Location: error');
	}
 ?>
<head>
	<meta charset="UTF-8"/>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link href="https://fonts.googleapis.com/css?family=Bree+Serif|Work+Sans" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="css/estilos.css">
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/start/jquery-ui.css">
	<title>Teccheck</title>
</head>
<div class="row">
	<div class="col-md-12 colorPrimario">
		<header>
			<div class="row">
				<div class="col-md-3" align="left">
					<img src="img/logo-teCheck-home.png">
				</div>
				<div class="col-md-6">
					<h2 class="titulos">Administrador - Comparador de precios</h2>
				</div>
				<div class="col-md-3">
					<div class="row">
						<div class="col-md-4" align="right">
							<form method="post" action="class/User.php">
								<input type="hidden" name="post" id="post" value="logout">
								<button type="submit" style="background: none;border:none;cursor: pointer;">
									<img  src="img/ic_salir.png">
								</button>
								
							</form>
						</div>
						<div class="col-md-8" align="left" class="cuerpo">
							<?php 
								echo $_SESSION['usuario'];
								echo "<p style='font-size:70%;font-weight:bold'>". $_SESSION['correo'] . "</p>";
							?>
						</div>
					</div>

				</div>
			</div>
			
		</header>
	</div>
</div>
