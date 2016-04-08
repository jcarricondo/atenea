<?php

class SimuladorAlmacen extends MySQL {

	var $id_simulador;
	var $numero_serie;
	var $estado;
	var $id_almacen;
	var $comentarios;
	var $fecha_creado;
	var $activo;
	

	// Carga de datos de un simulador de Almacen ya existente en la base de datos
	function cargarDatos($id_simulador,$numero_serie,$estado,$id_almacen,$comentarios,$fecha_creado,$activo) {
		$this->id_simulador = $id_simulador;
		$this->numero_serie = $numero_serie;
		$this->estado = $estado;
		$this->id_almacen = $id_almacen;
		$this->comentarios = $comentarios;
		$this->fecha_creado = $fecha_creado;
		$this->activo = $activo;
	}

	// Se obtienen los datos del Simulador de Almacen en base a su ID
	function cargaDatosSimuladorId($id_simulador) {
		$consultaSql = sprintf("select * from simuladores where id_simulador=%s",
			$this->makeValue($id_simulador, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id_simulador"],
			$resultados["numero_serie"],
			$resultados["estado"],
			$resultados["id_almacen"],
			$resultados["comentarios"],
			$resultados["fecha_creado"],
			$resultados["activo"]
		);
	}

	// Funcion que establece los atributos del Simulador del Almacen
	function datosNuevoSimulador($id_simulador = NULL,$numero_serie,$estado,$id_almacen,$comentarios,$fecha_creado,$activo){
		$this->id_simulador = $id_simulador;
		$this->numero_serie = $numero_serie;
		$this->estado = $estado;
		$this->id_almacen = $id_almacen;
		$this->comentarios = $comentarios;
		$this->fecha_creado = $fecha_creado;
		$this->activo = $activo;
	}

	// Funcion que verifica si existe un simulador a partir de su numero de serie y de su almacen
	function existeSimulador($numero_serie,$id_almacen){
		$consulta = sprintf("select * from simuladores where numero_serie=%s and id_almacen=%s",
			$this->makeValue($numero_serie, "text"),
			$this->makeValue($id_almacen, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();
	}	

	// Funcion que devuelve el id_simulador de un simulador segun su num_serie
	function dameIdSimuladorPorNumSerie($numero_serie){
		$consulta = 'select id_simulador from simuladores where numero_serie='.$numero_serie.' and activo=1';
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();
	}	

	// Devuelve el ultimo simulador creado en la BBDD de ese almacen
	function dameUltimoSimulador($id_almacen) {
		$consulta = sprintf("select max(id_simulador) as id_simulador from simuladores where activo=1 and id_almacen=%s",
			$this->makeValue($id_almacen,"int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();
	}

	// Funcion que devuelve el estado actual del simulador
	function dameEstadoActualSimulador($id_simulador){
		$consulta = sprintf("select estado from simuladores where id_simulador=%s and activo=1",
			$this->makeValue($id_simulador, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();		
	}

	// Devuelve el ultimo id de simuladores_estado del simulador
	function dameUltimoIdEstado($id_simulador){
		$consulta = sprintf("select max(id) as id from simuladores_estados where activo=1 and id_simulador=%s",
			$this->makeValue($id_simulador,"int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();
	}

	// Devuelve el ID de la tabla simuladores_estados del simulador activo de un almacen concreto
	function dameIDSimuladoresEstados($id_simulador){
		$consulta = sprintf("select id from simuladores_estados where id_simulador=%s and activo=1",
			$this->makeValue($id_simulador, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();
	}

	// Devuelve el estado de la tabla simuladores_estados segun el ID 
	function dameEstadoSimuladorLog($id){
		$consulta = sprintf("select estado from simuladores_estados where id=%s",
			$this->makeValue($id, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();
	}

	// Funcion que devuelve el HTML con el codigo de error
	function dameHTMLconMensajeError($mensaje_error){
		return $codigo = '<div class="ContenedorCamposCreacionBasico">
								<div class="LabelCreacionBasico">NUM. SERIE *</div>
					        	<input type="text" id="num_serie" name="num_serie" class="CreacionBasicoInput" value="" />
					        	<input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="CARGAR" onclick="cargaSimulador()" />
					   		</div>
					   		<div class="ContenedorCamposCreacionBasico">
				   				<div id="error_codigo" style="height: 30px;"><span style="color: red; font:bold 10px Verdana,Arial; padding: 5px;">'.$mensaje_error.'</span></div>
							</div>
				    		<br/>
				       		<div id="capa_simulador_buscador" class="ContenedorCamposCreacionBasico">
						    	<table id="tabla_buscador" style="width: 1100px; min-width: 480px;">
						        <tr style="height: 30px;">
						        	<th style="width:25%;">NUM. SERIE</th>
						            <th style="width:25%; text-align: center;">AVERIADO</th>
						            <th style="width:25%; text-align: center;"></th>
						            <th style="width:25%; text-align: center;"></th>
						        </tr>
						        <tr style="height: 35px;">
						        	<td style="width:25%;"></td>
						        	<td style="width:25%; text-align: center;"></td>
						            <td style="width:25%; text-align: center;"></td>
						            <td style="width:25%; text-align: center;"></td>
						        </tr>
						        </table>
				        		<div id="datos_simulador"></div>
			       			</div>';
	}

	// Funcion que crea un nuevo simulador de almacen
	function guardarSimulador(){
		$consulta = sprintf("insert into simuladores (numero_serie,estado,id_almacen,fecha_creado,activo) value (%s,%s,%s,current_timestamp,1)",
			$this->makeValue($this->numero_serie, "text"),
			$this->makeValue($this->estado, "text"),
			$this->makeValue($this->id_almacen, "int"));
		$this->setConsulta($consulta);	
		if($this->ejecutarSoloConsulta()) { 
			return 1;		
		}	
		else {
			return 2;
		}	
	}

	// Funcion que actualiza el estado del simulador
	function actualizaEstadoSimulador($id_simulador,$estado){
		$consulta = sprintf("update simuladores set estado=%s, activo=1 where id_simulador=%s",
			$this->makeValue($estado, "text"),
			$this->makeValue($id_simulador, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) { 
			return 1;		
		}	
		else {
			return 3;
		}
	}

	// Funcion que guarda el nuevo estado del simulador en el historial de estados de simuladores
	function guardarLogEstadoSimulador($id_simulador,$estado){
		$consulta = sprintf("insert into simuladores_estados (id_simulador,estado,fecha_creado,activo) value (%s,%s,current_timestamp,1)",
			$this->makeValue($id_simulador, "int"),
			$this->makeValue($estado, "text"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) { 
			return 1;		
		}	
		else {
			return 4;
		}	
	}

	// Funcion que obtiene el ultimo log de estado activo de un simulador
	function desactivarLogEstadoSimulador($id_simulador){
		$consulta = sprintf("update simuladores_estados set activo=0 where id_simulador=%s and activo=1",
			$this->makeValue($id_simulador, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) { 
			return 1;		
		}	
		else {
			return 5;
		}
	}

	// Elimina el estado de un simulador cuando se deshace la operacion 
	function eliminarEstadoSimulador($id){
		$consulta = sprintf("delete from simuladores_estados where id=%s",
			$this->makeValue($id, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		}
		else {
			return 6;
		}
	}

	// Vuelve a activar el estado del simulador al deshacerse la operacion 
	function reactivarEstadoSimulador($id){
		$consulta = sprintf("update simuladores_estados set activo=1 where id=%s",
			$this->makeValue($id, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		} 
		else {
			return 7;
		}
	}

	// Funcion que actualiza el estado del simulador y lo desactiva
	function actualizaEstadoSimuladorDesactivandolo($id_simulador,$estado){
		$consulta = sprintf("update simuladores set estado=%s, activo=0 where id_simulador=%s",
			$this->makeValue($estado, "text"),
			$this->makeValue($id_simulador, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) { 
			return 1;		
		}	
		else {
			return 8;
		}
	}

	// Funcion que guarda los comentarios de un simulador
	function guardarComentarios($id_simulador,$comentarios){
		$consulta = sprintf("update simuladores set comentarios=%s where id_simulador=%s and activo=1",
			$this->makeValue($comentarios, "text"),
			$this->makeValue($id_simulador, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) { 
			return 1;		
		}	
		else {
			return 9;
		}
	}

	
	// Devuelve la cadena de un error según su identificador
	function getErrorMessage($error_num) {
		switch($error_num) {
			case 2:
				return 'Se produjo un error al guardar el simulador<br/>';
			break;
			case 3:
				return 'Se produjo un error al actualizar el estado del simulador<br/>';
			break;
			case 4:
				return 'Se produjo un error al guardar el log de estado del simulador<br/>';
			break;
			case 5:
				return 'Se produjo un error al desactivar el log de estados del simulador<br/>';
			break;
			case 6:
				return 'Se produjo un error al eliminar el estado del simulador<br/>';
			break;
			case 7:
				return 'Se produjo un error al reactivar el estado del simulador<br/>';
			break;
			case 8:
				return 'Se produjo un error al desactivar el simulador<br/>';
			break;
			case 9:
				return 'Se produjo un error al guardar los comentarios del simulador<br/>';
			break;
		}
	}
}
?>