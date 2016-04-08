<?php
// Clase utilizada para las Ordenes de Compra
class Orden_Compra extends MySQL {

	var $id_compra;
	var $id_produccion;
	var $id_proveedor;
	var $orden_compra;
	var $numero_pedido;
	var $fecha_pedido;
	var $fecha_entrega;
	var $fecha_entrega_BBDD;
	var $fecha_requerida;
	var $direccion_entrega;
	var $direccion_facturacion;
	var $fecha_factura;
	var $comentarios;
	var $estado;
	var $estado_anterior;
	var $precio;
	var $precio_tasas;
	var $fecha_creado;
	var $activo;
	var $porcentaje_recepcion = NULL;
	var $porcentaje_recepcion_decimal = NULL;

	var $id_orden_compra_referencia;
	var $nombre_prov;

	var $aux;
	var $ids_;
	var $nombres_factura;
	var $id_factura;
	var $tipo;
	var $bruto;
	var $neto;
	var $fecha_entrega_factura;
	var $adjunto;
	var $nombre_factura;
	var $neto_factura;

	var $tasas;
	var $total_paquetes;
	var $piezas_pedidas;
	var $piezas_recibidas;

	var $ids_referencias;
	var $referencias;
	var $referencias_oc_desactivadas;
	var $facturas;
	var $nombre_archivo_adjunto;
	var $ids_facturas;
	var $ids_adjuntos;

	var $id_adjuntos;
	var $nombre_adjunto;
	var $nombres_adjuntos;
	var $oc_no_modificadas;

	// Carga de datos de una orden de compra ya existente en la base de datos
	function cargarDatos($id_compra,$id_produccion,$id_proveedor,$orden_compra,$numero_pedido,$fecha_pedido,$fecha_requerida,$direccion_entrega,$direccion_facturacion,$fecha_factura,$comentarios,$estado,$tasas,$fecha_creado,$activo,$nombre_prov,$fecha_entrega) {
		$this->id_compra = $id_compra;
		$this->id_produccion = $id_produccion;
		$this->id_proveedor = $id_proveedor;
		$this->orden_compra = $orden_compra;
		$this->numero_pedido = $numero_pedido;
		$this->fecha_pedido = $fecha_pedido;
		$this->fecha_entrega = $fecha_entrega;
		$this->fecha_requerida = $fecha_requerida;
		$this->direccion_entrega = $direccion_entrega;
		$this->direccion_facturacion = $direccion_facturacion;
		$this->fecha_factura = $fecha_factura;
		$this->comentarios = $comentarios;
		$this->estado = $estado;
		$this->fecha_creado = $fecha_creado;
		$this->activo = $activo;
		$this->nombre_prov = $nombre_prov;
		$this->tasas = $tasas;
	}

