<?php
class listadoSoftwares extends MySQL {
	
	// Variables de la clase
	var $software = "";
	var $referencia = "";
	var $version = "";
	var $descripcion = "";
	var $fecha_desde = "";
	var $fecha_hasta = "";
		
	var $consultaSql = "";
	var $softwares = NULL;
	
	
	// Pasan los valores de las variables del buscador a las de la clase
	function setValores($software,$referencia,$version,$descripcion,$fecha_desde,$fecha_hasta) {
		$this->software = $software;
		$this->referencia = $referencia;
		$this->version = $version;
		$this->descripcion = $descripcion;
		$this->fecha_desde = $fecha_desde;
		$this->fecha_hasta = $fecha_hasta;
			
		$this->prepararConsulta();
	}
	
	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
		$campos = "select id_componente from componentes where id_componente is not null and id_tipo = '3' and activo=1 "; // Para que cuando se muestre el listado sin ningun campo de busqueda rellenado solo muestre los softwares  
		
		if($this->software != "") {
			$condiciones .= "and nombre like '%".$this->software."%'";
		}
		if($this->referencia != "") {
			$condiciones .= "and referencia like '%".$this->referencia."%'";
		}
		if($this->version != "") {
			$condiciones .= "and version like '%".$this->version."%'";
		}
		if($this->descripcion != "") {
			$condiciones .= "and descripcion like '%".$this->descripcion."%'";
		}
		if($this->fecha_desde != "") {
			$condiciones .= "and componentes.fecha_creacion >= '".$this->fecha_desde."' ";
		}
		if($this->fecha_hasta != ""){
			$condiciones .= "and componentes.fecha_creacion <= '".$this->fecha_hasta."' ";	
		}

		$ordenado = " order by componentes.nombre ";
				
		$this->consultaSql = $campos.$condiciones.$ordenado;
	}
	
	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->softwares = $this->getResultados();
	}
	
}
?>