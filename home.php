<!DOCTYPE html>
<html>
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
				<input type="hidden" name="post" id="post" value="crearTienda">

				<div class="row">
					<div class="col-md-4" align="left">
						<label for="nombre">
							<b>Nombre de la Tienda</b>
							<input type="text" id="nombre" name="nombre" class="form-control" required>
						</label>
					</div>
					<div class="col-md-4" align="left">
						<label for="apellido">
							<b>URL</b>
							<input type="text" id="url" name="url" class="form-control" required>
						</label>
					</div>
					<div class="col-md-4" align="left">
						<label for="correo">
							<b>Clase</b>
							<input type="text" id="clase" name="clase" class="form-control" required>
						</label>
					</div>
				</div>
				<br>
				<button class="btn btn-primary" type="submit">
					Agregar
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
		<!--MODAL MODIFICACION-->
		<div id="modal-tiendas" title="x" style="overflow-x:hidden;display: none" align="left">
			<form id="form-updateTienda">
				<input type="hidden" name="post" id="post" value="updateTienda">
				<input type="hidden" name="id_tienda" id="id_tienda">
				<div class="row">
					<div class="col-md-6" align="left">
						<label for="nombre">
							<b>Tienda</b>
							<input type="text" id="u-nombre" name="u-nombre" class="form-control" required>
						</label>
						<br>
						<label for="apellido">
							<b>URL</b>
							<input type="text" id="u-url" name="u-url" class="form-control" required>
						</label>
					</div>
					<div class="col-md-6" align="left">
						<label for="correo">
							<b>Clase</b>
							<input type="text" id="u-clase" name="u-clase" style="width: 100%" class="form-control" required>
						</label>
						<br>
						<button class="btn btn-primary" type="submit">
							Modificar
						</button>	
					</div>
				</div>
			</form>
		</div>
		<!--/MODAL TIENDA-->
	</div>

	<script type="text/javascript" src="js/tiendas.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("a[href=tiendas]").addClass("menu-activo");
		});
	</script>
</body>
</html>