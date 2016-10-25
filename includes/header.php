<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $titulo_pagina; ?> | Atenea</title>

<link rel="stylesheet" type="text/css" media="all" href="../js/tinybox/style_tiny.css" />
<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
<link rel="stylesheet" type="text/css" media="all" href="../js/jquery/css/ui-lightness/jquery-ui-1.8.22.custom.css" />
<link rel="stylesheet" type="text/css" media="all" href="../js/Source/datepicker_vista/datepicker_vista.css" />

<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css" />

</head>

<script>
function abrir(url) {
//open(url,'','top=200,left=700,width=500,height=500') ;
open(url,'','top=200,left=450,width=1000,height=500') ;
}

function Abrir_ventana(pagina) {
	// width = 820 height=500 top = 200 left= 500
	var opciones="toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=1220, height=600, top=100, left=350";
	window.open(pagina,"",opciones);
}

</script>

<script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
<script type="text/javascript" src="../js/funciones.js"></script>
<script type="text/javascript" src="../js/tinybox/tinybox.js"></script>
<script src="http://code.jquery.com/jquery-1.8.2.js"></script>
<script src="http://code.jquery.com/ui/1.9.1/jquery-ui.js"></script>
<!--<script  src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>-->
<link rel="stylesheet" href="/resources/demos/style.css" />

<script>
	 
	 jQuery(document).ready(function() {
		$(".BotonMenu").click(function(){
  		var href = $this.attr('href');
		ruta = href;
      	window.location=ruta;
     	return false;
 		});
 	 }); 
	 
	 jQuery(document).ready(function() {
		$(".BotonMenuActual").click(function(){
  		var href = $this.attr('href');
		ruta = href;
      	window.location=ruta;
     	return false;
 		});
 	 }); 
	 
	 jQuery(document).ready(function() {
		$(".BotonMenuActualOP").click(function(){
  		var href = $this.attr('href');
		ruta = href;
      	window.location=ruta;
     	return false;
 		});
 	 }); 
	 <?php
	 echo $jq;
	 ?>
	 
    jQuery(function($){
        $.datepicker.regional['es'] = {
                closeText: 'Cerrar',
                prevText: '&#x3c;Ant',
                nextText: 'Sig&#x3e;',
                currentText: 'Hoy',
                monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
                monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
                'Jul','Ago','Sep','Oct','Nov','Dic'],
                dayNames: ['Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado'],
                dayNamesShort: ['Dom','Lun','Mar','Mi&eacute;','Juv','Vie','S&aacute;b'],
                dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','S&aacute;'],
                weekHeader: 'Sm',
                dateFormat: 'dd/mm/yy',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''};
        $.datepicker.setDefaults($.datepicker.regional['es']);
	});
	
	$(function() {
        $( ".fechaCal" ).datepicker();
    });
	
</script>

<body>
	<div id="ContenedorHeader">
      <div class="LogoCabecera">
          <a href="../principal/index.php">
              <?php
                switch ($_SESSION["AT_id_sede"]) {
                    case 0:
                        echo '<img src="../images/atenea1.jpg" width="150" height="74" alt="" />'; 
                    break;
                    case 1:
                        echo '<img src="../images/atenea1.jpg" width="150" height="74" alt="" />'; 
                    break;
                    case 2:
                        echo '<img src="../images/atenea1_toro.jpg" width="150" height="74" alt="" />'; 
                    break;
                    case 3:
                        echo '<img src="../images/atenea1_bra.jpg" width="150" height="74" alt="" />'; 
                    break;
                    case 4:
                        echo '<img src="../images/atenea1_fra.jpg" width="150" height="74" alt="" />';
                    break;
                    default:
                        echo '<img src="../images/atenea1.jpg" width="150" height="74" alt="" />'; 
                    break;
                }
              ?>
          </a>
      </div> 
            
      <div class="TituloCabecera">
          <?php echo $titulo_pagina; ?>
      </div>   
      <div class="LogoSimumak">
          <a href="http://www.simumak.com/es" target="_blank">
				  <?php
        	   echo '<img src="../images/simumak-logo.jpg" width="250" height="67" alt="Simumak" title="Simumak">';
				  ?>
          </a>
      </div>
	</div> 