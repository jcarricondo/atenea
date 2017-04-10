<?php
class Orden_Produccion extends MySQL {
	
	var $id_produccion; 
	var $unidades;
	var $codigo;
	var $fecha_inicio;
	var $fecha_entrega;
	var $fecha_entrega_deseada;
	var $estado;
	var $comentarios;
	var $fecha_creado;
	var $activo;
	var $porcentaje_recepcion = NULL;
	var $porcentaje_recepcion_decimal = NULL;
	
	var $id_producto;	
	var $ids_productos;
	var $num_serie;
	var $referencias_orden_compra;
	var $referencias_op;
	var $ids_orden_compra;
	var $nombre_componente;
	var $alias_op;
	var $id_tipo;
	var $ids_produccion;
	var $id_tipo_componente;
	var $ids_proveedores; 
	var $ids_kit;
	var $fecha_inicio_construccion;
	var $id_sede;


	// Carga de datos de una orden de producción ya existente en la base de datos	
	function cargarDatos($id_produccion,$unidades,$codigo,$id_tipo,$fecha_inicio,$fecha_entrega,$fecha_entrega_deseada,$fecha_inicio_construccion,$estado,$comentarios,$fecha_creado,$activo,$alias_op,$id_sede) {
		$this->id_produccion = $id_produccion;
		$this->unidades = $unidades;
		$this->codigo = $codigo;
		$this->fecha_inicio = $fecha_inicio;
		$this->fecha_entrega = $fecha_entrega;
		$this->fecha_entrega_deseada = $fecha_entrega_deseada;
		$this->fecha_inicio_construccion = $fecha_inicio_construccion;
		$this->estado = $estado;
		$this->comentarios = $comentarios;
		$this->fecha_creado = $fecha_creado;
		$this->activo = $activo;
		$this->alias_op = $alias_op;
		$this->id_tipo = $id_tipo;
		$this->id_sede = $id_sede;
	}
	
	// Se obtienen los datos de la orden de producción en base a su ID
	function cargaDatosProduccionId($id_produccion) {
		$consultaSql = sprintf("select * from orden_produccion where id_produccion=%s",
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id_produccion"],
			$resultados["unidades"],
			$resultados["codigo"],
			$resultados["id_tipo"],
			$resultados["fecha_inicio"],
			$resultados["fecha_entrega"],
			$resultados["fecha_entrega_deseada"],
			$resultados["fecha_inicio_construccion"],
			$resultados["estado"],
			$resultados["comentarios"],
			$resultados["fecha_creado"],
			$resultados["activo"],
			$resultados["alias"],
			$resultados["id_sede"]
		);
	}

	// Se hace la carga de datos de la nueva orden de producción
	function datosNuevaProduccion($id_produccion = NULL,$unidades,$codigo,$id_tipo,$fecha_inicio,$fecha_entrega,$fecha_entrega_deseada,$estado,$comentarios,$alias_op,$fecha_inicio_construccion,$id_sede) {
		$this->id_produccion = $id_produccion;
		$this->unidades = $unidades;
		$this->codigo = $codigo;
		$this->fecha_inicio = $fecha_inicio;
		$this->fecha_entrega = $fecha_entrega;
		$this->fecha_entrega_deseada = $fecha_entrega_deseada;
		$this->estado = $estado;
		$this->comentarios = $comentarios;
		$this->alias_op = $alias_op;
		$this->id_tipo = $id_tipo;
		$this->fecha_inicio_construccion = $fecha_inicio_construccion;
		$this->id_sede = $id_sede;
	}

	// Guarda los cambios realizados en la orden de producción
	function guardarCambios() {
		// Si el id_producción es NULL lo toma como una nueva orden de producción
		if($this->id_produccion == NULL) {
			$consulta = sprintf("insert into orden_produccion (alias,unidades,codigo,id_tipo,fecha_inicio,fecha_entrega,fecha_entrega_deseada,fecha_inicio_construccion,estado,id_sede,comentarios,fecha_creado,activo) value (%s,%s,%s,%s,current_timestamp,current_timestamp,current_timestamp,%s,'BORRADOR',%s,%s,current_timestamp,1)",
				$this->makeValue($this->alias_op, "text"),
				$this->makeValue($this->unidades, "int"),
				$this->makeValue($this->codigo, "text"),
				$this->makeValue($this->id_tipo, "int"),
				$this->makeValue($this->fecha_inicio_construccion, "text"),
				$this->makeValue($this->id_sede,"int"),
				$this->makeValue($this->comentarios, "text"));
			$this->setConsulta($consulta);
			if($this->ejecutarSoloConsulta()) {
				$this->id_produccion = $this->getUltimoID();
				return 1;
			} 
			else {
				return 2;
			}
		} 
		else {
			$consulta = sprintf("update orden_produccion set alias=%s,unidades=%s,fecha_inicio=%s,fecha_entrega=%s,fecha_entrega_deseada=%s,estado=%s,comentarios=%s where id_produccion=%s",
				$this->makeValue($this->alias_op, "text"),
				$this->makeValue($this->unidades, "int"),
				$this->makeValue($this->fecha_inicio, "date"),
				$this->makeValue($this->fecha_entrega, "date"),
				$this->makeValue($this->fecha_entrega_deseada, "date"),
				$this->makeValue($this->estado, "text"),
				$this->makeValue($this->comentarios, "text"),
				$this->makeValue($this->id_produccion, "int"));
			$this->setConsulta($consulta);
			if($this->ejecutarSoloConsulta()) {
				return 1;
			}
			else {
				return 3;
			}
		}
	}

