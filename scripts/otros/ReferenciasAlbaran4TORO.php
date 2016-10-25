<?php 
// Script para insertar referencias del albaran 4 que no se guardaron correctamente
include("../classes/mysql.class.php");
include("../classes/basicos/referencia.class.php");

$db = new MySQL();
$referencia = new Referencia();

$id_albaran = 4;

$array_refs = array(620,621,626,627,636,637,638,639,641,645,669,670,671,674,680,682);
$array_piezas = array(400,399,808,405,254,209,200,200,241,200,281,284,284,61,200,200);

for($i=0;$i<count($array_refs);$i++){
	$id_referencia = $array_refs[$i];

	$referencia->cargaDatosReferenciaId($id_referencia);

	$nombre_referencia = $referencia->referencia;
	$nombre_proveedor = $referencia->nombre_proveedor;
	$referencia_proveedor = $referencia->part_proveedor_referencia;
	$nombre_pieza = $referencia->part_nombre;
	$pack_precio = $referencia->pack_precio;
	$unidades = $referencia->unidades;
	$cantidad = $array_piezas[$i];


	print_r($id_referencia); echo "<br/>";
	print_r($nombre_referencia); echo "<br/>";
	print_r($nombre_proveedor); echo "<br/>";
	print_r($referencia_proveedor); echo "<br/>";
	print_r($nombre_pieza); echo "<br/>";
	print_r($pack_precio); echo "<br/>";
	print_r($unidades); echo "<br/>";
	print_r($cantidad); echo "<br/>";


	$consulta = sprintf("insert into albaranes_referencias (id_albaran,id_referencia,nombre_referencia,nombre_proveedor,referencia_proveedor,nombre_pieza,pack_precio,unidades_paquete,cantidad,activo) value (%s,%s,%s,%s,%s,%s,%s,%s,%s,1)",	
		$db->makeValue($id_albaran, "int"),
		$db->makeValue($id_referencia, "int"),
		$db->makeValue($nombre_referencia, "text"),
		$db->makeValue($nombre_proveedor, "text"),
		$db->makeValue($referencia_proveedor, "text"),
		$db->makeValue($nombre_pieza, "text"),
		$db->makeValue($pack_precio, "float"),
		$db->makeValue($unidades, "int"),
		$db->makeValue($cantidad, "float"));
	$db->setConsulta($consulta);
	if($db->ejecutarSoloConsulta()) {
		$db->id = $db->getUltimoID();
		echo "Id Referencia: ".$id_referencia. " OK";
	} 
	else {
		echo "Id Referencia: ".$id_referencia. " ERROR";
	}
}



/* 
REFERENCIAS A GUARDAR

ID_REF
620 - 400 piezas
621 - 399 piezas
626 - 808 piezas
627 - 405 piezas
636 - 254 piezas
637 - 209 piezas
638 - 200 piezas
639 - 200 piezas
641 - 241 piezas
645 - 200 piezas
669 - 281 piezas
670 - 284 piezas
671 - 284 piezas
674 - 61  piezas
680 - 200 piezas
682 - 200 piezas
*/



?>