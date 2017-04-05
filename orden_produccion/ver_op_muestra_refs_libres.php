<?php
// Cargamos las referencias libres de la ficha de la orden de producción
$resultados = $orden_produccion->cargaDatosPorProduccionComponente($id_produccion,0); ?>

<div class="ContenedorCamposCreacionBasico">
    <div class="LabelCreacionBasico">Referencias Libres</div>
    <div class="CajaReferencias">
        <div id="CapaTablaIframe">
            <table>
            <tr>
                <th style="text-align:center">ID_REF</th>
                <th>NOMBRE</th>
                <th>PROVEEDOR</th>
                <th>REF PROV</th>
                <th>NOMBRE PIEZA</th>
                <th style="text-align:center">PIEZAS</th>
                <th style="text-align:center">PACK PRECIO</th>
                <th style="text-align:center">UDS/P</th>
                <th style="text-align:center">TOTAL PAQS</th>
                <th style="text-align:center">PRECIO UNIDAD</th>
                <th style="text-align:center">PRECIO</th>
            </tr>
            <?php
                $precio_componente = 0;
                for($j=0;$j<count($resultados);$j++){
                    $id_referencia = $resultados[$j]["id_referencia"];
                    $uds_paquete = $resultados[$j]["uds_paquete"];
                    $piezas = $resultados[$j]["piezas"];
                    $total_paquetes = $resultados[$j]["total_paquetes"];
                    $pack_precio = $resultados[$j]["pack_precio"];

                    if($pack_precio != 0 and $uds_paquete != 0) $precio_unidad = $pack_precio / $uds_paquete;
                    else $precio_unidad = 0;

                    $precio_referencia = $precio_unidad * $piezas;
                    $precio_componente = $precio_componente + $precio_referencia;
                    $referencia->cargaDatosReferenciaId($id_referencia); ?>

                    <tr>
                        <td style="text-align:center"><?php echo $id_referencia; ?></td>
                        <td>
                        <?php
                            if (strlen($referencia->referencia) > $max_caracteres_ref){
                                echo substr($referencia->referencia,0,$max_caracteres_ref).'...';
                            }
                            else echo $referencia->referencia;
                        ?>
                        </td>
                        <td>
                        <?php
                            if (strlen($referencia->nombre_proveedor) > $max_caracteres){
                                echo substr($referencia->nombre_proveedor,0,$max_caracteres).'...';
                            }
                            else echo $referencia->nombre_proveedor;
                        ?>
                        </td>
                        <td><?php $referencia->vincularReferenciaProveedor(); ?></td>
                        <td>
                        <?php
                            if (strlen($referencia->part_nombre) > $max_caracteres){
                                echo substr($referencia->part_nombre,0,$max_caracteres).'...';
                            }
                            else echo $referencia->part_nombre;
                        ?>
                        </td>
                        <td style="text-align:center"><?php echo number_format($piezas, 2, ',', '.'); ?></td>
                        <td style="text-align:center"><?php echo number_format($pack_precio, 2, ',', '.'); ?></td>
                        <td style="text-align:center"><?php echo number_format($uds_paquete, 2, ',', '.'); ?></td>
                        <td style="text-align:center"><?php echo number_format($total_paquetes, 2, ',', '.'); ?></td>
                        <td style="text-align:center"><?php echo number_format($precio_unidad, 2, ',', '.'); ?></td>
                        <td style="text-align:center"><?php echo number_format($precio_referencia, 2, ',', '.'); ?></td>
                    </tr>
            <?php
                }
                $coste_refs_libres = $precio_componente;
                $coste_producto = $coste_producto + $coste_refs_libres;
            ?>
            </table>
        </div>
    </div>
</div>
<div class="ContenedorCamposCreacionBasico">
    <div class="LabelCreacionBasico">Coste Referencias Libres</div>
    <div class="tituloComponente">
        <table id="tablaTituloPrototipo">
        <tr>
            <td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
                <?php echo '<span class="tituloComp">'.number_format($coste_refs_libres, 2, ',', '.').'€'.'</span>';?>
            </td>
        </tr>
        </table>
    </div>
</div>
<br/>