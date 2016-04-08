<?php

class AlbaranInformatico extends MySQL {

	var $id_albaran;
	var $nombre_albaran;
	var $tipo_albaran;
	var $motivo;
	var $id_usuario;
	var $id_almacen;
	var $origen_destino;
	var $observaciones;
	var $fecha_creado;
	var $activo;

	// Carga de datos de un albarán ya existente en la base de datos
	function cargarDatos($id_albaran,$nombre_albaran,$tipo_albaran,$motivo,$id_usuario,$id_almacen,$origen_destino,$observaciones,$fecha_creado,$activo) {
		$this->id_albaran = $id_albaran;
		$this->nombre_albaran = $nombre_albaran;
		$this->tipo_albaran = $tipo_albaran;
		$this->motivo = $motivo;
		$this->id_usuario = $id_usuario;
		$this->id_almacen = $id_almacen;
		$this->origen_destino = $origen_destino;
		$this->observaciones = $observaciones;
		$this->fecha_creado = $fecha_creado;
		$this->activo = $activo;
	}

	// Se obtienen los datos del albarán de materiales informáticos en base a su ID
	function cargaDatosAlbaranId($id_albaran) {
		$consultaSql = sprintf("select * from albaranes_informaticos where id_albaran=%s",
			$this->makeValue($id_albaran, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id_albaran"],
			$resultados["nombre_albaran"],
			$resultados["tipo_albaran"],
			$resultados["motivo"],
			$resultados["id_usuario"],
			$resultados["id_almacen"],
			$resultados["origen_destino"],
			$resultados["observaciones"],
			$resultados["fecha_creado"],
			$resultados["activo"]
		);
	}

	// Función que establece los atributos del albarán de material informático
	function datosNuevoAlbaran($id_albaran = NULL,$nombre_albaran,$tipo_albaran,$motivo,$id_usuario,$id_almacen,$origen_destino,$observaciones,$fecha_creado,$activo){
		$this->id_albaran = $id_albaran;
		$this->nombre_albaran = $nombre_albaran;
		$this->tipo_albaran = $tipo_albaran;
		$this->motivo = $motivo;
		$this->id_usuario = $id_usuario;
		$this->id_almacen = $id_almacen;
		$this->origen_destino = $origen_destino;
		$this->observaciones = $observaciones;
		$this->fecha_creado = $fecha_creado;
		$this->activo = $activo;
	}

	// Guarda el albarán
	function guardarAlbaran() {
		if($this->id_albaran == NULL) {
			// Comprueba si ya existe el albarán en la BBDD
			if(!$this->comprobarAlbaranDuplicado()){
				$consulta = sprintf("insert into albaranes_informaticos (nombre_albaran,tipo_albaran,motivo,id_usuario,id_almacen,origen_destino,observaciones,fecha_creado,activo)
										value (%s,%s,%s,%s,%s,%s,%s,current_timestamp,1)",
					$this->makeValue($this->nombre_albaran, "text"),
					$this->makeValue($this->tipo_albaran, "text"),
					$this->makeValue($this->motivo, "text"),
					$this->makeValue($this->id_usuario, "int"),
					$this->makeValue($this->id_almacen, "int"),
					$this->makeValue($this->origen_destino, "text"),
					$this->makeValue($this->observaciones, "text"));
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

	function guardarMovimientoMaterial($id_albaran,$id_material,$estado){
		$consulta = sprintf("insert into albaranes_informaticos_movimientos (id_albaran,id_material,estado,fecha_creado,activo) value (%s,%s,%s,current_timestamp,1)",
			$this->makeValue($id_albaran, "int"),
			$this->makeValue($id_material, "int"),
			$this->makeValue($estado, "text"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		} 
		else {
			return 4;
		}
	}

	function guardarLogMaterial($id_albaran,$num_serie,$esta_averiado,$id_material){
		$consulta = sprintf("insert into albaranes_informaticos_log (id_albaran,num_serie,averiado,id_material,fecha_creado,activo) value (%s,%s,%s,%s,current_timestamp,1)",
			$this->makeValue($id_albaran, "int"),
			$this->makeValue($num_serie, "text"),
			$this->makeValue($esta_averiado, "text"),
			$this->makeValue($id_material, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		} 
		else {
			return 5;
		}
	}

	/*
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
	*/

	// Desactiva el log de un material de un albarán al deshacer la operación 
	function desactivarLogMaterial($id_albaran,$id_material){
		$consulta = sprintf("update albaranes_informaticos_log set activo=0 where id_albaran=%s and id_material=%s",
			$this->makeValue($id_albaran, "int"),
			$this->makeValue($id_material, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		} 
		else {
			return 8;
		}
	}

	// Elimina el movimiento de un albarán de un material cuando se deshace la operación 
	function eliminarEstadoMaterial($id){
		$consulta = sprintf("delete from albaranes_informaticos_movimientos where id=%s",
			$this->makeValue($id, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		}
		else {
			return 9;
		}
	}

	// Función que desactiva un albarán vacio
	function desactivarAlbaran($id_albaran){
		$consulta = sprintf("update albaranes_informaticos set activo=0 where id_albaran=%s",
								$this->makeValue($id_albaran, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		}
		else {
			return 10;
		}
	}

	// Devuelve los movimientos activos de un material
	function dameUltimoMovimientoMaterial($id_material) {
		$consulta = sprintf("select * from albaranes_informaticos_movimientos where id_material=%s and activo=1 order by fecha_creado DESC",
			$this->makeValue($id_material, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();
	}

	// Comprueba si ya existe un albarán con ese nombre en la BBDD
	function comprobarAlbaranDuplicado() {
		$consulta = sprintf("select id_albaran from albaranes_informaticos where nombre_albaran=%s and activo=1",
									$this->makeValue($this->nombre_albaran, "text"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		if($this->getNumeroFilas() == 0) {
			return false;
		} 
		else {
			return true;
		}
	}

	// Devuelve el último albarán registrado en la BBDD de un almacen concreto
	function dameUltimoAlbaran($id_almacen) {
		$consulta = sprintf("select max(id_albaran) as id_albaran from albaranes_informaticos where activo=1 and id_almacen=%s",
			$this->makeValue($id_almacen, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();
	}

	// Funcion que devuelve el movimiento segun su ID
	function dameMovimientoAlbaranPorId($id){
		$consulta = sprintf("select * from albaranes_informaticos_log where id=%s",
			$this->makeValue($id, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();	
	}

	/*
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
	*/

	
	// Devuelve la cadena de un error según su identificador
	function getErrorMessage($error_num) {
		switch($error_num) {
			case 2:
				return 'Se produjo un error al guardar el albarán<br/>';
			break;
			case 3:
				return 'Ya existe un albarán con ese nombre en la base de datos<br/>';
			break;
			case 4:
				return 'Se produjo un error al guardar el movimiento del material<br/>';			
			break;
			case 5:
				return 'Se produjo un error al guardar el log del albarán<br/>';
			break;
			case 6:
				return 'Se produjo un error al desactivar el estado del material<br/>';
			break;
			case 7:
				return 'Se produjo un error al reactivar el estado del material<br/>';
			break;
			case 8:
				return 'Se produjo un error al desactivar el log del albarán<br/>';
			break;
			case 9:
				return 'Se produjo un error al eliminar el movimiento del material';
			break;	
			case 10:
				return 'Se produjo un error al desactivar el albarán vacio';
			break;
		}
	}
}
?>