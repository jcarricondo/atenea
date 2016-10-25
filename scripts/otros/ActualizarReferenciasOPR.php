<?php
// Script para actualizar los pack_precio y las unidades de la tabla OPR de las Ordenes de Produccion activas en estado "BORRADOR"

include("../includes/sesion.php");
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/basicos/referencia.class.php");

$db = new MySQL();
$orden_produccion = new Orden_Produccion();
$referencia = new Referencia();

// Ordenes de Produccion activas en estado borrador
// ATENEA
// $ids = array(111,139);
// ATENEA TORO
$ids = array(109);

for($i=0;$i<count($ids);$i++){
	// Guardamos el id_produccion
	$id_produccion = $ids[$i];

	echo "ORDEN PRODUCCION: ";
	print_r($id_produccion); echo "<br/>";
	echo "<br/>"; echo "<br/>";

	// Obtenemos las id_red que se guardaron en la tabla opr para la id_produccion
	$consulta = sprintf("select * from orden_produccion_referencias where id_produccion=%s and activo=1 order by id_referencia ",
		$db->makeValue($id_produccion,"int"));
	$db->setConsulta($consulta);
	$db->ejecutarConsulta();
	$resultados = $db->getResultados();

	for($j=0;$j<count($resultados);$j++){
		$id = $resultados[$j]["id"];
		$id_referencia = $resultados[$j]["id_referencia"];
		$uds_paquete = $resultados[$j]["uds_paquete"];
		$piezas = $resultados[$j]["piezas"];
		$pack_precio = $resultados[$j]["pack_precio"];

		/*
		// print_r($id); echo "<br/>";
		print_r($id_referencia); echo "<br/>";
		print_r($uds_paquete); echo "<br/>";
		print_r($pack_precio); echo "<br/>";
		echo "<br/><br/>";
		*/
		
		// Comprobamos que las unidades paquete y pack_precio de las referencias de OPR coinciden con la referencias de la tabla referencias 
		$consulta = sprintf("select id_referencia, unidades, pack_precio from referencias where id_referencia=%s and activo=1 ",
			$db->makeValue($id_referencia,"int"));
		$db->setConsulta($consulta);
		$db->ejecutarConsulta();
		$datos_referencia = $db->getResultados();

		$ref_id_referencia = $datos_referencia[0]["id_referencia"];
		$ref_uds_paquete = $datos_referencia[0]["unidades"];
		$ref_pack_precio = $datos_referencia[0]["pack_precio"];

		/*
		// print_r($id); echo "<br/>";
		echo "TABLA REFERENCIAS"; echo "<br/>";
		print_r($ref_id_referencia); echo "<br/>";
		print_r($ref_uds_paquete); echo "<br/>";
		print_r($ref_pack_precio); echo "<br/>";
		echo "<br/><br/>";
		*/

		if (($uds_paquete != $ref_uds_paquete) or ($pack_precio != $ref_pack_precio)){
			print_r($id); echo "<br/>";
			print_r($id_referencia); echo "<br/>";
			print_r($uds_paquete); echo "<br/>";
			print_r($pack_precio); echo "<br/>";
			echo "<br/><br/>";	
			echo "TABLA REFERENCIAS"; echo "<br/>";
			print_r($ref_id_referencia); echo "<br/>";
			print_r($ref_uds_paquete); echo "<br/>";
			print_r($ref_pack_precio); echo "<br/>";
			echo "<br/><br/>";

			// Recalculamos los total_paquetes
			$referencia->calculaTotalPaquetes($ref_uds_paquete,$piezas);
			$ref_total_paquetes = $referencia->total_paquetes;


			// Actualizamos la tabla OPR con los datos correctos de referencias
			$consulta = sprintf("update orden_produccion_referencias set uds_paquete=%s, total_paquetes=%s, pack_precio=%s where id=%s ",
				$db->makeValue($ref_uds_paquete,"int"),
				$db->makeValue($ref_total_paquetes,"int"),
				$db->makeValue($ref_pack_precio,"float"),
				$db->makeValue($id,"int"));
			$db->setConsulta($consulta);
			if($db->ejecutarSoloConsulta()){
				echo "El id=".$id." de la tabla OPR se ha actualizado correctamente"; echo "<br/><br/>";
			}
			else{
				echo "Se produjo un error al actualizar el id=".$id." de la tabla OPR"; echo "<br/><br/>";
			}

		}
	}
}
?>