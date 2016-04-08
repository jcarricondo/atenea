<?php
class listadoFabricantes extends MySQL {
	
	// Variables de la clase
	var $fabricante = "";
	var $direccion = "";
	var $telefono = "";
	var $email = "";
	var $ciudad = "";
	var $pais = "";
	var $fecha_desde = "";
	var $fecha_hasta = "";
	
	var $consultaSql = "";
	var $fabricantes = NULL;
	
	
	// Pasan los valores de las variables del buscador a las de la clase
	function setValores($fabricante,$direccion,$telefono,$email,$ciudad,$pais,$fecha_desde,$fecha_hasta) {
		$this->fabricante = $fabricante;
		$this->direccion = $direccion;
		$this->telefono = $telefono;
		$this->email = $email;
		$this->ciudad = $ciudad;
		$this->pais = $pais;
		$this->fecha_desde = $fecha_desde;
		$this->fecha_hasta = $fecha_hasta;
		
		$this->prepararConsulta();
	}
	
	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
		$campos = "select id_fabricante from fabricantes where id_fabricante is not null and activo=1 ";
		if($this->fabricante != "") {
			$condiciones .= "and nombre_fab like '%".$this->fabricante."%'";
		}
		if($this->direccion != "") {
			$condiciones .= "and direccion like '%".$this->direccion."%'";
		}
		if($this->telefono != "") {
			$condiciones .= "and telefono like '%".$this->telefono."%'";
		}
		if($this->email != "") {
			$condiciones .= "and email like '%".$this->email."%'";
		}
		if($this->ciudad != "") {
			$condiciones .= "and ciudad like '%".$this->ciudad."%'";
		}
		if($this->pais != "") {
			$condiciones .= "and pais like '%".$this->pais."%'";
		}
		if($this->fecha_desde != "") {
			$condiciones .= "and fabricantes.fecha_creado >= '".$this->fecha_desde."' ";
		}
		if($this->fecha_hasta != ""){
			$condiciones .= "and fabricantes.fecha_creado <= '".$this->fecha_hasta."' ";	
		}
		
		$ordenado = " order by fabricantes.nombre_fab ";
		
		$this->consultaSql = $campos.$condiciones.$ordenado;
	}
	
	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->fabricantes = $this->getResultados();
	}
}
?>