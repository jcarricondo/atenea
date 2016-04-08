<?php 
set_time_limit(10000);
// Excel con todas las referencias de SMK
include("../../classes/mysql.class.php");
include("../../classes/basicos/referencia.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySQL();
$referencia = new Referencia();

$salida = "";

// Generamos la tabla HTML 
$table = '<table>
	<tr>
		<th>REFERENCIAS SIMUMAK</th>
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
	
$consulta = "select id_referencia from referencias where activo=1";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res = $db->getResultados();
	
for($i=0;$i<count($res);$i++){
	$id_referencia = $res[$i]["id_referencia"];
	$referencia->cargaDatosReferenciaId($id_referencia);
	
	$nombre = $referencia->referencia;
	$fabricante = $referencia->fabricante;
	$proveedor = $referencia->proveedor;
	$part_nombre = $referencia->part_nombre;
	$part_tipo = $referencia->part_tipo;
	$part_proveedor_referencia = $referencia->part_proveedor_referencia;
	$part_fabricante_referencia = $referencia->part_fabricante_referencia;
	$part_valor_nombre = $referencia->part_valor_nombre;
	$part_valor_cantidad = $referencia->part_valor_cantidad;
	$pack_precio = $referencia->pack_precio;
	$unidades = $referencia->unidades;
	$part_descripcion = $referencia->part_descripcion;
	$comentarios = $referencia->comentarios;
	$part_precio_cantidad = $referencia->part_precio_cantidad;
	$nombre_proveedor = $referencia->nombre_proveedor;
	$nombre_fabricante = $referencia->nombre_fab;

	// Preparamos la codificacion de la referencia 
	$nombre_ref = '';
	$nombre_referencia_codificada = utf8_decode($referencia->referencia);
	for($m=0;$m<strlen($nombre_referencia_codificada);$m++){
		if ($nombre_referencia_codificada[$m] == '?'){
			$nombre_ref .= '&#8364;'; 	
		}
		else {
			$nombre_ref .= $nombre_referencia_codificada[$m]; 
		}
	}

	$ref_prov = '';
	$ref_prov_codificada = utf8_decode($referencia->part_proveedor_referencia);
	for($m=0;$m<strlen($ref_prov_codificada);$m++){
		if ($ref_prov_codificada[$m] == '?'){
			$ref_prov .= '&#8364;'; 	
		}
		else {
			$ref_prov .= $ref_prov_codificada[$m]; 
		}
	}

	$tipo_pieza = '';
	$tipo_pieza_codificada = utf8_decode($referencia->part_tipo);
	for($m=0;$m<strlen($tipo_pieza_codificada);$m++){
		if ($tipo_pieza_codificada[$m] == '?'){
			$tipo_pieza .= '&#8364;'; 	
		}
		else {
			$tipo_pieza .= $tipo_pieza_codificada[$m]; 
		}
	}
	
	$ref_fab = '';
	$ref_fab_codificada = utf8_decode($referencia->part_fabricante_referencia);
	for($m=0;$m<strlen($ref_fab_codificada);$m++){
		if ($ref_fab_codificada[$m] == '?'){
			$ref_fab .= '&#8364;'; 	
		}
		else {
			$ref_fab .= $ref_fab_codificada[$m]; 
		}
	}
	
	$descrip = '';
	$descrip_codificada = utf8_decode($referencia->part_descripcion);
	for($m=0;$m<strlen($descrip_codificada);$m++){
		if ($descrip_codificada[$m] == '?'){
			$descrip .= '&#8364;'; 	
		}
		else {
			$descrip .= $descrip_codificada[$m]; 
		}
	}
	
	$valor_nombre = '';
	$valor_nombre_codificada = utf8_decode($referencia->part_valor_nombre);
	for($m=0;$m<strlen($valor_nombre_codificada);$m++){
		if ($valor_nombre_codificada[$m] == '?'){
			$valor_nombre .= '&#8364;'; 	
		}
		else {
			$valor_nombre .= $valor_nombre_codificada[$m]; 
		}
	}
	
	$valor_nombre2 = '';
	$valor_nombre2_codificada = utf8_decode($referencia->part_valor_nombre_2);
	for($m=0;$m<strlen($valor_nombre2_codificada);$m++){
		if ($valor_nombre2_codificada[$m] == '?'){
			$valor_nombre2 .= '&#8364;'; 	
		}
		else {
			$valor_nombre2 .= $valor_nombre2_codificada[$m]; 
		}
	}
	
	$valor_nombre3 = '';
	$valor_nombre3_codificada = utf8_decode($referencia->part_valor_nombre_3);
	for($m=0;$m<strlen($valor_nombre3_codificada);$m++){
		if ($valor_nombre3_codificada[$m] == '?'){
			$valor_nombre3 .= '&#8364;'; 	
		}
		else {
			$valor_nombre3 .= $valor_nombre3_codificada[$m]; 
		}
	}
	
	$valor_nombre4 = '';
	$valor_nombre4_codificada = utf8_decode($referencia->part_valor_nombre_4);
	for($m=0;$m<strlen($valor_nombre4_codificada);$m++){
		if ($valor_nombre4_codificada[$m] == '?'){
			$valor_nombre4 .= '&#8364;'; 	
		}
		else {
			$valor_nombre4 .= $valor_nombre4_codificada[$m]; 
		}	
	}
	
	$valor_nombre5 = '';
	$valor_nombre5_codificada = utf8_decode($referencia->part_valor_nombre_5);
	for($m=0;$m<strlen($valor_nombre5_codificada);$m++){
		if ($valor_nombre5_codificada[$m] == '?'){
			$valor_nombre5 .= '&#8364;'; 	
		}
		else {
			$valor_nombre5 .= $valor_nombre5_codificada[$m]; 
		}
	}
	
	$coments = '';
	$coments_codificada = utf8_decode($referencia->comentarios);
	for($m=0;$m<strlen($coments_codificada);$m++){
		if ($coments_codificada[$m] == '?'){
			$coments .= '&#8364;'; 	
		}
		else {
			$coments .= $coments_codificada[$m]; 
		}
	}
		
	if($i % 2 == 0) $color = ' background-color: #fff;';
	else $color = ' background-color: #eee;';


	// Generamos la fila HTML de la tabla correspondiente a una referencia
	$salida .= '
	<tr>
		<td style="'.$color.'" align="center" >'.$referencia->id_referencia.'</td>
		<td style="'.$color.'" align="left">'.$nombre_ref.'</td>
		<td style="'.$color.'" align="center">'.$ref_prov.'</td>
		<td style="'.$color.'" align="left">'.utf8_decode($referencia->nombre_proveedor).'</td>
		<td style="'.$color.'" align="center">'.$tipo_pieza.'</td>
		<td style="'.$color.'" align="left">'.utf8_decode($referencia->part_nombre).'</td>
		<td style="'.$color.'" align="left">'.utf8_decode($referencia->nombre_fabricante).'</td>
		<td style="'.$color.'" align="center">'.$ref_fab.'</td>
		<td style="'.$color.'" align="right">'.number_format($referencia->pack_precio,2,',','.').'</td>
		<td style="'.$color.'" align="right">'.utf8_decode($referencia->unidades).'</td>
	</tr>';
}
$table_end = '</table>';

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=excelReferenciasSMK.xls");

echo $table.$salida.$table_end; 
?>