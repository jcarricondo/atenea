<?php
set_time_limit(10000);
// Este fichero confirma y guarda la nueva Orden de Producción
// Carga de clases y funciones JavaScript
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/cabina.class.php");
include("../classes/basicos/periferico.class.php");
include("../classes/basicos/software.class.php");
include("../classes/basicos/kit.class.php");
include("../classes/basicos/proveedor.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/basicos/nombre_producto.class.php");
include("../classes/basicos/plantilla_producto.class.php");
include("../classes/basicos/referencia_componente.class.php");
include("../classes/basicos/listado_referencias_componentes.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/orden_produccion/incluir_referencia_libre.class.php");
include("../classes/orden_compra/orden_compra.class.php");
include("../classes/productos/producto.class.php");
include("../classes/control_usuario.class.php");
include("../classes/kint/Kint.class.php");
permiso(9);

$funciones = new Funciones();
$cabina = new Cabina();
$periferico = new Periferico();
$soft = new Software();
$kit = new Kit();
$proveedor = new Proveedor();
$ref_modificada = new Referencia();
$nombre_producto = new Nombre_Producto();
$plant = new Plantilla_Producto();
$ref_comp = new Referencia_Componente();
$ref_kit = new Referencia_Componente();
$ref_componente = new listadoReferenciasComponentes();
$ref_kits = new listadoReferenciasComponentes();
$orden_produccion = new Orden_Produccion();
$ref = new Referencia_Libre();
$orden_compra = new Orden_Compra();
$producto = new Producto();
$control_usuario = new Control_Usuario();

// Obtenemos el tipo de usuario para conocer su sede
$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];

$esAdminGlobal = $control_usuario->esAdministradorGlobal($id_tipo_usuario);
// Comprobamos si es Administrador o Usuario de Gestion
$esAdminGes = $control_usuario->esAdministradorGes($id_tipo_usuario);

// Se reciben los datos de Nueva Orden de Producción
$alias_op = $_POST["alias_op"];
$unidades = $_POST["unidades"];
$id_name_producto = $_POST["producto"];
$id_cabina = $_POST["cabina"];
$perifericos = $_POST["perifericos"];
$software = $_POST["software"];
$referencias_libres = $_POST["REFS"];
$piezas = $_POST["piezas"];
$fecha_inicio_construccion = $_POST["fecha_inicio_construccion"];
$sede = $_POST["sede"];

// Comprobamos si se escogió una plantilla
$id_plantilla = $_POST["select_plantilla"];
if(!empty($id_plantilla)){
    unset($id_cabina);
    unset($perifericos);
    unset($software);
    unset($referencias_libres);
    unset($piezas);

    // Obtenemos los componentes de la plantilla
    $id_cabina = $plant->dameCabinaPlantillaProducto($id_plantilla);
    $res_perifericos = $plant->damePerifericosPlantillaProducto($id_plantilla);
    $res_software = $plant->dameSoftwarePlantillaProducto($id_plantilla);

    for($i=0;$i<count($res_perifericos);$i++){
        $perifericos[] = $res_perifericos[$i]["id_componente"];
    }

    for($i=0;$i<count($res_software);$i++){
        $software[] = $res_software[$i]["id_componente"];
    }
}

// Obtenemos la sede de la Orden de Produccion
$id_sede = $sede;

// Si el usuario no introdujo fecha de inicio de construcción será la fecha_creacion + 7 dias
if($fecha_inicio_construccion == NULL){
	// Obtenemos la última fecha de inicio de construcción de una OP iniciada
	$dias = 7;
	$resultado_fecha = $orden_produccion->dameUltimaFechaConstruccionIniciada();
	$ultima_fecha = $resultado_fecha["fecha_construccion"];

	// Comprobamos si la última fecha en estado iniciado es superior a la fecha actual
	$fecha_actual = date_create("now");
	// Le damos formato BBDD
	$fecha_actual = date_format($fecha_actual, "Y-m-d");
	// Comparamos ambas fechas 
	if($fecha_actual > $ultima_fecha){
		// La fecha actual ha sobrepasado la última fecha de inicio de construcción 
		$fecha = $fecha_actual;
	}
	else {
		$fecha = $ultima_fecha;
	}

	// Convertimos la fecha de formato MySQL a formato dd/mm/YYYY
	$fecha = $funciones->cFechaNormal($fecha);
	// Convertimos la fecha a formato mm/dd/YYYY para poder sumarle 1 semana
	$fecha = $funciones->cFechaMyEsp($fecha);
	// Sumamos 1 semana a la última fecha de inicio de construcción de las OP iniciadas o a la fecha_actual según el caso 
	$fecha_inicio_construccion = date("m/d/Y", strtotime($fecha." +".$dias." days" ));
	// Convertimos la fecha a formato dd/mm/YYYY	
	$fecha_inicio_construccion = $funciones->cFechaMyEsp($fecha_inicio_construccion);
}

