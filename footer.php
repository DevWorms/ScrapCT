<script>
	window.jQuery || document.write('<script src="<?php echo app_url(); ?>js/vendor/jquery-1.11.2.min.js"><\/script>')
	var APP_URL = '<?php echo app_url(); ?>';
</script>
<script type="text/javascript" src="<?php echo app_url(); ?>js/vendor/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo app_url(); ?>js/notify.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<div  id="wait">
     <div id="progressbar"></div>
</div>
<br><br>
<footer class="cuerpo">
	<img src="<?php echo app_url(); ?>img/logo-teCheck-home.png">
	<br><br>
	Copyright 2017 |  Todos los derechos reservados  |  Aviso Legal
</footer>
<script type="text/javascript">
	function showProgress(){
	   $( "#progressbar" ).progressbar({
	      value: false
	   });
	   $("#wait").show();
	   var progressbar = $( "#progressbar" );
	   progressbar.progressbar( "option", "value", false );
	}

	function hideProgress(){
		$("#wait").hide();
        $("#progressbar").progressbar("destroy");
	}
</script>