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
		<div class="col-md-9" class="box-container">
			<?php include 'footer.php'; ?>
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function() {
			$("a[href=tiendas]").addClass("menu-activo");
		});
	</script>
</body>
</html>