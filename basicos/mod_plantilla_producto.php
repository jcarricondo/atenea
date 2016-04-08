<?php 
// Este fichero modifica una plantilla de producto existente
include("../includes/sesion.php");
include("../classes/basicos/plantilla_producto.class.php");
include("../classes/basicos/nombre_producto.class.php");
include("../classes/basicos/cabina.class.php");
include("../classes/basicos/periferico.class.php");
include("../classes/basicos/software.class.php");
include("../classes/basicos/listado_cabinas.class.php");
include("../classes/basicos/listado_perifericos.class.php");
include("../classes/basicos/listado_softwares.class.php");
include("../classes/basicos/listado_nombre_producto.class.php");
permiso(34);

$plant = new Plantilla_Producto();
$np = new Nombre_Producto();
$cab = new Cabina();
$per = new Periferico();
$soft = new Software();
$listado_cab = new listadoCabinas();
$listado_per = new listadoPerifericos();
$listado_soft = new listadoSoftwares();
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
    $id_cabina = $_POST["cabina"];
    $ids_perifericos = $_POST["perifericos"];
    $ids_software = $_POST["software"];

    $no_hay_cabina = empty($id_cabina) || $id_cabina == -1;
    $no_hay_perifericos = empty($ids_perifericos);
    $no_hay_software = empty($ids_software);

    $plantilla_vacia = ($no_hay_cabina && $no_hay_perifericos && $no_hay_software);

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
                if(!$no_hay_cabina) $ids_componentes[] = $id_cabina;
                for($i=0;$i<count($ids_perifericos);$i++) {
                    $ids_componentes[] = $ids_perifericos[$i];
                }
                for($i=0;$i<count($ids_software);$i++) {
                    $ids_componentes[] = $ids_software[$i];
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
$id_cabina = $plant->dameCabinaPlantillaProducto($id_plantilla);
$ids_perifericos = $plant->damePerifericosPlantillaProducto($id_plantilla);
$ids_software = $plant->dameSoftwarePlantillaProducto($id_plantilla);

$titulo_pagina = "Básicos > Modifica plantilla de producto";
$pagina = "mod_plantilla_producto";
include("../includes/header.php");
echo '<script type="text/javascript" src="../js/basicos/mod_plantilla_producto.js"></script>';
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
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Cabina</div>
            <?php
                if($modificar) { ?>
                    <div id="CapaBotonesCabOP">
                        <input type="button" id="BotonCabProduccion" name="BotonCabProduccion" class="BotonEliminar" value="Mostrar cabinas en producción" onclick="javascript:MostrarCabProduccion()"/>
                    </div>
            <?php
                }
                else {
                    $cab->cargaDatosCabinaId($id_cabina);
                    $nombre_cabina = $cab->cabina;
                    $version_cabina = $cab->version; ?>

                    <input type="text" id="cabina" name="cabina" class="CreacionBasicoInput" value="<?php echo $nombre_cabina.'_v'.$version_cabina;?>" <?php echo $solo_lectura;?> />
            <?php
                }
            ?>
        </div>
        <?php
            if($modificar) { ?>
                <div class="ContenedorCamposCreacionBasico">
                    <div id="lista_cabinas">
                        <div class="LabelCreacionBasico"></div>
                        <select id="cabina" name="cabina" class="CreacionBasicoInput">
                            <option value="0">Selecciona...</option>
                            <?php
                                $listado_cab->prepararConsulta();
                                $listado_cab->realizarConsulta();
                                $resultado_cabinas = $listado_cab->cabinas;

                                // Ahora mostramos en el select la cabina que tenia asignada el nombre de producto de esa
                                for($i = 0; $i < count($resultado_cabinas); $i++) {
                                    $datoCab = $resultado_cabinas[$i];
                                    $cab->cargaDatosCabinaId($datoCab["id_componente"]);
                                    echo '<option value="' . $cab->id_componente . '" ';
                                    if($cab->id_componente == $id_cabina) echo 'selected="selected"';
                                        echo '>' . $cab->cabina . '_v' . $cab->version . '</option>';
                                    }
                            ?>
                        </select>

                        <?php
                            // Solo cabinas en produccion guardadas en un input hidden
                            $listado_cab->prepararConsultaProduccion();
                            $listado_cab->realizarConsulta();
                            $resultado_cabinas = $listado_cab->cabinas;

                            for($i = 0; $i < count($resultado_cabinas); $i++) {
                                $datoCab = $resultado_cabinas[$i];
                                $cab->cargaDatosCabinaId($datoCab["id_componente"]);
                                echo '<input type="hidden" id="id_cab_produccion[]" name="id_cab_produccion[]" value="' . $cab->id_componente . '"/>';
                                echo '<input type="hidden" id="nombre_cab_produccion[]" name="nombre_cab_produccion[]" value="' . $cab->cabina . '_v' . $cab->version . '"/>';
                                if($cab->id_componente == $cab->id_cabina["id_componente"]) {
                                    echo '<input type="hidden" id="cab_sel_produccion[]" name="cab_sel_produccion[]" value="' . $cab->id_componente . '"/>';
                                }
                            }

                            // Todas las cabinas guardadas en un input hidden
                            $listado_cab->prepararConsulta();
                            $listado_cab->realizarConsulta();
                            $resultado_todas_cabinas = $listado_cab->cabinas;

                            for($i = 0; $i < count($resultado_todas_cabinas); $i++) {
                                $datoTodasCab = $resultado_todas_cabinas[$i];
                                $cab->cargaDatosCabinaId($datoTodasCab["id_componente"]);
                                echo '<input type="hidden" id="id_todas_cabinas[]" name="id_todas_cabinas[]" value="' . $cab->id_componente . '"/>';
                                echo '<input type="hidden" id="nombre_todas_cabinas[]" name="nombre_todas_cabinas[]" value="' . $cab->cabina . '_v' . $cab->version . '"/>';
                                if($cab->id_componente == $cab->id_cabina["id_componente"]) {
                                    echo '<input type="hidden" id="cab_sel_todas[]" name="cab_sel_todas[]" value="' . $cab->id_componente . '"/>';
                                }
                            }
                        ?>
                    </div>
                </div>
        <?php
            }
        ?>
        <br/>

        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Periféricos</div>
            <?php
                if($modificar) { ?>
                    <div id="CapaBotonesPerOP">
                        <input type="button" id="BotonPerProduccion" name="BotonPerProduccion" class="BotonEliminar" value="Mostrar periféricos en producción" onclick="javascript:MostrarPerProduccion()" />
                    </div>
            <?php
                }
                else { ?>
                    <div class="CajaPerifericos">
                        <table style="width:700px; height:208px; border:1px solid #fff; margin:5px 10px 0px 12px;">
                            <tr>
                                <td id="lista" style="width:250px; border:1px solid #fff; padding-left:0px; padding-top:0px;">
                                    <select multiple="multiple" id="perifericos[]" name="perifericos[]" class="SelectMultiplePerDestino" style="margin-left:9px;" disabled="disabled">
                                        <?php
                                        for($i=0;$i<count($ids_perifericos);$i++){
                                            $id_componente = $ids_perifericos[$i]["id_componente"];
                                            $per->cargaDatosPerifericoId($id_componente);
                                            echo '<option value="'.$per->id_componente.'">'.$per->periferico.'_v'.$per->version.'</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>
            <?php
                }
            ?>
        </div>

        <?php
            if($modificar){ ?>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="CajaPerifericos">
                        <table style="width:700px; height:208px; border:1px solid #fff; margin:5px 10px 0px 12px;">
                            <tr>
                                <td id= "listas_no_asignados" style="width:250px; border:1px solid #fff;">
                                    <select multiple="multiple" id="perifericos_no_asignados[]" name="perifericos_no_asignados[]" class="SelectMultiplePerOrigen" >
                                        <?php
                                            $listado_per->prepararConsulta();
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
                                <td id="lista" style="width:250px; border:1px solid #fff;">
                                    <select multiple="multiple" id="perifericos[]" name="perifericos[]" class="SelectMultiplePerDestino">
                                        <?php
                                            for($i=0;$i<count($ids_perifericos);$i++){
                                                $id_componente = $ids_perifericos[$i]["id_componente"];
                                                $per->cargaDatosPerifericoId($id_componente);
                                                echo '<option value="'.$per->id_componente.'">'.$per->periferico.'_v'.$per->version.'</option>';
                                            }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
        <?php
            }
        ?>
        <br/>

        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Software</div>
            <select multiple="multiple" id="software[]" name="software[]" class="SelectMultiple" <?php echo $solo_lectura; ?>>
                <?php
                    $listado_soft->prepararConsulta();
                    $listado_soft->realizarConsulta();
                    $resultado_softwares = $listado_soft->softwares;

                    for($i=0;$i<count($resultado_softwares);$i++) {
                        $datoSoft = $resultado_softwares[$i];
                        $soft->cargaDatosSoftwareId($datoSoft["id_componente"]);
                        if($modificar){
                            echo '<option value="'.$soft->id_componente.'" ';
                            for($j=0;$j<count($ids_software); $j++)
                                if($soft->id_componente == $ids_software[$j]["id_componente"]) echo 'selected="selected"';
                            echo '>'.$soft->software.'</option>';
                        }
                        else {
                            for($j=0;$j<count($ids_software);$j++){
                                if($soft->id_componente == $ids_software[$j]["id_componente"]) echo '<option value="'.$soft->id_componente.'">'.$soft->software.'</option>';
                            }
                        }
                    }
                ?>
            </select>
        </div>
        <br/>
        <br/>

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