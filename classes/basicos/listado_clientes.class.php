<?php
class listadoClientes extends MySQL {
	
	// Variables de la clase
	var $cliente = "";
	var $telefono = "";
	var $email = "";
	var $direccion = "";
	var $codigo_postal = "";
	var $ciudad = "";
	var $pais = "";
	var $fecha_alta = "";
	var $fecha_desde = "";
	var $fecha_hasta = "";
	
	var $consultaSql = "";
	var $clientes = NULL;
	
	
	// Pasan los valores de las variables del buscador a las de la clase
	function setValores($cliente,$telefono,$email,$direccion,$codigo_postal,$ciudad,$pais,$fecha_alta,$fecha_desde,$fecha_hasta) {
		$this->cliente = $cliente;
		$this->telefono = $telefono;
		$this->email = $email;
		$this->direccion = $direccion;
		$this->codigo_postal = $codigo_postal;
		$this->ciudad = $ciudad;
		$this->pais = $pais;
		$this->fecha_alta = $fecha_alta;
		$this->fecha_desde = $fecha_desde;
		$this->fecha_hasta = $fecha_hasta;
	
		$this->prepararConsulta();
	}
	
	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
		$campos = "select id_cliente from clientes where id_cliente is not null and activo=1 ";
		if($this->cliente != "") {
			$condiciones .= "and nombre like '%".$this->cliente."%'";
		}
		if($this->telefono != "") {
			$condiciones .= "and telefono like '%".$this->telefono."%'";
		}
		if($this->email != "") {
			$condiciones .= "and email like '%".$this->email."%'";
		}
		if($this->direccion != "") {
			$condiciones .= "and direccion like '%".$this->direccion."%'";
		}
		if($this->codigo_postal != "") {
			$condiciones .= "and cp like '%".$this->codigo_postal."%'";
		}
		if($this->ciudad != "") {
			$condiciones .= "and ciudad like '%".$this->ciudad."%'";
		}
		if($this->pais != "") {
			$condiciones .= "and pais like '%".$this->pais."%'";
		}
		if($this->fecha_alta != "") {
			$condiciones .= "and fecha_alta like '%".$this->fecha_alta."%'";
		}
		if($this->fecha_desde != "") {
			$condiciones .= "and clientes.fecha_creado >= '".$this->fecha_desde."' ";
		}
		if($this->fecha_hasta != ""){
			$condiciones .= "and clientes.fecha_creado <= '".$this->fecha_hasta."' ";	
		}
		
		$ordenado = " order by clientes.nombre ";
		
		$this->consultaSql = $campos.$condiciones.$ordenado;
	}

	function consultaProductoPedido($producto) {
		$campos = "select id_cliente from clientes where id_cliente is not null and activo=1 ";
		$condiciones = " and id_cliente in (select id_cliente from orden_pedido where id_producto=".$producto.")";
		$ordenado = " order by clientes.nombre ";
		
		$this->consultaSql = $campos.$condiciones.$ordenado;
	}
	
	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->clientes = $this->getResultados();
	}
	
}
?>