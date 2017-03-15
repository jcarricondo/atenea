<?php 
// Este fichero genera un excel con las referencias de los componentes de una plantilla
include("../includes/sesion.php");
include("../classes/basicos/plantilla_producto.class.php");
include("../classes/basicos/nombre_producto.class.php");
include("../classes/basicos/componente.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/basicos/referencia_heredada.class.php");
include("../classes/basicos/referencia_compatible.class.php");

$plant = new Plantilla_Producto();
$np = new Nombre_Producto();
$comp = new Componente();
$ref = new Referencia();
$ref_heredada = new Referencia_Heredada();
$ref_compatible = new Referencia_Compatible();

$id_plantilla = $_GET["id_plantilla"];
$plant->cargaDatosPlantillaProductoId($id_plantilla);
$nombre_plantilla = strtoupper($plant->nombre);
$id_nombre_producto = $plant->id_nombre_producto;
$np->cargaDatosNombreProductoId($id_nombre_producto);
$nombre_producto = strtoupper($np->nombre);

$salida = '<table>
                <tr>
                    <th style="text-align: left;">Componente</th>
                    <th style="text-align: left;">Kit</th>
                    <th style="text-align: center;">ID Ref.</th>
                    <th style="text-align: left;">Nombre</th>
                    <th style="text-align: left;">Referencia Proveedor</th>
                    <th style="text-align: left;">Proveedor</th>
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
                    <th style="text-align: right;">Piezas</th>
                    <th style="text-align: left;">Comentarios</th>
                    <th style="text-align: center;">COMPATIBLE</th>
                </tr>';

// Obtenemos los componentes principales de la plantilla
$res_componentes = $plant->dameComponentesPlantillaProducto($id_plantilla);
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

for($i=0;$i<count($array_componentes_final);$i++) {
    $id_componente = $array_componentes_final[$i];
    $comp->cargaDatosComponenteId($id_componente);
    $id_tipo = $comp->id_tipo;
    $nombre_componente = $comp->nombre.'_v'.$comp->version;

    $esComponentePrincipal = $comp->esComponentePrincipal($id_tipo);
    if($esComponentePrincipal) {
        $nombre_componente_principal = $nombre_componente;
        $nombre_subcomponente = '';
    }
    else {
        $nombre_subcomponente = $nombre_componente;
    }

     // Obtenemos las referencias del componente
    $referencias_componente = $comp->dameRefsYPiezasComponente($id_componente);
    for($j=0;$j<count($referencias_componente);$j++){
        $referencias_componente[$j]["id_referencia"] = intval($referencias_componente[$j]["id_referencia"]);
        $referencias_componente[$j]["piezas"] = floatval($referencias_componente[$j]["piezas"]);
    }

    for($j=0;$j<count($referencias_componente);$j++){
        $id_referencia = $referencias_componente[$j]["id_referencia"];
        $piezas = $referencias_componente[$j]["piezas"];

        $ref->cargaDatosReferenciaId($id_referencia);
        $ref->calculaTotalPaquetes($ref->unidades,$piezas);
        $ref->calculaCosteReferencia();
        $ref->prepararCodificacionReferencia();

        $id_grupo = $ref_compatible->dameGrupoReferencia($id_referencia);
        if(!empty($id_grupo)) $es_compatible = "SI";
        else $es_compatible = "NO";

        $salida .= '<tr>
                        <td style="text-align: left;">'.utf8_decode($nombre_componente_principal).'</td>
                        <td style="text-align: left;">'.utf8_decode($nombre_subcomponente).'</td>
                        <td style="text-align: center;">'.$id_referencia.'</td>
                        <td style="text-align: left;">'.$ref->referencia.'</td>
                        <td style="text-align: left;">'.$ref->part_proveedor_referencia.'</td>
                        <td style="text-align: left;">'.$ref->nombre_proveedor.'</td>
                        <td style="text-align: right;">'.number_format(round($ref->coste,2),2,',','.').'</td>
                        <td style="text-align: right;">'.$ref->total_paquetes.'</td>
                        <td style="text-align: left;">'.$ref->part_tipo.'</td>
                        <td style="text-align: left;">'.$ref->part_nombre.'</td>
                        <td style="text-align: left;">'.$ref->nombre_fabricante.'</td>
                        <td style="text-align: left;">'.$ref->part_fabricante_referencia.'</td>
                        <td style="text-align: left;">'.$ref->part_descripcion.'</td>
                        <td style="text-align: left;">'.$ref->part_valor_nombre.'</td>
                        <td style="text-align: left;">'.$ref->part_valor_cantidad.'</td>
                        <td style="text-align: left;">'.$ref->part_valor_nombre_2.'</td>
                        <td style="text-align: left;">'.$ref->part_valor_cantidad_2.'</td>
                        <td style="text-align: left;">'.$ref->part_valor_nombre_3.'</td>
                        <td style="text-align: left;">'.$ref->part_valor_cantidad_3.'</td>
                        <td style="text-align: left;">'.$ref->part_valor_nombre_4.'</td>
                        <td style="text-align: left;">'.$ref->part_valor_cantidad_4.'</td>
                        <td style="text-align: left;">'.$ref->part_valor_nombre_5.'</td>
                        <td style="text-align: left;">'.$ref->part_valor_cantidad_5.'</td>
                        <td style="text-align: right;">'.number_format(round($ref->pack_precio,2),2,',','.').'</td>
                        <td style="text-align: right;">'.$ref->unidades.'</td>
                        <td style="text-align: right;">'.number_format(round($piezas,2),2,',','.').'</td>
                        <td style="text-align: left;">'.$ref->comentarios.'</td>
                        <td style="text-align: center;">'.$es_compatible.'</td>
                    </tr>';
    }
}
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=informeReferenciasCompPlantilla.xls");
echo $salida;
?>