<?php
class listadoFamilias extends MySQL {
	
	// Variables de la clase
	var $nombre = "";
	var $fecha_desde = "";
	var $fecha_hasta = "";
		
	var $consultaSql = "";
	var $familias = NULL;
	
	
	// Pasan los valores de las variables del buscador a las de la clase
	function setValores($nombre,$fecha_desde,$fecha_hasta) {
		$this->nombre = $nombre;
		$this->fecha_desde = $fecha_desde;
		$this->fecha_hasta = $fecha_hasta;

		$this->prepararConsulta();
	}
	
	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
		$campos = "select id_familia from familias where id_familia is not null and activo=1 ";
		if($this->nombre != "") {
			$condiciones .= "and nombre_familia like '%".$this->nombre."%'";
		}
		if($this->fecha_desde != "") {
			$condiciones .= "and familias.fecha_creado >= '".$this->fecha_desde."' ";
		}
		if($this->fecha_hasta != ""){
			$condiciones .= "and familias.fecha_creado <= '".$this->fecha_hasta."' ";	
		}

		$ordenado = " order by familias.nombre_familia ";

		$this->consultaSql = $campos.$condiciones.$ordenado;
	}
	
	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->familias = $this->getResultados();
	}
	
}
?>