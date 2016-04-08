<?php
class Usuario extends MySQL{
	
	var $id_usuario; // ID de usuario
	var $usuario; // Usuario
	var $email; // Email
	var $fecha_creado; // Fecha de creación del usuario
	var $fecha_login; // Fecha del último login del usuario
	var $bloqueado; // Bloqueado
	var $activado; // Activado
	var $id_tipo; // Tipo de usuario
	var $id_almacen;

	var $password; 
	var $repita_password;
	var $actualizar_permisos;


	
	function cargarDatos($id_usuario,$usuario,$email,$fecha_creado,$fecha_login,$bloqueado,$activado,$id_tipo,$id_almacen) {
		$this->id_usuario = $id_usuario;
		$this->usuario = $usuario;
		$this->email = $email;
		$this->fecha_creado = $fecha_creado;
		$this->fecha_login = $fecha_login;
		$this->bloqueado = $bloqueado;
		$this->activado = $activado;
		$this->id_tipo = $id_tipo;
		$this->id_almacen = $id_almacen;
	}

	function cargarDatosModificacion($id_usuario,$usuario,$email,$password,$repita_password,$actualizar_permisos,$id_tipo,$id_almacen) {
		$this->id_usuario = $id_usuario;
		$this->usuario = $usuario;
		$this->email = $email;
		$this->password = $password;
		$this->repita_password = $repita_password;
		$this->actualizar_permisos = $actualizar_permisos;
		$this->id_tipo = $id_tipo;
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
			$resultados["activado"],
			$resultados["id_tipo"],
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

	// Se hace la carga de datos del nuevo usuario
	function datosNuevoUsuario($id_usuario = NULL,$usuario,$password,$repita_password,$email,$id_tipo,$id_almacen) {
		$this->id_usuario = $id_usuario;
		$this->usuario = $usuario;
		$this->password = $password;
		$this->repita_password = $repita_password;
		$this->email = $email;
		$this->id_tipo = $id_tipo;
		$this->id_almacen = $id_almacen;
	}

	// Se hace la carga de datos del usuario existente
	function datosUsuario($id_usuario,$usuario,$password,$repita_password,$email,$id_tipo) {
		$this->id_usuario = $id_usuario;
		$this->usuario = $usuario;
		$this->password = $password;
		$this->repita_password = $repita_password;
		$this->email = $email;
		$this->id_tipo = $id_tipo;
	}
	
	function datosNuevoEmail ($id_usuario = NULL, $email) {
		$this->id_usuario = $id_usuario;
		$this->email = $email;	
	}
	
	// Guarda los cambios realizados en el usuario
	function guardarCambios() {
		// Comprueba si no existe el usuario
		if($this->id_usuario == NULL) {
			if(!$this->contrasenyasDistintas()){
				// Comprueba si hay otro usuario con el mismo nombre
				if(!$this->comprobarUsuarioDuplicado()) {
					$consulta = sprintf("insert into usuarios (usuario,email,password,id_tipo,id_almacen,fecha_creacion,fecha_login,bloqueado,activo) value (%s,%s,old_password(%s),%s,%s,current_timestamp,current_timestamp,0,1)",
						$this->makeValue($this->usuario, "text"),
						$this->makeValue($this->email, "text"),
						$this->makeValue($this->password, "text"),
						$this->makeValue($this->id_tipo, "int"),
						$this->makeValue($this->id_almacen, "int"));
					$this->setConsulta($consulta);
					if($this->ejecutarSoloConsulta()) {
						// Asigna los permisos en funcion del tipo de usuario
						$this->id_usuario = $this->getUltimoID();
						$res_permisos = $this->asignarPermisos();

						$error_permisos = false;
						for($i=0;$i<count($res_permisos);$i++){
							$insertSql = sprintf("insert into usuarios_permisos (id_usuario,id_permiso,tipo) values (%s,%s,1)",
								$this->makeValue($this->id_usuario, "int"),
								$this->makeValue($res_permisos[$i], "int"));
							$this->setConsulta($insertSql);
							if(!$this->ejecutarSoloConsulta()) {
								// ERROR AL INSERTAR LOS PERMISOS
								$error_permisos = true;
								$i = count($res_permisos);
							}
						}

						if(!$error_permisos){
							// OK
							return 1;	
						}
						else {
							// ERROR: Guardar permisos
							return 7;
						}
					} 
					else {
						// ERROR: Guardar nuevo usuario
						return 3;
					}
				} 
				else {
					// ERROR: Usuario duplicado
					return 2;
				}
			} 
			else {
				// ERROR: Las contraseñas deben coincidir
				return 5;
			}
		} 
		else {
			// MODIFICACION DEL USUARIO EXISTENTE
			if($this->password == NULL and $this->repita_password == NULL) {
				// No se actualiza la contraseña
				$consultaSql = sprintf("update usuarios set usuario=%s, email=%s, id_tipo=%s, id_almacen=%s where id_usuario=%s",
					$this->makeValue($this->usuario, "text"),
					$this->makeValue($this->email, "text"),
					$this->makeValue($this->id_tipo, "int"),
					$this->makeValue($this->id_almacen, "int"),
					$this->makeValue($this->id_usuario, "int"));
			}
			else {
				// Se actualiza tambien la contraseña
				if(!$this->contrasenyasDistintas()){
					$consultaSql = sprintf("update usuarios set usuario=%s, email=%s, password=old_password(%s), id_tipo=%s, id_almacen=%s where id_usuario=%s",
						$this->makeValue($this->usuario, "text"),
					  	$this->makeValue($this->email, "text"),
						$this->makeValue($this->password, "text"),
						$this->makeValue($this->id_tipo, "int"),
						$this->makeValue($this->id_almacen, "int"),
						$this->makeValue($this->id_usuario, "int"));
				}
				else {
					// ERROR: Las contraseñas deben coincidir
					return 5;
				}
			}
			if(!$this->comprobarUsuarioDuplicado()) {
				// Actualizamos los datos del usuario
				$this->setConsulta($consultaSql);
				if($this->ejecutarSoloConsulta()) {
					// Comprobamos si se cambio el tipo de usuario
					if($this->actualizar_permisos){
						// QUITAMOS LOS PERMISOS
						$res_borrar = $this->borrarPermisos();
						if($res_borrar == 1){
							// ASIGNAMOS LOS NUEVOS PERMISOS
							$res_permisos = $this->asignarPermisos();

							$error_permisos = false;
							for($i=0;$i<count($res_permisos);$i++){
								$insertSql = sprintf("insert into usuarios_permisos (id_usuario,id_permiso,tipo) values (%s,%s,1)",
									$this->makeValue($this->id_usuario, "int"),
									$this->makeValue($res_permisos[$i], "int"));
								$this->setConsulta($insertSql);
								if(!$this->ejecutarSoloConsulta()) {
									// ERROR AL INSERTAR LOS PERMISOS
									$error_permisos = true;
									$i = count($res_permisos);
								}
							}

							if(!$error_permisos){
								// OK
								return 1;	
							}
							else {
								// ERROR: Guardar permisos
								return 7;
							}
						}
						else {
							// ERROR: Borrar permisos
							return 8;
						}
					}
					else {
						// OK
						return 1;	
					}
				}
				else {
					// ERROR: Actualizar datos
					return 4;
				}
			}
			else {
				// ERROR: Usuario duplicado 
				return 2;
			}
		}
	}

	function modificaEmail() {
		$consulta = sprintf("update usuarios set email=%s where id_usuario=%s",
			$this->makeValue($this->email, "text"),
			$this->makeValue($this->id_usuario, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		} else {
			return 6;
		}
	}

	// Funcion que elimina los permisos de un usuario
	function borrarPermisos(){
		 // Se asignan los permisos
    	$deleteSql = sprintf("delete from usuarios_permisos where id_usuario=%s",
        	$this->makeValue($this->id_usuario, "int"));
    	$this->setConsulta($deleteSql);
    	if($this->ejecutarSoloConsulta()){
    		return 1;
    	}
    	else {
    		return 8;
    	}
	}
	
	function contrasenyasDistintas()	{
		return $no_coinciden = ($this->password != $this->repita_password);	
	}
		
	// Comprueba si hay otro usuario con el mismo nombre
	// Devuelve true si hay usuarios duplicados
	function comprobarUsuarioDuplicado() {
		if($this->id_usuario == NULL) {
			$consulta = sprintf("select id_usuario from usuarios where usuario=%s",
				$this->makeValue($this->usuario, "text"));
		} else {	
			$consulta = sprintf("select id_usuario from usuarios where usuario=%s and id_usuario<>%s",
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
	
	// Funcion que determina si un usuario puede acceder
	function userConPermiso($id_usuario,$id_permiso){
		$consultaSql = sprintf("select id from usuarios_permisos where id_usuario=%s and id_permiso=%s",
			$this->makeValue($id_usuario, "int"),
			$this->makeValue($id_permiso, "int"));
		$this->setConsulta($consultaSql); 
		$this->ejecutarSoloConsulta();
		if($this->getNumeroFilas() == 0) {
			return false;
		}
		else return true;
	}

	// Funcion que devuelve todos los usuarios activos
	function dameUsuariosActivos(){
		$consultaSql = "select id_usuario,usuario,id_tipo,id_almacen from usuarios where activo=1";
		$this->setConsulta($consultaSql); 
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Funcion que devuelve el nombre del tipo de usuario
	function dameNombreTipoUsuario($id_tipo){
		$consultaSql = sprintf("select tipo from tipos_usuarios where id=%s",
			$this->makeValue($id_tipo, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();
	}

	// Funcion que devuelve los tipos de usuario
	function dameTiposUsuario(){
		$consultaSql = "select * from tipos_usuarios order by administrador DESC";
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $this->getResultados();	
	}

	// Funcion para tranformar la hora de Madrid a la de Brasil
	function fechaHoraBrasil($date){
    	date_default_timezone_set('Europe/Madrid');
        $fecha = new DateTime($date);
        $nuevafecha = $fecha->sub(new DateInterval('PT5H'));
        $nuevafecha = $fecha->format('d/m/Y H:i');
        return $nuevafecha; 
    }

    // Funcion para tranformar la hora a la de Madrid
	function fechaHoraSpain($date){
    	date_default_timezone_set('Europe/Madrid');
        $fecha = new DateTime($date);
        $nuevafecha = $fecha->format('d/m/Y H:i');
        return $nuevafecha;
    }

    // Funcion para tranformar la hora de Madrid a la de Brasil
	function fechaBrasil($date){
    	date_default_timezone_set('Europe/Madrid');
        $fecha = new DateTime($date);
        $nuevafecha = $fecha->sub(new DateInterval('PT5H'));
        $nuevafecha = $fecha->format('d/m/Y');
	    return $nuevafecha; 
    }

    // Funcion para tranformar la hora a la de Madrid
	function fechaSpain($date){
    	date_default_timezone_set('Europe/Madrid');
        $fecha = new DateTime($date);
        $nuevafecha = $fecha->format('d/m/Y');
        return $nuevafecha;
    }

    // Funcion que devuelve todos los usuarios administradores
    function dameUsuariosAdminGlobales(){
        $consultaSql = "select * from usuarios where activo=1 and id_tipo=1";
        $this->setConsulta($consultaSql);
        $this->ejecutarConsulta();
        return $this->getResultados();
    }

    // Funcion que devuelve todos los usuarios activos de que pueden operar en almacen
    // Estos usuarios son ADM_SMK, ADM_FAB, ADM_MAN, ADM_GES, USR_FAB, USR_MAN, USR_GES
    function dameUsuariosAlmacen(){
        $consultaSql = "select * from usuarios where activo=1 and (id_tipo=1 or id_tipo=3 or id_tipo=4 or id_tipo=6 or id_tipo=7 or id_tipo = 8 or id_tipo =9)";
        $this->setConsulta($consultaSql);
        $this->ejecutarConsulta();
        return $this->getResultados();
    }

    // Funcion que devuelve todos los usuarios activos de fabrica de Simumak
    function dameUsuariosFabricaSimumak(){
        $consultaSql = "select * from usuarios where activo=1 and id_tipo=1 or id_almacen=1";
        $this->setConsulta($consultaSql);
        $this->ejecutarConsulta();
        return $this->getResultados();
    }

    // Funcion que devuelve todos los usuarios activos de fabrica de Toro
    function dameUsuariosFabricaToro(){
        $consultaSql = "select * from usuarios where activo=1 and id_tipo=1 or id_almacen=2";
        $this->setConsulta($consultaSql);
        $this->ejecutarConsulta();
        return $this->getResultados();
    }

    // Funcion que devuelve todos los usuarios activos de tipo Fabrica
    function dameUsuariosFabrica(){
        $consultaSql = "select * from usuarios where activo=1 and id_tipo=1 or id_tipo=3 or id_tipo=6";
        $this->setConsulta($consultaSql);
        $this->ejecutarConsulta();
        return $this->getResultados();
    }

	// Funcion que asigna permisos en funcion del tipos de usuario 
	function asignarPermisos(){
		/*
			LISTA DE PERMISOS 
			
			1.- LISTADO BASICO
			2.- CREAR BASICO
			3.- MODIFICAR BASICO
			4.-	ELIMINAR BASICO
			5.- CREAR USUARIOS
			6.- MODIFICAR USUARIOS
			7.- ELIMINAR USUARIOS
			8.- VER ORDEN PRODUCCION
			9.- CREAR ORDEN PRODUCCION
			10.- MODIFICAR ORDEN PRODUCCION
			11.- CAMBIAR ESTADO OP
			12.- GESTIONAR PRIORIDAD OP
			13.- VER ORDEN COMPRA
			14.- MODIFICAR ORDEN COMPRA
			15.- CREAR OP MANTENIMIENTO
			16.- VER PRODUCTOS
			17.- ENTREGAR PRODUCTO
			18.- VER PEDIDO
			19.- CREAR PEDIDO
			20.- ORDENAR PRODUCCIÓN
			21.- VER ALMACEN PIEZAS
			22.- ENTRADA MATERIAL ALMACEN
			23.- SALIDA MATERIAL ALMACEN
			24.- AJUSTE MATERIAL ALMACEN
			25.- VER ALMACEN PIEZAS
			26.- ENTRADA MATERIAL ALMACEN
			27.- SALIDA MATERIAL ALMACEN
			28.- AJUSTE MATERIAL ALMACEN
			29.- ENTRADA PERIFÉRICOS
			30.- SALIDA PÉRIFERICOS
			31.- EDITAR PÉRIFERICO
			32.- EDITAR ESTADO PÉRIFERICO
			33.- CREAR IMPUTACIÓN
			34.- VER BASICO
			35.- CREAR CENTRO LOGISTICO
			36.- MODIFICAR CENTRO LOGISTICO
			37.- ELIMINAR CENTRO LOGISTICO
			38.- VER MATERIAL INFORMATICO
			39.- MODIFICAR MATERIAL INFORMATICO
			40.- ENTRADA SIMULADORES
			41.- SALIDA SIMULADORES
			42.- EDITAR SIMULADOR
			43.- EDITAR ESTADO SIMULADOR
		*/

		switch ($this->id_tipo) {
			case '1':
				// ADMINISTRADOR SIMUMAK
				// TODOS LOS PERMISOS
				$permisos = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,/*33,*/34,35,36,37,38,39,40,41,42,43);
			break;
			case '2':
				// ADMINISTRADOR DISEÑO
				$permisos = array(1,2,3,4,5,6,7,21,34,35,36,37);
			break;
			case '3':
				// ADMINISTRADOR FABRICA
				$permisos = array(1,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,34,35,36,37);
			break;
			case '4':
				// ADMINISTRADOR MANTENIMIENTO
				$permisos = array(1,5,6,7,21,22,23,24,29,30,31,32,34,40,41,42,43);
			break;
			case '5':
				// USUARIO DISEÑO
				$permisos = array(1,2,3,4,21,34,35,36,37);
			break;
			case '6':
				// USUARIO FABRICA
				$permisos = array(1,8,11,13,15,16,17,18,19,21,22,23,34,35,36,37);
			break;
			case '7':
				// USUARIO MANTENIMIENTO
				$permisos = array(1,21,22,23,29,30,31,32,34,40,41,42,43);
			break;
			case '8':
				// ADMINISTRADOR GESTIÓN
				$permisos = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,34,35,36,37);
			break;
			case '9':
				// USUARIO GESTIÓN
				$permisos = array(1,8,11,13,15,16,17,18,19,21,22,23,34,35,36,37);
			break;
			default:
				# code...
			break;
		}
		return $permisos;
	}

	// Devuelve la cadena de un error según su identificador
	function getErrorMessage($error_num) {
		switch($error_num) {
			case 2:
				return 'Ya existe un usuario con ese nombre en la base de datos<br/>';
			break;
			case 3:
				return 'Se produjo un error al guardar el nuevo usuario<br/>';
			break;
			case 4:
				return 'Se produjo un error al modificar los datos del usuario<br/>';
			break;
			case 5:
				return 'Las contraseñas deben coincidir</br>';
			break;
			case 6:
				return 'Se produjo un error al modificar el email del usuario</br>';
			break;
			case 7:
				return 'Se produjo un error al guardar los permisos del usuario</br>';
			break;
			case 8:
				return 'Se produjo un error al eliminar los permisos del usuario<br/>';
			break;
		}
	}


}
?>