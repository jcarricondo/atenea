<?php 
include("../includes/sesion.php");
include("../classes/imputaciones/imputacion.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");			
include("../classes/orden_produccion/listado_ordenes_produccion.class.php");
permiso(33);

if(isset($_POST["guardandoImputacion"]) and $_POST["guardandoImputacion"] == 1) {
	// Se reciben los datos
	$fecha = $_POST["fecha"];
	$tipo_trabajo = $_POST["tipo_trabajo"];
	$horas = $_POST["horas"];
	$orden_produccion = $_POST["orden_produccion"];
	$descripcion = $_POST["descripcion"];
			
	$imputacion = new Imputacion();
	$imputacion->datosNuevaImputacion(NULL,$imputacion->cFechaMy($fecha),$tipo_trabajo,$horas,$orden_produccion,$descripcion,$_SESSION["AT_id_usuario"]);
	$resultado = $imputacion->guardarCambios();
	if($resultado == 1) {
		header("Location: imputacion.php?imputacion=creado");
	} else {
		$mensaje_error = $imputacion->getErrorMessage($resultado);
	}
}
else {
	$fecha = "";
	$tipo_trabajo = 0;
	$horas = "";
	$orden_produccion = 0;
	$descripcion = "";
	$Campos_no_rellenados = false;
}
$jq = '$(function() {
		$("#fecha").datepicker({ dateFormat: "dd/mm/yy" });
	});';

$titulo_pagina = "Nueva imputación de horas";
$pagina = "new_imputacion";
include ('../includes/header.php');
?>
<script type="text/javascript">

	function validarFormulario() {
		var error1 = false;
		if(!/^([0][1-9]|[12][0-9]|3[01])(\/|-)(0[1-9]|1[012])\2(\d{4})$/.test(document.getElementById("fecha").value)) {
			alert("La fecha indicada no tiene el formato valido: dd/mm/aaaa");
			error1 = true;
		}
		if(document.getElementById("tipo_trabajo").value == 0) {
			error1 = true;
		}
		if(document.getElementById("orden_produccion").value == 0) {
			error1 = true;
		}
		if(isNaN(document.getElementById("horas").value)) {
			error1 = true;
		}
		if (error1) {
			alert("Rellene los campos obligatorios");
			return false;
		}
		else return true;
	}
	
</script>

<div class="separador"></div> 
<?php include("../includes/menu_imputaciones.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
 	</div>
      
    <h3>Crear una nueva imputación</h3>
    <form id="FormularioCreacionBasico" name="crearOrdenProduccion"  onsubmit="return validarFormulario()" action="nueva_imputacion.php" method="post">
		<br />
        <h5>Rellene los siguientes campos para guardar la imputación de horas</h5>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Orden de Produccion *</div>
            <select id="orden_produccion" name="orden_produccion"  class="CreacionBasicoInput">
            	<option value="0">Selecciona</option>
            <?php
				$orden_produccion = new listadoOrdenesProduccion();
				$orden_produccion->setValores("","","","","","","","","","BORRADOR,INICIADO","");
				$orden_produccion->prepararConsulta();
				$orden_produccion->realizarConsulta();
				$listadoOrdenes = $orden_produccion->ordenes_produccion;
				$listaMostrados = array();
				for($i=0;$i<count($listadoOrdenes);$i++) {
					$ordenProduccion = new Orden_Produccion();
					$ordenProduccion->cargaDatosProduccionId($listadoOrdenes[$i]["id_produccion"]);
					if(!in_array($ordenProduccion->id_produccion,$listaMostrados)) {
						array_push($listaMostrados,$ordenProduccion->id_produccion);
						?>
                        <option value="<?php echo $ordenProduccion->id_produccion;?>"<?php if($ordenProduccion->id_produccion == $orden_produccion) { echo ' selected="selected"'; } ?>><?php echo $ordenProduccion->codigo; ?></option>
                        <?php
					}
				}
			?>
            </select>

        </div>
    	<div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Fecha *</div>
            <input type="text" id="fecha" name="fecha" class="CreacionBasicoInput" readonly="readonly" value="<?php echo $fecha;?>" />
        </div>  
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Tipo de Trabajo *</div>
            <select id="tipo_trabajo" name="tipo_trabajo"  class="CreacionBasicoInput">
            	<option value="0">Selecciona</option>
                <option value="1"<?php if($tipo_trabajo == 1) { echo ' selected="selected"'; }?>>Mecánico</option>
                <option value="2"<?php if($tipo_trabajo == 2) { echo ' selected="selected"'; }?>>Eléctrico</option>
                <option value="3"<?php if($tipo_trabajo == 3) { echo ' selected="selected"'; }?>>Electrónico</option>
                <option value="4"<?php if($tipo_trabajo == 4) { echo ' selected="selected"'; }?>>Gestión y supervisión</option>
            </select>

        </div>
        <div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Horas *</div>
            <input type="text" id="horas" name="horas" class="CreacionBasicoInput" value="<?php echo $horas;?>" />
        </div>    
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Descripcion</div>
          	<textarea id="descripcion" name="descripcion" rows="5" class="textareaInput"><?php echo $descripcion; ?></textarea>
        </div> 
        <div class="ContenedorBotonCreacionBasico">
           	<input type="button" id="volver" name="volver" value="Volver" onclick="javascript:window.history.back()"/> 
            <input type="hidden" id="guardandoImputacion" name="guardandoImputacion" value="1"/>
            <input type="submit" id="continuar" name="continuar" value="Guardar" />
        </div>
        <div class="mensajeCamposObligatorios">
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

<?php include ("../includes/footer.php"); ?>