	// Elimina la Orden de Produccion
	function eliminar(){
		//ELIMINAR
		$consulta = sprintf("update orden_produccion set activo=0 where id_produccion=%s",	
			$this->makeValue($this->id_produccion, "int"));
			$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		} else {
			return 4;
		}
	}

	// Inicia una orden de producción. Establece las fechas de entrega y cambia el estado a iniciado
	function iniciarOrdenProduccion ($id_produccion, $fecha_inicio, $fecha_entrega, $fecha_entrega_deseada) {
		$consulta = sprintf ("update orden_produccion set fecha_inicio=%s, fecha_entrega=%s, fecha_entrega_deseada=%s, estado='INICIADO' where id_produccion=%s",
			$this->makeValue($fecha_inicio, "date"),
			$this->makeValue($fecha_entrega, "date"),
			$this->makeValue($fecha_entrega_deseada, "date"),
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) return 1;
		else return 6;
	}

	// Establece las fechas de inicio y entrega y vuelve a poner el estado a borrador
	function estadoBorradorOrdenProduccion($id_produccion, $fecha_inicio, $fecha_entrega, $fecha_entrega_deseada) {
		$consulta = sprintf ("update orden_produccion set fecha_inicio=%s, fecha_entrega=%s, fecha_entrega_deseada=%s, estado='BORRADOR' where id_produccion=%s",
			$this->makeValue($fecha_inicio, "date"),
			$this->makeValue($fecha_entrega, "date"),
			$this->makeValue($fecha_entrega_deseada, "date"),
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consulta);
		if ($this->ejecutarSoloConsulta()) return 1;
		else return 8;
	}

	// Cambia el estado de los productos de una orden de producción a borrador
	function estadoBorradorProducto($id_produccion) {
		$consulta = sprintf ("update productos set estado='BORRADOR' where id_produccion=%s",
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consulta);
		if ($this->ejecutarSoloConsulta()) return 1;
		else return 9;
	}

	// Desactiva los productos de una orden de producción dada
	function desactivarProductos ($id_produccion) {
		$consulta = sprintf("update productos set activo=0 where id_produccion=%s",
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) return 1;
		else return 10;
	}

	// Desactiva una orden de producción
	function desactivarOrdenProduccion ($id_produccion) {
		$consulta = sprintf("update orden_produccion set activo=0 where id_produccion=%s",
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) return 1;
		else return 11;
	}

	// Inserta el código de la OP una vez conseguido su id_producción
	function insertaCodigoOrdenProduccion($id_produccion,$codigo) {
		$consulta = sprintf("update orden_produccion set codigo=%s where id_produccion=%s",
			$this->makeValue($codigo, "text"),
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) return 1;
		else return 12;	
	}

	// Inserta el código de la OP de Mantenimiento una vez conseguido su id_producción
	function insertaCodigoOrdenProduccionMantenimiento($id_produccion,$codigo) {
		$consulta = sprintf("update orden_produccion set codigo=%s where id_produccion=%s",
			$this->makeValue($codigo, "text"),
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) return 1;
		else return 12;	
	}

	// Desactiva las órdenes de compra de una orden de producción dada
	function desactivarOrdenCompraPorIdProduccion($id_produccion) {
		$consulta = sprintf("update orden_compra set activo=0 where id_produccion=%s",
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) return 1;
		else return 13;
	}

	// Función que actualiza el estado de una OP. Si esta en "iniciado" cambia a "finalizado".
	// Guarda en la BBDD el estado que le llega por parámetro
	function actualizaEstadoOP($id_produccion,$estado){
		$consulta = sprintf("update orden_produccion set estado=%s where id_produccion=%s",
			$this->makeValue($estado, "text"),
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) return 1;
		else return 14;
	}

	// Función para insertar en el alias el código de la OP si el alias es vacio, o el nuevo alias modificado
	function insertaAliasOrdenProduccion($id_produccion,$codigo) {
		$consulta = sprintf("update orden_produccion set alias=%s where id_produccion=%s",
			$this->makeValue($codigo, "text"),
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) return 1;
		else return 15;	
	}

	// Función que cambia el estado a FINALIZADO de los productos de una Orden de Producción que ha sido finalizada
	function finalizarProductos($id_produccion){
		$consulta = sprintf("update productos set estado='FINALIZADO' where id_produccion=%s",
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) return 1;
		else return 16;
	}

	// Funcion que guarda un componente de la produccion
	function guardarComponenteProduccion($id_produccion,$id_componente,$num_serie){
		$consulta = sprintf("insert into orden_produccion_componentes (id_produccion,id_componente,num_serie,fecha_creado,activo) value (%s,%s,%s,current_timestamp,1)",
			$this->makeValue($id_produccion, "int"),
			$this->makeValue($id_componente, "int"),
			$this->makeValue($num_serie, "text"));
		$this->setConsulta($consulta);	
		if ($this->ejecutarSoloConsulta()) { 
			return 1;		
		}	
		else {
			return 17;
		}	
	}

	// Funcion que guarda las referencias de una produccion
	function guardarReferenciasProduccion($id_produccion,$id_tipo,$id_produccion_componente,$id_componente,$id_referencia,$uds_paquete,$piezas,$total_paquetes,$pack_precio){
		$consulta = sprintf("insert into orden_produccion_referencias (id_produccion,id_tipo_componente,id_produccion_componente,id_componente,id_referencia,uds_paquete,piezas,total_paquetes,pack_precio,fecha_creado,activo) value (%s,%s,%s,%s,%s,%s,%s,%s,%s,current_timestamp,1)",
			$this->makeValue($id_produccion, "int"),
			$this->makeValue($id_tipo, "int"),
			$this->makeValue($id_produccion_componente,"int"),
			$this->makeValue($id_componente, "int"),
			$this->makeValue($id_referencia, "int"),
			$this->makeValue($uds_paquete, "int"),
			$this->makeValue($piezas, "float"),
			$this->makeValue($total_paquetes, "int"),
			$this->makeValue($pack_precio, "float"));
		$this->setConsulta($consulta);	
		if ($this->ejecutarSoloConsulta()) { 
			return 1;		
		}	
		else {
			return 18;
		}			
	}

	// Desactiva los componentes de una Orden de Produccion
	function desactivarProductosComponentes($id_produccion) {
		$consulta = sprintf("update orden_produccion_componentes set activo=0 where id_produccion=%s",
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) return 1;
		else return 19;
	}

	// Desactiva las referencias de una Orden de Produccion
	function desactivarProductosReferencias($id_produccion) {
		$consulta = sprintf("update orden_produccion_referencias set activo=0 where id_produccion=%s",
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) return 1;
		else return 20;
	}

	// Actualizamos las piezas recibidas de la Orden de Produccion
	function recibirTodasPiezasOP($id_produccion){
		// Obtenemos las referencias de OCR correspondientes a la OP
		$resultados = $this->dameOCReferenciasPorProduccion($id_produccion);
	
		for($i=0;$i<count($resultados);$i++){
			$id = $resultados[$i]["id"];
			$total_piezas = $resultados[$i]["total_piezas"];

			// Actualizams el campo piezas_recibidas
			$consulta = sprintf("update orden_compra_referencias set piezas_recibidas=%s where id=%s",
				$this->makeValue($total_piezas, "float"),
				$this->makeValue($id, "int"));
			$this->setConsulta($consulta);

			if(!$this->ejecutarSoloConsulta()){
				return 21;
			}
		}
		return 1;
	}

	// Funcion que actualiza las fechas de entrega y entrega deseada de la OP
	function actualizarFechasOP($id_produccion,$fecha_entrega,$fecha_entrega_deseada){
		$updateSql = sprintf("update orden_produccion set fecha_entrega=%s, fecha_entrega_deseada=%s where id_produccion=%s",
			$this->makeValue($fecha_entrega, "text"),
			$this->makeValue($fecha_entrega_deseada, "text"),
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($updateSql);
		if($this->ejecutarSoloConsulta()){
			return 1;
		}
		else {
			return 22;
		}
	}

	// Funcion que actualiza la fecha de inicio de construccion de una Orden de Produccion
	function cambiarFechaProduccion($id_produccion,$fecha){
		$updateSql = sprintf("update orden_produccion set fecha_inicio_construccion=%s where id_produccion=%s",
			$this->makeValue($fecha, "text"),
			$this->makeValue($id_produccion,"int"));
		$this->setConsulta($updateSql);
		if($this->ejecutarSoloConsulta()){
			return 1;
		}
		else{
			return 23;
		}	
	}

	// Funcion que devuelve el tipo del componente
	function dameTipoComponente($id_componente){
		$consulta = sprintf("select id_tipo from componentes where id_componente=%s",
			$this->makeValue($id_componente, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();	 
		return $this->getPrimerResultado();
	}

	// Funcion que devuelve el ultimo id_produccion_componente creado en la base de datos
	function dameUltimoIdProduccionComponente(){
		return $this->getUltimoID();
	}

	// Devuelves los ids de los productos de una orden de producción activa
	function dameIdsProductoOP($id_produccion){
		$consulta = sprintf("select id_producto from productos where productos.id_produccion=%s and productos.activo=1 ",
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->ids_productos = $this->getResultados();
	}	

	// Devuelve el primer producto de la Orden de Producción
	function dameIdProducto($id_produccion){
		$consulta = sprintf("select id_producto from productos where productos.id_produccion=%s and productos.activo=1 ",
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->id_producto = $this->getPrimerResultado();
	}

	// Devuelve los ids de los proveedores de un producto de una orden de produccion
	function dameIdsProveedores($id_produccion) {
		$consulta = sprintf("select id_proveedor from orden_produccion_referencias as opr inner join referencias on (referencias.id_referencia=opr.id_referencia) where opr.id_produccion=%s and opr.activo=1 group by id_proveedor ",
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->ids_proveedores = $this->getResultados();
 	}

	// Devuelve un array con campos id_produccion, id_referencia, sum(piezas) por referencia = piezas, pack_precio, uds_paquete
	function dameReferenciasPorProveedor($id_produccion,$id_proveedor) {
		$consulta = sprintf("select *, sum(opr.piezas) as piezas from orden_produccion_referencias as opr inner join referencias on (referencias.id_referencia = opr.id_referencia) inner join proveedores on (proveedores.id_proveedor = referencias.id_proveedor) where proveedores.id_proveedor=%s and opr.id_produccion=%s and opr.activo=1 group by opr.id_referencia ", 
			$this->makeValue($id_proveedor, "int"),
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Devuelve el id_orden_compra dado una orden de produccion y un proveedor
	function dameIdCompraPorProduccionYProveedor($id_produccion,$id_proveedor) {
		$consulta = sprintf("select id_orden_compra from orden_compra where id_produccion=%s and id_proveedor=%s and orden_compra.activo=1",
			$this->makeValue($id_produccion, "int"),
			$this->makeValue($id_proveedor, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Devuelve los ids de los periféricos de la orden de produccion
	function dameIdsPerifericos($id_produccion) {
		$consultaId = sprintf("select * from orden_produccion_componentes where id_produccion=%s and id_componente in 
								(select id_componente from componentes where id_tipo=2 and activo=1) and activo=1",
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consultaId);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Función que determina si una orden de producción tiene una plantilla con kits libres
	function tieneKitsLibres($id_produccion){
		$consultaSql = sprintf("select distinct id_componente from orden_produccion_referencias where activo=1 and id_produccion=%s and id_tipo_componente=6",
						$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$res = $this->getResultados();
		return !empty($res);
	}

	// Función que devuelve los Kits Libres de una plantilla de una Orden de Producción
	function dameIdsKitsLibres($id_produccion){
		$consultaSql = sprintf("select distinct id_componente from orden_produccion_referencias where activo=1 and id_produccion=%s and id_tipo_componente=6",
				$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$res = $this->getResultados();
		if(!empty($res)) {
			foreach($res as $kit_libre) $ids_kits_libres[] = intval($kit_libre["id_componente"]);
			//$ids_kits_libres = array_column($res,"id_componente");
		}
		return $ids_kits_libres;
	}

	// Devuelve los id_produccion_componentes de la orden de produccion
	function dameIdsProduccionComponente($id_produccion) {
		$consultaId = sprintf("select opc.id_produccion_componente from orden_produccion_componentes as opc where opc.id_produccion=%s and opc.activo=1",
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consultaId);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Devuelve el tipo de componente segun su id_producto_componente
	function dameIdTipoPorIdProduccionComponente($id_produccion_componente) {
		$consultaId = sprintf("select id_tipo_componente from orden_produccion_referencias as opr where opr.id_produccion_componente=%s group by id_tipo_componente ",
			$this->makeValue($id_produccion_componente, "int"));
		$this->setConsulta($consultaId);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Devuelve el id_componente segun su id_producto_componente
	function dameIdComponentePorIdProduccionComponente($id_produccion_componente) {
		$consultaId = sprintf("select id_componente from orden_produccion_componentes as opc where opc.id_produccion_componente=%s group by id_componente ",
			$this->makeValue($id_produccion_componente, "int"));
		$this->setConsulta($consultaId);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Devuelve los id_componente de una Orden de Produccion
	function dameComponentesOP($id_produccion) {
		$consultaId = sprintf("select id_componente from orden_produccion_componentes as opc where opc.id_produccion=%s and activo=1 ",
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consultaId);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Devuelve las referencias por id_produccion y id_componente
	function dameReferenciasPorIdComponente($id_produccion,$id_componente) {
		$consultaId = sprintf("select * from orden_produccion_referencias as opr where opr.id_produccion=%s and opr.id_componente=%s and opr.activo=1 group by id_referencia ",
			$this->makeValue($id_produccion, "int"),
			$this->makeValue($id_componente, "int"));
		$this->setConsulta($consultaId);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Devuelve los id_referencia y las piezas de las referencias por id_produccion e id_componente
	function dameIdReferenciaPiezasPorIdComponente($id_produccion,$id_componente) {
		$consultaId = sprintf("select id_referencia, piezas from orden_produccion_referencias as opr where opr.id_produccion=%s and opr.id_componente=%s and opr.activo=1 group by id_referencia ",
			$this->makeValue($id_produccion, "int"),
			$this->makeValue($id_componente, "int"));
		$this->setConsulta($consultaId);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Devuelve los id_referencia y las piezas de las referencias por id_produccion e id_produccion_componente
	function dameIdReferenciaPiezasPorIdProduccionComponente($id_produccion,$id_produccion_componente) {
		$consultaId = sprintf("select id_referencia, piezas from orden_produccion_referencias as opr where opr.id_produccion=%s and opr.id_produccion_componente=%s and opr.activo=1 group by id_referencia ",
			$this->makeValue($id_produccion, "int"),
			$this->makeValue($id_produccion_componente, "int"));
		$this->setConsulta($consultaId);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Devuelve los datos segun la orden de produccion y el componente
	function cargaDatosPorProduccionComponente($id_produccion,$id_produccion_componente) {
		$consulta = sprintf("select * from orden_produccion_referencias as opr where opr.id_produccion=%s and opr.id_produccion_componente=%s and opr.activo=1 order by opr.id_referencia",
			$this->makeValue($id_produccion,"int"),
			$this->makeValue($id_produccion_componente,"int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();		
	}

	// Devuelve todas las referencias de un simulador de la orden de produccion
	function cargaDatosPorProduccion($id_produccion) {
		$consulta = sprintf("select * from orden_produccion_referencias as opr where opr.id_produccion=%s and opr.activo=1",
			$this->makeValue($id_produccion,"int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();		
	}

	// Devuelve todas las referencias de las Ordenes de Compra de una Produccion
	function dameOCReferenciasPorProduccion($id_produccion){
		$consulta = sprintf("select * from orden_compra_referencias as ocr where ocr.id_orden in (select id_orden_compra from orden_compra where id_produccion=%s and activo=1) order by id_referencia",
			$this->makeValue($id_produccion,"int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();		
	}

	// Devuelve los ids de las ordenes de compra activas de una orden de producción
	function dameIdsOrdenesCompra($id_produccion){
		$consulta = sprintf("select id_orden_compra from orden_compra where orden_compra.id_produccion=%s and orden_compra.activo=1 ",	
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->ids_orden_compra = $this->getResultados();
	}

	// Comprueba si la fecha introducida está en el formato correcto
	function validarFecha ($fecha) {
 		$fecha = explode("/",$fecha);
 		if(sizeof($fecha) != 3) return false;
 		if(checkdate($fecha[1],$fecha[0],$fecha[2])) return true;
 		else return false;
	}
	
	// Convierte la fecha del listado a formato MySQL
	function cFechaMy ($fecha) {
 		$f = explode("/",$fecha);
 		return $f[2]."-".$f[1]."-".$f[0];
	}
	
	// Convierte la fecha MySQL a formato listado
	function cFechaNormal($fecha) {
 		$f = explode("-",$fecha);
 		if(count($f) > 1) {
  			return $f[2]."/".$f[1]."/".$f[0];
 		} else {
  			return $fecha;
		 }
	}
	
	// Convierte la fecha a mm/dd/aaaa y viceversa
	function cFechaMyEsp ($fecha) {
 		$f = explode("/",$fecha);
 		return $f[1]."/".$f[0]."/".$f[2];
	}

	// Función que comprueba si el alias introducido existe ya en la BBDD
	function compruebaAlias($alias){
		$consulta = sprintf("select id_produccion from orden_produccion where alias=%s and activo=1",
			$this->makeValue($alias, "text"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		if($this->getNumeroFilas() == 0) {
			return false;	
		} else {
			return true;
		}
	}

	function getPorcentajeRecepcion() {
		$consultaSql = sprintf("select sum(ocr.total_piezas) as piezas_pedidas, sum(ocr.piezas_recibidas) as piezas_recibidas from orden_compra as oc inner join orden_produccion as op on (op.id_produccion=oc.id_produccion) inner join orden_compra_referencias as ocr on (ocr.id_orden=oc.id_orden_compra) where op.id_produccion=%s and ocr.activo=1",
			$this->makeValue($this->id_produccion, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$datos = $this->getPrimerResultado();
		$pedidas = $datos["piezas_pedidas"];
		$recibidas = $datos["piezas_recibidas"];
		if ($pedidas != 0){
			$porcentaje = ($recibidas * 100 ) / $pedidas;
		}
		else $porcentaje = 0;
		$this->porcentaje_recepcion_decimal = $porcentaje;
		$this->porcentaje_recepcion = number_format($porcentaje,0,',','.');
		$this->actualizarEstadoRecepcion();
	}

	function actualizarEstadoRecepcion() {
		if($this->estado == "BORRADOR") {
			if($this->porcentaje_recepcion_decimal > 0 ) {
				$updateSql = sprintf("update orden_produccion set estado='INICIADO', fecha_inicio=current_timestamp where id_produccion=%s",
					$this->makeValue($this->id_produccion, "int"));
				$this->setConsulta($updateSql);
				if($this->ejecutarSoloConsulta()) {
					$this->estado = "INICIADO";
				}
			}
		}
		if($this->estado == "INICIADO") {
			if($this->porcentaje_recepcion_decimal == 100){
				$updateSql = sprintf("update orden_produccion set estado='FINALIZADO' where id_produccion=%s",
					$this->makeValue($this->id_produccion, "int"));
				$this->setConsulta($updateSql);
				if($this->ejecutarSoloConsulta()){
					$this->estado = "FINALIZADO";
				}
			}
		}
		if($this->estado == "FINALIZADO") {
			if($this->porcentaje_recepcion_decimal < 100){
				$updateSql = sprintf("update orden_produccion set estado='INICIADO' where id_produccion=%s",
					$this->makeValue($this->id_produccion, "int"));
				$this->setConsulta($updateSql);
				if($this->ejecutarSoloConsulta()){
					$this->estado = "INICIADO";
				}
			}
		}
	}

	// Funcion que devuelve las referencias y el total_piezas de varias Ordenes de Produccion
	function dameReferenciasVariasOp($ids_produccion){
		$consulta = "select *, sum(total_piezas) as total_piezas_ops, sum(piezas_recibidas) as total_piezas_rec, sum(piezas_usadas) as total_piezas_usa from orden_compra_referencias as ocr where ocr.id_orden in (select id_orden_compra from orden_compra where ("; 
		for($i=0;$i<count($ids_produccion);$i++){
			if($i == 0){
				$consulta .= sprintf("id_produccion=%s",
					$this->makeValue($ids_produccion[$i], "int"));	
			}
			else {
				$consulta .= sprintf (" or id_produccion=%s", 
					$this->makeValue($ids_produccion[$i], "int"));
			}
		}
		$consulta .= ") and activo=1) group by id_referencia order by id_referencia";
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Funcion que devuelve la ultima orden de produccion activa
	function dameUltimaOpIniciadaActiva($id_sede){
		$consulta = sprintf("select id_produccion from orden_produccion where estado='INICIADO' and id_sede=%s and activo=1 order by id_produccion DESC",
			$this->makeValue($id_sede,"int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->id_produccion = $this->getPrimerResultado();
	}

	// Función que devuelve las ordenes de produccion INICIADAS
	function dameOrdenesProduccionIniciadas($id_sede){
		$consulta = sprintf("select id_produccion from orden_produccion where estado='INICIADO' and id_sede=%s and activo=1",
			$this->makeValue($id_sede, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->id_produccion = $this->getResultados();
	}

	// Función que devuelve las ordenes de produccion INICIADAS y ordenadas por inicio de CONSTRUCCION
	function dameOrdenesProduccionIniciadasPorConstruccion($id_sede){
		$consulta = sprintf("select id_produccion from orden_produccion where estado='INICIADO' and id_sede=%s and activo=1 order by fecha_inicio_construccion, id_produccion limit 3",
			$this->makeValue($id_sede, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->id_produccion = $this->getResultados();
	}

	// Devuelve los ids de los kits de un componente
	function dameIdsKitComponente($id_componente) {
		$consulta = sprintf("select id_kit from componentes_kits where componentes_kits.id_componente=%s and componentes_kits.activo=1 ",
			$this->makeValue($id_componente, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->ids_kit = $this->getResultados();
	}

	// Devuelve los números de serie de los productos activos de una orden de producción
	function dameNumSerieProductos($id_produccion) {
		$consulta = sprintf("select productos.num_serie from productos where productos.id_produccion=%s and activo=1 ",
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->num_serie = $this->getResultados();
	}

	// Devuelve los ids de las referencias de las órdenes de compra de una orden de producción
	function dameOrdenCompraReferenciasABorrar($id_produccion){
		$consulta = sprintf("select id from orden_compra_referencias inner join orden_compra on (orden_compra.id_orden_compra=orden_compra_referencias.id_orden) where orden_compra.id_produccion=%s and orden_compra.activo=1 ",	
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->referencias_orden_compra = $this->getResultados();
	}

	// Devuelve todos los id productos de una Orden de Producción
	function dameIdProductoSinActivo($id_produccion){
		$consulta = sprintf("select id_producto from productos where productos.id_produccion=%s ",
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->id_producto = $this->getPrimerResultado();
	}
	
	// Función que dada una orden de producción comprueba si sus órdenes de compra tienen facturas asociadas
	function comprobarHayFacturasOrdenCompra($id_produccion){
		$consulta = sprintf("select id_factura from orden_compra_facturas inner join orden_compra on (orden_compra.id_orden_compra=orden_compra_facturas.id_orden) where orden_compra.id_produccion=%s and orden_compra.activo=1",
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		if($this->getNumeroFilas() == 0) {
			return false;	
		} else {
			return true;
		}
	}
	
	// Función que dada una orden de producción comprueba si alguna de sus órdenes de compra están en estado pedida o recibida
	function comprobarHayOrdenesCompraEnPedida($id_produccion){
		$consulta = sprintf("select id_orden_compra from orden_compra where orden_compra.id_produccion=%s and (orden_compra.estado='PEDIDA' or orden_compra.estado='RECIBIDA') and orden_compra.activo=1",
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		if($this->getNumeroFilas() == 0) {
			return false;	
		} else {
			return true;
		}
	}

	// Función que dado un id_componente devuelve el nombre y la referencia de ese componente
	function dameNombreComponente($id_componente){
		$consulta = sprintf("select nombre,referencia from componentes where id_componente=%s",	
			$this->makeValue($id_componente, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->nombre_componente = $this->getResultados();
	}

	// Devuelve el total de piezas que se han recibido de una referencia de una orden de producción
	function dameTotalPiezasRecibidasUsadasReferenciaOP($id_produccion,$id_referencia){
		$consulta = sprintf("select ocr.piezas_recibidas,ocr.piezas_usadas from orden_compra as oc inner join orden_compra_referencias as ocr on (ocr.id_orden=oc.id_orden_compra) where oc.id_produccion=%s and ocr.id_referencia=%s and ocr.activo=1",	
			$this->makeValue($id_produccion, "int"),
			$this->makeValue($id_referencia, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->referencias_op = $this->getResultados();
	}

	// Función que comprueba si el alias existe en la base de datos, exceptuando el suyo propio
	function compruebaModAlias($alias,$id_produccion){
		$consulta = sprintf("select id_produccion from orden_produccion where alias=%s and activo=1 and id_produccion<>%s ",
			$this->makeValue($alias, "text"),
			$this->makeValue($id_produccion,"int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		if($this->getNumeroFilas() == 0) {
			return false;	
		} else {
			return true;
		}
	}
	
	// Funcion que devuelve las ultimas ordenes de produccion que esten activas
	function dameIdsProduccionActivas(){
		$consulta = "select id_produccion from orden_produccion where activo=1";
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->ids_produccion = $this->getResultados();
	}

	// Funcion que devuelve la ultima orden de produccion activa
	function dameUltimaOpActiva(){
		$consulta = "select id_produccion from orden_produccion where activo=1 order by id_produccion DESC";
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->id_produccion = $this->getPrimerResultado();
	}

	// Función que devuelve los id_producción de las OP Iniciadas que contengan la referencia id_referencia
	function dameOPIniciadasReferencia($id_referencia,$id_sede){
		// Primero comprobamos si el usuario filtro por Orden de Produccion
		$consulta = sprintf("select id_produccion from orden_produccion where estado='INICIADO' and activo=1 and id_sede=%s and id_produccion in 
	      						(select id_produccion from orden_compra where id_orden_compra in 
    	                           	(select id_orden from orden_compra_referencias where id_referencia=%s and activo=1)) order by orden_produccion.id_produccion asc",
        		$this->makeValue($id_sede, "int"),
        		$this->makeValue($id_referencia,"int"));
        $this->setConsulta($consulta);
        $this->ejecutarConsulta();
       	$this->ids_produccion = $this->getResultados();
    } 

    // Funcion que devuelve los id_produccion de las OP Iniciadas que contengan la referencia id_referencia ordenadas por las ultimas creadas
	function dameOPIniciadasReferenciaModernas($id_referencia,$id_sede){
		// Primero comprobamos si el usuario filtro por Orden de Produccion
		$consulta = sprintf("select id_produccion from orden_produccion where estado='INICIADO' and activo=1 and id_sede=%s and id_produccion in 
	      						(select id_produccion from orden_compra where id_orden_compra in 
    	                           	(select id_orden from orden_compra_referencias where id_referencia=%s and activo=1)) order by orden_produccion.id_produccion desc",
        		$this->makeValue($id_sede, "int"),
        		$this->makeValue($id_referencia,"int"));
        $this->setConsulta($consulta);
        $this->ejecutarConsulta();
       	$this->ids_produccion = $this->getResultados();
    }

    // Funcion que devuelve las referencias de ordenadores que tiene una orden de produccion
	function dameReferenciasOrdenadores($id_produccion){
		$consulta = sprintf('select distinct opr.id_referencia from orden_produccion_referencias as opr where opr.id_produccion=%s and opr.id_referencia in (select id_referencia from referencias where referencias.part_tipo="ORDENADOR")',
			$this->makeValue($id_produccion,"int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();	
	}


    // Recorre las referencias de un componente y lo añade al array principal de referencias
	function agruparReferenciasComponentes($referencias_componente_secundario,$referencias_componente_final){
		$referencias_aux = $referencias_componente_final;
		for($i=0;$i<count($referencias_componente_secundario);$i++){
			$id_referencia = $referencias_componente_secundario[$i]["id_referencia"];
			$piezas = $referencias_componente_secundario[$i]["piezas"];
			$encontrado = false;
			$j=0;
			while(($j<count($referencias_componente_final)) and (!$encontrado)){
				// Si coinciden las referencias sumamos las piezas.
				if ($id_referencia == $referencias_componente_final[$j]["id_referencia"]){
					$referencias_aux[$j]["piezas"] = $referencias_aux[$j]["piezas"] + $piezas; 
					$encontrado = true;
				}
				$j++;	
			}
			if (!$encontrado){
				// Si no esta la referencia la insertamos al final
				array_push($referencias_aux,$referencias_componente_secundario[$i]);
			}
			// Modificamos el array de referencias del componente por el array modificado con las referencias del kit
			unset($referencias_componente_final);
			$referencias_componente_final = $referencias_aux;
		}
		unset($referencias_aux);
		return $referencias_componente_final;	
	}

	// Funcion que devuelve las Referencias de las Ordenes de Compra de una Orden de Produccion
	function dameReferenciasCompra($id_produccion){
		$consulta = sprintf('select * from orden_compra_referencias where activo=1 and id_orden in 
			 					(select id_orden_compra from orden_compra where id_produccion=%s and activo=1)',	
			$this->makeValue($id_produccion,"int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();		
	}

	// Funcion que devuelve la ultima fecha de construccion iniciada
	function dameUltimaFechaConstruccionIniciada(){
		$consultaSql = "select (max(fecha_inicio_construccion)) as fecha_construccion from orden_produccion where activo=1 and estado='INICIADO' order by fecha_inicio_construccion desc ";
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();
	}

	

	// Devuelve la cadena de un error según su identificador
	function getErrorMessage($error_num) {
		switch($error_num) {
			case 2:
				return 'Se produjo un error al guardar la nueva orden de producción<br/>';
			break;
			case 3:
				return 'Se produjo un error al modificar la nueva orden de producción<br/>';
			break;
			case 4:
				return 'Se produjo un error al eliminar los datos de la orden de producción<br/>';
			break;
			case 5:
				# code
			break;
			case 6:
				return 'Se produjo un error al iniciar la orden de producción<br/>';
			break;
			case 7:
				return 'Inserte una fecha correcta con formato (DD/MM/AAAA)<br/>';
			break;
			case 8:
				return 'No se cambiaron los estados a estado BORRADOR<br/>';
			break;
			case 9:
				return 'No se cambió a BORRADOR el estado del producto<br/>';
			break;	
			case 10:
				return 'No se desactivó el producto<br/>';
			break;
			case 11:
				return 'No se desactivó la Orden de Producción<br/>';
			break;	
			case 12:
				return 'No se insertó el código de la Orden de Producción<br/>';
			break;
			case 13:
				return 'Se produjo un error al desactivar las Ordenes de Compra de la Orden de Producción a modificar<br/>';
			break;	
			case 14:
				return 'Se produjo un error al actualizar el estado de la Orden de Producción';
			break;	
			case 15:
				return 'No se guardó el alias de la Orden de Producción<br/>';
			break;
			case 16:
				return 'Se produjo un error finalizar los productos de la Orden de Producción finalizada<br/>';
			break;
			case 17:
				return 'Se produjo un error al guardar los componentes de la Orden de Producción<br/>';
			break;
			case 18:
				return 'Se produjo un error al guardar las referencias de la Orden de Producción<br/>';
			break;
			case 19:
				return 'Se produjo un error al desactivar los componentes de la Orden de Producción<br/>';
			break;
			case 20:
				return 'Se produjo un error al desactivar las referencias de la Orden de Producción<br/>';
			break;
			case 21:
				return 'Se produjo un error al recibir todas las piezas de la Orden de Producción<br/>';
			break;
			case 22:
				return 'Se produjo un error al actualizar las fechas de entrega de la Orden de Producción<br/>';
			break;
			case 23:
				return 'Se produjo un error al actualizar la fecha de inicio de construcción de la Orden de Producción<br/>';
			break;	
		}
	}
}
?>