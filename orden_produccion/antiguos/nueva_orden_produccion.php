<?php
// Primer paso para la creación de una Orden de Producción 
// Carga de clases y funciones JavaScript
include("../includes/sesion.php");
include("../classes/control_usuario.class.php");
include("../classes/sede/sede.class.php");
include("../classes/basicos/periferico.class.php");
include("../classes/basicos/nombre_producto.class.php");
include("../classes/basicos/listado_perifericos.class.php");
include("../classes/basicos/listado_nombre_producto.class.php");
include("../classes/almacen/almacen.class.php");
// include("../classes/basicos/cabina.class.php");
// include("../classes/basicos/software.class.php");
// include("../classes/basicos/listado_cabinas.class.php");
// include("../classes/basicos/listado_softwares.class.php");
permiso(9);

$control_usuario = new Control_Usuario();
$sede_class = new Sede();
$nombre_prod = new Nombre_Producto();
$perif = new Periferico();
$perif_t = new Periferico();
$perifs = new listadoPerifericos();
$todos_perifs = new listadoPerifericos();
$nom_prods = new listadoNombreProducto();
$almacen = new Almacen();
// $cab = new Cabina();
// $cab_t = new Cabina();
// $soft = new Software();
// $cabs = new listadoCabinas();
// $todas_cabs = new listadoCabinas();
// $softs = new listadoSoftwares();

// Obtenemos el tipo de usuario para conocer su sede
$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$id_almacen = $_SESSION["AT_id_almacen"];

$esAdminGlobal = $control_usuario->esAdministradorGlobal($id_tipo_usuario);
// Comprobamos si es Administrador o Usuario de Gestion
$esAdminGes = $control_usuario->esAdministradorGes($id_tipo_usuario);

if(isset($_POST["guardandoOP"]) and $_POST["guardandoOP"] == 1) {
	// Se reciben los datos
	$alias_op = $_POST["alias_op"];
	$unidades = $_POST["unidades"];
	$producto = $_POST["producto"];
	$perifericos = $_POST["perifericos"];
	$ref_libres = $_POST["REFS"];
	$piezas = $_POST["piezas"];
	$fecha_inicio_construccion = $_POST["fecha_inicio_construccion"];
	$sede = $_POST["sede"];
    // $cabina = $_POST["cabina"];
    // $software = $_POST["software"];
}
else {
	$alias_op = "";
	$unidades = "";
	$producto = "";
	$perifericos = "";
	$ref_libres = "";
	$fecha_inicio_construccion = "";
	$sede = "";
	$Campos_no_rellenados = false;
    // $cabina = "";
    // $software = "";
}
$titulo_pagina = "Órdenes de Producción > Nueva Orden de Producción";
$pagina = "new_orden_produccion";
include ('../includes/header.php');
echo '<script type="text/javascript" src="../js/orden_produccion/nueva_orden_produccion.js"></script>';
?>

