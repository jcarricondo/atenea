<?php
set_time_limit(10000);
// Primer paso para la modificación de la Orden de Producción
// Carga de clases y funciones JavaScript
include("../includes/sesion.php");
include("../classes/basicos/cabina.class.php");
include("../classes/basicos/periferico.class.php");
include("../classes/basicos/software.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/basicos/nombre_producto.class.php");
include("../classes/basicos/listado_cabinas.class.php");
include("../classes/basicos/listado_perifericos.class.php");
include("../classes/basicos/listado_softwares.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/orden_produccion/incluir_referencia_libre.class.php");
include("../classes/productos/producto.class.php");
include("../classes/control_usuario.class.php");
permiso(10);

$orden_produccion = new Orden_Produccion();
$producto = new Producto();
$control_usuario = new Control_Usuario();

if(isset($_POST["guardandoOrdenProduccion"]) and $_POST["guardandoOrdenProduccion"] == 1) {
	// Se reciben los datos
	$alias_op = $_POST["alias_op"];
	$unidades = $_POST["unidades"];
	$nombre_producto = $_POST["nombre_producto"];
	$cabina = $_POST["cabina"];
	$perifericos = $_POST["perifericos"];
	$software = $_POST["software"];
	$ref_libres = $_POST["REFS"];
	$cliente= $_POST["cliente"];
	$id_produccion = $_GET["id_produccion"];
	$piezas = $_POST["piezas"];
}
else {
// Se cargan los datos de la orden de produccion y los productos asociados en funcion de su ID
$id_produccion = $_GET["id_produccion"];
$orden_produccion->cargaDatosProduccionId($id_produccion);
$alias_op = $orden_produccion->alias_op;
$unidades = $orden_produccion->unidades;
$fecha_inicio = $orden_produccion->fecha_inicio;
$fecha_entrega = $orden_produccion->fecha_entrega;
$fecha_entrega_deseada = $orden_produccion->fecha_entrega_deseada;
$estado = $orden_produccion->estado;
$id_sede = $orden_produccion->id_sede;
$id_producto = $_GET["id_producto"];
$producto->cargaDatosProductoId($id_producto);
$id_nombre_producto = $producto->id_nombre_producto;
}

$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$esAdminGlobal = $control_usuario->esAdministradorGlobal($id_tipo_usuario);
$esAdminGes = $control_usuario->esAdministradorGes($id_tipo_usuario);

$titulo_pagina = "Órdenes de Producción > Modifica Orden de Producción";
$pagina = "mod_orden_produccion";
include ('../includes/header.php');
echo '<script type="text/javascript" src="../js/orden_produccion/mod_orden_produccion.js"></script>';
?>

