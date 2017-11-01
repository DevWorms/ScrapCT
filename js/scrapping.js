function printConsola(texto){
	var previo = "";
	if($("#consola_amazon").html()){
		previo = $("#consola_amazon").html();
	}
	var print = previo + " <br> consola > " +  texto ;
	$("#consola_amazon").html(print);
    $("#consola_amazon").animate({ scrollTop: ($("#consola_amazon").height() * 10) }, 5000);
}

function iniciarProceso(){
	printConsola("<span style='color:blue'>Proceso inicializado</span>");
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
        title:"Aviso Scrapping"
    });

    setProgreso(10);

    $( "#modal-avisoAmazon").dialog('open');

    $.ajax({
        url: APP_URL + 'class/Scrapping.php',
        success: function (res) {
            printConsola(res);
            setProgreso(90);
        },
        error: function (res) {
            printConsola("<span style='color:blue'>" + res.responseText + "</span>");
            setProgreso(100);
        }
    });
}

function setProgreso(progreso) {
	if(progreso <= 100){
		$("#bar_amazon").css('width', progreso + '%');
   		$("#bar_amazon").html(progreso + ' % ');
	}
}
