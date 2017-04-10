<?php
// Este fichero modifica una plantilla de producto existente
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/basicos/plantilla_producto.class.php");
include("../classes/basicos/nombre_producto.class.php");
include("../classes/basicos/kit.class.php");
include("../classes/basicos/periferico.class.php");
include("../classes/basicos/listado_kits.class.php");
include("../classes/basicos/listado_perifericos.class.php");
include("../classes/basicos/listado_nombre_producto.class.php");
permiso(34);

$plant = new Plantilla_Producto();
$np = new Nombre_Producto();
$kit = new Kit();
$per = new Periferico();
$listado_kit = new listadoKits();
$listado_per = new listadoPerifericos();
$listado_np = new ListadoNombreProducto();

// Comprobamos si el usuario puede modificar el basico
if(!permisoMenu(3)){
    $modificar = false;
    $solo_lectura = 'readonly="readonly" disabled="disabled"';
}
else {
    $modificar = true;
    $solo_lectura = '';
}

if(isset($_POST["guardandoPlantilla"]) and $_POST["guardandoPlantilla"] == 1){
    // Se reciben los datos del formulario de la plantilla
    $id_plantilla = $_GET["id"];
	$nombre = $_POST["nombre"];
	$version = $_POST["version"];
    $id_nombre_producto = $_POST["sel_inp_nombre_producto"];
    $ids_perifericos = $_POST["perifericos"];
    $ids_kits = $_POST["kits"];

    $no_hay_perifericos = empty($ids_perifericos);
    $no_hay_kits = empty($ids_kits);
    $plantilla_vacia = $no_hay_perifericos && $no_hay_kits;

    if(!$plantilla_vacia){
        // Guardamos la plantilla del producto
        $plant->datosNuevaPlantilla($id_plantilla,$nombre,$version,$id_nombre_producto);
        $resultado = $plant->guardarCambios();
        if($resultado == 1) {
            // Desactivamos los componentes antiguos de esa plantilla
            $res_desactivar = $plant->desactivarComponentesPlantilla($id_plantilla);
            if($res_desactivar == 1) {
                $error_componentes = false;
                // Preparamos los ids de los componentes de la plantilla de producto
                for($i=0;$i<count($ids_perifericos);$i++) {
                    $ids_componentes[] = $ids_perifericos[$i];
                }
                for($i=0;$i<count($ids_kits);$i++) {
                    $ids_componentes[] = $ids_kits[$i];
                }

                // Guardamos los nuevos componentes de la plantilla de producto
                $i=0;
                while($i<count($ids_componentes) && !$error_componentes) {
                    $id_componente = $ids_componentes[$i];
                    $id_tipo_componente = $plant->dameTipoComponente($id_componente);
                    $id_tipo_componente = $id_tipo_componente["id_tipo"];
                    $resultado = $plant->guardarComponentePlantillaProducto($id_plantilla,$id_componente,$id_tipo_componente);
                    $error_componentes = $resultado != 1;
                    $i++;
                }

                if(!$error_componentes) {
                    header("Location: plantillas_de_productos.php?plantilla=modificado");
                }
                else {
                    // ERROR AL GUARDAR EL COMPONENTE DE LA PLANTILLA DE PRODUCTO
                    $mensaje_error = $plant->getErrorMessage($resultado);
                }
            }
            else {
                $mensaje_error = $plant->getErrorMessage($res_desactivar);
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
// Cargamos los datos de la plantilla
$id_plantilla = $_GET["id"];
$plant->cargaDatosPlantillaProductoId($id_plantilla);
$nombre = $plant->nombre;
$version = $plant->version;
$id_nombre_producto = $plant->id_nombre_producto;
$ids_perifericos = $plant->damePerifericosPlantillaProducto($id_plantilla);
$ids_kits = $plant->dameKitsPlantillaProducto($id_plantilla);

$titulo_pagina = "Básicos > Modifica plantilla de producto";
$pagina = "mod_plantilla_producto";
include("../includes/header.php");
echo '<script type="text/javascript" src="../js/basicos/plantilla_de_productos_27032017_1313.js"></script>';
echo '<script type="text/javascript" src="../js/basicos/mod_plantilla_producto_27032017_1313.js"></script>';
?>

<div class="separador" xmlns="http://www.w3.org/1999/html"></div>
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
 	</div>
    
    <h3> Modificaci&oacute;n de una plantilla de producto </h3>
    
    <form id="FormularioCreacionBasico" name="modificarPlantillaProducto" onsubmit="return validarFormulario()" action="mod_plantilla_producto.php?id=<?php echo $id_plantilla;?>" method="post" >
    	<br />
        <h5> Modifique los datos en el siguiente formulario </h5>
    	<div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Plantilla de Producto *</div>
            <input type="text" id="nombre" name="nombre" class="CreacionBasicoInput" value="<?php echo $nombre;?>" <?php echo $solo_lectura;?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Versión *</div>
            <input type="text" id="version" name="version" class="CreacionBasicoInput" value="<?php echo $version;?>" onblur="extractNumber(this,2,true);" onkeyup="extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" <?php echo $solo_lectura;?> />
        </div> 
        <div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Nombre de Producto *</div>
            <?php
                $listado_np->prepararConsulta();
                $listado_np->realizarConsulta();
                $resultado_nombres = $listado_np->nombre_productos;

                if($modificar) { ?>
                    <select id="sel_inp_nombre_producto" name="sel_inp_nombre_producto" class="CreacionBasicoInput">
                    <?php
                        for($i = 0; $i < count($resultado_nombres); $i++) {
                            $datoNombre = $resultado_nombres[$i];
                            $np->cargaDatosNombreProductoId($datoNombre["id_nombre_producto"]);
                            echo '<option value="' . $np->id_nombre_producto . '"';
                            if ($np->id_nombre_producto == $id_nombre_producto) echo ' selected="selected"';
                            echo '>' . $np->nombre . '_v' . $np->version . '</option>';
                        }
                    ?>
                    </select>
            <?php
                }
                else {
                    for($i=0;$i<count($resultado_nombres);$i++) {
                        $datoNombre = $resultado_nombres[$i];
                        $np->cargaDatosNombreProductoId($datoNombre["id_nombre_producto"]);
                        if($np->id_nombre_producto == $id_nombre_producto){ ?>
							<input type="text" id="sel_inp_nombre_producto" name="sel_inp_nombre_producto" class="CreacionBasicoInput" value="<?php echo $np->nombre;?>" <?php echo $solo_lectura; ?>/>
                    <?php
						}
                    }
                }
            ?>
        </div>
        <br/>

        <h5>Selecci&oacute;n de componentes para la plantilla del producto</h5><br/>
        <?php include("mod_plantilla_producto_add_perifericos.php") ?>
        <?php include("mod_plantilla_producto_add_kits.php") ?>

        <div class="ContenedorBotonCreacionBasico">
            <input type="button" id="volver" name="volver" value="Volver" onclick="javascript:window.history.back()" />
            <?php
                if($modificar){ ?>
                    <input type="hidden" id="guardandoPlantilla" name="guardandoPlantilla" value="1" />
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
<?php include ("../includes/footer.php");?>