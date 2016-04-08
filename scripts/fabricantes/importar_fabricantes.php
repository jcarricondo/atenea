<?php 
set_time_limit(10000);
// Script parar importar los fabricantes de TORO y BRASIL 
include("../../classes/mysql.class.php");
include("../../classes/basicos/fabricante.class.php");
include("../../classes/funciones/log_unificacion.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySql();
$fab = new Fabricante();
$log = new Log_Unificacion();

echo '<br/>FABRICANTES DE TORO<br/>';
// Importamos los fabricantes de TORO que no existan en SMK
$consulta = "select * from fabricantes_toro where activo=1";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res_toro = $db->getResultados();

for($i=0;$i<count($res_toro);$i++){
	// Datos del fabricante de TORO
	$nombre = $res_toro[$i]["nombre_fab"];
	$descripcion = $res_toro[$i]["descripcion"];
	$direccion = $res_toro[$i]["direccion"];
	$ciudad = $res_toro[$i]["ciudad"];
	$pais = $res_toro[$i]["pais"];
	$telefono = $res_toro[$i]["telefono"];
	$email = $res_toro[$i]["email"];

	// Comprueba si existe el fabricante
	$fab->datosNuevoFabricante(NULL, $nombre,$descripcion,$direccion,$ciudad,$pais,$telefono,$email);
	if(!$fab->comprobarFabricanteDuplicado()){
		// Guardamos el fabricante
		$res = $fab->guardarCambios();
		if($res == 1){
			// Insertamos el log
			$mensaje_log = '<span style="color:green">El fabricante ['.$nombre.'] se ha importado correctamente</span><br/>';
			$log->datosNuevoLog(NULL,"IMPORTAR_FABRICANTES (TORO)",$mensaje_log,$fecha);
			$res_log = $log->guardarLog();
			if($res_log == 1){
				echo $mensaje_log;
			}
			else echo 'Se produjo un error al guardar el LOG';
		}
		else {
			echo $fab->getErrorMessage($res);
		}
	}
}

echo '<br/>FABRICANTES DE BRASIL<br/>';
// Importamos los fabricantes de BRASIL que no existan en SMK
$consulta = "select * from fabricantes_brasil where activo=1";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res_brasil = $db->getResultados();

for($i=0;$i<count($res_brasil);$i++){
	// Datos del fabricante de BRASIL
	$nombre = $res_brasil[$i]["nombre_fab"];
	$descripcion = $res_brasil[$i]["descripcion"];
	$direccion = $res_brasil[$i]["direccion"];
	$ciudad = $res_brasil[$i]["ciudad"];
	$pais = $res_brasil[$i]["pais"];
	$telefono = $res_brasil[$i]["telefono"];
	$email = $res_brasil[$i]["email"];

	// Comprueba si existe el fabricante
	$fab->datosNuevoFabricante(NULL,$nombre,$descripcion,$direccion,$ciudad,$pais,$telefono,$email);
	if(!$fab->comprobarFabricanteDuplicado()){
		// Guardamos el fabricante
		$res = $fab->guardarCambios();
		if($res == 1){
			// Insertamos el log
			$mensaje_log = '<span style="color:green">El fabricante ['.$nombre.'] se ha importado correctamente</span><br/>';
			$log->datosNuevoLog(NULL,"IMPORTAR_FABRICANTES (BRASIL)",$mensaje_log,$fecha);
			$res_log = $log->guardarLog();
			if($res_log == 1){
				echo $mensaje_log;
			}
			else echo 'Se produjo un error al guardar el LOG';
		}
		else {
			echo $fab->getErrorMessage($res);
		}
	}
}
?>

