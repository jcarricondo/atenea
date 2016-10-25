<?php 
// Script parar importar los componentes de TORO y BRASIL 
set_time_limit(10000);
include("../../classes/mysql.class.php");
include("../../classes/basicos/cabina.class.php");
include("../../classes/basicos/periferico.class.php");
// include("../../classes/basicos/software.class.php");
include("../../classes/basicos/interface.class.php");
include("../../classes/basicos/kit.class.php");
include("../../classes/funciones/log_unificacion.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySql();
$cab = new Cabina();
$per = new Periferico();
// $sof = new Software();
$int = new Interfaz();
$kit = new Kit();
$log = new Log_Unificacion();

echo '<br/>COMPONENTES DE TORO<br/>';
// Importamos los componentes de TORO que no existan en SMK
$consulta = "select * from componentes_toro where activo=1";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res_toro = $db->getResultados();

for($i=0;$i<count($res_toro);$i++){
	// Datos del componente de TORO
	$id_componente_toro = $res_toro[$i]["id_componente"];
	$nombre = $res_toro[$i]["nombre"];
	$referencia = $res_toro[$i]["referencia"];
	$descripcion = $res_toro[$i]["descripcion"];
	$version = $res_toro[$i]["version"];
	$id_tipo = $res_toro[$i]["id_tipo"];
	$estado = $res_toro[$i]["estado"];
	$prototipo = $res_toro[$i]["prototipo"];

	switch ($id_tipo) {
		case '1':
			// CABINA
			$cab->datosNuevoCabina(NULL,$nombre,$referencia,$descripcion,$version,NULL,NULL,1,NULL,NULL,$estado,$prototipo,NULL);
			if(!$cab->comprobarCabinaDuplicada()){
				// Guardamos la cabina sin referencias, archivos, interfaces ni kits
				$res = $cab->guardarCambios();
				if($res == 1){
					// Insertamos el log
					$mensaje_log = '<span style="color:green">La cabina ['.$nombre.']['.$id_componente_toro.'-TOR] se ha importado correctamente</span><br/>';
					$log->datosNuevoLog(NULL,"IMPORTAR_COMPONENTES (TORO)",$mensaje_log,$fecha);
					$res_log = $log->guardarLog();
					if($res_log == 1){
						echo $mensaje_log;
					}
					else echo 'Se produjo un error al guardar el LOG';
				}
				else {
					echo $cab->getErrorMessage($res);
				}
			}	
		break;
		case '2':
			// PERIFERICO
			$per->datosNuevoPeriferico(NULL,$nombre,$referencia,$descripcion,$version,NULL,NULL,2,NULL,NULL,$estado,$prototipo,NULL);
			if(!$per->comprobarPerifericoDuplicado()){
				// Guardamos el periferico sin referencias, archivos, interfaces ni kits
				$res = $per->guardarCambios();
				if($res == 1){
					// Insertamos el log
					$mensaje_log = '<span style="color:green">El periferico ['.$nombre.']['.$id_componente_toro.'-TOR] se ha importado correctamente</span><br/>';
					$log->datosNuevoLog(NULL,"IMPORTAR_COMPONENTES (TORO)",$mensaje_log,$fecha);
					$res_log = $log->guardarLog();
					if($res_log == 1){
						echo $mensaje_log;
					}
					else echo 'Se produjo un error al guardar el LOG';
				}
				else {
					echo $per->getErrorMessage($res);
				}
			}	
		break;
		case '3':
			// SOFTWARE
			/*
			$sof->datosNuevoSoftware(NULL,$nombre,$referencia,$descripcion,$version,3);
			if(!$sof->comprobarSoftwareDuplicado()){
				// Guardamos el software
				$res = $sof->guardarCambios();
				if($res == 1){
					// Insertamos el log
					$mensaje_log = '<span style="color:green">El software ['.$nombre.']['.$id_componente_toro.'-TOR] se ha importado correctamente</span><br/>';
					$log->datosNuevoLog(NULL,"IMPORTAR_COMPONENTES (TORO)",$mensaje_log,$fecha);
					$res_log = $log->guardarLog();
					if($res_log == 1){
						echo $mensaje_log;
					}
					else echo 'Se produjo un error al guardar el LOG';
				}
				else {
					echo $sof->getErrorMessage($res);
				}
			}
			*/
		break;
		case '4':
			/*
			// INTERFACES
			$int->datosNuevoInterfaz(NULL,$nombre,$referencia,$descripcion,$version,NULL,NULL,4,NULL,$estado,$prototipo);
			if(!$int->comprobarInterfazDuplicada()){
				// Guardamos la interfaz
				$res = $int->guardarCambios();
				if($res == 1){
					// Insertamos el log
					$mensaje_log = '<span style="color:green">La interfaz ['.$nombre.']['.$id_componente_toro.'-TOR] se ha importado correctamente</span><br/>';
					$log->datosNuevoLog(NULL,"IMPORTAR_COMPONENTES (TORO)",$mensaje_log,$fecha);
					$res_log = $log->guardarLog();
					if($res_log == 1){
						echo $mensaje_log;
					}
					else echo 'Se produjo un error al guardar el LOG';
				}
				else {
					echo $int->getErrorMessage($res);
				}
			}
			*/
		break;
		case '5':
			// KIT 
			$kit->datosNuevoKit(NULL,$nombre,$referencia,$descripcion,$version,NULL,NULL,5,NULL,$estado,$prototipo);
			if(!$kit->comprobarKitDuplicado()){
				// Guardamos el kit
				$res = $kit->guardarCambios();
				if($res == 1){
					// Insertamos el log
					$mensaje_log = '<span style="color:green">El kit ['.$nombre.']['.$id_componente_toro.'-TOR] se ha importado correctamente</span><br/>';
					$log->datosNuevoLog(NULL,"IMPORTAR_COMPONENTES (TORO)",$mensaje_log,$fecha);
					$res_log = $log->guardarLog();
					if($res_log == 1){
						echo $mensaje_log;
					}
					else echo 'Se produjo un error al guardar el LOG';
				}
				else {
					echo $kit->getErrorMessage($res);
				}
			}	
		break;
		default:
			# code...
		break;
	}
}

// ¿¿¿ LOS USUARIOS DE BRASIL NO PUEDEN CREAR COMPONENTES Y NO SE UTILIZAN EN NINGUNA ORDEN DE PRODUCCION POR LO QUE NO EXPORTAMOS NINGUN COMPONENTE CREADO ???
/*
echo '<br/><br/>COMPONENTES DE BRASIL<br/>';
// Importamos los componentes de BRASIL que no existan en SMK
$consulta = "select * from componentes_brasil where activo=1";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res_brasil = $db->getResultados();

for($i=0;$i<count($res_brasil);$i++){
	// Datos del componente de BRASIL
	$id_componente_brasil = $res_brasil["id_componente"];
	$nombre = $res_brasil[$i]["nombre"];
	$referencia = $res_brasil[$i]["referencia"];
	$descripcion = $res_brasil[$i]["descripcion"];
	$version = $res_brasil[$i]["version"];
	$id_tipo = $res_brasil[$i]["id_tipo"];
	$estado = $res_brasil[$i]["estado"];
	$prototipo = $res_brasil[$i]["prototipo"];

	switch ($id_tipo) {
		case '1':
			// CABINA
			$cab->datosNuevoCabina(NULL,$nombre,$referencia,$descripcion,$version,NULL,NULL,1,NULL,NULL,$estado,$prototipo,NULL);
			if(!$cab->comprobarCabinaDuplicada()){
				// Guardamos la cabina sin referencias, archivos, interfaces ni kits
				$res = $cab->guardarCambios();
				if($res == 1){
					// Insertamos el log
					$mensaje_log = '<span style="color:green">La cabina ['.$nombre.']['.$id_componente_brasil.'-BRA] se ha importado correctamente</span><br/>';
					$log->datosNuevoLog(NULL,"IMPORTAR_COMPONENTES (BRASIL)",$mensaje_log,$fecha);
					$res_log = $log->guardarLog();
					if($res_log == 1){
						echo $mensaje_log;
					}
					else echo 'Se produjo un error al guardar el LOG';
				}
				else {
					echo $cab->getErrorMessage($res);
				}
			}	
		break;
		case '2':
			// PERIFERICO
			$per->datosNuevoPeriferico(NULL,$nombre,$referencia,$descripcion,$version,NULL,NULL,2,NULL,NULL,$estado,$prototipo,NULL);
			if(!$per->comprobarPerifericoDuplicado()){
				// Guardamos el periferico sin referencias, archivos, interfaces ni kits
				$res = $per->guardarCambios();
				if($res == 1){
					// Insertamos el log
					$mensaje_log = '<span style="color:green">El periferico ['.$nombre.']['.$id_componente_brasil.'-BRA] se ha importado correctamente</span><br/>';
					$log->datosNuevoLog(NULL,"IMPORTAR_COMPONENTES (BRASIL)",$mensaje_log,$fecha);
					$res_log = $log->guardarLog();
					if($res_log == 1){
						echo $mensaje_log;
					}
					else echo 'Se produjo un error al guardar el LOG';
				}
				else {
					echo $per->getErrorMessage($res);
				}
			}	
		break;
		case '3':
			// SOFTWARE
			$sof->datosNuevoSoftware(NULL,$nombre,$referencia,$descripcion,$version,3);
			if(!$sof->comprobarSoftwareDuplicado()){
				// Guardamos el software
				$res = $sof->guardarCambios();
				if($res == 1){
					// Insertamos el log
					$mensaje_log = '<span style="color:green">El software ['.$nombre.']['.$id_componente_brasil.'-BRA] se ha importado correctamente</span><br/>';
					$log->datosNuevoLog(NULL,"IMPORTAR_COMPONENTES (BRASIL)",$mensaje_log,$fecha);
					$res_log = $log->guardarLog();
					if($res_log == 1){
						echo $mensaje_log;
					}
					else echo 'Se produjo un error al guardar el LOG';
				}
				else {
					echo $sof->getErrorMessage($res);
				}
			}	
		break;
		case '4':
			// INTERFACES
			$int->datosNuevoInterfaz(NULL,$nombre,$referencia,$descripcion,$version,NULL,NULL,4,NULL,$estado,$prototipo);
			if(!$int->comprobarInterfazDuplicada()){
				// Guardamos la interfaz
				$res = $int->guardarCambios();
				if($res == 1){
					// Insertamos el log
					$mensaje_log = '<span style="color:green">La interfaz ['.$nombre.']['.$id_componente_brasil.'-BRA] se ha importado correctamente</span><br/>';
					$log->datosNuevoLog(NULL,"IMPORTAR_COMPONENTES (BRASIL)",$mensaje_log,$fecha);
					$res_log = $log->guardarLog();
					if($res_log == 1){
						echo $mensaje_log;
					}
					else echo 'Se produjo un error al guardar el LOG';
				}
				else {
					echo $int->getErrorMessage($res);
				}
			}	
		break;
		case '5':
			// KIT 
			$kit->datosNuevoKit(NULL,$nombre,$referencia,$descripcion,$version,NULL,NULL,5,NULL,$estado,$prototipo);
			if(!$kit->comprobarKitDuplicado()){
				// Guardamos el kit
				$res = $kit->guardarCambios();
				if($res == 1){
					// Insertamos el log
					$mensaje_log = '<span style="color:green">El kit ['.$nombre.']['.$id_componente_brasil.'-BRA] se ha importado correctamente</span><br/>';
					$log->datosNuevoLog(NULL,"IMPORTAR_COMPONENTES (BRASIL)",$mensaje_log,$fecha);
					$res_log = $log->guardarLog();
					if($res_log == 1){
						echo $mensaje_log;
					}
					else echo 'Se produjo un error al guardar el LOG';
				}
				else {
					echo $kit->getErrorMessage($res);
				}
			}	
		break;
		default:
			# code...
		break;
	}
}
*/
?>
