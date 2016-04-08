<?php 
// Script parar importar los registros de subida de archivos BRASIL
set_time_limit(10000); 
include("../../classes/mysql.class.php");
include("../../classes/basicos/cabina.class.php");
include("../../classes/basicos/periferico.class.php");
include("../../classes/basicos/software.class.php");
include("../../classes/basicos/interface.class.php");
include("../../classes/basicos/kit.class.php");
include("../../classes/funciones/log_unificacion.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySql();
$cab = new Cabina();
$per = new Periferico();
$sof = new Software();
$int = new Interfaz();
$kit = new Kit();
$log = new Log_Unificacion();

echo '<br/>COMPONENTES ARCHIVOS BRASIL<br/>';
// Importamos los archivos de BRASIL que no existan en SMK
$consulta = "select * from componentes_archivos_brasil where activo=1";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res_brasil = $db->getResultados();

for($i=0;$i<count($res_brasil);$i++){
	// Datos del archivo de BRASIL
	$id_componente_brasil = $res_brasil[$i]["id_componente"];
	$id_tipo_brasil = $res_brasil[$i]["id_tipo"];
	$nombre_archivo_brasil = $res_brasil[$i]["nombre_archivo"];

	// Obtenemos los datos del componente de la tabla "componentes_brasil"
	$consultaSql = sprintf("select * from componentes_brasil where activo=1 and id_componente=%s",
		$db->makeValue($id_componente_brasil,"int"));
	$db->setConsulta($consultaSql);
	$db->ejecutarConsulta();
	$res_componente_brasil = $db->getPrimerResultado();
	$nombre_componente_brasil = $res_componente_brasil["nombre"];
	$version_componente_brasil = $res_componente_brasil["version"];

	// Si no se elimino el componente
	if($res_componente_brasil != NULL){

		echo '<br/>COMPONENTE BRASIL ['.$id_componente_brasil.'] '.$nombre_componente_brasil.' v'.$version_componente_brasil.'<br/>';

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

		if($id_componente_brasil == $id_componente){
			if($nombre_componente_brasil == $nombre_componente){
				if($version_componente_brasil == $version_componente){
					echo '<span style="color: green;">El componente ['.$id_componente_brasil.']['.$nombre_componente_brasil.']_v'.$version_componente_brasil.' de BRASIL coincide con el componente ['.$id_componente.']['.$nombre_componente.']_v'.$version_componente.' de  SMK</span><br/>';	

					// Comprobamos si ya esta guardado el archivo del componente en la BBDD de SMK
					$consultaSql = sprintf("select * from componentes_archivos where id_componente=%s and nombre_archivo=%s and activo=1",
						$db->makeValue($id_componente, "int"),
						$db->makeValue($nombre_archivo_brasil, "text"));
					$db->setConsulta($consultaSql);
					$db->ejecutarConsulta();
					if($db->getNumeroFilas() != 0){
						echo '<span style="color: green;">NO ES NECESARIO GUARDAR EL ARCHIVO</span><br/>';	
					}
					else {
						echo "Guardar el archivo<br/>";
						echo "Guardando archivo...<br/>";

						$insertSql = sprintf("insert into componentes_archivos (id_componente,id_tipo,nombre_archivo,fecha_subida,activo)  values (%s,%s,%s,current_timestamp,1)",
							$db->makeValue($id_componente_brasil, "int"),
							$db->makeValue($id_tipo_brasil, "int"),
							$db->makeValue($nombre_archivo_brasil, "text"));
						$db->setConsulta($insertSql);
						if($db->ejecutarSoloConsulta()){
							// Insertamos el log
							$mensaje_log = '<span style="color:green">El archivo '.$nombre_archivo_brasil. 'se importo correctamente</span><br/><br/>';
							$log->datosNuevoLog(NULL,"IMPORTAR_COMPONENTES_ARCHIVOS (BRASIL)",$mensaje_log,$fecha);
							$res_log = $log->guardarLog();
							if($res_log == 1){
								echo $mensaje_log;
							}
							else echo 'Se produjo un error al guardar el LOG';	
						}
						else {
							echo '<span style="color:red;">Se produjo un error al exportar el archivo de Brasil</span>';
						}
					}
				}
			}
		}
		else {
			echo '<span style="color: orange;">El componente ['.$id_componente_brasil.'] de BRASIL no coincide con el componente ['.$id_componente.'] de SMK</span><br/>';	
			// Los id no coinciden. Mostramos los datos:
			echo $id_componente_brasil; echo "<br/>";
			echo $id_componente; echo "<br/>";
			echo $nombre_componente_brasil; echo "<br/>";
			echo $nombre_componente; echo "<br/>";
			echo $version_componente_brasil; echo "<br/>";
			echo $version_componente; echo "<br/>";

			// Comprobamos si ya esta guardado el archivo del componente en la BBDD de SMK
			$consultaSql = sprintf("select * from componentes_archivos where id_componente=%s and nombre_archivo=%s and activo=1",
				$db->makeValue($id_componente, "int"),
				$db->makeValue($nombre_archivo_brasil, "text"));
			$db->setConsulta($consultaSql);
			$db->ejecutarConsulta();
			if($db->getNumeroFilas() != 0){
				echo '<span style="color: green;">NO ES NECESARIO GUARDAR EL ARCHIVO</span><br/>';	
			}
			else {
				$id_tipo = 5;
				echo "Guardando archivo...<br/>";
				$insertSql = sprintf("insert into componentes_archivos (id_componente,id_tipo,nombre_archivo,fecha_subida,activo)  values (%s,%s,%s,current_timestamp,1)",
					$db->makeValue($id_componente, "int"),
					$db->makeValue($id_tipo, "int"),
					$db->makeValue($nombre_archivo_brasil, "text"));
				$db->setConsulta($insertSql);
				if($db->ejecutarSoloConsulta()){
					// Insertamos el log
					$mensaje_log = '<span style="color:green">El archivo '.$nombre_archivo_brasil. 'se importo correctamente</span><br/><br/>';
					$log->datosNuevoLog(NULL,"IMPORTAR_COMPONENTES_ARCHIVOS (BRASIL)",$mensaje_log,$fecha);
					$res_log = $log->guardarLog();
					if($res_log == 1){
						echo $mensaje_log;
					}
					else echo 'Se produjo un error al guardar el LOG';	
				}
				else {
					echo '<span style="color:red;">Se produjo un error al exportar el archivo de Brasil</span>';
				}
			}
		}
	}
	else {
		echo '<span style="color: red;">El componente ['.$id_componente_brasil.'] se elimino anteriormente de la BBDD de BRASIL </span><br/>';
	}
}


?>

