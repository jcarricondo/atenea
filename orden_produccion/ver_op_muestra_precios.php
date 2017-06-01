<?php
// Anteriormente calculamos el coste total del producto y después el coste total de la Orden de Producción en función del precio unitario de cada referencia
// Ahora mostraremos los precios reales que coincide con los excel de la Orden de Producción.
// Es decir, mostraremos el precio total de la Orden de Producción en función del total paquetes utilizado y no el del precio unitario de la referencia
// Para ello obtenemos los costes de las referencias de las Ordenes de Compra

$coste_total_produccion = 0;
$coste_total_producto = 0;
$resultados = $orden_produccion->dameOCReferenciasPorProduccion($id_produccion);

for($i=0;$i<count($resultados);$i++){
    $coste_referencia_oc = $resultados[$i]["coste"];
    $coste_total_produccion = $coste_total_produccion + $coste_referencia_oc;
}

// Redondeamos el coste total de la Orden de Produccion
$coste_total_produccion = round($coste_total_produccion,2);

// Obtenemos el coste total del producto en funcion de las unidades de la Orden de Produccion
$coste_total_producto = round(($coste_total_produccion / $unidades),2); ?>

<div class="ContenedorCamposCreacionBasico">
    <div class="LabelCreacionBasico">Coste Total Producto</div>
    <div class="tituloComponente">
        <table id="tablaTituloPrototipo">
        <tr>
            <td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;"><span class="tituloComp"><?php echo number_format($coste_total_producto, 2, ',', '.').'€';?></span></td>
        </tr>
        </table>
    </div>
</div>

<div class="ContenedorCamposCreacionBasico">
    <div class="LabelCreacionBasico">Coste Total Orden de Producción</div>
    <div class="tituloComponente">
        <table id="tablaTituloPrototipo">
        <tr>
            <td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
            <?php
                $coste_produccion = $coste_producto * $unidades; ?>
                <span class="tituloComp"><?php echo number_format($coste_total_produccion, 2, ',', '.').'€';?></span>
            </td>
        </tr>
        </table>
    </div>
</div>
<br />