	// Se obtienen los datos de la orden de compra en base a su ID
	function cargaDatosOrdenCompraId($id_compra) {
		$consultaSql = sprintf("select orden_compra.*,proveedores.nombre_prov from orden_compra inner join proveedores on (proveedores.id_proveedor=orden_compra.id_proveedor) where id_orden_compra=%s",
			$this->makeValue($id_compra, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id_orden_compra"],
			$resultados["id_produccion"],
			$resultados["id_proveedor"],
			$resultados["orden_compra"],
			$resultados["numero_pedido"],
			$resultados["fecha_pedido"],
			$resultados["fecha_requerida"],
			$resultados["direccion_entrega"],
			$resultados["direccion_facturacion"],
			$resultados["fecha_factura"],
			$resultados["comentarios"],
			$resultados["estado"],
			$resultados["tasas"],
			$resultados["fecha_creado"],
			$resultados["activo"],
			$resultados["nombre_prov"],
			$resultados["fecha_entrega"]
		);
	}

	// Carga los datos de las facturas de una orden de compra existentes en la base de datos
	function cargarDatosFacturasOC($id_factura,$id_compra,$id_proveedor,$nombre_factura,$tipo,$bruto,$neto,$fecha_entrega_factura,$comentarios,$adjunto) {
		$this->id_factura = $id_factura;
		$this->id_compra = $id_compra;
		$this->id_proveedor = $id_proveedor;
		$this->nombre_factura = $nombre_factura;
		$this->tipo =  $tipo;
		$this->bruto = $bruto;
		$this->neto = $neto;
		$this->fecha_entrega_factura = $fecha_entrega_factura;
		$this->comentarios = $comentarios;
		$this->adjunto = $adjunto;
	}

	// Se obtienen los datos de las facturas en base a su ID
	function cargaDatosFacturasId($id) {
		$consultaSql = sprintf("select * from orden_compra_facturas where id_factura=%s ",
			$this->makeValue($id, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatosFacturasOC(
			$resultados["id_factura"],
			$resultados["id_orden"],
			$resultados["id_proveedor"],
			$resultados["nombre_factura"],
			$resultados["tipo"],
			$resultados["bruto"],
			$resultados["neto"],
			$resultados["fecha_entrega"],
			$resultados["comentarios"],
			$resultados["adjunto"]);
	}

	// Carga los datos de los archivos adjuntos de una orden de compra existentes en la base de datos
	function cargarDatosAdjuntosOC($id_adjuntos,$id_compra,$id_proveedor,$nombre_adjunto,$comentarios) {
		$this->id_adjuntos = $id_adjuntos;
		$this->id_compra = $id_compra;
		$this->id_proveedor = $id_proveedor;
		$this->nombre_adjunto = $nombre_adjunto;
		$this->comentarios = $comentarios;
	}

	// Se obtienen los datos de los archivos_adjuntos en base a su ID
	function cargaDatosAdjuntosId($id) {
		$consultaSql = sprintf("select * from orden_compra_adjuntos where id_adjuntos=%s ",
			$this->makeValue($id, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatosAdjuntosOC(
			$resultados["id_adjuntos"],
			$resultados["id_orden"],
			$resultados["id_proveedor"],
			$resultados["nombre_adjunto"],
			$resultados["comentarios"]);
	}

	// Se hace la carga de datos de la nueva orden de compra
	function datosNuevaCompra($id_compra = NULL, $id_produccion, $id_proveedor, $numero_pedido, $fecha_pedido, $fecha_requerida, $direccion_entrega, $direccion_facturacion, $fecha_factura, $comentarios,$estado_anterior, $estado, $tasas, $unidades,$fecha_entrega,$orden_compra) {
 		$this->id_compra = $id_compra;
		$this->id_produccion = $id_produccion;
		$this->id_proveedor = $id_proveedor;
		$this->numero_pedido = $numero_pedido;
		$this->fecha_pedido = $fecha_pedido;
		$this->fecha_entrega = $fecha_entrega;
		$this->fecha_requerida = $fecha_requerida;
		$this->direccion_entrega = $direccion_entrega;
		$this->direccion_facturacion = $direccion_facturacion;
		$this->fecha_factura = $fecha_factura;
		$this->comentarios = $comentarios;
		$this->estado_anterior = $estado_anterior;
		$this->estado = $estado;
		$this->tasas = $tasas;
		$this->unidades = $unidades;
		$this->orden_compra = $orden_compra;
	}

	function setPrecios($precio,$precio_tasas) {
		$this->precio = $precio;
		$this->precio_tasas = $precio_tasas;
	}

	function setFacturas($nombre_archivo,$neto_factura) {
		$this->nombre_factura = $nombre_archivo;
		$this->neto_factura = $neto_factura;
	}

	function setArchivosAdjuntos($nombre_archivo_adjunto){
		$this->nombre_archivo_adjunto = $nombre_archivo_adjunto;
	}

	// Guarda los cambios realizados en la orden de compra
 	function guardarCambios() {
  		// Si el id_compra es NULL lo toma como una nueva orden de compra y guarda los datos de la orden de compra en la base de datos.
  		if($this->id_compra == NULL) {
   			$consulta = sprintf("insert into orden_compra(id_produccion,id_proveedor,orden_compra,numero_pedido,fecha_pedido,fecha_requerida,fecha_factura,estado,tasas,fecha_creado,direccion_entrega,direccion_facturacion,activo) value (%s,%s,%s,%s,%s,current_timestamp,current_timestamp,%s,%s,current_timestamp,%s,%s,1)",
	    		$this->makeValue($this->id_produccion, "int"),
	    		$this->makeValue($this->id_proveedor, "int"),
			    $this->makeValue($this->orden_compra, "text"),
			    $this->makeValue($this->numero_pedido, "text"),
			    $this->makeValue($this->fecha_pedido, "date"),
			    $this->makeValue($this->estado, "text"),
			    $this->makeValue($this->tasas, "float"),
			    $this->makeValue($this->direccion_entrega, "text"),
			    $this->makeValue($this->direccion_facturacion, "text"));
		   $this->setConsulta($consulta);
		   if($this->ejecutarSoloConsulta()) {
		   		return 1;
		   } 
		   else {
		    	return 2;
		   }
		}
		// Modificación de la Orden de Compra / Modificación de varias órdenes de compra desde el listado.
		else {
			$consulta = "";
			switch($this->estado_anterior) {
				case "GENERADA":
					switch($this->estado) {
						case "GENERADA":
							$consulta = sprintf("update orden_compra set fecha_pedido=%s, direccion_entrega=%s, direccion_facturacion=%s, fecha_factura=current_timestamp, estado=%s, tasas=%s where id_orden_compra=%s",
								$this->makeValue($this->fecha_pedido, "date"),
								$this->makeValue($this->direccion_entrega, "text"),
								$this->makeValue($this->direccion_facturacion, "text"),
								$this->makeValue($this->estado, "text"),
								$this->makeValue($this->tasas, "float"),
								$this->makeValue($this->id_compra, "int"));
						break;
						case "PEDIDO INICIADO":
							$consulta = sprintf("update orden_compra set fecha_pedido=current_timestamp, direccion_entrega=%s, direccion_facturacion=%s, fecha_factura=current_timestamp, estado=%s, tasas=%s where id_orden_compra=%s",
								$this->makeValue($this->direccion_entrega, "text"),
								$this->makeValue($this->direccion_facturacion, "text"),
								$this->makeValue($this->estado, "text"),
								$this->makeValue($this->tasas, "float"),
								$this->makeValue($this->id_compra, "int"));
						break;
						case "PEDIDO CERRADO":
							$consulta = sprintf("update orden_compra set fecha_pedido=%s, direccion_entrega=%s, direccion_facturacion=%s, fecha_factura=current_timestamp, estado=%s, tasas=%s where id_orden_compra=%s",
								$this->makeValue($this->fecha_pedido, "date"),
								$this->makeValue($this->direccion_entrega, "text"),
								$this->makeValue($this->direccion_facturacion, "text"),
								$this->makeValue($this->estado, "text"),
								$this->makeValue($this->tasas, "float"),
								$this->makeValue($this->id_compra, "int"));
						break;
						case "PARCIALMENTE RECIBIDO":
							// El usuario no puede cambiar a este estado
						break;
						case "RECIBIDO":
							// El usuario no puede cambiar a este estado
						break;
						case "STOCK":
							$consulta = sprintf("update orden_compra set fecha_pedido=%s, direccion_entrega=%s, direccion_facturacion=%s, fecha_factura=current_timestamp, estado=%s, tasas=%s where id_orden_compra=%s",
								$this->makeValue($this->fecha_pedido, "date"),
								$this->makeValue($this->direccion_entrega, "text"),
								$this->makeValue($this->direccion_facturacion, "text"),
								$this->makeValue($this->estado, "text"),
								$this->makeValue($this->tasas, "float"),
								$this->makeValue($this->id_compra, "int"));
							$this->ordenCompraStock();
						break;
					}
				break;
				case "PEDIDO INICIADO":
					switch($this->estado) {
						case "GENERADA":
							$consulta = sprintf("update orden_compra set fecha_pedido=%s, direccion_entrega=%s, direccion_facturacion=%s, fecha_factura=current_timestamp, estado=%s, tasas=%s where id_orden_compra=%s",
								$this->makeValue($this->fecha_pedido, "date"),
								$this->makeValue($this->direccion_entrega, "text"),
								$this->makeValue($this->direccion_facturacion, "text"),
								$this->makeValue($this->estado, "text"),
								$this->makeValue($this->tasas, "float"),
								$this->makeValue($this->id_compra, "int"));
						break;
						case "PEDIDO INICIADO":
							$consulta = sprintf("update orden_compra set fecha_pedido=%s, direccion_entrega=%s, direccion_facturacion=%s, fecha_factura=current_timestamp, estado=%s, tasas=%s where id_orden_compra=%s",
								$this->makeValue($this->fecha_pedido, "date"),
								$this->makeValue($this->direccion_entrega, "text"),
								$this->makeValue($this->direccion_facturacion, "text"),
								$this->makeValue($this->estado, "text"),
								$this->makeValue($this->tasas, "float"),
								$this->makeValue($this->id_compra, "int"));
						break;
						case "PEDIDO CERRADO":
							$consulta = sprintf("update orden_compra set fecha_pedido=%s, direccion_entrega=%s, direccion_facturacion=%s, fecha_factura=current_timestamp, estado=%s, tasas=%s where id_orden_compra=%s",
								$this->makeValue($this->fecha_pedido, "date"),
								$this->makeValue($this->direccion_entrega, "text"),
								$this->makeValue($this->direccion_facturacion, "text"),
								$this->makeValue($this->estado, "text"),
								$this->makeValue($this->tasas, "float"),
								$this->makeValue($this->id_compra, "int"));
						break;
						case "PARCIALMENTE RECIBIDO":
							// El usuario no puede cambiar a este estado
						break;
						case "RECIBIDO":
							// El usuario no puede cambiar a este estado
						break;
						case "STOCK":
							$consulta = sprintf("update orden_compra set fecha_pedido=%s, direccion_entrega=%s, direccion_facturacion=%s, fecha_factura=current_timestamp, estado=%s, tasas=%s where id_orden_compra=%s",
								$this->makeValue($this->fecha_pedido, "date"),
								$this->makeValue($this->direccion_entrega, "text"),
								$this->makeValue($this->direccion_facturacion, "text"),
								$this->makeValue($this->estado, "text"),
								$this->makeValue($this->tasas, "float"),
								$this->makeValue($this->id_compra, "int"));
							$this->ordenCompraStock();
						break;
					}
				break;
				case "PEDIDO CERRADO":
					switch($this->estado) {
						case "GENERADA":
							$consulta = sprintf("update orden_compra set fecha_pedido=%s, direccion_entrega=%s, direccion_facturacion=%s, fecha_factura=current_timestamp, estado=%s, tasas=%s where id_orden_compra=%s",
								$this->makeValue($this->fecha_pedido, "date"),
								$this->makeValue($this->direccion_entrega, "text"),
								$this->makeValue($this->direccion_facturacion, "text"),
								$this->makeValue($this->estado, "text"),
								$this->makeValue($this->tasas, "float"),
								$this->makeValue($this->id_compra, "int"));
						break;
						case "PEDIDO INICIADO":
							$consulta = sprintf("update orden_compra set fecha_pedido=%s, direccion_entrega=%s, direccion_facturacion=%s, fecha_factura=current_timestamp, estado=%s, tasas=%s where id_orden_compra=%s",
								$this->makeValue($this->fecha_pedido, "date"),
								$this->makeValue($this->direccion_entrega, "text"),
								$this->makeValue($this->direccion_facturacion, "text"),
								$this->makeValue($this->estado, "text"),
								$this->makeValue($this->tasas, "float"),
								$this->makeValue($this->id_compra, "int"));
						break;
						case "PEDIDO CERRADO":
							$consulta = sprintf("update orden_compra set fecha_pedido=%s, direccion_entrega=%s, direccion_facturacion=%s, fecha_factura=current_timestamp, estado=%s, tasas=%s where id_orden_compra=%s",
								$this->makeValue($this->fecha_pedido, "date"),
								$this->makeValue($this->direccion_entrega, "text"),
								$this->makeValue($this->direccion_facturacion, "text"),
								$this->makeValue($this->estado, "text"),
								$this->makeValue($this->tasas, "float"),
								$this->makeValue($this->id_compra, "int"));
						break;
						case "PARCIALMENTE RECIBIDO":
							// El usuario no puede cambiar a este estado
						break;
						case "RECIBIDO":
							// El usuario no puede cambiar a este estado
						break;
						case "STOCK":
							$consulta = sprintf("update orden_compra set fecha_pedido=%s, direccion_entrega=%s, direccion_facturacion=%s, fecha_factura=current_timestamp, estado=%s, tasas=%s where id_orden_compra=%s",
								$this->makeValue($this->fecha_pedido, "date"),
								$this->makeValue($this->direccion_entrega, "text"),
								$this->makeValue($this->direccion_facturacion, "text"),
								$this->makeValue($this->estado, "text"),
								$this->makeValue($this->tasas, "float"),
								$this->makeValue($this->id_compra, "int"));
							$this->ordenCompraStock();
						break;
					}
				break;
				/* El usuario no puede cambiar desde este estado
				case "PARCIALMENTE RECIBIDO"
					switch($this->estado) {
						case "GENERADA":

						break;
						case "PEDIDO INICIADO":

						break;
						case "PEDIDO CERRADO":

						break;
						case "PARCIALMENTE RECIBIDO":
							// El usuario no puede cambiar a este estado
						break;
						case "RECIBIDO":
							// El usuario no puede cambiar a este estado
						break;
						case "STOCK":

						break;
					}
				break;
				*/
				/* El usuario no puede cambiar desde este estado
				case "RECIBIDO":
					switch($this->estado) {
						case "GENERADA":

						break;
						case "PEDIDO INICIADO":

						break;
						case "PEDIDO CERRADO":

						break;
						case "PARCIALMENTE RECIBIDO":
							// El usuario no puede cambiar a este estado
						break;
						case "RECIBIDO":
							// El usuario no puede cambiar a este estado
						break;
						case "STOCK":

						break;
					}
				break;
				*/
				case "STOCK":
					switch($this->estado) {
						case "GENERADA":
							$consulta = sprintf("update orden_compra set fecha_pedido=%s, direccion_entrega=%s, direccion_facturacion=%s, fecha_factura=current_timestamp, estado=%s, tasas=%s where id_orden_compra=%s",
								$this->makeValue($this->fecha_pedido, "date"),
								$this->makeValue($this->direccion_entrega, "text"),
								$this->makeValue($this->direccion_facturacion, "text"),
								$this->makeValue($this->estado, "text"),
								$this->makeValue($this->tasas, "float"),
								$this->makeValue($this->id_compra, "int"));
							$this->ordenCompraStockFuera();
						break;
						case "PEDIDO INICIADO":
							$consulta = sprintf("update orden_compra set fecha_pedido=%s, direccion_entrega=%s, direccion_facturacion=%s, fecha_factura=current_timestamp, estado=%s, tasas=%s where id_orden_compra=%s",
								$this->makeValue($this->fecha_pedido, "date"),
								$this->makeValue($this->direccion_entrega, "text"),
								$this->makeValue($this->direccion_facturacion, "text"),
								$this->makeValue($this->estado, "text"),
								$this->makeValue($this->tasas, "float"),
								$this->makeValue($this->id_compra, "int"));
							$this->ordenCompraStockFuera();
						break;
						case "PEDIDO CERRADO":
							$consulta = sprintf("update orden_compra set fecha_pedido=%s, direccion_entrega=%s, direccion_facturacion=%s, fecha_factura=current_timestamp, estado=%s, tasas=%s where id_orden_compra=%s",
								$this->makeValue($this->fecha_pedido, "date"),
								$this->makeValue($this->direccion_entrega, "text"),
								$this->makeValue($this->direccion_facturacion, "text"),
								$this->makeValue($this->estado, "text"),
								$this->makeValue($this->tasas, "float"),
								$this->makeValue($this->id_compra, "int"));
							$this->ordenCompraStockFuera();
						break;
						case "PARCIALMENTE RECIBIDO":
							// El usuario no puede cambiar a este estado
						break;
						case "RECIBIDO":
							// El usuario no puede cambiar a este estado
						break;
						case "STOCK":
							$consulta = sprintf("update orden_compra set fecha_pedido=%s, direccion_entrega=%s, direccion_facturacion=%s, fecha_factura=current_timestamp, estado=%s, tasas=%s where id_orden_compra=%s",
								$this->makeValue($this->fecha_pedido, "date"),
								$this->makeValue($this->direccion_entrega, "text"),
								$this->makeValue($this->direccion_facturacion, "text"),
								$this->makeValue($this->estado, "text"),
								$this->makeValue($this->tasas, "float"),
								$this->makeValue($this->id_compra, "int"));
						break;
					}
				break;
			}
			if ($consulta != ""){
				$this->setConsulta($consulta);
				if($this->ejecutarSoloConsulta()) {
					// Insertamos las facturas en la BBDD
					$i=0;
					$fallo = false;
					while ($i<count($this->nombre_factura) and (!$fallo)) {
						if (!empty($this->nombre_factura[$i])) {
							$consulta_archivos = sprintf("insert into orden_compra_facturas (id_orden, id_proveedor, nombre_factura, neto, fecha_entrega, activo) value (%s,%s,%s,%s,current_timestamp,1)",
    							$this->makeValue($this->id_compra, "int"),
								$this->makeValue($this->id_proveedor, "int"),
    							$this->makeValue($this->nombre_factura[$i], "text"),
								$this->makeValue($this->neto_factura[$i],"float"));
							$this->setConsulta($consulta_archivos);
							if (!$this->ejecutarSoloConsulta()) $fallo = true;
						}
						$i++;
					}
					if (!$fallo) {
					// Insertamos los archivos adjuntos
						$i=0;
						$fallo = false;
						while ($i<count($this->nombre_archivo_adjunto) and (!$fallo)) {
							if (!empty($this->nombre_archivo_adjunto[$i])) {
								$consulta_archivos = sprintf("insert into orden_compra_adjuntos (id_orden, id_proveedor, nombre_adjunto, fecha_creado, activo) value (%s,%s,%s,current_timestamp,1)",
    								$this->makeValue($this->id_compra, "int"),
									$this->makeValue($this->id_proveedor, "int"),
    								$this->makeValue($this->nombre_archivo_adjunto[$i], "text"));
								$this->setConsulta($consulta_archivos);
								if (!$this->ejecutarSoloConsulta()) $fallo = true;
							}
							$i++;
						}
						if (!$fallo) {
							// MODIFICACION ORDEN DE COMPRA OK
							return 1;
						}
						else {
							return 17;
						}
					}
					else {
						// Fallo al guardar las facturas en la BBDD
						return 13;
					}
				}
				else {
					return 3;
				}
			}
			return 1;
		}
	}

	// Funcion que guarda las Referencias de una orden de compra
	function guardarReferenciasOC($id_compra,$id_referencia,$uds_paquete,$piezas,$total_piezas,$total_packs,$pack_precio,$coste){
		$consulta = sprintf("insert into orden_compra_referencias (id_orden,id_referencia,uds_paquete,piezas,total_piezas,piezas_recibidas,total_packs,pack_precio,coste,fecha_creado,activo) value(%s,%s,%s,%s,%s,0,%s,%s,%s,current_timestamp,1)",
			$this->makeValue($id_compra, "int"),
			$this->makeValue($id_referencia, "int"),
			$this->makeValue($uds_paquete, "int"),
			$this->makeValue($piezas, "float"),
			$this->makeValue($total_piezas, "float"),
			$this->makeValue($total_packs, "int"),
			$this->makeValue($pack_precio, "float"),
			$this->makeValue($coste, "float"));
		$this->setConsulta($consulta);
		if ($this->ejecutarSoloConsulta()) {
			return 1;
		}
		else return 6;
	}


	function ordenCompraStock() {
		$consultaSql = sprintf("select id,id_orden,id_referencia,total_piezas,piezas_recibidas,(total_piezas-piezas_recibidas) as total_descontar from orden_compra_referencias where id_orden=%s and activo=1",
			$this->makeValue($this->id_compra, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$datos = $this->getResultados();
		for($i=0;$i<count($datos);$i++){
			$id_referencia = $datos[$i]["id_referencia"];
			$total_descontar = $datos[$i]["total_descontar"];
			$piezas_restantes = 0;
			// Se consulta si hay piezas en stock de esa referencia
			$consultaReferencia = sprintf("select id_referencia,piezas from stock_almacenes where id_referencia=%s",
				$this->makeValue($id_referencia, "int"));
			$this->setConsulta($consultaReferencia);
			$this->ejecutarConsulta();
			$datosStock = $this->getResultados();
			$piezas_stock = $datosStock[0]["piezas"];
			$piezas_restantes = $piezas_stock - $total_descontar;
			$updateSql = "";
			if($piezas_restantes <= 0) {
				$updateSql = sprintf("delete from stock_almacenes where id_referencia=%s",
					$this->makeValue($id_referencia, "int"));
			} else {
				$updateSql = sprintf("update stock_almacenes set piezas=%s where id_referencia=%s",
					$this->makeValue($piezas_restantes, "float"),
					$this->makeValue($id_referencia, "int"));
			}
			if($updateSql != "") {
				$this->setConsulta($updateSql);
				$this->ejecutarSoloConsulta();
			}
			$updateCompraSql = sprintf("update orden_compra_referencias set piezas_recibidas=total_piezas where id=%s",
				$this->makeValue($datos[$i]["id"], "int"));
			$this->setConsulta($updateCompraSql);
			$this->ejecutarSoloConsulta();
		}
	}

	function ordenCompraStockFuera() {
		$consultaSql = sprintf("select id,id_orden,id_referencia,total_piezas,piezas_recibidas from orden_compra_referencias where id_orden=%s and activo=1",
			$this->makeValue($this->id_compra, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$datos = $this->getResultados();
		for($i=0;$i<count($datos);$i++){
			$id_referencia = $datos[$i]["id_referencia"];
			$piezas_pedidas = $datos[$i]["total_piezas"];
			$piezas_restantes = 0;
			// Se consulta si hay piezas en stock de esa referencia
			$consultaReferencia = sprintf("select id_referencia,piezas from stock_almacenes where id_referencia=%s",
				$this->makeValue($id_referencia, "int"));
			$this->setConsulta($consultaReferencia);
			$this->ejecutarSoloConsulta();
			if($this->getNumeroFilas() > 0) {
				$updateSql = sprintf("update stock_almacenes set piezas=piezas+".$piezas_pedidas." where id_referencia=%s",
					$this->makeValue($id_referencia, "int"));
			} else {
				$updateSql = sprintf("insert into stock_almacenes (id_referencia,piezas) values (%s,%s)",
					$this->makeValue($id_referencia, "int"),
					$this->makeValue($piezas_pedidas, "float"));
			}
			if($updateSql != "") {
				$this->setConsulta($updateSql);
				$this->ejecutarSoloConsulta();
			}
			$updateCompraSql = sprintf("update orden_compra_referencias set piezas_recibidas=0 where id=%s",
				$this->makeValue($datos[$i]["id"], "int"));
			$this->setConsulta($updateCompraSql);
			$this->ejecutarSoloConsulta();
		}
	}

	// Función para eliminar una orden de compra
	function eliminar($id_compra){
		$consulta = sprintf("update orden_compra set activo=0 where id_orden_compra=%s",
			$this->makeValue($id_compra, "int"));
			$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 5;
		} else {
			return 4;
		}
	}

	// Función para desactivar las referencias de una orden de compra segun el id_compra
	function desactivarOrden_Compra_Referencias($id_compra) {
		$consulta = sprintf("update orden_compra_referencias set activo=0 where id_orden=%s",
			$this->makeValue($id_compra, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) return 1;
		else return 9;
	}

	// Función para desactivar las referencias de una orden de compra segun el id de referencia de la orden de compra
	function desactivarOrden_Compra_ReferenciasId($id) {
		$consulta = sprintf("update orden_compra_referencias set activo=0 where id=%s",
			$this->makeValue($id, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) return 1;
		else return 9;
	}

	// Función para desactivar las facturas asociadas a una orden de compra
	function desactivarOrden_Compra_Facturas($id_compra) {
		$consulta = sprintf("update orden_compra_facturas set activo=0 where id_orden=%s",
			$this->makeValue($id_compra, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) return 1;
		else return 10;
	}

	// Función para desactivar los archivos adjuntos asociados a una orden de compra
	function desactivarOrdenCompraAdjuntos($id_compra) {
		$consulta = sprintf("update orden_compra_adjuntos set activo=0 where id_orden=%s",
			$this->makeValue($id_compra, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) return 1;
		else return 10;
	}

	// Función para obtener el coste total de una orden de compra en función de las referencias del proveedor
	function damePrecioOC($id_compra,$proveedor) {
		$consulta = sprintf("select sum(coste) as precio from orden_compra_referencias inner join referencias on (referencias.id_referencia = orden_compra_referencias.id_referencia) inner join proveedores on (proveedores.id_proveedor = referencias.id_proveedor) where proveedores.id_proveedor=%s and id_orden=%s and orden_compra_referencias.activo=1",
			$this->makeValue($proveedor, "int"),
			$this->makeValue($id_compra, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->precio = $this->getResultados();
	}

	// Función para obtener los ids_facturas de una orden de compra asociadas a un proveedor
	function dameIds_factura($id_compra,$proveedor) {
		$consulta = sprintf("select id_factura from orden_compra_facturas where orden_compra_facturas.id_orden=%s and orden_compra_facturas.id_proveedor=%s and orden_compra_facturas.activo=1 ",
			$this->makeValue($id_compra, "int"),
			$this->makeValue($proveedor, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->ids_facturas = $this->getResultados();
	}

	// Función para obtener los nombres de las facturas de una orden de compra asociadas a un proveedor
	function dameNombres_Facturas($id_compra,$proveedor) {
		$consulta = sprintf("select nombre_factura from orden_compra_facturas where orden_compra_facturas.id_orden=%s and orden_compra_facturas.id_proveedor=%s and orden_compra_facturas.activo=1 ",
			$this->makeValue($id_compra, "int"),
			$this->makeValue($proveedor, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->nombres_factura = $this->getResultados();
	}

	// Función para obtener los ids_archivos_adjuntos de una orden de compra asociadas a un proveedor
	function dameIdsArchivosAdjuntos($id_compra,$proveedor) {
		$consulta = sprintf("select id_adjuntos from orden_compra_adjuntos where orden_compra_adjuntos.id_orden=%s and orden_compra_adjuntos.id_proveedor=%s and orden_compra_adjuntos.activo=1 ",
			$this->makeValue($id_compra, "int"),
			$this->makeValue($proveedor, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->ids_adjuntos = $this->getResultados();
	}

	// Función para obtener los nombres de los archivos adjuntos de una orden de compra asociadas a un proveedor
	function dameNombresAdjuntos($id_compra,$proveedor) {
		$consulta = sprintf("select nombre_adjunto from orden_compra_adjuntos where orden_compra_adjuntos.id_orden=%s and orden_compra_adjuntos.id_proveedor=%s and orden_compra_adjuntos.activo=1 ",
			$this->makeValue($id_compra, "int"),
			$this->makeValue($proveedor, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->nombres_adjuntos = $this->getResultados();
	}

	// Función para desactivar las facturas asociadas a una orden de compra
	function quitarArchivo($nombre_factura,$id_compra) {
		$consulta = sprintf("update orden_compra_facturas set activo=0 where orden_compra_facturas.id_orden=%s and orden_compra_facturas.nombre_factura=%s ",
			$this->makeValue($id_compra, "int"),
			$this->makeValue($nombre_factura, "text"));
		$this->setConsulta($consulta);
		if ($this->ejecutarSoloConsulta()) {
			return 1;
		}
		else return 11;
	}

	// Función para desactivar los adjuntos asociados a una orden de compra
	function quitarArchivoAdjuntos($nombre_adjunto,$id_compra) {
		$consulta = sprintf("update orden_compra_adjuntos set activo=0 where orden_compra_adjuntos.id_orden=%s and orden_compra_adjuntos.nombre_adjunto=%s ",
			$this->makeValue($id_compra, "int"),
			$this->makeValue($nombre_adjunto, "text"));
		$this->setConsulta($consulta);
		if ($this->ejecutarSoloConsulta()) {
			return 1;
		}
		else return 18;
	}

	// Función para obtener los ids_referencias de una orden de compra
	function dameIdReferencias($id_compra) {
		$consulta = sprintf("select id_referencia from orden_compra_referencias where orden_compra_referencias.id_orden=%s and orden_compra_referencias.activo=1 ",
			$this->makeValue($id_compra, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->ids_referencias = $this->getResultados();
	}

	// Función que devuelve el numero total de paquetes en función de sus piezas y del número de unidades por paquete
	function calculaTotalPaquetes($uds_paquete,$piezas){
		if ($piezas<$uds_paquete) {
			$this->total_paquetes = 1;
		}
		else {
			$resto = fmod($piezas,$uds_paquete);
			$tot_paquetes = floor($piezas/$uds_paquete);
			if ($resto == 0) $this->total_paquetes = $tot_paquetes;
			else $this->total_paquetes = $tot_paquetes + 1;
		}
	}

	// Función para obtener los datos de las referencias de una orden de compra ordenadas por coste mayor
	function dameDatosOrdenCompraReferencias($id_compra) {
		$consulta = sprintf("select * from orden_compra_referencias where orden_compra_referencias.id_orden=%s and orden_compra_referencias.activo=1 order by coste DESC",
			$this->makeValue($id_compra, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->referencias = $this->getResultados();
	}

	// Función para obtener los datos de las referencias de una orden de compra
	function dameDatosOCReferencias($id_compra) {
		$consulta = sprintf("select * from orden_compra_referencias where orden_compra_referencias.id_orden=%s and orden_compra_referencias.activo=1",
			$this->makeValue($id_compra, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->referencias = $this->getResultados();
	}

	// Función para obtener la fecha de creacion de una orden de compra
	function dameFechaCreadoOC($id_compra){
		$consulta = sprintf("select fecha_creado from orden_compra where orden_compra.id_orden_compra=%s and orden_compra.activo=1 ",
			$this->makeValue($id_compra, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->fecha_creado = $this->getResultados();
	}

	// Función para obtener la fecha de entrega de una orden de compra
	function dameFechaEntregaOC($id_compra){
		$consulta = sprintf("select fecha_entrega from orden_compra where orden_compra.id_orden_compra=%s and orden_compra.activo=1 ",
			$this->makeValue($id_compra, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->fecha_entrega_BBDD = $this->getResultados();
	}

	// Función para guardar las direcciones de entrega y facturación de una orden de compra. Utilizada al descargar la factura en modificación de una orden de compra
	function guardaDirecciones($id_compra, $id_direccion_entrega, $id_direccion_facturacion){
		$consulta = sprintf("update orden_compra set direccion_entrega=%s, direccion_facturacion=%s where id_orden_compra=%s and activo=1",
			$this->makeValue($id_direccion_entrega, "int"),
			$this->makeValue($id_direccion_facturacion, "int"),
			$this->makeValue($id_compra,"int"));
		$this->setConsulta($consulta);
		if ($this->ejecutarSoloConsulta()) {
			return 1;
		}
		else return 14;
	}

	// Función para obtener las últimas órdenes de compra de proveedores únicos que fueron desactivadas
	function dameUltimasOCDesactivadasPorProveedor($id_produccion){
		$consulta = sprintf("select * from (select * from orden_compra where activo=0 and id_produccion=%s order by id_orden_compra desc) as comp group by id_proveedor",
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->referencias_oc_desactivadas = $this->getResultados();
	}

	// Función que comprueba si la OC desactivada tenia facturas asociadas
	function compruebaFacturasAntiguas($id_orden){
		$consulta = sprintf("select * from orden_compra_facturas where id_orden=%s ",
			$this->makeValue($id_orden, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->facturas = $this->getResultados();
		if($this->getNumeroFilas() == 0) {
			return false;
		}
		else {
			return true;
		}
	}

	// Copiamos las facturas antiguas de la orden desactivada a la nueva orden generada
	function copiarFacturasAntiguas($id_orden,$facturas){
		$consulta = sprintf("insert into orden_compra_facturas (id_orden, id_proveedor, nombre_factura, neto, fecha_entrega, activo, fecha_creado) value (%s,%s,%s,%s,%s,1,current_timestamp)",
			$this->makeValue($id_orden, "int"),
			$this->makeValue($facturas["id_proveedor"], "int"),
			$this->makeValue($facturas["nombre_factura"], "text"),
			$this->makeValue($facturas["neto"], "float"),
			$this->makeValue($facturas["fecha_entrega"], "date"));
		$this->setConsulta($consulta);
		if ($this->ejecutarSoloConsulta()) {
			return 1;
		}
		else return 13;
	}

	// Función que devuelve el id de una orden de compra activa con id_produccion y id_proveedor dados.
	function dameIdOCPorProveedorDeUnaOP($id_produccion,$id_proveedor){
		$consulta = sprintf("select id_orden_compra from orden_compra where orden_compra.id_produccion=%s and orden_compra.id_proveedor=%s and orden_compra.activo=1 ",
			$this->makeValue($id_produccion, "int"),
			$this->makeValue($id_proveedor, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->id_compra = $this->getResultados();
	}

	// Función que comprueba si el id_proveedor de las últimas órdenes de compra desactivadas, coincide con alguna de las nuevas órdenes generadas
	function existeOCProveedor($id_produccion,$id_proveedor){
		$consulta = sprintf("select id_orden_compra from orden_compra where orden_compra.id_produccion=%s and orden_compra.id_proveedor=%s and orden_compra.activo=1 ",
			$this->makeValue($id_produccion, "int"),
			$this->makeValue($id_proveedor, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		if($this->getNumeroFilas() == 0) {
			return false;
		} else {
			return true;
		}
	}

	// Función que actualiza las fechas y el estado de la nueva orden de compra generada
	function actualizaOC($id_orden,$fecha_pedido_desactivada,$fecha_entrega_desactivada,$fecha_requerida,$fecha_factura,$estado){
		$consulta = sprintf("update orden_compra set fecha_pedido=%s, fecha_entrega=%s, estado=%s, fecha_requerida=%s, fecha_factura=%s where id_orden_compra=%s and activo=1",
			$this->makeValue($fecha_pedido_desactivada, "date"),
			$this->makeValue($fecha_entrega_desactivada, "date"),
			$this->makeValue($estado, "text"),
			$this->makeValue($fecha_requerida, "date"),
			$this->makeValue($fecha_factura, "date"),
			$this->makeValue($id_orden,"int"));
		$this->setConsulta($consulta);
		if ($this->ejecutarSoloConsulta()) {
			return 1;
		}
		else return 16;
	}

	function repasarReferenciasRecibidas($id_compra_desactivada,$id_compra) {
		// Se consulta la orden de compra actual para obtener las referencias que se han creado
		$consultaOCActual = sprintf("select id,id_referencia from orden_compra_referencias where id_orden=%s and activo=1",
			$this->makeValue($id_compra, "int"));
		$this->setConsulta($consultaOCActual);
		$this->ejecutarConsulta();
		$orden_compras = $this->getResultados();
		$datos = $this->getResultados();
		for($i=0;$i<count($datos);$i++){
			$id_referencia = $datos[$i]["id_referencia"];
			$id = $datos[$i]["id"];
			// Se consulta la anterior orden de compra con esa referencia
			$consultaOCAntigua = sprintf("select piezas_recibidas from orden_compra_referencias where id_orden=%s and id_referencia=%s",
				$this->makeValue($id_compra_desactivada, "int"),
				$this->makeValue($id_referencia, "int"));
			$this->setConsulta($consultaOCAntigua);
			$this->ejecutarConsulta();
			$datosAnteriores = $this->getPrimerResultado();
			$piezas_recibidas = $datosAnteriores["piezas_recibidas"];
			if($piezas_recibidas == NULL) $piezas_recibidas = 0;
			$updateSql = sprintf("update orden_compra_referencias set piezas_recibidas=%s where id=%s",
				$this->makeValue($piezas_recibidas, "int"),
				$this->makeValue($id, "int"));
			$this->setConsulta($updateSql);
			$this->ejecutarSoloConsulta();
		}
	}

	// Quitar acentos y caracteres especiales a los archivos
	function limpiar($String){
		$String = str_replace(array("á","à","â","ã","ª","ä"),"a",$String);
		$String = str_replace(array("Á","À","Â","Ã","Ä"),"A",$String);
		$String = str_replace(array("Í","Ì","Î","Ï"),"I",$String);
		$String = str_replace(array("í","ì","î","ï"),"i",$String);
		$String = str_replace(array("é","è","ê","ë"),"e",$String);
		$String = str_replace(array("É","È","Ê","Ë"),"E",$String);
		$String = str_replace(array("ó","ò","ô","õ","ö","º"),"o",$String);
		$String = str_replace(array("Ó","Ò","Ô","Õ","Ö"),"O",$String);
		$String = str_replace(array("ú","ù","û","ü"),"u",$String);
		$String = str_replace(array("Ú","Ù","Û","Ü"),"U",$String);
		$String = str_replace(array("[","^","´","`","¨","~","]"),"",$String);
		$String = str_replace("ç","c",$String);
		$String = str_replace("Ç","C",$String);
		$String = str_replace("ñ","n",$String);
		$String = str_replace("Ñ","N",$String);
		$String = str_replace("Ý","Y",$String);
		$String = str_replace("ý","y",$String);
		return $String;
	}

	function getPorcentajeRecepcion() {
		$consultaSql = sprintf("select sum(round(total_piezas,2)) as piezas_pedidas, sum(round(piezas_recibidas,2)) as piezas_recibidas from orden_compra_referencias where id_orden=%s and activo=1",
			$this->makeValue($this->id_compra, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$datos = $this->getPrimerResultado();
		$pedidas = $datos["piezas_pedidas"];
		$this->piezas_pedidas = $pedidas;
		$recibidas = $datos["piezas_recibidas"];
		$this->piezas_recibidas = $recibidas;
		if ($pedidas != 0){
			$porcentaje = ($recibidas * 100 ) / $pedidas;
		}
		else $porcentaje = 0;
		$this->porcentaje_recepcion_decimal = $porcentaje;
		if($pedidas != 0){
			$porcentaje = ($recibidas * 100 ) / $pedidas;
		}
		else $porcentaje = 0;
		$this->porcentaje_recepcion = number_format($porcentaje,0,',','.');
		$this->actualizarEstadoRecepcion();
	}

	// Devuelve el numero de piezas_almacen segun id_orden e id_referencia
	function getTotalPiezasEnAlmacenReferencia($id_orden,$id_referencia) {
		$consultaSql = sprintf("select (piezas_recibidas-piezas_usadas) as piezas_almacen from orden_compra_referencias where id_orden=%s and id_referencia=%s and activo=1",
			$this->makeValue($id_orden, "int"),
			$this->makeValue($id_referencia, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$datos = $this->getPrimerResultado();
		$piezas_almacen = $datos["piezas_almacen"];
		$piezas_almacen = number_format($piezas_almacen,0,',','.');
		return $piezas_almacen;
	}

	function getPorcentajeRecepcionReferencia($id_orden,$id_referencia) {
		$this->id_compra = $id_orden;
		$consultaSql = sprintf("select sum(total_piezas) as piezas_pedidas, sum(piezas_recibidas) as piezas_recibidas from orden_compra_referencias where id_orden=%s and id_referencia=%s and activo=1",
			$this->makeValue($this->id_compra, "int"),
			$this->makeValue($id_referencia, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$datos = $this->getPrimerResultado();
		$pedidas = $datos["piezas_pedidas"];
		$this->piezas_pedidas = $pedidas;
		$recibidas = $datos["piezas_recibidas"];
		$this->piezas_recibidas = $recibidas;
		if ($pedidas != 0){
			$porcentaje = ($recibidas * 100 ) / $pedidas;
		}
		else $porcentaje = 0;
		$this->porcentaje_recepcion_decimal = $porcentaje;
		$porcentaje = ($recibidas * 100 ) / $pedidas;
		$this->porcentaje_recepcion = number_format($porcentaje,0,',','.');
	}

	function actualizarEstadoRecepcion() {
		$updateSql = "";
		$nuevo_estado = "";
		switch($this->estado) {
			case "GENERADA":
				if($this->porcentaje_recepcion_decimal != NULL) {
					if($this->porcentaje_recepcion_decimal > 0 and $this->porcentaje_recepcion_decimal < 100) {
						$updateSql = sprintf("update orden_compra set fecha_factura=current_timestamp, fecha_pedido=current_timestamp, estado='PARCIALMENTE RECIBIDO' where id_orden_compra=%s",
							$this->makeValue($this->id_compra, "int"));
						$nuevo_estado = "PARCIALMENTE RECIBIDO";
					}
					else if($this->porcentaje_recepcion_decimal == 100) {
						$updateSql = sprintf("update orden_compra set fecha_factura=current_timestamp, fecha_pedido=current_timestamp, fecha_entrega=current_timestamp, estado='RECIBIDO' where id_orden_compra=%s",
							$this->makeValue($this->id_compra, "int"));
						$nuevo_estado = "RECIBIDO";
					} else {
						// Nada
					}
				}
			break;
			case "PEDIDO INICIADO":
				if($this->porcentaje_recepcion_decimal != NULL) {
					if($this->porcentaje_recepcion_decimal > 0 and $this->porcentaje_recepcion_decimal < 100) {
						$updateSql = sprintf("update orden_compra set fecha_factura=current_timestamp, fecha_pedido=current_timestamp, estado='PARCIALMENTE RECIBIDO' where id_orden_compra=%s",
							$this->makeValue($this->id_compra, "int"));
						$nuevo_estado = "PARCIALMENTE RECIBIDO";
					}
					else if($this->porcentaje_recepcion_decimal == 100) {
						$updateSql = sprintf("update orden_compra set fecha_factura=current_timestamp, fecha_pedido=current_timestamp, fecha_entrega=current_timestamp, estado='RECIBIDO' where id_orden_compra=%s",
							$this->makeValue($this->id_compra, "int"));
						$nuevo_estado = "RECIBIDO";
					} else {
						// Nada
					}
				}
			break;
			case "PEDIDO CERRADO":
				if($this->porcentaje_recepcion_decimal != NULL) {
					if($this->porcentaje_recepcion_decimal > 0 and $this->porcentaje_recepcion_decimal < 100) {
						$updateSql = sprintf("update orden_compra set fecha_factura=current_timestamp, fecha_pedido=current_timestamp, estado='PARCIALMENTE RECIBIDO' where id_orden_compra=%s",
							$this->makeValue($this->id_compra, "int"));
						$nuevo_estado = "PARCIALMENTE RECIBIDO";
					}
					else if($this->porcentaje_recepcion_decimal == 100) {
						$updateSql = sprintf("update orden_compra set fecha_factura=current_timestamp, fecha_pedido=current_timestamp, fecha_entrega=current_timestamp, estado='RECIBIDO' where id_orden_compra=%s",
							$this->makeValue($this->id_compra, "int"));
						$nuevo_estado = "RECIBIDO";
					} else {
						// Nada
					}
				}
			break;
			case "PARCIALMENTE RECIBIDO":
				if($this->porcentaje_recepcion_decimal != NULL) {
					if($this->porcentaje_recepcion_decimal > 0 and $this->porcentaje_recepcion_decimal < 100) {
						// Nada
					}
					else if($this->porcentaje_recepcion_decimal == 100) {
						$updateSql = sprintf("update orden_compra set fecha_factura=current_timestamp, fecha_entrega=current_timestamp, estado='RECIBIDO' where id_orden_compra=%s",
							$this->makeValue($this->id_compra, "int"));
						$nuevo_estado = "RECIBIDO";
					} else {
						$updateSql = sprintf("update orden_compra set fecha_factura=current_timestamp, estado='PEDIDO CERRADO' where id_orden_compra=%s",
							$this->makeValue($this->id_compra, "int"));
						$nuevo_estado = "PEDIDO CERRADO";
					}
				}
			break;
			case "RECIBIDO":
				if($this->porcentaje_recepcion_decimal != NULL) {
					if($this->porcentaje_recepcion_decimal > 0 and $this->porcentaje_recepcion_decimal < 100) {
						$updateSql = sprintf("update orden_compra set fecha_factura=current_timestamp, fecha_pedido=current_timestamp, estado='PARCIALMENTE RECIBIDO' where id_orden_compra=%s",
							$this->makeValue($this->id_compra, "int"));
						$nuevo_estado = "PARCIALMENTE RECIBIDO";
					}
					else if($this->porcentaje_recepcion_decimal == 100) {
						// Nada
					} else {
						$updateSql = sprintf("update orden_compra set fecha_factura=current_timestamp, estado='PEDIDO CERRADO' where id_orden_compra=%s",
							$this->makeValue($this->id_compra, "int"));
						$nuevo_estado = "PEDIDO CERRADO";
					}
				}
			break;
		}
		if($updateSql != "") {
			$this->setConsulta($updateSql);
			if($this->ejecutarSoloConsulta()) {
				$this->estado = $nuevo_estado;
			}
		}
	}
	function __destruct() {
		$this->actualizarEstadoRecepcion();
	}

	// Funcion que devuelve los proveedores de las ordenes de compra seleccionadas
	function getProveedoresOrdenCompra($ids_compra) {
		$consultaSql = "select id_proveedor from orden_compra where id_orden_compra in (".$ids_compra.") group by id_proveedor";
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	function getOrdenesCompraProveedor($idprov,$ids_compra) {
		$consultaSql = "select id_orden_compra from orden_compra where id_orden_compra in (".$ids_compra.") and id_proveedor=".$idprov;
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Funcion que devuelve el proveedor de la Orden de Compra
	function dameProveedorOC($id_compra) {
		$consultaSql = sprintf("select id_proveedor from orden_compra where id_orden_compra=%s",
			$this->makeValue($id_compra, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$this->id_proveedor = $this->getPrimerResultado();
	}

    // Funcion que devuelve los datos de observaciones de una fra_request
    function dameObservacionesFraRequest(){
        // Cargamos las Observaciones
        $consulta_observaciones = 'select valor from orden_compra_opciones where clave="texto_observaciones"';
        $this->setConsulta($consulta_observaciones);
        $this->ejecutarConsulta();
        return current($this->getPrimerResultado());
    }

    // Funcion que devuelve los datos de la agencia de transporte
    function dameDatosAgenciaTransporte(){
        $consulta_agencia = 'select * from agencia_transporte';
        $this->setConsulta($consulta_agencia);
        $this->ejecutarConsulta();
        return $this->getPrimerResultado();
    }


	// Devuelve la cadena de un error según su identificador
	function getErrorMessage($error_num) {
		switch($error_num) {
			case 2:
				return 'Se produjo un error al guardar la nueva orden de compra<br/>';
			break;
			case 3:
				return 'Se produjo un error al modificar la nueva orden de compra<br/>';
			break;
			case 4:
				return 'Se produjo un error al eliminar los datos de la orden de compra<br/>';
			break;
			case 6:
				return 'Se produjo un error al guardar las referencias de un proveedor en la orden de compra<br/>';
			break;
			case 9:
				return 'Se produjo un error al eliminar las referencias de la orden de compra<br/>';
			break;
	 		case 10:
				return 'Se produjo un error al eliminar las facturas de la orden de compra<br/>';
			break;
			case 11:
				return 'Se produjo un error al quitar la factura de la orden de compra<br/>';
			break;
			case 12:
				return 'Se produjo un error al adjuntar las facturas<br/>';
			break;
			case 13:
				return 'Se produjo un error al guardar las facturas en la base de datos<br/>';
			break;
			case 14:
				return 'Se produjo un error al guardar las direcciones de la orden de compra<br/>';
			break;
			case 15:
				return 'Se produjo un error al modificar varias órdenes de compra<br/>';
			break;
			case 16:
				return 'Se produjo un error al modificar varias órdenes de compra<br/>';
			break;
			case 17:
				return 'Se produjo un error al adjuntar los archivos adjuntos<br/>';
			break;
			case 18:
				return 'Se produjo un error al quitar el archivo adjunto de la orden de compra<br/>';
			break;
			case 19:
				return 'Se produjo un error al eliminar los archivos adjuntos de la orden de compra<br/>';
			break;
		}
	}
}
?>