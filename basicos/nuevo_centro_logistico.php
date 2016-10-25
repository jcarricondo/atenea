<?php
// Este fichero crea un nuevo centro logistico de basicos
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/centro_logistico.class.php");
permiso(35);

if(isset($_POST["guardandoCentroLogistico"]) and $_POST["guardandoCentroLogistico"] == 1) {
	// Se reciben los datos
	$nombre = $_POST["nombre"];
	$direccion = $_POST["direccion"];
	$telefono = $_POST["telefono"];
	$email = $_POST["email"];
	$ciudad = $_POST["ciudad"];
	$pais = $_POST["pais"];
	$forma_pago = $_POST["forma_pago"];
	$metodo_pago = $_POST["metodo_pago"];
	$tiempo_suministro = $_POST["tiempo_suministro"];
	$descripcion = $_POST["descripcion"];
	$provincia = $_POST["provincia"];
	$codigo_postal = $_POST["codigo_postal"];
	$persona_contacto = $_POST["persona_contacto"];
	
	$validacion = new Funciones();
	
	if (($nombre == '') or ($direccion == '') or ($telefono == '') or ($email == '') or ($ciudad == '') or ($pais == '')){
		echo '<script type="text/javascript">alert("Rellene los campos obligatorios")</script>';
	}
	elseif (!$validacion->verificarSoloDigitos($telefono)) {
		echo '<script type="text/javascript">alert("El telefono introducido no es correcto")</script>';
	}
	elseif (!$validacion->verificarEmail($email)){
		echo '<script type="text/javascript">alert("El email introducido no es correcto")</script>';
	}
	elseif (!$validacion->verificarSoloDigitos($codigo_postal)){
		echo '<script type="text/javascript">alert("El codigo postal introducido no es correcto")</script>';
	}
	else {
		$centroLogistico = new CentroLogistico();
		$centroLogistico->datosNuevoCentroLogistico(NULL,$nombre,$descripcion,$direccion,$ciudad,$pais,$telefono,$email,$forma_pago,$tiempo_suministro,$metodo_pago,$provincia,$codigo_postal,$persona_contacto);
		$resultado = $centroLogistico->guardarCambios();
		if($resultado == 1) {
			header("Location: centros_logisticos.php?centro_logistico=creado");
		} else {
			$mensaje_error = $centroLogistico->getErrorMessage($resultado);
		}
	}
} 
else {
	$nombre = "";
	$direccion = "";
	$telefono = "";
	$email = "";
	$ciudad = "";
	$pais = "";
	$forma_pago = "";
	$metodo_pago = "";
	$tiempo_suministro = "";
	$descripcion = "";
	$provincia = "";
	$codigo_postal = "";
	$persona_contacto = "";
}

