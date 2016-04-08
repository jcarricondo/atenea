<?php 
set_time_limit(10000);
// Script parar importar las referencias de las ordenes de compra de TORO
include("../../classes/mysql.class.php");
include("../../classes/orden_produccion/orden_produccion.class.php");
include("../../classes/orden_compra/orden_compra.class.php");
include("../../classes/funciones/log_unificacion.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySql();
$op = new Orden_Produccion();
$oc = new Orden_Compra();
$log = new Log_Unificacion();

echo '<br/>REFS OCS de TORO<br/>';
// Importamos las referencias de las  OCs de TORO que no existan en SMK
$consulta = "select * from orden_compra_referencias_toro where activo=1";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res_toro = $db->getResultados();

for($i=0;$i<count($res_toro);$i++){
	$id_orden = $res_toro[$i]["id_orden"];
	$id_referencia = $res_toro[$i]["id_referencia"];
	$uds_paquete = $res_toro[$i]["uds_paquete"];
	$piezas = $res_toro[$i]["piezas"];
	$total_piezas = $res_toro[$i]["total_piezas"];
	$piezas_recibidas = $res_toro[$i]["piezas_recibidas"];
	$piezas_usadas = $res_toro[$i]["piezas_usadas"];
	$total_packs = $res_toro[$i]["total_packs"];
	$pack_precio = $res_toro[$i]["pack_precio"];
	$coste = $res_toro[$i]["coste"];
	$fecha_creado = $res_toro[$i]["fecha_creado"];

	// Comprobamos cual es la Orden de Compra equivalente de TORO
	$consulta_oc = sprintf("select * from orden_compra_toro where activo=1 and id_orden_compra=%s",
		$db->makeValue($id_orden, "int"));
	$db->setConsulta($consulta_oc);
	$db->ejecutarConsulta();
	$res_oc = $db->getPrimerResultado();
	$id_produccion = $res_oc["id_produccion"];
	$id_proveedor = $res_oc["id_proveedor"];

	if($res_oc != NULL) {
		// Comprobamos cual es la Orden de Produccion equivalente de TORO
		$consulta_op = sprintf("select * from orden_produccion_toro where activo=1 and id_produccion=%s",
			$db->makeValue($id_produccion, "int"));
		$db->setConsulta($consulta_op);
		$db->ejecutarConsulta();
		$res_op = $db->getPrimerResultado();
		$alias_toro = $res_op["alias"];

		// Si existe la OP 
		if($res_op != NULL){
			//d($id_orden);
			//d($id_produccion);
			//d($id_proveedor);
			//d($alias_toro);			
			
			// Buscamos el nuevo id_produccion del OP con ese alias
			$consulta_op = sprintf("select * from orden_produccion where activo=1 and alias=%s",
				$db->makeValue($alias_toro,"text"));
			$db->setConsulta($consulta_op);
			$db->ejecutarConsulta();
			$res_op = $db->getPrimerResultado();
			$id_produccion_new = $res_op["id_produccion"];

			// d($id_produccion_new);


			// Obtenemos el id_orden_compra
			$consulta_id_compra = sprintf("select id_orden_compra from orden_compra where activo=1 and id_produccion=%s and id_proveedor=%s",
				$db->makeValue($id_produccion_new,"int"),
				$db->makeValue($id_proveedor, "int"));
			$db->setConsulta($consulta_id_compra);
			$db->ejecutarConsulta();
			$res_id_compra = $db->getPrimerResultado();
			$id_compra = $res_id_compra["id_orden_compra"];

			// d($id_compra);

			if($pack_precio == NULL) $pack_precio=0;
			if($id_referencia == 1821) $id_referencia = 1806;
			else if($id_referencia == 1823) $id_referencia = 1808;

			// UPDATE OCR
			$insertSql = sprintf("insert into orden_compra_referencias(id_orden,id_referencia,uds_paquete,piezas,total_piezas,piezas_recibidas,piezas_usadas,total_packs,pack_precio,coste,fecha_creado,activo) 
									values(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,1)",
				$db->makeValue($id_compra, "int"),
				$db->makeValue($id_referencia, "int"),
				$db->makeValue($uds_paquete, "int"),
				$db->makeValue($piezas, "float"),
				$db->makeValue($total_piezas, "float"),
				$db->makeValue($piezas_recibidas, "float"),
				$db->makeValue($piezas_usadas, "float"),
				$db->makeValue($total_packs, "int"),
				$db->makeValue($pack_precio,"float"),
				$db->makeValue($coste, "float"),
				$db->makeValue($fecha_creado, "date"));
			$db->setConsulta($insertSql);

			if($db->ejecutarSoloConsulta()){
				// Insertamos el log
				$mensaje_log = '<span style="color:green">Se ha guardado la ocr [ID_REF:'.$id_referencia.'][ID_OC:'.$id_compra.'] correctamente</span><br/><br/>';
				$log->datosNuevoLog(NULL,"IMPORTAR_OC_REFERENCIAS (TORO) - Importar referencias",$mensaje_log,$fecha);
				$res_log = $log->guardarLog();
				if($res_log == 1){
					echo $mensaje_log;
				}
				else echo 'Se produjo un error al guardar el LOG';	
			}
			else{
				echo '<span style="color:red">Se ha producido un error al guardar la ocr [ID_REF:'.$id_referencia.'][ID_OC:'.$id_compra.']</span><br/>';
			}
		}
	}
}

// Recorremos las referencias duplicadas y actualizamos en el caso de que algun componente contenga alguna de ellas 
$refDuplicadasSql = "select * from referencias_duplicadas";
$db->setConsulta($refDuplicadasSql);
$db->ejecutarConsulta();	
$res_duplicadas = $db->getResultados();

// d($res_duplicadas);

for($i=0;$i<count($res_duplicadas);$i++){
	$id_referencia = $res_duplicadas[$i]["id_referencia"];
	$id_referencia_duplicada = $res_duplicadas[$i]["id_referencia_duplicada"];

	// d($id_referencia);
	// d($id_referencia_duplicada);

	// Si alguna orden de compra activa tiene una referencia duplicada la actualizamos 
	$ocrSql = sprintf("select * from orden_compra_referencias where activo=1 and id_referencia=%s",
		$db->makeValue($id_referencia_duplicada,"int"));
	$db->setConsulta($ocrSql);
	$db->ejecutarConsulta();
	$res_ocr = $db->getResultados();

	// d($res_ref_componentes);

	if($res_ocr != NULL){
		for($j=0;$j<count($res_ocr);$j++){
			$id = $res_ocr[$j]["id"];
			$id_orden_compra = $res_ocr[$j]["id_orden"];
			
			// Actualizamos la referencia desactivada de la orden de compra
			$updateSql = sprintf("update orden_compra_referencias set id_referencia=%s where id=%s",
				$db->makeValue($id_referencia, "int"),
				$db->makeValue($id, "int"));
			$db->setConsulta($updateSql);
			if($db->ejecutarSoloConsulta()){
				// Insertamos el log
				$mensaje_log = '<span style="color:green;">Se ha actualizado la referencia ['.$id_referencia_duplicada.'] => ['.$id_referencia.'] en la orden de compra['.$id_orden_compra.']</span><br/>';
				$log->datosNuevoLog(NULL,"IMPORTAR_ORDEN_COMPRA_REFERENCIAS - Actualizar referencias duplicadas",$mensaje_log,$fecha);
				$res_log = $log->guardarLog();
				if($res_log == 1){
					echo $mensaje_log;
				}
				else echo 'Se produjo un error al guardar el LOG';
			}
			else {
				echo 'Se ha producido un error al actualizar la referencia del componente';
			}
		}
	}		
}
?>
