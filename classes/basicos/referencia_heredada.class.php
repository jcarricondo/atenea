<?php
class Referencia_Heredada extends Referencia {

	var $id;
	var $id_referencia;
	var $id_referencia_heredada;
	var $cantidad;
	var $activo;
	var $fecha_creado;

	var $referencias_antecesor;
	var $referencias_heredadas;
	var $piezas_referencias_heredadas;


	function setReferenciasAntecesor($id_referencia,$referencias_antecesor){
		$this->id_referencia = $id_referencia;
		$this->referencias_antecesor = $referencias_antecesor;
	}

	function setReferenciasHeredadas($id_referencia,$referencias_heredadas,$piezas_referencias_heredadas) {
		$this->id_referencia = $id_referencia;
		$this->referencias_heredadas = $referencias_heredadas;
		$this->piezas_referencias_heredadas = $piezas_referencias_heredadas;
	}

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

	// Función que desactiva todas las referencias antecesor de una referencia
	function desactivarReferenciasAntecesor(){
		$updateSql = sprintf("update referencias_heredadas set activo=0 where id_ref_heredada=%s",
				$this->makeValue($this->id_referencia, "int"));
		$this->setConsulta($updateSql);
		if($this->ejecutarSoloConsulta()){
			return 1;
		}
		else return 3;
	}

