<?php 
set_time_limit(10000);
// Script para añadir permisos de los centros logisticos para los usuarios 
include("../../classes/mysql.class.php");
include("../../classes/basicos/usuario.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySql();
$user = new Usuario();

// ASIGNAR PERMISOS
$total_permisos = array(35,36,37);

$consulta_usuarios = "select * from usuarios";
$db->setConsulta($consulta_usuarios);
$db->ejecutarConsulta();
$res_usuarios = $db->getResultados();

for($i=0;$i<count($res_usuarios);$i++){
	$id_usuario = $res_usuarios[$i]["id_usuario"];
	$id_tipo = $res_usuarios[$i]["id_tipo"];

	switch ($id_tipo) {
		case 1:
			$actualizar_permisos = true;
		break;
		case 2:
			$actualizar_permisos = true;
		break;
		case 3:
			$actualizar_permisos = true;
		break;
		case 5:
			$actualizar_permisos = true;
		break;
		case 6:
			$actualizar_permisos = true;
		break;
		default:
			$actualizar_permisos = false;
		break;
	}

	if($actualizar_permisos){
		for($j=0;$j<count($total_permisos);$j++){
			$insertSql = sprintf("insert into usuarios_permisos (id_usuario,id_permiso) values (%s,%s)",
				$db->makeValue($id_usuario, "int"),
				$db->makeValue($total_permisos[$j], "int"));
			$db->setConsulta($insertSql);

			if($db->ejecutarSoloConsulta()){
				echo "SE HA GUARDADO EL PERMISO CORRECTAMENTE<br/>";
			}
			else {
				echo "ERROR AL ASIGNAR EL PERMISO [".$total_permisos[$j]."]<br/>";
			}
		}
	}
}
?>

