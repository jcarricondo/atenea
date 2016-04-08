<?php 
// Este fichero crea una nueva familia
include("../includes/sesion.php");
include("../classes/basicos/familia.class.php");
permiso(2);

if (isset($_POST["guardandoFamilia"]) and $_POST["guardandoFamilia"] == 1){
	// Se reciben los datos del formulario de la familia
	$nombre = $_POST["nombre"];
	
	if ($nombre == ''){
		echo '<script type="text/javascript">alert("Rellene los campos obligatorios")</script>';
	}
	else {
		$familia = new Familia();
		$familia->datosNuevaFamilia(NULL,$nombre);
		$resultado = $familia->guardarCambios();
		if($resultado == 1) {
			header("Location: familias.php?familia=creado");
		}
		else {
			$mensaje_error = $familia->getErrorMessage($resultado);
		}
	}
} 
else {
	$nombre = "";
}	

$titulo_pagina = "Básicos > Nueva Familia";
$pagina = "new_familia";
include ("../includes/header.php");	
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
 	</div>
    
    <h3> Creación de una nueva familia </h3>
    
    <form id="FormularioCreacionBasico" name="crearFamilia" action="nueva_familia.php" method="post" >
    	<br />
        <h5> Rellene los siguientes campos para la creación de una nueva familia </h5>
    	<div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Nombre *</div>
            <input type="text" id="nombre" name="nombre" class="CreacionBasicoInput" value="<?php echo $nombre;?>" />
        </div>
	    <div class="ContenedorBotonCreacionBasico">
          	<input type="button" id="volver" name="volver" value="Volver" onclick="javascript:window.history.back()" /> 
            <input type="hidden" id="guardandoFamilia" name="guardandoFamilia" value="1" />
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
<?php include ("../includes/footer.php"); ?>