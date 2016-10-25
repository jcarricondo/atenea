<?php 
// En esta clase cargamos los datos de un componente de la tabla componentes_referencias o productos_referencias y otras operaciones 
// sobre los componentes
class Referencia_Componente extends MySQL {
	
	var $id;
	var $id_referencia;
	var $referencia;
	var $nombre_proveedor;
	var $nombre_pieza;
	var $tipo_pieza;
	var $referencia_proveedor;
	var $uds_paquete;
	var $piezas;
	var $total_paquetes;
	var $pack_precio;
	
	var $id_producto;
	var $id_componente;
	var $ids_kits;
	var $ids_referencias_xls;
	var $referencias_xls;
	var $referencias_componente;
	var $resultados;

	function cargarDatos($id,$id_referencia,$uds_paquete,$piezas,$total_paquetes,$pack_precio,$referencia,$nombre_proveedor,$nombre_pieza,$tipo_pieza,$referencia_proveedor) {
		$this->id = $id;
		$this->id_referencia = $id_referencia;
		$this->referencia = $referencia;
		$this->nombre_proveedor = $nombre_proveedor;
		$this->nombre_pieza = $nombre_pieza;
		$this->uds_paquete = $uds_paquete;
		$this->piezas = $piezas;
		$this->total_paquetes = $total_paquetes;
		$this->pack_precio = $pack_precio;
		$this->tipo_pieza = $tipo_pieza;
		$this->referencia_proveedor = $referencia_proveedor;
	}
	
