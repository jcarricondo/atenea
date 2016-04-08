<?php
class CentroLogistico extends MySQL {

	var $id_centro_logistico;
	var $nombre;
	var $descripcion;
	var $direccion;
	var $ciudad;
	var $pais;
	var $telefono;
	var $email;
	var $forma_pago;
	var $tiempo_suministro;
	var $metodo_pago;
	var $provincia;
	var $codigo_postal;
	var $persona_contacto;
	var $fecha_creado;
	var $activo;


	// Carga de datos de un centro logistico ya existente en la base de datos
	function cargarDatos($id_centro_logistico,$nombre,$descripcion,$direccion,$ciudad,$pais,$telefono,$email,$forma_pago,$tiempo_suministro,$metodo_pago,$provincia,$codigo_postal,$persona_contacto,$fecha_creado,$activo) {
		$this->id_centro_logistico = $id_centro_logistico;
		$this->nombre = $nombre;
		$this->descripcion = $descripcion;
		$this->direccion = $direccion;
		$this->ciudad = $ciudad;
		$this->pais = $pais;
		$this->telefono = $telefono;
		$this->email = $email;
		$this->forma_pago = $forma_pago;
		$this->tiempo_suministro = $tiempo_suministro;
		$this->metodo_pago = $metodo_pago;
		$this->provincia = $provincia;
		$this->codigo_postal = $codigo_postal;
		$this->persona_contacto = $persona_contacto;
		$this->fecha_creado = $fecha_creado;
		$this->activo = $activo;
	}