// Ordenamos el array de Periféricos
if(count($perifericos)!=0) sort($perifericos);
if(isset($_POST["guardandoOrdenProduccion"]) and $_POST["guardandoOrdenProduccion"] == 1) {
	// Guardar Nueva Orden Producción
	$id_nombre_producto = $_POST["id_nombre_producto"];
	$id_cabina = $_POST["id_cabina"];
	$ids_perifericos = $_POST["IDS_PERS"];
	$ids_softwares = $_POST["IDS_SOFT"];
	$ref_libres = $_POST["ref_libres"];

	// Convertimos la fecha_inicio_construccion en formato MySql
	$fecha_inicio_construccion = $funciones->cFechaMy($fecha_inicio_construccion);
	
	// PRIMERO CREAMOS LA ORDEN DE PRODUCCION
	$id_tipo = 1;
	$orden_produccion->datosNuevaProduccion($id_produccion,$unidades,$codigo,$id_tipo,$fecha_inicio,$fecha_entrega,$fecha_entrega_deseada,$estado,$comentarios,$alias_op,$fecha_inicio_construccion,$id_sede);
	$resultado = $orden_produccion->guardarCambios();

	if($resultado == 1) {
		// Con el nuevo id_produccion formamos el código de orden de producción
		$id_produccion = $orden_produccion->id_produccion;
		// Cargamos los datos de la clase Nombre_Producto
		$nombre_producto->cargaDatosNombreProductoId($id_nombre_producto);
		$nombre_producto_aux = $nombre_producto->nombre;
		$codigo_nombre_producto = $nombre_producto->codigo;

		$codigo = 'OP_'.$nombre_producto_aux.'_'.$id_produccion;
		$resultado = $orden_produccion->insertaCodigoOrdenProduccion($id_produccion,$codigo);

		if($resultado == 1){
			if($alias_op == "") $alias_op = $codigo;

			$resultado = $orden_produccion->insertaAliasOrdenProduccion($id_produccion,$alias_op);
			if($resultado == 1) {
				// Guardamos los componentes que forman un producto en la Orden de Produccion				
				$contador_componente = 1;
				
				if($id_cabina != NULL and $id_cabina != 0 and $id_cabina != -1){
					$ids_componentes[] = $id_cabina;
					// Comprobamos si la cabina tiene kits
					$orden_produccion->dameIdsKitComponente($id_cabina);
					for($i=0;$i<count($orden_produccion->ids_kit);$i++){
						$ids_kit[] = $orden_produccion->ids_kit[$i]["id_kit"];
					}
					if($ids_kit != NULL) $ids_componentes = array_merge($ids_componentes,$ids_kit);
				}
				unset($ids_kit);
				if($ids_perifericos != NULL){
					for($i=0;$i<count($ids_perifericos);$i++){
						$ids_componentes[] = $ids_perifericos[$i];
						// Comprobamos si la cabina tiene kits
						$orden_produccion->dameIdsKitComponente($ids_perifericos[$i]);
						for($j=0;$j<count($orden_produccion->ids_kit);$j++){
							$ids_kit[] = $orden_produccion->ids_kit[$j]["id_kit"];
						}
						if($ids_kit != NULL) $ids_componentes = array_merge($ids_componentes,$ids_kit);
					    unset($ids_kit);
					}
				}
				if($ids_softwares != NULL){
					if($ids_componentes != NULL) $ids_componentes = array_merge($ids_componentes,$ids_softwares);
					else $ids_componentes = $ids_softwares;
				}

				$i=0;
				$error = false;
				while($i<count($ids_componentes) and !$error){
					$id_tipo = $orden_produccion->dameTipoComponente($ids_componentes[$i]);
					switch ($id_tipo["id_tipo"]) {
						case '1':
							// CABINA
							$cabina->cargaDatosCabinaId($ids_componentes[$i]);
							$num_serie_componente = $cabina->referencia."_".$cabina->version."_".$id_produccion."_".$contador_componente;
							$resultado = $orden_produccion->guardarComponenteProduccion($id_produccion,$ids_componentes[$i],$num_serie_componente);		
						break;
						case '2':
							// PERIFERICO
							$periferico->cargaDatosPerifericoId($ids_componentes[$i]);
							$num_serie_componente = $periferico->referencia."_".$periferico->version."_".$id_produccion."_".$contador_componente;
							$resultado = $orden_produccion->guardarComponenteProduccion($id_produccion,$ids_componentes[$i],$num_serie_componente);		
						break;
						case '3':
							// SOFTWARE
							$soft->cargaDatosSoftwareId($ids_componentes[$i]);
							$num_serie_componente = "-";
							$resultado = $orden_produccion->guardarComponenteProduccion($id_produccion,$ids_componentes[$i],$num_serie_componente);		
						break;
						case '4':
							// INTERFAZ
							// Dejan de existir en Agosto de 2016
						break;
						case '5':
							// KIT
							$kit->cargaDatosKitId($ids_componentes[$i]);
							$num_serie_componente = $kit->referencia."_".$kit->version."_".$id_produccion."_".$contador_componente;
							$resultado = $orden_produccion->guardarComponenteProduccion($id_produccion,$ids_componentes[$i],$num_serie_componente);				
						break;	
						default:
							# code...
						break;
					}
					$error = $resultado != 1;

					if(!$error){
						// Guardamos las referencias del componente
						$id_produccion_componente = $orden_produccion->dameUltimoIdProduccionComponente();
						// Guardamos las referencias de los componentes
						$ref_comp->dameReferenciasPorIdComponente($ids_componentes[$i]);
						$referencias_componente = $ref_comp->referencias_componente;
						for($j=0;$j<count($referencias_componente);$j++){
							$id_referencia = $referencias_componente[$j]["id_referencia"];
							$uds_paquete = $referencias_componente[$j]["uds_paquete"];
							$piezas = $referencias_componente[$j]["piezas"];
							// Calculamos el total_paquetes para la referencia
							$ref_modificada->calculaTotalPaquetes($uds_paquete,$piezas);
							$total_paquetes = $ref_modificada->total_paquetes;
							
							// Obtenemos el pack_precio de la tabla referencias 
							$ref_modificada->cargaDatosReferenciaId($id_referencia); 
							$pack_precio = $ref_modificada->pack_precio;
	
							$resultado = $orden_produccion->guardarReferenciasProduccion($id_produccion,$id_tipo["id_tipo"],$id_produccion_componente,$ids_componentes[$i],$id_referencia,$uds_paquete,$piezas,$total_paquetes,$pack_precio);		
							if($resultado != 1){
								$j = count($referencias_componente);
								$error = true;
							}	
						}		
					}
					$contador_componente++;
					$i++;
				}
				if($resultado == 1){
					// Guardamos las referencias libres
					$ref_libres = $_POST["ref_libres"];
					$uds_paquete = $_POST["uds_paquete"];
					$Piezas = $_POST["Piezas"];
					$tot_paquetes = $_POST["tot_paquetes"];

					// Tenemos que comprobar si se insertaron referencias libres duplicadas
					// Si hay referencias
					if($ref_libres != NULL){
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
								if($l==0) $uds_paquete_final[] = $uds_paquete[$clave_pieza];
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
						$uds_paquete = $uds_paquete_final;
						$tot_paquetes = $total_paquetes_final;

						$i=0;
						$error = false;
						while($i<count($ref_libres) and !$error){
							$ref_modificada->cargaDatosReferenciaId($ref_libres[$i]);
							$pack_precio = $ref_modificada->pack_precio;

							$resultado = $orden_produccion->guardarReferenciasProduccion($id_produccion,0,0,0,$ref_libres[$i],$uds_paquete[$i],$Piezas[$i],$tot_paquetes[$i],$pack_precio);		
							if($resultado != 1) $error = true;
							$i++;
						}
					}

					if($resultado == 1){
						// Guardamos los productos 
						$contador_componentes = 0;
						$contador_producto = 150;
						$fallo = false;
						$i=0;
						while ($i<$unidades and !$fallo) {
							// Los numeros de serie del producto empiezan en 150
							$num_serie = $codigo_nombre_producto.'_'.$id_produccion.'_'.$contador_producto;

							$producto->datosNuevoProducto($id_producto,$id_produccion,$id_nombre_producto,$id_cliente,$num_serie);
							$resultado = $producto->guardarCambios();
							$fallo = $resultado != 1;
							$contador_producto++;
							$i++;
						}
						if(!$fallo){
							// INICIAR ORDENES DE COMPRA EN ESTADO BORRADOR
							// Tenemos que generar las Ordenes de compra en estado borrador. Para ello deberemos guardar:
							// en la tabla Orden de Compra los proveedores asociados a esa orden de produccion.
							// en la tabla Orden de Compra Referencias las referencias totales agrupadas por id_componente

							// Guardamos los ids_proveedores asociados a la Orden de Produccion.
							$orden_produccion->dameIdsProveedores($id_produccion);
							$ids_proveedores = $orden_produccion->ids_proveedores;
							for ($i=0;$i<count($ids_proveedores);$i++) {
								$array_ids_proveedores[$i] = $ids_proveedores[$i]["id_proveedor"];
							}

							$producto->dameUltimoContadorPedido();							
							$ultimo_id = $producto->id_contador_pedido["id"];
							$fallo = false;
							$i=0;
							while($i<count($array_ids_proveedores) and !$fallo) {
								// Tenemos que obtener los t. de suministro y la forma de pago de los proveedores para obtener las FEP y las FPP
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
								if(empty($ultimo_id)){
									$producto->IniciaContadorPedido(1);
									$ultimo_id = $producto->id_contador_pedido["id"];
								}
								else {
									$producto->AumentaContadorPedido($ultimo_id);
									$ultimo_id = $producto->id_contador_pedido;
								}

								$numero_pedido = $nombre_producto_aux.'_'.$unidades.'_'.$ultimo_id;
								// Generamos el nombre de la OC ('OP + id_produccion + nombre proveedor')
								$nombre_orden_compra = 'OP'.$id_produccion.$proveedor->nombre;

								// Establecemos las direcciones de entrega y facturacion predefinidas para las ordenes de compra
								$direccion_facturacion = 1;
								$direccion_entrega = 2;	

								$estado = "GENERADA";
								$orden_compra->datosNuevaCompra($id_compra,$id_produccion,$array_ids_proveedores[$i],$numero_pedido,$fecha_pedido,$fecha_requerida,$direccion_entrega,$direccion_facturacion,$fecha_factura,$comentarios,$estado_anterior,$estado,$tasas,$unidades,$fecha_entrega,$nombre_orden_compra);
								$resultado = $orden_compra->guardarCambios();
								$fallo = $resultado != 1;
									
								$i++;
								if($fallo == true) {
									$mensaje_error = $orden_compra->getErrorMessage($resultado);
									$id_compra = $orden_compra->id_compra;
									$resultado = $orden_compra->eliminar($id_compra);
								}
							}
							if(!$fallo) {
								// Despues de insertar la orden de compra insertamos sus referencias asociadas
								$fallo = false;
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
										$ref_modificada->calculaTotalPaquetes($referencias_proveedor[$j]["uds_paquete"],$total_piezas);
										$total_packs = $ref_modificada->total_paquetes;
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
									header("Location: ordenes_produccion.php?OProduccion=creado&sedes=".$id_sede);	
								} 
								else{
									// ERROR AL GUARDAR LAS REFERENCIAS DE LAS ORDENES DE COMPRA	 
									$mensaje_error = $orden_compra->getErrorMessage($resultado);		
								}
							}	
							else{
								// ERROR AL GUARDAR LAS ORDENES DE COMPRA	 
								$mensaje_error = $orden_compra->getErrorMessage($resultado);	
							}
						}	
						else {
							// ERROR AL GUARDAR LOS PRODUCTOS
							$mensaje_error = $producto->getErrorMessage($resultado);
						}
					}	
					else {
						// ERROR AL GUARDAR LAS REFERENCIAS LIBRES
						$mensaje_error = $orden_produccion->getErrorMessage($resultado);
					}
				} 
				else {
					// ERROR AL GUARDAR LOS COMPONENTES O SUS REFERENCIAS
					$mensaje_error = $orden_produccion->getErrorMessage($resultado);
				}
			}	
			else {
				// ERROR AL GUARDAR EL ALIAS DE LA ORDEN DE PRODUCCION
				$mensaje_error = $orden_produccion->getErrorMessage($resultado);
			}
		}
		else {
			// ERROR AL GUARDAR EL CODIGO DE LA ORDEN DE PRODUCCION
			$mensaje_error = $orden_produccion->getErrorMessage($resultado);
		}
	}
	else {
		// ERROR AL GUARDAR LOS DATOS DE LA ORDEN DE PRODUCCION
		$mensaje_error = $orden_produccion->getErrorMessage($resultado);
	}
}
$max_caracteres_ref = 50;
$max_caracteres = 20;
$titulo_pagina = "Órdenes de Producción > Confirmación Orden de Producción";
$pagina = "confirm_new_orden_produccion";
include ('../includes/header.php');
?>

