<?php
// Este fichero confirma y guarda la nueva Orden de Producción
set_time_limit(10000);
// Carga de clases y funciones JavaScript
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/periferico.class.php");
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

$control_usuario = new Control_Usuario();
$funciones = new Funciones();
$ref = new Referencia();
$ref_libre = new Referencia_Libre();
$ref_comp = new Referencia_Componente();
$listado_ref_comp = new listadoReferenciasComponentes();
$periferico = new Periferico();
$kit = new Kit();
$proveedor = new Proveedor();
$producto = new Producto();
$nombre_producto = new Nombre_Producto();
$orden_produccion = new Orden_Produccion();
$orden_compra = new Orden_Compra();
$plant = new Plantilla_Producto();

// Obtenemos el tipo de usuario para conocer su sede
$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];

$esAdminGlobal = $control_usuario->esAdministradorGlobal($id_tipo_usuario);
// Comprobamos si es Administrador o Usuario de Gestion
$esAdminGes = $control_usuario->esAdministradorGes($id_tipo_usuario);

// Se reciben los datos de Nueva Orden de Producción
$alias_op = $_POST["alias_op"];
$unidades = $_POST["unidades"];
$id_name_producto = $_POST["producto"];
$perifericos = $_POST["perifericos"];
$referencias_libres = $_POST["REFS"];
$piezas = $_POST["piezas"];
$fecha_inicio_construccion = $_POST["fecha_inicio_construccion"];
$sede = $_POST["sede"];

// Comprobamos si se escogió una plantilla
$id_plantilla = $_POST["select_plantilla"];
if(!empty($id_plantilla)){
    unset($perifericos);
    unset($referencias_libres);
    unset($piezas);

    // Obtenemos los componentes de la plantilla
    $res_perifericos = $plant->damePerifericosPlantillaProducto($id_plantilla);
	$res_kits_libres = $plant->dameKitsPlantillaProducto($id_plantilla);

    for($i=0;$i<count($res_perifericos);$i++) $perifericos[] = $res_perifericos[$i]["id_componente"];
	for($i=0;$i<count($res_kits_libres);$i++) $kits_libres[] = $res_kits_libres[$i]["id_componente"];
}

// Obtenemos la sede de la Orden de Producción
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
	if($fecha_actual > $ultima_fecha) $fecha = $fecha_actual;
	else $fecha = $ultima_fecha;

	// Convertimos la fecha de formato MySQL a formato dd/mm/YYYY
	$fecha = $funciones->cFechaNormal($fecha);
	// Convertimos la fecha a formato mm/dd/YYYY para poder sumarle 1 semana
	$fecha = $funciones->cFechaMyEsp($fecha);
	// Sumamos 1 semana a la última fecha de inicio de construcción de las OP iniciadas o a la fecha_actual según el caso 
	$fecha_inicio_construccion = date("m/d/Y", strtotime($fecha." +".$dias." days" ));
	// Convertimos la fecha a formato dd/mm/YYYY	
	$fecha_inicio_construccion = $funciones->cFechaMyEsp($fecha_inicio_construccion);
}

