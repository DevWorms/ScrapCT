function descargarExcel(){
	$.ajax({
        url: 'class/GenerarExcel.php',
        type: 'POST',
        data: {'post': 'genera_excel' },
        dataType: 'html',
        beforeSend: function() {
            showProgress();
            $("#info_excel").html("Generando excel ... ");
        },
        success: function(response) {
            $("#info_excel").html("Excel obtenido ahora puedes descargarlo en el siguiente enlace");
			$("#descarga_excel").attr('href', response);
			$("#descarga_excel").html('Descargar excel ' + '<img src="img/download.png">');
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