<?php
set_time_limit(10000);
// Script para importar los boxes de TORO y SMK
include("../../classes/mysql.class.php");
include("../../classes/funciones/log_unificacion.class.php");

$db = new MySql();
$log = new Log_Unificacion();

$num_boxes = 20;

// COMPONENTE 88
for($id_box=1;$id_box<=$num_boxes;$id_box++){
	$insertSql = sprintf("INSERT INTO modulos_fabricacion_box (id_componente,unidades_fabrican,id_box,id_sede,activo) VALUES (88,1,%s,2,1)",
		$db->makeValue($id_box, "int"));
	$db->setConsulta($insertSql);
	if($db->ejecutarSoloConsulta()){
		// Insertamos el log
		$mensaje_log = '<span style="color: green;">Se han insertado el modulo de fabricacion del componente 88 para el box ['.$id_box.']</span><br/><br/>';
		$log->datosNuevoLog(NULL,"IMPORTAR_MODULOS_NUEVOS",$mensaje_log,$fecha);
		$res_log = $log->guardarLog();
		if($res_log == 1){
			echo $mensaje_log;
		}
		else echo 'Se produjo un error al guardar el LOG';
	}
	else{
		echo '<span style="color: red;">Se produjo un error al insertar el modulo de fabricacion del componente 88 para el box ['.$id_box.']</span><br/>';	
	}
}	

// COMPONENTE 111
for($id_box=1;$id_box<=$num_boxes;$id_box++){
	$insertSql = sprintf("INSERT INTO modulos_fabricacion_box (id_componente,unidades_fabrican,id_box,id_sede,activo) VALUES (111,1,%s,2,1)",
		$db->makeValue($id_box, "int"));
	$db->setConsulta($insertSql);
	if($db->ejecutarSoloConsulta()){
		// Insertamos el log
		$mensaje_log = '<span style="color: green;">Se han insertado el modulo de fabricacion del componente 111 para el box ['.$id_box.']</span><br/><br/>';
		$log->datosNuevoLog(NULL,"IMPORTAR_MODULOS_NUEVOS",$mensaje_log,$fecha);
		$res_log = $log->guardarLog();
		if($res_log == 1){
			echo $mensaje_log;
		}
		else echo 'Se produjo un error al guardar el LOG';
	}
	else{
		echo '<span style="color: red;">Se produjo un error al insertar el modulo de fabricacion del componente 111 para el box ['.$id_box.']</span><br/>';	
	}
}				

?>
