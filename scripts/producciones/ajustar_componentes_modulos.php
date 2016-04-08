<?php 
set_time_limit(10000);
// Script para ajustar los componentes de los modulos de fabricacion de box de las sedes
include("../../classes/mysql.class.php");
include("../../classes/funciones/log_unificacion.class.php");

$db = new MySql();
$log = new Log_Unificacion();

echo '<br/>';
// Obtenemos los componentes de los modulos de TORO
$consulta = "select * from modulos_fabricacion_box where activo=1 and id_sede=2";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res = $db->getResultados();

$total_modulos = 0;
for($i=0;$i<count($res);$i++){
	$total_modulos++;
	$id = $res[$i]["id"];
	$id_componente_toro = $res[$i]["id_componente"];

	// Obtenemos los datos del componente de la tabla "componentes_toro"
	$consultaSql = sprintf("select * from componentes_toro where activo=1 and id_componente=%s",
		$db->makeValue($id_componente_toro,"int"));
	$db->setConsulta($consultaSql);
	$db->ejecutarConsulta();
	$res_componente_toro = $db->getPrimerResultado();
	$nombre_componente_toro = $res_componente_toro["nombre"];
	$version_componente_toro = $res_componente_toro["version"];

	echo '<br/>COMPONENTE TORO ['.$id_componente_toro.'] '.$nombre_componente_toro.' v'.$version_componente_toro;

	// Con el nombre del componente y su version comprobamos si existe en la bbdd con el mismo id
	$consultaSql = sprintf("select * from componentes where activo=1 and nombre=%s and version=%s",
		$db->makeValue($nombre_componente_toro,"text"),
		$db->makeValue($version_componente_toro, "int"));
	$db->setConsulta($consultaSql);
	$db->ejecutarConsulta();
	$res_componente = $db->getPrimerResultado();
	$id_componente = $res_componente["id_componente"];
	$nombre_componente = $res_componente["nombre"];
	$version_componente = $res_componente["version"];		

	echo '<br/>COMPONENTE &nbsp;SMK ['.$id_componente.'] '.$nombre_componente.' v'.$version_componente.'<br/>';

	if($id_componente_toro == $id_componente){
		if($nombre_componente_toro == $nombre_componente){
			if($version_componente_toro == $version_componente){
				echo '<span style="color: green;">El componente ['.$id_componente_toro.']['.$nombre_componente_toro.']_v'.$version_componente_toro.' de TORO coincide con el componente ['.$id_componente.']['.$nombre_componente.']_v'.$version_componente.' de  SMK</span><br/>';	
			}
			else {
				echo '<span style="color: orange;">Las versiones de los componentes no coinciden</span><br/>';	
			}
		}
		else {
			echo '<span style="color: orange;">Los nombres de los componentes no coinciden</span><br/>';	
		}
	}
	else {
		// ACTUALIZAR TABLA MODULOS_FABRICACION_BOX
		$updateSql = sprintf("update modulos_fabricacion_box set id_componente=%s where id=%s",
			$db->makeValue($id_componente,"int"),
			$db->makeValue($id, "int"));
		$db->setConsulta($updateSql);
		if($db->ejecutarSoloConsulta()){
			// Insertamos el log
			$mensaje_log = '<span style="color: green;">[ID:'.$id.'] Se ha actualizado el id_componente ['.$id_componente_toro.'] de TORO por el id_componente ['.$id_componente.'] de SMK</span><br/><br/>';
			$log->datosNuevoLog(NULL,"AJUSTAR_COMPONENTES_MODULOS",$mensaje_log,$fecha);
			$res_log = $log->guardarLog();
			if($res_log == 1){
				echo $mensaje_log;
			}
			else echo 'Se produjo un error al guardar el LOG';
		}
		else {
			echo '<span style="color: red;">[ID:'.$id.'] Se produjo un error al actualizar el id_componente ['.$id_componente_toro.'] de TORO por el id_componente ['.$id_componente.'] de SMK</span><br/>';	
		}
	}
}
echo '<br/>TOTAL MODULOS:'.$total_modulos; 
?>

