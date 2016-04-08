<?php
class ateneaUser extends MySql{
	
	var $id_usuario; // ID de usuario
	var $usuario; // Usuario
	var $email; // Email
	var $fecha_creado; // Fecha de creación del usuario
	var $fecha_login; // Fecha del último login del usuario
	var $bloqueado; // Bloqueado
	var $activado; // Activado
	var $tipo; // Tipo de usuario
	var $id_almacen;

	var $contraseña;
	var $repita_contraseña;
	
	function cargarDatos($id_usuario,$usuario,$email,$fecha_creado,$fecha_login,$bloqueado,$activado,$tipo,$id_almacen) {
		$this->id_usuario = $id_usuario;
		$this->usuario = $usuario;
		$this->email = $email;
		$this->fecha_creado = $fecha_creado;
		$this->fecha_login = $fecha_login;
		$this->bloqueado = $bloqueado;
		$this->activado = $activado;
		$this->tipo = $tipo;
		$this->id_almacen = $id_almacen;
	}
	
	function cargaDatosUsuarioId($id_usuario) {
		$consultaSql = sprintf("select * from usuarios where id_usuario=%s",
			$this->makeValue($id_usuario, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id_usuario"],
			$resultados["usuario"],
			$resultados["email"],
			$resultados["fecha_creacion"],
			$resultados["fecha_login"],
			$resultados["bloqueado"],
			$resultados["activo"],
			$resultados["tipo"],
			$resultados["id_almacen"]
		);
	}
	
	function isBloqueado() {
		if($this->bloqueado == 1) {
			return true;
		} else {
			return false;
		}
	}
	
	function isActivado() {
		if($this->activado == 1) {
			return true;
		} else {
			return false;
		}
	}
	
	
	// Se hace la carga de datos  del nuevo usuario
	function datosNuevoUsuario($id_usuario = NULL,$usuario,$contraseña,$repita_contraseña,$email,$tipo,$id_almacen) {
		$this->id_usuario = $id_usuario;
		$this->usuario = $usuario;
		$this->pass = $contraseña;
		$this->repita_password = $repita_contraseña;
		$this->email = $email;
		$this->tipo = $tipo;
		$this->id_almacen = $id_almacen;
	}
	
	// Guarda los cambios realizados en el usuario
	function guardarCambios() {
		// Si el id_usuario es NULL lo toma como un nuevo usuario
		if($this->id_usuario == NULL) {
			// Comprueba si coinciden las contraseñas
			if (!$this->contraseñasDistintas())	{
				// Comprueba si hay otro usuario con el mismo nombre
					if(!$this->comprobarUsuarioDuplicado()) {
						$consulta = sprintf("insert into usuarios (usuario,email,password,fecha_creacion,fecha_login,bloqueado,activo) value (%s,%s,%s,current_timestamp,current_timestamp,0,1)",
						$this->makeValue($this->usuario, "text"),
						$this->makeValue($this->email, "text"),
						$this->makeValue($this->pass, "text"));
						$this->setConsulta($consulta);
						if($this->ejecutarSoloConsulta()) {
							$this->id_usuario = $this->getUltimoID();
							return 1;
						} else {
							return 3;
						}
					} else {
						return 2;
						}
			} else {
				return 5;
				}
		} else {
			// Comprueba si coinciden las contraseñas
			if (!$this->contraseñasDistintas())	{
				if(!$this->comprobarUsuarioDuplicado()) {
					$consulta = sprintf("update usuarios set usuario=%s, email=%s,password=%s where id_usuario=%s",
				  $this->makeValue($this->usuario, "text"),
				  $this->makeValue($this->email, "text"),
				  $this->makeValue($this->pass, "int"),
				  $this->makeValue($this->id_usuario, "int"));
				
					$this->setConsulta($consulta);
					if($this->ejecutarSoloConsulta()) {
						return 1;
					} else {
						return 4;
					}
				} else {
					return 2;
					}
			} else {
				return 5;
			}
		}
	}
	
	function contraseñasDistintas()	{
		return $no_coinciden = ($this->pass != $this->repita_password);	
	}
	
	
	// Comprueba si hay otro usuario con el mismo nombre
	// Devuelve true si hay usuarios duplicados
	function comprobarUsuarioDuplicado() {
		if($this->id_usuario == NULL) {
			$consulta = sprintf("select id_usuario from usuarios where usuario=%s and activo=1",
				$this->makeValue($this->usuario, "text"));
		} else {	
			$consulta = sprintf("select id_usuario from usuarios where usuario=%s and activo=1 and id_usuario<>%s",
				$this->makeValue($this->usuario, "text"),
				$this->makeValue($this->id_usuario, "int"));
		}
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		if($this->getNumeroFilas() == 0) {
			return false;	
		} else {
			return true;
		}
	}
	
	// Devuelve la cadena de un error según su identificador
	function getErrorMessage($error_num) {
		switch($error_num) {
			case 2:
				return 'Ya existe un usuario con ese nombre en la base de datos<br/>';
			break;
			case 3:
				return 'Se produjo un error al guardar el nuevo usuariobr/>';
			break;
			case 4:
				return 'Se produjo un error al modificar los datos del usuario<br/>';
			break;
			case 5:
				return 'Las contraseñas deben coincidir</br>';
			break;
		}
	}	

}
?>