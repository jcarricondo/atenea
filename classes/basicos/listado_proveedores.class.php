<?php
class listadoProveedores extends MySQL {
	
	// Variables de la clase
	var $proveedor = "";
	var $direccion = "";
	var $telefono = "";
	var $email = "";
	var $ciudad = "";
	var $pais = "";
	var $forma_pago = "";
	var $metodo_pago = "";
	var $tiempo_suministro = "";
	var $provincia = "";
	var $codigo_postal = "";
	var $persona_contacto = "";
	var $fecha_desde = "";
	var $fecha_hasta = "";
	var $paginacion = "";
	
	var $consultaSql = "";
	var $proveedores = NULL;
	
	
	// Pasan los valores de las variables del buscador a las de la clase
	function setValores($proveedor,$direccion,$telefono,$email,$ciudad,$pais,$forma_pago,$metodo_pago,$tiempo_suministro,$provincia,$codigo_postal,$persona_contacto,$fecha_desde,$fecha_hasta,$paginacion) {
		$this->proveedor = $proveedor;
		$this->direccion = $direccion;
		$this->telefono = $telefono;
		$this->email = $email;
		$this->ciudad = $ciudad;
		$this->pais = $pais;
		$this->forma_pago = $forma_pago;
		$this->metodo_pago = $metodo_pago;
		$this->tiempo_suministro = $tiempo_suministro;
		$this->provincia = $provincia;
		$this->codigo_postal = $codigo_postal;
		$this->persona_contacto = $persona_contacto;
		$this->fecha_desde = $fecha_desde;
		$this->fecha_hasta = $fecha_hasta;
		$this->paginacion = $paginacion;
			
		$this->prepararConsulta();
	}
	
	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
		$campos = "select id_proveedor from proveedores where id_proveedor is not null and activo=1 ";
		if($this->proveedor != "") {
			$condiciones .= "and nombre_prov like '%".$this->proveedor."%'";
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
		if($this->forma_pago != 0) {
			$condiciones .= "and forma_pago= ".$this->forma_pago." ";
		}
		if($this->metodo_pago != 0) {
			$condiciones .= "and metodo_pago= ".$this->metodo_pago." ";
		}
		if($this->tiempo_suministro != 0) {
			$condiciones .= "and tiempo_suministro= ".$this->tiempo_suministro." ";
		}
		if($this->provincia != "") {
			$condiciones .= "and provincia like '%".$this->provincia."%'";
		}
		if($this->codigo_postal != "") {
			$condiciones .= "and codigo_postal like '%".$this->codigo_postal."%'";
		}
		if($this->persona_contacto != "") {
			$condiciones .= "and persona_contacto like '%".$this->persona_contacto."%'";
		}
		if($this->fecha_desde != "") {
			$condiciones .= "and proveedores.fecha_creado >= '".$this->fecha_desde."' ";
		}
		if($this->fecha_hasta != ""){
			$condiciones .= "and proveedores.fecha_creado <= '".$this->fecha_hasta."' ";	
		}
		
		$ordenado = " order by proveedores.nombre_prov ";
		
		$this->consultaSql = $campos.$condiciones.$ordenado.$this->paginacion;
	}
	
	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->proveedores = $this->getResultados();
	}
}
?>