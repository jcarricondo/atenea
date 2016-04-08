<?php 
// Este fichero genera un excel con con las referencias de los componentes de la plantilla
set_time_limit(10000);
include("../classes/mysql.class.php");
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
// Guardamos en un array los componentes con sus interfaces y kits asociados
for($i=0;$i<count($res_componentes);$i++){
    $id_componente = $res_componentes[$i]["id_componente"];
    $id_tipo_componente = $res_componentes[$i]["id_tipo_componente"];

    $array_componentes_final[] = $id_componente;

    // Comprobamos si ese componente tiene interfaces asociadas
    $interfaces_comp = $comp->dameInterfacesComponente($id_componente);
    for($j=0;$j<count($interfaces_comp);$j++){
        $id_interfaz = $interfaces_comp[$j]["id_interfaz"];
        $array_componentes_final[] = $id_interfaz;
    }

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

    if($referencias_componente_final != NULL){
        // Agrupamos las referencias
        $referencias_componente_final = $comp->agruparReferenciasComponentes($referencias_componente,$referencias_componente_final);
    }
    else {
        $referencias_componente_final = $referencias_componente;
    }
}

if(!empty($referencias_componente_final)) {
    // Ordenamos el array de referencias
    array_multisort($referencias_componente_final);
}

for($i=0;$i<count($referencias_componente_final);$i++){
    $id_referencia = $referencias_componente_final[$i]["id_referencia"];
    $piezas = $referencias_componente_final[$i]["piezas"];
    $ref->cargaDatosReferenciaId($id_referencia);

    // Calculamos el numero total de paquetes en funcion de las piezas
    $ref->calculaTotalPaquetes($ref->unidades,$piezas);
    $ref->calculaCosteReferencia();
    $ref->prepararCodificacionReferencia();

    $salida .= '<tr>
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
$salida .= '</table>';
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=informeReferenciasPlantilla.xls");
echo $salida;
?>