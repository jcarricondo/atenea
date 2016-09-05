<?php 
// Este fichero modifica un software de basicos
include("../includes/sesion.php");
include("../classes/basicos/software.class.php");
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

$software = new Software();
if(isset($_POST["guardandoSoftware"]) and $_POST["guardandoSoftware"] == 1) {
	// Se reciben los datos
	$nombre = $_POST["nombre"];
	$referencia = $_POST["referencia"];
	$descripcion = $_POST["descripcion"];
	$version = $_POST["version"];
	$id_componente = $_GET["id"];
	
	if (($nombre == '') or ($referencia == '') or ($descripcion == '') or ($version == '')){
		echo '<script type="text/javascript">alert("Rellene los campos obligatorios")</script>';
	}
	else {
		$software->datosNuevoSoftware($id_componente,$nombre,$referencia,$descripcion,$version,3);
		$resultado = $software->guardarCambios();
		if($resultado == 1) {
			header("Location: software_simulacion.php?soft=modificado");
		}
		else {
			$mensaje_error = $software->getErrorMessage($resultado);
		}
	}
}
// Se cargan los datos buscando por el ID
$software->cargaDatosSoftwareId($_GET["id"]);
$nombre = $software->software;
$referencia = $software->referencia;
$descripcion = $software->descripcion;
$version = $software->version;

// Titulo de pagina
$titulo_pagina = "Básico > Modifica software";
$pagina = "mod_software";
include ('../includes/header.php');
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
 	</div>
    
    <h3> Modificación de software de simulación </h3>
    <form id="FormularioCreacionBasico" name="modificarSoftware" action="mod_software_simulacion.php?id=<?php echo $software->id_componente; ?>" method="post" >
    	<br />
        <h5> Modifique los datos en el siguiente formulario </h5>
    	<div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Nombre *</div>
            <input type="text" id="nombre" name="nombre" class="CreacionBasicoInput" value="<?php echo $nombre;?>" <?php echo $solo_lectura; ?> />
        </div>    
        <div class="ContenedorCamposCreacionBasico">
          	<div class="LabelCreacionBasico">Referencia *</div>
               	<input type="text" id="referencia" name="referencia" class="CreacionBasicoInput" value="<?php echo $referencia;?>" <?php echo $solo_lectura; ?> />
            </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Versión *</div>
           	<input type="text" id="version" name="version" class="CreacionBasicoInput" value="<?php echo $version;?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Descripción *</div>
          	<input type="text" id="descripcion" name="descripcion" class="CreacionBasicoInput" value="<?php echo $descripcion;?>" <?php echo $solo_lectura; ?> />
        </div>
        
        <div class="ContenedorBotonCreacionBasico">
           	<input type="button" id="volver" name="volver" value="Volver" onClick="javascript:history.back()"/> 
            <?php 
                if($modificar){ ?>
                    <input type="hidden" id="guardandoSoftware" name="guardandoSoftware" value="1"/>
                    <input type="submit" id="continuar" name="continuar" value="Continuar" />
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
<?php include ("../includes/footer.php");?>