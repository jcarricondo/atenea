<?php
// Fichero que genera el excel con los boxes asignados al tecnico
// Se mostraran las referencias de los componentes que pertenecen a los boxes asignados al tecnico
$op = new Orden_Produccion();
$oc = new Orden_Compra();
$referencia = new Referencia();

$salida = "";

$salida .= '<table>
				<tr>
					<td colspan="3" style="font-weight:bold;">CODIGO</td> 
					<td colspan="3" style="font-weight:bold;">'.$codigo.'</td>
				</tr>
				<tr>
					<td colspan="3" style="font-weight:bold;">ALIAS</td> 
					<td colspan="3" style="font-weight:bold;">'.$alias.'</td>
				</tr>
				<tr>
					<td colspan="3" style="font-weight:bold;">CODIGO ESCANDALLO</td> 
					<td colspan="3" style="font-weight:bold;">'.$codigo_escandallo.'</td>
				</tr>
				<tr>
					<td colspan="3" style="font-weight:bold;">TECNICO </td> 
					<td colspan="3" style="font-weight:bold;">TECNICO_'.$tec.'</td>
				</tr>
				<tr>

				</tr>
			</table>';
	
// Utilizamos una variable auxiliar para no cambiar el valor de contador_componente
$contador_componente_reposicion = $contador_componente;

