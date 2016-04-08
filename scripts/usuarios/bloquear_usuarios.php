<?php 
set_time_limit(10000);
// Script para bloquear y resetear todos los usuarios
include("../../classes/mysql.class.php");
include("../../classes/basicos/usuario.class.php");
include("../../classes/funciones/log_unificacion.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySql();
$user = new Usuario();
$log = new Log_Unificacion();

// BLOQUEAMOS TODOS LOS USUARIOS Y LES ASIGNAMOS ROL DE ADMIN GLOBAL
$updateSql = "update usuarios set id_tipo=1, id_taller=0, id_almacen=0, bloqueado=1";
$db->setConsulta($updateSql);
if($db->ejecutarSoloConsulta()){
	// Insertamos el log
	$mensaje_log = '<span style="color:green">SE HAN RESETEADO CORRECTAMENTE TODOS LOS USUARIOS</span><br/>';
	$log->datosNuevoLog(NULL,"BLOQUEAR_USUARIOS",$mensaje_log,$fecha);
	$res_log = $log->guardarLog();
	if($res_log == 1){
		echo $mensaje_log;
	}
	else echo 'Se produjo un error al guardar el LOG';
}
else {
	echo "ERROR AL RESETEAR LOS USUARIOS";
}

?>

