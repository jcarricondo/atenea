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

$op->cargaDatosProduccionId($id);
$num_simuladores = $op->unidades;

$table = '<table>
	<tr>
		<th style="text-align: center;">ID Ref.</th>
    	<th>Nombre</th>
        <th>Referencia Proveedor</th>
        <th>Proveedor</th>
		<th>Unidades por paquete</th>
		<th>Unidades por simulador</th>
		<th>Paquetes por simulador</th>
		<th>Precio por paquete</th>
		<th>Precio por unidad</th>
		<th>Precio por simulador (en base unidades)</th>
		<th>Precio por simulador (en base paquetes)</th>
        <th>Piezas Pedidas</th>
        <th>Piezas Recibidas</th>
        <th>Piezas Restantes</th>
        <th>Piezas Usadas</th>
        <th>Precio</th>
		<th>Total Paquetes</th>
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
        <th>Comentarios</th>
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
			<td align="center">'.$referencia->id_referencia.'</td>
			<td>'.$referencia->referencia.'</td>
			<td align="center">'.$referencia->part_proveedor_referencia.'</td>
			<td>'.$referencia->nombre_proveedor.'</td>
			<td>'.utf8_decode($unidades_por_paquete).'</td>
			<td>'.utf8_decode($unidades_por_simulador).'</td>
			<td>'.utf8_decode($paquetes_por_simulador).'</td>
			<td align="right">'.number_format($precio_por_paquete,2,',','.').'</td>
			<td align="right">'.number_format($precio_por_unidad,2,',','.').'</td>
			<td align="right">'.number_format($precio_por_simulador_unidades,2,',','.').'</td>
			<td align="right">'.number_format($precio_por_simulador_paquetes,2,',','.').'</td>
			<td>'.number_format($total_piezas,2,',','.').'</td>
			<td>'.number_format($total_piezas_recibidas,2,',','.').'</td>
			<td>'.number_format($total_piezas_restantes,2,',','.').'</td>
			<td>'.number_format($total_piezas_usadas,2,',','.').'</td>
			<td align="right">'.number_format($coste,2,',','.').'</td>
			<td align="right">'.number_format($total_paquetes,2,',','.').'</td>
			<td align="center">'.$referencia->part_tipo.'</td>
			<td>'.$referencia->part_nombre.'</td>
			<td>'.$referencia->nombre_fabricante.'</td>
			<td align="center">'.$referencia->part_fabricante_referencia.'</td>
			<td>'.$referencia->part_descripcion.'</td>
			<td>'.$referencia->part_valor_nombre.'</td>
			<td>'.$referencia->part_valor_cantidad.'</td>
			<td>'.$referencia->part_valor_nombre_2.'</td>
			<td>'.$referencia->part_valor_cantidad_2.'</td>
			<td>'.$referencia->part_valor_nombre_3.'</td>
			<td>'.$referencia->part_valor_cantidad_3.'</td>
			<td>'.$referencia->part_valor_nombre_4.'</td>
			<td>'.$referencia->part_valor_cantidad_4.'</td>
			<td>'.$referencia->part_valor_nombre_5.'</td>
			<td>'.$referencia->part_valor_cantidad_5.'</td>
			<td>'.$referencia->comentarios.'</td>
		</tr>';
}
$table_end = '</table>';
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=informeReferencias.xls");
echo $table.$salida.$table_end; 
?>
