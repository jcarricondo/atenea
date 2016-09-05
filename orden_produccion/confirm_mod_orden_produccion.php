<?php
// Paso final para la modificación de la Orden de Producción
set_time_limit(10000);
// Carga de clases y funciones JavaScript
include("../includes/sesion.php");
include("../classes/basicos/cabina.class.php");
include("../classes/basicos/periferico.class.php");
include("../classes/basicos/kit.class.php");
// include("../classes/basicos/software.class.php");
include("../classes/basicos/nombre_producto.class.php");
include("../classes/basicos/proveedor.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/basicos/referencia_componente.class.php");
include("../classes/basicos/listado_referencias_componentes.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/orden_produccion/incluir_referencia_libre.class.php");
include("../classes/orden_compra/orden_compra.class.php");
include("../classes/productos/producto.class.php");
include("../classes/control_usuario.class.php"); 
permiso(10);

$orden_produccion = new Orden_Produccion();
$producto = new Producto();
$cabina = new Cabina();
$periferico = new Periferico();
$Kit = new Kit();
// $soft = new Software();
$referencia_componente = new Referencia_Componente();
$ref_comp = new Referencia_Componente();
$proveedor = new Proveedor();
$nomb_prod = new Nombre_Producto();
$ref_modificada = new Referencia();
$control_usuario = new Control_Usuario;

$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
// Comprobamos si es Administrador Global
$esAdminGlobal = $control_usuario->esAdministradorGlobal($id_tipo_usuario);
// Comprobamos si es Administrador o Usuario de Gestion
$esAdminGes = $control_usuario->esAdministradorGes($id_tipo_usuario); 
$esUsuarioGes = $control_usuario->esUsuarioGes($id_tipo_usuario); 

// Se reciben los datos de Modificacion Orden de Produccion
$alias_op = $_POST["alias_op"];
$unidades = $_POST["unidades"];
$nombre_producto = $_POST["producto"];
$id_cabina = $_POST["cabina"]; 
$ids_perifericos = $_POST["perifericos"];
// $ids_softwares = $_POST["software"];
$referencias_libres = $_POST["REFS"];  
$id_nombre_producto = $_POST["id_nombre_producto"]; 
$id_produccion = $_GET["id_produccion"];
$id_producto = $_GET["id_producto"];
$cliente = $_POST["cliente"];
$ids_clientes = $_POST["ids_clientes"];
$piezas = $_POST["piezas"];

// Comprobamos el estado de la OP para obtener las referencias de los componentes
$orden_produccion->cargaDatosProduccionId($id_produccion);
$estado_op = $orden_produccion->estado;
$id_sede = $orden_produccion->id_sede;

