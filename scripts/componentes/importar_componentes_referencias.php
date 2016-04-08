<?php 
// Script parar importar las referencias de los componentes de TORO y BRASIL 
set_time_limit(10000);
include("../../classes/mysql.class.php");
include("../../classes/kint/Kint.class.php");
include("../classes/basicos/referencia.class.php");
include("../../classes/funciones/log_unificacion.class.php");

$db = new MySql();
$ref = new Referencia();
$log = new Log_Unificacion();

// Obtenemos los ultimos componentes importados de TORO
$importadosSQL = "select * from componentes where activo=1 having timestampdiff(day,fecha_creacion,now()) = 0";
$db->setConsulta($importadosSQL);
$db->ejecutarConsulta();
$res_importados = $db->getResultados();

// Guardamos en un array los id_componentes importados recientemente
for($i=0;$i<count($res_importados);$i++){
	$array_importados[] = $res_importados[$i]["id_componente"];
}

// d($res_importados);
// d($array_importados);

// Primero importamos las referencias de los componentes creados en TORO 
// Obtenenemos los componentes importados de TORO y cotejamos sus referencias en TORO
// Adaptamos las referencias del componente en el caso de que no coincidan con las de SMK

for($i=0;$i<count($array_importados);$i++){
	$id_componente = $array_importados[$i];

	// d($id_componente);

	// Obtenemos el nombre y la version del componente para poder obtener su id_componente de TORO
	$componenteSql = sprintf("select * from componentes where activo=1 and id_componente=%s",
		$db->makeValue($id_componente,"int"));
	$db->setConsulta($componenteSql);
	$db->ejecutarConsulta();
	$res_componentes = $db->getPrimerResultado();	

	// d($res_componentes);

	$nombre_toro = $res_componentes["nombre"];
	$version_toro = $res_componentes["version"];

	// d($nombre_toro);
	// d($version_toro);


	// Obtenemos el id_componente de TORO correspondiente en funcion del nombre y version del componente
	$componenteTOROSql = sprintf("select * from componentes_toro where activo=1 and nombre=%s and version=%s",
		$db->makeValue($nombre_toro,"text"),
		$db->makeValue($version_toro,"text"));
	$db->setConsulta($componenteTOROSql);
	$db->ejecutarConsulta();
	$res_componentesTORO = $db->getPrimerResultado();	

	// d($res_componentesTORO);

	$id_componente_toro = $res_componentesTORO["id_componente"];

	// d($id_componente_toro);

	// Obtenemos las referencias activas que tiene el componente en TORO 
	$refComponenteSql = sprintf("select * from componentes_referencias_toro where activo=1 and id_componente=%s",
		$db->makeValue($id_componente_toro,"int"));
	$db->setConsulta($refComponenteSql);
	$db->ejecutarConsulta();
	$res_ref_componentesTORO = $db->getResultados();

	// d($res_ref_componentesTORO);

	// Comprobamos si las referencias del componente coinciden en SMK y TORO 
	for($j=0;$j<count($res_ref_componentesTORO);$j++){
		$id_referencia_toro = $res_ref_componentesTORO[$j]["id_referencia"];
		$uds_paquete_toro = $res_ref_componentesTORO[$j]["uds_paquete"];
		$piezas_toro = $res_ref_componentesTORO[$j]["piezas"];
		$total_paquetes_toro = $res_ref_componentesTORO[$j]["total_paquetes"];
		$fecha_creado_toro = $res_ref_componentesTORO[$j]["fecha_creado"];

		// Comprobamos si la referencia de SMK no se desactivo 
		$referenciasSql = sprintf("select * from referencias where activo=1 and id_referencia=%s",
			$db->makeValue($id_referencia_toro,"int"));
		$db->setConsulta($referenciasSql);
		$db->ejecutarConsulta();
		$res_referencias = $db->getPrimerResultado();

		if($res_referencias != NULL){
			// Insertamos las referencia en el componente con el pack_precio de SMK
			$pack_precio = $res_referencias["pack_precio"];

			/*
			d($id_referencia_toro);
			d($uds_paquete_toro);
			d($piezas_toro);
			d($total_paquetes_toro);	
			d($pack_precio);
			d($fecha_creado_toro);
			*/

			$insertReferenciaSql = sprintf("insert into componentes_referencias (id_componente,id_referencia,uds_paquete,piezas,total_paquetes,pack_precio,fecha_creado)
										values (%s,%s,%s,%s,%s,%s,%s)",
				$db->makeValue($id_componente, "int"),
				$db->makeValue($id_referencia_toro, "int"),
				$db->makeValue($uds_paquete_toro, "int"),
				$db->makeValue($piezas_toro, "float"),
				$db->makeValue($total_paquetes_toro, "int"),
				$db->makeValue($pack_precio, "float"),
				$db->makeValue($fecha_creado_toro, "date"));
			$db->setConsulta($insertReferenciaSql);
			if($db->ejecutarSoloConsulta()){
				// Insertamos el log
				$mensaje_log = '<br/><span style="color:green;">Se ha guardado la referencia ['.$id_referencia_toro.'] en el componente importado['.$id_componente.'] correctamente</span><br/>';
				$log->datosNuevoLog(NULL,"IMPORTAR_COMPONENTES_REFERENCIAS - Referencias del componente de TORO",$mensaje_log,$fecha);
				$res_log = $log->guardarLog();
				if($res_log == 1){
					echo $mensaje_log;
				}
				else echo 'Se produjo un error al guardar el LOG';
			}
			else {
				echo 'Se produjo un error al guardar la referencia';
			}
		}
	}
}

// Ahora tenemos que actualizar todas las referencias de los componentes que se han desactivado por estar duplicadas
// Para ello recorremos las referencias duplicadas y consultamos con la tabla referencia componentes

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

	// Si algun componente activo tiene una referencia duplicada la actualizamos 
	$refComponenteSql = sprintf("select * from componentes_referencias where activo=1 and id_referencia=%s",
		$db->makeValue($id_referencia_duplicada,"int"));
	$db->setConsulta($refComponenteSql);
	$db->ejecutarConsulta();
	$res_ref_componentes = $db->getResultados();

	// d($res_ref_componentes);

	if($res_ref_componentes != NULL){
		for($j=0;$j<count($res_ref_componentes);$j++){
			$id = $res_ref_componentes[$j]["id"];
			$id_componente = $res_ref_componentes[$j]["id_componente"];
			
			// Actualizamos la referencia desactivada del componente 			
			$updateSql = sprintf("update componentes_referencias set id_referencia=%s where id=%s",
				$db->makeValue($id_referencia, "int"),
				$db->makeValue($id, "int"));
			$db->setConsulta($updateSql);
			if($db->ejecutarSoloConsulta()){
				// Insertamos el log
				$mensaje_log = '<br/><span style="color:green;">Se ha actualizado la referencia ['.$id_referencia_duplicada.'] => ['.$id_referencia.'] en el componente importado['.$id_componente.']</span><br/>';
				$log->datosNuevoLog(NULL,"IMPORTAR_COMPONENTES_REFERENCIAS - Actualizar referencias duplicadas",$mensaje_log,$fecha);
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

