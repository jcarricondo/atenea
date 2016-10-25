<?php
class Log_Unificacion extends MySql{
	
	var $id; 
	var $proceso; 
	var $comentarios;
	var $fecha;
	
	
	function cargarDatos($id,$proceso,$comentarios,$fecha) {
		$this->id = $id;
		$this->proceso = $proceso;
		$this->comentarios = $comentarios;
		$this->fecha = $fecha;
	}
	
	function cargaDatosLogId($id) {
		$consultaSql = sprintf("select * from log_unificacion where id=%s",
			$this->makeValue($id, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id"],
			$resultados["proceso"],
			$resultados["comentarios"],
			$resultados["fecha"]
		);
	}
	
	function datosNuevoLog($id = NULL,$proceso,$comentarios,$fecha) {
		$this->id = $id;
		$this->proceso = $proceso;
		$this->comentarios = $comentarios;
		$this->fecha = $fecha;
	}
		
	function guardarLog() {
		$insertSql = sprintf("insert into log_unificacion (proceso,comentarios,fecha) values (%s,%s,current_timestamp)",
			$this->makeValue($this->proceso, "text"),
			$this->makeValue($this->comentarios, "text"),
			$this->makeValue($this->fecha, "date"));
		$this->setConsulta($insertSql);
		if($this->ejecutarSoloConsulta()){
			return 1;
		}
		else {
			return 2;
		}
	}
		
}
?>