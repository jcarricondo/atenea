<?php
class Pedido extends MySQL {

	var $id_pedido;
	var $id_cliente;
	var $id_producto;
	var $numero_pedido;
	var $unidades;
	var $fecha_pedido;
	var $fecha_entrega_estimada;
	var $fecha_entrega_planificada;
	var $fecha_entrega;
	var $estado;

	function setValores($id_pedido,$id_cliente,$id_producto,$numero_pedido,$unidades,$fecha_entrega_estimada,$fecha_entrega_planificada,$fecha_entrega,$estado,$fecha_pedido) {
		$this->id_pedido = $id_pedido;
		$this->id_cliente = $id_cliente;
		$this->id_producto = $id_producto;
		$this->numero_pedido = $numero_pedido;
		$this->unidades = $unidades;
		$this->fecha_entrega_estimada = $fecha_entrega_estimada;
		$this->fecha_entrega_planificada = $fecha_entrega_planificada;
		$this->fecha_entrega = $fecha_entrega;
		$this->estado = $estado;
		$this->fecha_pedido = $fecha_pedido;
	}

	function cargarPedidoId($id_pedido) {
		$consulta = sprintf("select * from orden_pedido where id_pedido=%s",
			$this->makeValue($id_pedido, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$datos = $this->getPrimerResultado();
		$this->setValores($datos["id_pedido"],
			$datos["id_cliente"],
			$datos["id_producto"],
			$datos["numero_pedido"],
			$datos["unidades"],
			$datos["fecha_entrega_estimada"],
			$datos["fecha_entrega_planificada"],
			$datos["fecha_entrega"],
			$datos["estado"],
			$datos["fecha_pedido"]);
	}

	function guardarCambios() {
		if($this->id_pedido == NULL) {
			// Nuevo Pedido
			// Se comprueba si ya existe le número de pedido en la base de datos
			$consultaSql = sprintf("select id_pedido from orden_pedido where numero_pedido=%s and activo=1",
				$this->makeValue($this->numero_pedido, "text"));
			$this->setConsulta($consultaSql);
			$this->ejecutarSoloConsulta();
			if($this->getNumeroFilas() > 0) {
				return 3;
			} else {
				$consultaSql = sprintf("insert into orden_pedido (id_cliente,id_producto,numero_pedido,fecha_pedido,fecha_entrega_estimada,fecha_entrega_planificada,fecha_entrega,unidades,estado,activo) values (%s,%s,%s,current_timestamp,%s,%s,%s,%s,%s,1)",
					$this->makeValue($this->id_cliente, "int"),
					$this->makeValue($this->id_producto, "int"),
					$this->makeValue($this->numero_pedido, "text"),
					$this->makeValue($this->fecha_entrega_estimada, "text"),
					$this->makeValue($this->fecha_entrega_planificada, "text"),
					$this->makeValue($this->fecha_entrega, "text"),
					$this->makeValue($this->unidades, "int"),
					$this->makeValue($this->estado, "text"));
				$this->setConsulta($consultaSql);
				if($this->ejecutarSoloConsulta()) {
					$this->id_pedido = $this->getUltimoID();
					return 1;
				} else {
					return 2;
				}
			}
		} else {
			// Modificación de pedido
			$updateSql = sprintf("update orden_pedido set id_cliente=%s, id_producto=%s, numero_pedido=%s, fecha_entrega_estimada=%s, fecha_entrega_planificada=%s, unidades=%s where id_pedido=%s",
				$this->makeValue($this->id_cliente, "int"),
				$this->makeValue($this->id_producto, "int"),
				$this->makeValue($this->numero_pedido, "text"),
				$this->makeValue($this->fecha_entrega_estimada, "text"),
				$this->makeValue($this->fecha_entrega_planificada, "text"),
				$this->makeValue($this->unidades, "int"),
				$this->makeValue($this->id_pedido, "int"));
			$this->setConsulta($updateSql);
			if($this->ejecutarSoloConsulta()) {
				return 1;
			} else {
				return 4;
			}
		}
	}

	function eliminarPedido() {
		$updateSql = sprintf("update orden_pedido set activo=0 where id_pedido=%s",
			$this->makeValue($this->id_pedido, "int"));
		$this->setConsulta($updateSql);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		} else {
			return 5;
		}
	}

	// Funcion que actualiza la fecha de entrega cuando el producto ha sido entregado
	function actualizarFechaEntregaPedido($id_pedido){
		$updateSql = sprintf("update orden_pedido set fecha_entrega=current_timestamp where id_pedido=%s",
			$this->makeValue($id_pedido, "int"));
		$this->setConsulta($updateSql);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		}
		else {
			return 6;
		}
	}

	// Funcion que comprueba si el pedido esta en estado ENTREGADO
	function comprobarPedidoEntregado($id_pedido){
		$consultaSql = sprintf("select id_pedido from orden_pedido where id_pedido=%s and estado='ENTREGADO' and activo=1 ",
			$this->makeValue($id_pedido, "int"));
		$this->setConsulta($consultaSql);
		if($this->ejecutarSoloConsulta()){
			if($this->getNumeroFilas() > 0){
				// El pedido esta entregado
				return 1;
			}
			else {
				// El pedido todavia no esta en estado ENTREGADO
				return 8;
			}
		}
		else{
			return 7;
		}
	}

	// Devuelve la cadena de un error según su identificador
	function getErrorMessage($error_num) {
		switch($error_num) {
			case 2:
				return 'Se ha producido un error al guardar el pedido<br/>';
			break;
			case 3:
				return 'El número de pedido ya existe en la base de datos<br/>';
			break;
			case 4:
				return 'Se ha producido un error al modificar el pedido<br/>';
			break;
			case 5:
				return 'Se ha producido un error al eliminar el pedido<br/>';
			break;
			case 6:
				return 'Se ha producido un error al actualizar la fecha de entrega del pedido<br/>';
			break;
			case 7:
				return 'Se ha producido un error al comprobar el estado del pedido<br/>';
			break;
			case 8:
				return 'El pedido todavia no se encuentra en estado ENTREGADO<br/>';
			break;
		}
	}

}
?>