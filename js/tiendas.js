$(document).ready(function() {
	getTiendas();
});

/**
 * Obtiene la lista de usuarios y los pone en la tabla
 */
function getTiendas(){
	$.ajax({
        url: 'class/Tiendas.php',
        type: 'POST',
        data: {'post': 'getTiendas' },
        dataType: 'json',
        beforeSend: function() {
            showProgress();
        },
        success: function(response) {
            if (response.estado == 1) {
                var filas = "";
                var tiendas = response.tiendas;
                for (var i = tiendas.length - 1; i >= 0; i--) {
                	filas += "<tr>";
                	filas += "<td>" + tiendas[i].tienda + "</td>";
                	filas += "<td>";
                	filas += "<a href='" + tiendas[i].url + "' target='_blank'>" + tiendas[i].url + "</a>";
                	filas += "</td>";
                	filas += "<td>" + tiendas[i].clase + "</td>";
                	filas += "<td><a href='#' onclick='deleteTienda("+tiendas[i].id+")'>Eliminar</td>";
                	filas += "<td><a href='#' onclick='updateTienda("+tiendas[i].id+")'>Modificar</td>";
                	filas += "</tr>";
                }

                $("#tbl-tiendas tbody").html(filas);
            }
        },
        error: function(error) {
        	$.notify("Ocurrio un error", "error")
           	hideProgress();
        },
        complete: function() {
       		hideProgress();
        }
    });
}