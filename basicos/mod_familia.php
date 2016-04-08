<?php
// Este fichero modifica la familia de basicos
include("../includes/sesion.php");
include("../classes/basicos/familia.class.php");
permiso(34);
// Comprobamos si el usuario puede modificar el basico
if(!permisoMenu(3)){ 
    $modificar = false;
    $solo_lectura = 'readonly="readonly"';
}
else {
    $modificar = true;
    $solo_lectura = '';
}

$familia = new Familia();
if (isset($_POST["guardandoFamilia"]) and $_POST["guardandoFamilia"] == 1){
	// Se reciben los datos del formulario de la familia
	$nombre = $_POST["nombre_familia"];
	$id_familia = $_GET["id"];
	
	if ($nombre == '') {
		echo '<script type="text/javascript">alert("Rellene los campos obligatorios")</script>';
	}
	else {
		$familia->datosNuevaFamilia($id_familia,$nombre);
		$resultado = $familia->guardarCambios();
		if($resultado == 1) {
			header("Location: familias.php?familia=modificado");
		} 
		else {
			$mensaje_error = $familia->getErrorMessage($resultado);
		}
	}
}
// Se cargan los datos buscando por el ID
$familia->cargaDatosFamiliaId($_GET["id"]);
$nombre = $familia->nombre;

// Titulo de pagina
$titulo_pagina = "Básico > Modifica familia";
$pagina = "mod_familia";
include ('../includes/header.php');
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php") ?>
    </div>

    <h3> Modificación de familia </h3>
    
    <form id="FormularioCreacionBasico" name="modificarFamilia" action="mod_familia.php?id=<?php echo $familia->id_familia;?>" method="post">
    	<br />
        <h5> Modifique los datos en el siguiente formulario</h5>
    	<div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Nombre *</div>
            <input type="text" id="nombre_familia" name="nombre_familia" class="CreacionBasicoInput" value="<?php echo $nombre; ?>" <?php echo $solo_lectura; ?> />
        </div>    
	
        <div class="ContenedorBotonCreacionBasico">
          	<input type="button" id="volver" name="volver" value="Volver" onclick="javascript:window.history.back()" /> 
          	<?php 
          		if($modificar) { ?>
		            <input type="hidden" id="guardandoFamilia" name="guardandoFamilia" value="1" />
		            <input type="submit" id="guardar" name="guardar" value="Continuar" />
		    <?php 
		    	} 
		    ?>
        </div>
        <?php 
			if($mensaje_error != "") {
				echo '<div class="mensaje_error">'.$mensaje_error.'</div>'; 
			}
		?>
        <br />
    </form>
</div>
<?php include ('../includes/footer.php');  ?>