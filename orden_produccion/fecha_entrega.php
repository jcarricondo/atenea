<?php
// Este fichero cambia la Fecha de Entrega de la Orden de Produccion
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/nombre_producto.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/productos/producto.class.php");
permiso(10);

$id_produccion = $_GET["id_produccion"];
$unidades = $_GET["unidades"];
$id_producto = $_GET["id_producto"];

$bbdd = new MySQL;
$orden_produccion = new Orden_Produccion();
$producto = new Producto();
$nombre_producto = new Nombre_Producto();
$funciones = new Funciones();

if(isset($_POST["cambiarFechasOP"]) and $_POST["cambiarFechasOP"] == 1){
	$fecha_entrega = $_POST["fecha_entrega"];
	$fecha_entrega_deseada = $_POST["fecha_entrega_deseada"];

	if(($fecha_entrega != NULL) and ($fecha_entrega_deseada != NULL)){
		// Convertimos las fechas a formato MySQL para guardarlas en la BBDD
		$fecha_entrega = $funciones->cFechaMy($fecha_entrega);
		$fecha_entrega_deseada = $funciones->cFechaMy($fecha_entrega_deseada);

		// Modificamos las fechas de entrega y entrega_deseada
		$resultado = $orden_produccion->actualizarFechasOP($id_produccion,$fecha_entrega,$fecha_entrega_deseada);
		if($resultado == 1){
			echo '<script type="text/javascript">opener.location.href="ordenes_produccion.php?fechas_mod=YES";window.close();</script>'; 
		}
		else{
			$mensaje_error = $orden_produccion->getErrorMessage($resultado);
		}
	} 
	else {
		echo '<script type="text/javascript">alert("Introduzca las fechas de entrega y entrega deseada");</script>';
	}
}

// Cargamos los datos de la Orden de Produccion
$orden_produccion->cargaDatosProduccionId($id_produccion);
$alias = $orden_produccion->alias_op;
$estado = $orden_produccion->estado;
$fecha_inicio = $orden_produccion->fecha_inicio;
$fecha_entrega = $orden_produccion->fecha_entrega;
$fecha_entrega_deseada = $orden_produccion->fecha_entrega_deseada;

// Convertimos las fechas MySQL a formato español
$fecha_inicio = $funciones->cFechaNormal($fecha_inicio);
$fecha_entrega = $funciones->cFechaNormal($fecha_entrega);
$fecha_entrega_deseada = $funciones->cFechaNormal($fecha_entrega_deseada);

// Cargamos los datos del producto
$producto->cargaDatosProductoId($id_producto);
$id_nombre_producto = $producto->id_nombre_producto;
// Cargamos los datos del nombre del producto
$nombre_producto->cargaDatosNombreProductoId($id_nombre_producto);
$nombre_del_producto = $nombre_producto->nombre;
?>
<script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
<script src="http://code.jquery.com/jquery-1.8.2.js"></script>
<script src="http://code.jquery.com/ui/1.9.1/jquery-ui.js"></script>
<script>
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
<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css" />
<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />

<div id="MuestraIniciar">
	<h1>Modificaci&oacute;n de las fechas de la Orden de Producci&oacute;n</h1>

	<div id="CapaFormularioIniciarOP">
		<form id="FormularioIniciarOP" name="cambiarFechasOrdenProduccion" action="fecha_entrega.php?id_producto=<?php echo $id_producto;?>&id_produccion=<?php echo $id_produccion;?>&unidades=<?php echo $unidades;?>" method="post">
       	<br />
        <h5>Introduzca las fechas que desee modificar</h5>
    	<div class="ContenedorCamposIniciarOP">
        	<div class="LabelIniciarOP">Orden de Producci&oacute;n</div>
        	<div class="LabelIniciarOP"><?php echo $alias; ?></div>
        </div>
        <div class="ContenedorCamposIniciarOP">
        	<div class="LabelIniciarOP">Producto</div>
        	<div class="LabelIniciarOP"><?php echo $nombre_del_producto; ?></div>
        </div>
        <div class="ContenedorCamposIniciarOP">
        	<div class="LabelIniciarOP">Unidades</div>
        	<div class="LabelIniciarOP"><?php echo $unidades; ?></div>
        </div>
        <div class="ContenedorCamposIniciarOP">
        	<div class="LabelIniciarOP">Estado</div>
        	<div class="LabelIniciarOP"><?php echo $estado; ?></div>
        </div>
    	<div class="ContenedorCamposIniciarOP">
        	<div class="LabelIniciarOP">Fecha Inicio</div>
        	<div class="LabelIniciarOP"><?php echo $fecha_inicio; ?></div>
        </div>
        <div class="ContenedorCamposIniciarOP">
        	<div class="LabelIniciarOP">Fecha Entrega * &nbsp;&nbsp;(DD/MM/AAAA)</div>
            <input type="text" id="datepicker_cambiar_fecha_entrega" name="fecha_entrega" class="fechaCal" value="<?php echo $fecha_entrega;?>" />
        </div>    
        <div class="ContenedorCamposIniciarOP">
        	<div class="LabelIniciarOP">Fecha Entrega Deseada * &nbsp;&nbsp;(DD/MM/AAAA)</div>
            <input type="text" id="datepicker_cambiar_fecha_deseada" name="fecha_entrega_deseada" class="fechaCal" value="<?php echo $fecha_entrega_deseada;?>" />
        </div>
 		<br />

        <div class="ContenedorCamposIniciarOP">
          	<input type="button" id="cerrar" name="cerrar" class="BotonEliminar" value="CERRAR" onclick="javascript:window.close()" /> 
            <input type="hidden" id="cambiarFechasOP" name="cambiarFechasOP" value="1" />
            <input type="submit" id="guardar" name="guardar" class="BotonEliminar" value="CONTINUAR" onclick="javascript: if (confirm('¿Desea modificar las fechas de la Orden de Producción?')) { window.location.href=fecha_entrega.php?id_producto=<?php echo $id_producto;?>&id_produccion=<?php echo $id_produccion;?>' } else { void('') };" />
        </div>
        <div class="mensajeCamposObligatoriosIniciarOP">
        	* Campos obligatorios
        </div>
        <?php 
			if($mensaje_error != "") {
				echo '<div class="mensaje_error">'.$mensaje_error.'</div>'; 
			}
		?>
        <br />
     	</form>
	</div> 
</div>