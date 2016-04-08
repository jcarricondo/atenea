<?php
class GestionPrioridad extends MySql{

	var $id;
	var $id_referencia;
	var $total_piezas;
	
	// Atributos utilizados para OCR
	var $id_ocr;
	var $piezas_recibidas_ocr;
	

	function setReferencia($id_referencia,$total_piezas) {
		$this->id_referencia = $id_referencia;
		$this->total_piezas = $total_piezas;
	}

	function setOCReferencia($id_ocr,$piezas_recibidas_ocr) {
		$this->id_ocr = $id_ocr;
		$this->piezas_recibidas_ocr = $piezas_recibidas_ocr;
	}	

	// Funcion que hace el backup de las Ordenes de Compra de las OP Iniciadas
	function guardarReferenciaOC(){
		$insertSql = sprintf("insert into backup_orden_compra_referencias (id,piezas_recibidas) value (%s,%s)",
			$this->makeValue($this->id_ocr, "int"),
			$this->makeValue($this->piezas_recibidas_ocr, "float"));
		$this->setConsulta($insertSql);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		} 
		else {
			return 2;
		}
	}

	// Funcion que guarda las referencias agrupadas de las OP iniciadas 
	function guardarReferenciaAgrupacion(){
		$insertSql = sprintf("insert into agrupacion_piezas_recibidas (id_referencia,total_piezas) value (%s,%s)",
			$this->makeValue($this->id_referencia, "int"),
			$this->makeValue($this->total_piezas, "float"));
		$this->setConsulta($insertSql);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		} 
		else {
			return 3;
		}
	}

	// Funcion que restaura el backup
	function actualizarOCRBackup($id,$piezas_recibidas){
		$updateSql = sprintf("update orden_compra_referencias set piezas_recibidas=%s where id=%s",
			$this->makeValue($piezas_recibidas, "float"),
			$this->makeValue($id, "int"));
		$this->setConsulta($updateSql);
		if($this->ejecutarSoloConsulta()){
			return 1;
		}
		else{
			return 4;
		}
	}

	// Funcion que elimina las referencias agrupadas
	function borrarReferenciasAgrupadas(){
		$deleteSql = "delete from agrupacion_piezas_recibidas";
		$this->setConsulta($deleteSql);
		if($this->ejecutarSoloConsulta()){
			return 1;
		}
		else{
			return 5;
		}	
	}

	// Funcion que se encarga de resetear las piezas_recibidas de las OCR <> 0 de las OP Iniciadas
	function resetearPiezasRecibidasOP($id_produccion){
		$updateSql = sprintf("update orden_compra_referencias set piezas_recibidas=0 where activo=1 and piezas_recibidas <> 0 and id_orden in 
								(select id_orden_compra from orden_compra where activo=1 and id_produccion=%s)",
			$this->makeValue($id_produccion,"int"));
		$this->setConsulta($updateSql);
		if($this->ejecutarSoloConsulta()){
			return 1;
		}
		else{
			return 6;
		}
	}

	// Funcion que elimina el backup
	function borrarBackup(){
		$deleteSql = "delete from backup_orden_compra_referencias";
		$this->setConsulta($deleteSql);
		if($this->ejecutarSoloConsulta()){
			return 1;
		}
		else{
			return 7;
		}
	}

	// Funcion que actualiza la referencia agrupada despues de la recepcion
	function actualizarPiezasReferenciaAgrupada($piezas,$id_referencia){
		$updateSql = sprintf("update agrupacion_piezas_recibidas set total_piezas=%s where id_referencia=%s",
			$this->makeValue($piezas, "float"),
			$this->makeValue($id_referencia, "int"));
		$this->setConsulta($updateSql);
		if($this->ejecutarSoloConsulta()){
			return 1;
		}
		else{
			return 8;
		}	
	}

	// Funcion que borra las tablas de Agrupacion y Backup
	function borrarTablas(){
		$resultado_borrado_agrupacion = $this->borrarReferenciasAgrupadas();
		if($resultado_borrado_agrupacion == 1){
			// Borramos la parte del backup que se haya guardado 
			$resultado_borrado_backup = $this->borrarBackup();
			if($resultado_borrado_backup == 1){
				return 1;
			}
			else {
				return $resultado_borrado_backup;
			}
		}
		else {
			return $resultado_borrado_agrupacion;
		}	
	}

	// Funcion que restaura el backup a su estado original
	function restaurarBackup(){
		$resultados_preparar_backup = $this->prepararBackup();
		for($i=0;$i<count($resultados_preparar_backup);$i++){
			$id = $resultados_preparar_backup[$i]["id"];
			$piezas_recibidas = $resultados_preparar_backup[$i]["piezas_recibidas"];

			$resultado_actualizarOCRBackup = $this->actualizarOCRBackup($id,$piezas_recibidas);
			if($resultado_actualizarOCRBackup != 1){
				return $resultado_actualizarOCRBackup;
			}
		}
		return 1;		
	}

	// Funcion que prepara los resultados del backup
	function prepararBackup(){
		$consultaSql = "select * from backup_orden_compra_referencias";
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Funcion que obtiene TODOS los id_referencia, piezas_recibidas y total_piezas de las referencias de compra de una OP ordenadas por id_referencia
	function dameReferenciasCompraOrderReferencia($id_produccion){
		$consultaSql = sprintf("select id,id_referencia,total_piezas,piezas_recibidas from orden_compra_referencias where activo=1 and id_orden in
									(select id_orden_compra from orden_compra where activo=1 and id_produccion=%s) order by id_referencia",
			$this->makeValue($id_produccion, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Funcion que obtiene el total de piezas de la agrupacion de una referencia
	function dameTotalPiezasReferenciaAgrupacion($id_referencia){
		$consultaSql = sprintf("select total_piezas from agrupacion_piezas_recibidas where id_referencia=%s",
			$this->makeValue($id_referencia, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();
	}

	// Funcion que devuelve las piezas recibidas <> 0 de las Referencias de las Ordenes de Compra de una Orden de Produccion
	function damePiezasRecibidasReferenciasCompra($id_produccion){
		$consulta = sprintf('select id, piezas_recibidas from orden_compra_referencias where activo=1 and piezas_recibidas <> 0 and id_orden in 
			 					(select id_orden_compra from orden_compra where id_produccion=%s and activo=1)',	
			$this->makeValue($id_produccion,"int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();		
	}

	// Funcion que devuelve las referencias agrupadas de las Referencias de las Ordenes de Compra con piezas <> 0 y de OP iniciadas
	function dameReferenciasAgrupadas(){
		$consulta = 'select id_referencia, sum(piezas_recibidas) as total_piezas from orden_compra_referencias where piezas_recibidas <> 0 and activo=1 and id_orden in
						(select id_orden_compra from orden_compra where activo=1 and id_produccion in 
							(select id_produccion from orden_produccion where activo=1 and estado="INICIADO")) group by id_referencia order by id_referencia';
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();			
	}

	// Función que devuelve las referencias de las OP iniciadas que existan en mas de una OP
	function dameReferenciasVariasOP($id_sede){
		$consulta = sprintf("select id_referencia, count(id_referencia) as num_veces from orden_compra_referencias where activo=1 and id_orden in 
								(select id_orden_compra from orden_compra where activo=1 and id_produccion in 
									(select id_produccion from orden_produccion where activo=1 and estado='INICIADO' and id_sede=%s)) group by id_referencia
										having num_veces<>1 order by id_referencia",
			$this->makeValue($id_sede, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Funcion que devuelve TRUE si no se recibieron completamente las piezas de la referencia en todas las OP iniciadas
	function refCompletaEnTodasOp($id_referencia,$id_sede){
		$consulta = sprintf("select id from orden_compra_referencias where activo=1 and id_referencia=%s and total_piezas<>piezas_recibidas and id_orden in
								(select id_orden_compra from orden_compra where activo=1 and id_produccion in 
									(select id_produccion from orden_produccion where activo=1 and estado='INICIADO' and id_sede=%s))",
			$this->makeValue($id_referencia,"int"),
			$this->makeValue($id_sede, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$num_resultados = count($this->getResultados());
		if($num_resultados == 0){
			return true;
		}
		else {
			return false;
		}	
	}

	// Funcion que devuelve TRUE si nunca se recibieron piezas en todas las OP iniciadas
	function refVaciaEnTodasOp($id_referencia,$id_sede){
		$consulta = sprintf("select id from orden_compra_referencias where activo=1 and id_referencia=%s and piezas_recibidas<>0 and id_orden in
								(select id_orden_compra from orden_compra where activo=1 and id_produccion in 
									(select id_produccion from orden_produccion where activo=1 and estado='INICIADO' and id_sede=%s))",
			$this->makeValue($id_referencia,"int"),
			$this->makeValue($id_sede, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$num_resultados = count($this->getResultados());
		if($num_resultados == 0){
			return true;
		}
		else {
			return false;
		}	
	}

	// Funcion que devuelve el id y las piezas_recibidas de las ordenes de compra segun el id_referencia
	function dameOrdenesCompraRecibidasPorIdReferencia($id_referencia,$id_sede){
		$consulta = sprintf("select id,piezas_recibidas from orden_compra_referencias where activo=1 and piezas_recibidas<>0 and id_referencia=%s and id_orden in 
								(select id_orden_compra from orden_compra where activo=1 and id_produccion in
									(select id_produccion from orden_produccion where activo=1 and estado='INICIADO' and id_sede=%s))",
						$this->makeValue($id_referencia, "int"),
						$this->makeValue($id_sede, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Funcion que devuelve el id y las piezas_recibidas de las ordenes de compra segun el id_referencia para restablecer el BACKUP
	function dameOrdenesCompraBACKUP($id_referencia,$id_sede){
		$consulta = sprintf("select id,piezas_recibidas from orden_compra_referencias where activo=1 and id_referencia=%s and id_orden in 
								(select id_orden_compra from orden_compra where activo=1 and id_produccion in
									(select id_produccion from orden_produccion where activo=1 and estado='INICIADO' and id_sede=%s))",
						$this->makeValue($id_referencia, "int"),
						$this->makeValue($id_sede,"int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}



	/*
	// Funcion que devuelve las referencias de compra recibidas de las Ordenes de Produccion iniciadas y el numero de ordenes de produccion en las que aparece
	function dameReferenciasRecibidas(){
		$consulta = "select id_referencia,count(id_referencia) as num_veces from orden_compra_referencias where activo=1 and piezas_recibidas<>0 and id_orden in
						(select id_orden_compra from orden_compra where activo=1 and id_produccion in
							(select id_produccion from orden_produccion where activo=1 and estado='INICIADO')) group by id_referencia order by id_referencia";
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}
	*/

	


	// Devuelve la cadena de un error según su identificador
	function getErrorMessage($error_num) {
		switch($error_num) {
			case 2:
				return 'Se produjo un error de conexión al hacer un backup de las Referencias de Compra de las Órdenes de Producción. IMPORTANTE!! Recargue la página para que se haga el backup correctamente<br/>';
			break;
			case 3:
				return 'Se produjo un error de conexión al realizar la agrupación de referencias. Repita la operación pasados unos minutos<br/>';
			break;
			case 4:
				return 'Se produjo un error de conexión al restaurar el backup<br/>';
			break;
			case 5:
				return 'Se produjo un error de conexión al eliminar las referencias agrupadas.<br/>';
			break;
			case 6:
				return 'Se produjo un error de conexión al resetear las piezas recibidas de las Órdenes de Compra de una Orden de Producción. Repita la operación pasados unos minutos<br/>';
			break;
			case 7:
				return 'Se produjo un error al eliminar el backup<br/>';
			break;
			case 8:
				return 'Se produjo un error al actualizar las piezas de la agrupación. Repita la operación pasados unos minutos<br/>';
			break;	
		}	
	}	
}
?>
