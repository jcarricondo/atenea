<?php 
// Script parar importar las referencias de las ordenes de produccion de TORO
set_time_limit(10000);
include("../../classes/mysql.class.php");
include("../../classes/orden_produccion/orden_produccion.class.php");
include("../../classes/funciones/log_unificacion.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySql();
$op = new Orden_Produccion();
$log = new Log_Unificacion();

echo '<br/>OPS REFERENCIAS de TORO<br/>';

// Primero insertamos tal cual las opr de toro en las opr de simumak
$consulta_opr_toro = "select * from orden_produccion_referencias_toro where activo=1";
$db->setConsulta($consulta_opr_toro);
$db->ejecutarConsulta();
$opr_toro = $db->getResultados();

// d($opr_toro);

for($i=0;$i<count($opr_toro);$i++){
	$id_produccion_toro = $opr_toro[$i]["id_produccion"];
	$id_tipo_componente_toro = $opr_toro[$i]["id_tipo_componente"];
	$id_produccion_componente_toro = $opr_toro[$i]["id_produccion_componente"];
	$id_componente_toro = $opr_toro[$i]["id_componente"];
	$id_referencia_toro = $opr_toro[$i]["id_referencia"];
	$uds_paquete_toro = $opr_toro[$i]["uds_paquete"];
	$piezas_toro = $opr_toro[$i]["piezas"];
	$total_paquetes_toro = $opr_toro[$i]["total_paquetes"];
	$pack_precio_toro = $opr_toro[$i]["pack_precio"];

	// Comprobamos la orden_produccion equivalente guardada en SMK
	$c_datos_op_toro = sprintf("select * from orden_produccion_toro where activo=1 and id_produccion=%s",
		$db->makeValue($id_produccion_toro, "int"));
	$db->setConsulta($c_datos_op_toro);
	$db->ejecutarConsulta();
	$datos_op_toro = $db->getResultados();

	if($datos_op_toro){
		// Comprobamos el componente equivalente guardado en SMK
		$c_datos_componente_toro = sprintf("select * from componentes_toro where activo=1 and id_componente=%s",
			$db->makeValue($id_componente_toro, "int"));
		$db->setConsulta($c_datos_componente_toro);
		$db->ejecutarConsulta();
		$datos_componente_toro = $db->getResultados();

		$id_comp_toro = $datos_componente_toro[0]["id_componente"];
		$nombre_toro = $datos_componente_toro[0]["nombre"];
		$vs_toro = $datos_componente_toro[0]["version"];

		// Comprobamos que los id_componentes son los mismos en ambas tablas 
		$c_componente = sprintf("select * from componentes where activo=1 and nombre=%s and version=%s",
			$db->makeValue($nombre_toro, "text"),
			$db->makeValue($vs_toro, "float"));
		$db->setConsulta($c_componente);
		$db->ejecutarConsulta();
		$datos_componente = $db->getResultados(); 

		$id_componente = $datos_componente[0]["id_componente"];

		if($id_componente != $id_comp_toro){
			/*
			d($id_comp_toro);
			d($nombre_toro);
			d($vs_toro);
			*/

			var_dump("No coinciden ".$id_componente."___".$id_comp_toro);

			$id_componente_toro = $id_componente;
		}

		// d($datos_op_toro);

		echo '<span style="color:green">-------------------------------------------------------------</span><br/>';


		$alias_toro = $datos_op_toro[0]["alias"];

		//d($alias_toro);

		// Obtenemos el id_produccion equivalente 
		$c_datos_op = sprintf("select id_produccion from orden_produccion where activo=1 and id_sede=2 and alias=%s",
			$db->makeValue($alias_toro, "text"));
		$db->setConsulta($c_datos_op);
		$db->ejecutarConsulta();
		$id_produccion = $db->getResultados();	
		$id_produccion = $id_produccion[0]["id_produccion"];

		//d($id_produccion);

		if($id_comp_toro == 425) $id_componente_toro = 429;
		if($id_referencia_toro == 1821) $id_referencia_toro = 1806;
		else if($id_referencia_toro == 1823) $id_referencia_toro = 1808;

		$resultado = $op->guardarReferenciasProduccion($id_produccion,$id_tipo_componente_toro,$id_produccion_componente_toro,$id_componente_toro,
								$id_referencia_toro,$uds_paquete_toro,$piezas_toro,$total_paquetes_toro,$pack_precio_toro);		

		if($resultado == 1){
			// Insertamos el log
			$mensaje_log = '<span style="color:green">Se ha guardado la opr [ID_REF:'.$id_referencia_toro.'][ID_COMP:'.$id_componente_toro.'][ID_PROD:'.$id_produccion.'] correctamente</span><br/><br/>';
			$log->datosNuevoLog(NULL,"IMPORTAR_OP_REFERENCIAS (TORO) - Importar referencias",$mensaje_log,$fecha);
			$res_log = $log->guardarLog();
			if($res_log == 1){
				echo $mensaje_log;
			}
			else echo 'Se produjo un error al guardar el LOG';	
		}
		else {
			echo 'Se ha producido un error al guardar las opr de TORO';
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

	// Si algun componente activo tiene una referencia duplicada la actualizamos 
	$refComponenteSql = sprintf("select * from orden_produccion_referencias where activo=1 and id_referencia=%s",
		$db->makeValue($id_referencia_duplicada,"int"));
	$db->setConsulta($refComponenteSql);
	$db->ejecutarConsulta();
	$res_ref_componentes = $db->getResultados();

	// d($res_ref_componentes);

	if($res_ref_componentes != NULL){
		for($j=0;$j<count($res_ref_componentes);$j++){
			$id = $res_ref_componentes[$j]["id"];
			$id_componente = $res_ref_componentes[$j]["id_componente"];
			$id_produccion = $res_ref_componentes[$j]["id_produccion"];
			
			// Actualizamos la referencia desactivada del componente 			
			$updateSql = sprintf("update orden_produccion_referencias set id_referencia=%s where id=%s",
				$db->makeValue($id_referencia, "int"),
				$db->makeValue($id, "int"));
			$db->setConsulta($updateSql);
			if($db->ejecutarSoloConsulta()){
				// Insertamos el log
				$mensaje_log = '<br/><span style="color:green;">Se ha actualizado la referencia ['.$id_referencia_duplicada.'] => ['.$id_referencia.'] en el componente importado['.$id_componente.'] de la OP['.$id_produccion.']</span><br/>';
				$log->datosNuevoLog(NULL,"IMPORTAR_ORDEN_PRODUCCION_REFERENCIAS - Actualizar referencias duplicadas",$mensaje_log,$fecha);
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

