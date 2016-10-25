<?php
session_start();
date_default_timezone_set('Europe/Madrid');
include("../classes/mysql.class.php");
include("../classes/ateneaUser.class.php");
$db = new MySQL();
$ateneaUser = new ateneaUser();
if($_SESSION["AT_loginok"] != 1) { 
	session_start();
	session_unset();
	session_destroy();
	header("Location:../");
} else {
	$ateneaUser->cargaDatosUsuarioId($_SESSION["AT_id_usuario"]);
}

function permiso($id_permiso) {
	global $db;
	$consultaSql = sprintf("select id from usuarios_permisos where id_usuario=%s and id_permiso=%s",
		$db->makeValue($_SESSION["AT_id_usuario"], "int"),
		$db->makeValue($id_permiso, "int"));
	$db->setConsulta($consultaSql); 
	$db->ejecutarSoloConsulta();
	if($db->getNumeroFilas() == 0) {
		header("Location:../usuario/no_permitido.php");
	}
}

function permisoMenu($id_permiso) {
	global $db;
	$consultaSql = sprintf("select id from usuarios_permisos where id_usuario=%s and id_permiso=%s",
		$db->makeValue($_SESSION["AT_id_usuario"], "int"),
		$db->makeValue($id_permiso, "int"));
	$db->setConsulta($consultaSql);
	$db->ejecutarSoloConsulta();
	if($db->getNumeroFilas() == 0) {
		return false;
	} else {
		return true;
	}
}
?>