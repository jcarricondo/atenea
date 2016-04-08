<?php
class Cliente extends MySQL {
	
	var $id_cliente; // ID de cliente
	var $nombre; // Nombre del cliente
	var $telefono; // Teléfono del cliente
	var $email; // Email del cliente
	var $direccion; // Dirección del cliente
	var $cp; // Codigo postal del cliente
	var $ciudad; // Ciudad del cliente
	var $pais; // País del cliente
	var $fecha_alta; // Fecha de alta del cliente
	var $activo;
	
	function cargarDatos($id_cliente,$nombre,$telefono,$email,$direccion,$cp,$ciudad,$pais,$fecha_alta) {
		$this->id_cliente = $id_cliente;
		$this->nombre = $nombre;
		$this->telefono = $telefono;
		$this->email = $email;
		$this->direccion = $direccion;
		$this->cp = $cp;
		$this->ciudad = $ciudad;
		$this->pais = $pais;
		$this->fecha_alta = $fecha_alta;
	}
	
	function cargaDatosClienteId($id_cliente) {
		$consultaSql = sprintf("select * from clientes where id_cliente=%s",
			$this->makeValue($id_cliente, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id_cliente"],
			$resultados["nombre"],
			$resultados["telefono"],
			$resultados["email"],
			$resultados["direccion"],
			$resultados["cp"],
			$resultados["ciudad"],
			$resultados["pais"],
			$resultados["fecha_alta"]
		);
	}
	
	// Se hace la carga de datos  del nuevo cliente
	function datosNuevoCliente($id_cliente = NULL,$nombre,$direccion,$cp,$ciudad,$pais,$telefono,$email) {
		$this->id_cliente = $id_cliente;
		$this->nombre = $nombre;
		$this->direccion = $direccion;
		$this->cp = $cp;
		$this->ciudad = $ciudad;
		$this->pais = $pais;
		$this->telefono = $telefono;
		$this->email = $email;
	}
	
	// Guarda los cambios realizados en el cliente
	function guardarCambios() {
		// Si el id_cliente es NULL lo toma como un nuevo cliente
		if($this->id_cliente == NULL) {
			// Comprueba si hay otro cliente con el mismo nombre
			if(!$this->comprobarClienteDuplicado()) {
				$consulta = sprintf("insert into clientes (nombre,direccion,cp,ciudad,pais,telefono,email,fecha_creado,activo) value (%s,%s,%s,%s,%s,%s,%s,current_timestamp,1)",
					$this->makeValue($this->nombre, "text"),
					$this->makeValue($this->direccion, "text"),
					$this->makeValue($this->cp, "int"),
					$this->makeValue($this->ciudad, "text"),
					$this->makeValue($this->pais, "text"),
					$this->makeValue($this->telefono, "text"),
					$this->makeValue($this->email, "text"));
				$this->setConsulta($consulta);
				if($this->ejecutarSoloConsulta()) {
					$this->id_cliente = $this->getUltimoID();
					return 1;
				} else {
					return 3;
				}
			} else {
				return 2;
				
			}
		} else {
			if(!$this->comprobarClienteDuplicado()) {
				$consulta = sprintf("update clientes set nombre=%s, direccion=%s, cp=%s, ciudad=%s, pais=%s, telefono=%s, email=%s where id_cliente=%s",
					$this->makeValue($this->nombre, "text"),
					$this->makeValue($this->direccion, "text"),
					$this->makeValue($this->cp, "int"),
					$this->makeValue($this->ciudad, "text"),
					$this->makeValue($this->pais, "text"),
					$this->makeValue($this->telefono, "int"),
					$this->makeValue($this->email, "text"),
					$this->makeValue($this->id_cliente, "int"));
				$this->setConsulta($consulta);
				if($this->ejecutarSoloConsulta()) {
					return 1;
				} else {
					return 4;
				}
			} else {
				return 2;
			}
		}
	}
	
	// Comprueba si hay otro cliente con el mismo nombre
	// Devuelve true si hay clientes duplicados
	function comprobarClienteDuplicado() {
		if($this->id_cliente == NULL) {
			$consulta = sprintf("select id_cliente from clientes where nombre=%s and activo=1",
				$this->makeValue($this->nombre, "text"));
		} else {	
			$consulta = sprintf("select id_cliente from clientes where nombre=%s and activo=1 and id_cliente<>%s",
				$this->makeValue($this->nombre, "text"),
				$this->makeValue($this->id_cliente, "int"));
		}
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		if($this->getNumeroFilas() == 0) {
			return false;	
		} else {
			return true;
		}
	}
	
	/*function eliminar(){
		//ELIMINAR
		$consulta = sprintf("delete from clientes where id_cliente=%s",	
			$this->makeValue($this->id_cliente, "int"));
			$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 6;
		} else {
			return 5;
		}
	}*/
	
	function eliminar(){
		//ELIMINAR
		$consulta = sprintf("update clientes set clientes.activo=0 where id_cliente=%s",	
			$this->makeValue($this->id_cliente, "int"));
			$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 6;
		} else {
			return 5;
		}
	}
	
	
	// Devuelve la cadena de un error según su identificador
	function getErrorMessage($error_num) {
		switch($error_num) {
			case 2:
				return 'Ya existe un cliente con ese nombre en la base de datos<br/>';
			break;
			case 3:
				return 'Se produjo un error al guardar el nuevo cliente<br/>';
			break;
			case 4:
				return 'Se produjo un error al modificar los datos del cliente<br/>';
			break;
			case 5:
				return 'Se produjo un error al eliminar el cliente</br>';
			break;	
		}
	}	
}
?>