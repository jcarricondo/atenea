<?php 
set_time_limit(10000);
// Script para asignar permisos de administrador global a los usuarios administradores
include("../../classes/mysql.class.php");
include("../../classes/basicos/usuario.class.php");
include("../../classes/funciones/log_unificacion.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySql();
$user = new Usuario();
$log = new Log_Unificacion();

// ASIGNAR PERMISOS
$total_permisos = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,34,35,36,37,38,39,40,41,42,43);
$users_admin = array(1,2,3,5,7);
for($u=0;$u<count($users_admin);$u++){
	$id_usuario = $users_admin[$u];
	$user->cargaDatosUsuarioId($id_usuario);
	$nombre_usuario = $user->usuario;
	for($i=0;$i<count($total_permisos);$i++){
		$insertSql = sprintf("insert into usuarios_permisos (id_usuario,id_permiso,tipo) values (%s,%s,1)",
			$db->makeValue($id_usuario, "int"),
			$db->makeValue($total_permisos[$i], "int"));
		$db->setConsulta($insertSql);

		if($db->ejecutarSoloConsulta()){
			// Insertamos el log
			$mensaje_log = '<span style="color:green">SE HA GUARDADO EL PERMISO['.$total_permisos[$i].'] CORRECTAMENTE PARA EL USUARIO '.$nombre_usuario.'</span><br/>';
			$log->datosNuevoLog(NULL,"PERMISOS_ADMINISTRADORES",$mensaje_log,$fecha);
			$res_log = $log->guardarLog();
			if($res_log == 1){
				echo $mensaje_log;
			}
			else echo 'Se produjo un error al guardar el LOG';
		}
		else {
			echo "ERROR AL ASIGNAR EL PERMISO [".$total_permisos[$i]."] PARA EL USUARIO ".$nombre_usuario."<br/>";
		}
	}
}
?>

