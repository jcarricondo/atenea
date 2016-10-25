<?php
class Familia extends MySql{
	
	var $id_familia; // ID de familia
	var $nombre; // Nombre

	var $fecha_creado;
	var $activo;
		
	function cargarDatos($id_familia,$nombre,$fecha_creado) {
		$this->id_familia = $id_familia;
		$this->nombre = $nombre;
		$this->fecha_creado = $fecha_creado;
	}
	
	function cargaDatosFamiliaId($id_familia) {
		$consultaSql = sprintf("select * from familias where id_familia=%s",
			$this->makeValue($id_familia, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id_familia"],
			$resultados["nombre_familia"],
			$resultados["fecha_creado"]
		);
	}
	
	// Se hace la carga de datos  de la nueva familia
	function datosNuevaFamilia($id_familia = NULL,$nombre) {
		$this->id_familia = $id_familia;
		$this->nombre = $nombre;
	}
	
	// Guarda los cambios realizados en la familia
	function guardarCambios() {
		// Si el id_familia es NULL lo toma como una nueva familia
		if($this->id_familia == NULL) {
			// Comprueba si hay otra familia con el mismo nombre
			if(!$this->comprobarFamiliaDuplicada()) {
				$consulta = sprintf("insert into familias (nombre_familia,fecha_creado,activo) value (%s,current_timestamp,1)",
					$this->makeValue($this->nombre, "text"));

				$this->setConsulta($consulta);
				if($this->ejecutarSoloConsulta()) {
					$this->id_familia = $this->getUltimoID();
					return 1;
				} else {
					return 3;
				}
			} else {
				return 2;
			}
		} else {
			if(!$this->comprobarFamiliaDuplicada()) {
				$consulta = sprintf("update familias set nombre_familia=%s where id_familia=%s",
					$this->makeValue($this->nombre, "text"),
					$this->makeValue($this->id_familia, "int"));
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
	
	// Comprueba si hay otra familia con el mismo nombre
	// Devuelve true si hay familias duplicados
	function comprobarFamiliaDuplicada() {
		if($this->id_familia == NULL) {
			$consulta = sprintf("select id_familia from familias where nombre_familia=%s",
				$this->makeValue($this->nombre, "text"));
		} else {	
			$consulta = sprintf("select id_familia from familias where nombre_familia=%s and id_familia<>%s",
				$this->makeValue($this->nombre, "text"),
				$this->makeValue($this->id_familia, "int"));
		}
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		if($this->getNumeroFilas() == 0) {
			return false;	
		} else {
			return true;
		}
	}
	
	function eliminar(){
		//ELIMINAR
		/*$consulta = sprintf("delete from familias where id_familia=%s",	
			$this->makeValue($this->id_familia, "int"));
			$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 6;
		} else {
			return 5;
		}*/
		$consulta = sprintf("update familias set activo=0 where id_familia=%s",	
			$this->makeValue($this->id_familia, "int"));
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
				return 'Ya existe una familia con ese nombre en la base de datos<br/>';
			break;
			case 3:
				return 'Se produjo un error al guardar la nueva familia<br/>';
			break;
			case 4:
				return 'Se produjo un error al modificar los datos de la familia<br/>';
			break;
			case 5:
				return 'Se produjo un error al eliminar la familia</br>';
			break;	
		}
	}			
}
?>