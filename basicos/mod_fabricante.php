<?php
// Este fichero modifica el fabricante
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/fabricante.class.php");
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

$fabricante = new Fabricante();
if(isset($_POST["guardandoFabricante"]) and $_POST["guardandoFabricante"] == 1) {
	// Se reciben los datos
	$nombre = $_POST["nombre"];
	$direccion = $_POST["direccion"];
	$telefono = $_POST["telefono"];
	$email = $_POST["email"];
	$ciudad = $_POST["ciudad"];
	$pais = $_POST["pais"];
	$descripcion = $_POST["descripcion"];
	$id_fabricante = $_GET["id"];
	
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
		$fabricante->datosNuevoFabricante($id_fabricante,$nombre,$descripcion,$direccion,$ciudad,$pais,$telefono,$email);
		$resultado = $fabricante->guardarCambios();
		if($resultado == 1) {
			header("Location: fabricantes.php?fab=modificado");
		} 
		else {
			$mensaje_error = $fabricante->getErrorMessage($resultado);
		}
	}
}
// Se cargan los datos buscando por el ID
$fabricante->cargaDatosFabricanteId($_GET["id"]);
$nombre = $fabricante->nombre;
$direccion = $fabricante->direccion;
$telefono = $fabricante->telefono;
$email = $fabricante->email;
$ciudad = $fabricante->ciudad;
$pais = $fabricante->pais;
$descripcion = $fabricante->descripcion;

// Se cargan los datos del fabricante
$titulo_pagina = "Básico > Modifica fabricante";
$pagina = "mod_fabricante";
include ('../includes/header.php');
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>

    <h3> Modificación de fabricante </h3>
    
    <form id="FormularioCreacionBasico" name="modificarFabricante" action="mod_fabricante.php?id=<?php echo $fabricante->id_fabricante;?>" method="post">
    	<br />
        <h5> Modifique los datos en el siguiente formulario</h5>
    	<div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Nombre *</div>
            <input type="text" id="nombre" name="nombre" class="CreacionBasicoInput" value="<?php echo $nombre; ?>" <?php echo $solo_lectura; ?> />
        </div>    
        <div class="ContenedorCamposCreacionBasico">
          	<div class="LabelCreacionBasico">Direcci&oacute;n *</div>
           	<input type="text" id="direccion" name="direccion" class="CreacionBasicoInput" value="<?php echo $direccion; ?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Tel&eacute;fono *</div>
           	<input type="text" id="telefono" name="telefono" class="CreacionBasicoInput" value="<?php echo $telefono; ?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
          	<div class="LabelCreacionBasico">Email *</div>
           	<input type="text" id="email" name="email" class="CreacionBasicoInput" value="<?php echo $email; ?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Ciudad *</div>
            <input type="text" id="ciudad" name="ciudad" class="CreacionBasicoInput" value="<?php echo $ciudad; ?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Pais *</div>
           	<input type="text" id="pais" name="pais" class="CreacionBasicoInput" value="<?php echo $pais; ?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">    
           	<div class="LabelCreacionBasico">Descripción</div>
          	<textarea type="text" id="descripcion" name="descripcion" rows="5" class="textareaInput" <?php echo $solo_lectura; ?>><?php echo $descripcion; ?></textarea>	
        </div>   
        <div class="ContenedorBotonCreacionBasico">
          	<input type="button" id="volver" name="volver" value="Volver" onclick="javascript:window.history.back()" /> 
            <?php 
                if($modificar){ ?>
                   <input type="hidden" id="guardandoFabricante" name="guardandoFabricante" value="1" />
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