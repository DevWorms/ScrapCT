
function printConsola(texto){
	var previo = "";
	if($("#consola_amazon").html()){
		previo = $("#consola_amazon").html();
	}
	var print = previo + " <br> consola > " +  texto ;
	$("#consola_amazon").html(print);
}

function iniciarProceso(){
	printConsola("Proceso inicializado, <span style='color:blue'>oprima obtener categorias ...</span>");
	$("#segundo_amazon").slideDown(1000);
	$("#primer_amazon button").attr('disabled', 'true');
	 var tamano = 50 / 100;
    //inicializamos el dialog
    $( "#modal-avisoAmazon").dialog({
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
        title:"Aviso amazon"
    });

    $( "#modal-avisoAmazon").dialog('open');

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



