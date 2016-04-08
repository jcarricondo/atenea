<?php
class listadoReferenciasComponentes extends MySQL {
	
	// Variables de la clase
	var $id_producto;
	var $id_componente;
	var $id_tipo_componente;
	var $id_producto_componente;
	var $consultaSql = "";
	var $referencias_componentes = NULL;
	
	function setValores($id_componente) {
		$this->id_componente = $id_componente;
		$this->prepararConsulta();
	}
	
	function setValoresReferencias($id_componente) {
		$this->id_componente = $id_componente;
		$this->prepararConsultaReferencias();	
	}
		
	// Prepara la cadena para la consulta a la base de datos. Ordenado por proveedor y luego por precio mas alto
	// Realiza la consulta sobre la tabla componentes_referencias, calculando el precio total de la referencia. El precio total de la 
	// referencia se calcula a partir de el precio del pack y el numero de unidades. Estos datos los cogemos de la tabla referencias
	function prepararConsulta() {
		$this->consultaSql = 'select id, (referencias.pack_precio / referencias.unidades) * piezas as precio from componentes_referencias inner join referencias on (componentes_referencias.id_referencia = referencias.id_referencia) inner join proveedores on (referencias.id_proveedor = proveedores.id_proveedor) where componentes_referencias.id_componente='.$this->id_componente.' and componentes_referencias.activo=1 order by referencias.part_tipo="ORDENADOR" DESC,proveedores.nombre_prov, precio DESC';
	} 

	function prepararConsultaReferencias() {
		$this->consultaSql = "select componentes_referencias.id_referencia from componentes_referencias inner join referencias on (componentes_referencias.id_referencia = referencias.id_referencia) where componentes_referencias.id_componente=".$this->id_componente." and componentes_referencias.activo=1 order by referencias.referencia";
	}

	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->referencias_componentes = $this->getResultados();
	}

	function realizarConsultaReferencias() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->referencias_componentes = $this->getResultados();
	}

}
?>