<?php 
// Este fichero genera un excel con las referencias de laS OP combinadas
include("../includes/sesion.php");
include("../classes/basicos/referencia.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/productos/producto.class.php");

// Obtenemos los ids por url. Si hay varios, los extraemos uno a uno. 
$ids = $_GET["ids_produccion"];
$ids_produccion = explode(",",$ids);

$db = new MySQL();
$referencia = new Referencia();
$op = new Orden_Produccion();
$producto = new Producto();
$referencia = new Referencia();

// Obtenemos las referencias y el total de las piezas de las Ordenes de Produccion
$resultados = $op->dameReferenciasVariasOp($ids_produccion);

$table = '<table>
	<tr>
		<th style="text-align: center;">ID Ref.</th>
    	<th style="text-align: left;">Nombre</th>
        <th style="text-align: left;">Referencia Proveedor</th>
        <th style="text-align: left;">Proveedor</th>
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
        <th style="text-align: right;">Precio Pack</th>
        <th style="text-align: right;">Unidades Paquete</th>
        <th style="text-align: left;">Comentarios</th>
    </tr>';

$salida = "";

for($i=0;$i<count($resultados);$i++){
	$id_referencia = $resultados[$i]["id_referencia"];
	$total_piezas_ops = $resultados[$i]["total_piezas_ops"];
	$pack_precio = $resultados[$i]["pack_precio"];
	$unidades_paquete = $resultados[$i]["uds_paquete"];

	$referencia->cargaDatosReferenciaId($id_referencia);
	$referencia->calculaTotalPaquetes($unidades_paquete,$total_piezas_ops);
	$total_paquetes = $referencia->total_paquetes;
	$precio_referencia = ($total_paquetes * $pack_precio);

	$total_piezas_recibidas = $resultados[$i]["total_piezas_rec"];
	$total_piezas_usadas = $resultados[$i]["total_piezas_usa"];
	$total_piezas_restantes = $total_piezas_ops - $total_piezas_recibidas;
	
	$nombre_ref = '';
	$nombre_referencia_codificada = utf8_decode($referencia->referencia);
	for($m=0;$m<strlen($nombre_referencia_codificada);$m++){
		if ($nombre_referencia_codificada[$m] == '?') $nombre_ref .= '&#8364;';
		else $nombre_ref .= $nombre_referencia_codificada[$m];
	}

	$ref_prov = '';
	$ref_prov_codificada = utf8_decode($referencia->part_proveedor_referencia);
	for($m=0;$m<strlen($ref_prov_codificada);$m++){
		if ($ref_prov_codificada[$m] == '?') $ref_prov .= '&#8364;';
		else $ref_prov .= $ref_prov_codificada[$m];
	}
	
	$tipo_pieza = '';
	$tipo_pieza_codificada = utf8_decode($referencia->part_tipo);
	for($m=0;$m<strlen($tipo_pieza_codificada);$m++){
		if ($tipo_pieza_codificada[$m] == '?') $tipo_pieza .= '&#8364;';
		else $tipo_pieza .= $tipo_pieza_codificada[$m];
	}
	
	$ref_fab = '';
	$ref_fab_codificada = utf8_decode($referencia->part_fabricante_referencia);
	for($m=0;$m<strlen($ref_fab_codificada);$m++){
		if ($ref_fab_codificada[$m] == '?') $ref_fab .= '&#8364;';
		else $ref_fab .= $ref_fab_codificada[$m];
	}
	
	$descrip = '';
	$descrip_codificada = utf8_decode($referencia->part_descripcion);
	for($m=0;$m<strlen($descrip_codificada);$m++){
		if ($descrip_codificada[$m] == '?') $descrip .= '&#8364;';
		else $descrip .= $descrip_codificada[$m];
	}
	
	$valor_nombre = '';
	$valor_nombre_codificada = utf8_decode($referencia->part_valor_nombre);
	for($m=0;$m<strlen($valor_nombre_codificada);$m++){
		if ($valor_nombre_codificada[$m] == '?') $valor_nombre .= '&#8364;';
		else $valor_nombre .= $valor_nombre_codificada[$m];
	}
	
	$valor_nombre2 = '';
	$valor_nombre2_codificada = utf8_decode($referencia->part_valor_nombre_2);
	for($m=0;$m<strlen($valor_nombre2_codificada);$m++){
		if ($valor_nombre2_codificada[$m] == '?') $valor_nombre2 .= '&#8364;';
		else $valor_nombre2 .= $valor_nombre2_codificada[$m];
	}
	
	$valor_nombre3 = '';
	$valor_nombre3_codificada = utf8_decode($referencia->part_valor_nombre_3);
	for($m=0;$m<strlen($valor_nombre3_codificada);$m++){
		if ($valor_nombre3_codificada[$m] == '?') $valor_nombre3 .= '&#8364;';
		else $valor_nombre3 .= $valor_nombre3_codificada[$m];
	}
	
	$valor_nombre4 = '';
	$valor_nombre4_codificada = utf8_decode($referencia->part_valor_nombre_4);
	for($m=0;$m<strlen($valor_nombre4_codificada);$m++){
		if ($valor_nombre4_codificada[$m] == '?') $valor_nombre4 .= '&#8364;';
		else $valor_nombre4 .= $valor_nombre4_codificada[$m];
	}
	
	$valor_nombre5 = '';
	$valor_nombre5_codificada = utf8_decode($referencia->part_valor_nombre_5);
	for($m=0;$m<strlen($valor_nombre5_codificada);$m++){
		if ($valor_nombre5_codificada[$m] == '?') $valor_nombre5 .= '&#8364;';
		else $valor_nombre5 .= $valor_nombre5_codificada[$m];
	}
	
	$coments = '';
	$coments_codificada = utf8_decode($referencia->comentarios);
	for($m=0;$m<strlen($coments_codificada);$m++){
		if ($coments_codificada[$m] == '?') $coments .= '&#8364;';
		else $coments .= $coments_codificada[$m];
	}
	
	$salida .= '
		<table>
		<tr>
			<td style="text-align: center;">'.$referencia->id_referencia.'</td>
			<td style="text-align: left;">'.$nombre_ref.'</td>
			<td style="text-align: left;">'.$ref_prov.'</td>
			<td style="text-align: left;">'.utf8_decode($referencia->nombre_proveedor).'</td>
			<td style="text-align: right;">'.number_format($total_piezas_ops,2,',','.').'</td>
			<td style="text-align: right;">'.number_format($total_piezas_recibidas,2,',','.').'</td>
			<td style="text-align: right;">'.number_format($total_piezas_restantes,2,',','.').'</td>
			<td style="text-align: right;">'.number_format($total_piezas_usadas,2,',','.').'</td>
			<td style="text-align: right;">'.number_format($precio_referencia,2,',','.').'</td>
			<td style="text-align: right;">'.$total_paquetes.'</td>
			<td style="text-align: left;">'.$tipo_pieza.'</td>
			<td style="text-align: left;">'.utf8_decode($referencia->part_nombre).'</td>
			<td style="text-align: left;">'.utf8_decode($referencia->nombre_fabricante).'</td>
			<td style="text-align: left;">'.$ref_fab.'</td>
			<td style="text-align: left;">'.$descrip.'</td>
			<td style="text-align: left;">'.$valor_nombre.'</td>
			<td style="text-align: left;">'.utf8_decode($referencia->part_valor_cantidad).'</td>
			<td style="text-align: left;">'.$valor_nombre2.'</td>
			<td style="text-align: left;">'.utf8_decode($referencia->part_valor_cantidad_2).'</td>
			<td style="text-align: left;">'.$valor_nombre3.'</td>
			<td style="text-align: left;">'.utf8_decode($referencia->part_valor_cantidad_3).'</td>
			<td style="text-align: left;">'.$valor_nombre4.'</td>
			<td style="text-align: left;">'.utf8_decode($referencia->part_valor_cantidad_4).'</td>
			<td style="text-align: left;">'.$valor_nombre5.'</td>
			<td style="text-align: left;">'.utf8_decode($referencia->part_valor_cantidad_5).'</td>
			<td style="text-align: right;">'.number_format($pack_precio,2,',','.').'</td>
			<td style="text-align: right;">'.utf8_decode($unidades_paquete).'</td>
			<td style="text-align: left;">'.$coments.'</td>
		</tr>
		</table>';
}

$table_end = '</table>';
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=informe_referencias_op_combinadas.xls");
echo $table.$salida.$table_end; 
?>