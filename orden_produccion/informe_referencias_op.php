<?php 
//Este fichero genera un excel con las referencias de la OP
include("../includes/sesion.php");
include("../classes/basicos/referencia.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/productos/producto.class.php");

$id	= $_GET["id"];

$db = new MySQL();
$referencia = new Referencia();
$op = new Orden_Produccion();
$producto = new Producto();
$referencia = new Referencia();

// Cargamos las referencias de orden_produccion_referencias
$resultados = $op->dameOCReferenciasPorProduccion($id);

$table = '<table>
	<tr>
		<th style="text-align: center;">ID Ref.</th>
    	<th style="text-align: left;">Nombre</th>
        <th style="text-align: left;">Referencia Proveedor</th>
        <th style="text-align: left;">Proveedor</th>
		<th style="text-align: right;">Unidades por paquete</th>
		<th style="text-align: right;">Unidades por simulador</th>
		<th style="text-align: right;">Paquetes por simulador</th>
		<th style="text-align: right;">Precio por paquete</th>
		<th style="text-align: right;">Precio por unidad</th>
		<th style="text-align: right;">Precio por simulador (en base unidades)</th>
		<th style="text-align: right;">Precio por simulador (en base paquetes)</th>
        <th style="text-align: right;">Piezas Pedidas</th>
        <th style="text-align: right;">Piezas Recibidas</th>
        <th style="text-align: right;">Piezas Restantes</th>
        <th style="text-align: right;">Piezas Usadas</th>
        <th style="text-align: right;">Precio</th>
		<th style="text-align: right;">Total Paquetes</th>
        <th style="text-align: left;">Tipo Pieza</th>
        <th style="text-align: left;">Nombre Pieza</th>
        <th style="text-align: left;">Fabricante</th>
        <th style="text-align: left;">Referencia Fabricante</th>
        <th style="text-align: left;">Descripci&oacute;n</th>
        <th style="text-align: left;">Nombre</th>
        <th style="text-align: left;">Valor</th>
        <th style="text-align: left;">Nombre 2</th>
        <th style="text-align: left;">Valor 2</th>
        <th style="text-align: left;">Nombre 3</th>
        <th style="text-align: left;">Valor 3</th>
        <th style="text-align: left;">Nombre 4</th>
        <th style="text-align: left;">Valor 4</th>
        <th style="text-align: left;">Nombre 5</th>
        <th style="text-align: left;">Valor 5</th>
        <th style="text-align: left;">Comentarios</th>
    </tr>';

$salida = "";

// Preparamos las refernecias
for($i=0;$i<count($resultados);$i++){
	$id_ref = $resultados[$i]["id_referencia"];
	$unidades_por_paquete = $resultados[$i]["uds_paquete"];
	$unidades_por_simulador = $resultados[$i]["piezas"];
	$precio_por_paquete = $resultados[$i]["pack_precio"];
	if($unidades_por_paquete != 0) {
		$paquetes_por_simulador = ceil($unidades_por_simulador / $unidades_por_paquete);
		$precio_por_unidad = round($precio_por_paquete / $unidades_por_paquete, 2, PHP_ROUND_HALF_UP);
	}
	else {
		$paquetes_por_simulador = 0;
		$precio_por_unidad = 0;
	}
	$precio_por_simulador_unidades = $unidades_por_simulador * $precio_por_unidad;
	$precio_por_simulador_paquetes = $paquetes_por_simulador * $precio_por_paquete;

	$total_piezas = $resultados[$i]["total_piezas"];
	$total_piezas = round($total_piezas,2);
	$total_piezas_recibidas = $resultados[$i]["piezas_recibidas"];
	$total_piezas_recibidas = round($total_piezas_recibidas,2);
	$total_piezas_usadas = $resultados[$i]["piezas_usadas"];
	$total_piezas_usadas = round($total_piezas_usadas,2);
	$total_piezas_restantes = $total_piezas - $total_piezas_recibidas;
	$total_piezas_restantes = round($total_piezas_restantes,2);
	$coste = $resultados[$i]["coste"];
	$coste = round($coste,2);

	$piezas = $resultados[$i]["piezas"];
	$total_paquetes = $resultados[$i]["total_packs"];

	$referencia->cargaDatosReferenciaId($id_ref);
    $referencia->prepararCodificacionReferencia();

    $salida .= '
		<table>
		<tr>
			<td style="text-align: center;">'.$referencia->id_referencia.'</td>
			<td style="text-align: left;">'.$referencia->referencia.'</td>
			<td style="text-align: left;">'.$referencia->part_proveedor_referencia.'</td>
			<td style="text-align: left;">'.$referencia->nombre_proveedor.'</td>
			<td style="text-align: right;">'.number_format($unidades_por_paquete,2,',','.').'</td>
			<td style="text-align: right;">'.number_format($unidades_por_simulador,2,',','.').'</td>
			<td style="text-align: right;">'.number_format($paquetes_por_simulador,2,',','.').'</td>
			<td style="text-align: right;">'.number_format($precio_por_paquete,2,',','.').'</td>
			<td style="text-align: right;">'.number_format($precio_por_unidad,2,',','.').'</td>
			<td style="text-align: right;">'.number_format($precio_por_simulador_unidades,2,',','.').'</td>
			<td style="text-align: right;">'.number_format($precio_por_simulador_paquetes,2,',','.').'</td>
			<td style="text-align: right;">'.number_format($total_piezas,2,',','.').'</td>
			<td style="text-align: right;">'.number_format($total_piezas_recibidas,2,',','.').'</td>
			<td style="text-align: right;">'.number_format($total_piezas_restantes,2,',','.').'</td>
			<td style="text-align: right;">'.number_format($total_piezas_usadas,2,',','.').'</td>
			<td style="text-align: right;">'.number_format($coste,2,',','.').'</td>
			<td style="text-align: right;">'.number_format($total_paquetes,2,',','.').'</td>
			<td style="text-align: left;">'.$referencia->part_tipo.'</td>
			<td style="text-align: left;">'.$referencia->part_nombre.'</td>
			<td style="text-align: left;">'.$referencia->nombre_fabricante.'</td>
			<td style="text-align: left;">'.$referencia->part_fabricante_referencia.'</td>
			<td style="text-align: left;">'.$referencia->part_descripcion.'</td>
			<td style="text-align: left;">'.$referencia->part_valor_nombre.'</td>
			<td style="text-align: left;">'.$referencia->part_valor_cantidad.'</td>
			<td style="text-align: left;">'.$referencia->part_valor_nombre_2.'</td>
			<td style="text-align: left;">'.$referencia->part_valor_cantidad_2.'</td>
			<td style="text-align: left;">'.$referencia->part_valor_nombre_3.'</td>
			<td style="text-align: left;">'.$referencia->part_valor_cantidad_3.'</td>
			<td style="text-align: left;">'.$referencia->part_valor_nombre_4.'</td>
			<td style="text-align: left;">'.$referencia->part_valor_cantidad_4.'</td>
			<td style="text-align: left;">'.$referencia->part_valor_nombre_5.'</td>
			<td style="text-align: left;">'.$referencia->part_valor_cantidad_5.'</td>
			<td style="text-align: left;">'.$referencia->comentarios.'</td>
		</tr>';
}
$table_end = '</table>';
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=informeReferencias.xls");
echo $table.$salida.$table_end;
?>
