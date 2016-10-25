<?php
// Script para rellenar los nombres de las OC en la BBDD
include_once("../classes/mysql.class.php");
include_once("../classes/orden_compra/orden_compra.class.php");

$orden_compra = new Orden_Compra();
$bbdd = new MySQL();
// Los nombres de las OC estan formados por OP + id_OP + nombre de la OP

// Obtenemos el numero de registros de la tabla orden_compra
$consulta = sprintf("select id_orden_compra from orden_compra");	
$bbdd->setConsulta($consulta);
$bbdd->ejecutarConsulta();
$registros = $bbdd->getResultados();
$num_registros = $bbdd->getNumeroFilas();

for ($i=0;$i<$num_registros;$i++){
	// Carga de los datos de la OC
	$orden_compra->cargaDatosOrdenCompraId($registros[$i]["id_orden_compra"]);
	$nombre_compra = 'OP'.$orden_compra->id_produccion.$orden_compra->nombre_prov;

	$consulta = sprintf("update orden_compra set orden_compra=%s where id_orden_compra=%s",	
		$bbdd->makeValue($nombre_compra, "text"),
		$bbdd->makeValue($orden_compra->id_compra, "int"));
	$bbdd->setConsulta($consulta);
	$bbdd->ejecutarSoloConsulta();
}
?>