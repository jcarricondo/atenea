<?php 
set_time_limit(10000);
// Excel con todas las codificaciones de las referencias de SMK
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
    	<th style="background:green; color: white;">Cod. Nombre</th>
    	<th style="background:green; color: white;">Tipo Pieza</th>
    	<th style="background:green; color: white;">Cod. Tipo Pieza</th>
    	<th style="background:green; color: white;">Nombre Pieza</th>
    	<th style="background:green; color: white;">Cod. Nombre Pieza</th>
    	<th style="background:green; color: white;">Ref. Fabricante</th>
    	<th style="background:green; color: white;">Cod. Ref. Fabricante</th>
        <th style="background:green; color: white;">Ref. Proveedor</th>
        <th style="background:green; color: white;">Cod. Ref. Proveedor</th>
        <th style="background:green; color: white;">N1</th>
        <th style="background:green; color: white;">Cod. N1</th>
        <th style="background:green; color: white;">V1</th>
        <th style="background:green; color: white;">Cod. V1</th>
    </tr>';

$consulta = "select id_referencia from referencias where activo=1";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res = $db->getResultados();
	
for($i=0;$i<count($res);$i++){
	$id_referencia = $res[$i]["id_referencia"];
	$referencia->cargaDatosReferenciaId($id_referencia);
	
	$nombre = $referencia->referencia;
	$nombre_pieza = $referencia->part_nombre;
	$tipo_pieza = $referencia->part_tipo;
	$part_proveedor_referencia = $referencia->part_proveedor_referencia;
	$part_fabricante_referencia = $referencia->part_fabricante_referencia;
	$part_valor_nombre = $referencia->part_valor_nombre;
	$part_valor_cantidad = $referencia->part_valor_cantidad;
	
	
	/*
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
	*/
		
	if($i % 2 == 0) $color = ' background-color: #fff;';
	else $color = ' background-color: #eee;';


	if(mb_detect_encoding($nombre) === "ASCII") $cod_nombre = '<span style="color: red;"">'.mb_detect_encoding($nombre).'</span>';
	else $cod_nombre = '<span style="color: black;"">'.mb_detect_encoding($nombre).'</span>';

	if(mb_detect_encoding($tipo_pieza) === "ASCII") $cod_tipo_pieza = '<span style="color: red;"">'.mb_detect_encoding($cod_tipo_pieza).'</span>';
	else $cod_tipo_pieza = '<span style="color: black;"">'.mb_detect_encoding($tipo_pieza).'</span>';

	if(mb_detect_encoding($nombre_pieza) === "ASCII") $cod_nombre_pieza = '<span style="color: red;"">'.mb_detect_encoding($cod_nombre_pieza).'</span>';
	else $cod_nombre_pieza = '<span style="color: black;"">'.mb_detect_encoding($nombre_pieza).'</span>';

	if(mb_detect_encoding($part_fabricante_referencia) === "ASCII") $cod_part_fabricante_referencia = '<span style="color: red;"">'.mb_detect_encoding($cod_part_fabricante_referencia).'</span>';
	else $cod_part_fabricante_referencia = '<span style="color: black;"">'.mb_detect_encoding($part_fabricante_referencia).'</span>';

	if(mb_detect_encoding($part_proveedor_referencia) === "ASCII") $cod_part_proveedor_referencia = '<span style="color: red;"">'.mb_detect_encoding($cod_part_proveedor_referencia).'</span>';
	else $cod_part_proveedor_referencia = '<span style="color: black;"">'.mb_detect_encoding($part_proveedor_referencia).'</span>';

	if(mb_detect_encoding($part_valor_nombre) === "ASCII") $cod_part_valor_nombre = '<span style="color: red;"">'.mb_detect_encoding($cod_part_valor_nombre).'</span>';
	else $cod_part_valor_nombre = '<span style="color: black;"">'.mb_detect_encoding($part_valor_nombre).'</span>';

	if(mb_detect_encoding($part_valor_cantidad) === "ASCII") $cod_part_valor_cantidad = '<span style="color: red;"">'.mb_detect_encoding($cod_part_valor_cantidad).'</span>';
	else $cod_part_valor_cantidad = '<span style="color: black;"">'.mb_detect_encoding($part_valor_cantidad).'</span>';



	// Generamos la fila HTML de la tabla correspondiente a una referencia
	$salida .= '
	<tr>
		<td style="background:white; text-align:center; color: '.$color.'">'.$id_referencia.'</td>
    	<td style="background:white; color: '.$color.'">'.$nombre.'</td>
    	<td style="background:white; color: '.$color.'">'.$cod_nombre.'</td>
    	<td style="background:white; color: '.$color.'">'.$tipo_pieza.'</td>
    	<td style="background:white; color: '.$color.'">'.$cod_tipo_pieza.'</td>
    	<td style="background:white; color: '.$color.'">'.$nombre_pieza.'</td>
    	<td style="background:white; color: '.$color.'">'.$cod_nombre_pieza.'</td>
    	<td style="background:white; color: '.$color.'">'.$part_fabricante_referencia.'</td>
    	<td style="background:white; color: '.$color.'">'.$cod_part_fabricante_referencia.'</td>
        <td style="background:white; color: '.$color.'">'.$part_proveedor_referencia.'</td>
        <td style="background:white; color: '.$color.'">'.$cod_part_proveedor_referencia.'</td>
        <td style="background:white; color: '.$color.'">'.$part_valor_nombre.'</td>
        <td style="background:white; color: '.$color.'">'.$cod_part_valor_nombre.'</td>
        <td style="background:white; color: '.$color.'">'.$part_valor_cantidad.'</td>
        <td style="background:white; color: '.$color.'">'.$cod_part_valor_cantidad.'</td>
	</tr>'; 
} 
$table_end = '</table>';

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=excelCodificacionRefs.xls");

echo $table.$salida.$table_end; 
?>