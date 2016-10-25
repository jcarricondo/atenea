<?php 
// Este fichero crea un nuevo nombre de producto de basicos
include("../includes/sesion.php");
include("../classes/basicos/nombre_producto.class.php");
include("../classes/basicos/familia.class.php");
include("../classes/basicos/listado_familias.class.php");
permiso(2);

if(isset($_POST["guardandoProducto"]) and $_POST["guardandoProducto"] == 1){
	// Se reciben los datos del formulario del nombre de producto
	$nombre = $_POST["nombre"];
	$codigo = $_POST["codigo"];
	$version = $_POST["version"];
	$familia = $_POST["familia"];
	
	if(($nombre == '') or ($codigo == '') or ($version == '') or ($familia == '')){
		echo '<script type="text/javascript">alert("Rellene los campos obligatorios")</script>';
		$error = true;
	}
	else {
		$producto = new Nombre_Producto();
		$producto->datosNuevoProducto(NULL,$nombre,$codigo,$version,$familia);
		$resultado = $producto->guardarCambios();
		if($resultado == 1) {
			header("Location: nombres_de_productos.php?producto=creado");
		} 
		else {
			$mensaje_error = $producto->getErrorMessage($resultado);
		}
	}	
} 
else {
	$nombre = "";
	$codigo = "";
	$version = "";
	$familia = "";
}

$titulo_pagina = "Básicos > Nuevo nombre de producto";
$pagina = "new_nombre_producto";
include ("../includes/header.php");	
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
 	</div>
    
    <h3> Creaci&oacute;n de un nuevo nombre de producto </h3>
    
    <form id="FormularioCreacionBasico" name="crearNombreProducto" action="nuevo_nombre_producto.php" method="post" >
    	<br />
        <h5> Rellene los siguientes campos para la creaci&oacute;n de un nuevo nombre de producto </h5>
    	<div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Nombre de Producto *</div>
            <input type="text" id="nombre" name="nombre" class="CreacionBasicoInput" value="<?php echo $nombre;?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Código *</div>
            <input type="text" id="codigo" name="codigo" class="CreacionBasicoInput" value="<?php echo $codigo;?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">    
            <div class="LabelCreacionBasico">Versión *</div>
            <input type="text" id="version" name="version" class="CreacionBasicoInput" value="<?php echo $version;?>" />
        </div> 
        <div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Familia *</div>
            <select id="familia" name="familia"  class="CreacionBasicoInput">
			<?php 
				if ((!$_POST["guardandoProducto"] == 1) or ($_POST["guardandoProducto"] == 1) or $error)
					$nf = new listadoFamilias();
					$nf->prepararConsulta();
					$nf->realizarConsulta();
					$resultado_familias = $nf->familias;
					
					for($i=0;$i<count($resultado_familias);$i++) {
						$fam = new Familia();
						$datoFamilia = $resultado_familias[$i];
						$fam->cargaDatosFamiliaId($datoFamilia["id_familia"]);
						echo '<option value="'.$fam->id_familia.'">'.$fam->nombre.'</option>';
					}
				?>
            </select>
        </div>
           
        <div class="ContenedorBotonCreacionBasico">
          	<input type="button" id="volver" name="volver" value="Volver" onclick="javascript:window.history.back()" /> 
            <input type="hidden" id="guardandoProducto" name="guardandoProducto" value="1" />
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
<?php include ("../includes/footer.php");?>