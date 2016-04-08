<?php
class Direccion extends MySQL {
	
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
	var $comentarios;

	var $fecha_creado;
	var $activo;
	

	// Carga de datos de una direccion ya existente en la base de datos	
	function cargarDatos($id_direccion,$nombre_empresa,$cif,$direccion,$cp,$localidad,$provincia,$telefono,$tipo_direccion,$persona_contacto,$comentarios,$fecha_creado,$activo) {
		$this->id_direccion = $id_direccion;
		$this->nombre_empresa = $nombre_empresa;
		$this->cif = $cif;
		$this->direccion = $direccion;
		$this->codigo_postal = $cp;
		$this->localidad = $localidad;
		$this->provincia = $provincia;
		$this->telefono = $telefono;
		$this->tipo = $tipo_direccion;
		$this->persona_contacto = $persona_contacto;
		$this->comentarios = $comentarios;
		$this->fecha_creado = $fecha_creado;
		$this->activo = $activo;
	}
	
	// Se obtienen los datos de la direccion en base a su ID
	function cargaDatosDireccionId($id_direccion) {
		$consultaSql = sprintf("select * from direcciones where id_direccion=%s",
			$this->makeValue($id_direccion, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id_direccion"],
			$resultados["nombre_empresa"],
			$resultados["cif"],
			$resultados["direccion"],
			$resultados["cp"],
			$resultados["localidad"],
			$resultados["provincia"],
			$resultados["telefono"],
			$resultados["tipo_direccion"],
			$resultados["persona_contacto"],
			$resultados["comentarios"],
			$resultados["fecha_creado"],
			$resultados["activo"]
		);
	}
	
	// Se hace la carga de datos  de la nueva direccion
	function datosNuevaDireccion($id_direccion = NULL, $nombre_empresa,$cif,$direccion,$codigo_postal,$localidad,$provincia,$telefono,$persona_contacto,$comentarios,$tipo) {
		$this->id_direccion = $id_direccion;
		$this->nombre_empresa = $nombre_empresa;
		$this->cif = $cif;
		$this->direccion = $direccion;
		$this->codigo_postal = $codigo_postal;
		$this->localidad = $localidad;
		$this->provincia = $provincia;
		$this->telefono = $telefono;
		$this->tipo = $tipo;
		$this->persona_contacto = $persona_contacto;
		$this->comentarios = $comentarios;
	}
	
	// Se hace la carga de datos de la modificación de la direccion
	function datosDireccion($id_direccion, $nombre_empresa,$cif,$direccion,$codigo_postal,$localidad,$provincia,$telefono,$persona_contacto,$comentarios,$tipo) {
		$this->id_direccion = $id_direccion;
		$this->nombre_empresa = $nombre_empresa;
		$this->cif = $cif;
		$this->direccion = $direccion;
		$this->codigo_postal = $codigo_postal;
		$this->localidad = $localidad;
		$this->provincia = $provincia;
		$this->telefono = $telefono;
		$this->tipo = $tipo;
		$this->persona_contacto = $persona_contacto;
		$this->comentarios = $comentarios;
	}
	
	// Guarda los cambios realizados en la direccion
	function guardarCambios() {
		// Si el id_proveedor es NULL lo toma como una direccion
		if($this->id_direccion == NULL) {
			// Comprueba si hay otra direccion con el mismo nombre
			if(!$this->comprobarDireccionDuplicada()) {
				$consulta = sprintf("insert into direcciones (nombre_empresa,cif,direccion,cp,localidad,provincia,telefono,persona_contacto,comentarios,tipo_direccion,fecha_creado,activo) value (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,current_timestamp,1)",
					$this->makeValue($this->nombre_empresa, "text"),
					$this->makeValue($this->cif, "text"),
					$this->makeValue(utf8_decode($this->direccion), "text"),
					$this->makeValue($this->codigo_postal, "int"),
					$this->makeValue($this->localidad, "text"),
					$this->makeValue($this->provincia, "text"),
					$this->makeValue($this->telefono, "text"),
					$this->makeValue($this->persona_contacto, "text"),
					$this->makeValue($this->comentarios, "text"),
					$this->makeValue($this->tipo, "int"));
				$this->setConsulta($consulta);
				if($this->ejecutarSoloConsulta()) {
					$this->id_direccion = $this->getUltimoID();
					return 1;
				} else {
					return 3;
					}
			} else {
				return 2;
				}
		} else {
			  if(!$this->comprobarDireccionDuplicada()) {
					$consulta = sprintf("update direcciones set nombre_empresa=%s, cif=%s, direccion=%s, cp=%s, localidad=%s, provincia=%s, telefono=%s, persona_contacto=%s, comentarios=%s, tipo_direccion=%s where id_direccion=%s",
						$this->makeValue($this->nombre_empresa, "text"),
						$this->makeValue($this->cif, "text"),
						$this->makeValue(utf8_decode($this->direccion), "text"),
						$this->makeValue($this->codigo_postal, "int"),
						$this->makeValue($this->localidad, "text"),
						$this->makeValue($this->provincia, "text"),
						$this->makeValue($this->telefono, "text"),
						$this->makeValue($this->persona_contacto, "text"),
						$this->makeValue($this->comentarios, "text"),
						$this->makeValue($this->tipo, "int"),
						$this->makeValue($this->id_direccion, "int"));
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
	
	// Comprueba si hay otra direccion con el mismo nombre
	// Devuelve true si hay direcciones duplicadas
	function comprobarDireccionDuplicada() {
		if($this->id_direccion == NULL) {
			$consulta = sprintf("select id_direccion from direcciones where nombre_empresa=%s and activo=1",
				$this->makeValue($this->nombre_empresa, "text"));
		} else {	
			$consulta = sprintf("select id_direccion from direcciones where nombre_empresa=%s and activo=1 and id_direccion<>%s",
				$this->makeValue($this->nombre_empresa, "text"),
				$this->makeValue($this->id_direccion, "int"));
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
		$consulta = sprintf("update direcciones set activo=0 where id_direccion=%s",	
			$this->makeValue($this->id_direccion, "int"));
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
				return 'Ya existe una direccion con ese nombre en la base de datos<br/>';
			break;
			case 3:
				return 'Se produjo un error al guardar la nueva dirección<br/>';
			break;
			case 4:
				return 'Se produjo un error al modificar los datos de la dirección<br/>';
			break;
			case 5:
				return 'Se produjo un error al eliminar la dirección<br/>';
			break;
		}
	}
	
}
?>