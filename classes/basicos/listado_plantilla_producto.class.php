<?php
class listadoPlantillaProducto extends MySQL {

    // Variables de la clase
	var $nombre = "";
	var $version = "";
    var $id_nombre_producto = "";
	var $fecha_desde = "";
	var $fecha_hasta = "";

	var $consultaSql = "";
	var $plantillas = NULL;
	
	
	// Pasan los valores de las variables del buscador a las de la clase
	function setValores($nombre,$version,$id_nombre_producto,$fecha_desde,$fecha_hasta) {
		$this->nombre = $nombre;
		$this->version = $version;
        $this->id_nombre_producto = $id_nombre_producto;
		$this->fecha_desde = $fecha_desde;
		$this->fecha_hasta = $fecha_hasta;

		$this->prepararConsulta();
	}
	
	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
        $condiciones = "";
		$campos = "select id_plantilla from plantilla_producto where id_plantilla is not null and activo=1";
		if($this->nombre != "") {
			$condiciones .= " and nombre like '%".$this->nombre."%'";
		}
		if($this->version != "") {
			$condiciones .= " and version like '%".$this->version."%'";
		}
		if($this->id_nombre_producto != "") {
			$condiciones .= " and id_nombre_producto=".$this->id_nombre_producto;
		}
		if($this->fecha_desde != "") {
			$condiciones .= " and fecha_creado >= '".$this->fecha_desde."' ";
		}
		if($this->fecha_hasta != ""){
			$condiciones .= " and fecha_creado <= '".$this->fecha_hasta."' ";
		}
		
		$ordenado = " order by nombre ";
		
		$this->consultaSql = $campos.$condiciones.$ordenado;
	}

	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->plantillas = $this->getResultados();
	}

}
?>