<?php
// Este fichero crea una nueva direccion
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/direccion.class.php");
permiso(2);

if(isset($_POST["guardandoDireccion"]) and $_POST["guardandoDireccion"] == 1) {
	// Se reciben los datos
	$nombre_empresa = $_POST["nombre_empresa"];
	$cif = $_POST["cif"];
	$direccion = $_POST["direccion"];
	$codigo_postal = $_POST["codigo_postal"];
	$localidad = $_POST["localidad"];
	$provincia = $_POST["provincia"];
	$telefono = $_POST["telefono"];
	$persona_contacto = $_POST["persona_contacto"];
	$comentarios = $_POST["comentarios"];
	$tipo = $_POST["tipo"];
	
	$validacion = new Funciones();	
	
	if (($nombre_empresa == '') or ($cif == '') or ($direccion == '') or ($codigo_postal == '') or ($localidad == '') or ($provincia == '') or ($telefono == '')){
		echo '<script type="text/javascript">alert("Rellene los campos obligatorios")</script>';
	}
	elseif (!$validacion->verificarSoloDigitos($codigo_postal)) {
		echo '<script type="text/javascript">alert("El codigo postal introducido no es correcto")</script>';
	}
	elseif (!$validacion->verificarSoloDigitos($telefono)) {
		echo '<script type="text/javascript">alert("El telefono introducido no es correcto")</script>';
	}
	else {
		$dir = new Direccion();
		$dir->datosNuevaDireccion(NULL,$nombre_empresa,$cif,$direccion,$codigo_postal,$localidad,$provincia,$telefono,$persona_contacto,$comentarios,$tipo);
		$resultado = $dir->guardarCambios();
		if($resultado == 1) {
			header("Location: direcciones.php?dir=creado");
		} 
		else {
			$mensaje_error = $dir->getErrorMessage($resultado);
		}
	}
} 
else {
	$nombre_empresa = "";
	$cif = "";
	$direccion = "";
	$codigo_postal = "";
	$localidad = "";
	$provincia = "";
	$telefono = "";
	$persona_contacto = "";
	$comentarios = "";
	$tipo = "";
}

$titulo_pagina = "Básico > Nuevo dirección";
$pagina = "new_direccion";
include ('../includes/header.php');
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>

    <h3> Creaci&oacute;n de una nueva dirección </h3>
    
    <form id="FormularioCreacionBasico" name="crearDireccion" action="nueva_direccion.php" method="post">
    	<br />
        <h5> Rellene los siguientes campos para la creaci&oacute;n de una nueva dirección </h5>
    	<div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Nombre Empresa *</div>
            <input type="text" id="nombre_empresa" name="nombre_empresa" class="CreacionBasicoInput" value="<?php echo $nombre_empresa; ?>" />
        </div>    
        <div class="ContenedorCamposCreacionBasico">
          	<div class="LabelCreacionBasico">CIF *</div>
           	<input type="text" id="cif" name="cif" class="CreacionBasicoInput" value="<?php echo $cif; ?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
          	<div class="LabelCreacionBasico">Dirección *</div>
           	<input type="text" id="direccion" name="direccion" class="CreacionBasicoInput" value="<?php echo $direccion; ?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">CP *</div>
           	<input type="text" id="codigo_postal" name="codigo_postal" class="CreacionBasicoInput" value="<?php echo $codigo_postal; ?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
          	<div class="LabelCreacionBasico">Localidad *</div>
           	<input type="text" id="localidad" name="localidad" class="CreacionBasicoInput" value="<?php echo $localidad; ?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Provincia *</div>
            <input type="text" id="provincia" name="provincia" class="CreacionBasicoInput" value="<?php echo $provincia; ?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Telefono *</div>
           	<input type="text" id="telefono" name="telefono" class="CreacionBasicoInput" value="<?php echo $telefono; ?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">    
           	<div class="LabelCreacionBasico">Persona de contacto</div>
          	<input type="text" id="persona_contacto" name="persona_contacto" class="CreacionBasicoInput" value="<?php echo $persona_contacto; ?>" />	
        </div>   
        <div class="ContenedorCamposCreacionBasico">    
           	<div class="LabelCreacionBasico">Comentarios</div>
          	<textarea type="text" id="comentarios" name="comentarios" rows="5" class="textareaInput"><?php echo $comentarios; ?></textarea>	
        </div> 
        <div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Tipo</div>
            <select id="tipo" name="tipo"  class="CreacionBasicoInput">
            	<?php 
					$num_tipo = 2;
					$tipos = array ("0","1");
					for($i=0;$i<$num_tipo;$i++) {
						echo '<option value="'.$tipos[$i].'">';
							if ($tipos[$i] == "0") echo "ENTREGA";
							else echo "FACTURACION";
						echo '</option>';
					}
				?>
            </select>
        </div> 

        <div class="ContenedorBotonCreacionBasico">
          	<input type="button" id="volver" name="volver" value="Volver" onclick="javascript:window.history.back()" /> 
            <input type="hidden" id="guardandoDireccion" name="guardandoDireccion" value="1" />
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