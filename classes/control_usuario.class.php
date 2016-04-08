<?php
class Control_Usuario extends MySQL {
	
	// Login de usuario
	function login($usuario, $password) {
		if(trim($usuario) == "") {
			return 2;
		} else {
			$salida = $this->validaLogin($usuario,$password);
			return $salida;
		}
	}
	
	// Validación del login de usuario
	function validaLogin($usuario,$password) {
		$buscandoUsuario = sprintf("select id_usuario from usuarios where usuario=%s",
			$this->makeValue($usuario, "text"));
		$this->setConsulta($buscandoUsuario);
		$this->ejecutarConsulta();
		if($this->getNumeroFilas() == 0) {
			return 3;
		} else {
			$verificandoPassword = sprintf("select id_usuario,usuario,activo,bloqueado from usuarios where usuario=%s and password=old_password(%s)",
				$this->makeValue($usuario, "text"),
				$this->makeValue($password, "text"));
			$this->setConsulta($verificandoPassword);
			$this->ejecutarConsulta();
			if($this->getNumeroFilas() == 0) {
				return 4;
			} else {
				$datosUsuario = $this->getPrimerResultado();
				if($datosUsuario["activo"] == 1) {
					if($datosUsuario["bloqueado"] == 0) {
						$insertLogin = sprintf("update usuarios set fecha_login=current_timestamp where id_usuario=%s",
							$this->makeValue($datosUsuario["id_usuario"], "int"));
						$this->setConsulta($insertLogin);
						$this->ejecutarSoloConsulta();
						return 1;	
					} else {
						return 6;
					}
				} else {
					return 5;
				}
			}
		}
	}
	
	// Devuelve los datos del usuario
	function getDatosUsuario($usuario) {
        $obtenDatos = sprintf("select id_usuario,usuario,email,id_tipo,id_almacen,fecha_creacion,fecha_login,bloqueado,activo from usuarios where usuario=%s",
            $this->makeValue($usuario, "text"));
        $this->setConsulta($obtenDatos);
        $this->ejecutarConsulta();
        return $this->getPrimerResultado();
    }

	// Comprueba los permisos de usuario para la sección indicada
	function comprobarPermisos($id_usuario,$id_permiso) {
		if($id_usuario == NULL or $id_permiso == NULL) {
			$this->cerrarSesion();
		} else {
			$comprobarPermisos = sprintf("select id,tipo from permisos where id_usuario=%s and id_permiso=%s",
				$this->makeValue($id_usuario, "int"),
				$this->makeValue($id_permiso, "int"));
			$this->setConsulta($comprobarPermisos);
			$this->ejecutarConsulta();
			if($this->getNumeroFilas() == 0) {
				$this->cerrarSesion();
			} else {
				$datosPermiso = $this->getPrimerResultado();
				return $datosPermiso["tipo"];
			}
		}
	}

    // Devuelve la sede segun el tipo de usuario y el almacen al que pertenece
    function dameSedeSegunUsuario($id_tipo,$id_almacen){
        switch($id_tipo){
            case '1':
                // ADMINISTRADOR SMK
                return 0;
                break;
            case '2':
                // ADMINISTRADOR DIS
                return 0;
                break;
            case '5';
                // USUARIO DIS
                return 0;
                break;
            case '8':
                // ADMINISTRADOR GES
                return 0;
                break;
            case '9';
                // USUARIO GES
                return 0;
                break;
            default:
                if($id_almacen != 0){
                    // ADMINISTRADOR / USUARIO FAB
                    $buscaSede = sprintf("select id_sede from almacenes where id_almacen=%s",
                        $this->makeValue($id_almacen,"int"));
                    $this->setConsulta($buscaSede);
                    $this->ejecutarConsulta();
                    $res_sede = $this->getPrimerResultado();
                    return $res_sede["id_sede"];
                }
                else return 0;
                break;
        }
    }

	// Comprueba si el usuario es administrador global
	function esAdministradorGlobal($id_tipo){
		return $id_tipo == 1;
	}

