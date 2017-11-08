<?php
require_once __DIR__ . '/app/link.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <link rel="stylesheet" type="text/css" href="<?php echo app_url(); ?>css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Bree+Serif|Work+Sans" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo app_url(); ?>css/estilos.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/start/jquery-ui.css">
    <title>Teccheck</title>
</head>
<body>
<?php
include 'header.php';
?>
<div class="row body">

    <?php
    include 'menu.php';
    ?>

    <div class="col-md-9 box-container cuerpo">
        <h3>Actualizar precios<br><br></h3>
        <div class="row">
            <div class="col-md-12" align="center">
                <ol class="lista_amazon">
                    <li id="primer_scrap">
                        <button class="btn btn-info" onclick="iniciarProceso();">
                            Iniciar
                        </button>
                        <br><br>
                    </li>
                    <li id="segundo_scrap" style="display: none;">
                        <button class="btn btn-success" onclick="getURLs();">
                            Obtener URL de productos
                        </button>
                        <br><br>
                    </li>
                    <li id="tercer_scrap" style="display: none;">
                        <button class="btn btn-primary" onclick="ejecutarScraping();">
                            Ejecutar Scraping
                        </button>
                        <br><br>
                    </li>
                    <li id="cuarto_scrap"></li>
                </ol>
                <div class="bar_fondo" style="display: none">
                    <div id="bar_scrapping" class="bar_liquid">
                        0
                    </div>
                </div>

            </div>
            <div class="col-md-12">
                <label for="consola_scrapping">
                    <b>Consola</b>
                </label>
                <div id="consola_scrapping" class="form-control consola"></div>
            </div>
        </div>
        <?php include 'footer.php' ?>
    </div>
    <div id="modal-avisoScrap" title="x" style="overflow-x:hidden;display: none" align="left">
        <h3 style="color: red">
            <b>Atención ! </b>
            <img src="<?php echo app_url(); ?>img/warning.png">
        </h3 style="color: black">
        <h4>Antes y durante el proceso es importante no apagar el equipo, seguir los pasos, no cerrar el navegador, y verificar que se tienen una buena conexión a internet.</h4>
    </div>
</div>

<script type="text/javascript" src="<?php echo app_url(); ?>js/scrapping.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $("a[href=scrapping]").addClass("menu-activo");
    });
</script>
</body>
</html>