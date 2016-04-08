<?php 
// Script parar importar los componentes kits de TORO y BRASIL 
set_time_limit(10000);
include("../../classes/mysql.class.php");
include("../../classes/basicos/cabina.class.php");
include("../../classes/basicos/periferico.class.php");
include("../../classes/basicos/kit.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySql();
$cab = new Cabina();
$per = new Periferico();
$kit = new Kit();

echo '<br/>COMPONENTES KITS DE TORO<br/>';
// Importamos los componentes_kits de TORO que no existan en SMK
$consulta = "select * from componentes_kits_toro where activo=1";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res_toro = $db->getResultados();

for($i=0;$i<count($res_toro);$i++){
	// Datos del componente_kits de TORO
	$id_tipo_componente_toro = $res_toro[$i]["id_tipo_componente"];
	$id_componente_toro = $res_toro[$i]["id_componente"];
	$id_kit_toro = $res_toro[$i]["id_kit"];

	// Comprobamos que el componentes principal no haya sido eliminado de la BBDD de TORO 	
	$consultaSql = sprintf("select * from componentes_toro where activo=1 and id_componente=%s",
		$db->makeValue($id_componente_toro,"int"));
	$db->setConsulta($consultaSql);
	$db->ejecutarConsulta();
	$res_componente_toro = $db->getPrimerResultado();
	$nombre_componente_toro = $res_componente_toro["nombre"];
	$version_componente_toro = $res_componente_toro["version"];

	//d($consultaSql);
	//d($res_componente_toro);

	// Si no se elimino el componente
	if($res_componente_toro != NULL){
		echo '<br/>COMPONENTE TORO ['.$id_componente_toro.'] '.$nombre_componente_toro.' v'.$version_componente_toro.'<br/>';
		// Comprobamos que el KIT no se haya eliminado de la BBDD de TORO 
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
			$nombre_componente = $res_componente["nombre"];
			$version_componente = $res_componente["version"];		

			echo '<br/>COMPONENTE SMK ['.$id_componente.'] '.$nombre_componente.' v'.$version_componente.'<br/>';

			// Con el nombre del kit y su version comprobamos si existe en la bbdd con el mismo id
			$consultaSql = sprintf("select * from componentes where activo=1 and nombre=%s and version=%s",
				$db->makeValue($nombre_kit_toro,"text"),
				$db->makeValue($version_kit_toro, "int"));
			$db->setConsulta($consultaSql);
			$db->ejecutarConsulta();
			$res_kit = $db->getPrimerResultado();
			$id_kit = $res_kit["id_componente"];
			$nombre_kit = $res_kit["nombre"];
			$version_kit = $res_kit["version"];		

			echo 'KIT ['.$id_kit.'] '.$nombre_kit.' v'.$version_kit.'<br/>';

			// Comprobamos si coinciden los componentes de las tablas de SMK y TORO
			if($id_componente_toro == $id_componente){
				if($nombre_componente_toro == $nombre_componente){
					if($version_componente_toro == $version_componente){
						echo '<span style="color: green;">El componente ['.$id_componente_toro.']['.$nombre_componente_toro.']_v'.$version_componente_toro.' de TORO coincide con el componente ['.$id_componente.']['.$nombre_componente.']_v'.$version_componente.' de  SMK</span><br/>';	
						if($id_kit_toro == $id_kit){
							if($nombre_kit_toro == $nombre_kit){	
								if($version_kit_toro == $version_kit){
									echo '<span style="color: green;">El kit ['.$id_kit_toro.']['.$nombre_kit_toro.']_v'.$version_kit_toro.' de TORO coincide con el kit ['.$id_kit.']['.$nombre_kit.']_v'.$version_kit.' de  SMK</span><br/>';			
									echo '<span style="color: green;">NO ES NECESARIO ACTUALIZAR</span><br/>';

									// Comprobamos si ya esta guardado el kit del componente en la BBDD de SMK
									$consultaSql = sprintf("select * from componentes_kits where id_componente=%s and id_kit=%s and activo=1",
										$db->makeValue($id_componente, "int"),
										$db->makeValue($id_kit, "int"));
									$db->setConsulta($consultaSql);
									$db->ejecutarConsulta();
									if($db->getNumeroFilas() != 0){
										echo '<span style="color: green;">NO ES NECESARIO GUARDAR EL KIT</span><br/><br/>';	
									}
									else echo "Insertar el kit";


								}
								else {
									echo '<span style="color: orange;">La version del kit ['.$version_kit_toro.'] de TORO no coincide con la version kit ['.$version_kit.'] de SMK</span><br/>';	
								}
							}
							else {
								echo '<span style="color: orange;">El nombre del kit ['.$nombre_kit_toro.'] de TORO no coincide con el nombre kit ['.$nombre_kit.'] de SMK</span><br/>';	
							}
						}	
						else {
							echo '<span style="color: orange;">El kit ['.$id_kit_toro.'] de TORO no coincide con el kit ['.$id_kit.'] de SMK</span><br/>';	
							// Los id no coinciden. Mostramos los datos:
							echo $id_kit_toro; echo "<br/>";
							echo $id_kit; echo "<br/>";
							echo $nombre_kit_toro; echo "<br/>";
							echo $nombre_kit; echo "<br/>";
							echo $version_kit_toro; echo "<br/>";
							echo $version_kit; echo "<br/>";

							// Comprobamos si ya esta guardado el kit del componente en la BBDD de SMK
							$consultaSql = sprintf("select * from componentes_kits where id_componente=%s and id_kit=%s and activo=1",
								$db->makeValue($id_componente, "int"),
								$db->makeValue($id_kit, "int"));
							$db->setConsulta($consultaSql);
							$db->ejecutarConsulta();
							if($db->getNumeroFilas() != 0){
								echo '<span style="color: green;">NO ES NECESARIO GUARDAR EL KIT</span><br/>';	
							}
							else echo "Insertar el kit";
						}
					}
					else {
						echo '<span style="color: orange;">La version del componente ['.$version_componente_toro.'] de TORO no coincide con la version componente ['.$version_componente.'] de SMK</span><br/>';	
					}
				}
				else {
					echo '<span style="color: orange;">El nombre del componente ['.$nombre_componente_toro.'] de TORO no coincide con el nombre componente ['.$nombre_componente.'] de SMK</span><br/>';	
				}
			}
			else {
				echo '<span style="color: orange;">El componente ['.$id_componente_toro.'] de TORO no coincide con el componente ['.$id_componente.'] de SMK</span><br/>';	
			}
			*/
		}
		else {
			echo '<span style="color: red;">El kit ['.$id_kit_toro.'][TOR] se eliminó anteriormente de la BBDD de TORO y no es necesario importarlo </span><br/>';
		}	
	}
	else {
		echo '<span style="color: red;">El componente ['.$id_componente_toro.'][TOR] se eliminó anteriormente de la BBDD de TORO y no es necesario importarlo </span><br/>';
	}
}

