<?php

class Produccion extends MySQL {

	var $id_produccion;
	var $id_usuario;
	var $id_componente;
	var $unidades_componente;
	var $numero_tecnicos;
	var $repeticiones_componente;
	var $codigo;
	var $fecha_creacion;

	var $resultados;


	function cargarDatos($id_produccion,$id_usuario,$id_componente,$unidades_componente,$numero_tecnicos,$repeticiones_componente,$codigo,$fecha_creacion) {
		$this->id_produccion = $id_produccion;
		$this->id_usuario = $id_usuario;
		$this->id_componente = $id_componente;
		$this->unidades_componente = $unidades_componente;
		$this->numero_tecnicos = $numero_tecnicos;
		$this->repeticiones_componente = $repeticiones_componente;
		$this->codigo = $codigo;
		$this->fecha_creacion = $fecha_creacion;
	}

	function cargaDatosEscandalloCodigo($codigo) {
		$consultaSql = sprintf("select * from escandallo_log where codigo=%s ",
			$this->makeValue($codigo, "text"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id_produccion"],
			$resultados["id_usuario"],
			$resultados["id_componente"],
			$resultados["unidades_componente"],
			$resultados["numero_tecnicos"],
			$resultados["repeticiones_componente"],
			$resultados["codigo"],
			$resultados["fecha_creacion"]
		);
	}

	// Devuelve los resultados del escandallo segun su codigo
	function dameResultadosEscandalloPorCodigo($codigo){
		$consulta = sprintf("select * from escandallo_log where codigo=%s",
			$this->makeValue($codigo, "text"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$resultados = $this->resultados;
	}

	// Devuelve todos los codigos de escandallo
	function dameTodosCodigos(){
		$consulta = "select * from escandallo_log group by codigo order by fecha_creacion desc, id desc";
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$resultados = $this->resultados;
	}

	// Devuelve todos los codigos de escandallo por sede
	function dameTodosCodigosPorSede($id_sede){
		$consulta = sprintf("select * from escandallo_log where id_produccion in 
								(select id_produccion from orden_produccion where activo=1 and id_sede=%s) group by codigo order by fecha_creacion desc, id desc",
		$this->makeValue($id_sede, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$resultados = $this->resultados;
	}

	// Funcion que actualiza las piezas usadas de orden_compra_referencias tras la generacion de los escandallos
	function actualizaPiezasUsadas($id_produccion,$id_ref,$total_piezas){
		// Obtenemos el id_orden_compra 
		$consulta = sprintf("select id_orden_compra from orden_compra inner join referencias on (referencias.id_proveedor = orden_compra.id_proveedor) where id_produccion=%s and referencias.id_referencia=%s and orden_compra.activo=1",
			$this->makeValue($id_produccion, "int"),
			$this->makeValue($id_ref, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$id_compra = $this->getPrimerResultado();
		$id_compra = $id_compra["id_orden_compra"];

		// Obtenemos el campo piezas_usadas de la Orden de Compra
		$consulta = sprintf("select piezas_usadas from orden_compra_referencias where id_orden=%s and id_referencia=%s",
			$this->makeValue($id_compra, "int"),
			$this->makeValue($id_ref, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$piezas_usadas = $this->getPrimerResultado();
		$piezas_usadas = $piezas_usadas["piezas_usadas"];
		$piezas_usadas = $piezas_usadas + $total_piezas;

		// Actualizamos el numero de piezas usadas de la orden de compra
		$consulta = sprintf("update orden_compra_referencias set piezas_usadas=%s where id_orden=%s and id_referencia=%s and activo=1",
			$this->makeValue($piezas_usadas, "int"),
			$this->makeValue($id_compra, "int"),
			$this->makeValue($id_ref, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()){
			return 1;
		}
		else {
			return 2;
		}
	}

	// Devuelve la cadena de un error según su identificador
	function getErrorMessage($error_num) {
		switch($error_num) {
			case 2:
				return 'Se produjo un error al actualizar las piezas usadas tras la generacion de los escandallos<br/>';
			break;
		}
	}
}
?>
