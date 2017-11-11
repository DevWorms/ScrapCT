$(document).ready(function() {
    $("#btn_buscar").click(function(event) {
        buscador();
    });
});

function buscador(){
    var buscar = document.getElementById('buscar').value;
    if(buscar != ""){
        $.ajax({
        url: 'class/CustomPost.php',
        type: 'POST',
        data: {'post': 'buscador', 'criterio' : buscar },
        dataType: 'json',
        beforeSend: function() {
            showProgress();
        },
        success: function(response) {
            if (response.estado == 1) {
                var filas = "";
                var productos = response.productos;
                for (var i = productos.length - 1; i >= 0; i--) {
                    filas += "<tr>";
                    filas += "<td>" + productos[i].ID + "</td>";
                    filas += "<td>" + productos[i].post_title + "</td>";
                    filas += "<td>" + productos[i].post_name + "</td>";
                    filas += "<td><a href='"+productos[i].guid +"'>"+productos[i].guid+"</td>";
                    filas += "<td><a href='#' onclick='mostrarModificar("+productos[i].ID+")'><img src='img/edit.png'/></td>";
                    filas += "<td><a href='#' onclick='eliminar("+productos[i].ID+")'><img src='img/delete.png'/></td>";
                    filas += "</tr>";
                }

                $("#tbl-productos tbody").html(filas);
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
	
}

function eliminar(id){
    $.ajax({
        url: 'class/CustomPost.php',
        type: 'POST',
        data: {'id':id, 'post' : 'eliminar'},
        dataType: 'json',
        beforeSend: function() {
            showProgress();
        },
        success: function(response) {
            if (response.estado == 1) {
                $.notify(response.mensaje,"info");
            }else{
                $.notify(response.mensaje,"error");
                hideProgress();
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


function mostrarModificar(id){
    // obtenemos el porcentaje
    var tamano = 60 / 100;
    //inicializamos el dialog
    $( "#modal-custompost").dialog({
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
        title:"Modificar Custom Post"
    }).dialog('open');


    $.ajax({
        url: 'class/CustomPost.php',
        type: 'POST',
        data: {'post': 'getProducto', 'id':id },
        dataType: 'json',
        beforeSend: function() {
            showProgress();
        },
        success: function(response) {
            if (response.estado == 1) {
                var post = response.producto;
                for (var i =0; i< post.length ; i++) {
                    setValue(post[i].meta_key, post[i].meta_value);
                    setValue('post_name', post[i].post_name);
                    setValue('post_title', post[i].post_title);
                    setValue('ID', post[i].ID);
                }

            }else{
                $.notify(response.mensaje, "error")
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

function setValue(tag, value){
    $( "input[name='"+ tag +"']" ).val(value);
}

function modificar(){

        var datos = $("#form-modificaProducto").serialize();
        $.ajax({
            url: 'class/CustomPost.php',
            type: 'POST',
            data: datos + "&post=modificar",
            dataType: 'json',
            beforeSend: function() {
                showProgress();
            },
            success: function(response) {
                if (response.estado == 1) {
                    $.notify(response.mensaje,"info");
                }else{
                    $.notify(response.mensaje,"error");
                    hideProgress();
                }
            },
            error: function(error) {
                $.notify("Ocurrio un error", "error")
                hideProgress();
            },
            complete: function() {
                $("#modal-custompost").dialog('close');
                hideProgress();
            }
        }); 
       
}