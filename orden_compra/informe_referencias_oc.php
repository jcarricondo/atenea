<?php
// Este fichero genera un excel con las referencias de una OC
include("../includes/sesion.php");
include("../classes/basicos/referencia.class.php");
include("../classes/orden_compra/orden_compra.class.php");

$id_compra	= $_GET["id_compra"];
$db = new MySQL();
$referencia = new Referencia();
$oc = new Orden_Compra();
// Consulta para obtener los id de las referencias de la OC
$oc->dameIdReferencias($id_compra);
$ids_referencias = $oc->ids_referencias;

$salida = "";
for ($i=0;$i<count($ids_referencias);$i++){
	// Consulta para obtener los datos de las referencias
	//$oc->dameDatosOrdenCompraReferencias($id_compra);
	$oc->dameDatosOCReferencias($id_compra);
	$referencia->cargaDatosReferenciaId($ids_referencias[$i]["id_referencia"]);
	$total_piezas = $oc->referencias[$i]["total_piezas"];
	$total_piezas = round($total_piezas,2);
	$unidades_paquete = $oc->referencias[$i]["uds_paquete"];
	$pack_precio = $oc->referencias[$i]["pack_precio"];
	if ($unidades_paquete != 0) {
		$total_paquetes = $total_piezas / $unidades_paquete;
	}
	else {
		$total_paquetes = 0;
	}

	if($total_paquetes < 1) { $total_paquetes = 1; }
	$precio_referencia = ($total_paquetes * $pack_precio);

	$referencia->calculaTotalPaquetes($unidades_paquete,$total_piezas);
	$total_paquetes = $referencia->total_paquetes;
	$precio_referencia = ($total_paquetes * $pack_precio);

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

	$porcentaje_recepcion = $oc->getPorcentajeRecepcionReferencia($id_compra,$referencia->id_referencia);
	if($oc->piezas_recibidas <> 0) {
		$porcentaje_recepcion = ($oc->piezas_recibidas * 100 ) / $oc->piezas_pedidas;
	} else {
		$porcentaje_recepcion = 0;
	}
	$porcentaje_recepcion = number_format($porcentaje_recepcion,2,',','.');

	$salida .= '
		<table>
		<tr>
			<td align="center">'.$referencia->id_referencia.'</td>
			<td>'.$nombre_ref.'</td>
			<td align="center">'.$ref_prov.'</td>
			<td>'.utf8_decode($referencia->nombre_proveedor).'</td>
			<td>'.number_format($total_piezas,2,',','.').'</td>
			<td align="right">'.number_format($precio_referencia,2,',','.').'</td>
			<td align="right">'.number_format($total_paquetes,2,',','.').'</td>
			<td align="right">'.$oc->piezas_pedidas.'</td>
			<td align="right">'.$oc->piezas_recibidas.'</td>
			<td align="center">'.$porcentaje_recepcion.'</td>
			<td align="center">'.$tipo_pieza.'</td>
			<td>'.utf8_decode($referencia->part_nombre).'</td>
			<td>'.utf8_decode($referencia->nombre_fabricante).'</td>
			<td align="center">'.$ref_fab.'</td>
			<td>'.$descrip.'</td>
			<td>'.$valor_nombre.'</td>
			<td>'.utf8_decode($referencia->part_valor_cantidad).'</td>
			<td>'.$valor_nombre2.'</td>
			<td>'.utf8_decode($referencia->part_valor_cantidad_2).'</td>
			<td>'.$valor_nombre3.'</td>
			<td>'.utf8_decode($referencia->part_valor_cantidad_3).'</td>
			<td>'.$valor_nombre4.'</td>
			<td>'.utf8_decode($referencia->part_valor_cantidad_4).'</td>
			<td>'.$valor_nombre5.'</td>
			<td>'.utf8_decode($referencia->part_valor_cantidad_5).'</td>
			<td align="right">'.number_format($referencia->pack_precio,2,',','.').'</td>
			<td>'.utf8_decode($referencia->unidades).'</td>
			<td>'.$coments.'</td>
		</tr>
		</table>';
}

$table = '<table>
	<tr>
		<th>ID Ref.</th>
    	<th>Nombre</th>
        <th>Referencia Proveedor</th>
        <th>Proveedor</th>
        <th>Piezas</th>
        <th>Precio</th>
		<th>Total Paquetes</th>
		<th>Piezas Pedidas</th>
		<th>Piezas recibidas</th>
		<th>% Recibido</th>
        <th>Tipo Pieza</th>
        <th>Nombre Pieza</th>
        <th>Fabricante</th>
        <th>Referencia Fabricante</th>
        <th>Descripci&oacute;n</th>
        <th>Nombre</th>
        <th>Valor</th>
        <th>Nombre 2</th>
        <th>Valor 2</th>
        <th>Nombre 3</th>
        <th>Valor 3</th>
        <th>Nombre 4</th>
        <th>Valor 4</th>
        <th>Nombre 5</th>
        <th>Valor 5</th>
        <th>Precio Pack</th>
        <th>Unidades Paquete</th>
        <th>Comentarios</th>
    </tr>';
$table_end = '</table>';

// Obtenemos el numero de pedido para darle nombre al archivo
$oc->cargaDatosOrdenCompraId($id_compra);
$numero_pedido = str_replace(' ', '_', $oc->numero_pedido);

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$numero_pedido.".xls");
echo $table.$salida.$table_end;
?>