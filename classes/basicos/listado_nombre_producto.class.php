<?php
class listadoNombreProducto extends MySQL {
	
	// Variables de la clase
	var $nombre = "";
	var $codigo = "";
	var $version = "";
	var $familia = "";
	var $fecha_desde = "";
	var $fecha_hasta = "";
		
	var $consultaSql = "";
	var $nombre_productos = NULL;
	
	
	// Pasan los valores de las variables del buscador a las de la clase
	function setValores($nombre,$codigo,$version,$familia,$fecha_desde,$fecha_hasta) {
		$this->nombre = $nombre;
		$this->codigo = $codigo;
		$this->version = $version;
		$this->familia = $familia;
		$this->fecha_desde = $fecha_desde;
		$this->fecha_hasta = $fecha_hasta;

		$this->prepararConsulta();
	}
	
	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
		$campos = "select id_nombre_producto from nombre_producto inner join familias on (familias.id_familia=nombre_producto.id_familia)where id_nombre_producto is not null and nombre_producto.activo=1 ";
		if($this->nombre != "") {
			$condiciones .= "and nombre like '%".$this->nombre."%'";
		}
		if($this->codigo != "") {
			$condiciones .= "and codigo like '%".$this->codigo."%'";
		}
		if($this->version != "") {
			$condiciones .= "and version like '%".$this->version."%'";
		}
		if($this->familia != "") {
			$condiciones .= "and familias.nombre_familia like '%".$this->familia."%'";
		}
		if($this->fecha_desde != "") {
			$condiciones .= "and nombre_producto.fecha_creado >= '".$this->fecha_desde."' ";
		}
		if($this->fecha_hasta != ""){
			$condiciones .= "and nombre_producto.fecha_creado <= '".$this->fecha_hasta."' ";	
		}
		
		$ordenado = " order by nombre_producto.nombre ";
		
		$this->consultaSql = $campos.$condiciones.$ordenado;
	}

	
	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->nombre_productos = $this->getResultados();
	}
	
}
?>