<div class="separador"></div> 
<?php include("../includes/menu_op.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar"><?php include ("../includes/sidebar.php"); ?></div>

    <h3>Modificación de orden de producción</h3>
    <form id="FormularioCreacionBasico" name="modificarOrdenProduccion" onsubmit="return validarFormulario()" action="confirm_mod_orden_produccion.php?id_produccion=<?php echo $id_produccion;?>&id_producto=<?php echo $id_producto;?>" method="post">
    	<br />
        <h5> Modifique los datos en el siguiente formulario</h5>
    	 <?php 
    		if($esAdminGlobal || $esAdminGes){
    			// ADMINISTRADOR GLOBAL. Elige la sede de la OP 
    			if($id_sede == 1) $nombre_sede = "SIMUMAK";
    			else if($id_sede == 2) $nombre_sede = "TORO";
 		?>
		    	<div class="ContenedorCamposCreacionBasico">
		           	<div class="LabelCreacionBasico">Sede</div>
		            <input type="text" id="nombre_sede" name="nombre_sede" class="CreacionBasicoInput" readonly="readonly" value="<?php echo $nombre_sede;?>" />
		            <input type="hidden" id="sede" name="sede" value="<?php echo $sede;?>"/> 
		        </div>
		<?php
			}
		?> 
    	<div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Alias</div>
            <input type="text" id="alias_op" name="alias_op" class="CreacionBasicoInput" value="<?php echo $alias_op;?>" onblur="comprobarAliasCorrecto()" />           <div id="alias_correcto">
            	<input type="hidden" id="alias_validado" name="alias_validado" value="1" />
            </div>
        </div>         
        <div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Unidades *</div>
            <input type="text" id="unidades" name="unidades" class="CreacionBasicoInput" readonly="readonly" value="<?php echo $unidades;?>" />
        </div>    
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Producto *</div>
            	<?php 
					// Primero cargamos el nombre de producto asociado a los productos de esa Orden de Produccion para que se quede seleccionado de manera predeterminada
					$nombre_producto = new Nombre_Producto();
					$nombre_producto->cargaDatosNombreProductoId($id_nombre_producto);
				?>
            <input type="text" id="producto" name="producto" class="CreacionBasicoInput" readonly="readonly" value="<?php echo $nombre_producto->nombre;?>" />
            <input type="hidden" id="id_nombre_producto" name="id_nombre_producto" value="<?php echo $id_nombre_producto;?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Cabina</div>
            <div id="CapaBotonesCabOP"><input type="button" id="BotonCabProduccion" name="BotonCabProduccion" class="BotonEliminar" value="Mostrar cabinas en producción" onclick="javascript:MostrarCabProduccion()" /></div>
        </div>
        <div class="ContenedorCamposCreacionBasico">
        	<div id="lista_cabinas">
            	<div class="LabelCreacionBasico"></div>
            	<select id="cabina" name="cabina" class="CreacionBasicoInput">
            		<option value="0">Selecciona...</option>
            		<?php 
						$bbdd = new MySQL;
						$cabs = new listadoCabinas();
						$cabs->prepararConsulta();
						$cabs->realizarConsulta();
						$resultado_cabinas = $cabs->cabinas;

						$ids_produccion_componente = $orden_produccion->dameIdsProduccionComponente($id_produccion);
						$i=0;
						$encontrado = false;
						while($i<count($ids_produccion_componente) and !$encontrado){
        					$id_produccion_componente = $ids_produccion_componente[$i]["id_produccion_componente"];
        					$id_componente = $orden_produccion->dameIdComponentePorIdProduccionComponente($id_produccion_componente);
        					$id_cabina = $id_componente[0]["id_componente"];
        					// Obtenemos el tipo del componente
        					$id_tipo = $orden_produccion->dameTipoComponente($id_cabina);
        					$id_tipo = $id_tipo["id_tipo"];
        					if($id_tipo == 1){
        						$encontrado = true;
        					}
        					$i++;
        				}	

						// Ahora mostramos en el select la cabina que tenia asignada el producto de esa orden de produccion					
						for($i=0;$i<count($resultado_cabinas);$i++) {
							$cab = new Cabina();
							$datoCab = $resultado_cabinas[$i];
							$cab->cargaDatosCabinaId($datoCab["id_componente"]);
							echo '<option value="'.$cab->id_componente.'" '; if ($cab->id_componente == $id_cabina) echo 'selected="selected"'; echo '>'.$cab->cabina.'_v'.$cab->version.'</option>';
			       		}
					?>
            	</select>
                
                <?php 
					// Solo cabinas en produccion guardadas en un input hidden 
					$cabs = new listadoCabinas();
					$cabs->prepararConsultaProduccion();
					$cabs->realizarConsulta();
					$resultado_cabinas = $cabs->cabinas;

					for($i=0;$i<count($resultado_cabinas);$i++) {
						$cab = new Cabina();
						$datoCab = $resultado_cabinas[$i];
						$cab->cargaDatosCabinaId($datoCab["id_componente"]);
						echo '<input type="hidden" id="id_cab_produccion[]" name="id_cab_produccion[]" value="'.$cab->id_componente.'"/>';
						echo '<input type="hidden" id="nombre_cab_produccion[]" name="nombre_cab_produccion[]" value="'.$cab->cabina.'_v'.$cab->version.'"/>';									
						if ($cab->id_componente == $Cabina->id_cabina["id_componente"]) {
							echo '<input type="hidden" id="cab_sel_produccion[]" name="cab_sel_produccion[]" value="'.$cab->id_componente.'"/>'; 
						}
					}
				
					// Todas las cabinas guardadas en un input hidden
					$todas_cabs = new listadoCabinas();
					$todas_cabs->prepararConsulta();
					$todas_cabs->realizarConsulta();
					$resultado_todas_cabinas = $todas_cabs->cabinas;

					for($i=0;$i<count($resultado_todas_cabinas);$i++) {
						$cab_t = new Cabina();
						$datoTodasCab = $resultado_todas_cabinas[$i];
						$cab_t->cargaDatosCabinaId($datoTodasCab["id_componente"]);
						echo '<input type="hidden" id="id_todas_cabinas[]" name="id_todas_cabinas[]" value="'.$cab_t->id_componente.'"/>';
						echo '<input type="hidden" id="nombre_todas_cabinas[]" name="nombre_todas_cabinas[]" value="'.$cab_t->cabina.'_v'.$cab_t->version.'"/>';
						if ($cab_t->id_componente == $Cabina->id_cabina["id_componente"]) {
							echo '<input type="hidden" id="cab_sel_todas[]" name="cab_sel_todas[]" value="'.$cab_t->id_componente.'"/>'; 
						}									
					}
				?>
            </div>        
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Periféricos</div>
            <div id="CapaBotonesPerOP">
            	<input type="button" id="BotonPerProduccion" name="BotonPerProduccion" class="BotonEliminar" value="Mostrar periféricos en producción" onclick="javascript:MostrarPerProduccion()"/>
            </div>
        </div>
        <div class="ContenedorCamposCreacionBasico">
            <div class="CajaPerifericos">
            	<table style="width:700px; height:208px; border:1px solid #fff; margin:5px 10px 0px 12px;">
                <tr>
                   	<td id= "listas_no_asignados" style="width:250px; border:1px solid #fff;">
            			<select multiple="multiple" id="perifericos_no_asignados[]" name="perifericos_no_asignados[]" class="SelectMultiplePerOrigen" >
            			<?php 
							$bbdd = new MySQL;
							$perifs = new listadoPerifericos();
							$perifs->prepararConsulta();
							$perifs->realizarConsulta();
							$resultado_perifericos = $perifs->perifericos;

							$Perifericos = new Orden_Produccion();
							for($i=0;$i<count($resultado_perifericos);$i++) {
								$perif = new Periferico();
								$datoPerif = $resultado_perifericos[$i];
								$perif->cargaDatosPerifericoId($datoPerif["id_componente"]);
								echo '<option value="'.$perif->id_componente.'">'.$perif->periferico.'_v'.$perif->version.'</option>'; 
							}
						?>
            			</select>   
                        
                        <?php 
							// Solo los perifericos de produccion guardados en input hidden
							$perifs = new listadoPerifericos();
							$perifs->prepararConsultaProduccion();
							$perifs->realizarConsulta();
							$resultado_perifericos = $perifs->perifericos;

							for($i=0;$i<count($resultado_perifericos);$i++) {
								$perif = new Periferico();
								$datoPerif = $resultado_perifericos[$i];
								$perif->cargaDatosPerifericoId($datoPerif["id_componente"]);
							    echo '<input type="hidden" id="id_per_produccion[]" name="id_per_produccion[]" value="'.$perif->id_componente.'"/>';
								echo '<input type="hidden" id="nombre_per_produccion[]" name="nombre_per_produccion[]" value="'.$perif->periferico.'_v'.$perif->version.'"/>';	
							}
						
							// Todos los perifericos guardados en input	hidden						
							$todos_perifs = new listadoPerifericos();
							$todos_perifs->prepararConsulta();
							$todos_perifs->realizarConsulta();
							$resultado_todos_perifericos = $todos_perifs->perifericos;

							for($i=0;$i<count($resultado_todos_perifericos);$i++) {
								$perif_t = new Periferico();
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
                    <td id="lista" style="width:250px; border:1px solid #fff;">
	                	<select multiple="multiple" id="perifericos[]" name="perifericos[]" class="SelectMultiplePerDestino">
                        	<?php
                        		for($i=0;$i<count($ids_produccion_componente);$i++){
        							$id_produccion_componente = $ids_produccion_componente[$i]["id_produccion_componente"];
        							$id_componente = $orden_produccion->dameIdComponentePorIdProduccionComponente($id_produccion_componente);
        							$id_componente = $id_componente[0]["id_componente"];
        							// Obtenemos el tipo del componente
        							$id_tipo = $orden_produccion->dameTipoComponente($id_componente);
        							$id_tipo = $id_tipo["id_tipo"];
        							if($id_tipo == 2){
        								$perif->cargaDatosPerifericoId($id_componente);
										echo '<option value="'.$perif->id_componente.'">'.$perif->periferico.'_v'.$perif->version.'</option>';
        							}
        					  	}
        					?>
                        </select>
                    </td>                        
                </tr>
                </table>       
        	</div>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Software</div>
            <select multiple="multiple" id="software[]" name="software[]" class="SelectMultiple">
            	<?php 
					$bbdd = new MySQL;
					$softs = new listadoSoftwares();
					$softs->prepararConsulta();
					$softs->realizarConsulta();
					$resultado_softwares = $softs->softwares;

					$Softwares = new Orden_Produccion();
					$ids_softwares = $Softwares->dameIdsSoftwares($id_produccion);
					for($i=0;$i<count($ids_softwares);$i++){
						$ids_softwares[$i] = $ids_softwares[$i]["id_componente"];
					}

					for($i=0;$i<count($resultado_softwares);$i++) {
						$soft = new Software();
						$datoSoft = $resultado_softwares[$i];
						$soft->cargaDatosSoftwareId($datoSoft["id_componente"]);
						echo '<option value="'.$soft->id_componente.'" '; 
						for ($j=0;$j<count($ids_softwares);$j++)
							if ($soft->id_componente == $ids_softwares[$j]) echo 'selected="selected"'; echo '>'.$soft->software.'</option>';
					}
				?>
            </select> 
        </div>        
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Referencias Libres </div>
           		<div class="CajaReferencias">
            		<div id="CapaTablaIframe">
    					<table id="mitablaRefsLibres">
        				<tr>
                        	<th style="text-align:center">ID_REF</th>
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
	                    <?php 
	                    	// Referencias Libres
	                    	// Obtenemos las piezas la tabla orden_produccion_referencias y los datos de la tabla referencias
	                    	$precio_total = 0;
							$fila = 0; 
							$ref_modificada = new Referencia();

							$referencias_libres = $orden_produccion->cargaDatosPorProduccionComponente($id_produccion,0);
							// Hacemos la carga de la tabla referencias
							for ($i=0;$i<count($referencias_libres);$i++){
								$id_referencia_libre = $referencias_libres[$i]["id_referencia"];
								$piezas = $referencias_libres[$i]["piezas"];

								$ref_modificada->cargaDatosReferenciaId($id_referencia_libre);
								if ($ref_modificada->pack_precio <> 0 and $ref_modificada->unidades <>0){
									$precio_unidad = $ref_modificada->pack_precio / $ref_modificada->unidades;	
								}
								else {
									$precio_unidad = 0;	
								}
								$ref_modificada->calculaTotalPaquetes($ref_modificada->unidades,$piezas);
								$total_paquetes = $ref_modificada->total_paquetes;
								$precio_referencia = $piezas * $precio_unidad;
								$precio_total = $precio_total + $precio_referencia;
						?>
                        	<tr>
                        		<td style="text-align:center"><?php echo $ref_modificada->id_referencia; ?></td>
                        		<td id="enlaceComposites">
                        			<a href="../basicos/mod_referencia.php?id=<?php echo $ref_modificada->id_referencia;?>"/><?php echo $ref_modificada->referencia; ?></a>
                        			<input type="hidden" name="REFS[]" id="REFS[]" value="<?php echo $ref_modificada->id_referencia;?>" />
                        		</td>
                        		<td><?php echo $ref_modificada->nombre_proveedor; ?></td>
                        		<td><?php echo $ref_modificada->vincularReferenciaProveedor();?></td>
                        		<td><?php echo $ref_modificada->part_nombre; ?></td>
                        		<td style="text-align:center"><input type="text" name="piezas[]" id="piezas[]" class="CampoPiezasInput" value="<?php echo $piezas; ?>" onblur="javascript:validarPiezasCorrectas(<?php echo $fila;?>)" /></td>
                        		<td style="text-align:center"><?php echo number_format($ref_modificada->pack_precio,2,'.',''); ?></td>
                        		<td style="text-align:center"><?php echo $ref_modificada->unidades; ?></td>
                        		<td style="text-align:center"><?php echo $total_paquetes;?></td>
                        		<td style="text-align:center"><?php echo number_format($precio_unidad, 2, '.', '');?></td>
                        		<td style="text-align:center"><?php echo number_format($precio_referencia, 2, '.', '');?></td>
                        		<td style="text-align:center"><input type="checkbox" name="chkbox" value="<?php echo $id_referencia_libre;?>" /></td>
                        	</tr> 
		                        <?php $fila = $fila + 1; ?>
								<input type="hidden" name="fila" id="fila" value="<?php echo $fila;?>"/>        
		                <?php		
							}
						?>
                        </table>
                    </div>
			    </div> 
            	<input type="button" id="mas" name="mas" class="BotonMas"  value="+" onclick="javascript:Abrir_ventana('buscador_referencias_libres.php')"/> 																																																																																																																																			
           		<input type="button" id="menos" name="menos" class="BotonMenos" value="-" onclick="javascript:removeRow(mitabla)"  />
        </div>
        <br/>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Coste Referencias Libres </div>
            <label id="precio_refs_libres" class="LabelPrecio"><?php echo number_format($precio_total, 2, ',', '.').'€';?></label>
        </div>        
        
        <?php 
			// Se cargan los productos de la orden de produccion "id_produccion"
			$Productos_OP = new Orden_Produccion();
			$Productos_OP->dameIdsProductoOP($id_produccion);
			echo '<br/><div class="ContenedorCamposCreacionBasico"><div class="LabelCreacionBasico">Productos</div><div class="CajaReferencias"><div id="CapaTablaIframe"><table id="mitabla">';
           	include ("../orden_produccion/muestra_producto.php");
			// Muestra tabla producto $i
			echo '</table></div></div></div>';
		?>
        <br />

        <div class="ContenedorBotonCreacionBasico">
          	<input type="button" id="volver" name="volver" value="Volver" onclick="window.history.back()" /> 
            <input type="hidden" id="confirmarOrdenProduccion" name="confirmarOrdenProduccion" value="1" />
            <input type="submit" id="guardar" name="guardar" value="Continuar" />
            <input type="hidden" id="id_produccion" name="id_produccion" value="<?php echo $id_produccion;?>" />
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