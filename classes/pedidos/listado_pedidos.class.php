<?php
class listadoPedidos extends MySQL {

	var $numero_pedido;
	var $id_cliente;
	var $id_producto;
	var $fecha_pedido;
	var $fecha_entrega_estimada;
	var $fecha_entrega_planificada;
	var $fecha_entrega;
	var $estado;

	var $resultados;

	function setValores($numero_pedido,$id_cliente,$id_producto,$fecha_pedido,$fecha_entrega_estimada,$fecha_entrega_planificada,$fecha_entrega,$estado) {
		$this->numero_pedido = $numero_pedido;
		$this->id_cliente = $id_cliente;
		$this->id_producto = $id_producto;
		$this->fecha_pedido = $fecha_pedido;
		$this->fecha_entrega_estimada = $fecha_entrega_estimada;
		$this->fecha_entrega_planificada = $fecha_entrega_planificada;
		$this->fecha_entrega = $fecha_entrega;
		$this->estado = $estado;
	}

	function buscarPedidosCliente($id_cliente) {
		$consulta = sprintf("select id_pedido from orden_pedido where activo=1 and id_cliente=%s and estado!='ENTREGADO'",
			$this->makeValue($id_cliente, "int"));
		$consulta .= " order by fecha_pedido desc,id_pedido desc ";
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->resultados = $this->getResultados();
	}

	function realizarConsulta() {
		$consulta = "select id_pedido from orden_pedido where activo=1";
		
		if($this->numero_pedido != "") {
			$consulta .= sprintf(" and numero_pedido=%s",
				$this->makeValue($this->numero_pedido, "text"));
		}

		if($this->id_cliente != 0) {
			$consulta .= sprintf(" and id_cliente=%s",
				$this->makeValue($this->id_cliente, "int"));
		}

		if($this->id_producto != 0) {
			$consulta .= sprintf(" and id_producto=%s",
				$this->makeValue($this->id_producto, "int"));
		}

		if($this->fecha_pedido != "") {
			$consulta .= sprintf(" and fecha_pedido=%s",
				$this->makeValue($this->fecha_pedido, "text"));
		}

		if($this->fecha_entrega_estimada != "") {
			$consulta .= sprintf(" and fecha_entrega_estimada=%s",
				$this->makeValue($this->fecha_entrega_estimada, "text"));
		}

		if($this->fecha_entrega_planificada != ""){
			$consulta .= sprintf(" and fecha_entrega_planificada=%s", 
				$this->makeValue($this->fecha_entrega_planificada, "text"));
		}		

		if($this->fecha_entrega != ""){
			$consulta .= sprintf(" and fecha_entrega=%s", 
				$this->makeValue($this->fecha_entrega, "text"));
		}

		if($this->estado != "") {
			$consulta .= sprintf(" and estado=%s",
				$this->makeValue($this->estado, "text"));
		}

		$consulta .= " order by fecha_pedido desc,id_pedido desc ";
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->resultados = $this->getResultados();

	}

}
?>