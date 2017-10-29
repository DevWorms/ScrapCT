
function printConsola(texto){
	var previo = "";
	if($("#consola_amazon").html()){
		previo = $("#consola_amazon").html();
	}
	var print = previo + " <br> consola > " +  texto ;
	$("#consola_amazon").html(print);
}

function iniciarProceso(){
	printConsola("Proceso inicializado, oprima obtener categorias ...");
	$("#segundo_amazon").slideDown(1000);
	$("#primer_amazon button").attr('disabled', 'true');
}

function obtenerCategorias(){
	$("#tercero_amazon").slideDown(1000);
	printConsola("<span style='color:blue'>Obteniendo Categorias ...</span>");
	$("#segundo_amazon button").attr('disabled', 'true');
	$.ajax({
        url: 'class/AmazonConnection.php',
        type: 'POST',
        data: {'post': 'obtenerCategorias' },
        dataType: 'html',
        success: function(response) {
            printConsola(response);
        },
        error: function(error) {
        	$.notify("Ocurrio un error", "error")
        }
    });
}

function ejecutarProceso(){
	printConsola("<span style='color:blue'>Ejecutando proceso AMAZON ...</span>");
	$("#tercero_amazon button").attr('disabled', 'true');
}



