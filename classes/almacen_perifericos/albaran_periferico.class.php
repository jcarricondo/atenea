<?php

class AlbaranPeriferico extends MySQL {

	var $id_albaran;
	var $nombre_albaran;
	var $tipo_albaran;
	var $id_centro_logistico;
	var $motivo;
	var $id_usuario;
	var $id_almacen;
	var $fecha_creado;
	var $activo;

	// Carga de datos de un albaran ya existente en la base de datos
	function cargarDatos($id_albaran,$nombre_albaran,$tipo_albaran,$id_centro_logistico,$motivo,$id_usuario,$id_almacen,$fecha_creado,$activo) {
		$this->id_albaran = $id_albaran;
		$this->nombre_albaran = $nombre_albaran;
		$this->tipo_albaran = $tipo_albaran;
		$this->id_centro_logistico = $id_centro_logistico;
		$this->motivo = $motivo;
		$this->id_usuario = $id_usuario;
		$this->id_almacen = $id_almacen;
		$this->fecha_creado = $fecha_creado;
		$this->activo = $activo;
	}

	// Se obtienen los datos del albaran de Perifericos en base a su ID
	function cargaDatosAlbaranId($id_albaran) {
		$consultaSql = sprintf("select * from albaranes_perifericos where id_albaran=%s",
			$this->makeValue($id_albaran, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id_albaran"],
			$resultados["nombre_albaran"],
			$resultados["tipo_albaran"],
			$resultados["id_centro_logistico"],
			$resultados["motivo"],
			$resultados["id_usuario"],
			$resultados["id_almacen"],
			$resultados["fecha_creado"],
			$resultados["activo"]
		);
	}

	// Funcion que establece los atributos del albaran de Perifericos
	function datosNuevoAlbaran($id_albaran = NULL,$nombre_albaran,$tipo_albaran,$id_centro_logistico,$motivo,$id_usuario,$id_almacen,$fecha_creado,$activo){
		$this->id_albaran = $id_albaran;
		$this->nombre_albaran = $nombre_albaran;
		$this->tipo_albaran = $tipo_albaran;
		$this->id_centro_logistico = $id_centro_logistico;
		$this->motivo = $motivo;
		$this->id_usuario = $id_usuario;
		$this->id_almacen = $id_almacen;
		$this->fecha_creado = $fecha_creado;
		$this->activo = $activo;
	}

	// Guarda el albaran
	function guardarAlbaran() {
		if($this->id_albaran == NULL) {
			// Comprueba si ya existe el albaran en la BBDD
			if(!$this->comprobarAlbaranDuplicado()){
				$consulta = sprintf("insert into albaranes_perifericos (nombre_albaran,tipo_albaran,id_centro_logistico,motivo,id_usuario,id_almacen,fecha_creado,activo) value (%s,%s,%s,%s,%s,%s,current_timestamp,1)",
					$this->makeValue($this->nombre_albaran, "text"),
					$this->makeValue($this->tipo_albaran, "text"),
					$this->makeValue($this->id_centro_logistico, "int"),
					$this->makeValue($this->motivo, "text"),
					$this->makeValue($this->id_usuario, "int"),
					$this->makeValue($this->id_almacen, "int"));
				$this->setConsulta($consulta);
				if($this->ejecutarSoloConsulta()) {
					$this->id_albaran = $this->getUltimoID();
					return 1;
				} 
				else {
					return 2;
				}
			}
			else return 3;	
		}
	}

	function guardarMovimientoPeriferico($id_albaran,$id_periferico,$estado){
		$consulta = sprintf("insert into albaranes_perifericos_movimientos (id_albaran,id_periferico,estado,fecha_creado,activo) value (%s,%s,%s,current_timestamp,1)",
			$this->makeValue($id_albaran, "int"),
			$this->makeValue($id_periferico, "int"),
			$this->makeValue($estado, "text"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		} 
		else {
			return 4;
		}
	}

	function guardarLogPeriferico($id_albaran,$numero_serie,$esta_averiado,$id_periferico){
		$consulta = sprintf("insert into albaranes_perifericos_log (id_albaran,numero_serie,averiado,id_periferico,fecha_creado,activo) value (%s,%s,%s,%s,current_timestamp,1)",
			$this->makeValue($id_albaran, "int"),
			$this->makeValue($numero_serie, "text"),
			$this->makeValue($esta_averiado, "text"),
			$this->makeValue($id_periferico, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		} 
		else {
			return 5;
		}
	}

	// Desactiva el movimiento de un albaran de un periferico al cambiar este de estado
	function desactivarEstadoPeriferico($id){
		$consulta = sprintf("update albaranes_perifericos_movimientos set activo=0 where id=%s",
			$this->makeValue($id, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		} 
		else {
			return 6;
		}

	}

	// Vuelve a activar el movimiento de un albaran de un periferico al deshacerse la operacion 
	function reactivarEstadoPeriferico($id){
		$consulta = sprintf("update albaranes_perifericos_movimientos set activo=1 where id=%s",
			$this->makeValue($id, "int"));
		$this->setConsulta($consulta);

		if($this->ejecutarSoloConsulta()) {
			return 1;
		} 
		else {
			return 7;
		}
	}

	// Desactiva el log de un periferico de un albaran al deshacer la operacion 
	function desactivarLogPeriferico($id_albaran,$id_periferico){
		$consulta = sprintf("update albaranes_perifericos_log set activo=0 where id_albaran=%s and id_periferico=%s",
			$this->makeValue($id_albaran, "int"),
			$this->makeValue($id_periferico, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		} 
		else {
			return 8;
		}
	}

	// Elimina el movimiento de un albaran de un periferico cuando se deshace la operacion 
	function eliminarEstadoPeriferico($id){
		$consulta = sprintf("delete from albaranes_perifericos_movimientos where id=%s",
			$this->makeValue($id, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		}
		else {
			return 9;
		}
	}

	// Funcion que desactiva un albaran vacio
	function desactivarAlbaran($id_albaran){
		$consulta = sprintf("update albaranes_perifericos set activo=0 where id_albaran=%s",
			$this->makeValue($id_albaran, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		}
		else {
			return 10;
		}
	}

    // Función que devuelve los albaranes vacíos
    function dameAlbaranesVacios(){
        $consulta = "select id_albaran from albaranes_perifericos where activo=1
	                    and id_albaran not in (select id_albaran from albaranes_perifericos_movimientos where activo=1)
	                    and id_albaran not in (select id_albaran from albaranes_perifericos_log where activo=1)";
        $this->setConsulta($consulta);
        $this->ejecutarConsulta();
        return $this->getResultados();
    }

    // Función que desactiva todos los albaranes vacíos
    function desactivarAlbaranesVacios(){
        // Comprobamos si existen albaranes vacios
        $res_alb = $this->dameAlbaranesVacios();
        for($i=0;$i<count($res_alb);$i++){
            $id_albaran = $res_alb[$i]["id_albaran"];
            $res = $this->desactivarAlbaran($id_albaran);
            if($res != 1){
                $i = count($res_alb);
            }
        }
        return res;
    }


	// Devuelve los movimientos activos de un periferico 
	function dameUltimoMovimientoPeriferico($id_periferico) {
		$consulta = sprintf("select * from albaranes_perifericos_movimientos where id_periferico=%s and activo=1 order by fecha_creado DESC",
			$this->makeValue($id_periferico, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();
	}


	// Comprueba si ya existe un albaran con ese nombre en la BBDD
	function comprobarAlbaranDuplicado() {
		if($this->id_albaran == NULL) {
			$consulta = sprintf("select id_albaran from albaranes_perifericos where nombre_albaran=%s and activo=1",
				$this->makeValue($this->nombre_albaran, "text"));
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

	// Devuelve el ultimo albaran registrado en la BBDD de un almacen concreto
	function dameUltimoAlbaran($id_almacen) {
		$consulta = sprintf("select max(id_albaran) as id_albaran from albaranes_perifericos where activo=1 and id_almacen=%s",
			$this->makeValue($id_almacen, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();
	}

	// Funcion que devuelve los datos del log dado un albaran
	function dameLogAlbaran($id_albaran){
		$consulta = sprintf("select * from albaranes_perifericos_log where id_albaran=%s and activo=1 ",
			$this->makeValue($id_albaran,"int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();	
	}

	// Funcion que devuelve los id de los albaranes activos
	function dameAlbaranesActivos(){
		$consulta = "select id_albaran from albaranes_perifericos where activo=1 ";
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();	
	}	

	// Funcion que devuelve los movimientos de un albaran
	function dameMovimientosAlbaran($id_albaran){
		$consulta = sprintf("select * from albaranes_perifericos_movimientos where id_albaran=%s",
			$this->makeValue($id_albaran, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();	
	}

	// Funcion que devuelve el movimiento segun su ID
	function dameMovimientoAlbaranPorId($id){
		$consulta = sprintf("select * from albaranes_perifericos_log where id=%s",
			$this->makeValue($id, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();	
	}

	// Funcion que devuelve el estado actual del periferico
	function dameEstadoActualPeriferico($id_periferico){
		$consulta = sprintf("select estado from albaranes_perifericos_movimientos where id_periferico=%s and activo=1",
			$this->makeValue($id_periferico, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();		
	}

	function dameDatosLogPeriferico($id_albaran,$id_periferico){
		$consulta = sprintf("select * from albaranes_perifericos_log where id_albaran=%s and id_periferico=%s and activo=1",
			$this->makeValue($id_albaran, "int"),
			$this->makeValue($id_periferico, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	
	// Devuelve la cadena de un error según su identificador
	function getErrorMessage($error_num) {
		switch($error_num) {
			case 2:
				return 'Se produjo un error al guardar el albarán<br/>';
			break;
			case 3:
				return 'Ya existe un albaran con ese nombre en la base de datos<br/>';
			break;
			case 4:
				return 'Se produjo un error al guardar el movimiento del periferico<br/>';			
			break;
			case 5:
				return 'Se produjo un error al guardar el log del albarán<br/>';
			break;
			case 6:
				return 'Se produjo un error al desactivar el estado del periferico<br/>';
			break;
			case 7:
				return 'Se produjo un error al reactivar el estado del periferico<br/>';
			break;
			case 8:
				return 'Se produjo un error al desactivar el log del albarán<br/>';
			break;
			case 9:
				return 'Se produjo un error al eliminar el movimiento del periferico';
			break;	
			case 10:
				return 'Se produjo un error al desactivar el albaran vacio';
			break;
		}
	}
}
?>