	// Función que desactiva un antecesor dada una referencia heredada
	function desactivaAntecesorReferencia($id_ref_antecesor,$id_ref_heredada) {
		$updateSql = sprintf("update referencias_heredadas set activo=0 where id_referencia=%s and id_ref_heredada=%s",
				$this->makeValue($id_ref_antecesor, "int"),
				$this->makeValue($id_ref_heredada, "int"));
		$this->setConsulta($updateSql);
		if (!$this->ejecutarSoloConsulta()) $error_antecesor = true;
		return $error_antecesor;
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

	// Función que devuelve las referencias heredadas de la referencia y su cantidad
	function dameHeredadasPrincipalesYCantidad($id_referencia){
		$consulta = sprintf("select id_ref_heredada, cantidad from referencias_heredadas where activo=1 and id_referencia=%s order by id_ref_heredada",
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

	// Función que devuelve todas las referencias heredadas incluyendo a su descendencia de una referencia
	// Devuelve los nodos del grafo por nivel. Busqueda en anchura (BFS)
	function dameTodasHeredadasNivel($raiz){
		$cola = array();
		$array_final = array();
		$nodos_leidos = array();
		$nodos_leidos[] = $raiz;

		// Obtenemos los nodos del grafo
		$nodos_grafo = $this->dameNodosGrafo($raiz);
		$heredadas_raiz = $this->dameHeredadasPrincipales($raiz);
		if(!empty($heredadas_raiz)){
			foreach($heredadas_raiz as $res_hijo){
				$hijo = intval($res_hijo["id_ref_heredada"]);
				$cola[] = $hijo;
			}
		}

		$hay_heredadas = true;
		while ($hay_heredadas) {
			if(!empty($cola)){
				foreach($cola as $res_hijo){
					$hijo = intval($res_hijo);

					// Obtenemos los herederos de los hijos
					$res_herederos_hijos = $this->dameHeredadasPrincipales($hijo);
					$nodos_leidos[] = $hijo;
					$array_final[] = $hijo;

					if(!empty($res_herederos_hijos)){
						foreach($res_herederos_hijos as $res_nietos){
							$nieto = intval($res_nietos["id_ref_heredada"]);

							// Si alguno de los padres no ha sido procesado no lo añadimos a la cola
							$todosPadresLeidos = $this->padresLeidosEnGrafo($nieto,$nodos_grafo,$array_final);
							if($todosPadresLeidos) {
								if(!in_array($nieto, $cola)) $cola[] = $nieto;
							}
						}
					}
					array_shift($cola);
				}
			}
			// Salida del bucle
			$hay_heredadas = !empty($cola);
		}
		return $nodos_leidos;
	}

	// Función que devuelve los nodos de un grafo en función de su raiz
	function dameNodosGrafo($raiz){
		// Obtenemos todos los nodos del grafo
		$res_nodos = $this->dameTodasHeredadas($raiz);
		$res_nodos = $this->eliminarReferenciasHeredadasDuplicadas($res_nodos);
		if(!empty($res_nodos)) foreach($res_nodos as $nodo) $nodos[] = intval($nodo["id_ref_heredada"]);
		return $nodos;
	}

	// Función que comprueba si los padres de una referencia han sido leidos en el grafo
	function padresLeidosEnGrafo($id_referencia,$nodos,$cola){
		// Obtenemos los padres de la referencia
		$res_padres = $this->dameAntecesoresPrincipales($id_referencia);
		if(!empty($res_padres)) foreach($res_padres as $padre) $padres[] = intval($padre["id_referencia"]);

		$res_padres_grafo = array_intersect($padres,$nodos);
		if(!empty($res_padres_grafo)) foreach($res_padres_grafo as $value) $padres_grafo[] = intval($value);

		// Comprobamos que los padres de la referencia estan en la cola para ser leidos
		$estaPadreEnCola = true;
		$i = 0;
		while ($estaPadreEnCola && $i<count($padres_grafo)){
			$estaPadreEnCola = in_array($padres_grafo[$i],$cola);
 			$i++;
		}
		return $estaPadreEnCola;
	}

	// Función que devuelve todas las referencias heredadas de una referencia y sus piezas necesarias
	function dameTodasHeredadasPiezas($heredadas_por_nivel){
		foreach($heredadas_por_nivel as $nodo){
			$res_heredadas = $this->dameHeredadasPrincipalesYCantidad($nodo);

			if(!empty($res_heredadas)){
				foreach($res_heredadas as $heredadas){
					$id_ref = intval($heredadas["id_ref_heredada"]);
					$piezas = floatval($heredadas["cantidad"]);
					$matriz_piezas[$nodo][$id_ref] = $piezas;
				}
			}
		}

		if(!empty($matriz_piezas))
			foreach($matriz_piezas as $padre => $array_hijos){
				if(!empty($array_hijos))
					foreach($array_hijos as $hijo => $piezas){
						if(empty($array_final[$hijo])){
							if(empty($array_final[$padre])){
								// El padre es la raiz
								$array_final[$hijo] = $piezas;
							}
							else{
								$array_final[$hijo] = $piezas * $array_final[$padre];
							}
						}
						else{
							$array_final[$hijo] = $array_final[$hijo] + ($piezas * $array_final[$padre]);
						}
					}
			}
		return $array_final;
	}


	// Función que devuelve todas las referencias y las heredadas de varias referencias
	function dameTodasReferenciasIncluidasHeredadas($array_referencias){
		// Cambiamos la clave del array bidimensional para hacer la comprobación con las refs heredadas
		for($i=0;$i<count($array_referencias);$i++){
			$key = key($array_referencias[$i]);
			if($key == "id_referencia"){
				$array_referencias[$i]["id_ref_heredada"] = $array_referencias[$i]["id_referencia"];
				unset($array_referencias[$i]["id_referencia"]);
			}
			else $array_referencias[$i]["id_ref_heredada"] = $array_referencias[$i]["id_ref_heredada"];
		}
		$array_referencias_total = $array_referencias;

		// Combinamos las referencias y eliminamos las duplicadas
		for($i=0;$i<count($array_referencias);$i++){
			$id_referencia = $array_referencias[$i]["id_ref_heredada"];
			$res_heredadas = $this->dameTodasHeredadas($id_referencia);
			if(!empty($res_heredadas)){
				$array_referencias_total = array_merge($array_referencias_total,$res_heredadas);
			}
			else $array_referencias_total[$i]["id_ref_heredada"] = $id_referencia;
		}

		$array_referencias_total = $this->eliminarReferenciasHeredadasDuplicadas($array_referencias_total);

		// Volvemos a cambiar la clave del array bidimensional
		for($i=0;$i<count($array_referencias_total);$i++){
			$key = key($array_referencias_total[$i]);
			if($key == "id_ref_heredada"){
				$array_referencias_total[$i]["id_referencia"] = $array_referencias_total[$i]["id_ref_heredada"];
				unset($array_referencias_total[$i]["id_ref_heredada"]);
			}
			else $array_referencias_total[$i]["id_referencia"] = $array_referencias_total[$i]["id_referencia"];
		}
		return $array_referencias_total;
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

	// Función que quita las referencias heredadas duplicadas de un array
	function eliminarReferenciasHeredadasDuplicadas($array_id_referencias){
		$array_aux = null;
		for($i=0;$i<count($array_id_referencias);$i++){
			$id_referencia = $array_id_referencias[$i]["id_ref_heredada"];
			if($i == 0) $array_aux[] = $id_referencia;
			else if(!in_array($id_referencia,$array_aux)) $array_aux[] = $id_referencia;
		}
		for($i=0;$i<count($array_aux);$i++) $array_referencias[]["id_ref_heredada"] = $array_aux[$i];
		return $array_referencias;
	}

	/*
	// Función recursiva que devuelve un array con las referencias heredadas y sus piezas totales
	function dameHerederasYCantidad($id_referencia,&$array_piezas,&$array_final){
		$id_referencia_padre = $id_referencia;
		$res_heredadas_principales = $this->dameHeredadasPrincipalesYCantidad($id_referencia); d($res_heredadas_principales);
		$tiene_heredadas = !empty($res_heredadas_principales);

		if($tiene_heredadas){
			for($i=0;$i<count($res_heredadas_principales);$i++){
				$id_ref = intval($res_heredadas_principales[$i]["id_ref_heredada"]);
				$piezas = floatval($res_heredadas_principales[$i]["cantidad"]);

				$array_piezas[$id_ref][$id_referencia_padre] = $piezas;

				// Aplicamos recursividad
				$this->dameHerederasYCantidad($id_ref,$array_piezas,$array_final);
			}
		}
		return $array_piezas;
	}
	*/


	// Devuelve la cadena de un error según su identificador
	function getErrorMessage($error_num) {
		switch($error_num) {
			case 2:
				return 'Se produjo un error al desactivar las referencias herederas de la referencia<br/>';
			break;
			case 3:
				return 'Se produjo un error al desactivar las referencias antecesor de la referencia<br/>';
			break;
		}
	}
}
?>