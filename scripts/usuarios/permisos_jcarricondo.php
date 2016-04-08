<?php 
set_time_limit(10000);
// Script para asignar permisos de administrador global al usuario jcarrincondo
include("../../classes/mysql.class.php");
include("../../classes/basicos/usuario.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySql();
$user = new Usuario();

// ASIGNAR PERMISOS
$total_permisos = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34);
for($i=0;$i<count($total_permisos);$i++){
	$insertSql = sprintf("insert into usuarios_permisos (id_usuario,id_permiso,tipo) values (2,%s,1)",
		$db->makeValue($total_permisos[$i], "int"));
	$db->setConsulta($insertSql);

	if($db->ejecutarSoloConsulta()){
		echo "SE HA GUARDADO EL PERMISO CORRECTAMENTE<br/>";
	}
	else {
		echo "ERROR AL ASIGNAR EL PERMISO [".$total_permisos[$i]."]<br/>";
	}
}
?>

