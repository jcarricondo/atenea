<?php
// Este fichero modifica un nombre de producto de basicos
include("../includes/sesion.php");
include("../classes/basicos/nombre_producto.class.php");
include("../classes/basicos/familia.class.php");
include("../classes/basicos/listado_familias.class.php");
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

$bbdd = new MySQL;
$producto = new Nombre_Producto();
$fam = new Familia();
$nf = new listadoFamilias();

if (isset($_POST["guardandoProducto"]) and $_POST["guardandoProducto"] == 1){
	// Se reciben los datos del formulario del nombre de producto
	$nombre = $_POST["nombre"];
	$codigo = $_POST["codigo"];
	$version = $_POST["version"];
	$familia = $_POST["familia"];
	$id_nombre_producto = $_GET["id"];
	
	if (($nombre == '') or ($codigo == '') or ($version == '') or ($familia == '')){
		echo '<script type="text/javascript">alert("Rellene los campos obligatorios")</script>';
		$error = true;
	}
	else {
		$producto->datosNuevoProducto($id_nombre_producto,$nombre,$codigo,$version,$familia);
		$resultado = $producto->guardarCambios();
		if($resultado == 1) {
			header("Location: nombres_de_productos.php?producto=modificado");
		} 
		else {
			$mensaje_error = $producto->getErrorMessage($resultado);
		}
	}
}

// Se cargan los datos buscando por el ID
$producto->cargaDatosNombreProductoId($_GET["id"]);
$nombre = $producto->nombre;
$codigo = $producto->codigo;
$version = $producto->version;
$familia = $producto->familia;

// Titulo de pagina
$titulo_pagina = "Básico > Modifica nombre de producto";
$pagina = "mod_nombre_producto";
include ('../includes/header.php');
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php") ?>
    </div>

    <h3> Modificación del nombre de producto </h3>
    
    <form id="FormularioCreacionBasico" name="modificarProducto" action="mod_nombre_producto.php?id=<?php echo $producto->id_nombre_producto;?>" method="post">
    	<br />
        <h5> Modifique los datos en el siguiente formulario</h5>
    	<div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Nombre *</div>
            <input type="text" id="nombre" name="nombre" class="CreacionBasicoInput" value="<?php echo $nombre; ?>" <?php echo $solo_lectura; ?> />
        </div>    
        <div class="ContenedorCamposCreacionBasico">
          	<div class="LabelCreacionBasico">Codigo *</div>
           	<input type="text" id="codigo" name="codigo" class="CreacionBasicoInput" value="<?php echo $codigo; ?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Versión *</div>
           	<input type="text" id="version" name="version" class="CreacionBasicoInput" value="<?php echo $version; ?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
          	<div class="LabelCreacionBasico">Familia *</div>
            <?php 
				$nf->prepararConsulta();
				$nf->realizarConsulta();
				$resultado_familias = $nf->familias;

				if($modificar) { ?>
					<select id="familia" name="familia"  class="CreacionBasicoInput">
		            	<?php 
							for($i=0;$i<count($resultado_familias);$i++) {
								$datoFamilia = $resultado_familias[$i];
								$fam->cargaDatosFamiliaId($datoFamilia["id_familia"]);
								echo '<option value="'.$fam->id_familia.'" '; if ($fam->nombre == $familia) echo 'selected="selected"'; echo '>'.$fam->nombre.'</option>';
		                    }
						?>
            		</select>
            <?php
            	}
            	else {
            		for($i=0;$i<count($resultado_familias);$i++) {
						$datoFamilia = $resultado_familias[$i];
						$fam->cargaDatosFamiliaId($datoFamilia["id_familia"]);
						if ($fam->nombre == $familia){ ?>
							<input type="text" id="familia" name="familia" class="CreacionBasicoInput" value="<?php echo $fam->nombre; ?>" <?php echo $solo_lectura; ?> />				
					<?php 
						}		
					}
				}		
            ?>

        </div>
        
        <div class="ContenedorBotonCreacionBasico">
          	<input type="button" id="volver" name="volver" value="Volver" onclick="javascript:window.history.back()" /> 
            <?php 
            	if($modificar){ ?>
		            <input type="hidden" id="guardandoProducto" name="guardandoProducto" value="1" />
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
<?php include ('../includes/footer.php');?>