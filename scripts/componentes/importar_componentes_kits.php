<?php 
// Script parar importar los componentes kits de TORO 
set_time_limit(10000);
include("../../classes/mysql.class.php");
include("../../classes/basicos/cabina.class.php");
include("../../classes/basicos/periferico.class.php");
include("../../classes/basicos/kit.class.php");
include("../../classes/funciones/log_unificacion.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySql();
$cab = new Cabina();
$per = new Periferico();
$kit = new Kit();
$log = new Log_Unificacion();

// Obtenemos los ultimos componentes importados de TORO
$importadosSQL = "select * from componentes where activo=1 having timestampdiff(day,fecha_creacion,now()) = 0";
$db->setConsulta($importadosSQL);
$db->ejecutarConsulta();
$res_importados = $db->getResultados();

// Guardamos en un array los id_componentes importados recientemente
for($i=0;$i<count($res_importados);$i++){
	$array_importados[] = $res_importados[$i]["id_componente"];
}

// d($res_importados);
// d($array_importados);

echo '<br/>COMPONENTES KITS DE TORO<br/>';

// Obtenemos los kits de TORO que esten activos y pertenezcan a componentes activos de TORO 
$consulta = "select * from componentes_kits_toro where activo=1 and id_componente in 
				(select id_componente from componentes_toro where activo=1)";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res_toro = $db->getResultados();

for($i=0;$i<count($res_toro);$i++){
	// Datos del componente_kits de TORO
	$id_tipo_componente_toro = $res_toro[$i]["id_tipo_componente"];
	$id_componente_toro = $res_toro[$i]["id_componente"];
	$id_kit_toro = $res_toro[$i]["id_kit"];

	// Obtenemos los datos del componente principal que contiene el kit 
	$consultaSql = sprintf("select * from componentes_toro where activo=1 and id_componente=%s",
		$db->makeValue($id_componente_toro,"int"));
	$db->setConsulta($consultaSql);
	$db->ejecutarConsulta();
	$res_componente_toro = $db->getPrimerResultado();
	$nombre_componente_toro = $res_componente_toro["nombre"];
	$version_componente_toro = $res_componente_toro["version"];

	// Si no se elimino el componente
	if($res_componente_toro != NULL){
		echo '<br/>COMPONENTE TORO ['.$id_componente_toro.'] '.$nombre_componente_toro.' v'.$version_componente_toro.'<br/>';
		
		// Obtenemos los datos del kit de TORO
		$consultaSql = sprintf("select * from componentes_toro where activo=1 and id_componente=%s",
			$db->makeValue($id_kit_toro,"int"));
		$db->setConsulta($consultaSql);
		$db->ejecutarConsulta();
		$res_kit_toro = $db->getPrimerResultado();
		$nombre_kit_toro = $res_kit_toro["nombre"];
		$version_kit_toro = $res_kit_toro["version"];

		// Si no se elimino el kit
		if($res_kit_toro != NULL){
			echo 'KIT TORO ['.$id_kit_toro.'] '.$nombre_kit_toro.' v'.$version_kit_toro.'<br/>';

			// Con el nombre del componente y su version comprobamos si existe en la bbdd con el mismo id
			$consultaSql = sprintf("select * from componentes where activo=1 and nombre=%s and version=%s",
				$db->makeValue($nombre_componente_toro,"text"),
				$db->makeValue($version_componente_toro, "int"));
			$db->setConsulta($consultaSql);
			$db->ejecutarConsulta();
			$res_componente = $db->getPrimerResultado();
			$id_componente = $res_componente["id_componente"];
			$id_tipo = $res_componente["id_tipo"];
			$nombre_componente = $res_componente["nombre"];
			$version_componente = $res_componente["version"];		

			echo '<br/>COMPONENTE SMK ['.$id_componente.'] '.$nombre_componente.' v'.$version_componente.'<br/>';

			// Solo guardamos los kits de los componentes principales insertados recientemente 
			if(in_array($id_componente,$array_importados)){
				// Obtenemos los datos del componente de SMK en funcion del nombre y version del kit de TORO 
				$consultaSql = sprintf("select * from componentes where activo=1 and nombre=%s and version=%s",
					$db->makeValue($nombre_kit_toro,"text"),
					$db->makeValue($version_kit_toro, "int"));
				$db->setConsulta($consultaSql);
				$db->ejecutarConsulta();
				$res_kit = $db->getPrimerResultado();
				$id_kit = $res_kit["id_componente"];
				$nombre_kit = $res_kit["nombre"];
				$version_kit = $res_kit["version"];		

				// Si no se elimino el kit
				if($res_kit != NULL){
					echo 'KIT ['.$id_kit.'] '.$nombre_kit.' v'.$version_kit.'<br/>';
					echo 'Insertar KIT['.$id_kit_toro.'][TOR] => ['.$id_kit.'][SMK] en el componente ['.$id_componente.'][SMK]<br/><br/>';

					// Guardamos el kit en el componente
					$insertKitSql = sprintf("insert into componentes_kits (id_tipo_componente,id_componente,id_kit,fecha_creado,activo) values (%s,%s,%s,current_timestamp,1)",
						$db->makeValue($id_tipo, "int"),
						$db->makeValue($id_componente, "int"),
						$db->makeValue($id_kit, "int"));
					$db->setConsulta($insertKitSql);
					if($db->ejecutarSoloConsulta()){
						// Insertamos el log
						$mensaje_log = '<span style="color:green">Se ha insertado correctamente el KIT['.$id_kit.'][SMK] en el componente ['.$id_componente.'][SMK]</span><br/><br/>';
						$log->datosNuevoLog(NULL,"IMPORTAR_COMPONENTES_KITS (TORO)",$mensaje_log,$fecha);
						$res_log = $log->guardarLog();
						if($res_log == 1){
							echo $mensaje_log;
						}
						else echo 'Se produjo un error al guardar el LOG';	
					}
					else {
						echo '<span style:"color:red">Se produjo un error al insertar el kit en la tabla componentes_kits</span>';
					}
				}
				else {
					echo '<span style:"color:red">No existe el kit en la BBDD de SMK</span>';
				}
			}
			else {
				echo '<span style="color: green;">EL COMPONENTE DE TORO NO FUE IMPORTADO. MANTENEMOS EL DE SMK</span><br/>';
				echo '<span style="color: green;">NO ES NECESARIO ACTUALIZAR</span><br/><br/>';
			}
		}
		else {
			echo '<span style="color: red;">El kit ['.$id_kit_toro.'][TOR] se eliminó anteriormente de la BBDD de TORO y no es necesario importarlo </span><br/><br/>';
		}	
	}
	else {
		echo '<span style="color: red;">El componente ['.$id_componente_toro.'][TOR] se eliminó anteriormente de la BBDD de TORO y no es necesario importarlo </span><br/><br/>';
	}
}

// LOS USUARIOS DE BRASIL NO PUEDEN CREAR COMPONENTES Y NO SE UTILIZAN EN NINGUNA ORDEN DE PRODUCCION POR LO QUE NO EXPORTAMOS NINGUN COMPONENTE CREADO

?>
