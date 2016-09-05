<?php
set_time_limit(10000);
// Este fichero crea una nueva plantilla de producto de basicos
include("../includes/sesion.php");
include("../classes/basicos/plantilla_producto.class.php");
include("../classes/basicos/nombre_producto.class.php");
include("../classes/basicos/cabina.class.php");
include("../classes/basicos/periferico.class.php");
/* include("../classes/basicos/software.class.php"); */
include("../classes/basicos/listado_cabinas.class.php");
include("../classes/basicos/listado_perifericos.class.php");
/* include("../classes/basicos/listado_softwares.class.php"); */
include("../classes/basicos/listado_nombre_producto.class.php");
permiso(2);

$plant = new Plantilla_Producto();
$np = new Nombre_Producto();
$cab = new Cabina();
$per = new Periferico();
/* $soft = new Software(); */
$listado_cab = new listadoCabinas();
$listado_per = new listadoPerifericos();
/* $listado_soft = new listadoSoftwares(); */
$listado_np = new ListadoNombreProducto();

if(isset($_POST["guardandoPlantilla"]) and $_POST["guardandoPlantilla"] == 1){
	// Se reciben los datos del formulario de la plantilla
	$nombre = $_POST["nombre"];
	$version = $_POST["version"];
    $id_nombre_producto = $_POST["select_nombre_producto"];
    $id_cabina = $_POST["cabina"];
    $ids_perifericos = $_POST["perifericos"];
    /* $ids_software = $_POST["software"]; */

    $no_hay_cabina = empty($id_cabina) || $id_cabina == -1;
    $no_hay_perifericos = empty($ids_perifericos);
    /* $no_hay_software = empty($ids_software); */

    $plantilla_vacia = ($no_hay_cabina && $no_hay_perifericos /* && $no_hay_software */);

    // Comprobamos que la plantilla no este vacia
    if(!$plantilla_vacia) {
        // Guardamos la plantilla del producto
        $plant->datosNuevaPlantilla($NULL,$nombre,$version,$id_nombre_producto);
        $resultado = $plant->guardarCambios();
        if($resultado == 1) {
            // Guardamos sus componentes
            $id_plantilla = $plant->id_plantilla;
            $error_componentes = false;

            // Preparamos los ids de los componentes de la plantilla de producto
            if(!$no_hay_cabina) $ids_componentes[] = $id_cabina;
            for($i=0;$i<count($ids_perifericos);$i++) {
                $ids_componentes[] = $ids_perifericos[$i];
            }
            /*
            for($i=0;$i<count($ids_software);$i++) {
                $ids_componentes[] = $ids_software[$i];
            }
            */

            // Guardamos los componentes de la plantilla de producto
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
echo '<script type="text/javascript" src="../js/basicos/nueva_plantilla_producto.js"></script>';
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
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Cabina</div>
            <div id="CapaBotonesCabOP">
                <input type="button" id="BotonTodasCabinas" name="BotonTodasCabinas" class="BotonEliminar" value="Mostrar todas las cabinas" onclick="javascript:MostrarTodasCabinas()" />
                <input type="hidden" id="tipo_boton_cabina" name="tipo_boton_cabina" value="TODAS"/>
            </div>
        </div>
        <!-- Lista de las cabinas -->
        <div class="ContenedorCamposCreacionBasico">
            <div id="lista_cabinas">
                <div class="LabelCreacionBasico"></div>
                <select id="cabina" name="cabina" class="CreacionBasicoInput">
                    <option value="0">Selecciona..</option>
                    <?php
                        $listado_cab->prepararConsultaProduccion();
                        $listado_cab->realizarConsulta();
                        $resultado_cabinas = $listado_cab->cabinas;

                        for($i=0;$i<count($resultado_cabinas);$i++) {
                            $datoCab = $resultado_cabinas[$i];
                            $cab->cargaDatosCabinaId($datoCab["id_componente"]);
                            echo '<option value="'.$cab->id_componente.'">'.$cab->cabina.'_v'.$cab->version.'</option>';
                        }
                    ?>
                </select>

                <?php
                    // Se guarda en un input hidden los id de las cabinas de produccion
                    // Se guarda en un input hidden los nombres de las cabinas de produccion
                    $listado_cab->prepararConsultaProduccion();
                    $listado_cab->realizarConsulta();
                    $resultado_cabinas = $listado_cab->cabinas;

                    for($i=0;$i<count($resultado_cabinas);$i++) {
                        $datoCab = $resultado_cabinas[$i];
                        $cab->cargaDatosCabinaId($datoCab["id_componente"]);
                        echo '<input type="hidden" id="id_cab_produccion[]" name="id_cab_produccion[]" value="'.$cab->id_componente.'"/>';
                        echo '<input type="hidden" id="nombre_cab_produccion[]" name="nombre_cab_produccion[]" value="'.$cab->cabina.'_v'.$cab->version.'"/>';
                    }

                    // Se guarda en un input hidden los id de todas las cabinas
                    // Se guarda en un input hidden los nombres de todas las cabinas
                    $listado_cab->prepararConsulta();
                    $listado_cab->realizarConsulta();
                    $resultado_todas_cabinas = $listado_cab->cabinas;

                    for($i=0;$i<count($resultado_todas_cabinas);$i++) {
                        $datoTodasCab = $resultado_todas_cabinas[$i];
                        $cab->cargaDatosCabinaId($datoTodasCab["id_componente"]);
                        echo '<input type="hidden" id="id_todas_cabinas[]" name="id_todas_cabinas[]" value="'.$cab->id_componente.'"/>';
                        echo '<input type="hidden" id="nombre_todas_cabinas[]" name="nombre_todas_cabinas[]" value="'.$cab->cabina.'_v'.$cab->version.'"/>';
                    }
                ?>
            </div>
        </div>
        <br/>

        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Periféricos</div>
            <div id="CapaBotonesPerOP">
                <input type="button" id="BotonTodosPerifericos" name="BotonTodosPerifericos" class="BotonEliminar" value="Mostrar todos los periféricos" onclick="javascript:MostrarTodosPerifericos()"/>
            </div>
        </div>
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico"></div>
            <div class="contenedorComponentes">
                <table style="width:700px; height:208px; border:1px solid #fff;">
                    <tr>
                        <td id= "listas_no_asignados" style="width:250px; border:1px solid #fff; padding-left:10px;">
                            <select multiple="multiple" id="perifericos_no_asignados[]" name="perifericos_no_asignados[]" class="SelectMultiplePerOrigen">
                                <?php
                                    // Solo los perifericos de produccion
                                    $listado_per->prepararConsultaProduccion();
                                    $listado_per->realizarConsulta();
                                    $resultado_perifericos = $listado_per->perifericos;

                                    for($i=0;$i<count($resultado_perifericos);$i++) {
                                        $datoPerif = $resultado_perifericos[$i];
                                        $per->cargaDatosPerifericoId($datoPerif["id_componente"]);
                                        echo '<option value="'.$per->id_componente.'">'.$per->periferico.'_v'.$per->version.'</option>';
                                    }
                                ?>
                            </select>

                            <?php
                                // Solo los perifericos de produccion guardados en input hidden
                                $listado_per->prepararConsultaProduccion();
                                $listado_per->realizarConsulta();
                                $resultado_perifericos = $listado_per->perifericos;

                                for($i=0;$i<count($resultado_perifericos);$i++) {
                                    $datoPerif = $resultado_perifericos[$i];
                                    $per->cargaDatosPerifericoId($datoPerif["id_componente"]);
                                    echo '<input type="hidden" id="id_per_produccion[]" name="id_per_produccion[]" value="'.$per->id_componente.'"/>';
                                    echo '<input type="hidden" id="nombre_per_produccion[]" name="nombre_per_produccion[]" value="'.$per->periferico.'_v'.$per->version.'"/>';
                                }

                                // Todos los perifericos guardados en input	hidden
                                $listado_per->prepararConsulta();
                                $listado_per->realizarConsulta();
                                $resultado_todos_perifericos = $listado_per->perifericos;

                                for($i=0;$i<count($resultado_todos_perifericos);$i++) {
                                    $datoTodosPerif = $resultado_todos_perifericos[$i];
                                    $per->cargaDatosPerifericoId($datoTodosPerif["id_componente"]);
                                    echo '<input type="hidden" id="id_todos_perifericos[]" name="id_todos_perifericos[]" value="'.$per->id_componente.'"/>';
                                    echo '<input type="hidden" id="nombre_todos_perifericos[]" name="nombre_todos_perifericos[]" value="'.$per->periferico.'_v'.$per->version.'"/>';
                                }
                            ?>
                        </td>
                        <td style="border:1px solid #fff; vertical-align:middle">
                            <table style="width:100%; border:1px solid #fff;">
                                <tr>
                                    <td style="border:1px solid #fff;"><input type="button" id="añadirPeriferico" name="añadirPeriferico" class="BotonEliminar" onclick="AddToSecondList()" value="AÑADIR" /></td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #fff;"></td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #fff;"><input type="button" id="quitarPeriferico" name="quitarPeriferico" class="BotonEliminar" onclick="DeleteSecondListItem()" value="QUITAR" /></td>
                                </tr>
                            </table>
                        </td>
                        <td id="lista" style="width:250px; border:1px solid #fff;"><select multiple="multiple" id="perifericos[]" name="perifericos[]" class="SelectMultiplePerDestino"></select></td>
                    </tr>
                </table>
            </div>
        </div>
        <br/>

        <!-- <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Software</div>
            <select multiple="multiple" id="software[]" name="software[]" class="SelectMultiple">
                <?php
                    /*
                    $listado_soft->prepararConsulta();
                    $listado_soft->realizarConsulta();
                    $resultado_softwares = $listado_soft->softwares;

                    for($i=0;$i<count($resultado_softwares);$i++) {
                        $datoSoft = $resultado_softwares[$i];
                        $soft->cargaDatosSoftwareId($datoSoft["id_componente"]);
                        echo '<option value="'.$soft->id_componente.'">'.$soft->software.'</option>';
                    }
                    */
                ?>
            </select>
        </div> -->
        <br/>
        <br/>

        <div class="ContenedorBotonCreacionBasico">
          	<input type="button" id="volver" name="volver" value="Volver" onclick="javascript:window.history.back()" /> 
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