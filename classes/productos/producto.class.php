<?php
class Producto extends MySQL {
	
	var $id_producto; 
	var $id_produccion;
	var $id_nombre_producto;
	var $id_cliente;
	var $id_pedido;
	var $num_serie;
	var $num_ordenadores;
	var $estado_producto;
	var $fecha_entrega;
	var $fecha_entrega_prevista;
	var $fecha_creado;
	var $activo;
	var $id_contador_pedido;


	// Carga de datos de un producto ya existente en la base de datos	
	function cargarDatos($id_producto,$id_produccion,$id_nombre_producto,$id_pedido,$id_cliente,$num_serie,$num_ordenadores,$estado,$fecha_entrega,$fecha_entrega_prevista,$fecha_creado,$activo) {
		$this->id_producto = $id_producto;
		$this->id_produccion = $id_produccion;
		$this->id_nombre_producto = $id_nombre_producto;
		$this->id_pedido = $id_pedido;
		$this->id_cliente = $id_cliente;
		$this->num_serie = $num_serie;
		$this->num_ordenadores = $num_ordenadores;
		$this->estado_producto = $estado;
		$this->fecha_entrega = $fecha_entrega;
		$this->fecha_entrega_prevista = $fecha_entrega_prevista;
		$this->fecha_creado = $fecha_creado;
		$this->activo = $activo;
	}
	
