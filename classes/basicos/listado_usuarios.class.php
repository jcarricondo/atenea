<?php
class listadoUsuarios extends MySQL {
	
	// Variables de la clase
	var $usuario = "";
	var $email = "";
	var $fecha_creacion = "";
	var $fecha_login = "";
	var $id_tipo = "";
	var $fecha_desde = "";
	var $fecha_hasta = "";
	var $id_almacen = "";

	var $consultaSql = "";
	var $usuarios = NULL;
	
	
	// Pasan los valores de las variables del buscador a las de la clase
	function setValores($usuario,$email,$id_tipo,$fecha_desde,$fecha_hasta,$id_almacen) {
		$this->usuario = $usuario;
		$this->email = $email;
		$this->id_tipo = $id_tipo;
		$this->fecha_desde = $fecha_desde;
		$this->fecha_hasta = $fecha_hasta;
		$this->id_almacen = $id_almacen; 

		$this->prepararConsulta();
	}
	
	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
		$campos = "select id_usuario from usuarios where id_usuario is not null and activo=1 ";
		if($this->usuario != "") {
			$condiciones .= "and usuario like '%".$this->usuario."%'";
		}
		if($this->email != "") {
			$condiciones .= "and email like '%".$this->email."%'";
		}
		if($this->id_tipo != "") {
			$condiciones .= "and id_tipo=".$this->id_tipo;
		}
		if($this->fecha_desde != "") {
			$condiciones .= " and usuarios.fecha_creacion >= '".$this->fecha_desde."' ";
		}
		if($this->fecha_hasta != ""){
			$condiciones .= " and usuarios.fecha_creacion <= '".$this->fecha_hasta."' ";	
		}
		if($this->id_almacen != ""){
			$condiciones .= " and usuarios.id_almacen=".$this->id_almacen;
		}

		$ordenado = " order by usuarios.usuario "; 
		$this->consultaSql = $campos.$condiciones.$ordenado;
	}
	
	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->usuarios = $this->getResultados();
	}
	
}
?>