<div class="separador"></div>
<?php include("../includes/menu_op.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar"><?php include ("../includes/sidebar.php"); ?></div>

    <h3> Confirmación de la nueva Orden de Producción </h3>
    <form id="FormularioCreacionBasico" name="confirmNuevaOrdenProduccion" action="confirm_nueva_orden_produccion.php" method="post">
    	<br />
        <h5> Datos de la nueva Orden de Producción </h5>
        <?php 
    		if($esAdminGlobal || $esAdminGes){
    			// ADMINISTRADOR GLOBAL. Elige la sede de la OP 
    			if($id_sede == 1) $nombre_sede = "SIMUMAK";
    			else if($id_sede == 2) $nombre_sede = "TORO"; ?>
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
        	<div class="LabelCreacionBasico">Unidades *</div>
            <input type="text" id="unidades" name="unidades" class="CreacionBasicoInput" readonly="readonly" value="<?php echo $unidades;?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Fecha Inicio Construcci&oacute;n *</div>
            <input type="text" id="fecha_inicio_construccion" name="fecha_inicio_construccion" class="CreacionBasicoInput" readonly="readonly"  value="<?php echo $fecha_inicio_construccion;?>" />
        </div>    
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Producto *</div>
           	<?php $nombre_producto->cargaDatosNombreProductoId($id_name_producto); ?>
			<input type="text" id="producto" name="producto" class="CreacionBasicoInput" readonly="readonly" value="<?php echo $nombre_producto->nombre;?>" />
            <input type="hidden" id="id_nombre_producto" name="id_nombre_producto" value="<?php echo $id_name_producto;?>"/>
        </div>
        <?php
            if(!empty($id_plantilla)){ ?>
                <div class="ContenedorCamposCreacionBasico">
           	        <div class="LabelCreacionBasico">Plantilla *</div>
                    <?php $plant->cargaDatosPlantillaProductoId($id_plantilla); ?>
			        <input type="text" id="plantilla" name="plantilla" class="CreacionBasicoInput" readonly="readonly" value="<?php echo $plant->nombre;?>" />
                    <input type="hidden" id="id_plantilla_producto" name="id_plantilla_producto" value="<?php echo $id_plantilla;?>"/>
                </div>
        <?php
            }
        ?>

        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Cabina</div>
            <?php $cabina->cargaDatosCabinaId($id_cabina); ?>
            <input type="text" id="nombre_cabina" name="nombre_cabina" class="CreacionBasicoInput" readonly="readonly" value="<?php if (($id_cabina != 0) and ($id_cabina != -1)) echo $cabina->cabina.'_v'.$cabina->version;?>" />
            <input type="hidden" id="id_cabina" name="id_cabina" value="<?php echo $id_cabina;?>"/>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Perifericos</div>
          	<?php
				$ids_perifericos = $perifericos;
				for ($i=0;$i<count($perifericos);$i++){
					echo '<input type="hidden" id="IDS_PERS[]" name="IDS_PERS[]" value="'.$ids_perifericos[$i].'"/>';
					$periferico->cargaDatosPerifericoId($perifericos[$i]);
					$nombres_per[] = $periferico->periferico.'_v'.$periferico->version;
				}
			?>
            <textarea id="nombre_perifericos[]" name="nombre_perifericos[]" class="TextAreaOP" readonly="readonly" cols="1" rows="<?php echo count($perifericos);?>"><?php for($i=0;$i<count($nombres_per);$i++) echo $nombres_per[$i]."\n";?></textarea>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Software</div>
            <?php
				$ids_softwares = $software;
				for($i=0;$i<count($software);$i++){
					echo '<input type="hidden" id="IDS_SOFT[]" name="IDS_SOFT[]" value="'.$ids_softwares[$i].'"/>';
					$soft->cargaDatosSoftwareId($software[$i]);
					$nombres_soft[] = $soft->software;
				}
			?>
            <textarea id="software[]" name="software[]" class="TextAreaOP" readonly="readonly" cols="1" rows="<?php echo count($software);?>"><?php for($i=0;$i<count($nombres_soft);$i++) echo $nombres_soft[$i]."\n";?></textarea>
        </div>

        <?php
			if(($id_cabina != 0) and ($id_cabina != -1)) { ?>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Referencias Cabina</div>
                    <div class="tituloComponente">
                        <table id="tablaTituloPrototipo">
                        <tr>
                            <td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;"><?php echo '<span class="tituloComp">'.$cabina->cabina.'_v'.$cabina->version.'</span>';?></td>
                            <td style="text-align:left; background:#fff; vertical-align:top; padding:0px 5px 0px 5px;">
                                <?php
                                    if ($cabina->prototipo == 1) {
                                        echo '<span class="ImagenPrototipo"><img src="../images/prototipo.jpg" width="20px" height="20px" alt="PROTOTIPO" title="PROTOTIPO"></span>';
                                    }
                                    else if ($cabina->prototipo == 0){
                                        echo '<span class="ImagenPrototipo"><img src="../images/engranaje.gif" width="20px" height="20px" alt="PRODUCCION" title="PRODUCCION"></span>';
                                    }
                                ?>
                            </td>
                        </tr>
                        </table>
                    </div>
                    <div class="CajaReferencias">
                        <div id="CapaTablaIframe">
                            <table id="mitablaCabina"><?php include ("../orden_produccion/muestra_referencias_cabinas_op.php");?></table>
                            <?php $referencias_cabina = $resultadosBusquedaCabinas; ?>
                        </div>
                    </div>
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Coste Cabina</div>
                    <div class="tituloComponente">
                        <table id="tablaTituloPrototipo">
                        <tr>
                            <td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;"><?php echo '<span class="tituloComp">'.number_format($precio_cabina, 2, ',', '.').'€'.'</span>';?></td>
                        </tr>
                        </table>
                    </div>
                </div>

                <!-- Kits de la cabina -->
                <?php
                    $orden_produccion->dameIdsKitComponente($id_cabina);
                    for ($i=0;$i<count($orden_produccion->ids_kit);$i++){
                        $kit->cargaDatosKitId($orden_produccion->ids_kit[$i]["id_kit"]); ?>
                        <div class="ContenedorCamposCreacionBasico">
                            <div class="LabelCreacionBasico">Referencias Kit</div>
                            <div class="tituloComponente">
                                <table id="tablaTituloPrototipo">
                                <tr>
                                    <td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;"><?php echo '<span class="tituloComp">'.$kit->kit.'</span>';?></td>
                                </tr>
                                </table>
                            </div>
                            <div class="CajaReferencias">
                                <div id="CapaTablaIframe">
                                <table id="mitablaKitCab<?php echo $i;?>">
                                <?php
                                    $ref_kits->setValores($orden_produccion->ids_kit[$i]["id_kit"]);
                                    $ref_kits->realizarConsulta();
                                    $resultadosReferenciasKit = $ref_kits->referencias_componentes; ?>
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
                                    $precio_kit = 0;
                                    for($j=0;$j<count($resultadosReferenciasKit);$j++) {
                                        $datoRef_Kit = $resultadosReferenciasKit[$j];
                                        $ref_kit->cargaDatosReferenciaComponenteId($datoRef_Kit["id"]);
                                        $ref_modificada->cargaDatosReferenciaId($ref_kit->id_referencia);

                                        if ($ref_modificada->pack_precio <> 0 and $ref_modificada->unidades <> 0){
                                            $precio_unidad = ($ref_modificada->pack_precio / $ref_modificada->unidades);
                                        }
                                        else $precio_unidad = 00;

                                        $ref_modificada->calculaTotalPaquetes($ref_modificada->unidades,$ref_kit->piezas);
                                        $total_paquetes = $ref_modificada->total_paquetes;
                                        $precio_referencia = $ref_kit->piezas * $precio_unidad;
                                        echo '<tr>
                                                <td style="text-align:center;">'.$ref_kit->id_referencia.'</td>
                                                <td id="enlaceComposites">
                                                    <a href="../basicos/mod_referencia.php?id='.$ref_kit->id_referencia.'"/>';
                                                        if (strlen($ref_modificada->referencia) > $max_caracteres_ref) {
                                                            echo substr($ref_modificada->referencia,0,$max_caracteres_ref).'...';
                                                        }
                                                        else {
                                                            echo $ref_modificada->referencia;
                                                        }
                                                    '</a>
                                                </td>';
                                        echo '<td>';
                                                    if (strlen($ref_modificada->nombre_proveedor) > $max_caracteres){
                                                        echo substr($ref_modificada->nombre_proveedor,0,$max_caracteres).'...';
                                                    }
                                                    else echo $ref_modificada->nombre_proveedor;
                                            '</td>';
                                        echo '<td>';
                                                $ref_modificada->vincularReferenciaProveedor();
                                        echo '</td><td>';
                                                    if (strlen($ref_modificada->part_nombre) > $max_caracteres){
                                                        echo substr($ref_modificada->part_nombre,0,$max_caracteres).'...';
                                                    }
                                                    else echo $ref_modificada->part_nombre;
                                            '</td>';
                                        echo '</td><td style="text-align:center">'.number_format($ref_kit->piezas, 2, ',', '.').'</td><td style="text-align:center">'.number_format($ref_modificada->pack_precio, 2, ',', '.').'</td><td style="text-align:center">'.$ref_modificada->unidades.'</td><td style="text-align:center">'.$total_paquetes.'</td><td style="text-align:center">'.number_format($precio_unidad, 2, ',', '.').'</td><td style="text-align:center">'.number_format($precio_referencia, 2, ',', '.').'</td></tr>';
                                        echo '</tr>';

                                        $precio_kit = $precio_kit + $precio_referencia;
                                        $costeKits = $costeKits + $precio_referencia;
                                    }
                                ?>
                                </table>
                                </div>
                            </div>
                        </div>
                        <div class="ContenedorCamposCreacionBasico">
                            <div class="LabelCreacionBasico">Coste Kit Cabina</div>
                            <div class="tituloComponente">
                                <table id="tablaTituloPrototipo">
                                <tr>
                                    <td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;"><?php echo '<span class="tituloComp">'.number_format($precio_kit, 2, ',', '.').'€'.'</span>';?></td>
                                </tr>
                                </table>
                            </div>
                        </div>
                <?php
                    }
                ?>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Coste Total Cabina</div>
                    <div class="tituloComponente">
                        <table id="tablaTituloPrototipo">
                        <tr>
                            <td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;"><?php $precio_total_cabina = $precio_cabina + $costeKits; echo '<span class="tituloComp">'.number_format($precio_total_cabina, 2, ',', '.').'€'.'</span>';?></td>
                        </tr>
                        </table>
                    </div>
                </div>
                <br/>
        <?php
			}
			else { ?>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Referencias Cabina</div>
                    <div class="tituloSinComponente"><?php echo "No hay cábina";?></div>
                </div>
        <?php
		    }
            // Obtener el numero de perifericos para generar las tablas de referencias correspondientes a ese periferico
            $precio_todos_perifericos = 0;
            for($i=0;$i<count($perifericos);$i++){
                $precio_periferico = 0;
                $periferico->cargaDatosPerifericoId($perifericos[$i]);
                $id_componente = $perifericos[$i];
                echo '<br />';
                echo '<div class="ContenedorCamposCreacionBasico"><div class="LabelCreacionBasico">Referencias Periferico</div><div class="tituloComponente">';
                echo '<table id="tablaTituloPrototipo"><tr><td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;"><span class="tituloComp">'.$periferico->periferico.'_v'.$periferico->version.'</span></td>
                          <td style="text-align:left; background:#fff; vertical-align:top; padding:0px 5px 0px 5px;">';
                             if ($periferico->prototipo == 1) {
                                echo '<span class="ImagenPrototipo"><img src="../images/prototipo.jpg" width="20px" height="20px" alt="PROTOTIPO" title="PROTOTIPO"></span>';
                             }
                             else if ($periferico->prototipo == 0) {
                                echo '<span class="ImagenPrototipo"><img src="../images/engranaje.gif" width="20px" height="20px" alt="PRODUCCION" title="PRODUCCION"></span>';
                             }
                echo 	 '</td></tr></table></div><div class="CajaReferencias"><div id="CapaTablaIframe"><table id="mitabla">';

                require ("../orden_produccion/muestra_referencias_perifericos_op.php");

                echo '</table></div></div></div>';
                echo '<div class="ContenedorCamposCreacionBasico"><div class="LabelCreacionBasico">Coste Periferico</div><div class="tituloComponente"><table id="tablaTituloPrototipo"><tr><td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;"><span class="tituloComp">'.number_format($precio_periferico, 2, ',', '.').'€'.'</span></td></tr></table></div></div>';

                // Kits del Periferico
                $orden_produccion->dameIdsKitComponente($ids_perifericos[$i]);
                $costeKits = 0;
                for($k=0;$k<count($orden_produccion->ids_kit);$k++){
                    $kit->cargaDatosKitId($orden_produccion->ids_kit[$k]["id_kit"]); ?>
                    <div class="ContenedorCamposCreacionBasico">
                        <div class="LabelCreacionBasico">Referencias Kit</div>
                        <div class="tituloComponente">
                            <table id="tablaTituloPrototipo">
                            <tr>
                                <td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;"><?php echo '<span class="tituloComp">'.$kit->kit.'</span>'; ?></td>
                            </tr>
                            </table>
                        </div>
                        <div class="CajaReferencias">
                            <div id="CapaTablaIframe">
                                <table id="mitablaKit<?php echo $k;?>Per<?php echo $i;?>">
                                <?php
                                    $ref_kits->setValores($orden_produccion->ids_kit[$k]["id_kit"]);
                                    $ref_kits->realizarConsulta();
                                    $resultadosReferenciasKit = $ref_kits->referencias_componentes;
                                ?>
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
                                    $precio_kit = 0;
                                    for($j=0;$j<count($resultadosReferenciasKit);$j++) {
                                        $datoRef_Kit = $resultadosReferenciasKit[$j];
                                        $ref_kit->cargaDatosReferenciaComponenteId($datoRef_Kit["id"]);
                                        $ref_modificada->cargaDatosReferenciaId($ref_kit->id_referencia);

                                        if ($ref_modificada->pack_precio <> 0 and $ref_modificada->unidades <> 0){
                                            $precio_unidad = ($ref_modificada->pack_precio / $ref_modificada->unidades);
                                        }
                                        else {
                                            $precio_unidad = 00;
                                        }
                                        $ref_modificada->calculaTotalPaquetes($ref_modificada->unidades,$ref_kit->piezas);
                                        $total_paquetes = $ref_modificada->total_paquetes;
                                        $precio_referencia = $ref_kit->piezas * $precio_unidad;
                                        echo '<tr>
                                                <td style="text-align:center;">'.$ref_kit->id_referencia.'</td>
                                                <td id="enlaceComposites">
                                                    <a href="../basicos/mod_referencia.php?id='.$ref_kit->id_referencia.'"/>';
                                                        if (strlen($ref_modificada->referencia) > $max_caracteres_ref) {
                                                            echo substr($ref_modificada->referencia,0,$max_caracteres_ref).'...';
                                                        }
                                                        else {
                                                            echo $ref_modificada->referencia;
                                                        }
                                                    '</a>
                                                </td>';
                                        echo '<td>';
                                                    if (strlen($ref_modificada->nombre_proveedor) > $max_caracteres){
                                                        echo substr($ref_modificada->nombre_proveedor,0,$max_caracteres).'...';
                                                    }
                                                    else echo $ref_modificada->nombre_proveedor;
                                            '</td>';
                                        echo '<td>';
                                            $ref_modificada->vincularReferenciaProveedor();
                                        echo '</td><td>';
                                                    if (strlen($ref_modificada->part_nombre) > $max_caracteres){
                                                        echo substr($ref_modificada->part_nombre,0,$max_caracteres).'...';
                                                    }
                                                    else echo $ref_modificada->part_nombre;
                                            '</td>';
                                        echo '</td><td style="text-align:center">'.number_format($ref_kit->piezas, 2, ',', '.').'</td><td style="text-align:center">'.number_format($ref_modificada->pack_precio, 2, ',', '.').'</td><td style="text-align:center">'.$ref_modificada->unidades.'</td><td style="text-align:center">'.$total_paquetes.'</td><td style="text-align:center">'.number_format($precio_unidad, 2, ',', '.').'</td><td style="text-align:center">'.number_format($precio_referencia, 2, ',', '.').'</td></tr>';
                                        echo '</tr>';
                                        $precio_kit = $precio_kit + $precio_referencia;
                                        $costeKits = $costeKits + $precio_referencia;
                                    }
                                ?>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="ContenedorCamposCreacionBasico">
                        <div class="LabelCreacionBasico">Coste Kit Periferico</div>
                        <div class="tituloComponente">
                            <table id="tablaTituloPrototipo">
                            <tr>
                                <td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;"><?php echo '<span class="tituloComp">'.number_format($precio_kit, 2, ',', '.').'€'.'</span>'; ?></td>
                            </tr>
                            </table>
                        </div>
                    </div>
            <?php
                }
            ?>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Coste Total Periferico</div>
                    <div class="tituloComponente">
                        <table id="tablaTituloPrototipo">
                        <tr>
                            <td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
                                <?php
                                    $precio_total_periferico = $precio_periferico + $costeKits;
                                    echo '<span class="tituloComp">'.number_format($precio_total_periferico, 2, ',', '.').'€'.'</span>';
                                    $precio_todos_perifericos = $precio_todos_perifericos + $precio_total_periferico;
                                ?>
                            </td>
                        </tr>
                        </table>
                    </div>
                </div>
                <br/>
		<?php
			}
			if(count($ids_perifericos) == 0) { ?>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Referencias Periféricos</div>
                    <div class="tituloSinComponente"><?php echo "No hay periféricos";?></div>
                </div>
        <?php
			}
		?>
		<br />

        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Referencias Libres </div>
           	<div class="CajaReferencias">
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
							echo '<input type="hidden" id="ref_libres[]" name="ref_libres[]" value="'.$ref_libres[$i].'"/>';
							$ref->cargaDatosReferenciaLibreId($referencias_libres[$i]);
							echo '<input type="hidden" id="uds_paquete[]" name="uds_paquete[]" value="'.$ref->cantidad.'"/>';

							// ref->cantidad hace referencia a unidades/paquete de la referencia. Las referencias libres no estan insertadas en componentes referencias por lo que tendremos que obtener los campos de piezas
							// mediante post y total paquetes llamando a la funcion
							$r1= new Referencia();
							$r1->calculaTotalPaquetes($ref->cantidad,$piezas[$i]);
							$total_paquetes = $r1->total_paquetes;

							if($ref->pack_precio <> 0 and $ref->cantidad <> 0) {
								$precio_unidad = $ref->pack_precio / $ref->cantidad;
							}
							else $precio_unidad = 00;
							$precio_referencia = $piezas[$i] * $precio_unidad;
							$precio_refs_libres = $precio_refs_libres + $precio_referencia;

							echo '<input type="hidden" id="Piezas[]" name="Piezas[]" value="'.$piezas[$i].'"/>';
							echo '<input type="hidden" id="tot_paquetes[]" name="tot_paquetes[]" value="'.$total_paquetes.'"/>'; ?>
                            <tr>
                                <td style="text-align:center;"><?php echo $ref->id_referencia; ?></td>
                                <td id="enlaceComposites">
                                    <a href="../basicos/mod_referencia.php?id=<?php echo $ref->id_referencia;?>" target="_blank"/>
                                        <?php
                                            if (strlen($ref->referencia) > $max_caracteres_ref){
                                                echo substr($ref->referencia,0,$max_caracteres_ref).'...';
                                            }
                                            else echo $ref->referencia;
                                        ?>
                                    </a>
                                </td>
                                <td>
                                    <?php
                                        if (strlen($ref->proveedor) > $max_caracteres){
                                            echo substr($ref->proveedor,0,$max_caracteres).'...';
                                        }
                                        else echo $ref->proveedor;
                                    ?>
                                </td>
                                <td><?php $ref->vincularReferenciaProveedor(); ?></td>
                                <td>
                                    <?php
                                        if (strlen($ref->nombre_pieza) > $max_caracteres){
                                            echo substr($ref->nombre_pieza,0,$max_caracteres).'...';
                                        }
                                        else echo $ref->nombre_pieza;
                                    ?>
                                </td>
                                <td style="text-align:center"><?php echo number_format($piezas[$i], 2, ',', '.');?></td><td style="text-align:center"><?php echo number_format($ref->pack_precio, 2, ',', '.');?></td><td style="text-align:center"><?php echo $ref->cantidad; ?></td><td style="text-align:center"><?php echo $total_paquetes;?></td><td style="text-align:center"><?php echo number_format($precio_unidad, 2, ',', '.');?></td><td style="text-align:center"><?php echo number_format($precio_referencia, 2, ',', '.');?></td></tr>
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

        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Coste Total Producto</div>
            <div class="tituloComponente">
				<table id="tablaTituloPrototipo">
                <tr>
                	<td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
   						<?php
							$precio_total_producto = 0;
							$precio_total_producto = $precio_total_cabina + $precio_todos_perifericos + $precio_refs_libres;
							echo '<span class="tituloComp">'.number_format($precio_total_producto, 2, ',', '.').'€'.'</span>';
						?>
                    </td>
                </tr>
                </table>
            </div>
        </div>

        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Coste Total Orden de Producción</div>
            <div class="tituloComponente">
				<table id="tablaTituloPrototipo">
                <tr>
                	<td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
   						<?php
							$precio_total_op = 0;
							$precio_total_op = $precio_total_producto * $unidades;
							echo '<span class="tituloComp">'.number_format($precio_total_op, 2, ',', '.').'€'.'</span>';
						?>
                    </td>
                </tr>
                </table>
            </div>
        </div>
        <br />

        <div class="ContenedorBotonCreacionBasico">
        	<input type="button" id="volver" name="volver" value="Volver" onclick="javascript:location='nueva_orden_produccion.php?volver=true'"/>
            <input type="hidden" id="guardandoOrdenProduccion" name="guardandoOrdenProduccion" value="1"/>
			<?php
				if(!$fallo) {
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
<?php include ("../includes/footer.php");?>