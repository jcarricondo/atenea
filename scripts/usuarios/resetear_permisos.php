<?php 
set_time_limit(10000);
// Script para resetear permisos de todos los usuarios
include("../../classes/mysql.class.php");
include("../../classes/basicos/usuario.class.php");
include("../../classes/funciones/log_unificacion.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySql();
$user = new Usuario();
$log = new Log_Unificacion();

// RESETEAR PERMISOS DE TODOS LOS USUARIOS
$deleteSql = "delete from usuarios_permisos";
$db->setConsulta($deleteSql);
if($db->ejecutarSoloConsulta()){
	// Insertamos el log
	$mensaje_log = '<span style="color:green">SE HAN RESETEADO CORRECTAMENTE TODOS LOS PERMISOS DE LOS USUARIOS</span><br/>';
	$log->datosNuevoLog(NULL,"RESETEAR_PERMISOS",$mensaje_log,$fecha);
	$res_log = $log->guardarLog();
	if($res_log == 1){
		echo $mensaje_log;
	}
	else echo 'Se produjo un error al guardar el LOG';
}
else {
	echo "ERROR AL RESETEAR LOS PERMISOS DE LOS USUARIOS";
}

?>

