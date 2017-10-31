$(document).ready(function() {
	//evento click del formulario
	$("#button-login").click(function(event) {
		login();
	});
});
/**
 * Inicio de sesion
 * @return none
 */
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
			if(response.estado == 1){
				var contenido = "<p style='color:blue'><br>" + response.mensaje+ "<br> Iniciando ... </p>";
				$("#confirmacion").html(contenido);
				setTimeout(function(){
					window.location.replace("tiendas");
				},2000);
			}else{
				var contenido = "<p style='color:red'><br>" + response.mensaje+ "*** </p>";
				$("#confirmacion").html(contenido);
			}
		},
		error:function(erro) {
			alert(erro);
		}
	});
}