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
			<h3>Obtener excel<br><br></h3>
			<h4 id="info_excel"></h4>
			<br><br>
			<h4><a href="" id="descarga_excel"></a></h4>
			<br><br>
			<button type="button" onclick="descargarExcel()" class="btn btn-success btn-lg">Ejecutar</button>
			
			<br>
			<?php include 'footer.php'; ?>
		</div>
		
	</div>

	<script type="text/javascript" src="js/excel.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("a[href=obtener-excel]").addClass("menu-activo");
		});
	</script>
</body>
</html>