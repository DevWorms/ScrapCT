$(document).ready(function() {

	//evento click del formulario
	$("#button-login").click(function(event) {
		login();
	});

});

function login(){
	var datos = $("#form-login").serialize();
	$.ajax({
		url: 'class/User.php',
		type: 'POST',
		dataType: 'json',
		data: datos,
		beforeSend: function() {
            $("#confirmacion").html("<p>Iniciando sesion</p>");
        },
		success:function(response){
			var contenido = "<p>" + response.mensaje+ "</p>";
			$("#confirmacion").html(contenido);

			if(response.estado == 1){
				//redireccion
			}
		},
		error:function(erro) {
			alert(erro);
		}
	});
	
}