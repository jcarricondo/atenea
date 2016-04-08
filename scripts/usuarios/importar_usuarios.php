<?php 
set_time_limit(10000);
// Script parar importar los usuarios de TORO y BRASIL 
include("../../classes/mysql.class.php");
include("../../classes/basicos/usuario.class.php");
include("../../classes/funciones/log_unificacion.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySql();
$user = new Usuario();
$log = new Log_Unificacion();

echo '<br/>USUARIOS DE TORO<br/>';
// Importamos los usuarios de TORO que no existan en SMK
$consulta = "select * from usuarios_toro where activo=1";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res_toro = $db->getResultados();

for($i=0;$i<count($res_toro);$i++){
	// Datos del usuario de TORO
	$usuario_toro = $res_toro[$i]["usuario"];
	$password_toro = $res_toro[$i]["password"];
	$email_toro = $res_toro[$i]["email"];
	$id_tipo = 0;
	$id_taller = 0;
	$id_almacen = 0;

	// Comprueba si existe el usuario en SMK
	$user->datosNuevoUsuario(NULL,$usuario_toro,$password_toro,$password_toro,$email_toro,$id_tipo,$id_taller,$id_almacen);
	if(!$user->comprobarUsuarioDuplicado()){
		// Guardamos el usuario 
		$res = $user->guardarCambios();
		if($res == 1){
			// Insertamos el log
			$mensaje_log = '<span style="color:green">El usuario ['.$usuario_toro.'] se ha importado correctamente</span><br/>';
			$log->datosNuevoLog(NULL,"IMPORTAR_USUARIOS (TORO)",$mensaje_log,$fecha);
			$res_log = $log->guardarLog();
			if($res_log == 1){
				echo $mensaje_log;
			}
			else echo 'Se produjo un error al guardar el LOG';
		}
		else {
			echo $user->getErrorMessage($res);
		}
	}
}

echo '<br/>USUARIOS DE BRASIL<br/>';
// Importamos los usuarios de BRASIL que no existan en SMK
$consulta = "select * from usuarios_brasil where activo=1";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res_brasil = $db->getResultados();

for($i=0;$i<count($res_brasil);$i++){
	// Datos del usuario de BRASIL
	$usuario_brasil = $res_brasil[$i]["usuario"];
	$password_brasil = $res_brasil[$i]["password"];
	$email_brasil = $res_brasil[$i]["email"];
	$id_tipo = 0;
	$id_taller = 0;
	$id_almacen = 0;

	// Comprueba si existe el usuario en SMK
	$user->datosNuevoUsuario(NULL,$usuario_brasil,$password_brasil,$password_brasil,$email_brasil,$id_tipo,$id_taller,$id_almacen);
	if(!$user->comprobarUsuarioDuplicado()){
		// Guardamos el usuario 
		$res = $user->guardarCambios();
		if($res == 1){
			// Insertamos el log
			$mensaje_log = '<span style="color:green">El usuario ['.$usuario_brasil.'] se ha importado correctamente</span><br/>';
			$log->datosNuevoLog(NULL,"IMPORTAR_USUARIOS (BRASIL)",$mensaje_log,$fecha);
			$res_log = $log->guardarLog();
			if($res_log == 1){
				echo $mensaje_log;
			}
			else echo 'Se produjo un error al guardar el LOG';
		}
		else {
			echo $user->getErrorMessage($res);
		}
	}
}


?>

