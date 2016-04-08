<?php
// Fichero que genera el excel con las referencias agrupadas de todos los boxes asignados al tecnico
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

$salida .= '<table>
					<tr>
						<td style="text-align:center; font-weight:bold; background: #F9FCAA;"></td>
						<td style="text-align:center; font-weight:bold; background: #F9FCAA;">ID_REF</td>
						<td style="font-weight:bold; background: #F9FCAA; max-width: 500px;">REFERENCIA</td>
						<td style="text-align:center; font-weight:bold; background: #F9FCAA;">TOTAL PIEZAS</td>
						<td style="text-align:center; font-weight:bold; background: #F9FCAA;">UNIDADES ALMACEN</td>
					</tr>';

// Utilizamos una variable auxiliar para no cambiar el valor de contador_componente
$contador_componente_piking = $contador_componente;

for($j=0;$j<count($referencias_piking);$j++){
	// Cargamos los datos de las referencias
	$id_ref = $referencias_piking[$j]["id_referencia"];
	$total_piezas = $referencias_piking[$j]["piezas"];
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
						<td style="text-align:center; background: #fff; font-weight: bold;">'.$total_piezas.'</td>
						<td style="text-align:center; font-size: 10px; background: #fff;">'.$oc->getTotalPiezasEnAlmacenReferencia($id_compra,$id_ref).'</td>
					</tr>';
	}				
	else {
		$salida .= '<tr>
						<td style="text-align:center; background: #eee;">'.$fila.'</td>
						<td style="text-align:center; background: #eee;">'.$id_ref.'</td>
						<td style="background: #eee; max-width: 500px;">'.$nombre_referencia.'</td>
						<td style="text-align:center; background: #eee; font-weight: bold;">'.$total_piezas.'</td>
						<td style="text-align:center; font-size: 10px; background: #eee;">'.$oc->getTotalPiezasEnAlmacenReferencia($id_compra,$id_ref).'</td>
					</tr>';
	}
}

$salida .= '<tr></tr></table>';	

// Guardamos las referencias agrupadas de todos los componentes seleccionados para poder actualizar las piezas usadas
if($referencias_componentes_seleccionados != NULL){
	$referencias_componentes_seleccionados = $op->agruparReferenciasComponentes($referencias_piking,$referencias_componentes_seleccionados);
} 
else{
	$referencias_componentes_seleccionados = $referencias_piking;
}

// Reseteamos el array de las referencias agrupadas de los componentes asignados al tecnico
unset($referencias_piking);

// Lanzamos para descarga el excel generado
$csv = $salida;
// $ruta = $dir_actual."\EscandalloPiking".$tec.".xls"; // LOCAL
$ruta = $dir_actual."/EscandalloPiking".$tec.".xls"; // PRODUCCION

$fp=fopen($ruta,"w");
fwrite($fp,$csv);
fclose($fp);

// Actualizamos el contador de componentes y el numero de componentes a asignar para el siguiente tecnico
$contador_componente = $contador_componente_reposicion;
$num_boxes_asignados = $num_boxes_asignados - $num_boxes_por_tecnico;
$tecnicos_por_asignar--;

if($tecnicos_por_asignar > 0){
	if($tecnicos_por_asignar == 1){
		if($num_boxes_asignados > $num_boxes_por_tecnico){
			$num_boxes_por_tecnico = $num_boxes_asignados;	
		}
	} 
	if($num_boxes_asignados < $num_boxes_por_tecnico){
		$num_boxes_por_tecnico = $num_boxes_asignados;
	}
}	
    	
$tec++;
$dir_actual = $dir_descarga;
?>