	function cargaDatosReferenciaComponenteId($id) {
		$consultaSql = sprintf ("select componentes_referencias.id, componentes_referencias.id_referencia, componentes_referencias.uds_paquete, componentes_referencias.piezas, componentes_referencias.total_paquetes,componentes_referencias.pack_precio,referencias.referencia, proveedores.nombre_prov,referencias.part_nombre, referencias.part_tipo, referencias.part_proveedor_referencia from componentes_referencias inner join referencias on (referencias.id_referencia=componentes_referencias.id_referencia) inner join proveedores on (proveedores.id_proveedor=referencias.id_proveedor) where componentes_referencias.id=%s",
			$this->makeValue($id, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id"],
			$resultados["id_referencia"],
			$resultados["uds_paquete"],
			$resultados["piezas"],
			$resultados["total_paquetes"],
			$resultados["pack_precio"],
			$resultados["referencia"],
			$resultados["nombre_prov"],
			$resultados["part_nombre"],
			$resultados["part_tipo"],
			$resultados["part_proveedor_referencia"]);
	}
	
	function cargarDatosProductosReferencia($id,$id_producto,$id_componente,$id_referencia,$uds_paquete,$piezas,$total_paquetes,$pack_precio,$referencia,$nombre_proveedor,$nombre_pieza,$tipo_pieza,$referencia_proveedor) {
		$this->id = $id;
		$this->id_producto = $id_producto;
		$this->id_componente = $id_componente;
		$this->id_referencia = $id_referencia;
		$this->referencia = $referencia;
		$this->nombre_proveedor = $nombre_proveedor;
		$this->nombre_pieza = $nombre_pieza;
		$this->uds_paquete = $uds_paquete;
		$this->piezas = $piezas;
		$this->total_paquetes = $total_paquetes;
		$this->pack_precio = $pack_precio;
		$this->tipo_pieza = $tipo_pieza;
		$this->referencia_proveedor = $referencia_proveedor;
	}
	

	function calculaTotalPaquetes($uds_paquete,$num_pieza){
		if ($num_pieza<$uds_paquete) {
			$this->total_paquetes = 1;	
		}
		else {
			$resto = fmod($num_pieza,$uds_paquete);
			$tot_paquetes = floor($num_pieza/$uds_paquete);
			if ($resto == 0) $this->total_paquetes = $tot_paquetes;
			else $this->total_paquetes = $tot_paquetes + 1;
		}
	}

	// Devuelve los ids de los kits que pertenecen a un componente
	function dameIdsKitComponente($id_componente) {
		$consulta = sprintf("select id_kit from componentes_kits where componentes_kits.id_componente=%s and componentes_kits.activo=1 ",
			$this->makeValue($id_componente, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->ids_kits = $this->getResultados();
	}
	
	function dameIdsReferenciasXlsBasicos($ids,$es_interfaz,$es_kit){
		$consulta = "select * from componentes_referencias where (";
		if ($es_interfaz){
			// Si es interfaz solo hay un componente
			$consulta .= sprintf ("id_componente=%s)",
				$this->makeValue($ids, "int"));
		}
		else if ($es_kit){
			// Si es kit solo hay un componente
			$consulta .= sprintf ("id_componente=%s)",
				$this->makeValue($ids, "int"));
		}
		else {
			// Si es una cabina o un periferico
			for($i=0;$i<count($ids);$i++){
				// Si solo hay un id
				if (count($ids) == 1){
					$consulta .= sprintf ("id_componente=%s)",
						$this->makeValue($ids[$i], "int"));
				}
				// Para el ultimo id_componente
				else if ($i == count($ids)-1 ) {
					$consulta .= sprintf ("id_componente=%s) ",
						$this->makeValue($ids[$i], "int"));
				}
				else {
					$consulta .= sprintf ("id_componente=%s or ",
						$this->makeValue($ids[$i], "int"));
				}
			}
		}
		$consulta .= (" and componentes_referencias.activo=1 group by id_referencia");
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->ids_referencias_xls = $this->getResultados();
	}

	function dameDatosYTotalPiezasReferencia($ids,$es_interfaz,$es_kit,$id_referencia){
		$consulta = "select *, sum(piezas) as total_piezas from componentes_referencias where (";
		if ($es_interfaz){
			// Si es interfaz solo hay un componente
			$consulta .= sprintf ("id_componente=%s)", 	
				$this->makeValue($ids, "int"));
		}
		else if ($es_kit){
			// Si es kit solo hay un componente
			$consulta .= sprintf ("id_componente=%s)", 	
				$this->makeValue($ids, "int"));
		}
		else {
			for($i=0;$i<count($ids);$i++){
			// Si solo hay un id
				if (count($ids) == 1){
					$consulta .= sprintf ("id_componente=%s)", 	
						$this->makeValue($ids[$i], "int"));
				}
				// Para el ultimo id_componente
				else if ($i == count($ids)-1 ) {
					$consulta .= sprintf ("id_componente=%s) ", 	
						$this->makeValue($ids[$i], "int"));
				}
				else {
					$consulta .= sprintf ("id_componente=%s or ", 
						$this->makeValue($ids[$i], "int"));
				}
			}
		}
		$consulta .= sprintf(" and id_referencia=%s", 
			$this->makeValue($id_referencia, "int"));
		$consulta .= (" and componentes_referencias.activo=1");
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->referencias_xls = $this->getResultados();
	}
		
	// Devuelve las referencias de un id_componente
	function dameReferenciasPorIdComponente($id_componente) {
		$consultaSql = sprintf ("select * from componentes_referencias where componentes_referencias.id_componente=%s and activo=1 ",
			$this->makeValue($id_componente, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$this->referencias_componente = $this->getResultados();
	}

	// Desactiva las referencias de un componente
	function desactivaReferenciasPorIdComponente($id_componente) {
		$updateSql = sprintf ("update componentes_referencias set activo=0 where componentes_referencias.id_componente=%s ",
			$this->makeValue($id_componente, "int"));
		$this->setConsulta($updateSql);
		if($this->ejecutarSoloConsulta()){
			return 1;
		}
		else return 0;
	}
	
	// Comprueba si el id_Componente es un kit
	function esIdComponenteKit($id_componente){
		$consulta = sprintf ("select id_tipo from componentes where componentes.id_componente=%s and activo=1 ",
			$this->makeValue($id_componente, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		if($this->getNumeroFilas() != 0) {
			$this->resultados = $this->getResultados();
			if ($this->resultados[0]["id_tipo"] == 5) {
				return true;	
			}
			else { 
				return false;
			}
		} 
		else {
			return false;
		}	
	}

	// Recorre las referencias del kit y añade las referencias al componente
	function addReferenciasKitAlComponente($referencias_kit,$referencias_componente){
		unset($referencias_aux);
		$referencias_aux = $referencias_componente;
		for($i=0;$i<count($referencias_kit);$i++){
			$id_referencia = $referencias_kit[$i]["id_referencia"];
			$piezas = $referencias_kit[$i]["piezas"];
			$encontrado = false;
			$j=0;
			while(($j<count($referencias_componente)) and (!$encontrado)){
				// Si coinciden las referencias sumamos las piezas.
				if ($id_referencia == $referencias_componente[$j]["id_referencia"]){
					$referencias_aux[$j]["piezas"] = $referencias_aux[$j]["piezas"] + $piezas; 
					$encontrado = true;
				}
				$j++;	
			}
			if (!$encontrado){
				// Si no esta la referencia la insertamos al final
				if($referencias_aux != NULL){ 
					array_push($referencias_aux,$referencias_kit[$i]);
				}
				else {
					$referencias_aux[] = $referencias_kit[$i];
				}
			}
			// Modificamos el array de referencias del componente por el array modificado con las referencias del kit
			unset($referencias_componente);
			$referencias_componente = $referencias_aux;
		}
		return $referencias_componente;	
	}

}
?>