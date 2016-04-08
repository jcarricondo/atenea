<?php 
set_time_limit(10000);
// Script parar ajustar los usuarios y proveedores que pertenecen a un albaran de una pieza
include("../../classes/mysql.class.php");
include("../../classes/funciones/log_unificacion.class.php");

$db = new MySql();
$log = new Log_Unificacion();

echo '<br/>ALBARANES PIEZAS<br/>';
// Recorremos todos los albaranes piezas y ajustamos su id_usuario y su id_proveedor
$consulta = "select * from albaranes_piezas where activo=1";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res = $db->getResultados();

for($i=0;$i<count($res);$i++){
	// Datos de los albaranes de piezas
	$id_albaran = $res[$i]["id_albaran"];
	$nombre_albaran = $res[$i]["nombre_albaran"];
	// El participante es el proveedor de Brasil
	$id_proveedor_brasil = $res[$i]["id_participante"];
	// El usuario es el exportado de la tabla de BRASIl
	$id_usuario_brasil = $res[$i]["id_usuario"];

	// Obtenemos el nombre de proveedor de la tabla de proveedores_brasil
	$consulta_proveedor = sprintf("select nombre_prov from proveedores_brasil where activo=1 and id_proveedor=%s",
		$db->makeValue($id_proveedor_brasil, "int"));
	$db->setConsulta($consulta_proveedor);
	$db->ejecutarConsulta();
	$res_proveedor = $db->getPrimerResultado();
	$nombre_proveedor_brasil = $res_proveedor["nombre_prov"];	

	// Obtenemos el id_proveedor de la tabla proveedores
	$consulta_proveedor = sprintf("select id_proveedor from proveedores where activo=1 and nombre_prov=%s",
		$db->makeValue($nombre_proveedor_brasil, "text"));
	$db->setConsulta($consulta_proveedor);
	$db->ejecutarConsulta();
	$res_id_proveedor = $db->getPrimerResultado();
	$id_proveedor = $res_id_proveedor["id_proveedor"];

	// Obtenemos el nombre de usuario de la tabla de usuarios_brasil
	$consulta_usuario = sprintf("select usuario from usuarios_brasil where activo=1 and id_usuario=%s",
		$db->makeValue($id_usuario_brasil, "int"));
	$db->setConsulta($consulta_usuario);
	$db->ejecutarConsulta();
	$res_usuario = $db->getPrimerResultado();
	$nombre_usuario_brasil = $res_usuario["usuario"];

	// Obtenemos el id_usuario de la tabla usuarios
	// Si no coinciden los ids actualizamos el id_usuario
	$consulta_usuario = sprintf("select id_usuario from usuarios where activo=1 and usuario=%s",
		$db->makeValue($nombre_usuario_brasil, "text"));
	$db->setConsulta($consulta_usuario);
	$db->ejecutarConsulta();
	$res_id_usuario = $db->getPrimerResultado();
	$id_usuario = $res_id_usuario["id_usuario"];	

	// Si no se desactivó el usuario del albaran
	if($nombre_usuario_brasil != NULL){
		if($id_usuario != $id_usuario_brasil){
			// No coinciden los ids usuario. Actualizamos
			$update_albaranes = sprintf("update albaranes_piezas set id_usuario=%s where id_albaran=%s and activo=1",
				$db->makeValue($id_usuario, "int"),
				$db->makeValue($id_albaran, "int"));
			$db->setConsulta($update_albaranes);
			if($db->ejecutarSoloConsulta()){
				// Insertamos el log
				$mensaje_log = '<span style="color:green;">Se ha actualizado el usuario ['.$nombre_usuario_brasil.'] con id_usuario_brasil ['.$id_usuario_brasil.'] a id_usuario_smk['.$id_usuario.']</span><br/><br/>';
				$log->datosNuevoLog(NULL,"AJUSTAR_ALBARANES_PIEZAS (BRASIL)",$mensaje_log,$fecha);
				$res_log = $log->guardarLog();
				if($res_log == 1){
					echo $mensaje_log;
				}
				else echo 'Se produjo un error al guardar el LOG';
			}
			else {
				echo '<span style="color:red;">Se ha producido un error al actualizar el id_usuario de los albaranes piezas de Brasil</span><br/>';
			}
		}
		else {
			echo '<span style="color:green;">No hace falta actualizar el id del usuario ['.$nombre_usuario_brasil.']</span><br/>';			
		}
	}
	else {
		echo '<span style="color:orange;">El usuario ['.$nombre_usuario_brasil.'] fue desactivado en Brasil</span><br/>';
	}

	// Si no se desactivó el proveedor del albaran
	if($nombre_proveedor_brasil != NULL){
		if($id_proveedor != $id_proveedor_brasil){
			// No coinciden los id_proveedor. Actualizamos
			$update_albaranes = sprintf("update albaranes_piezas set id_proveedor=%s where id_albaran=%s and activo=1",
				$db->makeValue($id_proveedor, "int"),
				$db->makeValue($id_albaran, "int"));
			$db->setConsulta($update_albaranes);
			if($db->ejecutarSoloConsulta()){
				// Insertamos el log
				$mensaje_log = '<span style="color:green;">Se ha actualizado el proveedor ['.$nombre_proveedor_brasil.'] con id_proveedor_brasil ['.$id_proveedor_brasil.'] a id_proveedor_smk['.$id_proveedor.']</span><br/><br/>';
				$log->datosNuevoLog(NULL,"AJUSTAR_ALBARANES_PIEZAS (BRASIL)",$mensaje_log,$fecha);
				$res_log = $log->guardarLog();
				if($res_log == 1){
					echo $mensaje_log;
				}
				else echo 'Se produjo un error al guardar el LOG';
			}	
			else {
				echo '<span style="color:red;">Se ha producido un error al actualizar el id_proveedor de los albaranes piezas de Brasil</span><br/><br/>';
			}
		}
		else {
			echo '<span style="color:green;">No hace falta actualizar el id del proveedor ['.$nombre_proveedor_brasil.']</span><br/><br/>';			
		}
	}
	else {
		echo '<span style="color:orange;">El proveedor ['.$nombre_proveedor_brasil.'] fue desactivado en Brasil</span><br/>';
	}
}


