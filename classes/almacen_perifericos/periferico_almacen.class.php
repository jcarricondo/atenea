<?php

class PerifericoAlmacen extends MySQL {

	var $id_periferico;
	var $numero_serie;
	var $codigo;
	var $tipo_periferico;
	var $estado;
	var $id_almacen;
	var $comentarios;
	var $fecha_creado;
	var $activo;
	

	// Carga de datos de un periférico de un almacen ya existente en la base de datos
	function cargarDatos($id_periferico,$numero_serie,$tipo_periferico,$estado,$id_almacen,$comentarios,$fecha_creado,$activo) {
		$this->id_periferico = $id_periferico;
		$this->numero_serie = $numero_serie;
		$this->tipo_periferico = $tipo_periferico;
		$this->estado = $estado;
		$this->id_almacen = $id_almacen;
		$this->comentarios = $comentarios;
		$this->fecha_creado = $fecha_creado;
		$this->activo = $activo;
	}

	// Se obtienen los datos del Periférico de Almacen en base a su ID
	function cargaDatosPerifericoId($id_periferico) {
		$consultaSql = sprintf("select * from perifericos where id_periferico=%s",
			$this->makeValue($id_periferico, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id_periferico"],
			$resultados["numero_serie"],
			$resultados["tipo_periferico"],
			$resultados["estado"],
			$resultados["id_almacen"],
			$resultados["comentarios"],
			$resultados["fecha_creado"],
			$resultados["activo"]
		);
	}

	// Función que establece los atributos del Periférico del Almacen
	function datosNuevoPeriferico($id_periferico = NULL,$numero_serie,$tipo_periferico,$estado,$id_almacen,$comentarios,$fecha_creado,$activo){
		$this->id_periferico = $id_periferico;
		$this->numero_serie = $numero_serie;
		$this->tipo_periferico = $tipo_periferico;
		$this->estado = $estado;
		$this->id_almacen = $id_almacen;
		$this->comentarios = $comentarios;
		$this->fecha_creado = $fecha_creado;
		$this->activo = $activo;
	}

	// Función que verifica si existe un periférico a partir de su número de serie y de su almacen
	function existePeriferico($numero_serie,$id_almacen){
		$consulta = sprintf("select * from perifericos where numero_serie=%s and id_almacen=%s",
			$this->makeValue($numero_serie, "text"),
			$this->makeValue($id_almacen, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();
	}	

	// Función que devuelve el id_periférico de un periférico según su num_serie
	function dameIdPerifericoPorNumSerie($numero_serie){
		$consulta = 'select id_periferico from perifericos where numero_serie='.$numero_serie.' and activo=1';
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();
	}	

	// Devuelve el último periférico creado en la BBDD de ese almacen
	function dameUltimoPeriferico($id_almacen) {
		$consulta = sprintf("select max(id_periferico) as id_periferico from perifericos where activo=1 and id_almacen=%s",
			$this->makeValue($id_almacen,"int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();
	}

	// Función que devuelve el estado actual del periférico
	function dameEstadoActualPeriferico($id_periferico){
		$consulta = sprintf("select estado from perifericos where id_periferico=%s and activo=1",
			$this->makeValue($id_periferico, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();		
	}


	// Función que devuelve el nombre de un tipo de periférico según si id_tipo
	function dameNombreTipoPeriferico($tipo_periferico){
		$consulta = sprintf("select nombre from perifericos_tipo where id=%s and activo=1",
			$this->makeValue($tipo_periferico, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();
	}

	// Función que devuelve los datos de un tipo de periférico según su código
	function dameDatosPerifericosTipoPorCodigo($codigo){
		$consulta = sprintf("select * from perifericos_tipo where codigo=%s and activo=1",
			$this->makeValue($codigo, "text"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();
	}	

	// Carga los tipos de los periféricos
	function dameDatosTipoPerifericos(){
		$consulta = "select * from perifericos_tipo where activo=1";
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}	


	// Devuelve el último id de perifericos_estado del periférico
	function dameUltimoIdEstado($id_periferico){
		$consulta = sprintf("select max(id) as id from perifericos_estados where activo=1 and id_periferico=%s",
			$this->makeValue($id_periferico,"int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();
	}

	// Devuelve el ID de la tabla perifericos_estados del periférico activo de un almacen concreto
	function dameIDPerifericosEstados($id_periferico){
		$consulta = sprintf("select id from perifericos_estados where id_periferico=%s and activo=1",
			$this->makeValue($id_periferico, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();
	}

	// Devuelve el estado de la tabla perifericos_estados segun el ID 
	function dameEstadoPerifericoLog($id){
		$consulta = sprintf("select estado from perifericos_estados where id=%s",
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
					        	<input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="CARGAR" onclick="cargaPeriferico()" />
					   		</div>
					   		<div class="ContenedorCamposCreacionBasico">
				   				<div id="error_codigo" style="height: 30px;"><span style="color: red; font:bold 10px Verdana,Arial; padding: 5px;">'.$mensaje_error.'</span></div>
							</div>
				    		<br/>
				       		<div id="capa_periferico_buscador" class="ContenedorCamposCreacionBasico">
						    	<table id="tabla_buscador" style="width: 1100px; min-width: 480px;">
						        <tr style="height: 30px;">
						        	<th style="width:20%;">NUM. SERIE</th>
						            <th style="width:20%;">TIPO</th>
						            <th style="width:20%; text-align: center;">AVERIADO</th>
						            <th style="width:20%; text-align: center;"></th>
						            <th style="width:20%; text-align: center;"></th>
						        </tr>
						        <tr style="height: 35px;">
						        	<td style="width:20%;"></td>
						        	<td style="width:20%;"></td>
						        	<td style="width:20%; text-align: center;"></td>
						            <td style="width:20%; text-align: center;"></td>
						            <td style="width:20%; text-align: center;"></td>
						        </tr>
						        </table>
				        		<div id="datos_periferico"></div>
			       			</div>';
	}

	// Función que crea un nuevo periférico de almacen
	function guardarPeriferico(){
		$consulta = sprintf("insert into perifericos (numero_serie,tipo_periferico,estado,id_almacen,fecha_creado,activo) value (%s,%s,%s,%s,current_timestamp,1)",
			$this->makeValue($this->numero_serie, "text"),
			$this->makeValue($this->tipo_periferico, "int"),
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

	// Funcion que actualiza el estado del periferico
	function actualizaEstadoPeriferico($id_periferico,$estado){
		$consulta = sprintf("update perifericos set estado=%s, activo=1 where id_periferico=%s",
			$this->makeValue($estado, "text"),
			$this->makeValue($id_periferico, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) { 
			return 1;		
		}	
		else {
			return 3;
		}
	}

	// Funcion que guarda el nuevo estado del periferico en el historial de estados de perifericos
	function guardarLogEstadoPeriferico($id_periferico,$estado){
		$consulta = sprintf("insert into perifericos_estados (id_periferico,estado,fecha_creado,activo) value (%s,%s,current_timestamp,1)",
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

	// Funcion que obtiene el ultimo log de estado activo de un periferico
	function desactivarLogEstadoPeriferico($id_periferico){
		$consulta = sprintf("update perifericos_estados set activo=0 where id_periferico=%s and activo=1",
			$this->makeValue($id_periferico, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) { 
			return 1;		
		}	
		else {
			return 5;
		}
	}

	// Elimina el estado de un periferico cuando se deshace la operacion 
	function eliminarEstadoPeriferico($id){
		$consulta = sprintf("delete from perifericos_estados where id=%s",
			$this->makeValue($id, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		}
		else {
			return 6;
		}
	}

	// Vuelve a activar el estado del periferico al deshacerse la operacion 
	function reactivarEstadoPeriferico($id){
		$consulta = sprintf("update perifericos_estados set activo=1 where id=%s",
			$this->makeValue($id, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		} 
		else {
			return 7;
		}
	}

	// Funcion que actualiza el estado del periferico y lo desactiva
	function actualizaEstadoPerifericoDesactivandolo($id_periferico,$estado){
		$consulta = sprintf("update perifericos set estado=%s, activo=0 where id_periferico=%s",
			$this->makeValue($estado, "text"),
			$this->makeValue($id_periferico, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) { 
			return 1;		
		}	
		else {
			return 8;
		}
	}

	// Funcion que guarda los comentarios de un periferico
	function guardarComentarios($id_periferico,$comentarios){
		$consulta = sprintf("update perifericos set comentarios=%s where id_periferico=%s and activo=1",
			$this->makeValue($comentarios, "text"),
			$this->makeValue($id_periferico, "int"));
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
				return 'Se produjo un error al guardar el periferico<br/>';
			break;
			case 3:
				return 'Se produjo un error al actualizar el estado del periferico<br/>';
			break;
			case 4:
				return 'Se produjo un error al guardar el log de estado del periferico<br/>';
			break;
			case 5:
				return 'Se produjo un error al desactivar el log de estados del periferico<br/>';
			break;
			case 6:
				return 'Se produjo un error al eliminar el estado del periferico<br/>';
			break;
			case 7:
				return 'Se produjo un error al reactivar el estado del periferico<br/>';
			break;
			case 8:
				return 'Se produjo un error al desactivar el periferico<br/>';
			break;
			case 9:
				return 'Se produjo un error al guardar los comentarios del periferico<br/>';
			break;
		}
	}
}
?>