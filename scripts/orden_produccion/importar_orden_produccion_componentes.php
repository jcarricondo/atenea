<?php 
// Script parar importar los componentes de las ordenes de produccion de TORO
set_time_limit(10000);
include("../../classes/mysql.class.php");
include("../../classes/basicos/cabina.class.php");
include("../../classes/basicos/periferico.class.php");
// include("../../classes/basicos/software.class.php");
// include("../../classes/basicos/interface.class.php");
include("../../classes/basicos/kit.class.php");
include("../../classes/orden_produccion/orden_produccion.class.php");
include("../../classes/funciones/log_unificacion.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySql();
$cabina = new Cabina();
$periferico = new Periferico();
// $soft = new Software();
// $interfaz = new Interfaz();
$kit = new Kit();
$op = new Orden_Produccion();
$log = new Log_Unificacion();

echo '<br/>OPS COMPONENTES de TORO<br/>';
// Importamos los componentes de las OPs de TORO que no existan en SMK
$consulta = "select * from orden_produccion_componentes_toro where activo=1";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res_toro = $db->getResultados();

for($i=0;$i<count($res_toro);$i++){
	// Obtenemos los datos de los componentes de las OPS de TORO
	$id_produccion_toro = $res_toro[$i]["id_produccion"];
	$id_componente_toro = $res_toro[$i]["id_componente"];
	$num_serie_toro = $res_toro[$i]["num_serie"];
	$fecha_creado_toro = $res_toro[$i]["fecha_creado"];

	//d($id_produccion_toro);

	// Comprobamos cual es la Orden de Produccion equivalente de TORO
	$consulta_op = sprintf("select * from orden_produccion_toro where activo=1 and id_produccion=%s",
		$db->makeValue($id_produccion_toro, "int"));
	$db->setConsulta($consulta_op);
	$db->ejecutarConsulta();
	$res_op = $db->getPrimerResultado();
	$alias_toro = $res_op["alias"];

	//d($alias_toro);

	// Buscamos el nuevo id_produccion del OP con ese alias
	$consulta_op = sprintf("select * from orden_produccion where activo=1 and alias=%s",
		$db->makeValue($alias_toro,"text"));
	$db->setConsulta($consulta_op);
	$db->ejecutarConsulta();
	$res_op = $db->getPrimerResultado();
	$id_produccion_new = $res_op["id_produccion"];

	//d($id_produccion_new);

	// Comprobamos cual es el nuevo componente de la OP equivalente a TORO
	$consulta_comp = sprintf("select * from componentes_toro where activo=1 and id_componente=%s",
		$db->makeValue($id_componente_toro, "int"));
	$db->setConsulta($consulta_comp);
	$db->ejecutarConsulta();
	$res_comp = $db->getPrimerResultado();
	
	$nombre_componente_toro = $res_comp["nombre"];
	$version_componente_toro = $res_comp["version"];
	$id_tipo_componente_toro = $res_comp["id_tipo"];

	// Buscamos el componente actualizado 
	$consulta_comp = sprintf("select id_componente from componentes where nombre=%s and version=%s and activo=1 and id_tipo=%s",
		$db->makeValue($nombre_componente_toro, "text"),
		$db->makeValue($version_componente_toro, "float"),
		$db->makeValue($id_tipo_componente_toro, "int"));
	$db->setConsulta($consulta_comp);
	$db->ejecutarConsulta();
	$res_comp = $db->getPrimerResultado();
	$id_componente_new = $res_comp["id_componente"];

	if($id_componente_toro != $id_componente_new){
		d($id_componente_toro);
		d($id_componente_new);
	}

	// <!-- Coinciden los componentes -->
	// NO HACE FALTA ACTUALIZAR COMPONENTES

	// Actualizamos el num_serie
	// Utilizamos una variable auxiliar para contabilizar las id_produccion y resetear el contador_componente
	if($id_produccion_new != $id_produccion_aux) {
		$contador_componente = 1;
	}
	else {
		$contador_componente++;
	}

	switch ($id_tipo_componente_toro) {
		case '1':
			// CABINA
			$cabina->cargaDatosCabinaId($id_componente_new);
			$num_serie_componente = $cabina->referencia."_".$cabina->version."_".$id_produccion_new."_".$contador_componente;
		break;
		case '2':
			// PERIFERICO
			$periferico->cargaDatosPerifericoId($id_componente_new);
			$num_serie_componente = $periferico->referencia."_".$periferico->version."_".$id_produccion_new."_".$contador_componente;
		break;
		case '3':
			// SOFTWARE
			$soft->cargaDatosSoftwareId($id_componente_new);
			$num_serie_componente = "-";
		break;
		case '4':
			// INTERFAZ
			$interfaz->cargaDatosInterfazId($id_componente_new);
			$num_serie_componente = $interfaz->referencia."_".$interfaz->version."_".$id_produccion_new."_".$contador_componente;
		break;
		case '5':
			// KIT
			$kit->cargaDatosKitId($id_componente_new);
			$num_serie_componente = $kit->referencia."_".$kit->version."_".$id_produccion_new."_".$contador_componente;
		break;	
		default:
			# code...
		break;
	}

	$id_produccion_aux = $id_produccion_new;

	if($id_produccion_new != NULL){
		$resultado = $op->guardarComponenteProduccion($id_produccion_new,$id_componente_new,$num_serie_componente);	

		if($resultado == 1){
			// Insertamos el log
			$mensaje_log = '<span style="color:green">El componente ['.$nombre_componente_toro.'] perteneciente a la OP['.$id_produccion_new.'] se ha importado correctamente</span><br/><br/>';
			$log->datosNuevoLog(NULL,"IMPORTAR_OP_COMPONENTES (TORO)",$mensaje_log,$fecha);
			$res_log = $log->guardarLog();
			if($res_log == 1){
				echo $mensaje_log;
			}
			else echo 'Se produjo un error al guardar el LOG';	
		}
		else {
			echo 'Se ha producido un error al importar los componentes de la OPS de TORO';
		}
	}
}
?>

