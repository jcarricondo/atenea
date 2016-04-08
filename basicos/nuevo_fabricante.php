<?php
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/fabricante.class.php");
permiso(2);

if(isset($_POST["guardandoFabricante"]) and $_POST["guardandoFabricante"] == 1) {
	// Se reciben los datos
	$nombre = $_POST["nombre"];
	$direccion = $_POST["direccion"];
	$telefono = $_POST["telefono"];
	$email = $_POST["email"];
	$ciudad = $_POST["ciudad"];
	$pais = $_POST["pais"];
	$descripcion = $_POST["descripcion"];
	
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
	else {
		$fabricante = new Fabricante();
		$fabricante->datosNuevoFabricante(NULL,$nombre,$descripcion,$direccion,$ciudad,$pais,$telefono,$email);
		$resultado = $fabricante->guardarCambios();
		if($resultado == 1) {
			header("Location: fabricantes.php?fab=creado");
		}
		else {
			$mensaje_error = $fabricante->getErrorMessage($resultado);
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
	$descripcion = "";
}

$titulo_pagina = "Básico > Nuevo fabricante";
$pagina = "new_fabricante";
include ('../includes/header.php');
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>

    <h3> Creaci&oacute;n de un nuevo fabricante </h3>
    
    <form id="FormularioCreacionBasico" name="crearFabricante" action="nuevo_fabricante.php" method="post">
    	<br />
        <h5> Rellene los siguientes campos para la creaci&oacute;n de un nuevo fabricante </h5>
    	<div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Nombre *</div>
            <input type="text" id="nombre" name="nombre" class="CreacionBasicoInput" value="<?php echo $nombre; ?>" />
        </div>    
        <div class="ContenedorCamposCreacionBasico">
          	<div class="LabelCreacionBasico">Direcci&oacute;n *</div>
           	<input type="text" id="direccion" name="direccion" class="CreacionBasicoInput" value="<?php echo $direccion; ?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Tel&eacute;fono *</div>
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
           	<div class="LabelCreacionBasico">Descripcion</div>
          	<textarea type="text" id="descripcion" name="descripcion" rows="5" class="textareaInput"><?php echo $descripcion; ?></textarea>	
        </div>   
        <div class="ContenedorBotonCreacionBasico">
          	<input type="button" id="volver" name="volver" value="Volver" onclick="javascript:window.history.back()" /> 
            <input type="hidden" id="guardandoFabricante" name="guardandoFabricante" value="1" />
            <input type="submit" id="guardar" name="guardar" value="Continuar" />
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
<?php include ('../includes/footer.php'); ?>