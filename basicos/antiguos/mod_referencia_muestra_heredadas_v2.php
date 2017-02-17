<?php
// Este fichero muestra el contenido de las referencias antecesores de la referencia principal
?>

<!-- AÑADIR TABLA REFERENCIAS HEREDADAS -->
<div class="ContenedorCamposCreacionBasico" id="capa_referencias_heredadas">
    <div class="LabelCreacionBasico">Referencias Heredadas</div>
    <div class="CajaReferencias">
        <div id="ContenedorReferenciasHeredadas" class="ContenedorReferencias">
            <table id="mitablaHeredadas">
            <tr>
                <th style="text-align:center">ID REF</th>
                <th>NOMBRE</th>
                <th>PROVEEDOR</th>
                <th>REF. PROVEEDOR</th>
                <th>NOMBRE PIEZA</th>
                <th style="text-align:center">PIEZAS</th>
                <th style="text-align:center">PACK PRECIO</th>
                <th style="text-align:center">UDS/P</th>
                <th style="text-align:center">PRECIO UNIDAD</th>
                <th style="text-align:center">PRECIO</th>
                <?php if($modificar) { ?>
                    <th style="text-align:center">ELIMINAR</th>
                <?php } ?>
            </tr>
<?php
    $unicos_herederos = $res_heredadas === $res_heredadas_principales;
    // Primero mostramos las heredadas principales
    for($i=0;$i<count($res_heredadas_principales);$i++) {
        $fila = 0;
        $id_ref_heredada = $res_heredadas_principales[$i]["id_ref_heredada"];
        $ref_her->cargaDatosReferenciaId($id_ref_heredada);
        $cantidad_piezas_heredada = $ref_heredada->dameCantidadPiezaHeredada($id_referencia, $id_ref_heredada);

        if($ref_her->pack_precio <> 0) {
            $precio_unidad_heredada = $ref_her->pack_precio / $ref_her->unidades;
            $precio_referencia_heredada = $precio_unidad_heredada * $cantidad_piezas_heredada;
        }
        else {
            $precio_unidad_heredada = 0;
            $precio_referencia_heredada = 0;
        } ?>

        <tr>
            <td style="text-align:center;"><?php echo $id_ref_heredada; ?></td>
            <td id="enlaceComposites">
                <a href="mod_referencia.php?id=<?php echo $id_ref_heredada; ?>" target="blank"/>
                <?php
                    if(strlen($ref_her->referencia) > $max_caracteres_ref) echo substr($ref_her->referencia, 0, $max_caracteres_ref) . '...';
                    else echo $ref_her->referencia; ?>
                </a>
                <input type="hidden" name="REFS[]" id="REFS[]" value="<?php echo $id_ref_heredada; ?>"/>
                <input type="hidden" name="REFS_HEREDADAS_TOTALES[]" id="REFS_HEREDADAS_TOTALES[]" value="<?php echo $id_ref_heredada; ?>"/>
            </td>
            <td><?php echo $ref_her->nombre_proveedor; ?></td>
            <td><?php $ref_her->vincularReferenciaProveedor(); ?></td>
            <td><?php echo $ref_her->part_nombre; ?></td>
            <?php
                if($modificar) { ?>
                    <td style="text-align:center">
                        <input type="text" name="piezas[]" id="piezas[]" class="CampoPiezasInput"
                               value="<?php echo number_format($cantidad_piezas_heredada, 2, '.', ''); ?>"
                               onblur="javascript:validarHayCaracter(<?php echo $fila; ?>)"/>
                    </td>
            <?php
                } else { ?>
                    <td style="text-align:center"><?php echo number_format($cantidad_piezas_heredada, 2, '.', ''); ?></td>
            <?php
                }
            ?>
            <td style="text-align:center"><?php echo number_format($ref_her->pack_precio, 2, '.', ''); ?></td>
            <td style="text-align:center"><?php echo $ref_her->unidades; ?></td>
            <td style="text-align:center"><?php echo number_format($precio_unidad_heredada, 2, '.', ''); ?></td>
            <td style="text-align:center"><?php echo number_format($precio_referencia_heredada, 2, '.', ''); ?></td>
            <?php if ($modificar) { ?>
                    <td style="text-align:center"><input type="checkbox" name="chkbox" value="<?php echo $id_ref_heredada; ?>"/></td>
            <?php } ?>
        </tr>
        <?php $fila = $fila + 1; ?>
<?php
    }
    if(!$unicos_herederos){
        for($i=0;$i<count($res_heredadas);$i++){
            $id_ref_heredada = $res_heredadas[$i]["id_ref_heredada"];
            // Obtenemos las heredadas de una heredada
            $res_heredadas_principales_heredada = $ref_heredada->dameHeredadasPrincipales($id_ref_heredada);
            $ref_heredada->cargaDatosReferenciaId($id_ref_heredada);

            if($res_heredadas_principales_heredada != NULL) {
                for($j = 0; $j < count($res_heredadas_principales_heredada); $j++) {
                    $id_ref_heredada_heredada = $res_heredadas_principales_heredada[$j]["id_ref_heredada"];
                    $ref_her->cargaDatosReferenciaId($id_ref_heredada_heredada);
                    $cantidad_piezas_heredada = $ref_heredada->dameCantidadPiezaHeredada($id_ref_heredada, $id_ref_heredada_heredada);

                    if ($ref_her->pack_precio <> 0) {
                        $precio_unidad_heredada = $ref_her->pack_precio / $ref_her->unidades;
                        $precio_referencia_heredada = $precio_unidad_heredada * $cantidad_piezas_heredada;
                    } else {
                        $precio_unidad_heredada = 0;
                        $precio_referencia_heredada = 0;
                    } ?>

                    <tr style="display: auto;">
                        <td style="text-align:center;"><?php echo $id_ref_heredada_heredada; ?></td>
                        <td id="enlaceComposites">
                            <a href="mod_referencia.php?id=<?php echo $id_ref_heredada_heredada; ?>" target="blank"/>
                            <?php
                                if (strlen($ref_her->referencia) > $max_caracteres_ref) echo substr($ref_her->referencia, 0, $max_caracteres_ref) . '...';
                                else echo $ref_her->referencia; ?>
                            </a>
                            <input type="hidden" name="REFS_HEREDADAS_TOTALES[]" id="REFS_HEREDADAS_TOTALES[]" value="<?php echo $id_ref_heredada_heredada; ?>"/>
                        </td>
                        <td><?php echo $ref_her->nombre_proveedor; ?></td>
                        <td><?php $ref_her->vincularReferenciaProveedor(); ?></td>
                        <td><?php echo $ref_her->part_nombre; ?></td>
                        <?php
                            if($modificar) { ?>
                                <td style="text-align:center"><?php echo number_format($cantidad_piezas_heredada, 2, '.', ''); ?></td>
                        <?php
                            }
                            else { ?>
                                <td style="text-align:center"><?php echo number_format($cantidad_piezas_heredada, 2, '.', ''); ?></td>
                        <?php
                            }
                        ?>
                        <td style="text-align:center"><?php echo number_format($ref_her->pack_precio, 2, '.', ''); ?></td>
                        <td style="text-align:center"><?php echo $ref_her->unidades; ?></td>
                        <td style="text-align:center"><?php echo number_format($precio_unidad_heredada, 2, '.', ''); ?></td>
                        <td style="text-align:center"><?php echo number_format($precio_referencia_heredada, 2, '.', ''); ?></td>
                        <td style="text-align:center"><input type="checkbox" name="chkbox" disabled value="<?php echo $id_ref_heredada_heredada; ?>"/></td>
                    </tr>
            <?php
                }
            }
        }
    }
?>
            </table>
        </div>
    </div>
    <?php
        if($modificar) { ?>
            <input type="button" id="mas" name="mas" class="BotonMas"  value="+" onclick="javascript:Abrir_ventana('buscador_referencias_heredadas.php?id_ref=<?php echo $id_referencia;?>')"/>
            <input type="button" id="menos" name="menos" class="BotonMenos" value="-" onclick="javascript:removeRowHeredada(mitablaHeredadas)"/>
            <input type="button" id="quitar_heredadas" name="quitar" class="BotonQuitarHeredadas" value="QUITAR HEREDADAS" onclick="javascript:quitarHeredadas(mitablaHeredadas)"/>
    <?php
        }
    ?>
</div>
<br/>
<br/>
<br/>