	// Se obtienen los datos del centro logistico en base a su ID
	function cargaDatosCentroLogisticoId($id_centro_logistico) {
		$consultaSql = sprintf("select * from centros_logisticos where id_centro_logistico=%s",
			$this->makeValue($id_centro_logistico, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id_centro_logistico"],
			$resultados["centro_logistico"],
			$resultados["descripcion"],
			$resultados["direccion"],
			$resultados["ciudad"],
			$resultados["pais"],
			$resultados["telefono"],
			$resultados["email"],
			$resultados["forma_pago"],
			$resultados["tiempo_suministro"],
			$resultados["metodo_pago"],
			$resultados["provincia"],
			$resultados["codigo_postal"],
			$resultados["persona_contacto"],
			$resultados["fecha_creado"],
			$resultados["activo"]
		);
	}

	// Se comprueba si el centro logistico existe en la base de datos
	function getExisteCentroLogistico($nombre) {
		$consultaSql = sprintf("select id_centro_logistico from centros_logisticos where centro_logistico=%s and activo=1",
			$this->makeValue($nombre, "text"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		return $resultados["id_centro_logistico"];
	}

	// Se hace la carga de datos del nuevo centro logistico
	function datosNuevoCentroLogistico($id_centro_logistico = NULL,$nombre,$descripcion,$direccion,$ciudad,$pais,$telefono,$email,$forma_pago,$tiempo_suministro,$metodo_pago,$provincia,$codigo_postal,$persona_contacto) {
		$this->id_centro_logistico = $id_centro_logistico;
		$this->nombre = $nombre;
		$this->descripcion = $descripcion;
		$this->direccion = $direccion;
		$this->ciudad = $ciudad;
		$this->pais = $pais;
		$this->telefono = $telefono;
		$this->email = $email;
		$this->forma_pago = $forma_pago;
		$this->tiempo_suministro = $tiempo_suministro;
		$this->metodo_pago = $metodo_pago;
		$this->provincia = $provincia;
		$this->codigo_postal = $codigo_postal;
		$this->persona_contacto = $persona_contacto;
	}

	// Guarda los cambios realizados en el centro logistico
	function guardarCambios() {
		// Si el id_centro_logistico es NULL lo toma como un nuevo centro logistico
		if($this->id_centro_logistico == NULL) {
			// Comprueba si hay otro centro logistico con el mismo nombre
			if(!$this->comprobarCentroLogisticoDuplicado()) {
				$consulta = sprintf("insert into centros_logisticos (centro_logistico,descripcion,direccion,ciudad,pais,telefono,email,forma_pago,tiempo_suministro,metodo_pago,provincia,codigo_postal,persona_contacto,fecha_creado,activo) value (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,current_timestamp,1)",
					$this->makeValue($this->nombre, "text"),
					$this->makeValue($this->descripcion, "text"),
					$this->makeValue($this->direccion, "text"),
					$this->makeValue($this->ciudad, "text"),
					$this->makeValue($this->pais, "text"),
					$this->makeValue($this->telefono, "text"),
					$this->makeValue($this->email, "text"),
					$this->makeValue($this->forma_pago, "int"),
					$this->makeValue($this->tiempo_suministro, "int"),
					$this->makeValue($this->metodo_pago, "int"),
					$this->makeValue($this->provincia, "text"),
					$this->makeValue($this->codigo_postal, "text"),
					$this->makeValue($this->persona_contacto, "text"));
				$this->setConsulta($consulta);
				if($this->ejecutarSoloConsulta()) {
					$this->id_centro_logistico = $this->getUltimoID();
					return 1;
				} else {
					return 3;
					}
			} else {
				return 2;
				}
		} else {
			  if(!$this->comprobarCentroLogisticoDuplicado()) {
					$consulta = sprintf("update centros_logisticos set centro_logistico=%s, descripcion=%s, direccion=%s, ciudad=%s, pais=%s, telefono=%s, email=%s, forma_pago=%s, tiempo_suministro=%s, metodo_pago=%s, provincia=%s, codigo_postal=%s, persona_contacto=%s where id_centro_logistico=%s",
						$this->makeValue($this->nombre, "text"),
						$this->makeValue($this->descripcion, "text"),
						$this->makeValue($this->direccion, "text"),
						$this->makeValue($this->ciudad, "text"),
						$this->makeValue($this->pais, "text"),
						$this->makeValue($this->telefono, "text"),
						$this->makeValue($this->email, "text"),
						$this->makeValue($this->forma_pago, "int"),
						$this->makeValue($this->tiempo_suministro, "int"),
						$this->makeValue($this->metodo_pago, "int"),
						$this->makeValue($this->provincia, "text"),
						$this->makeValue($this->codigo_postal, "text"),
						$this->makeValue($this->persona_contacto, "text"),
						$this->makeValue($this->id_centro_logistico, "int"));
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

	// Comprueba si hay otro centro logistico con el mismo nombre
	// Devuelve true si hay centros logisticos duplicados
	function comprobarCentroLogisticoDuplicado() {
		if($this->id_centro_logistico == NULL) {
			$consulta = sprintf("select id_centro_logistico from centros_logisticos where centro_logistico=%s and activo=1",
				$this->makeValue($this->nombre, "text"));
		} else {
			$consulta = sprintf("select id_centro_logistico from centros_logisticos where centro_logistico=%s and activo=1 and id_centro_logistico<>%s",
				$this->makeValue($this->nombre, "text"),
				$this->makeValue($this->id_centro_logistico, "int"));
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
		$consulta = sprintf("update centros_logisticos set activo=0 where id_centro_logistico=%s",
			$this->makeValue($this->id_centro_logistico, "int"));
			$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 6;
		} else {
			return 5;
		}
	}

	// Devuelve todos los centros activos en la BBDD
	function dameCentrosLogisticos(){
		$consultaSql = sprintf("select * from centros_logisticos where activo=1 order by centro_logistico");
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}


	// Devuelve la cadena de un error según su identificador
	function getErrorMessage($error_num) {
		switch($error_num) {
			case 2:
				return 'Ya existe un centro logístico con ese nombre en la base de datos<br/>';
			break;
			case 3:
				return 'Se produjo un error al guardar el nuevo centro logístico<br/>';
			break;
			case 4:
				return 'Se produjo un error al modificar los datos del centro logístico<br/>';
			break;
			case 5:
				return 'Se produjo un error al eliminar el centro logístico<br/>';
			break;
		}
	}

}
?>