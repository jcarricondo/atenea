<?php
// Script para actualizar los estados de las OC antiguas
include_once("../classes/mysql.class.php");
include_once("../classes/orden_compra/orden_compra.class.php");

$orden_compra = new Orden_Compra();
$bbdd = new MySQL();
// Los antiguos estados de la OC eran: GENERADA, PEDIDA, RECIBIDA
// Los nuevos estados de la OC son: GENERADA, PEDIDO INICIADO, PEDIDO CERRADO, PARCIALMENTE RECIBIDO, RECIBIDO, STOCK

// Obtenemos las OC que estuvieran en estado PEDIDA o RECIBIDA
$consulta = sprintf('select id_orden_compra from orden_compra where estado="PEDIDA" or estado="RECIBIDA"');	
$bbdd->setConsulta($consulta);
$bbdd->ejecutarConsulta();
$registros = $bbdd->getResultados();
$num_registros = $bbdd->getNumeroFilas();

for ($i=0;$i<$num_registros;$i++){
	// Si el estado de la OC antigua era PEDIDA la cambiamos por PEDIDO INICIADO
	// Si el estado de la OC antigua era RECIBIDA la cambiamos por RECIBIDO
	$orden_compra->cargaDatosOrdenCompraId($registros[$i]["id_orden_compra"]);
	if ($orden_compra->estado == "PEDIDA"){
		$consulta = sprintf('update orden_compra set estado="PEDIDO INICIADO" where id_orden_compra=%s',	
			$bbdd->makeValue($orden_compra->id_compra, "int"));
		$bbdd->setConsulta($consulta);
		$bbdd->ejecutarSoloConsulta();
	}
	else {
		$consulta = sprintf('update orden_compra set estado="RECIBIDO" where id_orden_compra=%s',	
			$bbdd->makeValue($orden_compra->id_compra, "int"));
		$bbdd->setConsulta($consulta);
		$bbdd->ejecutarSoloConsulta();
	}	
}
?>