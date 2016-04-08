<?php 
set_time_limit(10000);
// Script parar ajustar los usuarios que pertenecen a un albaran de un periferico 
include("../../classes/mysql.class.php");
include("../../classes/funciones/log_unificacion.class.php");

$db = new MySql();
$log = new Log_Unificacion();

echo '<br/>ALBARANES PERIFERICOS<br/>';
// Recorremos todos los albaranes perifericos y ajustamos su id_usuario
$consulta = "select * from albaranes_perifericos where activo=1";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res = $db->getResultados();

for($i=0;$i<count($res);$i++){
	// Datos de los albaranes de perifericos
	$id_albaran = $res[$i]["id_albaran"];
	$nombre_albaran = $res[$i]["nombre_albaran"];
	// El usuario es el exportado de la tabla de BRASIl
	$id_usuario_brasil = $res[$i]["id_usuario"];

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
			// No coinciden los ids. Actualizamos
			$update_albaranes = sprintf("update albaranes_perifericos set id_usuario=%s where id_albaran=%s and activo=1",
				$db->makeValue($id_usuario, "int"),
				$db->makeValue($id_albaran, "int"));
			$db->setConsulta($update_albaranes);
			if($db->ejecutarSoloConsulta()){
				// Insertamos el log
				$mensaje_log = '<span style="color:green;">Se ha actualizado el usuario ['.$nombre_usuario_brasil.'] con id_usuario_brasil ['.$id_usuario_brasil.'] a id_usuario_smk['.$id_usuario.']</span><br/><br/>';
				$log->datosNuevoLog(NULL,"AJUSTAR_ALBARANES_PERIFERICOS (BRASIL)",$mensaje_log,$fecha);
				$res_log = $log->guardarLog();
				if($res_log == 1){
					echo $mensaje_log;
				}
				else echo 'Se produjo un error al guardar el LOG';
			}
			else {
				echo '<span style="color:red;">Se ha producido un error al actualizar el id_usuario de los albaranes perifericos de Brasil</span><br/>';
			}
		}
		else {
			echo '<span style="color:green;">No hace falta actualizar el id del usuario ['.$nombre_usuario_brasil.']</span><br/>';			
		}
	}
	else {
		echo '<span style="color:orange;">El usuario ['.$nombre_usuario_brasil.'] fue desactivado en Brasil</span><br/>';
	}
}
?>

