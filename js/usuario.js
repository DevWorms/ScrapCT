$(document).ready(function() {
	getUsuarios();
    $("#form-crearUsuario").submit(function(event) {
        event.preventDefault();
        crearUsuario();
    });
});

/**
 * Obtiene la lista de usuarios y los pone en la tabla
 */
function getUsuarios(){
	$.ajax({
        url: 'class/User.php',
        type: 'POST',
        data: {'post': 'getUsuarios' },
        dataType: 'json',
        beforeSend: function() {
            showProgress();
        },
        success: function(response) {
            if (response.estado == 1) {
                var filas = "";
                var usuarios = response.usuarios;
                for (var i = usuarios.length - 1; i >= 0; i--) {
                	filas += "<tr>";
                	filas += "<td>" + usuarios[i].usuario + "</td>";
                	filas += "<td>" + usuarios[i].apellido + "</td>";
                	filas += "<td>" + usuarios[i].correo + "</td>";
                	filas += "<td><a href='#' onclick='deleteUsuario("+usuarios[i].id+")'>Eliminar</td>";
                	filas += "</tr>";
                }

                $("#tbl-usuarios tbody").html(filas);
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

function crearUsuario(){
	var datos = $("#form-crearUsuario").serialize();
	$.ajax({
        url: 'class/User.php',
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
       		getUsuarios();
        }
    });
}

function deleteUsuario(id){
	$.ajax({
        url: 'class/User.php',
        type: 'POST',
        data: {'id':id, 'post' : 'deleteUsuario'},
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
       		getUsuarios();
        }
    });
}