$titulo_pagina = "Básico > Nuevo Centro Logístico";
$pagina = "new_centro_logistico";
include ('../includes/header.php');
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>

    <h3> Creaci&oacute;n de un nuevo centro logístico </h3>
    
    <form id="FormularioCreacionBasico" name="crearCentroLogistico" action="nuevo_centro_logistico.php" method="post">
    	<br />
        <h5> Rellene los siguientes campos para la creaci&oacute;n de un nuevo centro logístico </h5>
    	<div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Nombre *</div>
            <input type="text" id="nombre" name="nombre" class="CreacionBasicoInput" value="<?php echo $nombre; ?>" />
        </div>    
        <div class="ContenedorCamposCreacionBasico">
          	<div class="LabelCreacionBasico">Dirección *</div>
           	<input type="text" id="direccion" name="direccion" class="CreacionBasicoInput" value="<?php echo $direccion; ?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Teléfono *</div>
           	<input type="text" id="telefono" name="telefono" class="CreacionBasicoInput" value="<?php echo $telefono; ?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
          	<div class="LabelCreacionBasico">Email *</div>
           	<input type="text" id="email" name="email" class="CreacionBasicoInput" value="<?php echo $email; ?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Ciudad *</div>
            <input type="text" id="ciudad" name="ciudad" class="CreacionBasicoInput" value="<?php echo $ciudad; ?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Pais *</div>
           	<input type="text" id="pais" name="pais" class="CreacionBasicoInput" value="<?php echo $pais; ?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Forma de pago</div>
           	<select id="forma_pago" name="forma_pago" class="CreacionBasicoInput">
            	<option value="0">Selecciona</option>
                <option value="1"<?php if($forma_pago == 1) { echo ' selected="selected"'; } ?>>Transferencia bancaria</option>
                <option value="2"<?php if($forma_pago == 2) { echo ' selected="selected"'; } ?>>Tarjeta de crédito/débito</option>
                <option value="3"<?php if($forma_pago == 3) { echo ' selected="selected"'; } ?>>PayPal</option>
                <option value="4"<?php if($forma_pago == 4) { echo ' selected="selected"'; } ?>>Recibo domiciliado</option>
            </select>
        </div> 
        <div class="ContenedorCamposCreacionBasico">   
         	<div class="LabelCreacionBasico">M&eacute;todo de pago</div>
           	<select id="metodo_pago" name="metodo_pago" class="CreacionBasicoInput">
            	<option value="0">Selecciona</option>
                <option value="1"<?php if($metodo_pago == 1) { echo ' selected="selected"'; } ?>>Pago previo</option>
                <option value="2"<?php if($metodo_pago == 2) { echo ' selected="selected"'; } ?>>30 días</option>
                <option value="3"<?php if($metodo_pago == 3) { echo ' selected="selected"'; } ?>>60 días</option>
                <option value="4"<?php if($metodo_pago == 4) { echo ' selected="selected"'; } ?>>90 días</option>
            </select>
        </div>    
        <div class="ContenedorCamposCreacionBasico">    
           	<div class="LabelCreacionBasico">Tiempo suministro</div>
          	<select id="tiempo_suministro" name="tiempo_suministro" class="CreacionBasicoInput">	
            	<option value="0">Selecciona</option>
                <option value="1"<?php if($tiempo_suministro == 1) { echo ' selected="selected'; } ?>>7 días</option>
                <option value="2"<?php if($tiempo_suministro == 2) { echo ' selected="selected'; } ?>>14 días</option>
                <option value="3"<?php if($tiempo_suministro == 3) { echo ' selected="selected'; } ?>>30 días</option>
                <option value="4"<?php if($tiempo_suministro == 4) { echo ' selected="selected'; } ?>>60 días</option>
                <option value="5"<?php if($tiempo_suministro == 5) { echo ' selected="selected'; } ?>>90 días</option>
            </select>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Provincia </div>
           	<input type="text" id="provincia" name="provincia" class="CreacionBasicoInput" value="<?php echo $provincia; ?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Código Postal *</div>
           	<input type="text" id="codigo_postal" name="codigo_postal" class="CreacionBasicoInput" value="<?php echo $codigo_postal; ?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Persona de Contacto</div>
           	<input type="text" id="persona_contacto" name="persona_contacto" class="CreacionBasicoInput" value="<?php echo $persona_contacto; ?>" />
        </div>   
        <div class="ContenedorCamposCreacionBasico">    
           	<div class="LabelCreacionBasico">Descripcion</div>
          	<textarea type="text" id="descripcion" name="descripcion" rows="5" class="textareaInput"><?php echo $descripcion; ?></textarea>	
        </div>   
        <div class="ContenedorBotonCreacionBasico">
          	<input type="button" id="volver" name="volver" value="Volver" onclick="javascript:window.history.back()" /> 
            <input type="hidden" id="guardandoCentroLogistico" name="guardandoCentroLogistico" value="1" />
            <input type="submit" id="guardar" name="guardar" value="Continuar" />
        </div>
        <div class="mensajeCamposObligatorios">* Campos obligatorios</div>
		<?php 
			if($mensaje_error != "") {
				echo '<div class="mensaje_error">'.$mensaje_error.'</div>'; 
			}
		?>
        <br />
    </form>
</div>
<!--<div class="separador"></div>-->
<?php include ('../includes/footer.php'); ?>