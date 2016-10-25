<?php 
set_time_limit(10000);
// Script para importar los albaranes del taller de TORO
include("../../classes/mysql.class.php");
include("../../classes/funciones/log_unificacion.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySql();
$log = new Log_Unificacion();

// Obtenemos todos los albaranes del taller de TORO
$consultaSql = "select * from talleres_albaranes_toro where activo=1";
$db->setConsulta($consultaSql);
$db->ejecutarConsulta();
$res = $db->getResultados();

d($res);
echo "<br/>TALLERES_ALBARANES de TORO<br/>";

for($i=0;$i<count($res);$i++){
	$id_albaran_toro = $res[$i]["id_albaran"];
	$nombre_albaran = $res[$i]["nombre_albaran"];
	$tipo_albaran = $res[$i]["tipo_albaran"];
	$id_participante_toro = $res[$i]["id_participante"];
	$id_tipo_participante = $res[$i]["id_tipo_participante"];
	$motivo = $res[$i]["motivo"];
	$id_usuario_toro = $res[$i]["id_usuario"];
	$fecha_creado_toro = $res[$i]["fecha_creado"];

	/*
	d($id_albaran_toro);
	d($nombre_albaran);
	d($tipo_albaran);
	d($id_participante_toro);
	d($id_tipo_participante);
	d($motivo);
	d($id_usuario_toro);
	d($fecha_creado_toro);
	*/

	// Cargamos los datos del usuario de TORO
	$cargaUsuarioToro = sprintf("select * from usuarios_toro where id_usuario=%s",
		$db->makeValue($id_usuario_toro, "int"));
	$db->setConsulta($cargaUsuarioToro);
	$db->ejecutarConsulta();
	$res_user = $db->getPrimerResultado();

	$nombre_usuario = $res_user["usuario"]; 
	d($nombre_usuario);

	// Obtenemos el id_usuario equivalente
	$consultaUser = sprintf("select * from usuarios where usuario=%s",
		$db->makeValue($nombre_usuario, "text"));
	$db->setConsulta($consultaUser); 
	$db->ejecutarConsulta();
	$res_id_user = $db->getPrimerResultado();

	$id_usuario = $res_id_user["id_usuario"];
	d($id_usuario);

	if($id_tipo_participante == 1){
		// Obtenemos el proveedor de TORO
		$cargaProveedorToro = sprintf("select * from proveedores_toro where id_proveedor=%s",
			$db->makeValue($id_participante_toro, "int"));
		$db->setConsulta($cargaProveedorToro);
		$db->ejecutarConsulta();
		$res_prov = $db->getPrimerResultado();
		$nombre_proveedor_toro = $res_prov["nombre_prov"];

		// Comprobamos con la tabla de proveedores de Simumak
		$consultaProv = sprintf("select * from proveedores where nombre_prov=%s",
			$db->makeValue($nombre_proveedor_toro, "text"));
		$db->setConsulta($consultaProv);
		$db->ejecutarConsulta();
		$res_proveedor = $db->getPrimerResultado();

		$id_proveedor = $res_proveedor["id_proveedor"];

		if($id_participante_toro != $id_proveedor) 	d("NO COINCIDEN");
	}

	$insertAlbaran = sprintf("insert into talleres_albaranes (nombre_albaran,tipo_albaran,id_participante,id_tipo_participante,motivo,id_usuario,id_taller,fecha_creado,activo)
						values(%s,%s,%s,%s,%s,%s,2,%s,1)",
		$db->makeValue($nombre_albaran, "text"),
		$db->makeValue($tipo_albaran, "text"),
		$db->makeValue($id_participante_toro, "int"),
		$db->makeValue($id_tipo_participante, "int"),
		$db->makeValue($motivo, "text"),
		$db->makeValue($id_usuario, "int"),
		$db->makeValue($fecha_creado_toro, "text"));
	$db->setConsulta($insertAlbaran);
	if($db->ejecutarSoloConsulta()){
		echo '<span style="color: green";> Se ha insertado el albaran ['.$nombre_albaran.'] de TORO del usuario ['.$id_usuario.'] en TALLERES_ALBARANES</span><br/>';
	
		$id_albaran = $db->getUltimoID();

		// INSERTAMOS LAS REFERENCIAS DEL ALBARAN DE TORO
		$consultaAlbRefs = sprintf("select * from talleres_albaranes_referencias_toro where activo=1 and id_albaran=%s",
								$db->makeValue($id_albaran_toro, "int"));
		$db->setConsulta($consultaAlbRefs);
		$db->ejecutarConsulta();
		$res_alb_refs = $db->getResultados();

		for($j=0;$j<count($res_alb_refs);$j++){
			// Recopilamos los datos 
			$id_referencia = $res_alb_refs[$j]["id_referencia"];
			$nombre_referencia = $res_alb_refs[$j]["nombre_referencia"];
			$nombre_proveedor = $res_alb_refs[$j]["nombre_proveedor"];
			$referencia_proveedor = $res_alb_refs[$j]["referencia_proveedor"];
			$nombre_pieza = $res_alb_refs[$j]["nombre_pieza"];
			$pack_precio = $res_alb_refs[$j]["pack_precio"];
			$unidades_paquete = $res_alb_refs[$j]["unidades_paquete"];
			$cantidad = $res_alb_refs[$j]["cantidad"];

			if($id_referencia == 1821) $id_referencia = 1806;
			if($id_referencia == 1823) $id_referencia = 1808;

			// Insertamos la referencia con su nuevo id_albaran
			$insertReferencia = sprintf("insert into talleres_albaranes_referencias (id_albaran,id_referencia,nombre_referencia,nombre_proveedor,referencia_proveedor,nombre_pieza,
											pack_precio,unidades_paquete,cantidad,activo) values(%s,%s,%s,%s,%s,%s,%s,%s,%s,1)",
									$db->makeValue($id_albaran, "int"),
									$db->makeValue($id_referencia, "int"),
									$db->makeValue($nombre_referencia, "text"),
									$db->makeValue($nombre_proveedor, "text"),
									$db->makeValue($referencia_proveedor, "text"),
									$db->makeValue($nombre_pieza, "text"),
									$db->makeValue($pack_precio, "float"),
									$db->makeValue($unidades_paquete, "int"),
									$db->makeValue($cantidad, "float"));
			$db->setConsulta($insertReferencia);
			if($db->ejecutarSoloConsulta()){
				echo '<span style="color: blue";> Se ha insertado la referencia ['.$id_referencia.'] con albaran ['.$id_albaran.'] en TALLERES_ALBARANES_REFERENCIAS</span><br/>';
			}
			else {
				echo '<span style="color: red";> Se ha producido un error al insertar la referencia ['.$id_referencia.'] con albaran ['.$id_albaran.'] en TALLERES_ALBARANES_REFERENCIAS</span><br/>';				
			}
		}	

		// INSERTAMOS EL LOG DEL ALBARAN
		$consultaAlbLog = sprintf("select * from talleres_albaranes_log_toro where activo=1 and id_albaran=%s",
								$db->makeValue($id_albaran_toro, "int"));
		$db->setConsulta($consultaAlbLog);
		$db->ejecutarConsulta();
		$res_alb_logs = $db->getResultados();

		for($j=0;$j<count($res_alb_logs);$j++){
			$id_referencia = $res_alb_logs[$j]["id_referencia"];
			$piezas = $res_alb_logs[$j]["piezas"];
			$metodo = $res_alb_logs[$j]["metodo"];
			$fecha_creado = $res_alb_logs[$j]["fecha_creado"];
			$id_produccion_toro = $res_alb_logs[$j]["id_produccion"];

			if($id_produccion_toro > 130) d($id_produccion_toro);

			if($id_produccion_toro != 0){
				// Comprobamos la orden_produccion equivalente guardada en SMK
				$c_datos_op_toro = sprintf("select * from orden_produccion_toro where activo=1 and id_produccion=%s",
										$db->makeValue($id_produccion_toro, "int"));
				$db->setConsulta($c_datos_op_toro);
				$db->ejecutarConsulta();
				$datos_op_toro = $db->getResultados();
				$alias_toro = $datos_op_toro[0]["alias"];

				$c_datos_op = sprintf("select id_produccion from orden_produccion where activo=1 and id_sede=2 and alias=%s",
									$db->makeValue($alias_toro, "text"));
				$db->setConsulta($c_datos_op);
				$db->ejecutarConsulta();
				$id_produccion = $db->getResultados();	
				$id_produccion = $id_produccion[0]["id_produccion"];
			}
			else $id_produccion = 0;

			if($id_referencia == 1821) $id_referencia = 1806;
			if($id_referencia == 1823) $id_referencia = 1808;

			if($id_produccion != NULL){
				// Insertamos el log con su nuevo id_albaran
				$insertLog = sprintf("insert into talleres_albaranes_log (id_albaran,id_referencia,id_produccion,piezas,metodo,fecha_creado,activo)
										values(%s,%s,%s,%s,%s,%s,1)",
								$db->makeValue($id_albaran, "int"),
								$db->makeValue($id_referencia, "int"),
								$db->makeValue($id_produccion, "int"),
								$db->makeValue($piezas, "float"), 
								$db->makeValue($metodo, "text"),
								$db->makeValue($fecha_creado, "text"));
				$db->setConsulta($insertLog);
				if($db->ejecutarSoloConsulta()){
					echo '<span style="color: purple";> Se ha insertado el log de la referencia ['.$id_referencia.'] con '.$piezas.' piezas con albaran ['.$id_albaran.'] con OP['.$id_produccion.'] en TALLERES_ALBARANES_LOG</span><br/>';
				}
				else {
					echo '<span style="color: red";> Se ha producido un error al insertar el log de la referencia ['.$id_referencia.'] con '.$piezas.' piezas con albaran ['.$id_albaran.'] en TALLERES_ALBARANES_LOG</span><br/>';
				}
			}
		}
	}	
	else {
		echo '<span style="color: red";> Se ha producido un error al insertar el albaran ['.$nombre_albaran.'] de TORO del usuario ['.$id_usuario.'] en TALLERES_ALBARANES</span><br/>';
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

	// Si algun log de un albaran activo tiene una referencia duplicada la actualizamos 
	$refTallerSql = sprintf("select * from talleres_albaranes_log where activo=1 and id_referencia=%s",
		$db->makeValue($id_referencia_duplicada,"int"));
	$db->setConsulta($refTallerSql);
	$db->ejecutarConsulta();
	$res_log_alb = $db->getResultados();

	// d($res_ref_componentes);

	if($res_log_alb != NULL){
		for($j=0;$j<count($res_log_alb);$j++){
			$id = $res_log_alb[$j]["id"];
			$id_referencia = $res_log_alb[$j]["id_referencia"];
			$id_albaran = $res_log_alb[$j]["id_albaran"];
			
			// Actualizamos la referencia desactivada del componente 			
			$updateSql = sprintf("update talleres_albaranes_log set id_referencia=%s where id=%s",
				$db->makeValue($id_referencia, "int"),
				$db->makeValue($id, "int"));
			$db->setConsulta($updateSql);
			if($db->ejecutarSoloConsulta()){
				// Insertamos el log
				$mensaje_log = '<span style="color:green;">Se ha actualizado la referencia ['.$id_referencia_duplicada.'] => ['.$id_referencia.'] en el albaran['.$id_albaran.'] </span><br/>';
				$log->datosNuevoLog(NULL,"AJUSTAR TALLERES_ALBARANES_LOG - Actualizar referencias duplicadas",$mensaje_log,$fecha);
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

// Ahora actualizamos la tabla de talleres_albaranes_referencias
for($i=0;$i<count($res_duplicadas);$i++){
	$id_referencia = $res_duplicadas[$i]["id_referencia"];
	$id_referencia_duplicada = $res_duplicadas[$i]["id_referencia_duplicada"];

	// d($id_referencia);
	// d($id_referencia_duplicada);

	// Si algun albaran activo tiene una referencia duplicada la actualizamos 
	$refTallerSql = sprintf("select * from talleres_albaranes_referencias where activo=1 and id_referencia=%s",
		$db->makeValue($id_referencia_duplicada,"int"));
	$db->setConsulta($refTallerSql);
	$db->ejecutarConsulta();
	$res_ref_alb = $db->getResultados();

	// d($res_ref_componentes);

	if($res_ref_alb != NULL){
		for($j=0;$j<count($res_ref_alb);$j++){
			$id = $res_ref_alb[$j]["id"];
			$id_albaran = $res_ref_alb[$j]["id_albaran"];
			
			// Actualizamos la referencia desactivada del componente 			
			$updateSql = sprintf("update talleres_albaranes_log set id_referencia=%s where id=%s",
				$db->makeValue($id_referencia, "int"),
				$db->makeValue($id, "int"));
			$db->setConsulta($updateSql);
			if($db->ejecutarSoloConsulta()){
				// Insertamos el log
				$mensaje_log = '<span style="color:green;">Se ha actualizado la referencia ['.$id_referencia_duplicada.'] => ['.$id_referencia.'] en el albaran['.$id_albaran.'] </span><br/>';
				$log->datosNuevoLog(NULL,"AJUSTAR TALLERES_ALBARANES_REFERENCIAS - Actualizar referencias duplicadas",$mensaje_log,$fecha);
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