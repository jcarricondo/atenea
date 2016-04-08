<?php
class listadoInterfaces extends MySQL {
	
	// Variables de la clase
	var $interfaz = "";
	var $referencia = "";
	var $version = "";
	var $descripcion = "";
	var $estado = "";
	var $prototipo ="";
	var $fecha_desde = "";
	var $fecha_hasta = "";
		
	var $consultaSql = "";
	var $interfaces = NULL;
	
	
	// Pasan los valores de las variables del buscador a las de la clase
	function setValores($interfaz,$referencia,$version,$descripcion,$estado,$prototipo,$fecha_desde,$fecha_hasta) {
		$this->interfaz = $interfaz;
		$this->referencia = $referencia;
		$this->version = $version;
		$this->descripcion = $descripcion;
		$this->estado = $estado;
		$this->prototipo = $prototipo;
		$this->fecha_desde = $fecha_desde;
		$this->fecha_hasta = $fecha_hasta;
			
		$this->prepararConsulta();
	}
	
	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
		$campos = "select id_componente from componentes where id_componente is not null and id_tipo = '4' and componentes.activo=1 "; 
		
		if($this->interfaz != "") {
			$condiciones .= "and nombre like '%".$this->interfaz."%'";
		}
		if($this->referencia != "") {
			$condiciones .= "and referencia like '%".$this->referencia."%'";
		}
		if($this->version != "") {
			$condiciones .= "and version=".$this->version." ";
		}
		if($this->descripcion != "") {
			$condiciones .= "and descripcion like '%".$this->descripcion."%'";
		}
		if($this->estado != "") {
			$condiciones .= "and estado like '%".$this->estado."%'";
		}
		if($this->prototipo != "") {
			$condiciones .= "and prototipo=".$this->prototipo." ";
		}
		if($this->fecha_desde != "") {
			$condiciones .= "and fecha_creacion >= '".$this->fecha_desde."' ";
		}
		if($this->fecha_hasta != ""){
			$condiciones .= "and fecha_creacion <= '".$this->fecha_hasta."' ";	
		}


		$ordenado = " order by componentes.nombre ";
				
		$this->consultaSql = $campos.$condiciones.$ordenado;
	}
	
		
	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->interfaces = $this->getResultados();
	}
	
}
?>