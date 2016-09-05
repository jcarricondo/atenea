<?php 
// Este fichero crea un nuevo software de basicos
include("../includes/sesion.php");
include("../classes/basicos/software.class.php");
permiso(2);

if(isset($_POST["guardandoSoftware"]) and $_POST["guardandoSoftware"] == 1) {
	// Se reciben los datos
	$nombre = $_POST["nombre"];
	$referencia = $_POST["referencia"];
	$descripcion = $_POST["descripcion"];
	$version = $_POST["version"];
		
	if (($nombre == '') or ($referencia == '') or ($descripcion == '') or ($version == '') ){
		echo '<script type="text/javascript">alert("Rellene los campos obligatorios")</script>';
	}
	else {
		$software = new Software();
		$software->datosNuevoSoftware(NULL,$nombre,$referencia,$descripcion,$version,3);
		$resultado = $software->guardarCambios();
		if($resultado == 1) {
			header("Location: software_simulacion.php?soft=creado");
		} 
		else {
			$mensaje_error = $software->getErrorMessage($resultado);
		}
	}
} 
else {
	$nombre = "";
	$referencia = "";
	$descripcion = "";
	$version = "";
}

$titulo_pagina = "Básico > Nueva software";
$pagina = "new_software";
include ('../includes/header.php');
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
 	</div>
    
    <h3> Creaci&oacute;n de un nuevo software de simulacion </h3>
    <form id="FormularioCreacionBasico" name="crearSoftware" action="nuevo_software_simulacion.php" method="post" >
    	<br />
        <h5> Rellene los siguientes campos para la creaci&oacute;n de un nuevo software de simulacion </h5>
    	<div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Nombre *</div>
            <input type="text" id="nombre" name="nombre" class="CreacionBasicoInput" value="<?php echo $nombre;?>"/>
        </div>    
        <div class="ContenedorCamposCreacionBasico">
          	<div class="LabelCreacionBasico">Referencia *</div>
               	<input type="text" id="referencia" name="referencia" class="CreacionBasicoInput" value="<?php echo $referencia;?>"/>
            </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Version *</div>
           	<input type="text" id="version" name="version" class="CreacionBasicoInput" value="<?php echo $version;?>"/>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Descripcion *</div>
          	<input type="text" id="descripcion" name="descripcion" class="CreacionBasicoInput" value="<?php echo $descripcion;?>"/>
        </div>
        
        <div class="ContenedorBotonCreacionBasico">
           	<input type="button" id="volver" name="volver" value="Volver" onClick="javascript:window.history.back()"/> 
            <input type="hidden" id="guardandoSoftware" name="guardandoSoftware" value="1"/>
            <input type="submit" id="continuar" name="continuar" value="Continuar" />
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