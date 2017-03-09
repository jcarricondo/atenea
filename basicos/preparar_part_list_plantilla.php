<?php 
// Este fichero genera un excel con con las referencias de los componentes de la plantilla
$salida = "";

$id_nombre_producto = $plant->id_nombre_producto;
$np->cargaDatosNombreProductoId($id_nombre_producto);
$nombre_producto = strtoupper($np->nombre);

$salida = '<table>
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
            <th style="text-align: left;">Tipo Pieza</th>
            <th style="text-align: left;">Nombre Pieza</th>
            <th style="text-align: left;">Fabricante</th>
            <th style="text-align: left;">Referencia Fabricante</th>
            <th style="text-align: left;">Descripci&oacute;n</th>
            <th style="text-align: left;">Nombre</th>
            <th style="text-align: right;">Valor</th>
            <th style="text-align: left;">Nombre 2</th>
            <th style="text-align: right;">Valor 2</th>
            <th style="text-align: left;">Nombre 3</th>
            <th style="text-align: right;">Valor 3</th>
            <th style="text-align: left;">Nombre 4</th>
            <th style="text-align: right;">Valor 4</th>
            <th style="text-align: left;">Nombre 5</th>
            <th style="text-align: right;">Valor 5</th>
            <th style="text-align: left;">Comentarios</th>
            <th style="text-align: center;">COMPATIBLE</th>
        </tr>';

// Obtenemos los componentes principales de la plantilla
$res_componentes = $plant->dameComponentesPlantillaProducto($id);
// Guardamos en un array los componentes con sus kits asociados
for($i=0;$i<count($res_componentes);$i++){
    $id_componente = $res_componentes[$i]["id_componente"];
    $id_tipo_componente = $res_componentes[$i]["id_tipo_componente"];

    $array_componentes_final[] = $id_componente;

    // Comprobamos si ese componente tiene kits asociados
    $kits_comp = $comp->dameKitsComponente($id_componente);
    for($j=0;$j<count($kits_comp);$j++){
        $id_kit = $kits_comp[$j]["id_kit"];
        $array_componentes_final[] = $id_kit;
    }
}

for($i=0;$i<count($array_componentes_final);$i++){
    $id_componente = $array_componentes_final[$i];

    // Obtenemos las referencias del componente
    $referencias_componente = $comp->dameRefsYPiezasComponente($id_componente);
    // Convertimos el string de numeros en integer para los id_ref y en float para las piezas
    for($j=0;$j<count($referencias_componente);$j++){
        $referencias_componente[$j]["id_referencia"] = intval($referencias_componente[$j]["id_referencia"]);
        $referencias_componente[$j]["piezas"] = floatval($referencias_componente[$j]["piezas"]);
    }

    if(!empty($referencias_componente_final))$referencias_componente_final = $comp->agruparReferenciasComponentes($referencias_componente,$referencias_componente_final);
    else $referencias_componente_final = $referencias_componente;
}

if(!empty($referencias_componente_final)) {
    // Ordenamos el array de referencias
    array_multisort($referencias_componente_final);
}

for($i=0;$i<count($referencias_componente_final);$i++){
    $id_referencia = $referencias_componente_final[$i]["id_referencia"];
    $piezas = $referencias_componente_final[$i]["piezas"];
    $ref->cargaDatosReferenciaId($id_referencia);
    $unidades_por_paquete = $ref->unidades;
    $unidades_por_simulador = $piezas;
    $precio_por_paquete = $ref->pack_precio;
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

    $id_motivo_compatibilidad = $ref->dameIdMotivoCompatibilidad($id_referencia);
    if($id_motivo_compatibilidad == "1") $es_compatible = "NO";
    else $es_compatible = "SI";

    $ref->prepararCodificacionReferencia();
    $salida .= '<tr>
                <td style="text-align: center;">'.$id_referencia.'</td>
                <td style="text-align: left;">'.$ref->referencia.'</td>
                <td style="text-align: left;">'.$ref->part_proveedor_referencia.'</td>
                <td style="text-align: left;">'.$ref->nombre_proveedor.'</td>
                <td style="text-align: right;">'.number_format($unidades_por_paquete,2,',','.').'</td>
                <td style="text-align: right;">'.number_format($unidades_por_simulador,2,',','.').'</td>
                <td style="text-align: right;">'.number_format($paquetes_por_simulador,2,',','.').'</td>
			    <td style="text-align: right;">'.number_format($precio_por_paquete,2,',','.').'</td>
			    <td style="text-align: right;">'.number_format($precio_por_unidad,2,',','.').'</td>
			    <td style="text-align: right;">'.number_format($precio_por_simulador_unidades,2,',','.').'</td>
			    <td style="text-align: right;">'.number_format($precio_por_simulador_paquetes,2,',','.').'</td>
                <td style="text-align: left;">'.$ref->part_tipo.'</td>
                <td style="text-align: left;">'.$ref->part_nombre.'</td>
                <td style="text-align: left;">'.$ref->nombre_fabricante.'</td>
                <td style="text-align: left;">'.$ref->part_fabricante_referencia.'</td>
                <td style="text-align: left;">'.$ref->part_descripcion.'</td>
                <td style="text-align: left;">'.$ref->part_valor_nombre.'</td>
                <td style="text-align: right;">'.$ref->part_valor_cantidad.'</td>
                <td style="text-align: left;">'.$ref->part_valor_nombre_2.'</td>
                <td style="text-align: right;">'.$ref->part_valor_cantidad_2.'</td>
                <td style="text-align: left;">'.$ref->part_valor_nombre_3.'</td>
                <td style="text-align: right;">'.$ref->part_valor_cantidad_3.'</td>
                <td style="text-align: left;">'.$ref->part_valor_nombre_4.'</td>
                <td style="text-align: right;">'.$ref->part_valor_cantidad_4.'</td>
                <td style="text-align: left;">'.$ref->part_valor_nombre_5.'</td>
                <td style="text-align: right;">'.$ref->part_valor_cantidad_5.'</td>
                <td style="text-align: left;">'.$ref->comentarios.'</td>
                <td style="text-align: center;">'.$es_compatible.'</td>
            </tr>';
}
$salida .= '</table>';
$partlist = $dir_documentacion_plantilla.$barra_directorio."PARTLIST.xls";
file_put_contents($partlist,$salida);
?>
