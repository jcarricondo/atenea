<?php
class listadoDirecciones extends MySQL {
	
	// Variables de la clase
	var $id_direccion; 
	var $nombre_empresa;
	var $cif;
	var $direccion;
	var $codigo_postal;
	var $localidad;
	var $provincia;
	var $telefono;
	var $tipo;
	var $persona_contacto;
	var $fecha_desde;
	var $fecha_hasta;

	var $fecha_creado;
	var $activo;
	
	var $consultaSql = "";
	var $direcciones = NULL;
	
	
	// Pasan los valores de las variables del buscador a las de la clase
	function setValores($nombre_empresa,$cif,$direccion,$codigo_postal,$localidad,$provincia,$telefono,$tipo,$persona_contacto,$fecha_desde,$fecha_hasta) {
	 	$this->nombre_empresa = $nombre_empresa;
		$this->cif = $cif;
		$this->direccion = $direccion;
		$this->codigo_postal = $codigo_postal;
		$this->localidad = $localidad;
		$this->provincia = $provincia;
		$this->telefono = $telefono;
		$this->tipo = $tipo;
		$this->persona_contacto = $persona_contacto;
		$this->fecha_desde = $fecha_desde;
		$this->fecha_hasta = $fecha_hasta;
		
		$this->prepararConsulta();
	}
	
	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
		$campos = "select id_direccion from direcciones where id_direccion is not null and activo=1 ";
		if($this->nombre_empresa != "") {
			$condiciones .= " and nombre_empresa like '%".$this->nombre_empresa."%'";
		}
		if($this->cif != "") {
			$condiciones .= " and cif like '%".$this->cif."%'";
		}
		if($this->direccion != "") {
			$condiciones .= " and direccion like '%".$this->direccion."%'";
		}
		if($this->codigo_postal != "") {
			$condiciones .= " and cp like '%".$this->codigo_postal."%'";
		}
		if($this->localidad != "") {
			$condiciones .= " and localidad like '%".$this->localidad."%'";
		}
		if($this->provincia != "") {
			$condiciones .= " and provincia like '%".$this->provincia."%'";
		}
		if($this->telefono != "") {
			$condiciones .= " and telefono like '%".$this->telefono."%'";
		}
		if($this->tipo != "") {
			$condiciones .= " and tipo_direccion like '%".$this->tipo."%'";
		}
		if($this->persona_contacto != "") {
			$condiciones .= " and persona_contacto like '%".$this->persona_contacto."%'";
		}
		if($this->fecha_desde != "") {
			$condiciones .= " and fecha_creado >= '".$this->fecha_desde."' ";
		}
		if($this->fecha_hasta != ""){
			$condiciones .= " and fecha_creado <= '".$this->fecha_hasta."' ";	
		}
		
		$ordenado = " order by direcciones.nombre_empresa ";
		
		$this->consultaSql = $campos.$condiciones.$ordenado;
	}
	
	function prepararConsultaDireccionesEntrega() {
		$campos = "select id_direccion from direcciones where id_direccion is not null and activo=1 and tipo_direccion=0 ";
		$this->consultaSql = $campos;
		}
		
	function prepararConsultaDireccionesFacturacion() {
		$campos = "select id_direccion from direcciones where id_direccion is not null and activo=1 and tipo_direccion=1 ";
		$this->consultaSql = $campos;
		}
	
	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->direcciones = $this->getResultados();
	}
	
	function realizarConsultaDireccionesEntrega() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->direcciones = $this->getResultados();
	}
	
	function realizarConsultaDireccionesFacturacion() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->direcciones = $this->getResultados();
	}
}
?>