// Ordenamos los componentes
if(count($perifericos)!=0) sort($perifericos);
if(count($kits_libres)!=0) sort($kits_libres);
if(isset($_POST["guardandoOrdenProduccion"]) and $_POST["guardandoOrdenProduccion"] == 1) {
	// Guardar Nueva Orden Producción
	$id_nombre_producto = $_POST["id_nombre_producto"];
	$ids_perifericos = $_POST["IDS_PERS"];
	$ids_kits_libres = $_POST["IDS_KITS_LIBRES"];
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
				if($ids_perifericos != NULL){
					for($i=0;$i<count($ids_perifericos);$i++){
						$ids_componentes[] = $ids_perifericos[$i];
						$orden_produccion->dameIdsKitComponente($ids_perifericos[$i]);
						for($j=0;$j<count($orden_produccion->ids_kit);$j++){
							$ids_kit[] = $orden_produccion->ids_kit[$j]["id_kit"];
						}
						if($ids_kit != NULL) $ids_componentes = array_merge($ids_componentes,$ids_kit);
					    unset($ids_kit);
					}
				}

				if($ids_kits_libres != NULL) {
					for($i=0;$i<count($ids_kits_libres);$i++) $ids_componentes[] = $ids_kits_libres[$i];
				}

				$i=0;
				$error = false;
				while($i<count($ids_componentes) and !$error){
					$id_tipo = $orden_produccion->dameTipoComponente($ids_componentes[$i]);
					switch ($id_tipo["id_tipo"]) {
						case '1':
							// Dejan de existir en Septiembre de 2016
						break;
						case '2':
							// PERIFERICO
							$periferico->cargaDatosPerifericoId($ids_componentes[$i]);
							$num_serie_componente = $periferico->referencia."_".$periferico->version."_".$id_produccion."_".$contador_componente;
							$resultado = $orden_produccion->guardarComponenteProduccion($id_produccion,$ids_componentes[$i],$num_serie_componente);		
						break;
						case '3':
							// Dejan de existir en Septiembre de 2016
						break;
						case '4':
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
							$ref->calculaTotalPaquetes($uds_paquete,$piezas);
							$total_paquetes = $ref->total_paquetes;
							
							// Obtenemos el pack_precio de la tabla referencias 
							$ref->cargaDatosReferenciaId($id_referencia);
							$pack_precio = $ref->pack_precio;
	
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
							$ref->cargaDatosReferenciaId($ref_libres[$i]);
							$pack_precio = $ref->pack_precio;

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
							for ($i=0;$i<count($ids_proveedores);$i++) $array_ids_proveedores[$i] = $ids_proveedores[$i]["id_proveedor"];

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
    <form id="FormularioCreacionBasico" name="confirmNuevaOrdenProduccion" action="confirm_new_op.php" method="post">
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
    	<div class="LabelCreacionBasico">Perif&eacute;ricos</div>
        <?php
			$ids_perifericos = $perifericos;
			for($i=0;$i<count($perifericos);$i++){ ?>
				<input type="hidden" id="IDS_PERS[]" name="IDS_PERS[]" value="<?php echo $ids_perifericos[$i];?>" />
			<?php
				$periferico->cargaDatosPerifericoId($perifericos[$i]);
				$nombres_per[] = $periferico->periferico.'_v'.$periferico->version;
			}
		?>
        <textarea id="nombre_perifericos[]" name="nombre_perifericos[]" class="TextAreaOP" readonly="readonly" cols="1" rows="<?php echo count($perifericos);?>"><?php for($i=0;$i<count($nombres_per);$i++) echo $nombres_per[$i]."\n";?></textarea>
    </div>
	<br />

	<?php
		if(!empty($id_plantilla)){ ?>
			<div class="ContenedorCamposCreacionBasico">
				<div class="LabelCreacionBasico">Kits Libres</div>
				<?php
					for($i=0;$i<count($kits_libres);$i++){ ?>
						<input type="hidden" id="IDS_KITS_LIBRES[]" name="IDS_KITS_LIBRES[]" value="<?php echo $kits_libres[$i];?>" />
				<?php
					$kit->cargaDatosKitId($kits_libres[$i]);
					$nombres_kit[] = $kit->kit.'_v'.$kit->version;
				}
				?>
				<textarea id="nombre_kits_libres[]" name="nombre_kits_libres[]" class="TextAreaOP" readonly="readonly" cols="1" rows="<?php echo count($kits_libres);?>"><?php for($i=0;$i<count($nombres_kit);$i++) echo $nombres_kit[$i]."\n";?></textarea>
			</div>
			<br/>
	<?php
		}
	?>

    <?php
    	// Obtener el número de periféricos para generar las tablas de referencias correspondientes a ese periférico
        $precio_todos_perifericos = 0;
        for($i=0;$i<count($perifericos);$i++){
        	$precio_periferico = 0;
            $periferico->cargaDatosPerifericoId($perifericos[$i]);
            $id_componente = $perifericos[$i];
			include("../orden_produccion/confirm_new_op_muestra_perifericos.php");
			include("../orden_produccion/confirm_new_op_muestra_kits.php");?>

			<div class="ContenedorCamposCreacionBasico">
				<div class="LabelCreacionBasico">Coste Total Periferico</div>
				<div class="tituloComponente">
					<table id="tablaTituloPrototipo">
					<tr>
						<td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
						<?php
							$precio_total_periferico = $precio_periferico + $costeKits;
							$precio_todos_perifericos = $precio_todos_perifericos + $precio_total_periferico;
						?>
						<span class="tituloComp"><?php echo number_format($precio_total_periferico,2,',','.').'€';?></span>
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
            	<div class="LabelCreacionBasico">Referencias Perif&eacute;ricos</div>
                <div class="tituloSinComponente"><?php echo "No hay periféricos";?></div>
            </div>
    <?php
		}
		if(!empty($id_plantilla)) {
			// Mostramos los kits libres de la plantilla
			$precio_todos_kits_libres = 0;
			for($i=0;$i<count($kits_libres);$i++){
				$precio_kit_libre = 0;
				$kit->cargaDatosKitId($kits_libres[$i]);
				$id_componente = $kits_libres[$i];
				include("confirm_new_op_muestra_kits_libres.php");
			}
		}
	?>
	<br />
	<?php include("confirm_new_op_muestra_refs_libres.php");?>

    <div class="ContenedorCamposCreacionBasico">
    	<div class="LabelCreacionBasico">Coste Total Producto</div>
        <div class="tituloComponente">
			<table id="tablaTituloPrototipo">
            <tr>
            	<td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
   				<?php
					$precio_total_producto = 0;
					$precio_total_producto = $precio_todos_perifericos + $precio_todos_kits_libres + $precio_refs_libres;
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
    	<input type="button" id="volver" name="volver" value="Volver" onclick="location='new_op.php'"/>
        <input type="hidden" id="guardandoOrdenProduccion" name="guardandoOrdenProduccion" value="1"/>
		<?php if(!$fallo) echo '<input type="submit" id="continuar" name="continuar" value="Continuar" />'; ?>
    </div>
    <?php if($mensaje_error != "") echo '<div class="mensaje_error">'.$mensaje_error.'</div>'; ?>
    <br />
    </form>
</div>
<?php include ("../includes/footer.php");?>