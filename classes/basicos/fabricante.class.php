<?php
class Fabricante extends MySQL {

	var $id_fabricante;
	var $nombre;
	var $descripcion;
	var $direccion;
	var $ciudad;
	var $pais;
	var $telefono;
	var $email;
	var $fecha_creado;
	var $activo;


	// Carga de datos de un fabricante ya existente en la base de datos
	function cargarDatos($id_fabricante,$nombre,$descripcion,$direccion,$ciudad,$pais,$telefono,$email,$fecha_creado,$activo) {
		$this->id_fabricante = $id_fabricante;
		$this->nombre = $nombre;
		$this->descripcion = $descripcion;
		$this->direccion = $direccion;
		$this->ciudad = $ciudad;
		$this->pais = $pais;
		$this->telefono = $telefono;
		$this->email = $email;
		$this->fecha_creado = $fecha_creado;
		$this->activo = $activo;
	}

	// Se obtienen los datos del fabricante en base a su ID
	function cargaDatosFabricanteId($id_fabricante) {
		$consultaSql = sprintf("select * from fabricantes where id_fabricante=%s",
			$this->makeValue($id_fabricante, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id_fabricante"],
			$resultados["nombre_fab"],
			$resultados["descripcion"],
			$resultados["direccion"],
			$resultados["ciudad"],
			$resultados["pais"],
			$resultados["telefono"],
			$resultados["email"],
			$resultados["fecha_creado"],
			$resultados["activo"]
		);
	}

	// Se comprueba si el fabricante existe en la base de datos
	function getExisteFabricante($nombre) {
		$consultaSql = sprintf("select id_fabricante from fabricantes where nombre_fab=%s and activo=1",
			$this->makeValue($nombre, "text"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		return $resultados["id_fabricante"];
	}

	// Se crea el fabricante desde la carga de referencias de perifericos
	function crearFabricanteImport($nombre) {
		$consultaSql = sprintf("insert into fabricantes (nombre_fab,descripcion,direccion,ciudad,pais,telefono,email,fecha_creado,activo) value (%s,'-','-','-','-','-','-',current_timestamp,1)",
			$this->makeValue($nombre, "text"));
		$this->setConsulta($consultaSql);
		$this->ejecutarSoloConsulta();
		return $this->getUltimoID();
	}

	// Se hace la carga de datos  del nuevo fabricante
	function datosNuevoFabricante($id_fabricante = NULL, $nombre,$descripcion,$direccion,$ciudad,$pais,$telefono,$email) {
		$this->id_fabricante = $id_fabricante;
		$this->nombre = $nombre;
		$this->descripcion = $descripcion;
		$this->direccion = $direccion;
		$this->ciudad = $ciudad;
		$this->pais = $pais;
		$this->telefono = $telefono;
		$this->email = $email;
	}

	// Se hace la carga de datos de la modificación del fabricante
	function datosFabricante($id_fabricante,$nombre,$descripcion,$direccion,$ciudad,$pais,$telefono,$email) {
		$this->id_fabricante = $id_fabricante;
		$this->nombre = $nombre;
		$this->descripcion = $descripcion;
		$this->direccion = $direccion;
		$this->ciudad = $ciudad;
		$this->pais = $pais;
		$this->telefono = $telefono;
		$this->email = $email;
	}

	// Guarda los cambios realizados en el fabricante
	function guardarCambios() {
		// Si el id_fabricante es NULL lo toma como un nuevo fabricante
		if($this->id_fabricante == NULL) {
			// Comprueba si hay otro fabricante con el mismo nombre
			if(!$this->comprobarFabricanteDuplicado()) {
				$consulta = sprintf("insert into fabricantes (nombre_fab,descripcion,direccion,ciudad,pais,telefono,email,fecha_creado,activo) value (%s,%s,%s,%s,%s,%s,%s,current_timestamp,1)",
					$this->makeValue($this->nombre, "text"),
					$this->makeValue($this->descripcion, "text"),
					$this->makeValue($this->direccion, "text"),
					$this->makeValue($this->ciudad, "text"),
					$this->makeValue($this->pais, "text"),
					$this->makeValue($this->telefono, "text"),
					$this->makeValue($this->email, "text"));
				$this->setConsulta($consulta);
				if($this->ejecutarSoloConsulta()) {
					$this->id_fabricante = $this->getUltimoID();
					return 1;
				} else {
					return 3;
					}
			} else {
				return 2;
				}
		} else {
			  if(!$this->comprobarFabricanteDuplicado()) {
					$consulta = sprintf("update fabricantes set nombre_fab=%s, descripcion=%s, direccion=%s, ciudad=%s, pais=%s, telefono=%s, email=%s where id_fabricante=%s",
						$this->makeValue($this->nombre, "text"),
						$this->makeValue($this->descripcion, "text"),
						$this->makeValue($this->direccion, "text"),
						$this->makeValue($this->ciudad, "text"),
						$this->makeValue($this->pais, "text"),
						$this->makeValue($this->telefono, "text"),
						$this->makeValue($this->email, "text"),
						$this->makeValue($this->id_fabricante, "int"));
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

	// Comprueba si hay otro fabricante  con el mismo nombre
	// Devuelve true si hay fabricantes duplicados
	function comprobarFabricanteDuplicado() {
		if($this->id_fabricante == NULL) {
			$consulta = sprintf("select id_fabricante from fabricantes where nombre_fab=%s and activo=1",
				$this->makeValue($this->nombre, "text"));
		} else {
			$consulta = sprintf("select id_fabricante from fabricantes where nombre_fab=%s and activo=1 and id_fabricante<>%s",
				$this->makeValue($this->nombre, "text"),
				$this->makeValue($this->id_fabricante, "int"));
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
		/*$consulta = sprintf("delete from fabricantes where id_fabricante=%s",
			$this->makeValue($this->id_fabricante, "int"));
			$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 6;
		} else {
			return 5;
		}*/
		$consulta = sprintf("update fabricantes set activo=0 where id_fabricante=%s",
			$this->makeValue($this->id_fabricante, "int"));
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
				return 'Ya existe un fabricante con ese nombre en la base de datos<br/>';
			break;
			case 3:
				return 'Se produjo un error al guardar el nuevo fabricante<br/>';
			break;
			case 4:
				return 'Se produjo un error al modificar los datos del fabricante<br/>';
			break;
			case 5:
				return 'Se produjo un error al eliminar el fabricante<br/>';
			break;
		}
	}

}
?>