<?php 
set_time_limit(10000);
// Script parar importar los proveedores de TORO y BRASIL 
include("../../classes/mysql.class.php");
include("../../classes/basicos/proveedor.class.php");
include("../../classes/funciones/log_unificacion.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySql();
$prov = new Proveedor();
$log = new Log_Unificacion();

echo '<br/>PROVEEDORES DE TORO<br/>';
// Importamos los proveedores de TORO que no existan en SMK
$consulta = "select * from proveedores_toro where activo=1";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res_toro = $db->getResultados();

for($i=0;$i<count($res_toro);$i++){
	// Datos del proveedor de TORO
	$nombre = $res_toro[$i]["nombre_prov"];
	$descripcion = $res_toro[$i]["descripcion"];
	$direccion = $res_toro[$i]["direccion"];
	$ciudad = $res_toro[$i]["ciudad"];
	$pais = $res_toro[$i]["pais"];
	$telefono = $res_toro[$i]["telefono"];
	$email = $res_toro[$i]["email"];
	$forma_pago = $res_toro[$i]["forma_pago"];
	$tiempo_suministro = $res_toro[$i]["tiempo_suministro"];
	$metodo_pago = $res_toro[$i]["metodo_pago"];
	$provincia = $res_toro[$i]["provincia"];
	$codigo = $res_toro[$i]["codigo"];
	$persona_contacto = $res_toro[$i]["persona_contacto"];

	// Comprueba si existe el proveedor en SMK
	$prov->datosNuevoProveedor(NULL,$nombre,$descripcion,$direccion,$ciudad,$pais,$telefono,$email,$forma_pago,$tiempo_suministro,$metodo_pago,$provincia,$codigo_postal,$persona_contacto);
	if(!$prov->comprobarProveedorDuplicado()){
		// Guardamos el proveedor
		$res = $prov->guardarCambios();
		if($res == 1){
			// Insertamos el log
			$mensaje_log = '<span style="color:green">El proveedor ['.$nombre.'] se ha importado correctamente</span><br/>';
			$log->datosNuevoLog(NULL,"IMPORTAR_PROVEEDORES (TORO)",$mensaje_log,$fecha);
			$res_log = $log->guardarLog();
			if($res_log == 1){
				echo $mensaje_log;
			}
			else echo 'Se produjo un error al guardar el LOG';
		}
		else {
			echo $prov->getErrorMessage($res);
		}
	}
}

echo '<br/>PROVEEDORES DE BRASIL<br/>';
// Importamos los proveedores de BRASIL que no existan en SMK
$consulta = "select * from proveedores_brasil where activo=1";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res_brasil = $db->getResultados();

for($i=0;$i<count($res_brasil);$i++){
	// Datos del proveedor de BRASIL
	$nombre = $res_brasil[$i]["nombre_prov"];
	$descripcion = $res_brasil[$i]["descripcion"];
	$direccion = $res_brasil[$i]["direccion"];
	$ciudad = $res_brasil[$i]["ciudad"];
	$pais = $res_brasil[$i]["pais"];
	$telefono = $res_brasil[$i]["telefono"];
	$email = $res_brasil[$i]["email"];
	$forma_pago = $res_brasil[$i]["forma_pago"];
	$tiempo_suministro = $res_brasil[$i]["tiempo_suministro"];
	$metodo_pago = $res_brasil[$i]["metodo_pago"];
	$provincia = $res_brasil[$i]["provincia"];
	$codigo = $res_brasil[$i]["codigo"];
	$persona_contacto = $res_brasil[$i]["persona_contacto"];

	// Comprueba si existe el proveedor en SMK
	$prov->datosNuevoProveedor(NULL,$nombre,$descripcion,$direccion,$ciudad,$pais,$telefono,$email,$forma_pago,$tiempo_suministro,$metodo_pago,$provincia,$codigo_postal,$persona_contacto);
	if(!$prov->comprobarProveedorDuplicado()){
		// Guardamos el proveedor
		$res = $prov->guardarCambios();
		if($res == 1){
			// Insertamos el log
			$mensaje_log = '<span style="color:green">El proveedor ['.$nombre.'] se ha importado correctamente</span><br/>';
			$log->datosNuevoLog(NULL,"IMPORTAR_PROVEEDORES (BRASIL)",$mensaje_log,$fecha);
			$res_log = $log->guardarLog();
			if($res_log == 1){
				echo $mensaje_log;
			}
			else echo 'Se produjo un error al guardar el LOG';
		}
		else {
			echo $prov->getErrorMessage($res);
		}
	}
}
?>