// Recorremos las referencias duplicadas y actualizamos en el caso de que algun componente contenga alguna de ellas 
$refDuplicadasSql = "select * from referencias_duplicadas";
$db->setConsulta($refDuplicadasSql);
$db->ejecutarConsulta();	
$res_duplicadas = $db->getResultados();

// Ahora actualizamos la tabla de albaranes_piezas_log
for($i=0;$i<count($res_duplicadas);$i++){
	$id_referencia = $res_duplicadas[$i]["id_referencia"];
	$id_referencia_duplicada = $res_duplicadas[$i]["id_referencia_duplicada"];

	// d($id_referencia);
	// d($id_referencia_duplicada);

	// Si algun albaran activo tiene un log con referencia duplicada la actualizamos 
	$refLogSql = sprintf("select * from albaranes_piezas_log where activo=1 and id_referencia=%s",
		$db->makeValue($id_referencia_duplicada,"int"));
	$db->setConsulta($refLogSql);
	$db->ejecutarConsulta();
	$res_ref_alb = $db->getResultados();

	// d($res_ref_componentes);

	if($res_ref_alb != NULL){
		for($j=0;$j<count($res_ref_alb);$j++){
			$id = $res_ref_alb[$j]["id"];
			$id_referencia = $res_ref_alb[$j]["id_referencia"];
			$id_albaran = $res_ref_alb[$j]["id_albaran"];
			
			// Actualizamos la referencia desactivada del componente 			
			$updateSql = sprintf("update albaranes_piezas_log set id_referencia=%s where id=%s",
				$db->makeValue($id_referencia, "int"),
				$db->makeValue($id, "int"));
			$db->setConsulta($updateSql);
			if($db->ejecutarSoloConsulta()){
				// Insertamos el log
				$mensaje_log = '<br/><span style="color:green;">Se ha actualizado la referencia ['.$id_referencia_duplicada.'] => ['.$id_referencia.'] en el albaran['.$id_albaran.'] </span><br/>';
				$log->datosNuevoLog(NULL,"AJUSTAR ALBARANES_PIEZAS - Actualizar log con referencias duplicadas",$mensaje_log,$fecha);
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

// Ahora actualizamos la tabla de albaranes_piezas_referencias
for($i=0;$i<count($res_duplicadas);$i++){
	$id_referencia = $res_duplicadas[$i]["id_referencia"];
	$id_referencia_duplicada = $res_duplicadas[$i]["id_referencia_duplicada"];

	// d($id_referencia);
	// d($id_referencia_duplicada);

	// Si algun albaran activo tiene una referencia duplicada la actualizamos 
	$refPiezasSql = sprintf("select * from albaranes_piezas_referencias where activo=1 and id_referencia=%s",
		$db->makeValue($id_referencia_duplicada,"int"));
	$db->setConsulta($refPiezasSql);
	$db->ejecutarConsulta();
	$res_ref_alb = $db->getResultados();

	// d($res_ref_componentes);

	if($res_ref_alb != NULL){
		for($j=0;$j<count($res_ref_alb);$j++){
			$id = $res_ref_alb[$j]["id"];
			$id_referencia = $res_ref_alb[$j]["id_referencia"];
			$id_albaran = $res_ref_alb[$j]["id_albaran"];
			
			// Actualizamos la referencia desactivada del componente 			
			$updateSql = sprintf("update albaranes_piezas_referencias set id_referencia=%s where id=%s",
				$db->makeValue($id_referencia, "int"),
				$db->makeValue($id, "int"));
			$db->setConsulta($updateSql);
			if($db->ejecutarSoloConsulta()){
				// Insertamos el log
				$mensaje_log = '<br/><span style="color:green;">Se ha actualizado la referencia ['.$id_referencia_duplicada.'] => ['.$id_referencia.'] en el albaran['.$id_albaran.'] </span><br/>';
				$log->datosNuevoLog(NULL,"AJUSTAR ALBARANES_PIEZAS - Actualizar referencias duplicadas",$mensaje_log,$fecha);
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

// Ahora actualizamos la tabla stock donde estan guardadas las piezas de Brasil
for($i=0;$i<count($res_duplicadas);$i++){
	$id_referencia = $res_duplicadas[$i]["id_referencia"];
	$id_referencia_duplicada = $res_duplicadas[$i]["id_referencia_duplicada"];

	// d($id_referencia);
	// d($id_referencia_duplicada);

	// Si alguna pieza duplicada esta en el stock, independientemente de su almacen, la actualizamos 
	$refStockSql = sprintf("select * from stock where id_referencia=%s",
		$db->makeValue($id_referencia_duplicada,"int"));
	$db->setConsulta($refStockSql);
	$db->ejecutarConsulta();
	$res_stock = $db->getResultados();

	// d($res_ref_componentes);

	if($res_stock != NULL){
		for($j=0;$j<count($res_stock);$j++){
			$id = $res_stock[$j]["id"];
			$id_referencia = $res_stock[$j]["id_referencia"];
			$id_almacen = $res_stock[$j]["id_almacen"];
			
			// Actualizamos la referencia desactivada del stock
			$updateSql = sprintf("update stock set id_referencia=%s where id=%s",
				$db->makeValue($id_referencia, "int"),
				$db->makeValue($id, "int"));
			$db->setConsulta($updateSql);
			if($db->ejecutarSoloConsulta()){
				// Insertamos el log
				$mensaje_log = '<br/><span style="color:green;">Se ha actualizado la referencia ['.$id_referencia_duplicada.'] => ['.$id_referencia.'] del almacen ['.$id_almacen.'] </span><br/>';
				$log->datosNuevoLog(NULL,"AJUSTAR STOCK - Actualizar referencias duplicadas",$mensaje_log,$fecha);
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

