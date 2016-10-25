<?php
class listadoImputaciones extends MySQL {
	
	// Variables de la clase
	var $id = "";
	var $id_usuario = "";
	var $orden_produccion = "";
	var $tipo_trabajo = "";
	var $horas = "";
	var $fecha = "";
	var $descripcion = "";
	var $fecha_grabado = "";
	var $fecha_desde = "";
	var $fecha_hasta = "";
	
	var $consultaSql = "";
	var $imputaciones = NULL;
	
	
	// Pasan los valores de las variables del buscador a las de la clase
	function setValores($id_usuario,$orden_produccion,$tipo_trabajo,$fecha_desde,$fecha_hasta) {
		$this->id_usuario = $id_usuario;
		$this->orden_produccion = $orden_produccion;
		$this->tipo_trabajo = $tipo_trabajo;
		$this->fecha_desde = $fecha_desde;
		$this->fecha_hasta = $fecha_hasta;
		
		$this->prepararConsulta();
	}
	
	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
		$campos = "select id from imputaciones as i inner join productos as p on (p.id_produccion=i.id_orden_produccion) where i.id is not null ";
		if($this->id_usuario != "") {
			$condiciones .= sprintf(" and i.id_usuario=%s",
				$this->makeValue($this->id_usuario, "int"));
		}
		if($this->orden_produccion != "") {
			//$condiciones .= " and p.codigo like '%".$this->orden_produccion."%'";
			$condiciones .= " and p.num_serie like '%".$this->orden_produccion."%'";
		}
		if($this->tipo_trabajo != 0) {
			$condiciones .= sprintf(" and tipo_trabajo=%s",
				$this->makeValue($this->tipo_trabajo, "int"));
		}
		if($this->fecha_desde != "") {
			$condiciones .= " and i.fecha >= '".$this->fecha_desde."' ";
		}
		if($this->fecha_hasta != ""){
			$condiciones .= " and i.fecha <= '".$this->fecha_hasta."' ";	
		}
		
		$ordenado = " group by i.id_orden_produccion,i.id order by i.fecha DESC";
		
		$this->consultaSql = $campos.$condiciones.$ordenado;		
	}
	
	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->imputaciones = $this->getResultados();
	}
}
?>