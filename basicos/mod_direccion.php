<?php
// Este fichero modifica la direccion de basicos
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/direccion.class.php");
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

$dir = new Direccion();
$id_direccion = $_GET["id"];
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
		$dir->datosNuevaDireccion($id_direccion,$nombre_empresa,$cif,$direccion,$codigo_postal,$localidad,$provincia,$telefono,$persona_contacto,$comentarios,$tipo);
		$resultado = $dir->guardarCambios();
		if($resultado == 1) {
			header("Location: direcciones.php?dir=modificado");
		} else {
			$mensaje_error = $dir->getErrorMessage($resultado);
		}
	}
}
// Se cargan los datos buscando por el ID
$dir->cargaDatosDireccionId($_GET["id"]);
$nombre_empresa = $dir->nombre_empresa;
$cif = $dir->cif;
$direccion = $dir->direccion;
$codigo_postal = $dir->codigo_postal;
$localidad = $dir->localidad;
$provincia = $dir->provincia;
$pais = $dir->pais;
$telefono = $dir->telefono;
$persona_contacto = $dir->persona_contacto;
$comentarios = $dir->comentarios;
$tipo = $dir->tipo;

// Se cargan los datos de la direccion
$titulo_pagina = "Básico > Modifica dirección";
$pagina = "mod_direccion";
include ('../includes/header.php');
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>

    <h3> Modificación de dirección </h3>
    
    <form id="FormularioCreacionBasico" name="modificarDireccion" action="mod_direccion.php?id=<?php echo $dir->id_direccion; ?>" method="post">
    	<br />
        <h5> Modifique los datos en el siguiente formulario</h5>
    	<div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Nombre Empresa *</div>
            <input type="text" id="nombre_empresa" name="nombre_empresa" class="CreacionBasicoInput" value="<?php echo $nombre_empresa; ?>" <?php echo $solo_lectura; ?> />
        </div>    
        <div class="ContenedorCamposCreacionBasico">
          	<div class="LabelCreacionBasico">CIF *</div>
           	<input type="text" id="cif" name="cif" class="CreacionBasicoInput" value="<?php echo $cif; ?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Dirección *</div>
           	<input type="text" id="direccion" name="direccion" class="CreacionBasicoInput" value="<?php echo utf8_encode($direccion); ?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
          	<div class="LabelCreacionBasico">CP *</div>
           	<input type="text" id="codigo_postal" name="codigo_postal" class="CreacionBasicoInput" value="<?php echo $codigo_postal; ?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Localidad *</div>
            <input type="text" id="localidad" name="localidad" class="CreacionBasicoInput" value="<?php echo $localidad; ?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Provincia *</div>
           	<input type="text" id="provincia" name="provincia" class="CreacionBasicoInput" value="<?php echo $provincia; ?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Telefono *</div>
           	<input type="text" id="telefono" name="telefono" class="CreacionBasicoInput" value="<?php echo $telefono; ?>" <?php echo $solo_lectura; ?> />
        </div> 
        <div class="ContenedorCamposCreacionBasico">    
           	<div class="LabelCreacionBasico">Persona de contacto</div>
          	<input type="text" id="persona_contacto" name="persona_contacto" class="CreacionBasicoInput" value="<?php echo $persona_contacto; ?>" <?php echo $solo_lectura; ?> />	
        </div>   
        <div class="ContenedorCamposCreacionBasico">    
           	<div class="LabelCreacionBasico">Comentarios</div>
          	<textarea type="text" id="comentarios" name="comentarios" rows="5" class="textareaInput" <?php echo $solo_lectura; ?>><?php echo $comentarios; ?></textarea>	
        </div>
        <div class="ContenedorCamposCreacionBasico">   
            <div class="LabelCreacionBasico">Tipo</div>
            <?php 
                $num_tipo = 2;
                $tipos = array ("0","1"); 

                if($modificar) { ?>
                    <select id="tipo" name="tipo"  class="CreacionBasicoInput">
                    	<?php 
                					for($i=0;$i<$num_tipo;$i++) {
                						echo '<option value="'.$tipos[$i].'"'; if ($tipo == $tipos[$i]) echo 'selected="selected"'; echo '>';
                						if ($tipos[$i] == "0") echo "ENTREGA";
                						else echo "FACTURACION";
                						echo '</option>';
                					}
                			?>
                    </select>
            <?php 
                }
                else { ?>
                    <input type="text" id="tipo" name="tipo" class="CreacionBasicoInput" value="<?php if ($tipo == "0") echo 'ENTREGA'; else echo 'FACTURACION'; ?>" <?php echo $solo_lectura; ?> /> 
            <?php     
                }
            ?>
        </div>    
        <div class="ContenedorBotonCreacionBasico">
          	<input type="button" id="volver" name="volver" value="Volver" onclick="javascript:window.history.back()" /> 
            <?php 
                if($modificar) { ?>
                    <input type="hidden" id="guardandoDireccion" name="guardandoDireccion" value="1" />
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