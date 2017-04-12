<?php
// Este fichero muestra las referencias de los kits en la confirmación de la creación de una Orden de Producción
$orden_produccion->dameIdsKitComponente($ids_perifericos[$i]);
$costeKits = 0;
for($k=0; $k<count($orden_produccion->ids_kit); $k++) {
    $kit->cargaDatosKitId($orden_produccion->ids_kit[$k]["id_kit"]); ?>
    <div class="ContenedorCamposCreacionBasico">
        <div class="LabelCreacionBasico">Referencias Kit</div>
        <div class="tituloComponente">
            <table id="tablaTituloPrototipo">
            <tr>
                <td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
                    <span class="tituloComp"><?php echo $kit->kit;?></span>
                </td>
            </tr>
            </table>
        </div>
        <div class="CajaReferencias">
            <div id="CapaTablaIframe">
                <table id="mitablaKit<?php echo $k; ?>Per<?php echo $i; ?>">
                <?php
                    $listado_ref_comp->setValores($orden_produccion->ids_kit[$k]["id_kit"]);
                    $listado_ref_comp->realizarConsulta();
                    $resultadosReferenciasKit = $listado_ref_comp->referencias_componentes;
                ?>
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
                    $precio_kit = 0;
                    for($j=0; $j<count($resultadosReferenciasKit); $j++) {
                        $datoRef_Kit = $resultadosReferenciasKit[$j];
                        $ref_comp->cargaDatosReferenciaComponenteId($datoRef_Kit["id"]);
                        $ref->cargaDatosReferenciaId($ref_comp->id_referencia);

                        if ($ref->pack_precio <> 0 and $ref->unidades <> 0) $precio_unidad = ($ref->pack_precio / $ref->unidades);
                        else $precio_unidad = 00;

                        $ref->calculaTotalPaquetes($ref->unidades, $ref_comp->piezas);
                        $total_paquetes = $ref->total_paquetes;
                        $precio_referencia = $ref_comp->piezas * $precio_unidad; ?>

                        <tr>
                            <td style="text-align:center;"><?php echo $ref_comp->id_referencia;?></td>
                            <td id="enlaceComposites">
                                <a href="../basicos/mod_referencia.php?id=' . $ref_comp->id_referencia . '"/>
                                <?php
                                    if (strlen($ref->referencia) > $max_caracteres_ref) echo substr($ref->referencia, 0, $max_caracteres_ref) . '...';
                                    else echo $ref->referencia; ?>
                                </a>
                            </td>
                            <td>
                            <?php
                                if (strlen($ref->nombre_proveedor) > $max_caracteres) echo substr($ref->nombre_proveedor, 0, $max_caracteres) . '...';
                                else echo $ref->nombre_proveedor; ?>
                            </td>
                            <td><?php $ref->vincularReferenciaProveedor(); ?></td>
                            <td>
                            <?php
                                if (strlen($ref->part_nombre) > $max_caracteres) echo substr($ref->part_nombre, 0, $max_caracteres) . '...';
                                else echo $ref->part_nombre; ?>
                            </td>
                            <td style="text-align:center"><?php echo number_format($ref_comp->piezas, 2, ',', '.');?></td>
                            <td style="text-align:center"><?php echo number_format($ref->pack_precio, 2, ',', '.');?></td>
                            <td style="text-align:center"><?php echo $ref->unidades;?></td>
                            <td style="text-align:center"><?php echo $total_paquetes;?></td>
                            <td style="text-align:center"><?php echo number_format($precio_unidad, 2, ',', '.');?></td>
                            <td style="text-align:center"><?php echo number_format($precio_referencia, 2, ',', '.')?></td>
                        </tr>
                    <?php
                        $precio_kit = $precio_kit + $precio_referencia;
                        $costeKits = $costeKits + $precio_referencia;
                    }
                ?>
                </table>
            </div>
        </div>
    </div>

    <div class="ContenedorCamposCreacionBasico">
        <div class="LabelCreacionBasico">Coste Kit Periferico</div>
        <div class="tituloComponente">
            <table id="tablaTituloPrototipo">
            <tr>
                <td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
                    <span class="tituloComp"><?php echo number_format($precio_kit, 2, ',', '.').'€';?></span>
                </td>
            </tr>
            </table>
        </div>
    </div>
    <br/>
<?php
}
?>

