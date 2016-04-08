<?php 
set_time_limit(10000);
// Excel con todas las referencias de TORO
include("../../classes/mysql.class.php");
include("../../classes/basicos/referencia.class.php");
include("../../classes/basicos/proveedor.class.php");
include("../../classes/basicos/fabricante.class.php");

$db = new MySQL();
$referencia = new Referencia();
$prov = new Proveedor();
$fab = new Fabricante();

$salida = "";

// Generamos la tabla HTML 
$table = '<table>
	<tr>
		<th>REFERENCIAS TORO</th>
	</tr>
	<tr>
		<th style="background:green; color: white;">ID Ref.</th>
    	<th style="background:green; color: white;">Nombre</th>
        <th style="background:green; color: white;">Referencia Proveedor</th>
        <th style="background:green; color: white;">Proveedor</th>   
        <th style="background:green; color: white;">Tipo Pieza</th>
        <th style="background:green; color: white;">Nombre Pieza</th> 
        <th style="background:green; color: white;">Fabricante</th>  
        <th style="background:green; color: white;">Referencia Fabricante</th>
        <th style="background:green; color: white;">Precio Pack</th>
        <th style="background:green; color: white;">Unidades Paquete</th>
    </tr>';
	
$consulta = "select * from referencias_toro where activo=1";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res = $db->getResultados();
	
for($i=0;$i<count($res);$i++){
	$id_referencia = $res[$i]["id_referencia"];
	$nombre = $res[$i]["referencia"];
	$part_nombre = $res[$i]["part_nombre"];
	$part_tipo = $res[$i]["part_tipo"];
	$part_proveedor_referencia = $res[$i]["part_proveedor_referencia"];
	$part_fabricante_referencia = $res[$i]["part_fabricante_referencia"];
	$pack_precio = $res[$i]["pack_precio"];
	$unidades = $res[$i]["unidades"];
	$id_proveedor = $res[$i]["id_proveedor"];
	$id_fabricante = $res[$i]["id_fabricante"];

	$consulta_prov = sprintf("select nombre_prov from proveedores_toro where activo=1 and id_proveedor=%s",
		$db->makeValue($id_proveedor, "int"));
	$db->setConsulta($consulta_prov);
	$db->ejecutarConsulta();
	$res_prov = $db->getPrimerResultado();
	$nombre_proveedor = $res_prov["nombre_prov"];

	$consulta_fab = sprintf("select nombre_fab from fabricantes_toro where activo=1 and id_fabricante=%s",
		$db->makeValue($id_fabricante, "int"));
	$db->setConsulta($consulta_fab);
	$db->ejecutarConsulta();
	$res_fab = $db->getPrimerResultado();
	$nombre_fabricante = $res_fab["nombre_fab"];

	
	// Preparamos la codificacion de la referencia 
	$nombre_ref = '';
	$nombre_referencia_codificada = utf8_decode($nombre);
	for($m=0;$m<strlen($nombre_referencia_codificada);$m++){
		if ($nombre_referencia_codificada[$m] == '?'){
			$nombre_ref .= '&#8364;'; 	
		}
		else {
			$nombre_ref .= $nombre_referencia_codificada[$m]; 
		}
	}

	$ref_prov = '';
	$ref_prov_codificada = utf8_decode($part_proveedor_referencia);
	for($m=0;$m<strlen($ref_prov_codificada);$m++){
		if ($ref_prov_codificada[$m] == '?'){
			$ref_prov .= '&#8364;'; 	
		}
		else {
			$ref_prov .= $ref_prov_codificada[$m]; 
		}
	}

	$tipo_pieza = '';
	$tipo_pieza_codificada = utf8_decode($part_tipo);
	for($m=0;$m<strlen($tipo_pieza_codificada);$m++){
		if ($tipo_pieza_codificada[$m] == '?'){
			$tipo_pieza .= '&#8364;'; 	
		}
		else {
			$tipo_pieza .= $tipo_pieza_codificada[$m]; 
		}
	}
	
	$ref_fab = '';
	$ref_fab_codificada = utf8_decode($part_fabricante_referencia);
	for($m=0;$m<strlen($ref_fab_codificada);$m++){
		if ($ref_fab_codificada[$m] == '?'){
			$ref_fab .= '&#8364;'; 	
		}
		else {
			$ref_fab .= $ref_fab_codificada[$m]; 
		}
	}
	
	if($i % 2 == 0) $color = ' background-color: #fff;';
	else $color = ' background-color: #eee;';


	// Generamos la fila HTML de la tabla correspondiente a una referencia
	$salida .= '
	<tr>
		<td style="'.$color.'" align="center" >'.$id_referencia.'</td>
		<td style="'.$color.'" align="left">'.$nombre_ref.'</td>
		<td style="'.$color.'" align="center">'.$ref_prov.'</td>
		<td style="'.$color.'" align="left">'.utf8_decode($nombre_proveedor).'</td>
		<td style="'.$color.'" align="center">'.$tipo_pieza.'</td>
		<td style="'.$color.'" align="left">'.utf8_decode($part_nombre).'</td>
		<td style="'.$color.'" align="left">'.utf8_decode($nombre_fabricante).'</td>
		<td style="'.$color.'" align="center">'.$ref_fab.'</td>
		<td style="'.$color.'" align="right">'.number_format($pack_precio,2,',','.').'</td>
		<td style="'.$color.'" align="right">'.utf8_decode($unidades).'</td>
	</tr>';
}
$table_end = '</table>';

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=excelReferenciasTOR.xls");

echo $table.$salida.$table_end; 
?>