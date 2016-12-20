<?php
// Este fichero muestra el contenido de las referencias compatibles de la referencia principal
?>

<!-- AÑADIR TABLA REFERENCIAS COMPATIBLES -->
<div class="ContenedorCamposCreacionBasico" id="capa_referencias_compatibles">
    <div class="LabelCreacionBasico">Referencias Compatibles</div>
    <div class="CajaReferencias">
        <div id="ContenedorReferenciasCompatibles" class="ContenedorReferencias">
            <table id="mitablaCompatibles">
                <tr>
                    <!-- <th style="text-align:center;">ID GRUPO</th>-->
                    <!-- <th style="text-align:center;">FECHA GRUPO</th>-->
                    <th style="text-align:center">ID REF</th>
                    <th>NOMBRE</th>
                    <th>PROVEEDOR</th>
                    <th>REF. PROVEEDOR</th>
                    <th>NOMBRE PIEZA</th>
                    <th style="text-align:center">PACK PRECIO</th>
                    <th style="text-align:center">UDS/P</th>
                    <th style="text-align:center">PRECIO UNIDAD</th>
                    <th style="text-align:center">PRECIO</th>
                    <?php if($modificar) { ?>
                        <th style="text-align:center">ELIMINAR</th>
                    <?php } ?>
                </tr>
                <?php
                    $fila_comp = 0;
                    for($i=0;$i<count($res_compatibles);$i++) {
                        $id_grupo = $res_compatibles[$i]["id_grupo"];
                        $fecha_grupo = $user->fechaHoraSpain($res_compatibles[$i]["fecha_creado"]);
                        $id_ref_compatible = $res_compatibles[$i]["id_referencia"];
                        $ref_comp->cargaDatosReferenciaId($id_ref_compatible);

                        if($ref_comp->pack_precio <> 0){
                            $precio_unidad_compatible = $ref_comp->pack_precio / $ref_comp->unidades;
                            $precio_referencia_compatible = $precio_unidad_compatible;
                        }
                        else {
                            $precio_unidad_compatible = 0;
                            $precio_referencia_compatible = 0;
                        } ?>
                        <tr>
                            <!-- <td style="text-align:center; display: none"><?php // echo $id_grupo;?></td>-->
                            <!-- <td style="text-align:center;"><?php // echo $fecha_grupo;?></td>-->
                            <td style="text-align:center;"><?php echo $id_ref_compatible;?></td>
                            <td id="enlaceComposites">
                                <a href="mod_referencia.php?id=<?php echo $id_ref_compatible;?>" target="blank" />
                                <?php
                                    if (strlen($ref_comp->referencia) > $max_caracteres_ref){
                                        echo substr($ref_comp->referencia,0,$max_caracteres_ref).'...';
                                    }
                                    else echo $ref_comp->referencia; ?>
                                </a>
                                <input type="hidden" name="REFS_COMP[]" id="REFS_COMP[]" value="<?php echo $id_ref_compatible;?>" />
                                <input type="hidden" name="REFS_COMP_GRUPO[]" id="REFS_COMP_GRUPO[]" value="<?php echo $id_ref_compatible;?>" />
                            </td>
                            <td><?php echo $ref_comp->nombre_proveedor; ?></td>
                            <td><?php $ref_comp->vincularReferenciaProveedor();?></td>
                            <td><?php echo $ref_comp->part_nombre;?></td>
                            <td style="text-align:center"><?php echo number_format($ref_comp->pack_precio, 2, '.', '');?></td>
                            <td style="text-align:center"><?php echo $ref_comp->unidades; ?></td>
                            <td style="text-align:center"><?php echo number_format($precio_unidad_compatible, 2, '.', '');?></td>
                            <td style="text-align:center"><?php echo number_format($precio_referencia_compatible, 2, '.', ''); ?></td>
                            <?php
                                if($modificar) {
                                    $texto_error_mismos_grupo = "No se puede eliminar una referencia que pertenece al mismo grupo. ";
                                    $texto_error_mismos_grupo .= "Para quitarla del grupo dirijase a la modificación de dicha referencia y pulse QUITAR COMPATIBILIDAD"; ?>
                                    <td style="text-align:center">
                                        <img alt="<?php echo $texto_error_mismos_grupo;?>" title="<?php echo $texto_error_mismos_grupo;?>" src="../images/estrella.png" style="vertical-align: middle;"  />
                                    </td>
                            <?php } ?>
                        </tr>
                        <?php $fila_comp = $fila_comp + 1; ?>
                        <input type="hidden" name="fila_comp" id="fila_comp" value="<?php echo $fila;?>"/>
                <?php
                    }
                ?>
            </table>
        </div>
    </div>
    <?php
        if($modificar) { ?>
            <input type="button" id="mas_comp" name="mas" class="BotonMas"  value="+" onclick="javascript:Abrir_ventana('buscador_referencias_compatibles.php?id_ref=<?php echo $id_referencia;?>')"/>
            <input type="button" id="menos_comp" name="menos" class="BotonMenos" value="-" onclick="javascript:removeRowCompatible(mitablaCompatibles)"/>
            <input type="button" id="quitar_comp" name="quitar" class="BotonQuitarCompatibilidad" value="QUITAR COMPATIBILIDAD" onclick="javascript:quitarCompatibilidad(mitablaCompatibles)"/>
    <?php
        }
    ?>
</div>
<br/>
<br/>
<br/>
