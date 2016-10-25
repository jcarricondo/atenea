<?php 
set_time_limit(10000);
// Script parar importar los nombre_producto de TORO y BRASIL 
include("../../classes/mysql.class.php");
include("../../classes/basicos/nombre_producto.class.php");
include("../../classes/funciones/log_unificacion.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySql();
$nom = new Nombre_Producto();
$log = new Log_Unificacion();

echo '<br/>NOMBRE PRODUCTO DE TORO<br/>';
// Importamos los nombres de producto de TORO que no existan en SMK
$consulta = "select * from nombre_producto_toro where activo=1";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res_toro = $db->getResultados();

for($i=0;$i<count($res_toro);$i++){
	// Datos del nombre_producto de TORO
	$nombre = $res_toro[$i]["nombre"];
	$codigo = $res_toro[$i]["codigo"];
	$version = $res_toro[$i]["version"];
	$id_familia = $res_toro[$i]["id_familia"];

	$nom->datosNuevoProducto(NULL,$nombre,$codigo,$version,$id_familia);
	if(!$nom->comprobarProductoDuplicado()){
		// Guardamos el nombre de producto
		$res = $nom->guardarCambios();
		if($res == 1){
			// Insertamos el log
			$mensaje_log = '<span style="color:green">El nombre de producto ['.$nombre.'] se ha importado correctamente</span><br/><br/>';
			$log->datosNuevoLog(NULL,"IMPORTAR_NOMBRE_PRODUCTOS (TORO)",$mensaje_log,$fecha);
			$res_log = $log->guardarLog();
			if($res_log == 1){
				echo $mensaje_log;
			}
			else echo 'Se produjo un error al guardar el LOG';	
		}
		else {
			echo $nom->getErrorMessage($res);
		}
	}
}

/*
echo '<br/>NOMBRE PRODUCTO DE BRASIL<br/>';
// Importamos los nombres de producto de BRASIL que no existan en SMK
$consulta = "select * from nombre_producto_brasil where activo=1";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res_brasil = $db->getResultados();

for($i=0;$i<count($res_brasil);$i++){
	// Datos del nombre_producto de BRASIL
	$nombre = $res_brasil[$i]["nombre"];
	$codigo = $res_brasil[$i]["codigo"];
	$version = $res_brasil[$i]["version"];
	$id_familia = $res_brasil[$i]["id_familia"];

	$nom->datosNuevoProducto(NULL,$nombre,$codigo,$version,$id_familia);
	if(!$nom->comprobarProductoDuplicado()){
		// Guardamos el nombre de producto
		$res = $nom->guardarCambios();
		if($res == 1){
			echo '<span style="color:green">El nombre de producto ['.$nombre.'] se ha importado correctamente</span><br/>';
		}
		else {
			echo $nom->getErrorMessage($res);
		}
	}
}
*/

?>

