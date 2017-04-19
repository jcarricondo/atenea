<?php
// Este fichero crea una nueva plantilla de producto de basicos
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/basicos/plantilla_producto.class.php");
include("../classes/basicos/nombre_producto.class.php");
include("../classes/basicos/kit.class.php");
include("../classes/basicos/periferico.class.php");
include("../classes/basicos/listado_kits.class.php");
include("../classes/basicos/listado_perifericos.class.php");
include("../classes/basicos/listado_nombre_producto.class.php");
permiso(2);

$plant = new Plantilla_Producto();
$np = new Nombre_Producto();
$kit = new Kit();
$per = new Periferico();
$listado_kit = new listadoKits();
$listado_per = new listadoPerifericos();
$listado_np = new ListadoNombreProducto();

if(isset($_POST["guardandoPlantilla"]) and $_POST["guardandoPlantilla"] == 1){
	// Se reciben los datos del formulario de la plantilla
	$nombre = $_POST["nombre"];
	$version = $_POST["version"];
    $id_nombre_producto = $_POST["select_nombre_producto"];
    $ids_perifericos = $_POST["perifericos"];
    $ids_kits = $_POST["kits"];

    $no_hay_perifericos = empty($ids_perifericos);
    $no_hay_kits = empty($ids_kits);
    $plantilla_vacia = $no_hay_perifericos && $no_hay_kits;

    // Comprobamos que la plantilla no este vacia
    if(!$plantilla_vacia) {
        // Guardamos la plantilla del producto
        $plant->datosNuevaPlantilla(NULL,$nombre,$version,$id_nombre_producto);
        $resultado = $plant->guardarCambios();
        if($resultado == 1) {
            // Guardamos sus componentes
            $id_plantilla = $plant->id_plantilla;
            $error_componentes = false;

            // Preparamos los ids de los componentes de la plantilla de producto
            for($i=0;$i<count($ids_perifericos);$i++) {
                $ids_componentes[] = array("id_componente" => $ids_perifericos[$i], "id_tipo" => 2);
            }
            for($i=0;$i<count($ids_kits);$i++) {
                $ids_componentes[] = array("id_componente" => $ids_kits[$i], "id_tipo" => 6);
            }

            // Guardamos los componentes de la plantilla de producto
            $i=0;
            while($i<count($ids_componentes) && !$error_componentes) {
                $id_componente = $ids_componentes[$i]["id_componente"];
                $id_tipo_componente = $ids_componentes[$i]["id_tipo"];
                $resultado = $plant->guardarComponentePlantillaProducto($id_plantilla,$id_componente,$id_tipo_componente);
                $error_componentes = $resultado != 1;
                $i++;
            }

            if(!$error_componentes) {
                header("Location: plantillas_de_productos.php?plantilla=creado");
            }
            else {
                // ERROR AL GUARDAR EL COMPONENTE DE LA PLANTILLA DE PRODUCTO
                $mensaje_error = $plant->getErrorMessage($resultado);
            }
        }
        else {
            // ERROR AL GUARDAR LA PLANTILLA DE PRODUCTO
            $mensaje_error = $plant->getErrorMessage($resultado);
        }
    }
    else {
        $mensaje_error = 'La plantilla de producto debe contener al menos un componente</br>';
    }
}
else {
	$nombre = "";
	$version = "";
	$id_nombre_producto = "";
}
$titulo_pagina = "Básicos > Nueva plantilla de producto";
$pagina = "new_plantilla_producto";
include("../includes/header.php");
echo '<script type="text/javascript" src="../js/basicos/plantilla_de_productos_27032017_1313.js"></script>';
echo '<script type="text/javascript" src="../js/basicos/nueva_plantilla_producto_27032017_1313.js"></script>';
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
 	</div>
    
    <h3> Creaci&oacute;n de una plantilla de producto </h3>
    
    <form id="FormularioCreacionBasico" name="crearPlantillaProducto" onsubmit="return validarFormulario()" action="nueva_plantilla_producto.php" method="post" >
    	<br />
        <h5> Rellene los siguientes campos para la creaci&oacute;n de una plantilla de producto </h5>
    	<div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Plantilla de Producto *</div>
            <input type="text" id="nombre" name="nombre" class="CreacionBasicoInput" value="<?php echo $nombre;?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Versión *</div>
            <input type="text" id="version" name="version" class="CreacionBasicoInput" value="<?php echo $version;?>" onblur="extractNumber(this,2,true);" onkeyup="extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" />
        </div> 
        <div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Nombre de Producto *</div>
            <select id="select_nombre_producto" name="select_nombre_producto"  class="CreacionBasicoInput">
                <?php
                    $listado_np->prepararConsulta();
                    $listado_np->realizarConsulta();
                    $resultado_nombres = $listado_np->nombre_productos;

                    for($i=0;$i<count($resultado_nombres);$i++) {
                        $datoNombre = $resultado_nombres[$i];
                        $np->cargaDatosNombreProductoId($datoNombre["id_nombre_producto"]);
                        echo '<option value="'.$np->id_nombre_producto.'">'.$np->nombre.'_v'.$np->version.'</option>';
                    }
                ?>
            </select>
        </div>
        <br/>

        <h5>Selecci&oacute;n de componentes para la plantilla del producto</h5><br/>
        <?php include("nueva_plantilla_producto_add_perifericos.php") ?>
        <?php include("nueva_plantilla_producto_add_kits.php") ?>

        <div class="ContenedorBotonCreacionBasico">
          	<input type="button" id="volver" name="volver" value="Volver" onclick="window.history.back()" />
            <input type="hidden" id="guardandoPlantilla" name="guardandoPlantilla" value="1" />
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