if(isset($_POST["guardandoOrdenProduccion"]) and $_POST["guardandoOrdenProduccion"] == 1) {
	// Obtenemos los datos
	$id_cabina = $_POST["id_cabina"]; 
	$ids_perifericos = $_POST["IDS_PERS"];
	// $ids_softwares = $_POST["IDS_SOFT"];
	$ids_clientes = $_POST["ids_clientes"];
	$ref_libres = $_POST["ref_libres"];

	// CABINAS 
	// Comprobamos si se selecciono el checkbox para eliminar la cabina
	if ($_POST["eliminar_cabina"] != 1) {
		$referencias_cabina[] = $_POST["REFS_CAB"];
		$uds_paquete_cabina[] = $_POST["UDS_CAB"];
		$piezas_cabina[] = $_POST["piezas_cabina"];

		for ($i=0;$i<count($referencias_cabina[0]);$i++){
			$referencia_componente->calculaTotalPaquetes($uds_paquete_cabina[0][$i],$piezas_cabina[0][$i]);
			$total_paquetes_cabina[$i] = $referencia_componente->total_paquetes;
		}
		// Comprobamos si las referencias de la cabina estan duplicadas
		$tipo_componente=1;	
		include("referencias_duplicadas_CMOP.php");
	}
	else {
		$cabina = NULL;
		$id_cabina = NULL;
		$referencias_cabina[] = NULL;
		$uds_paquete_cabina[] = NULL;
		$piezas_cabina[]= NULL;	
		$total_paquetes_cabina = NULL;
	}

	// PERIFERICOS
	// Vaciamos el array de los ids perifericos.
	$ids_perifericos_aux = $ids_perifericos;
	$perifericos_nombres = $_POST["perifericos_nombres"];
	unset($ids_perifericos);
	$perifericos_finales = 0;

	// Guardamos en un array los perifericos finales de la modificacion de la orden de Produccion
	for ($i=0;$i<count($ids_perifericos_aux);$i++) {
		if ($_POST["eliminar_periferico-".$i] != 1) {
			$ids_perifericos[]=$ids_perifericos_aux[$i];
			$referencias_perifericos[] = $_POST["REFS_PER_".$i];
			$uds_paquete_perifericos[] = $_POST["UDS_PERS_".$i]; 
			$piezas_perifericos[] = $_POST["piezas_perifericos_".$i];
			$perifericos_finales++;
		}
		// Calculamos los paquetes totales de los perifericos que se mantienen
		for ($j=0;$j<$perifericos_finales;$j++){
			for ($k=0;$k<count($referencias_perifericos[$j]);$k++){
				$referencia_componente->calculaTotalPaquetes($uds_paquete_perifericos[$j][$k],$piezas_perifericos[$j][$k]);
				$total_paquetes_perifericos[$j][$k] = $referencia_componente->total_paquetes;
			}
			$tipo_componente = 2;
			include("referencias_duplicadas_CMOP.php");
		}
		// Reseteamos el array de perifericos y asignamos los nuevos array reagrupados
		unset($referencias_perifericos);
		unset($piezas_perifericos);
		unset($uds_paquete_perifericos);
		unset($total_paquetes_perifericos);
		$referencias_perifericos = $referencias_perifericos_aux;
		$piezas_perifericos = $piezas_perifericos_aux;
		$uds_paquete_perifericos = $uds_paquete_perifericos_aux;
		$total_paquetes_perifericos = $total_paquetes_perifericos_aux;
	}

	// REFERENCIAS LIBRES
	$referencias_libres = $_POST["REFS_LIBRES"];
	$uds_paquete_ref_libre = $_POST["UDS_REF_LIBRES"];	
	$Piezas_Ref_Libres = $_POST["piezas_ref_libres"];	
	for ($i=0;$i<count($referencias_libres);$i++){
		$referencia_componente->calculaTotalPaquetes($uds_paquete_ref_libre[$i],$Piezas_Ref_Libres[$i]);
		$total_paquetes_ref_libres[$i] = $referencia_componente->total_paquetes;
	}
	// Comprobamos si las referencias libres estan duplicadas
	$tipo_componente=0;	
	include("referencias_duplicadas_CMOP.php");

	// Obtenemos los ids de los productos asociados a esa Orden de Produccion
	$orden_produccion->dameIdsProductoOP($id_produccion);
	$ids_productos = $orden_produccion->ids_productos; 
	
	// Obtenemos el numero de serie de los productos ya que este no se va a modificar
	$orden_produccion->dameNumSerieProductos($id_produccion);
	$numeros_serie = $orden_produccion->num_serie;

	// Desactivamos los productos de la Orden de Produccion
	$resultado = $orden_produccion->desactivarProductos($id_produccion);
	if($resultado != 1) $fallo = true;

	// Desactivamos las referencias de los productos de la Orden de Produccion
	if(!$fallo) {
		$resultado = $orden_produccion->desactivarProductosReferencias($id_produccion);
		if($resultado != 1) $fallo = true;
	}

	// Desactivamos los componentes de los productos de la Orden de Produccion
	if(!$fallo) {
		$resultado = $orden_produccion->desactivarProductosComponentes($id_produccion);
		if($resultado != 1) $fallo = true;
	}

	// Desactivamos las ordenes de compra
	if(!$fallo){
		// Tenemos que desactivar primero todas las ordenes de compra de la antigua Orden de Produccion
		// Empezamos buscando las referencias de las ordenes de compra que se van a borrar. 
		$orden_produccion->dameOrdenCompraReferenciasABorrar($id_produccion);
		$referencias_ordenes_compra = $orden_produccion->referencias_orden_compra;
		$orden_compra = new Orden_Compra();
		for($i=0;$i<count($referencias_ordenes_compra);$i++){
			$orden_compra->desactivarOrden_Compra_ReferenciasId($referencias_ordenes_compra[$i]["id"]);	
		}
		// Ahora buscamos las facturas asociadas a las órdenes de compra y las desactivamos
		// Empezamos obteniendo los ids de compra de las orden de producción que se va a modificar
		$orden_produccion->dameIdsOrdenesCompra($id_produccion);
		// Guardamos en un array sencillo los ids de las órdenes de compra de la Orden de Producción
		for($i=0;$i<count($orden_produccion->ids_orden_compra);$i++) {
			$array_ids_oc[$i] = $orden_produccion->ids_orden_compra[$i]["id_orden_compra"];
		}
		// Desactivamos las facturas asociadas a las órdenes de compra que se van a desactivar
		for($i=0;$i<count($orden_produccion->ids_orden_compra);$i++) {
			$orden_compra->desactivarOrden_Compra_Facturas($array_ids_oc[$i]);
		}
		// Desactivamos los adjuntos asociados a las órdenes de compra que se van a desactivar
		for($i=0;$i<count($orden_produccion->ids_orden_compra);$i++) {
			$orden_compra->desactivarOrdenCompraAdjuntos($array_ids_oc[$i]);
		}
		// Desactivamos ahora las ordenes de compra
		$resultado = $orden_produccion->desactivarOrdenCompraPorIdProduccion($id_produccion);
		$fallo = $resultado != 1;
	}	

	// Guardamos los nuevos componentes
 	if(!$fallo){
		$contador_componente = 1;
		if($id_cabina != NULL and $id_cabina != 0 and $id_cabina != -1){
			$ids_componentes[] = $id_cabina;
			// Comprobamos si la cabina tiene kits
			$orden_produccion->dameIdsKitComponente($id_cabina);
			for($i=0;$i<count($orden_produccion->ids_kit);$i++){
				$ids_kit[] = $orden_produccion->ids_kit[$i]["id_kit"];
			}
			if($ids_kit != NULL){
				$ids_componentes = array_merge($ids_componentes,$ids_kit);
			}
		}	
		unset($ids_kit);
		if($ids_perifericos != NULL){
			for($i=0;$i<count($ids_perifericos);$i++){
				$ids_componentes[] = $ids_perifericos[$i];
				// Comprobamos si el periférico tiene kits
				$orden_produccion->dameIdsKitComponente($ids_perifericos[$i]);
				for($j=0;$j<count($orden_produccion->ids_kit);$j++){
					$ids_kit[] = $orden_produccion->ids_kit[$j]["id_kit"];
				}
				if($ids_kit != NULL){
					$ids_componentes = array_merge($ids_componentes,$ids_kit);
				}
				unset($ids_kit);
			}
		}
		/*
		if($ids_softwares != NULL){
			if($ids_componentes != NULL){
				$ids_componentes = array_merge($ids_componentes,$ids_softwares);
			}
			else {
				$ids_componentes = $ids_softwares;	
			}
		}
		*/

		$i=0;
		$error = false;
		$contador_periferico = 0;
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
					/*
					$soft->cargaDatosSoftwareId($ids_componentes[$i]);
					$num_serie_componente = "-";
					$resultado = $orden_produccion->guardarComponenteProduccion($id_produccion,$ids_componentes[$i],$num_serie_componente);		
					*/
					// Dejan de ezistir en Septiembre de 2016
				break;
				case '4':
					// INTERFAZ
					// Dejan de existir en Agosto de 2016
				break;
				case '5':
					// KIT
					$Kit->cargaDatosKitId($ids_componentes[$i]);
					$num_serie_componente = $Kit->referencia."_".$Kit->version."_".$id_produccion."_".$contador_componente;
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
				if($id_tipo["id_tipo"] == 1) {
					// CABINA
					for($j=0;$j<count($referencias_cabina[0]);$j++){
						$id_referencia = $referencias_cabina[0][$j];
						$uds_paquete = $uds_paquete_cabina[0][$j];
						$piezas = $piezas_cabina[0][$j];
						$total_paquetes = $total_paquetes_cabina[$j];
						$ref_modificada->cargaDatosReferenciaId($id_referencia);
						$pack_precio = $ref_modificada->pack_precio;

						$resultado = $orden_produccion->guardarReferenciasProduccion($id_produccion,$id_tipo["id_tipo"],$id_produccion_componente,$ids_componentes[$i],$id_referencia,$uds_paquete,$piezas,$total_paquetes,$pack_precio);		
						if ($resultado != 1){
							$j = count($referencias_componente);
							$error = true;
						}
					}
				}	
				else if($id_tipo["id_tipo"] == 2){
					// PERIFERICO
					for($j=0;$j<count($referencias_perifericos[$contador_periferico]);$j++){
						$id_referencia = $referencias_perifericos[$contador_periferico][$j];
						$uds_paquete = $uds_paquete_perifericos[$contador_periferico][$j];
						$piezas = $piezas_perifericos[$contador_periferico][$j];
						$total_paquetes = $total_paquetes_perifericos[$contador_periferico][$j];
						$ref_modificada->cargaDatosReferenciaId($id_referencia);
						$pack_precio = $ref_modificada->pack_precio;

						$resultado = $orden_produccion->guardarReferenciasProduccion($id_produccion,$id_tipo["id_tipo"],$id_produccion_componente,$ids_componentes[$i],$id_referencia,$uds_paquete,$piezas,$total_paquetes,$pack_precio);		
						if ($resultado != 1){
							$j = count($referencias_componente);
							$error = true;
						}
					}
					$contador_periferico++;
				}
				else {
					$ref_comp->dameReferenciasPorIdComponente($ids_componentes[$i]);
					$referencias_componente = $ref_comp->referencias_componente;
					for($j=0;$j<count($referencias_componente);$j++){
						$id_referencia = $referencias_componente[$j]["id_referencia"];
						$uds_paquete = $referencias_componente[$j]["uds_paquete"];
						$piezas = $referencias_componente[$j]["piezas"];
						// Calculamos el total_paquetes para la referencia
						$ref_modificada->calculaTotalPaquetes($uds_paquete,$piezas);
						$total_paquetes = $ref_modificada->total_paquetes;

						// Guardamos el pack_precio de la tabla referencias
						$ref_modificada->cargaDatosReferenciaId($id_referencia); 
						$pack_precio = $ref_modificada->pack_precio;

						$resultado = $orden_produccion->guardarReferenciasProduccion($id_produccion,$id_tipo["id_tipo"],$id_produccion_componente,$ids_componentes[$i],$id_referencia,$uds_paquete,$piezas,$total_paquetes,$pack_precio);		
						if ($resultado != 1){
							$j = count($referencias_componente);
							$error = true;
						}	
					}
				}		
			}
			$contador_componente++;
			$i++;
			$fallo = $error;
		}
	}

	if(!$fallo){
		// Guardamos las referencias libres
		$ref_libres = $referencias_libres;
		$uds_paquete = $uds_paquete_ref_libre;
		$Piezas = $Piezas_Ref_Libres; 
		$tot_paquetes = $total_paquetes_ref_libres;

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
			$uds_paquete = $uds_paquete_final;
			$tot_paquetes = $total_paquetes_final;

			$i=0;
			$error = false;
			while($i<count($ref_libres) and !$error){
				$ref_modificada->cargaDatosReferenciaId($ref_libres[$i]);
				$pack_precio = $ref_modificada->pack_precio;

				$resultado = $orden_produccion->guardarReferenciasProduccion($id_produccion,0,0,0,$ref_libres[$i],$uds_paquete[$i],$Piezas[$i],$tot_paquetes[$i],$pack_precio);		
				if ($resultado != 1){
					$error = true;
				}	
				$i++;
				$fallo = $error;
			}
		}
	}

	if(!$fallo){
		// Guardamos los productos
		$nomb_prod->cargaDatosNombreProductoId($id_nombre_producto);
		$nombre_producto_aux = $nomb_prod->nombre;
		$codigo_nombre_producto = $nomb_prod->codigo;

		$contador_producto = 150;
		$i=0;
		while($i<$unidades and !$fallo) {
			// Los numeros de serie del producto empiezan en 150
			$num_serie = $codigo_nombre_producto.'_'.$id_produccion.'_'.$contador_producto;
			$producto->datosNuevoProducto($id_producto,$id_produccion,$id_nombre_producto,$ids_clientes[$i],$num_serie);
			$resultado = $producto->guardarCambios();
			$fallo = $resultado != 1;
			$contador_producto++;
			$i++;
		}
		if($fallo){
			$mensaje_error = $producto->getErrorMessage($resultado);
		}
	}

	// Guardamos las ordenes de compra
	if(!$fallo){
		// INICIAR ORDENES DE COMPRA EN ESTADO GENERADA 
		// Tenemos que generar las Ordenes de compra en estado GENERADA. Para ello deberemos guardar:
		// en la tabla Orden de Compra los proveedores asociados a esa orden de producción.
		// en la tabla Orden de Compra referencias las referencias totales agrupadas por id_componente

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
			if (empty($ultimo_id)){
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

		if(!$fallo){
			// Ahora tenemos que hacer una consulta sobre las últimas órdenes de compra de los proveedores con activo = 0. 
			// Es decir las últimas órdenes de compra por cada proveedor de la orden de producción que fueron desactivadas
 			$orden_compra->dameUltimasOCDesactivadasPorProveedor($id_produccion);	
				
			for($i=0;$i<count($orden_compra->referencias_oc_desactivadas);$i++){
				$id_compra_desactivada = $orden_compra->referencias_oc_desactivadas[$i]["id_orden_compra"];
				$id_proveedor_desactivado = $orden_compra->referencias_oc_desactivadas[$i]["id_proveedor"];
				// Si de las últimas órdenes de compra desactivadas, coincide el proveedor con alguna de las nuevas órdenes de compra generadas, 
				// entonces copiamos las facturas que pudiera tener la última orden de compra desactivada de ese proveedor.
				if ($orden_compra->existeOCProveedor($id_produccion,$id_proveedor_desactivado)){
					// Buscamos el id_compra generado para poder copiar las facturas o actualizar las fechas y el estado en el caso de que de la orden 		
					// de compra desactivada no estuviese en estado GENERADA
					$orden_compra->dameIdOCPorProveedorDeUnaOP($id_produccion,$id_proveedor_desactivado);
					$id_nueva_oc = $orden_compra->id_compra[0]["id_orden_compra"];
						
					// Consulta a las referencias de la orden orden de compra desactivada para consultar
      				$orden_compra->repasarReferenciasRecibidas($id_compra_desactivada,$id_nueva_oc);
						
					// Ahora comprobamos si la orden de compra desactivada tenia facturas 
					if ($orden_compra->compruebaFacturasAntiguas($id_compra_desactivada)){
						// Copiamos todas las facturas de la oc desactivada en la nueva oc generada
						for($j=0;$j<count($orden_compra->facturas);$j++){
							$resultado = $orden_compra->copiarFacturasAntiguas($id_nueva_oc,$orden_compra->facturas[$j]);
							if ($resultado != 1){
								echo '<script type="text/javascript">alert("'.$orden_compra->getErrorMessage($resultado).'");</script>';
								break;
							} 
						}
					}
						
					// Ahora comprobamos el estado de la orden de compra desactivada. Si no esta en estado GENERADA copiamos las fechas y el estado
					$orden_compra->cargaDatosOrdenCompraId($id_compra_desactivada);
					if ($orden_compra->estado != "GENERADA"){
						$fecha_pedido_desactivada = $orden_compra->fecha_pedido;
						$fecha_entrega_desactivada = $orden_compra->fecha_entrega;
						$fecha_requerida = $orden_compra->fecha_requerida;
						$fecha_factura = $orden_compra->fecha_factura;
						$estado = $orden_compra->estado;
							
						$resultado = $orden_compra->actualizaOC($id_nueva_oc,$fecha_pedido_desactivada,$fecha_entrega_desactivada,$fecha_requerida,$fecha_factura,$estado);
						if ($resultado != 1) {
							$fallo = true;
							echo '<script type="text/javascript">alert("'.$orden_compra->getErrorMessage($resultado).'");</script>';
							break;
						}
					}
				}
			}

			if(!$fallo){
				// Guardamos las nuevas referencias de las Ordenes de Compra
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
			}


			if (!$fallo) {
				// Despues de realizar toda el proceso modificamos el alias de la Orden de Produccion
				// Si el alias es vacio el alias sera igual al codigo de la Orden de Produccion
				if ($alias_op == ""){
					$orden_produccion->cargaDatosProduccionId($id_produccion);
					$codigo = $orden_produccion->codigo;	
					$alias_op = $codigo;
				}
				// Actualizamos el alias de la Orden de Produccion. 
				$resultado = $orden_produccion->insertaAliasOrdenProduccion($id_produccion,$alias_op);	
				if ($resultado == 1){				
					header("Location: ordenes_produccion.php?OProduccion=modificado&sedes=".$id_sede);
				}
				else {
					echo '<script>alert("No se ha modificado el alias de la Orden de Produccion")</script>';	
				}
			}
			else {
				// Si se produce fallo desactivamos las Ordenes de Compra y las referencias de las Ordenes de Compra que se habian generado hasta el fallo
				$orden_produccion->dameOrdenCompraReferenciasABorrar($id_produccion);
				$referencias_ordenes_compra = $orden_produccion->referencias_orden_compra;
				for($i=0;$i<count($referencias_ordenes_compra);$i++){
					$orden_compra->desactivarOrden_Compra_ReferenciasId($referencias_ordenes_compra[$i]["id"]);	
				}
				// Desactivamos ahora las ordenes de compra
				$resultado_borrar_ordenes_compra = $orden_produccion->desactivarOrdenCompraPorIdProduccion($id_produccion);
				if ($resultado_borrar_ordenes_compra == 1) {
					$mensaje_error = $orden_compra->getErrorMessage($resultado);
				}
				else {
					$mensaje_error = $orden_compra->getErrorMessage($resultado_borrar_ordenes_compra);
				}
			}
		}
		else {
			$mensaje_error = $orden_compra->getErrorMessage($resultado);
		}
	}
	else{
		$mensaje_error = $orden_produccion->getErrorMessage($resultado);
	} 
}
$max_caracteres_ref = 50;
$max_caracteres = 20;
$titulo_pagina = "Órdenes de Producción > Confirmación Orden de Producción";
$pagina = "confirm_mod_orden_produccion";
include ('../includes/header.php');
echo '<script type="text/javascript" src="../js/orden_produccion/confirm_mod_orden_produccion.js"></script>';
?>

<div class="separador"></div> 
<?php include("../includes/menu_op.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar"><?php include ("../includes/sidebar.php");?></div>
    	<?php include ("muestra_contenido_conf_mod_op.php");?>
</div>    
<?php include ("../includes/footer.php"); ?>
