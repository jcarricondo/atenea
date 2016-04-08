<?php
class Proveedor extends MySQL {

	var $id_proveedor;
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


	// Carga de datos de un proveedor ya existente en la base de datos
	function cargarDatos($id_proveedor,$nombre,$descripcion,$direccion,$ciudad,$pais,$telefono,$email,$forma_pago,$tiempo_suministro,$metodo_pago,$provincia,$codigo_postal,$persona_contacto,$fecha_creado,$activo) {
		$this->id_proveedor = $id_proveedor;
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

	// Se obtienen los datos del proveedor en base a su ID
	function cargaDatosProveedorId($id_proveedor) {
		$consultaSql = sprintf("select * from proveedores where id_proveedor=%s",
			$this->makeValue($id_proveedor, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id_proveedor"],
			$resultados["nombre_prov"],
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

	// Se comprueba si el proveedor existe en la base de datos
	function getExisteProveedor($nombre) {
		$consultaSql = sprintf("select id_proveedor from proveedores where nombre_prov=%s and activo=1",
			$this->makeValue($nombre, "text"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		return $resultados["id_proveedor"];
	}

	// Se crea el proveedor desde la carga de referencias de perifericos
	function crearProveedorImport($nombre) {
		$consultaSql = sprintf("insert into proveedores (nombre_prov,direccion,ciudad,pais,telefono,email,fecha_creado,activo) values (%s,'-','-','-','-','-',current_timestamp,1)",
			$this->makeValue($nombre, "text"));
		$this->setConsulta($consultaSql);
		$this->ejecutarSoloConsulta();
		return $this->getUltimoID();
	}

	// Se hace la carga de datos  del nuevo proveedor
	function datosNuevoProveedor($id_proveedor = NULL, $nombre,$descripcion,$direccion,$ciudad,$pais,$telefono,$email,$forma_pago,$tiempo_suministro,$metodo_pago,$provincia,$codigo_postal,$persona_contacto) {
		$this->id_proveedor = $id_proveedor;
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

	// Se hace la carga de datos de la modificación del proveedor
	function datosProveedor($id_proveedor,$nombre,$descripcion,$direccion,$ciudad,$pais,$telefono,$email,$forma_pago,$tiempo_suministro,$metodo_pago,$provincia,$codigo_postal,$persona_contacto) {
		$this->id_proveedor = $id_proveedor;
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

	// Guarda los cambios realizados en el proveedor
	function guardarCambios() {
		// Si el id_proveedor es NULL lo toma como un nuevo proveedor
		if($this->id_proveedor == NULL) {
			// Comprueba si hay otro proveedor con el mismo nombre
			if(!$this->comprobarProveedorDuplicado()) {
				$consulta = sprintf("insert into proveedores (nombre_prov,descripcion,direccion,ciudad,pais,telefono,email,forma_pago,tiempo_suministro,metodo_pago,provincia,codigo_postal,persona_contacto,fecha_creado,activo) value (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,current_timestamp,1)",
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
					$this->id_proveedor = $this->getUltimoID();
					return 1;
				} else {
					return 3;
					}
			} else {
				return 2;
				}
		} else {
			  if(!$this->comprobarProveedorDuplicado()) {
					$consulta = sprintf("update proveedores set nombre_prov=%s, descripcion=%s, direccion=%s, ciudad=%s, pais=%s, telefono=%s, email=%s, forma_pago=%s, tiempo_suministro=%s, metodo_pago=%s, provincia=%s, codigo_postal=%s, persona_contacto=%s where id_proveedor=%s",
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
						$this->makeValue($this->id_proveedor, "int"));
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

	// Comprueba si hay otro proveedor  con el mismo nombre
	// Devuelve true si hay proveedores duplicados
	function comprobarProveedorDuplicado() {
		if($this->id_proveedor == NULL) {
			$consulta = sprintf("select id_proveedor from proveedores where nombre_prov=%s and activo=1",
				$this->makeValue($this->nombre, "text"));
		} else {
			$consulta = sprintf("select id_proveedor from proveedores where nombre_prov=%s and activo=1 and id_proveedor<>%s",
				$this->makeValue($this->nombre, "text"),
				$this->makeValue($this->id_proveedor, "int"));
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
		/*$consulta = sprintf("delete from proveedores where id_proveedor=%s",
			$this->makeValue($this->id_proveedor, "int"));
			$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 6;
		} else {
			return 5;
		}*/
		$consulta = sprintf("update proveedores set activo=0 where id_proveedor=%s",
			$this->makeValue($this->id_proveedor, "int"));
			$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 6;
		} else {
			return 5;
		}
	}

	// Devuelve todos los proveedores activos en la BBDD
	function dameProveedores(){
		$consultaSql = sprintf("select * from proveedores where activo=1 order by nombre_prov");
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Devuelve la cadena de un error según su identificador
	function getErrorMessage($error_num) {
		switch($error_num) {
			case 2:
				return 'Ya existe un proveedor con ese nombre en la base de datos<br/>';
			break;
			case 3:
				return 'Se produjo un error al guardar el nuevo proveedor<br/>';
			break;
			case 4:
				return 'Se produjo un error al modificar los datos del proveedor<br/>';
			break;
			case 5:
				return 'Se produjo un error al eliminar el proveedor<br/>';
			break;
		}
	}

}
?>