<?php
class Software extends MySQL{
	
	var $id_componente;
	var $software;
	var $referencia;
	var $version;
	var $descripcion;

	var $id_tipo;
	
	function cargarDatos($id_componente,$software,$referencia,$version,$descripcion,$id_tipo) {
		$this->id_componente = $id_componente;
		$this->software = $software;
		$this->referencia = $referencia;
		$this->version = $version;
		$this->descripcion = $descripcion;

		$this->id_tipo = $id_tipo;
	}
	
	function cargaDatosSoftwareId($id_componente) {
		$consultaSql = sprintf("select * from componentes where id_componente=%s",
		$this->makeValue($id_componente, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id_componente"],
			$resultados["nombre"],
			$resultados["referencia"],
			$resultados["version"],
			$resultados["descripcion"],
			$resultados["id_tipo"]
		);
	}
	
	// Se hace la carga de datos  del nuevo software
	function datosNuevoSoftware($id_componente = NULL,$nombre,$referencia,$descripcion,$version,$id_tipo = 3) {
		$this->id_componente = $id_componente;
		$this->nombre = $nombre;
		$this->referencia = $referencia;
		$this->descripcion = $descripcion;
		$this->version = $version;
		$this->id_tipo = $id_tipo;
	}
	
	// Guarda los cambios realizados en el software
	function guardarCambios() {
		// Si el id_componente es NULL lo toma como un nuevo software
		if($this->id_componente == NULL) {
			// Comprueba si hay otro software con el mismo nombre
			if(!$this->comprobarSoftwareDuplicado()) {
				$consulta = sprintf("insert into componentes (nombre,referencia,descripcion,version,id_tipo,fecha_creacion,activo) value (%s,%s,%s,%s,%s,current_timestamp,1)",
					$this->makeValue($this->nombre, "text"),
					$this->makeValue($this->referencia, "text"),
					$this->makeValue($this->descripcion, "text"),
					$this->makeValue($this->version, "text"),
					$this->makeValue($this->id_tipo, "int"));
				$this->setConsulta($consulta);
				if($this->ejecutarSoloConsulta()) {
					$this->id_oomponente = $this->getUltimoID();
					return 1;
				} else {
					return 3;
				}
			} else {
				return 2;
			}
		} else {
			if(!$this->comprobarSoftwareDuplicado()) {
				$consulta = sprintf("update componentes set nombre=%s, referencia=%s, descripcion=%s, version=%s, id_tipo=%s where id_componente=%s",
					$this->makeValue($this->nombre, "text"),
					$this->makeValue($this->referencia, "text"),
					$this->makeValue($this->descripcion, "text"),
					$this->makeValue($this->version, "text"),
					$this->makeValue($this->id_tipo, "int"),
					$this->makeValue($this->id_componente, "int"));
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
	
	// Comprueba si hay otro software con el mismo nombre
	// Devuelve true si hay softwares duplicados
	function comprobarSoftwareDuplicado() {
		if($this->id_componente == NULL) {
			$consulta = sprintf("select id_componente from componentes where nombre=%s and activo=1 and id_tipo=3",
				$this->makeValue($this->nombre, "text"));
		} else {	
			$consulta = sprintf("select id_componente from componentes where nombre=%s and activo=1 and id_tipo=3 and id_componente<>%s",
				$this->makeValue($this->nombre, "text"),
				$this->makeValue($this->id_componente, "int"));
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
		$consulta = sprintf("delete from componentes where id_tipo=3 and id_componente=%s",	
			$this->makeValue($this->id_componente, "int"));
			$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 6;
		} else {
			return 5;
		}
	}*/
	
	function eliminar(){
		//ELIMINAR
		$consulta = sprintf("update componentes set activo=0 where id_tipo=3 and id_componente=%s",	
			$this->makeValue($this->id_componente, "int"));
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
				return 'Ya existe un software con ese nombre en la base de datos<br/>';
			break;
			case 3:
				return 'Se produjo un error al guardar el nuevo software<br/>';
			break;
			case 4:
				return 'Se produjo un error al modificar los datos del software<br/>';
			break;
			case 5:
				return 'Se produjo un error al eliminar el software<br/>';
			break;
		}
	}
}
?>
