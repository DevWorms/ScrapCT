var respiro = 3000;
var inicio = 0;
var fin = 0;
var cuantosProductos = 0;
var totalIntervalos = 0;
var indice = 0;
var intervalos=null;

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
    getCuantosByCategoria();

}

function setProgreso(progreso) {
	if(progreso <= 100){
		$("#bar_scrapping").css('width', progreso + '%');
   		$("#bar_scrapping").html(progreso + ' % ');
	}
}


function getCuantosByCategoria(){
    var categoria = $("#categoria").val();
    if(categoria != ""){
        $.ajax({
            url: 'class/Search.php',
            type: 'POST',
            data: {'post': 'getCuantosByCategoria', 'id' : categoria },
            dataType: 'html',
            success: function(response) {
                cuantosProductos = response;
                printConsola("Cantidad de productos para esta categoria " + response);
            },
            error: function(error) {
                printConsola("<span style='color:red'>" + error + "</span>");
            },complete:function(){
                $("#segundo_scrap").slideDown(500);
                $("#primer_scrap").attr('disabled', 'true');
            }
        });
    }
    
}

function getIntervalos(){
    var intervalosObtenidos = new Array();
    fin = 50;
    var intervalo = null;

    while(inicio < cuantosProductos){
        if((cuantosProductos - inicio) < 50){
            var ultimo = cuantosProductos - inicio;
            intervalo = {"inicio" : inicio , "fin" : ultimo};
            intervalosObtenidos.push(intervalo);
            break;
        }else{
            intervalo = {"inicio" : inicio , "fin" : fin};
            intervalosObtenidos.push(intervalo);
            inicio+= 50 ;
        }

    }

    return intervalosObtenidos;
}


function getPrecios(){
    $("#segundo_scrap").html("Ejecutando scraping en precios " + "<img src='img/loading.gif' width='40' height='40'><br><br>");
    var categoria = $("#categoria").val();
    var tienda = $("#tiendas").val();
    var datos = 'prueba=precios' + '&inicio='+  intervalos[indice].inicio + '&fin='+ intervalos[indice].fin + '&categoria=' + categoria + "&shop="+tienda;
    $.ajax({
        url: 'class/Scrapping.php',
        type: 'POST',
        data: datos,
        dataType: 'html',
        success: function(response) {
            printConsola(intervalos[indice].inicio + " , " + intervalos[indice].fin);
            indice = indice + 1;
            printConsola(response);
        },
        error: function(error) {
            printConsola("<span style='color:red'>" + error + "</span>");
        },complete:function(){
            if(indice <= totalIntervalos){
                setTimeout(function(){
                    getPrecios();
                },respiro);
            }else{
                $("#segundo_scrap").html("Completado");
                $("#tercer_scrap").slideDown(500);
                printConsola("Precios actualizados");
                indice = 0;
                inicio = 0;
                fin = 0;
                intervalos = null;
            }
        }

    });

}




function getCategorias(){
    $.ajax({
        url: 'class/Search.php',
        type: 'POST',
        data: {'post': 'getCategorias' },
        dataType: 'json',
        beforeSend: function() {
            showProgress();
        },
        success: function(response) {
            if (response.estado == 1) {
                var categorias = response.categorias;
                var options = "<option>Elige una categoria</option>";
                options += "<option value='allCategories'>Todas las categorias</option>";
                for (var i = categorias.length - 1; i >= 0; i--) {
                    options += "<option value='" + categorias[i].term_taxonomy_id+ "'>"+categorias[i].name+"</option>";
                }

                $("#categoria").html(options);
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

function setIntervalos(){
    intervalos = getIntervalos();
    totalIntervalos = intervalos.length - 1;
}

$(document).ready(function() {
    getCategorias();
});