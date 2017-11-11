<?php 
	require_once __DIR__ . '/app/link.php';
?>
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
			<h3>Custom posts<br><br></h3>
			<div class="row">
				<div class="col-md-9">
					<input type="text" id="buscar" name="buscar" class="form-control" placeholder="Buscar producto">
				</div>
				<div class="col-md-3">
					<button class="btn btn-info" id="btn_buscar">Buscar</button>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col-md-12" style="height: 400px;overflow-y: scroll;">
					<table class="table table-striped table-condensed" id="tbl-productos">
						<thead>
							<tr>
								<td>ID</td>
								<td>Post title</td>
								<td>Post name</td>
								<td>Guid</td>
								<td>Modificar</td>
								<td>Eliminar</td>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			<!--MODAL MODIFICACION-->
			<div id="modal-custompost" title="x" style="display: none;" >
				<form id="form-modificaProducto" style="height:450px;overflow-x:hidden;overflow-y: scroll;">
					<input type="hidden" name="ID">
					<div class="row">
						<div class="col-md-12">
							Post name
							<input type="text" name="post_name" class="form-control" placeholder="Post name">
							<br>
							Post title
							<input type="text" name="post_title" class="form-control" placeholder="Post title">
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-md-6">
							Modelo
							<input type="text" name="model" class="form-control" placeholder="Modelo">
							<br>
							Fabricante
							<input type="text" name="company" class="form-control" placeholder="Fabricante">
							<br>
							Imagen
							<input type="text" name="picture" class="form-control" placeholder="Imagen">
							<br>
							Amazon Link
							<input type="text" name="amazon_pl" class="form-control" placeholder="Amazon Link">
							<br>
							ASIN
							<input type="text" name="asin" class="form-control" placeholder="ASIN">
							<br>
							Linio link
							<input type="text" name="linio_pl" class="form-control" placeholder="Linio Link">
							<br>
							Samborns link
							<input type="text" name="sanborns_pl" class="form-control" placeholder="Samborns link">
							<br>
							Liverpool link
							<input type="text" name="liverpool_pl" class="form-control" placeholder="Liverpool link">
							<br>
							ClaroShop link
							<input type="text" name="claroshop_pl" class="form-control" placeholder="ClaroShop link">
							<br>
							Coppel link
							<input type="text" name="coppel_pl" class="form-control" placeholder="Coppel link">
							<br>
							Sears link
							<input type="text" name="sears_pl" class="form-control" placeholder="Sears Link">
							<br>
							Sams club  link
							<input type="text" name="sams_pl" class="form-control" placeholder="Sams club link">
							<br>
							BestBuy link
							<input type="text" name="bestbuy_pl" class="form-control" placeholder="BestBuy link">
							<br>
							Walmart Link
							<input type="text" name="walmart_pl" class="form-control" placeholder="Walmart link">
							<br>
						</div>
						<div class="col-md-6">
							Amazon link del afiliado
							<input type="text" name="amazon_affiliate_link" class="form-control" placeholder="Amazon affiliate link">
							<br>
							Elektra link
							<input type="text" name="elektra_pl" class="form-control" placeholder="Elektra link">
							<br>
							Precio sams
							<input type="text" name="price_sams" class="form-control" placeholder="Precio sams">
							<br>
							Precio sears
							<input type="text" name="price_sears" class="form-control" placeholder="Precio sears">
							<br>
							Precio cyberpuerta
							<input type="text" name="price_cyberpuerta" class="form-control" placeholder="Precio cyberpuerta">
							<br>
							Precio Linio
							<input type="text" name="price_linio" class="form-control" placeholder="Precio linio">
							<br>
							Precio amazon
							<input type="text" name="price_amazon" class="form-control" placeholder="Precio amazon">
							<br>
							Precio claroshop
							<input type="text" name="price_claroshop" class="form-control" placeholder="Precio claroshop">
							<br>
							Precio coppel
							<input type="text" name="price_coppel" class="form-control" placeholder="Precio coppel">
							<br>
							Precio bestbuy
							<input type="text" name="price_bestbuy" class="form-control" placeholder="Precio bestbuy">
							<br>
							Precio sanborns
							<input type="text" name="price_sanborns" class="form-control" placeholder="Precio sanborns">
							<br>
							Precio Liverpool
							<input type="text" name="price_liverpool" class="form-control" placeholder="Precio liverpool">
							<br>
							Mejor precio
							<input type="text" name="price_best" class="form-control" placeholder="Mejor precio">
							<br>
							Mejor tienda
							<input type="text" name="best_shop" class="form-control" placeholder="Mejor tienda">
							<br>
							<button type="button" class="btn btn-success" onclick="modificar()">Actualzar</button>
						</div>
					</div>
				</form>
			</div>
			<!--/MODAL TIENDA-->
		</div>
	</div>
	<br>
	<?php include 'footer.php'; ?>
	<script type="text/javascript" src="<?php echo app_url(); ?>js/custom-post.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("a[href=custom-post]").addClass("menu-activo");
		});
	</script>
</body>
</html>