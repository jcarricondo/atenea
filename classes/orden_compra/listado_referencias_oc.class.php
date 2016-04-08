<?php
class listadoReferenciasOC extends MySQL {
	
	// Clase que nos servirá para hacer una consulta y mostrar las referencias de la orden de produccion de esa orden de compra
	
	// Variables de la clase
	var $id_compra = "";
	var $id_proveedor = "";
	
	var $consultaSql = "";
	var $referencias_OC = NULL;
	var $referencias_OC_FR = NULL;
	
	
	// Pasan el valor del id_producto a la clase
	function setValores($id_compra,$id_proveedor) {
		$this->id_compra = $id_compra;
		$this->id_proveedor = $id_proveedor;
			
		$this->prepararConsulta();
	}
	
	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
		//$campos = "select * from orden_compra_referencias where orden_compra_referencias.activo=1 "; 
		$campos = "select orden_compra_referencias.*,proveedores.nombre_prov from orden_compra_referencias inner join referencias on (orden_compra_referencias.id_referencia=referencias.id_referencia) inner join proveedores on (proveedores.id_proveedor=referencias.id_proveedor) where orden_compra_referencias.activo=1 and referencias.id_proveedor=".$this->id_proveedor;
		$condiciones .= " and orden_compra_referencias.id_orden=".$this->id_compra." group by referencias.id_referencia";
		$ordenado .= " order by coste DESC";
		//$ordenado .= " group by referencias.id_referencia order by referencias.referencia";
		
		// select orden_compra_referencias.*,proveedores.nombre_prov from orden_compra_referencias inner join referencias on (orden_compra_referencias.id_referencia=referencias.id_referencia) inner join proveedores on (proveedores.id_proveedor=referencias.id_proveedor) where orden_compra_referencias.activo=1 and referencias.id_proveedor = 1 
			 																			  				
		$this->consultaSql = $campos.$condiciones.$ordenado;
	}
	
	function setValoresFRA_REQ($id_compra,$id_proveedor) {
		$this->id_compra = $id_compra;
		$this->id_proveedor = $id_proveedor;
			
		$this->prepararConsultaFRA_REQ();
	}
	
	
	// Prepara la cadena para la consulta a la base de datos
	function prepararConsultaFRA_REQ() {
		$campos = "select orden_compra_referencias.*,proveedores.nombre_prov,referencias.referencia,referencias.part_proveedor_referencia,referencias.part_nombre from orden_compra_referencias inner join referencias on (orden_compra_referencias.id_referencia=referencias.id_referencia) inner join proveedores on (proveedores.id_proveedor=referencias.id_proveedor) where orden_compra_referencias.activo=1";
		$condiciones .= " and id_orden=".$this->id_compra;
		$this->consultaSql = $campos.$condiciones;
	}
	

	
	
	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->referencias_OC = $this->getResultados();
	}
	
	function realizarConsultaFRA_REQ() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->referencias_OC_FR = $this->getResultados();
	}
	
}
?>