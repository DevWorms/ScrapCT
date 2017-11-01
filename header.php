<?php 
	if (!isset($_SESSION)) {
    	session_start();
	}

	if (!isset($_SESSION['id'] , $_SESSION['usuario'], $_SESSION['correo'])) {
		session_destroy();
		header('Location: error');
	}
 ?>
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
