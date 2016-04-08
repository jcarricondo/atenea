<?php
class listadoReferenciasArchivos extends MySQL {
	
	// Variables de la clase
	var $id_referencia;
	var $consultaSql = "";
	var $referencias_archivos = NULL;
	
	function setValores($id_referencia) {
		$this->id_referencia = $id_referencia;
		$this->prepararConsulta();
	}
	
		
	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
		$this->consultaSql = "select id_archivo from referencias_archivos where referencias_archivos.id_referencia=".$this->id_referencia." and activo=1 ";
	}
	
		
	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->referencias_archivos = $this->getResultados();
	}
		
}
?>