<div class="separador"></div>
<?php include("../includes/menu_op.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar"><?php include ("../includes/sidebar.php"); ?></div>
      
    <h3> Creación de una nueva orden de producción </h3>
    <form id="FormularioCreacionBasico" name="crearOrdenProduccion" onsubmit="return validarFormulario()" action="confirm_nueva_orden_produccion.php" method="post">
		<br />
        <h5> Rellene los siguientes campos para la creación de una nueva orden de producción </h5>
    	<?php 
    		if($esAdminGlobal || $esAdminGes){
    			// ADMINISTRADOR SIMUMAK. Elige la sede de la OP
                $res_sedes = $sede_class->dameSedesFabrica(); ?>
                <div class="ContenedorCamposCreacionBasico">
		           	<div class="LabelCreacionBasico">Sede</div>
		            <select id="sede" name="sede"  class="CreacionBasicoInput">
                        <?php
                            for($i=0;$i<count($res_sedes);$i++) {
                                $id_sede_bus = $res_sedes[$i]["id_sede"];
                                $nombre_sede = $res_sedes[$i]["sede"]; ?>
                                <option value="<?php echo $id_sede_bus; ?>"><?php echo $nombre_sede; ?></option>
                        <?php
                            }
                        ?>
					</select>
		        </div>
		<?php
			}
			else { 
				// Obtenemos la sede a la que pertenece el usuario 
				$id_sede = $almacen->dameSedeAlmacen($id_almacen);
				$id_sede = $id_sede["id_sede"]; ?>
				<input type="hidden" id="sede" name="sede" value="<?php echo $id_sede;?>"/> 
		<?php	
			}	
		?>        
    	<div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Alias</div>
            <input type="text" id="alias_op" name="alias_op" class="CreacionBasicoInput" value="<?php echo $alias_op;?>" onblur="comprobarAliasCorrecto()"/>
          	<div id="alias_correcto">
            	<input type="hidden" id="alias_validado" name="alias_validado" value="-1" />
            </div>
        </div>
        <div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Unidades *</div>
            <input type="text" id="unidades" name="unidades" class="CreacionBasicoInput" value="<?php echo $unidades;?>" onkeypress="return soloNumeros(event)" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Fecha Inicio Construcci&oacute;n</div>
            <input type="text" id="fecha_inicio_construccion" class="fechaCal" name="fecha_inicio_construccion" readonly="readonly" value="<?php echo $fecha_inicio_construccion;?>"  />
        </div>    
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Producto *</div>
            <select id="producto" name="producto"  class="CreacionBasicoInput" onchange="cargaPlantillasProducto(this.value)">
                <option value="0">Seleccione un nombre de producto</option>
            	<?php 
					$nom_prods->prepararConsulta();
					$nom_prods->realizarConsulta();
					$resultado_nombres_producto = $nom_prods->nombre_productos;

					for($i=0;$i<count($resultado_nombres_producto);$i++) {
						$datoNombreProducto = $resultado_nombres_producto[$i];
						$nombre_prod->cargaDatosNombreProductoId($datoNombreProducto["id_nombre_producto"]);
						echo '<option value="'.$nombre_prod->id_nombre_producto.'">'.$nombre_prod->nombre.'_'.$nombre_prod->version.'</option>';
					}
				?>
            </select>
        </div>
        <div id="CapaPlantillaProducto" class="ContenedorCamposCreacionBasico" style="display: none;">
            <div id="PlantillaProducto" style="display: none;">

            </div>
        </div>
        <div id="CapaContenedorComponentes" style="display: block;">
            <!--
            <div class="ContenedorCamposCreacionBasico">
                <div class="LabelCreacionBasico">Cabina</div>
                <div id="CapaBotonesCabOP">
                    <input type="button" id="BotonTodasCabinas" name="BotonTodasCabinas" class="BotonEliminar" value="Mostrar todas las cabinas" onclick="javascript:MostrarTodasCabinas()" />
                    <input type="hidden" id="tipo_boton_cabina" name="tipo_boton_cabina" value="TODAS"/>
                </div>
            </div>

            <!-- Lista de las cabinas ->
            <div class="ContenedorCamposCreacionBasico">
                <div id="lista_cabinas">
                    <div class="LabelCreacionBasico"></div>
                    <select id="cabina" name="cabina" class="CreacionBasicoInput">
                        <option value="0">Selecciona..</option>
                        <?php
                            /*
                            $cabs->prepararConsultaProduccion();
                            $cabs->realizarConsulta();
                            $resultado_cabinas = $cabs->cabinas;

                            for($i=0;$i<count($resultado_cabinas);$i++) {
                                $datoCab = $resultado_cabinas[$i];
                                $cab->cargaDatosCabinaId($datoCab["id_componente"]);
                                echo '<option value="'.$cab->id_componente.'">'.$cab->cabina.'_v'.$cab->version.'</option>';
                            }
                            */
                        ?>
                    </select>

                    <?php
                        /*
                        // Se guarda en un input hidden los id de las cabinas de produccion
                        // Se guarda en un input hidden los nombres de las cabinas de produccion
                        $cabs->prepararConsultaProduccion();
                        $cabs->realizarConsulta();
                        $resultado_cabinas = $cabs->cabinas;

                        for($i=0;$i<count($resultado_cabinas);$i++) {
                            $datoCab = $resultado_cabinas[$i];
                            $cab->cargaDatosCabinaId($datoCab["id_componente"]);
                            echo '<input type="hidden" id="id_cab_produccion[]" name="id_cab_produccion[]" value="'.$cab->id_componente.'"/>';
                            echo '<input type="hidden" id="nombre_cab_produccion[]" name="nombre_cab_produccion[]" value="'.$cab->cabina.'_v'.$cab->version.'"/>';
                        }

                        // Se guarda en un input hidden los id de todas las cabinas
                        // Se guarda en un input hidden los nombres de todas las cabinas
                        $todas_cabs->prepararConsulta();
                        $todas_cabs->realizarConsulta();
                        $resultado_todas_cabinas = $todas_cabs->cabinas;

                        for($i=0;$i<count($resultado_todas_cabinas);$i++) {
                            $datoTodasCab = $resultado_todas_cabinas[$i];
                            $cab_t->cargaDatosCabinaId($datoTodasCab["id_componente"]);
                            echo '<input type="hidden" id="id_todas_cabinas[]" name="id_todas_cabinas[]" value="'.$cab_t->id_componente.'"/>';
                            echo '<input type="hidden" id="nombre_todas_cabinas[]" name="nombre_todas_cabinas[]" value="'.$cab_t->cabina.'_v'.$cab_t->version.'"/>';
                        }
                        */
                    ?>
               </div>
            </div>-->

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
                                $perifs->prepararConsultaProduccion();
                                $perifs->realizarConsulta();
                                $resultado_perifericos = $perifs->perifericos;

                                for($i=0;$i<count($resultado_perifericos);$i++) {
                                    $datoPerif = $resultado_perifericos[$i];
                                    $perif->cargaDatosPerifericoId($datoPerif["id_componente"]);
                                    echo '<option value="'.$perif->id_componente.'">'.$perif->periferico.'_v'.$perif->version.'</option>';
                                }
                            ?>
                            </select>

                            <?php
                                // Solo los perifericos de produccion guardados en input hidden
                                $perifs->prepararConsultaProduccion();
                                $perifs->realizarConsulta();
                                $resultado_perifericos = $perifs->perifericos;

                                for($i=0;$i<count($resultado_perifericos);$i++) {
                                    $datoPerif = $resultado_perifericos[$i];
                                    $perif->cargaDatosPerifericoId($datoPerif["id_componente"]);
                                    echo '<input type="hidden" id="id_per_produccion[]" name="id_per_produccion[]" value="'.$perif->id_componente.'"/>';
                                    echo '<input type="hidden" id="nombre_per_produccion[]" name="nombre_per_produccion[]" value="'.$perif->periferico.'_v'.$perif->version.'"/>';
                                }

                                // Todos los perifericos guardados en input	hidden
                                $todos_perifs->prepararConsulta();
                                $todos_perifs->realizarConsulta();
                                $resultado_todos_perifericos = $todos_perifs->perifericos;

                                for($i=0;$i<count($resultado_todos_perifericos);$i++) {
                                    $datoTodosPerif = $resultado_todos_perifericos[$i];
                                    $perif_t->cargaDatosPerifericoId($datoTodosPerif["id_componente"]);
                                    echo '<input type="hidden" id="id_todos_perifericos[]" name="id_todos_perifericos[]" value="'.$perif_t->id_componente.'"/>';
                                    echo '<input type="hidden" id="nombre_todos_perifericos[]" name="nombre_todos_perifericos[]" value="'.$perif_t->periferico.'_v'.$perif_t->version.'"/>';
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

            <!--
            <div class="ContenedorCamposCreacionBasico">
                <div class="LabelCreacionBasico">Software</div>
                <select multiple="multiple" id="software[]" name="software[]" class="SelectMultiple">
                    <?php
                        /*
                        $softs->prepararConsulta();
                        $softs->realizarConsulta();
                        $resultado_softwares = $softs->softwares;

                        for($i=0;$i<count($resultado_softwares);$i++) {
                            $datoSoft = $resultado_softwares[$i];
                            $soft->cargaDatosSoftwareId($datoSoft["id_componente"]);
                            echo '<option value="'.$soft->id_componente.'">'.$soft->software.'</option>';
                        }
                        */
                    ?>
                </select>
            </div> -->
            <div class="ContenedorCamposCreacionBasico">
                <div class="LabelCreacionBasico">Referencias Libres </div>
                <div class="CajaReferencias">
                    <div id="CapaTablaIframe">
                        <table id="mitablaRefsLibres">
                        <tr>
                            <th style="text-align:center">ID</th>
                            <th>NOMBRE</th>
                            <th>PROVEEDOR</th>
                            <th>REF. PROVEEDOR</th>
                            <th>NOMBRE PIEZA</th>
                            <th style="text-align:center">PIEZAS</th>
                            <th style="text-align:center">PACK PRECIO</th>
                            <th style="text-align:center">UDS/P</th>
                            <th style="text-align:center">TOTAL PAQS</th>
                            <th style="text-align:center">PRECIO UNIDAD</th>
                            <th style="text-align:center">PRECIO</th>
                            <th style="text-align:center">ELIMINAR</th>
                        </tr>
                        </table>
                    </div>
                </div>
                <?php
                    // Hay que hacer un seguimiento de las filas para cuando se añadan referencias. Si se modifica el campo piezas de una referencia
                    // añadida, necesitaremos saber que fila de la tabla se esta modificando. Al principio el numero de filas es cero
                    $fila = 0;
                ?>
                <input type="hidden" name="fila" id="fila" value="<?php echo $fila;?>"/>
                <input type="button" id="mas" name="mas" class="BotonMas"  value="+" onclick="javascript:Abrir_ventana('buscador_referencias_libres.php')"/>
                <input type="button" id="menos" name="menos" class="BotonMenos" value="-" onclick="javascript:removeRow(mitablaRefsLibres)"  />
            </div>
            <br/>

            <div class="ContenedorCamposCreacionBasico">
                <div class="LabelCreacionBasico">Coste Referencias Libres </div>
                <label id="precio_refs_libres" class="LabelPrecio"><?php echo number_format(0.00, 2, ',', '.').'€';?></label>
            </div>
        </div>
        
        <div class="ContenedorBotonCreacionBasico">
           	<input type="button" id="volver" name="volver" value="Volver" onclick="javascript:window.history.back()"/> 
            <input type="hidden" id="guardandoOP" name="guardandoOP" value="1"/>            
            <input type="submit" id="continuar" name="continuar" value="Continuar" />
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