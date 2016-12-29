<?php
class Referencia_Heredada extends Referencia {

	var $id;
	var $id_referencia;
	var $id_referencia_heredada;
	var $cantidad;
	var $activo;
	var $fecha_creado;

	var $referencias_heredadas;
	var $piezas_referencias_heredadas;

	/*
	function cargarDatos($id,$id_referencia,$id_referencia_heredada,$cantidad,$activo,$fecha_creado) {
		$this->id = $id;
		$this->id_referencia = $id_referencia;
		$this->id_referencia_heredada = $id_referencia_heredada;
		$this->cantidad = $cantidad;
		$this->activo = $activo;
		$this->fecha_creado = $fecha_creado;
	}

	function cargaDatosReferenciaId($id_referencia) {
		$consultaSql = sprintf("select * from referencias_heredadas where activo=1 and referencias_heredadas.id_referencia=%s",
				$this->makeValue($id_referencia, "int"));
		$this->setConsulta($consultaSql);
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
				$resultados["id"],
				$resultados["id_referencia"],
				$resultados["id_referencia_heredada"],
				$resultados["cantidad"],
				$resultados["activo"],
				$resultados["fecha_creado"]
		);
	}
	*/

	// Función que devuelve los antecesores de la referencia heredada
	function dameAntecesoresPrincipales($id_referencia){
		$consulta = sprintf("select id_referencia from referencias_heredadas where activo=1 and id_ref_heredada=%s order by id_referencia",
				$this->makeValue($id_referencia, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$res_antecesores = $this->getResultados();
		return $res_antecesores;
	}

	// Función que devuelve todos los antecesores de una referencia
	function dameTodosAntecesores($id_referencia){
		// Obtenemos los padres de la referencia
		$res_antecesores = $this->dameAntecesoresPrincipales($id_referencia);
		$array_antecesores_leidos = array();
		$hay_ancestros = true;
		while ($hay_ancestros){
			for($i=0;$i<count($res_antecesores);$i++){
				$id_ref_antecesor = $res_antecesores[$i]["id_referencia"];
				if(!in_array($id_ref_antecesor,$array_antecesores_leidos)){
					// Buscamos antecesores de un antecesor
					$res_padres_antecesores = $this->dameAntecesoresPrincipales($id_ref_antecesor);
					if($res_padres_antecesores != NULL) $res_antecesores = array_merge($res_antecesores,$res_padres_antecesores);
					// Añadimos el nodo al array de los leidos
					$array_antecesores_leidos[] = $id_ref_antecesor;
				}
			}
			$hay_ancestros = $res_antecesores === $array_antecesores_leidos;
		}
		return $res_antecesores;
	}

	// Función que devuelve las referencias heredadas de la referencia
	function dameHeredadasPrincipales($id_referencia){
		$consulta = sprintf("select id_ref_heredada from referencias_heredadas where activo=1 and id_referencia=%s order by id_ref_heredada",
				$this->makeValue($id_referencia, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$res_heredadas = $this->getResultados();
		return $res_heredadas;
	}

	// Función que devuelve todas las referencias heredadas incluyendo a su descendencia de una referencia
	function dameTodasHeredadas($id_referencia){
		// Obtenemos los hijos y descendencia de la referencia
		$res_heredadas = $this->dameHeredadasPrincipales($id_referencia);
		$array_heredadas_leidas = array();
		$hay_heredadas = true;
		while ($hay_heredadas){
			for($i=0;$i<count($res_heredadas);$i++){
				$id_ref_heredada = $res_heredadas[$i]["id_ref_heredada"];
				if(!in_array($id_ref_heredada,$array_heredadas_leidas)){
					// Buscamos heredadas de una heredada
					$res_hijos_heredadas = $this->dameHeredadasPrincipales($id_ref_heredada);
					if($res_hijos_heredadas != NULL) $res_heredadas = array_merge($res_heredadas,$res_hijos_heredadas);
					// Añadimos el nodo al array de los leidos
					$array_heredadas_leidas[] = $id_ref_heredada;
				}
			}
			$hay_heredadas = $res_heredadas === $array_heredadas_leidas;
		}
		return $res_heredadas;
	}



	// Función que devuelve la cantidad de piezas de la referencia heredada
	function dameCantidadPiezaHeredada($id_referencia,$id_referencia_heredada){
		$consulta = sprintf("select cantidad from referencias_heredadas where activo=1 and id_referencia=%s and id_ref_heredada=%s",
				$this->makeValue($id_referencia, "int"),
				$this->makeValue($id_referencia_heredada, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$res_cantidad_heredada = $this->getPrimerResultado();
		$res_cantidad_heredada = $res_cantidad_heredada["cantidad"];
		return $res_cantidad_heredada;
	}

	function setReferenciasHeredadas($id_referencia,$referencias_heredadas,$piezas_referencias_heredadas) {
		$this->id_referencia = $id_referencia;
		$this->referencias_heredadas = $referencias_heredadas;
		$this->piezas_referencias_heredadas = $piezas_referencias_heredadas;
	}

	// Función para guardar las referencias heredadas de una referencia
	function guardarReferenciasHeredadas() {
		$error_heredadas = false;
		for ($i = 0; $i < count($this->referencias_heredadas); $i++) {
			$id_referencia_heredada = $this->referencias_heredadas[$i];
			$piezas_referencia_heredada = $this->piezas_referencias_heredadas[$i];

			$insertSql = sprintf("insert into referencias_heredadas (id_referencia,id_ref_heredada,cantidad,activo,fecha_creado)
									values (%s,%s,%s,1,CURRENT_TIMESTAMP)",
					$this->makeValue($this->id_referencia, "int"),
					$this->makeValue($id_referencia_heredada, "int"),
					$this->makeValue($piezas_referencia_heredada, "float"));
			$this->setConsulta($insertSql);
			if (!$this->ejecutarSoloConsulta()) $error_heredadas = true;
		}
		return $error_heredadas;
	}

	// Función que desactiva todas las referencias herederas de una referencia
	function desactivarReferenciasHeredadas(){
		$updateSql = sprintf("update referencias_heredadas set activo=0 where id_referencia=%s",
			$this->makeValue($this->id_referencia, "int"));
		$this->setConsulta($updateSql);
		if($this->ejecutarSoloConsulta()){
			return 1;
		}
		else return 2;
	}

	// Devuelve la cadena de un error según su identificador
	function getErrorMessage($error_num) {
		switch($error_num) {
			case 2:
				return 'Se produjo un error al desactivar las referencias herederas de la referencia<br/>';
			break;
		}
	}
}
?>