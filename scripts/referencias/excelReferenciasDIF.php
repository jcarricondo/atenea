<?php 
set_time_limit(10000);
// Excel que muestra las diferencias existentes entre ambas BBDD
include("../../classes/mysql.class.php");
include("../../classes/basicos/referencia.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySQL();
$referencia = new Referencia();

$total_refs_smk = 0;
$total_refs_tor = 0;
$total_refs_bra = 0;
$total_refs_ok_toro = 0;
$total_refs_ok_bra = 0;
$total_refs_no_exist_toro = 0;
$total_refs_no_exist_bra = 0;
$total_refs_unidades_toro = 0;
$total_refs_unidades_bra = 0;
$total_refs_pack_precio_toro = 0;
$total_refs_pack_precio_bra = 0;
$total_refs_ids_toro = 0;
$total_refs_ids_bra = 0;
$total_refs_difs_tor = 0;
$total_refs_difs_bra = 0;

$existe_ref = true;

$salida = "";

// Generamos la tabla HTML 
$table = '<table>
	<tr>
		<th>REFERENCIAS</th>
	</tr>
	<tr>
		<th style="background:green; color: white;">ID Ref.</th>
		<th style="background:green; color: white;">BBDD</th>
    	<th style="background:green; color: white;">Nombre</th>
        <th style="background:green; color: white;">Referencia Proveedor</th>
        <th style="background:green; color: white;">Proveedor</th>   
        <th style="background:green; color: white;">Precio Pack</th>
        <th style="background:green; color: white;">Unidades Paquete</th>
        <th style="background:green; color: white;">Diferencias</th>
    </tr>';
	
$consulta = "select id_referencia from referencias where activo=1";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res = $db->getResultados();

$total_refs_smk = count($res);
	
for($i=0;$i<count($res);$i++){
	$id_referencia = $res[$i]["id_referencia"];
	$referencia->cargaDatosReferenciaId($id_referencia);
	
	$nombre = $referencia->referencia;
	$part_nombre = $referencia->part_nombre;
	$part_tipo = $referencia->part_tipo;
	$part_proveedor_referencia = $referencia->part_proveedor_referencia;
	$pack_precio = $referencia->pack_precio;
	$unidades = $referencia->unidades;
	$nombre_proveedor = $referencia->nombre_proveedor;

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

	if($i % 2 == 0) $color = ' background-color: #fff;';
	else $color = ' background-color: #eee;';

	// Generamos la fila HTML de la tabla correspondiente a una referencia
	$salida .= '
	<tr>
		<td style="'.$color.'" align="center" >'.$referencia->id_referencia.'</td>
		<td style="'.$color.'" align="center" >SIMUMAK</td>
		<td style="'.$color.'" align="left">'.$nombre_ref.'</td>
		<td style="'.$color.'" align="center">'.$ref_prov.'</td>
		<td style="'.$color.'" align="left">'.utf8_decode($referencia->nombre_proveedor).'</td>
		<td style="'.$color.'" align="right">'.number_format($referencia->pack_precio,2,',','.').'</td>
		<td style="'.$color.'" align="right">'.utf8_decode($referencia->unidades).'</td>
		<td style="'.$color.'" align="center">-</td>
	</tr>';


	// Obtenemos la referencia de TORO con el id_referencia de SMK
	$consulta_toro = sprintf("select * from referencias_toro where activo=1 and id_referencia=%s",
		$db->makeValue($id_referencia,"int"));
	$db->setConsulta($consulta_toro);
	$db->ejecutarConsulta();
	$res_toro = $db->getPrimerResultado();

	if($res_toro != NULL) {
		$total_refs_tor++;
		$id_referencia_toro = $res_toro["id_referencia"];
		$nombre_toro = $res_toro["referencia"];
		$part_nombre_toro = $res_toro["part_nombre"];
		$part_tipo_toro = $res_toro["part_tipo"];
		$part_proveedor_referencia_toro = $res_toro["part_proveedor_referencia"];
		$pack_precio_toro = $res_toro["pack_precio"];
		$unidades_toro = $res_toro["unidades"];
		$id_proveedor_toro = $res_toro["id_proveedor"];

		$consulta_prov = sprintf("select nombre_prov from proveedores_toro where activo=1 and id_proveedor=%s",
			$db->makeValue($id_proveedor_toro, "int"));
		$db->setConsulta($consulta_prov);
		$db->ejecutarConsulta();
		$res_prov = $db->getPrimerResultado();
		$nombre_proveedor = $res_prov["nombre_prov"];

		// Comprobamos la referencia del proveedor y el nombre referencia
		if(($nombre == $nombre_toro) && ($part_proveedor_referencia == $part_proveedor_referencia_toro)){
			// Coinciden. Comprobamos las unidades y pack_precio
			if($unidades == $unidades_toro){
				if($pack_precio == $pack_precio_toro){
					$diferencia_toro = '<span style="color: green;">LA REFERENCIA COINCIDE CON LA BBDD DE TORO</span>'; 	
					$total_refs_ok_toro++;
					$existe_ref = true;
				}
				else {
					$diferencia_toro = '<span style="color: orange;">PACK_PRECIO DISTINTO EN TORO</span>'; 	
					$total_refs_pack_precio_toro++;
					$existe_ref = true;
				}
			}
			else {
				$diferencia_toro = '<span style="color: orange;">UNIDADES_PAQUETE DISTINTAS EN TORO</span>'; 
				$total_refs_unidades_toro++;
				$existe_ref = true;
			}
		}
		else {
			// Comprobamos si existe la referencia en BRASIL con otro id_ref distinto
			$consulta_id_ref = sprintf("select * from referencias_toro where referencia=%s and part_proveedor_referencia=%s and activo=1",
				$db->makeValue($nombre,"text"),
				$db->makeValue($part_proveedor_referencia, "text"));
			$db->setConsulta($consulta_id_ref);
			$db->ejecutarConsulta();
			$res_id_ref = $db->getPrimerResultado();

			if($res_id_ref != NULL){
				$id_ref_toro = $res_id_ref["id_referencia"];
				$diferencia_toro = '<span style="color: purple;">NO COINCIDEN LOS ID_REF: SMK['.$id_referencia.'] TORO['.$id_ref_toro.']</span>'; 
				$total_refs_ids_toro++;
				$existe_ref = true;
			}
			else {
				$diferencia_toro = '<span style="color: blue;">LAS REFERENCIAS NO COINCIDEN</span>'; 
				$total_refs_difs_toro++;
				$existe_ref = true;
			}
		}
	}
	else {
		// Comprobamos si existe la referencia en TORO con otro id_ref distinto
		$consulta_id_ref = sprintf("select * from referencias_toro where referencia=%s and part_proveedor_referencia=%s and activo=1",
			$db->makeValue($nombre,"text"),
			$db->makeValue($part_proveedor_referencia, "text"));
		$db->setConsulta($consulta_id_ref);
		$db->ejecutarConsulta();
		$res_id_ref = $db->getPrimerResultado();

		if($res_id_ref != NULL){
			$id_ref_toro = $res_id_ref["id_referencia"];
			$diferencia_toro = '<span style="color: purple;">NO COINCIDEN LOS ID_REF: SMK['.$id_referencia.'] TORO['.$id_ref_toro.']</span>'; 
			$total_refs_ids_toro++;
			$existe_ref = false;
		}
		else {
			$diferencia_toro = '<span style="color: red;">NO EXISTE EL ID_REFERENCIA ['.$id_referencia.'] EN TORO</span>'; 
			$total_refs_no_exist_toro++;
			$existe_ref = false;
		}
	}

	// Preparamos la codificacion de la referencia 
	$nombre_ref = '';
	$nombre_referencia_codificada = utf8_decode($nombre_toro);
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
	$tipo_pieza_codificada = utf8_decode($part_tipo_toro);
	for($m=0;$m<strlen($tipo_pieza_codificada);$m++){
		if ($tipo_pieza_codificada[$m] == '?'){
			$tipo_pieza .= '&#8364;'; 	
		}
		else {
			$tipo_pieza .= $tipo_pieza_codificada[$m]; 
		}
	}

	if($i % 2 == 0) $color = ' background-color: #fff;';
	else $color = ' background-color: #eee;';

	// Generamos la fila HTML de la tabla correspondiente a una referencia
	if($existe_ref){
		$salida .= '
		<tr>
			<td style="'.$color.'" align="center" >'.$id_referencia_toro.'</td>
			<td style="'.$color.'" align="center" >TORO</td>
			<td style="'.$color.'" align="left">'.$nombre_ref.'</td>
			<td style="'.$color.'" align="center">'.$ref_prov.'</td>
			<td style="'.$color.'" align="left">'.utf8_decode($nombre_proveedor).'</td>
			<td style="'.$color.'" align="right">'.number_format($pack_precio_toro,2,',','.').'</td>
			<td style="'.$color.'" align="right">'.utf8_decode($unidades_toro).'</td>
			<td style="'.$color.'" align="center">'.$diferencia_toro.'</td>
		</tr>';
	}
	else {
		$salida .= '
		<tr>
			<td style="'.$color.'" align="center" ></td>
			<td style="'.$color.'" align="center" >TORO</td>
			<td style="'.$color.'" align="left"></td>
			<td style="'.$color.'" align="center"></td>
			<td style="'.$color.'" align="left"></td>
			<td style="'.$color.'" align="right"></td>
			<td style="'.$color.'" align="right"></td>
			<td style="'.$color.'" align="center">'.$diferencia_toro.'</td>
		</tr>';	
	}


	$existe_ref = true;

	// Obtenemos la referencia de BRASIL con el id_referencia de SMK
	$consulta_bra = sprintf("select * from referencias_brasil where activo=1 and id_referencia=%s",
		$db->makeValue($id_referencia,"int"));
	$db->setConsulta($consulta_bra);
	$db->ejecutarConsulta();
	$res_bra = $db->getPrimerResultado();

	if($res_bra != NULL) {
		$total_refs_bra++;
		$id_referencia_bra = $res_bra["id_referencia"];
		$nombre_bra = $res_bra["referencia"];
		$part_nombre_bra = $res_bra["part_nombre"];
		$part_tipo_bra = $res_bra["part_tipo"];
		$part_proveedor_referencia_bra = $res_bra["part_proveedor_referencia"];
		$pack_precio_bra = $res_bra["pack_precio"];
		$unidades_bra = $res_bra["unidades"];
		$id_proveedor_bra = $res_bra["id_proveedor"];

		$consulta_prov = sprintf("select nombre_prov from proveedores_brasil where activo=1 and id_proveedor=%s",
			$db->makeValue($id_proveedor_bra, "int"));
		$db->setConsulta($consulta_prov);
		$db->ejecutarConsulta();
		$res_prov = $db->getPrimerResultado();
		$nombre_proveedor = $res_prov["nombre_prov"];

		// Comprobamos la referencia del proveedor y el nombre referencia
		if(($nombre == $nombre_bra) && ($part_proveedor_referencia == $part_proveedor_referencia_bra)){
			// Coinciden. Comprobamos las unidades y pack_precio
			if($unidades == $unidades_bra){
				if($pack_precio == $pack_precio_bra){
					$diferencia_bra = '<span style="color: green;">LA REFERENCIA COINCIDE CON LA BBDD DE BRASIL</span>'; 	
					$total_refs_ok_bra++;
					$existe_ref = true;
				}
				else {
					$diferencia_bra = '<span style="color: orange;">PACK_PRECIO DISTINTO EN BRASIL</span>'; 	
					$total_refs_pack_precio_bra++;
					$existe_ref = true;
				}
			}
			else {
				$diferencia_bra = '<span style="color: orange;">UNIDADES_PAQUETE DISTINTAS EN BRA</span>'; 
				$total_refs_unidades_bra++;
				$existe_ref = true;
			}
		}
		else {
			// Comprobamos si existe la referencia en BRASIL con otro id_ref distinto
			$consulta_id_ref = sprintf("select * from referencias_brasil where referencia=%s and part_proveedor_referencia=%s and activo=1",
				$db->makeValue($nombre,"text"),
				$db->makeValue($part_proveedor_referencia, "text"));
			$db->setConsulta($consulta_id_ref);
			$db->ejecutarConsulta();
			$res_id_ref = $db->getPrimerResultado();

			if($res_id_ref != NULL){
				$id_ref_bra = $res_id_ref["id_referencia"];
				$diferencia_bra = '<span style="color: purple;">NO COINCIDEN LOS ID_REF: SMK['.$id_referencia.'] BRA['.$id_ref_bra.']</span>'; 
				$total_refs_ids_bra++;
				$existe_ref = true;
			}
			else {
				$diferencia_bra = '<span style="color: blue;">LAS REFERENCIAS NO COINCIDEN</span>'; 
				$total_refs_difs_bra++;
				$existe_ref = true;
			}
		}
	}
	else {
		// Comprobamos si existe la referencia en BRASIL con otro id_ref distinto
		$consulta_id_ref = sprintf("select * from referencias_brasil where referencia=%s and part_proveedor_referencia=%s and activo=1",
			$db->makeValue($nombre,"text"),
			$db->makeValue($part_proveedor_referencia, "text"));
		$db->setConsulta($consulta_id_ref);
		$db->ejecutarConsulta();
		$res_id_ref = $db->getPrimerResultado();

		if($res_id_ref != NULL){
			$id_ref_bra = $res_id_ref["id_referencia"];
			$diferencia_bra = '<span style="color: purple;">NO COINCIDEN LOS ID_REF: SMK['.$id_referencia.'] BRA['.$id_ref_bra.']</span>'; 
			$total_refs_ids_bra++;
			$existe_ref = false;
		}
		else {
			$diferencia_bra = '<span style="color: red;">NO EXISTE EL ID_REFERENCIA ['.$id_referencia.'] EN BRASIL</span>'; 
			$total_refs_no_exist_bra++;
			$existe_ref = false;
		}
	}

	// Preparamos la codificacion de la referencia 
	$nombre_ref = '';
	$nombre_referencia_codificada = utf8_decode($nombre_bra);
	for($m=0;$m<strlen($nombre_referencia_codificada);$m++){
		if ($nombre_referencia_codificada[$m] == '?'){
			$nombre_ref .= '&#8364;'; 	
		}
		else {
			$nombre_ref .= $nombre_referencia_codificada[$m]; 
		}
	}

	$ref_prov = '';
	$ref_prov_codificada = utf8_decode($part_proveedor_referencia_bra);
	for($m=0;$m<strlen($ref_prov_codificada);$m++){
		if ($ref_prov_codificada[$m] == '?'){
			$ref_prov .= '&#8364;'; 	
		}
		else {
			$ref_prov .= $ref_prov_codificada[$m]; 
		}
	}

	$tipo_pieza = '';
	$tipo_pieza_codificada = utf8_decode($part_tipo_bra);
	for($m=0;$m<strlen($tipo_pieza_codificada);$m++){
		if ($tipo_pieza_codificada[$m] == '?'){
			$tipo_pieza .= '&#8364;'; 	
		}
		else {
			$tipo_pieza .= $tipo_pieza_codificada[$m]; 
		}
	}

	if($i % 2 == 0) $color = ' background-color: #fff;';
	else $color = ' background-color: #eee;';

	// Generamos la fila HTML de la tabla correspondiente a una referencia
	if($existe_ref){
		$salida .= '
		<tr>
			<td style="'.$color.'" align="center" >'.$id_referencia_bra.'</td>
			<td style="'.$color.'" align="center" >BRASIL</td>
			<td style="'.$color.'" align="left">'.$nombre_ref.'</td>
			<td style="'.$color.'" align="center">'.$ref_prov.'</td>
			<td style="'.$color.'" align="left">'.utf8_decode($nombre_proveedor).'</td>
			<td style="'.$color.'" align="right">'.number_format($pack_precio_bra,2,',','.').'</td>
			<td style="'.$color.'" align="right">'.utf8_decode($unidades_bra).'</td>
			<td style="'.$color.'" align="center">'.$diferencia_bra.'</td>
		</tr>';
	}
	else {
		$salida .= '
		<tr>
			<td style="'.$color.'" align="center" ></td>
			<td style="'.$color.'" align="center" >BRASIL</td>
			<td style="'.$color.'" align="left"></td>
			<td style="'.$color.'" align="center"></td>
			<td style="'.$color.'" align="left"></td>
			<td style="'.$color.'" align="right"></td>
			<td style="'.$color.'" align="right"></td>
			<td style="'.$color.'" align="center">'.$diferencia_bra.'</td>
		</tr>';	
	}
}

$table_end = '</table>';

$tabla_res = '<table>
				<tr></tr>
				<tr></tr>
				<tr>
					<td style="color: black">TOTAL REFERENCIAS SMK: '.$total_refs_smk.'</td>
				</tr>
				<tr>
					<td style="color: black">TOTAL REFERENCIAS TOR: '.$total_refs_tor.'</td>
				</tr>
				<tr>
					<td style="color: black">TOTAL REFERENCIAS BRA: '.$total_refs_bra.'</td>
				</tr>
				<tr>	
					<td style="color: green">TOTAL REFERENCIAS OK TORO: '.$total_refs_ok_toro.'</td>
				</tr>
				<tr>	
					<td style="color: green">TOTAL REFERENCIAS OK BRASIL: '.$total_refs_ok_bra.'</td>
				</tr>
				<tr>		
					<td style="color: orange">TOTAL REFS. PACK_PRECIO TORO: '.$total_refs_pack_precio_toro.'</td>
				</tr>	
				<tr>		
					<td style="color: orange">TOTAL REFS. PACK_PRECIO BRASIL: '.$total_refs_pack_precio_bra.'</td>
				</tr>	
				<tr>
					<td style="color: orange">TOTAL REFS. UDS_PAQUETE TORO: '.$total_refs_unidades_toro.'</td>
				</tr>
				<tr>
					<td style="color: orange">TOTAL REFS. UDS_PAQUETE BRASIL: '.$total_refs_unidades_bra.'</td>
				</tr>
				<tr>
					<td style="color: purple">TOTAL REFS. IDS DISTINTOS TORO: '.$total_refs_ids_toro.'</td>
				</tr>
				<tr>
					<td style="color: purple">TOTAL REFS. IDS DISTINTOS BRASIL: '.$total_refs_ids_bra.'</td>
				</tr>
				<tr>
					<td style="color: blue">TOTAL REFS. DISTINTAS TORO: '.$total_refs_difs_toro.'</td>
				</tr>
				<tr>
					<td style="color: blue">TOTAL REFS. DISTINTAS BRASIL: '.$total_refs_difs_bra.'</td>
				</tr>
				<tr>
					<td style="color: red">TOTAL REFS. NO EXISTEN EN TORO: '.$total_refs_no_exist_toro.'</td>
				</tr>
				<tr>
					<td style="color: red">TOTAL REFS. NO EXISTEN EN BRASIL: '.$total_refs_no_exist_bra.'</td>
				</tr>
			</table>';

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=excelReferenciasDIF.xls");

echo $table.$salida.$table_end.$tabla_res; 
?>