	// Se obtienen los datos del producto en base a su ID
	function cargaDatosProductoId($id_producto) {
		$consultaSql = sprintf("select * from productos where id_producto=%s",
			$this->makeValue($id_producto, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id_producto"],
			$resultados["id_produccion"],
			$resultados["id_nombre_producto"],
			$resultados["id_pedido"],
			$resultados["id_cliente"],
			$resultados["num_serie"],
			$resultados["num_ordenadores"],
			$resultados["estado"],
			$resultados["fecha_entrega"],
			$resultados["fecha_entrega_prevista"],
			$resultados["fecha_creado"],
			$resultados["activo"]
		);
	}
	
	// Se hace la carga de datos del nuevo producto
	function datosNuevoProducto($id_producto = NULL,$id_produccion,$id_nombre_producto,$id_cliente = NULL,$num_serie) {
		$this->id_producto = $id_producto;
		$this->id_produccion = $id_produccion;
		$this->id_nombre_producto = $id_nombre_producto;
		$this->id_cliente = $id_cliente;
		$this->num_serie = $num_serie;
	}

	// Guarda los cambios realizados en el producto
	function guardarCambios() {		
	// Si el id_producto es NULL lo toma como un nuevo producto
		if($this->id_producto == NULL) {
			// Comprueba si hay otro producto con el mismo nombre
			if(!$this->comprobarProductoDuplicado()) {
				$consulta = sprintf("insert into productos (id_produccion,id_nombre_producto,id_cliente,num_serie,num_ordenadores,estado,fecha_entrega,fecha_entrega_prevista,fecha_creado,activo) value (%s,%s,%s,%s,0,'BORRADOR',current_timestamp,current_timestamp,current_timestamp,1)",
					$this->makeValue($this->id_produccion, "int"),
					$this->makeValue($this->id_nombre_producto, "int"),
					$this->makeValue($this->id_cliente, "int"),
					$this->makeValue($this->num_serie, "text"));	
				$this->setConsulta($consulta);
				if ($this->ejecutarSoloConsulta()) {
					return 1;
				}
				else return 2;
			} 
		}	
	}

	// Añade un cliente al producto y cambia el estado a INICIADO
	function iniciarProducto ($id_producto, $id_cliente) {
		$consulta = sprintf ("update productos set id_cliente=%s, estado='EN CONSTRUCCION' where id_producto=%s",
			$this->makeValue($id_cliente, "int"),
			$this->makeValue($id_producto, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) return 1;
		else return 3;
	}

	// Guarda el cliente que se ha asociado al producto
	function incluirCliente_en_Producto($id_producto,$id_cliente) {
		$consultaId = sprintf("update productos set productos.id_cliente=%s where productos.id_producto=%s ",
			$this->makeValue($id_cliente, "int"),
			$this->makeValue($id_producto, "int"));
		$this->setConsulta($consultaId);
		if ($this->ejecutarSoloConsulta()) return 1;
		else return 4;
	}

	// Cambia el estado del producto a ENTREGADO
	function entregaProducto ($id_producto, $id_cliente) {
		$consulta = sprintf ("update productos set estado='ENTREGADO' where id_producto=%s",
			$this->makeValue($id_producto, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) return 1;
		else return 5;
	}

	function consultarPedidosProducto($id_producto,$id_pedido) {
		// Se consulta el total de pedidos ya guardados de este producto
		$pedidosGuardados = 0;
		$pedidosSolicitados = 0;
		$consultaPedidosGuardadosSql = sprintf("select count(id) as total from productos_pedidos where id_pedido=%s",
			$this->makeValue($id_pedido, "int"));
		$this->setConsulta($consultaPedidosGuardadosSql);
		$this->ejecutarConsulta();
		$datos = $this->getPrimerResultado();
		$pedidosGuardados = $datos["total"] + 1;
		// Se consultan las unidades del pedido
		$consultaPedidoUnidadesSql = sprintf("select unidades from orden_pedido where id_pedido=%s",
			$this->makeValue($id_pedido, "int"));
		$this->setConsulta($consultaPedidoUnidadesSql);
		$this->ejecutarConsulta();
		$datos = $this->getPrimerResultado();
		$pedidosSolicitados = $datos["unidades"];
		if($pedidosGuardados < $pedidosSolicitados) {
			$estadoPedido = "PARCIALMENTE ENTREGADO";
		} else {
			$estadoPedido = "ENTREGADO";
		}
		// Se guarda el pedido
		$insertSql = sprintf("insert into productos_pedidos (id_producto,id_pedido,fecha_asignado) values (%s,%s,current_timestamp)",
			$this->makeValue($id_producto, "int"),
			$this->makeValue($id_pedido, "int"));
		$this->setConsulta($insertSql);
		if($this->ejecutarSoloConsulta()) {
			$consultaId = sprintf("update productos set id_cliente=%s, estado='ENTREGADO', id_pedido=%s, fecha_entrega=current_timestamp where id_producto=%s ",
				$this->makeValue($id_cliente, "int"),
				$this->makeValue($id_pedido, "int"),
				$this->makeValue($id_producto, "int"));
			$this->setConsulta($consultaId);
			if($this->ejecutarSoloConsulta()) {
				$updatePedidoSql = sprintf("update orden_pedido set estado=%s where id_pedido=%s",
					$this->makeValue($estadoPedido, "text"),
					$this->makeValue($id_pedido, "int"));
				$this->setConsulta($updatePedidoSql);
				if($this->ejecutarSoloConsulta()) {
					return 8;
				} else {
					return 9;
				}
			} else {
				return 7;
			}
		} else {
			return 6;
		}
	}

	// Devuelve el ultimo contador de la tabla contador_num_pedido
	function dameUltimoContadorPedido() {
		$consultaId = sprintf("select contador_num_pedido.id from contador_num_pedido order by id desc ");
		$this->setConsulta($consultaId);
		$this->ejecutarConsulta();
		$this->id_contador_pedido = $this->getPrimerResultado();	
	}

	// Inicia el contador de la tabla contador_num_pedido
	function IniciaContadorPedido($uno) {
		$consultaId = sprintf("insert into contador_num_pedido (nombre,valor) value ('NOMBRE_1','1')");
		$this->setConsulta($consultaId);
		$this->ejecutarConsulta();
		if ($this->ejecutarSoloConsulta()) {
			$this->id_contador_pedido = $this->getUltimoID();
		}
	}

	// Aumenta el contador de la tabla contador_num_pedido
	function AumentaContadorPedido($ultimo_id) {
		$ultimo_id++;	
		$consultaId = sprintf("insert into contador_num_pedido (nombre,valor) value ('NOMBRE_".$ultimo_id."','".$ultimo_id."')");
		$this->setConsulta($consultaId);
		if ($this->ejecutarSoloConsulta()) {
			$this->id_contador_pedido = $this->getUltimoID();
		}
	}

	// Comprueba si hay otro producto con el mismo nombre
	// Devuelve true si hay productos duplicados
	function comprobarProductoDuplicado() {
		if($this->id_producto == NULL) {
			$consulta = sprintf("select id_producto from productos where num_serie=%s and activo=1 and id_produccion=%s",
				$this->makeValue($this->num_serie, "text"),
				$this->makeValue($this->id_produccion, "int"));
		} else {	
			$consulta = sprintf("select id_producto from productos where num_serie=%s and activo=1 and id_producto<>%s and id_produccion=%s",
				$this->makeValue($this->num_serie, "text"),
				$this->makeValue($this->id_producto, "int"),
				$this->makeValue($this->id_produccion, "int"));
		}
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		if($this->getNumeroFilas() == 0) {
			return false;	
		} else {
			return true;
		}
	}


	// Devuelve el id del nombre de producto de un producto
	function dameIdsNombreProducto($id_producto) {
		$consultaId = sprintf("select productos.id_nombre_producto from productos where productos.id_producto=%s ",
			$this->makeValue($id_producto, "int"));
		$this->setConsulta($consultaId);
		$this->ejecutarConsulta();
		$this->id_nombre_producto = $this->getPrimerResultado();
	}

	// Devuelve el número de serie de un producto
	function dameNumSerie($id_producto) {
				$consulta = sprintf("select productos.num_serie from productos where productos.id_producto=%s ",
			$this->makeValue($id_producto, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->num_serie = $this->getPrimerResultado();
	}

	// Devuelve el id cliente de un producto		
	function dameIdCliente($id_producto) {
		$consultaId = sprintf("select productos.id_cliente from productos where productos.id_producto=%s ",
			$this->makeValue($id_producto, "int"));
		$this->setConsulta($consultaId);
		$this->ejecutarConsulta();
		$this->id_cliente = $this->getPrimerResultado();
	}


	// Devuelve la cadena de un error según su identificador
	function getErrorMessage($error_num) {
		switch($error_num) {
			case 2:
				return 'Se produjo un error al guardar información del producto en la base de datos<br/>';
			break;
			case 3:
				return 'Se produjo un error al iniciar el estado del producto<br/>';
			break;
			case 4:
				return 'Se produjo un error al incluir los clientes al producto<br/>';
			break;
			case 5:
				return 'Se produjo un error al cambiar el estado del producto a ENTREGADO<br/>';
			break;
			case 6:
				return 'Se ha producido un error al guardar la asignación';
			break;
			case 7:
				return 'Se ha producido un error al marcar el producto como entregado';
			break;
			case 8:
				return 'El producto se ha marcado como entregado correctamente';
			break;
			case 9:
				return 'Se ha producido un error al actualizar el pedido';
			break;
		}		
	}
}
?>