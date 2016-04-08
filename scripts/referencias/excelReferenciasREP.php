<?php 
set_time_limit(10000);
// Excel que muestra las diferencias repetidas en SIMUMAK
include("../../classes/mysql.class.php");
include("../../classes/basicos/referencia.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySQL();
$referencia = new Referencia();

$total_refs_smk = 0;
$total_refs_no_activas_smk = 0;
$total_refs_repetidas = 0;

$existe_ref = true;

// Obtenemos las referencias de TORNILLERIA actualizadas recientemente
$consultaTORN = "select id_referencia from referencias where id_proveedor=101 and activo=1";
$db->setConsulta($consultaTORN);
$db->ejecutarConsulta();
$res_refs_TORN = $db->getResultados();

for($i=0;$i<count($res_refs_TORN);$i++){
	$array_tornilleria[] = $res_refs_TORN[$i]["id_referencia"];
}	


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
        <th style="background:green; color: white; text-align: center;">NUM. COMPONENTES ACTIVOS CON ESA REFERENCIA</th>
        <th style="background:green; color: white; text-align: center;">ACTUALIZADA TORNILLERIA</th>
    </tr>';
	
$consulta = "select id_referencia from referencias order by id_referencia";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res = $db->getResultados();

$total_refs_smk = count($res); 
$cont_rep = 0; 

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
	$activo_smk = $referencia->dameDigitoActivoReferencia($id_referencia); 

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

	// Comprobamos si la referencia es de Tornilleria
	$es_tornilleria = in_array($id_referencia, $array_tornilleria); 
	if($es_tornilleria) {
		$act_tornilleria = "SI";
	}
	else {
		$act_tornilleria = "-";
	}

	$repetidas = '';
	if($activo_smk == 0){
		$color = ' background-color: red; color: white;';	
		$total_refs_no_activas_smk++;
	}
	else {
		// Comprobamos si el nombre de la referencia coincide con el de otra referencia
		$consulta_nombre = sprintf("select id_referencia from referencias where referencia=%s and part_proveedor_referencia=%s and id_referencia>%s and activo=1 ",
			$db->makeValue($nombre, "text"),
			$db->makeValue($part_proveedor_referencia, "text"),
			$db->makeValue($id_referencia,"int"));
		$db->setConsulta($consulta_nombre);
		$db->ejecutarConsulta();
		$res_duplicadas = $db->getResultados();

		if($res_duplicadas != NULL){
			// $repetidas = '<span style="color: brown;">MISMO NOMBRE QUE REF: '; 	
			if($cont_rep % 2 == 0) $color = ' background-color: #fff; color: black;';
			else $color = ' background-color: #eee; color: black;';

			// Obtenemos el numero de componentes en los que esta montada esa referencia 
			$consultaComponentes = sprintf("select id_componente from componentes_referencias where activo=1 and id_referencia=%s order by id_componente",
				$db->makeValue($id_referencia, "int"));
			$db->setConsulta($consultaComponentes);
			$db->ejecutarConsulta();
			$num_componentes = $db->getNumeroFilas();

			$salida .= '
			<tr>
				<td style="'.$color.'" align="center" >'.$referencia->id_referencia.'</td>
				<td style="'.$color.'" align="center" >SIMUMAK</td>
				<td style="'.$color.'" align="left">'.$nombre_ref.'</td>
				<td style="'.$color.'" align="center">'.$ref_prov.'</td>
				<td style="'.$color.'" align="left">'.utf8_decode($referencia->nombre_proveedor).'</td>
				<td style="'.$color.'" align="right">'.number_format($referencia->pack_precio,2,',','.').'</td>
				<td style="'.$color.'" align="right">'.utf8_decode($referencia->unidades).'</td>
				<td style="'.$color.'" align="center">'.$num_componentes.'</td>
				<td style="'.$color.'" align="center">'.$act_tornilleria.'</td>
			</tr>';

			for($z=0;$z<count($res_duplicadas);$z++){
				$id_ref_rep = $res_duplicadas[$z]["id_referencia"];
				$total_refs_repetidas++;
				$existe_ref = true;
				
				$referencia->cargaDatosReferenciaId($id_ref_rep);
	
				$nombre_rep = $referencia->referencia;
				$part_nombre_rep = $referencia->part_nombre;
				$part_tipo_rep = $referencia->part_tipo;
				$part_proveedor_referencia_rep = $referencia->part_proveedor_referencia;
				$pack_precio_rep = $referencia->pack_precio;
				$unidades_rep = $referencia->unidades;
				$nombre_proveedor_rep = $referencia->nombre_proveedor;

				// Obtenemos el numero de componentes en los que esta montada esa referencia 
				$consultaComponentes = sprintf("select id_componente from componentes_referencias where activo=1 and id_referencia=%s order by id_componente",
					$db->makeValue($id_ref_rep, "int"));
				$db->setConsulta($consultaComponentes);
				$db->ejecutarConsulta();
				$num_componentes = $db->getNumeroFilas();

				// Comprobamos si la referencia es de Tornilleria
				$es_tornilleria = in_array($id_ref_rep, $array_tornilleria);
				if($es_tornilleria) {
					$act_tornilleria = "SI";
				}
				else {
					$act_tornilleria = "-";
				}
				
				// Generamos la fila HTML de la tabla correspondiente a una referencia
				$salida .= '
				<tr>
					<td style="'.$color.'" align="center" >'.$id_ref_rep.'</td>
					<td style="'.$color.'" align="center" >SIMUMAK</td>
					<td style="'.$color.'" align="left">'.$nombre_rep.'</td>
					<td style="'.$color.'" align="center">'.$part_proveedor_referencia_rep.'</td>
					<td style="'.$color.'" align="left">'.utf8_decode($nombre_proveedor_rep).'</td>
					<td style="'.$color.'" align="right">'.number_format($pack_precio_rep,2,',','.').'</td>
					<td style="'.$color.'" align="right">'.utf8_decode($unidades_rep).'</td>
					<td style="'.$color.'" align="center">'.$num_componentes.'</td>
					<td style="'.$color.'" align="center">'.$act_tornilleria.'</td>
				</tr>';
			}
			$cont_rep++;
		}
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
					<td style="color: red">TOTAL REFS. NO ACTIVAS EN SMK: '.$total_refs_no_activas_smk.'</td>
				</tr>
				<tr>
					<td style="color: #6E6E6E">TOTAL REFS. REPETIDAS EN SMK: '.$total_refs_repetidas.'</td>
				</tr>
			</table>';

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=ReferenciasRep.xls");
echo $table.$salida.$table_end.$tabla_res; 
?>