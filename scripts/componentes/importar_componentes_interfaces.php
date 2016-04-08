<?php 
// Script parar importar los componentes de TORO y BRASIL 
set_time_limit(10000);
include("../../classes/mysql.class.php");
include("../../classes/basicos/cabina.class.php");
include("../../classes/basicos/periferico.class.php");
include("../../classes/basicos/interface.class.php");
include("../../classes/funciones/log_unificacion.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySql();
$cab = new Cabina();
$per = new Periferico();
$int = new Interfaz();
$log = new Log_Unificacion();

echo '<br/>COMPONENTES INTERFACES DE TORO<br/>';
// Importamos los componentes_interfaces de TORO que no existan en SMK
$consulta = "select * from componentes_interfaces_toro where activo=1";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res_toro = $db->getResultados();

for($i=0;$i<count($res_toro);$i++){
	// Datos del componente_interfaz de TORO
	$id_tipo_componente_toro = $res_toro[$i]["id_tipo_componente"];
	$id_componente_toro = $res_toro[$i]["id_componente"];
	$id_interfaz_toro = $res_toro[$i]["id_interfaz"];

	// Obtenemos los datos del componente de la tabla "componentes_toro"
	$consultaSql = sprintf("select * from componentes_toro where activo=1 and id_componente=%s",
		$db->makeValue($id_componente_toro,"int"));
	$db->setConsulta($consultaSql);
	$db->ejecutarConsulta();
	$res_componente_toro = $db->getPrimerResultado();
	$nombre_componente_toro = $res_componente_toro["nombre"];
	$version_componente_toro = $res_componente_toro["version"];

	d($consultaSql);
	d($res_componente_toro);

	// Si no se elimino el componente
	if($res_componente_toro != NULL){

		echo '<br/>COMPONENTE TORO ['.$id_componente_toro.'] '.$nombre_componente_toro.' v'.$version_componente_toro.'<br/>';

		// Obtenemos los datos de la interfaz de la tabla "componentes_toro"
		$consultaSql = sprintf("select * from componentes_toro where activo=1 and id_componente=%s",
			$db->makeValue($id_interfaz_toro,"int"));
		$db->setConsulta($consultaSql);
		$db->ejecutarConsulta();
		$res_interfaz_toro = $db->getPrimerResultado();
		$nombre_interfaz_toro = $res_interfaz_toro["nombre"];
		$version_interfaz_toro = $res_interfaz_toro["version"];

		// Si no se elimino la interfaz
		if($res_interfaz_toro != NULL){
			echo 'INTERFAZ TORO ['.$id_interfaz_toro.'] '.$nombre_interfaz_toro.' v'.$version_interfaz_toro.'<br/>';

			// Con el nombre del componente y su version comprobamos si existe en la bbdd con el mismo id
			$consultaSql = sprintf("select * from componente where activo=1 and nombre=%s and version=%s",
				$db->makeValue($nombre_componente_toro,"text"),
				$db->makeValue($version_componente_toro, "int"));
			$db->setConsulta($consultaSql);
			$db->ejecutarConsulta();
			$res_componente = $db->getPrimerResultado();
			$id_componente = $res_componente["id_componente"];
			$nombre_componente = $res_componente["nombre"];
			$version_componente = $res_componente["version"];		

			echo '<br/>COMPONENTE SMK ['.$id_componente.'] '.$nombre_componente.' v'.$version_componente.'<br/>';

			// Con el nombre de la interfaz y su version comprobamos si existe en la bbdd con el mismo id
			$consultaSql = sprintf("select * from componente where activo=1 and nombre=%s and version=%s",
				$db->makeValue($nombre_interfaz_toro,"text"),
				$db->makeValue($version_interfaz_toro, "int"));
			$db->setConsulta($consultaSql);
			$db->ejecutarConsulta();
			$res_interfaz = $db->getPrimerResultado();
			$id_interfaz = $res_interfaz["id_componente"];
			$nombre_interfaz = $res_interfaz["nombre"];
			$version_interfaz = $res_interfaz["version"];		

			echo 'INTERFAZ ['.$id_interfaz.'] '.$nombre_interfaz.' v'.$version_interfaz.'<br/>';
		}
	}
}




?>

