<?php
set_time_limit(10000);
// Este fichero confirma la nueva Orden de Producción de Mantenimiento
// Carga de clases y funciones JavaScript
include("../includes/sesion.php");
include("../classes/basicos/proveedor.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/orden_produccion/incluir_referencia_libre.class.php");
include("../classes/orden_compra/orden_compra.class.php");
include("../classes/productos/producto.class.php");
include("../classes/control_usuario.class.php");
include("../classes/kint/Kint.class.php");
permiso(15);

$control_usuario = new Control_Usuario();
$orden_produccion = new Orden_Produccion();
$orden_compra = new Orden_Compra();
$producto = new Producto();
$contador_num_pedido = new Producto();	
$ref = new Referencia();

// Obtenemos el tipo de usuario para conocer su sede
$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];

// Se reciben los datos de Nueva Orden de Producción
$alias_op = $_POST["alias_op"];
$referencias_libres = $_POST["REFS"];  
$piezas = $_POST["piezas"]; 
$sede = $_POST["sede"];

// Obtenemos la sede de la Orden de Produccion
$id_sede = $sede;

// Confirmar Nueva Orden Producción
$ref_libres = $_POST["ref_libres"];

if(isset($_POST["guardandoOrdenProduccion"]) and $_POST["guardandoOrdenProduccion"] == 1) {
	// Primero creamos la orden de producción de Mantenimiento. 
	// En ordenes_produccion insertamos solo el id_produccion, el alias y el estado BORRADOR como predeterminado
	$unidades = 1;
	$id_tipo = 2;
	$orden_produccion->datosNuevaProduccion($id_produccion,$unidades,$codigo,$id_tipo,$fecha_inicio,$fecha_entrega,$fecha_entrega_deseada,$estado,$comentarios,$alias_op,$fecha_inicio_construccion,$id_sede);
	$resultado = $orden_produccion->guardarCambios();
	if ($resultado == 1) {
		// Con el nuevo id_produccion formamos el código de orden de producción
		$id_produccion = $orden_produccion->id_produccion;
		$codigo = "SMK_MANT_".$id_produccion;
		$resultado = $orden_produccion->insertaCodigoOrdenProduccion($id_produccion,$codigo);
		
		// Insertamos el alias en la Orden de Producción. Si el alias es vacio entonces metemos en el alias el código de la Orden de Producción
		if ($resultado == 1){
			if ($alias_op == ""){
				$alias_op = $codigo;
			}
			// Actualizamos el alias de la Orden de Producción. 
			$resultado = $orden_produccion->insertaAliasOrdenProduccion($id_produccion,$alias_op);
						
			if ($resultado == 1) {	
				// Creamos el "producto" e insertamos las referencias libres
				$ref_libres = $_POST["ref_libres"]; 
				$uds_paquete = $_POST["uds_paquete"];
				$Piezas = $_POST["Piezas"];
				$tot_paquetes = $_POST["tot_paquetes"];

				$contador_producto = 150;
				$num_serie = "SMK_MANT_".$id_produccion."_".$contador_producto;
				
				// Tenemos que comprobar si se insertaron referencias libres duplicadas
				// Si hay referencias
				if ($ref_libres != NULL){
					// Hay que comprobar si las referencias libres estan duplicadas. 
					// Calculamos las repeticiones de las referencias
					$array_repeticiones_referencias = array_count_values($ref_libres);	
					// Obtenemos los ids de las referencias (claves) sin repetir a partir del array con el numero de repeticiones por referencia
					$id_refs_unicas = array_keys($array_repeticiones_referencias);
								
					// Guardamos en un array las claves de las referencias repetidas del array de referencias 
					for($k=0;$k<count($id_refs_unicas);$k++){
						$claves_repetidas_todas_refs[$id_refs_unicas[$k]] = array_keys($ref_libres,$id_refs_unicas[$k]);					
					}
				
					// Ahora recalculamos las piezas en el caso de que haya referencias repetidas
					for($k=0;$k<count($claves_repetidas_todas_refs);$k++){
						$piezas_por_referencia = 0;
						$total_paquetes_por_referencia = 0;
						$claves_repetidas_referencia = $claves_repetidas_todas_refs[$id_refs_unicas[$k]];
															
						for($l=0;$l<count($claves_repetidas_referencia);$l++){ 
							$clave_pieza = $claves_repetidas_referencia[$l];
							$piezas_por_referencia = $piezas_por_referencia + $Piezas[$clave_pieza]; 
							$total_paquetes_por_referencia = $total_paquetes_por_referencia + $tot_paquetes[$clave_pieza];
								
							// Obtenemos la primera "unidad_paquete" de las referencias repetidas
							if ($l==0) {
								$uds_paquete_final[] = $uds_paquete[$clave_pieza];
							}
						}
						// Guardamos en un nuevo array la suma de las piezas de las referencias repetidas
						$piezas_final[] = $piezas_por_referencia;
						$total_paquetes_final[] = $total_paquetes_por_referencia;
					}
				
					// Guardamos en un nuevo array las referencias sin repeticiones
					$referencias_final = array_unique($ref_libres);
					$referencias_final = array_merge($referencias_final); 
						
					// Reseteamos los arrays y copiamos los obtenidos 
					unset($ref_libres);
					unset($Piezas);
					unset($uds_paquete);
					unset($tot_paquetes);
					$ref_libres = $referencias_final;
					$Piezas = $piezas_final;
					$uds_paquetes = $uds_paquete_final;
					$tot_paquetes = $total_paquetes_final;
				}

				for($i=0;$i<count($ref_libres);$i++){
					$id_referencia = $ref_libres[$i];
					$uds_paquete = $uds_paquetes[$i];
					$piezas = $Piezas[$i];
					$total_paquetes = $tot_paquetes[$i];

					$ref->cargaDatosReferenciaId($id_referencia);
					$pack_precio = $ref->pack_precio;

					// Guardamos las referencias de la orden de produccion de mantenimiento
					$resultado = $orden_produccion->guardarReferenciasProduccion($id_produccion,2,0,0,$id_referencia,$uds_paquete,$piezas,$total_paquetes,$pack_precio);		
					if ($resultado != 1){
						$i = count($ref_libres);
						$error = true;
					}
				}

				$producto->datosNuevoProducto($id_producto,$id_produccion,-1,$id_cliente,$num_serie,$num_ordenadores,$estado_producto,$fecha_entrega,$fecha_entrega_prevista,$ids_ordenadores,$ordenador,$id_cabina,$ids_perifericos,$perifericos,$ids_softwares,$software,$ref_cabina,$uds_paquete_cabina,$piezas_cabina,$total_paquetes_cabina,$ref_perifericos,$uds_paquete_perifericos,$piezas_perifericos,$total_paquetes_perifericos,$ref_libres,$uds_paquete,$Piezas,$tot_paquetes,$contador_productos,$contador_componentes);
				$resultado = $producto->guardarCambios();
		
				// Si se ha guardado el "producto de mantenimiento" correctamente, entonces generamos las ordenes de compra 
				if ($resultado == 1) {
					// INICIAR ORDENES DE COMPRA EN ESTADO BORRADOR
					// Tenemos que generar las Ordenes de compra en estado borrador. Para ello deberemos guardar:
					// en la tabla Orden de Compra los proveedores asociados a esa orden de produccion.
					// en la tabla Orden de Compra Referencias las referencias totales agrupadas por id_componente
			
					// Guardamos los ids de los productos de una Orden de Produccion
					$orden_produccion->dameIdsProductoOP($id_produccion); 
					$ids_productos_op = $orden_produccion->ids_productos;
					$array_ids_productos[0] = $ids_productos_op[0]["id_producto"];

					// Guardamos los ids_proveedores asociados a la Orden de Produccion.
					$orden_produccion->dameIdsProveedores($id_produccion);
					$ids_proveedores = $orden_produccion->ids_proveedores;
					for ($i=0;$i<count($ids_proveedores);$i++) {
						$array_ids_proveedores[$i] = $ids_proveedores[$i]["id_proveedor"];
					}
				
					
					$contador_num_pedido->dameUltimoContadorPedido();		
					$ultimo_id = $contador_num_pedido->id_contador_pedido["id"];
					$fallo = false;
					$i=0;
					$j=0;
					while ($i<count($array_ids_proveedores) and !$fallo) { 
						$j++;
						// Tenemos que obtener los t. de suministro y la forma de pago de los proveedores para obtener F.E.P. y F.P.P.las fechas de pago prevista
						$proveedor = new Proveedor();
						$proveedor->cargaDatosProveedorId($array_ids_proveedores[$i]);
						$tiempo_suministro = $proveedor->tiempo_suministro;
						$dias = 0;
						if ($tiempo_suministro == 0) $dias = $dias + 0;
						else if ($tiempo_suministro == 1) $dias = $dias + 7;
						else if ($tiempo_suministro == 2) $dias = $dias + 14;
						else if ($tiempo_suministro == 3) $dias = $dias + 30;
                		else if ($tiempo_suministro == 4) $dias = $dias + 60;
						else $dias = $dias + 90;					
							
						// Guardamos las ordenes de compra generadas en estado BORRADOR
						// Calculamos la fecha de pedido = fecha_actual + 7 dias
						// Calculamos la fecha de entrega prevista = fecha pedido + tiempo suministro del proveedor
						$fecha_pedido = date("m/d/Y", strtotime(date("m/d/Y")." +1 week"));
						$fecha_pedido = $orden_produccion->cFechaMyEsp($fecha_pedido);
						$fecha_pedido = $orden_produccion->cFechaMy($fecha_pedido);
									
						$fecha_entrega = date("m/d/Y", strtotime($fecha_pedido." +".$dias." days"));
						$fecha_entrega = $orden_produccion->cFechaMyEsp($fecha_entrega);
						$fecha_entrega = $orden_produccion->cFechaMy($fecha_entrega);
					
						// Contador para calcular el numero_pedido de las ordenes de compra
						if (empty ($ultimo_id)){
							$contador_num_pedido->IniciaContadorPedido(1);
							$ultimo_id = $contador_num_pedido->id_contador_pedido["id"];
						}
						else {
							$contador_num_pedido->AumentaContadorPedido($ultimo_id);
							$ultimo_id = $contador_num_pedido->id_contador_pedido;
						}
					
						$numero_pedido = "SMK_MANT_".$id_produccion."_".$ultimo_id;
						// Generamos el nombre de la OC ('OP + id_produccion + nombre proveedor')
						$nombre_orden_compra = 'OP'.$id_produccion.$proveedor->nombre;
						
						$estado = "GENERADA";
						$orden_compra->datosNuevaCompra($id_compra,$id_produccion,$array_ids_proveedores[$i],$numero_pedido,$fecha_pedido,$fecha_requerida,$direccion_entrega,$direccion_facturacion,$fecha_factura,$comentarios,$estado_anterior,$estado,$tasas,$unidades,$fecha_entrega,$nombre_orden_compra);
						$resultado = $orden_compra->guardarCambios();
								
						$fallo = $resultado != 1;
						$i++;
						if ($fallo == 1) {
							$mensaje_error = $orden_compra->getErrorMessage($resultado);
							$id_compra = $orden_compra->id_compra;
							$resultado = $orden_compra->eliminar($id_compra);
						}
					}
					if (!$fallo) {
						// Despues de insertar la orden de compra insertamos sus referencias asociadas
						$i=0;
						while($i<count($array_ids_proveedores) and !$fallo) {
							$referencias_proveedor = $orden_produccion->dameReferenciasPorProveedor($id_produccion,$array_ids_proveedores[$i]);
							// Guardamos en la tabla orden_compra_referencias todas las referencias de ese proveedor
							// Para calcular el total de piezas multiplicamos por las unidades de la Orden de Produccion 
							for($j=0;$j<count($referencias_proveedor);$j++) {
								$id_referencia = $referencias_proveedor[$j]["id_referencia"];
								$uds_paquete = $referencias_proveedor[$j]["uds_paquete"];
								$pack_precio = $referencias_proveedor[$j]["pack_precio"];
								$piezas = $referencias_proveedor[$j]["piezas"];
								$total_piezas = $unidades * $referencias_proveedor[$j]["piezas"];
								$ref->calculaTotalPaquetes($referencias_proveedor[$j]["uds_paquete"],$total_piezas);
								$total_packs = $ref->total_paquetes;
								$coste = $total_packs * $referencias_proveedor[$j]["pack_precio"];	

								// Obtenemos la orden de compra a la que pertenece esa referencia
								$id_compra = $orden_produccion->dameIdCompraPorProduccionYProveedor($id_produccion,$array_ids_proveedores[$i]);
								$id_compra = $id_compra[0]["id_orden_compra"];

								$resultado = $orden_compra->guardarReferenciasOC($id_compra,$id_referencia,$uds_paquete,$piezas,$total_piezas,$total_packs,$pack_precio,$coste);											
								if($resultado != 1){
									// Desactivar orden_compra_referencias
									$j = count($referencias_proveedor);
									$fallo = true;
								}
							}	
							$i++;
						}	
						if(!$fallo){
							header("Location: ../orden_produccion/ordenes_produccion.php?OProduccion=creado&tipo=2&sedes=".$id_sede);
						} 
						else{
							// ERROR AL GUARDAR LAS REFERENCIAS DE LAS ORDENES DE COMPRA	 
							$mensaje_error = $orden_compra->getErrorMessage($resultado);		
						}
					}
				}
				// Error al crear el producto
				else {
					$resultado_desactivarOP = $orden_produccion->desactivarOrdenProduccion($id_produccion);
					if ($resultado_desactivarOP == 1) {
						$mensaje_error = $producto->getErrorMessage($resultado);
					}
					else $mensaje_error = $producto->getErrorMessage($resultado_desactivarOP);
				}
			}
			// Error insertar el ALIAS OP
			else {
				$mensaje_error = $orden_produccion->getErrorMessage($resultado);
			}
		}
		// Error insertar codigo OP
		else {
			$mensaje_error = $orden_produccion->getErrorMessage($resultado);
		}
	}
	// Error crear OP
	else {
		$mensaje_error = $orden_produccion->getErrorMessage($resultado);
	}	
} 

