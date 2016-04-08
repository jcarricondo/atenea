<?php 
set_time_limit(10000);
// Script para desbloquear todos los usuarios
include("../../classes/mysql.class.php");
include("../../classes/basicos/usuario.class.php");
include("../../classes/funciones/log_unificacion.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySql();
$user = new Usuario();
$log = new Log_Unificacion();

// DESBLOQUEAMOS TODOS LOS USUARIOS
$updateSql = "update usuarios set bloqueado=0";
$db->setConsulta($updateSql);
if($db->ejecutarSoloConsulta()){
	// Insertamos el log
	$mensaje_log = '<span style="color:green">SE HAN DESBLOQUEADO CORRECTAMENTE TODOS LOS USUARIOS</span><br/>';
	$log->datosNuevoLog(NULL,"DESBLOQUEAR_USUARIOS",$mensaje_log,$fecha);
	$res_log = $log->guardarLog();
	if($res_log == 1){
		echo $mensaje_log;
	}
	else echo 'Se produjo un error al guardar el LOG';

}
else {
	echo "ERROR AL DESBLOQUEAR LOS USUARIOS";
}

?>

