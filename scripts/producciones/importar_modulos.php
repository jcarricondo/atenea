<?php 
set_time_limit(10000);
// Script para importar los modulos de fabricacion de TORO y SMK
include("../../classes/mysql.class.php");
include("../../classes/funciones/log_unificacion.class.php");

$db = new MySql();
$log = new Log_Unificacion();

echo '<br/>';
// Obtenemos los modulos de TORO
$consulta = "select * from modulos_fabricacion_box_toro";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res = $db->getResultados();

for($i=0;$i<count($res);$i++){
	$id_componente_toro = $res[$i]["id_componente"];
	$unidades_fabrican_toro = $res[$i]["unidades_fabrican"];
	$id_box_toro = $res[$i]["id_box"];
	$id_sede_toro = 2;

	// Insertamos los modulos
	$insertSql = sprintf("insert into modulos_fabricacion_box (id_componente,unidades_fabrican,id_box,id_sede,activo) values(%s,%s,%s,%s,1)",
		$db->makeValue($id_componente_toro, "int"),
		$db->makeValue($unidades_fabrican_toro, "int"),
		$db->makeValue($id_box_toro, "int"),
		$db->makeValue($id_sede_toro, "int"));
	$db->setConsulta($insertSql);
	if($db->ejecutarSoloConsulta()){
		// Insertamos el log
		$mensaje_log = '<span style="color: green;">ID_COMP['.$id_componente_toro.'] ID_BOX['.$id_box_toro.'] UF['.$unidades_fabrican_toro.'] SEDE['.$id_sede_toro.'] correctamente</span><br/><br/>';
		$log->datosNuevoLog(NULL,"IMPORTAR_MODULOS",$mensaje_log,$fecha);
		$res_log = $log->guardarLog();
		if($res_log == 1){
			echo $mensaje_log;
		}
		else echo 'Se produjo un error al guardar el LOG';
	}
	else{
		echo '<span style="color: red;">ERROR!! ID_COMP['.$id_componente_toro.'] ID_BOX['.$id_box_toro.'] UF['.$unidades_fabrican_toro.'] SEDE['.$id_sede_toro.']</span><br/>';	
	}
}

echo '<br/>';
// Obtenemos los modulos de SMK
$consulta = "select * from modulos_fabricacion_box_smk";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res = $db->getResultados();

for($i=0;$i<count($res);$i++){
	$id_componente = $res[$i]["id_componente"];
	$unidades_fabrican = $res[$i]["unidades_fabrican"];
	$id_box = $res[$i]["id_box"];
	// Adaptamos el box contado la nueva incorporacion de los boxes de toro
	$id_box = $id_box + 64;
	$id_sede = 1;

	// Insertamos los modulos
	$insertSql = sprintf("insert into modulos_fabricacion_box (id_componente,unidades_fabrican,id_box,id_sede,activo) values(%s,%s,%s,%s,1)",
		$db->makeValue($id_componente, "int"),
		$db->makeValue($unidades_fabrican, "int"),
		$db->makeValue($id_box, "int"),
		$db->makeValue($id_sede, "int"));
	$db->setConsulta($insertSql);
	if($db->ejecutarSoloConsulta()){
		// Insertamos el log
		$mensaje_log = '<span style="color: green;">ID_COMP['.$id_componente.'] ID_BOX['.$id_box.'] UF['.$unidades_fabrican.'] SEDE['.$id_sede.'] correctamente</span><br/><br/>';
		$log->datosNuevoLog(NULL,"IMPORTAR_MODULOS",$mensaje_log,$fecha);
		$res_log = $log->guardarLog();
		if($res_log == 1){
			echo $mensaje_log;
		}
		else echo 'Se produjo un error al guardar el LOG';
	}
	else{
		echo '<span style="color: red;">ERROR!! ID_COMP['.$id_componente.'] ID_BOX['.$id_box.'] UF['.$unidades_fabrican.'] SEDE['.$id_sede.']</span><br/>';	
	}
}
?>

