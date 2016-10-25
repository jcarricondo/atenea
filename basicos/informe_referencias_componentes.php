<?php 
// Este fichero genera un excel con las referencias de los componentes de una plantilla
include("../includes/sesion.php");
include("../classes/basicos/plantilla_producto.class.php");
include("../classes/basicos/nombre_producto.class.php");
include("../classes/basicos/componente.class.php");
include("../classes/basicos/referencia.class.php");

$plant = new Plantilla_Producto();
$np = new Nombre_Producto();
$comp = new Componente();
$ref = new Referencia();

$id_plantilla = $_GET["id_plantilla"];
$plant->cargaDatosPlantillaProductoId($id_plantilla);
$nombre_plantilla = strtoupper($plant->nombre);
$id_nombre_producto = $plant->id_nombre_producto;
$np->cargaDatosNombreProductoId($id_nombre_producto);
$nombre_producto = strtoupper($np->nombre);

$salida = '<table>
                <tr>
                    <th>Componente</th>
                    <th>Kit</th>
                    <th style="text-align: center;">ID Ref.</th>
                    <th>Nombre</th>
                    <th>Referencia Proveedor</th>
                    <th>Proveedor</th>
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
                    <th>Precio Pack</th>
                    <th>Unidades Paquete</th>
                    <th>Piezas</th>
                    <th>Comentarios</th>
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

        $salida .= '<tr>
                        <td>'.utf8_decode($nombre_componente_principal).'</td>
                        <td>'.utf8_decode($nombre_subcomponente).'</td>
                        <td style="text-align: center;">'.$id_referencia.'</td>
                        <td>'.$ref->referencia.'</td>
                        <td>'.$ref->part_proveedor_referencia.'</td>
                        <td>'.$ref->nombre_proveedor.'</td>
                        <td style="text-align: right;">'.number_format(round($ref->coste,2),2,',','.').'</td>
                        <td style="text-align: right;">'.$ref->total_paquetes.'</td>
                        <td>'.$ref->part_tipo.'</td>
                        <td>'.$ref->part_nombre.'</td>
                        <td>'.$ref->nombre_fabricante.'</td>
                        <td>'.$ref->part_fabricante_referencia.'</td>
                        <td>'.$ref->part_descripcion.'</td>
                        <td>'.$ref->part_valor_nombre.'</td>
                        <td>'.$ref->part_valor_cantidad.'</td>
                        <td>'.$ref->part_valor_nombre_2.'</td>
                        <td>'.$ref->part_valor_cantidad_2.'</td>
                        <td>'.$ref->part_valor_nombre_3.'</td>
                        <td>'.$ref->part_valor_cantidad_3.'</td>
                        <td>'.$ref->part_valor_nombre_4.'</td>
                        <td>'.$ref->part_valor_cantidad_4.'</td>
                        <td>'.$ref->part_valor_nombre_5.'</td>
                        <td>'.$ref->part_valor_cantidad_5.'</td>
                        <td style="text-align: right;">'.number_format(round($ref->pack_precio,2),2,',','.').'</td>
                        <td style="text-align: right;">'.$ref->unidades.'</td>
                        <td style="text-align: right;">'.number_format(round($piezas,2),2,',','.').'</td>
                        <td>'.$ref->comentarios.'</td>
                    </tr>';
    }
}
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=informeReferenciasCompPlantilla.xls");
echo $salida;
?>