// LOS USUARIOS DE BRASIL NO PUEDEN CREAR COMPONENTES Y NO SE UTILIZAN EN NINGUNA ORDEN DE PRODUCCION POR LO QUE NO EXPORTAMOS NINGUN COMPONENTE CREADO
/*
echo '<br/>COMPONENTES KITS DE BRASIL<br/>';
// Importamos los componentes_kits de BRASIL que no existan en SMK
$consulta = "select * from componentes_kits_brasil where activo=1";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res_brasil = $db->getResultados();

for($i=0;$i<count($res_brasil);$i++){
	// Datos del componente_kits de BRASIL
	$id_tipo_componente_brasil = $res_brasil[$i]["id_tipo_componente"];
	$id_componente_brasil = $res_brasil[$i]["id_componente"];
	$id_kit_brasil = $res_brasil[$i]["id_kit"];

	// Obtenemos los datos del componente de la tabla "componentes_brasil"
	$consultaSql = sprintf("select * from componentes_brasil where activo=1 and id_componente=%s",
		$db->makeValue($id_componente_brasil,"int"));
	$db->setConsulta($consultaSql);
	$db->ejecutarConsulta();
	$res_componente_brasil = $db->getPrimerResultado();
	$nombre_componente_brasil = $res_componente_brasil["nombre"];
	$version_componente_brasil = $res_componente_brasil["version"];

	//d($consultaSql);
	//d($res_componente_toro);

	// Si no se elimino el componente
	if($res_componente_brasil != NULL){

		echo '<br/>COMPONENTE BRASIL ['.$id_componente_brasil.'] '.$nombre_componente_brasil.' v'.$version_componente_brasil.'<br/>';

		// Obtenemos los datos del kit de la tabla "componentes_brasil"
		$consultaSql = sprintf("select * from componentes_brasil where activo=1 and id_componente=%s",
			$db->makeValue($id_kit_brasil,"int"));
		$db->setConsulta($consultaSql);
		$db->ejecutarConsulta();
		$res_kit_brasil = $db->getPrimerResultado();
		$nombre_kit_brasil = $res_kit_brasil["nombre"];
		$version_kit_brasil = $res_kit_brasil["version"];

		// Si no se elimino el kit
		if($res_kit_brasil != NULL){
			echo 'KIT BRASIL ['.$id_kit_brasil.'] '.$nombre_kit_brasil.' v'.$version_kit_brasil.'<br/>';

			// Con el nombre del componente y su version comprobamos si existe en la bbdd con el mismo id
			$consultaSql = sprintf("select * from componentes where activo=1 and nombre=%s and version=%s",
				$db->makeValue($nombre_componente_brasil,"text"),
				$db->makeValue($version_componente_brasil, "int"));
			$db->setConsulta($consultaSql);
			$db->ejecutarConsulta();
			$res_componente = $db->getPrimerResultado();
			$id_componente = $res_componente["id_componente"];
			$nombre_componente = $res_componente["nombre"];
			$version_componente = $res_componente["version"];		

			echo '<br/>COMPONENTE SMK ['.$id_componente.'] '.$nombre_componente.' v'.$version_componente.'<br/>';

			// Con el nombre del kit y su version comprobamos si existe en la bbdd con el mismo id
			$consultaSql = sprintf("select * from componentes where activo=1 and nombre=%s and version=%s",
				$db->makeValue($nombre_kit_brasil,"text"),
				$db->makeValue($version_kit_brasil, "int"));
			$db->setConsulta($consultaSql);
			$db->ejecutarConsulta();
			$res_kit = $db->getPrimerResultado();
			$id_kit = $res_kit["id_componente"];
			$nombre_kit = $res_kit["nombre"];
			$version_kit = $res_kit["version"];		

			echo 'KIT ['.$id_kit.'] '.$nombre_kit.' v'.$version_kit.'<br/>';

			// Comprobamos si coinciden los componentes de las tablas de SMK y BRA
			if($id_componente_brasil == $id_componente){
				if($nombre_componente_brasil == $nombre_componente){
					if($version_componente_brasil == $version_componente){
						echo '<span style="color: green;">El componente ['.$id_componente_brasil.']['.$nombre_componente_brasil.']_v'.$version_componente_brasil.' de BRASIL coincide con el componente ['.$id_componente.']['.$nombre_componente.']_v'.$version_componente.' de  SMK</span><br/>';	
						if($id_kit_brasil == $id_kit){
							if($nombre_kit_brasil == $nombre_kit){	
								if($version_kit_brasil == $version_kit){
									echo '<span style="color: green;">El kit ['.$id_kit_brasil.']['.$nombre_kit_brasil.']_v'.$version_kit_brasil.' de BRASIL coincide con el kit ['.$id_kit.']['.$nombre_kit.']_v'.$version_kit.' de  SMK</span><br/>';			
									echo '<span style="color: green;">NO ES NECESARIO ACTUALIZAR</span><br/>';

									// Comprobamos si ya esta guardado el kit del componente en la BBDD de SMK
									$consultaSql = sprintf("select * from componentes_kits where id_componente=%s and id_kit=%s and activo=1",
										$db->makeValue($id_componente, "int"),
										$db->makeValue($id_kit, "int"));
									$db->setConsulta($consultaSql);
									$db->ejecutarConsulta();
									if($db->getNumeroFilas() != 0){
										echo '<span style="color: green;">NO ES NECESARIO GUARDAR EL KIT</span><br/>';	
									}
									else echo "Insertar el kit";
								}
								else {
									echo '<span style="color: orange;">La version del kit ['.$version_kit_brasil.'] de BRASIL no coincide con la version kit ['.$version_kit.'] de SMK</span><br/>';	
								}
							}
							else {
								echo '<span style="color: orange;">El nombre del kit ['.$nombre_kit_brasil.'] de BRASIL no coincide con el nombre kit ['.$nombre_kit.'] de SMK</span><br/>';	
							}
						}	
						else {
							echo '<span style="color: orange;">El kit ['.$id_kit_brasil.'] de BRASIL no coincide con el kit ['.$id_kit.'] de SMK</span><br/>';	
							// Los id no coinciden. Mostramos los datos:
							echo $id_kit_brasil; echo "<br/>";
							echo $id_kit; echo "<br/>";
							echo $nombre_kit_brasil; echo "<br/>";
							echo $nombre_kit; echo "<br/>";
							echo $version_kit_brasil; echo "<br/>";
							echo $version_kit; echo "<br/>";

							// Comprobamos si ya esta guardado el kit del componente en la BBDD de SMK
							$consultaSql = sprintf("select * from componentes_kits where id_componente=%s and id_kit=%s and activo=1",
								$db->makeValue($id_componente, "int"),
								$db->makeValue($id_kit, "int"));
							$db->setConsulta($consultaSql);
							$db->ejecutarConsulta();
							if($db->getNumeroFilas() != 0){
								echo '<span style="color: green;">NO ES NECESARIO GUARDAR EL KIT</span><br/>';	
							}
							else echo "Insertar el kit";
						}
					}
					else {
						echo '<span style="color: orange;">La version del componente ['.$version_componente_brasil.'] de BRASIL no coincide con la version componente ['.$version_componente.'] de SMK</span><br/>';	
					}
				}
				else {
					echo '<span style="color: orange;">El nombre del componente ['.$nombre_componente_brasil.'] de BRASIL no coincide con el nombre componente ['.$nombre_componente.'] de SMK</span><br/>';	
				}
			}
			else {
				echo '<span style="color: orange;">El componente ['.$id_componente_brasil.'] de BRASIL no coincide con el componente ['.$id_componente.'] de SMK</span><br/>';	
			}
		}
		else {
			echo '<span style="color: red;">El kit ['.$id_kit_brasil.'] se elimino anteriormente de la BBDD de BRASIL </span><br/>';
		}	
	}
	else {
		echo '<span style="color: red;">El componente ['.$id_componente_brasil.'][BRA] se elimino anteriormente de la BBDD de BRASIL </span><br/>';
	}
}
*/
?>