	// Comprueba si el usuario es administrador global o de algunos de los módulos
	function esAdministrador($id_tipo){
        $buscaAdministrador = sprintf("select administrador from tipos_usuarios where id=%s",
            $this->makeValue($id_tipo,"int"));
        $this->setConsulta($buscaAdministrador);
        $this->ejecutarConsulta();
        $res_administrador = $this->getPrimerResultado();
        return ($res_administrador["administrador"] == 1);
	}

	// Comprueba si es administrador de Diseño
	function esAdministradorDis($id_tipo){
		return (($id_tipo == 1) || ($id_tipo == 2));
	}

	// Comprueba si es administrador de Fábrica
	function esAdministradorFab($id_tipo){
        return (($id_tipo == 1) || ($id_tipo == 3));
	}

	// Comprueba si es administrador de Mantenimiento
	function esAdministradorMan($id_tipo){
        return (($id_tipo == 1) || ($id_tipo == 4));
	}

    // Comprueba si es administrador de Almacen
    function esAdministradorAlmacen($id_tipo){
        $esAdminFab = $this->esAdministradorFab($id_tipo);
        $esAdminMan = $this->esAdministradorMan($id_tipo);
        return $esAdminFab || $esAdminMan;
    }

    // Comprueba si es administrador de Gestión
    function esAdministradorGes($id_tipo){
        return (($id_tipo == 1) || ($id_tipo == 8));
    }

    // Comprueba si es usuario de Diseño
    function esUsuarioDis($id_tipo){
        return (($id_tipo == 2) || ($id_tipo == 5));
    }

    // Comprueba si es usuario de Fábrica
    function esUsuarioFab($id_tipo){
        return (($id_tipo == 3) || ($id_tipo == 6));
    }

    // Comprueba si es usuario de Mantenimiento
    function esUsuarioMan($id_tipo){
        return (($id_tipo == 4) || ($id_tipo == 7));
    }

    // Comprueba si es usuario de Gestión
    function esUsuarioGes($id_tipo){
        return (($id_tipo == 8) || ($id_tipo == 9));
    }

    // Comprueba si es usuario de Mantenimiento de Brasil
    function esUsuarioBrasil($id_tipo,$id_sede){
        return (($id_tipo == 4 || $id_tipo == 7) && $id_sede == 3);
    }

    // Comprueba si es usuario de Fábrica de Simumak
    function esUsuarioFabricaSimumak($id_tipo,$id_almacen){
        return ($id_almacen == 1);
    }

    // Comprueba si es usuario de Fábrica de Toro
    function esUsuarioFabricaToro($id_tipo,$id_almacen){
        return ($id_almacen == 2);
    }

    // Función que establece si un tipo de usuario puede controlar a otro
    function controlarUsuario($id_tipo_usuario,$id_tipo,$coinciden_sede){
        switch ($id_tipo_usuario) {
            case 1:
                // ADMINISTRADOR SIMUMAK
                return true;
                break;
            case 2:
                // ADMINISTRADOR DISEÑO
                return ($id_tipo == 2 || $id_tipo == 5);
                break;
            case 3:
                // ADMINISTRADOR FABRICA
                return (($id_tipo == 3 && $coinciden_sede) || ($id_tipo == 6 && $coinciden_sede));
                break;
            case 4:
                // ADMINISTRADOR MANTENIMIENTO
                return (($id_tipo == 4 && $coinciden_sede) || ($id_tipo == 7 && $coinciden_sede));
                break;
            case 8:
                // ADMINISTRADOR GESTION
                return ($id_tipo == 8 || $id_tipo == 9);
                break;
            default:
                return false;
                break;
        }
    }

	// Cierra la sesión de usuario
	function cerrarSesion() {
		session_start();
		session_unset();
		session_destroy();
		header("Location:../");
	}
	
	// Devuelve la cadena de un error según su identificador - SOLO PARA EL LOGIN
	function getErrorMessage($error_num) {
		switch($error_num) {
			case 2:
				return 'Para iniciar sesión es necesario indicar un nombre de usuario';
			break;
			case 3:
				return 'El usuario no existe en la base de datos';
			break;
			case 4:
				return 'La contraseña indicada no es válida';
			break;
			case 5:
				return 'El usuario está inactivo';
			break;
			case 6:
				return 'El usuario está bloqueado';
			break;
		}
	}

}
?>