// Recorremos los boxes asignados al tecnico. Un componente puede ser fabricado en varios boxes
for($k=0;$k<$num_boxes_por_tecnico;$k++){
	// Obtenemos el nombre del box
	$nombre_box = $box->dameNombreBox($boxes_finales[$contador_componente_reposicion]);
	$nombre_box = $nombre_box[0]["nombre"];

	// En la primera fila mostramos el nombre del box
	$salida .= '<table>
					<tr>
						<td style="text-align: center;"></td>
						<td style="text-align: center;"></td>
						<td style="font-weight:bold; color: green;">'.$nombre_box.'</td>
						<td style="text-align:center; font-weight:bold; color: green;"></td>
						<td style="text-align:center; font-weight:bold; color: green;">TECNICO_'.$tec.'</td>';
		$salida .= '</tr>';			
	$salida .= '</table>';										

	$salida .= '<table>
					<tr>
						<td style="text-align:center; font-weight:bold; background: #F9FCAA;"></td>
						<td style="text-align:center; font-weight:bold; background: #F9FCAA;">ID_REF</td>
						<td style="font-weight:bold; background: #F9FCAA; max-width: 500px;">REFERENCIA</td>
						<td style="text-align:center; font-weight:bold; background: #F9FCAA;">TOTAL PIEZAS</td>
						<td style="text-align:center; font-weight:bold; background: #F9FCAA;">UNIDADES ALMACEN</td>
					</tr>';	

	// Cargamos los datos del box
	$resultados_box = $box->cargaDatosBoxPorIdBox($boxes_finales[$contador_componente_reposicion],$id_sede);			

	$id_componente = $ids_componentes_finales[$contador_componente_reposicion];
	// DESPUES DE UNIFICACION (Resuelve el problema de varios componentes en un mismo box. TESTEAR)
	//$id_componente = $resultados_box[0]["id_componente"]; (Antiguo)
	$unidades_fabrican = $unidades_finales[$contador_componente_reposicion];

	// Obtenemos las referencias del componente y las referencias de sus kits
	// Si hay referencias repetidas suma las piezas y las agrupamos por referencia
	$referencias_componente = $op->dameIdReferenciaPiezasPorIdComponente($id_produccion,$id_componente);	
	$referencias_componente_total = $referencias_componente;

	// Comprobamos si el componente tiene kits
	$op->dameIdsKitComponente($id_componente);

	for($l=0;$l<count($op->ids_kit);$l++){
		$id_kit = $op->ids_kit[$l]["id_kit"];
		// Obtenemos las referencias de los kits
		$referencias_componente = $op->dameIdReferenciaPiezasPorIdComponente($id_produccion,$id_kit);	

		// Agrupamos las referencias del kit con las referencias totales
		if($referencias_componente != NULL){
			if($referencias_componente_total != NULL){
				$referencias_componente_total = $op->agruparReferenciasComponentes($referencias_componente,$referencias_componente_total);
			}
			else{
				$referencias_componente_total = $referencias_componente;					
			}
		}
	}

	// Multiplicamos el total de las piezas por las unidades que fabrican en ese box de ese componente
	for($l=0;$l<count($referencias_componente_total);$l++){
		$referencias_componente_total[$l]["piezas"] = $referencias_componente_total[$l]["piezas"] * $unidades_fabrican;
	}	

	// Guardamos todas las referencias del box 
	if($referencias_componente_total != NULL){
		if($referencias_total_box != NULL){
			$referencias_total_box = $op->agruparReferenciasComponentes($referencias_componente_total,$referencias_total_box);
		}
		else{
			$referencias_total_box = $referencias_componente_total;					
		}
	}	
	unset($referencias_componente_total);

	// Ordenamos el array por id_refencia
	if($referencias_total_box != NULL){
		array_multisort($referencias_total_box); 
	}	

	// Recorremos todas las referencias del box y generamos las filas del excel
	for($j=0;$j<count($referencias_total_box);$j++){
		// Cargamos los datos de las referencias
		$id_ref = $referencias_total_box[$j]["id_referencia"];
		$piezas = $referencias_total_box[$j]["piezas"];
		$referencia->cargaDatosReferenciaId($id_ref);

		// Obtenemos las id_compra con id_referencia e id_produccion para ver cuantas piezas hay en el almacen (P.REC - P.USADAS)
		$consulta = sprintf("select id_orden_compra from orden_compra inner join referencias on (referencias.id_proveedor = orden_compra.id_proveedor) where id_produccion=%s and referencias.id_referencia=%s and orden_compra.activo=1",
			$db->makeValue($id_produccion, "int"),
			$db->makeValue($id_ref, "int"));
		$db->setConsulta($consulta);
		$db->ejecutarConsulta();
		$id_compra = $db->getPrimerResultado();
		$id_compra = $id_compra["id_orden_compra"];

		// Codificamos las variables para que se muestren correctamente en el excel
		$nombre_referencia = '';
		$nombre_referencia_codificada = utf8_decode($referencia->referencia);
		for($m=0;$m<strlen($nombre_referencia_codificada);$m++){
			if ($nombre_referencia_codificada[$m] == '?'){
				$nombre_referencia .= '&#8364;'; 	
			}
			else {
				$nombre_referencia .= $nombre_referencia_codificada[$m]; 
			}
		}

		$fila = $j + 1;
		// Comprobamos si la fila es par o impar
		if (($j % 2) == 0){
			$salida .= '<tr>
							<td style="text-align:center; background: #fff;">'.$fila.'</td>
							<td style="text-align:center; background: #fff;">'.$id_ref.'</td>
							<td style="background: #fff; max-width: 500px;">'.$nombre_referencia.'</td>
							<td style="text-align:center; background: #fff; font-weight: bold;">'.$piezas.'</td>
							<td style="text-align:center; font-size: 10px; background: #fff;">'.$oc->getTotalPiezasEnAlmacenReferencia($id_compra,$id_ref).'</td>
						</tr>';
		}				
		else {
			$salida .= '<tr>
							<td style="text-align:center; background: #eee;">'.$fila.'</td>
							<td style="text-align:center; background: #eee;">'.$id_ref.'</td>
							<td style="background: #eee; max-width: 500px;">'.$nombre_referencia.'</td>
							<td style="text-align:center; background: #eee; font-weight: bold;">'.$piezas.'</td>
							<td style="text-align:center; font-size: 10px; background: #eee;">'.$oc->getTotalPiezasEnAlmacenReferencia($id_compra,$id_ref).'</td>
						</tr>';
		}
	}

	// Calculamos el numero total de referencias de todos los boxes asignadas al reponedor
	if($referencias_total_box != NULL){
		if($referencias_piking != NULL){
			$referencias_piking = $op->agruparReferenciasComponentes($referencias_total_box,$referencias_piking);
		}
		else{
			$referencias_piking = $referencias_total_box;					
		}
	}

	// Ordenamos el array por id_refencia
	if($referencias_piking != NULL){
		array_multisort($referencias_piking); 
	}

	// Reseteamos el array de referencias de los componentes del tecnico
	unset($referencias_total_box);
	unset($referencias_componente);
	unset($id_componentes);
	unset($unidades_fabrican);
$salida .= '<tr></tr>';
$contador_componente_reposicion++;
}	

$contador_componente = $contador_componente_reposicion;

$salida .= '</table>';

// Lanzamos para descarga el excel generado
$csv = $salida;
// $ruta = $dir_actual."\EscandalloReposicion".$tec.".xls"; // LOCAL
$ruta = $dir_actual."/EscandalloReposicion".$tec.".xls"; // PRODUCCION

$fp=fopen($ruta,"w");
fwrite($fp,$csv);
fclose($fp);
?>
