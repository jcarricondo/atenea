<?php
class Plantilla_Producto extends MySql{
	
	var $id_plantilla;
    var $nombre;
    var $version;
    var $id_nombre_producto;
    var $fecha_creado;
    var $activo;

    var $id_cabina;
    var $ids_perifericos;
    // var $ids_software;
    var $ids_componentes;

		
	function cargarDatos($id_plantilla,$nombre,$version,$id_nombre_producto,$fecha_creado) {
        $this->id_plantilla = $id_plantilla;
        $this->nombre = $nombre;
        $this->version = $version;
		$this->id_nombre_producto = $id_nombre_producto;
        $this->fecha_creado = $fecha_creado;
	}
	
	function cargaDatosPlantillaProductoId($id_plantilla) {
		$consultaSql = sprintf("select * from plantilla_producto where id_plantilla=%s",
			$this->makeValue($id_plantilla, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
            $resultados["id_plantilla"],
            $resultados["nombre"],
            $resultados["version"],
            $resultados["id_nombre_producto"],
            $resultados["fecha_creado"]);
	}
	
	// Se hace la carga de datos  de la nueva plantilla
	function datosNuevaPlantilla($id_plantilla = NULL,$nombre,$version,$id_nombre_producto) {
		$this->id_plantilla = $id_plantilla;
        $this->nombre = $nombre;
        $this->version = $version;
        $this->id_nombre_producto = $id_nombre_producto;
	}

    // Funcion que devuelve el tipo del componente
    function dameTipoComponente($id_componente){
        $consulta = sprintf("select id_tipo from componentes where id_componente=%s",
            $this->makeValue($id_componente, "int"));
        $this->setConsulta($consulta);
        $this->ejecutarConsulta();
        return $this->getPrimerResultado();
    }

    // Funcion que devuelve la cabina de la plantilla de producto
    function dameCabinaPlantillaProducto($id_plantilla){
        $consulta = sprintf("select id_componente from plantilla_producto_componentes where activo=1 and id_tipo_componente=1 and id_plantilla=%s",
            $this->makeValue($id_plantilla, "int"));
        $this->setConsulta($consulta);
        $this->ejecutarConsulta();
        $this->id_cabina = $this->getPrimerResultado();
        return $this->id_cabina["id_componente"];
    }

    // Funcion que devuelve los perifericos de la plantilla de producto
    function damePerifericosPlantillaProducto($id_plantilla){
        $consulta = sprintf("select id_componente from plantilla_producto_componentes where activo=1 and id_tipo_componente=2 and id_plantilla=%s",
            $this->makeValue($id_plantilla, "int"));
        $this->setConsulta($consulta);
        $this->ejecutarConsulta();
        $this->ids_perifericos = $this->getResultados();
        return $this->ids_perifericos;
    }

    /*
	// Funcion que devuelve el software de la plantilla de producto
    function dameSoftwarePlantillaProducto($id_plantilla){
        $consulta = sprintf("select id_componente from plantilla_producto_componentes where activo=1 and id_tipo_componente=3 and id_plantilla=%s",
            $this->makeValue($id_plantilla, "int"));
        $this->setConsulta($consulta);
        $this->ejecutarConsulta();
        $this->ids_software = $this->getResultados();
        return $this->ids_software;
    }
    */

    // Funcion que devuelve todos los componentes de la plantilla de producto
    function dameComponentesPlantillaProducto($id_plantilla){
        $consulta = sprintf("select * from plantilla_producto_componentes where activo=1 and id_plantilla=%s",
            $this->makeValue($id_plantilla,"int"));
        $this->setConsulta($consulta);
        $this->ejecutarConsulta();
        $this->ids_componentes = $this->getResultados();
        return $this->ids_componentes;
    }

	// Guarda los cambios realizados en la plantilla
	function guardarCambios() {
		// Si el id_plantilla es NULL lo toma como una nueva plantilla
		if($this->id_plantilla == NULL) {
			// Comprueba si hay otra plantilla con el mismo nombre
			if(!$this->comprobarPlantillaDuplicada()) {
				$consulta = sprintf("insert into plantilla_producto (nombre,version,id_nombre_producto,fecha_creado,activo) value (%s,%s,%s,current_timestamp,1)",
                    $this->makeValue($this->nombre, "text"),
					$this->makeValue($this->version, "text"),
                    $this->makeValue($this->id_nombre_producto, "int"));
				$this->setConsulta($consulta);
				if($this->ejecutarSoloConsulta()) {
					$this->id_plantilla = $this->getUltimoID();
					return 1;
				}
                else {
					return 3;
				}
			}
            else {
				return 2;
			}
		}
        else {
			if(!$this->comprobarPlantillaDuplicada()) {
				$consulta = sprintf("update plantilla_producto set nombre=%s, version=%s, id_nombre_producto=%s where id_plantilla=%s",
					$this->makeValue($this->nombre, "text"),
					$this->makeValue($this->version, "text"),
					$this->makeValue($this->id_nombre_producto, "int"),
                    $this->makeValue($this->id_plantilla, "int"));
				$this->setConsulta($consulta);
				if($this->ejecutarSoloConsulta()) {
					return 1;
				}
                else {
					return 4;
				}
			}
            else {
				return 2;
			}
		}
	}

    // Funcion que guarda un componente de una plantilla de producto
    function guardarComponentePlantillaProducto($id_plantilla,$id_componente,$id_tipo_componente){
        $insertSql = sprintf("insert into plantilla_producto_componentes (id_plantilla,id_componente,id_tipo_componente,fecha_creado,activo) values (%s,%s,%s,current_timestamp,1)",
            $this->makeValue($id_plantilla, "int"),
            $this->makeValue($id_componente,"int"),
            $this->makeValue($id_tipo_componente,"int"));
        $this->setConsulta($insertSql);
        if($this->ejecutarSoloConsulta()){
            return 1;
        }
        else return 6;
    }

    // Funcion que devuelve las plantillas de un nombre de producto
    function damePlantillasNombreProducto($id_nombre_producto){
        $consultaSql = sprintf("select id_plantilla from plantilla_producto where activo=1 and id_nombre_producto=%s",
            $this->makeValue($id_nombre_producto,"int"));
        $this->setConsulta($consultaSql);
        $this->ejecutarConsulta();
        return $this->getResultados();
    }
	
	// Comprueba si hay otra plantilla con el mismo nombre
	// Devuelve true si hay plantillas duplicadas
	function comprobarPlantillaDuplicada() {
		if($this->id_plantilla == NULL) {
			$consulta = sprintf("select id_plantilla from plantilla_producto where nombre=%s and version=%s and id_nombre_producto=%s and activo=1",
				$this->makeValue($this->nombre, "text"),
				$this->makeValue($this->version, "text"),
                $this->makeValue($this->id_nombre_producto, "int"));
		}
        else {
			$consulta = sprintf("select id_plantilla from plantilla_producto where nombre=%s and version=%s and id_nombre_producto=%s and id_plantilla<>%s and activo=1",
				$this->makeValue($this->nombre, "text"),
				$this->makeValue($this->version, "text"),
				$this->makeValue($this->id_nombre_producto, "int"),
                $this->makeValue($this->id_plantilla, "int"));
		}
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		if($this->getNumeroFilas() == 0) {
			return false;	
		}
        else {
			return true;
		}
	}
	
	function eliminar(){
		$consulta = sprintf("update plantilla_producto set activo=0 where id_plantilla=%s",
			$this->makeValue($this->id_plantilla, "int"));
			$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		}
        else {
			return 5;
		}
	}

    function desactivarComponentesPlantilla($id_plantilla){
        $consulta = sprintf("update plantilla_producto_componentes set activo=0 where id_plantilla=%s",
            $this->makeValue($id_plantilla, "int"));
        $this->setConsulta($consulta);
        if($this->ejecutarSoloConsulta()){
            return 1;
        }
        else {
            return 7;
        }
    }
	
	// Devuelve la cadena de un error según su identificador
	function getErrorMessage($error_num) {
		switch($error_num) {
			case 2:
				return 'Ya existe una plantilla vinculada al nombre de producto con ese nombre y esa versión</br>';
			break;
			case 3:
				return 'Se produjo un error al guardar la nueva plantilla</br>';
			break;
			case 4:
				return 'Se produjo un error al modificar los datos de la plantilla</br>';
			break;
			case 5:
				return 'Se produjo un error al eliminar la plantilla</br>';
			break;
            case 6:
                return 'Se produjo un error al guardar los componentes de la plantilla</br>';
            break;
            case 7:
                return 'Se produjo un error al desactivar los componentes de la plantilla</br>';
            break;
		}
	}			
}
?>