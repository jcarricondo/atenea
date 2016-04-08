<?php 
// Script parar importar las ordenes de produccion de TORO
set_time_limit(10000);
include("../../classes/mysql.class.php");
include("../../classes/orden_produccion/orden_produccion.class.php");
include("../../classes/funciones/log_unificacion.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySql();
$op = new Orden_Produccion();
$log = new Log_Unificacion();

echo '<br/>OPS de TORO<br/>';
// Importamos las OPs de TORO que no existan en SMK
$consulta = "select * from orden_produccion_toro where activo=1";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res_toro = $db->getResultados();

for($i=0;$i<count($res_toro);$i++){
	$unidades = $res_toro[$i]["unidades"];
	$codigo = $res_toro[$i]["codigo"];
	$id_tipo = $res_toro[$i]["id_tipo"];
	$fecha_inicio = $res_toro[$i]["fecha_inicio"];
	$fecha_entrega = $res_toro[$i]["fecha_entrega"];
	$fecha_entrega_deseada = $res_toro[$i]["fecha_entrega_deseada"];
	$fecha_inicio_construccion = $res_toro[$i]["fecha_inicio_construccion"];
	$estado = $res_toro[$i]["estado"];
	$comentarios = $res_toro[$i]["comentarios"];
	$alias = $res_toro[$i]["alias"];

	$op->datosNuevaProduccion(NULL,$unidades,$codigo,$id_tipo,$fecha_inicio,$fecha_entrega,$fecha_entrega_deseada,$estado,$comentarios,$alias,$fecha_inicio_construccion,2);
	// Guardamos las Ordenes de Produccion 
	$consulta = sprintf("insert into orden_produccion (alias,unidades,codigo,id_tipo,fecha_inicio,fecha_entrega,fecha_entrega_deseada,fecha_inicio_construccion,estado,id_sede,comentarios,fecha_creado,activo) value (%s,%s,%s,%s,%s,%s,%s,%s,%s,2,%s,current_timestamp,1)",
		$db->makeValue($alias, "text"),
		$db->makeValue($unidades, "int"),
		$db->makeValue($codigo, "text"),
		$db->makeValue($id_tipo, "int"),
		$db->makeValue($fecha_inicio, "text"),
		$db->makeValue($fecha_entrega, "text"),
		$db->makeValue($fecha_entrega_deseada, "text"),
		$db->makeValue($fecha_inicio_construccion, "text"),
		$db->makeValue($estado, "text"),
		$db->makeValue($comentarios, "text"));
	$db->setConsulta($consulta);
	if($db->ejecutarSoloConsulta()) {
		// Insertamos el log
		$mensaje_log = '<span style="color:green">La Orden de Produccion ['.$alias.'] se ha importado correctamente</span><br/><br/>';
		$log->datosNuevoLog(NULL,"IMPORTAR_OP (TORO)",$mensaje_log,$fecha);
		$res_log = $log->guardarLog();
		if($res_log == 1){
			echo $mensaje_log;
		}
		else echo 'Se produjo un error al guardar el LOG';	
	}
	else {
		echo 'Se ha producido un error al importar las OPS';
	}
}
?>

