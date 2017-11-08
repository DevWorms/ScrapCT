var respiro = (2 * 60 * 1000);
var inicio = 1;
var fin = 0;
var ultimoId = 0;
var totalIntervalos = 0;
var indice = 0;
function printConsola(texto){
	var previo = "";
	if($("#consola_scrapping").html()){
		previo = $("#consola_scrapping").html();
	}
	var print = previo + " <br> consola > " +  texto ;
	$("#consola_scrapping").html(print);
    //$("#consola_scrapping").animate({ scrollTop: ($("#consola_scrapping").height() * 10) }, 5000);
}

function iniciarProceso(){
	printConsola("<span style='color:blue'>Proceso inicializado, obteniendo status</span>");
	$("#primer_scrap button").attr('disabled', 'true');
	 var tamano = 50 / 100;
    //inicializamos el dialog
    $( "#modal-avisoScrap").dialog({
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

    $( "#modal-avisoScrap").dialog('open');
    getLastId();

}

function setProgreso(progreso) {
	if(progreso <= 100){
		$("#bar_scrapping").css('width', progreso + '%');
   		$("#bar_scrapping").html(progreso + ' % ');
	}
}


function getLastId(){
    $.ajax({
        url: 'class/Search.php',
        type: 'POST',
        data: {'post': 'ultimoId' },
        dataType: 'html',
        success: function(response) {
            ultimoId = response;
            printConsola("Ultimo ID obtenido " + response);
        },
        error: function(error) {
            printConsola("<span style='color:red'>" + error + "</span>");
        },complete:function(){
            $("#segundo_scrap").slideDown(500);
            $("#primer_scrap").attr('disabled', 'true');
            obtenerURL();
        }
    });
}

function getIntervalos(){
    var intervalos = new Array();
    fin = 50;
    var intervalo = null;

    while(fin <= ultimoId){
        intervalo = {"inicio" : inicio , "fin" : fin};
        intervalos.push(intervalo);
        inicio = fin;
        fin = fin + 50 ;
    }

    if(fin > ultimoId){
        fin = fin -50;
    }

    if(fin < ultimoId){
        intervalo = {"inicio" : fin , "fin" : ultimoId};
        intervalos.push(intervalo);
    }

    return intervalos;

}


function getURLs(){
    var intervalos = getIntervalos();
    totalIntervalos = intervalos.length - 1;
    $.ajax({
        url: 'class/Search.php',
        type: 'POST',
        data: {'post': 'all' , 'inicio' : intervalos[indice].inicio , 'fin' : intervalos[indice].fin},
        dataType: 'html',
        success: function(response) {
            indice = indice + 1;
            printConsola(response);
        },
        error: function(error) {
            printConsola("<span style='color:red'>" + error + "</span>");
        },complete:function(){
            $("#segundo_scrap").attr('disabled', 'true');
            if(indice <= totalIntervalos){
                setTimeout(function(){
                    getURLs();
                },respiro);
                
            }else{
                indice = 0;
                $("#tercer_scrap").slideDown(500);
                printConsola("Url's de productos obtenidos");
            }
        }

    });

}


function ejecutarScraping(){
    var intervalos = getIntervalos();
    totalIntervalos = intervalos.length - 1;
    $.ajax({
        url: 'class/Scrapping.php',
        type: 'POST',
        data: {'post': 'init' , 'inicio' : intervalos[indice].inicio , 'fin' : intervalos[indice].fin},
        dataType: 'html',
        success: function(response) {
            indice = indice + 1;
            printConsola(response);
        },
        error: function(error) {
            printConsola("<span style='color:red'>" + error + "</span>");
        },complete:function(){
            $("#segundo_scrap").attr('disabled', 'true');
            if(indice <= totalIntervalos){
                setTimeout(function(){
                    ejecutarScraping();
                },respiro);
                
            }else{
                indice = 0;
                $("#cuarto_scrap").html("Termino el proceso del Scraping");
                printConsola("Proceso de scraping finalizado");
            }
        }

    });
}