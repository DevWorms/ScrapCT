$('document').ready(function() {
	alert("HOLA");
	//evento click del boton login
	$("#button-login").click(function(){
		iniciarSesion();
	});

});

/**
 * Funcion para le login
 */
function iniciarSesion(){
	$.ajax({
        type : 'POST',
        url  : 'class/User.php',
        data : {
           "post" : "login"
           "correo" : $("#correo").val(),
           "contrasena" : $("#contrasena").val()
        },
        dataType: 'json',
        success :  function(response) {
            response = JSON.parse(response);
            if (response.estado == 1) {
               $.notify(response.mensaje,'info');
            } else {
               $.notify(response.mensaje,'error');
            }
        },
        error : function (error) {
            $.notify(error,'error');
        }
    });
}