$titulo_pagina = "Orden Compra > Confirmación Orden de Producción de Mantenimiento";
$pagina = "confirm_new_orden_produccion_mantenimiento";
include ('../includes/header.php');
?>

<div class="separador"></div> 
<?php include("../includes/menu_oc.php");?> 

<div id="ContenedorCentral">
	<div id="ContenedorSidebar"><?php include ("../includes/sidebar.php"); ?></div>
    
    <h3> Confirmación de la nueva Orden de Producción de Mantenimiento </h3>
    <form id="FormularioCreacionBasico" name="confirmNuevaOrdenProduccion" action="confirm_nueva_op_mantenimiento.php" method="post">
    	<br />
        <h5> Datos de la nueva Orden de Producción de Mantenimiento </h5>
        <?php 
    		if($id_tipo_usuario == 1 || $id_tipo_usuario == 8 || $id_tipo_usuario == 9){
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
			else { ?>
				<input type="hidden" id="sede" name="sede" value="<?php echo $sede;?>"/> 
		<?php	
			}			
		?>
    	<div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Alias</div>
            <input type="text" id="alias_op" name="alias_op" class="CreacionBasicoInput" readonly="readonly" value="<?php echo $alias_op;?>" />
        </div>    
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Referencias Libres </div>
           	<div class="CajaReferenciasMantenimiento">
            	<div id="CapaTablaIframe">
    				<table id="mitablaRefsLibres">
        			<tr>
                       	<th style="text-align:center">ID_REF</th>
                       	<th>NOMBRE</th>
   						<th>PROVEEDOR</th>
    					<th>REF PROV</th>
   						<th>NOMBRE PIEZA</th>
    					<th style="text-align:center">PIEZAS</th>
   						<th style="text-align:center">PACK PRECIO</th>
   						<th style="text-align:center">UDS/P</th>
    					<th style="text-align:center">TOTAL PAQS</th>
    					<th style="text-align:center">PRECIO UNIDAD</th>
   						<th style="text-align:center">PRECIO</th>
                    </tr>  
                    <?php 
						$precio_refs_libres = 0;
						// Copiamos el array para el input hidden 
						$ref_libres = $referencias_libres;
						for($i=0;$i<count($referencias_libres);$i++) {
							// Se cargan los datos de las referencias según su identificador
							$ref = new Referencia_Libre();
							echo '<input type="hidden" id="ref_libres[]" name="ref_libres[]" value="'.$ref_libres[$i].'"/>';
							$ref->cargaDatosReferenciaLibreId($referencias_libres[$i]);
							echo '<input type="hidden" id="uds_paquete[]" name="uds_paquete[]" value="'.$ref->cantidad.'"/>';
					
							$r1= new Referencia();
							$r1->calculaTotalPaquetes($ref->cantidad,$piezas[$i]);
							$total_paquetes = $r1->total_paquetes;
												
							if($ref->pack_precio <> 0 and $ref->cantidad <> 0) {
								$precio_unidad = $ref->pack_precio / $ref->cantidad;
							} 
							else {
								$precio_unidad = 00;
							}
							$precio_referencia = $piezas[$i] * $precio_unidad;
							$precio_refs_libres = $precio_refs_libres + $precio_referencia;								
						
							echo '<input type="hidden" id="Piezas[]" name="Piezas[]" value="'.$piezas[$i].'"/>';
							echo '<input type="hidden" id="tot_paquetes[]" name="tot_paquetes[]" value="'.$total_paquetes.'"/>';
					?>
						<tr><td style="text-align:center"><?php echo $ref->id_referencia; ?></td><td id="enlaceComposites"><a href="../basicos/mod_referencia.php?id=<?php echo $ref->id_referencia;?>" target="_blank"/><?php echo $ref->referencia; ?></a></td><td><?php echo $ref->proveedor; ?></td><td><?php $ref->vincularReferenciaProveedor();//echo $ref->ref_proveedor;?></td><td><?php echo $ref->nombre_pieza; ?></td><td style="text-align:center"><?php echo number_format($piezas[$i], 2, ',', '.');?></td><td style="text-align:center"><?php echo number_format($ref->pack_precio, 2, ',', '.');?></td><td style="text-align:center"><?php echo $ref->cantidad; ?></td><td style="text-align:center"><?php echo $total_paquetes;?></td><td style="text-align:center"><?php echo number_format($precio_unidad, 2, ',', '.');?></td><td style="text-align:center"><?php echo number_format($precio_referencia, 2, ',', '.');?></td></tr>
                    <?php 
						} 
					?> 
                    </table>  
                    </div>
               </div> 
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Coste Refs Libres</div> 
            <div class="tituloComponente">
				<table id="tablaTituloPrototipo">
                <tr>
                	<td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;"><?php echo '<span class="tituloComp">'.number_format($precio_refs_libres, 2, ',', '.').'€'.'</span>'; ?></td>
                </tr>
                </table>    
            </div>
        </div>
        <br/>
        
        <div class="ContenedorBotonCreacionBasico">
        	<input type="button" id="volver" name="volver" value="Volver" onclick="javascript:location='nueva_op_mantenimiento.php?volver=true'"/> 
            <input type="hidden" id="guardandoOrdenProduccion" name="guardandoOrdenProduccion" value="1"/>
			<?php 
				if (!$fallo) {
					echo '<input type="submit" id="continuar" name="continuar" value="Continuar" />';
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
<?php include ("../includes/footer.php"); ?>