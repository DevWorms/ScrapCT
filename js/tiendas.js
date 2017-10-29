$(document).ready(function() {
	getTiendas();
    $("#form-updateTienda").submit(function(event) {
        event.preventDefault();
        updateTienda();
    });
    $("#form-crearTienda").submit(function(event) {
        event.preventDefault();
        crearTienda();
    });
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
                	filas += "<td><a href='#' onclick='showUpdate("+tiendas[i].id+")'>Modificar</td>";
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

function showUpdate(id){
    // obtenemos el porcentaje
    var tamano = 50 / 100;
    //inicializamos el dialog
    $( "#modal-tiendas").dialog({
        autoOpen: false,
            show: {
                  effect: "clip",
                  duration: 500
                },
            hide: {
                  effect: "drop",
                  duration: 500
                },
             position: { 
                  my: "center", 
                  at: "center", 
                  of: window 
            },
        width: screen.width * tamano,
        resizable:false,
        title:"Modificar Tienda"
    });

    $.ajax({
        url: 'class/Tiendas.php',
        type: 'POST',
        data: {'post': 'getTienda', 'id':id },
        dataType: 'json',
        beforeSend: function() {
            showProgress();
        },
        success: function(response) {
            if (response.estado == 1) {
                var tienda = response.tienda[0];
                $("#u-nombre").val(tienda.tienda);
                $("#u-url").val(tienda.url);
                $("#u-clase").val(tienda.clase);
                $("#id_tienda").val(tienda.id);
                $("#modal-tiendas").dialog('open');
            }else{
                $.notify(response.mensaje, "error")
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

function updateTienda(){
    if($("#id_tienda").val()){
        var datos = $("#form-updateTienda").serialize();
        $.ajax({
            url: 'class/Tiendas.php',
            type: 'POST',
            data: datos,
            dataType: 'json',
            beforeSend: function() {
                showProgress();
            },
            success: function(response) {
                if (response.estado == 1) {
                    $.notify(response.mensaje,"info");
                }else{
                    $.notify(response.mensaje,"error");
                }
            },
            error: function(error) {
                $.notify("Ocurrio un error", "error")
                hideProgress();
            },
            complete: function() {
                $("#modal-tiendas").dialog('close');
                getTiendas();
            }
        }); 
    }   
}

function crearTienda(){
    var datos = $("#form-crearTienda").serialize();
    $.ajax({
        url: 'class/Tiendas.php',
        type: 'POST',
        data: datos,
        dataType: 'json',
        beforeSend: function() {
            showProgress();
        },
        success: function(response) {
            if (response.estado == 1) {
                $.notify(response.mensaje,"info");
            }else{
                $.notify(response.mensaje,"error");
            }
        },
        error: function(error) {
            $.notify("Ocurrio un error", "error")
            hideProgress();
        },
        complete: function() {
            getTiendas();
        }
    }); 
}

function deleteTienda(id){
    $.ajax({
        url: 'class/Tiendas.php',
        type: 'POST',
        data: {'id':id, 'post' : 'deleteTienda'},
        dataType: 'json',
        beforeSend: function() {
            showProgress();
        },
        success: function(response) {
            if (response.estado == 1) {
                $.notify(response.mensaje,"info");
            }else{
                $.notify(response.mensaje,"error");
            }
        },
        error: function(error) {
            $.notify("Ocurrio un error", "error")
            hideProgress();
        },
        complete: function() {
            getTiendas();
        }
    });
}