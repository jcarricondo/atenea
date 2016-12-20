<?php
// Este fichero muestra el contenido de las referencias antecesores de la referencia principal
?>

<div class="ContenedorCamposCreacionBasico" id="capa_referencias_antecesores">
	<div class="LabelCreacionBasico">Hereda de</div>
	<div class="CajaReferencias">
		<div id="ContenedorReferenciasAntecesores" class="ContenedorReferencias">
    		<table id="mitablaAntecesores">
			<tr>
				<!-- <th style="text-align: center">HEREDA DE</th> -->
				<th style="text-align:center">ID REF</th>
				<th>NOMBRE</th>
				<th>PROVEEDOR</th>
				<th>REF. PROVEEDOR</th>
				<th>NOMBRE PIEZA</th>
				<th style="text-align: center">PIEZAS</th>
				<th style="text-align:center">PACK PRECIO</th>
				<th style="text-align:center">UDS/P</th>
				<th style="text-align:center">PRECIO UNIDAD</th>
				<th style="text-align:center">PRECIO</th>
			</tr>

<?php
    $unicos_antecesores = $res_antecesores === $res_antecesores_principales;
    // Primero mostramos los antecesores principales y sus piezas
    for($i=0;$i<count($res_antecesores_principales);$i++){
        $id_ref_antecesor = $res_antecesores_principales[$i]["id_referencia"];
        $id_ref_antecesor_antecesor = "-";
        $piezas_antecesor = "-";
        $precio_referencia_antecesor = "-";

        $ref_ant->cargaDatosReferenciaId($id_ref_antecesor);
        if ($ref_ant->pack_precio <> 0) $precio_unidad_antecesor = $ref_ant->pack_precio / $ref_ant->unidades;
        else $precio_unidad_antecesor = 0; ?>

        <tr style="background: #eee;">
            <!-- <td style="text-align: center;"><?php // echo $id_ref_antecesor_antecesor; ?></td> -->
            <td style="text-align:center;">
                <?php echo $id_ref_antecesor; ?>
                <input type="hidden" name="REFS_ANCESTRO[]" id="REFS_ANCESTRO[]" value="<?php echo $id_ref_antecesor; ?>"/>
            </td>
            <td>
                <a href="mod_referencia.php?id=<?php echo $id_ref_antecesor; ?>" target="blank"/>
                <?php
                    if (strlen($ref_ant->referencia) > $max_caracteres_ref) echo substr($ref_ant->referencia, 0, $max_caracteres_ref) . '...';
                    else echo $ref_ant->referencia; ?>
                </a>
            </td>
            <td><?php echo $ref_ant->nombre_proveedor; ?></td>
            <td><?php echo $ref_ant->part_proveedor_referencia; ?></td>
            <td><?php echo $ref_ant->part_nombre; ?></td>
            <td style="text-align: center"><?php echo $piezas_antecesor; ?></td>
            <td style="text-align:center"><?php echo number_format($ref_ant->pack_precio, 2, '.', ''); ?></td>
            <td style="text-align:center"><?php echo $ref_ant->unidades; ?></td>
            <td style="text-align:center"><?php echo number_format($precio_unidad_antecesor, 2, '.', ''); ?></td>
            <td style="text-align:center"><?php echo $precio_referencia_antecesor; ?></td>
        </tr>
<?php
    }
    if(!$unicos_antecesores){
        for($i=0;$i<count($res_antecesores);$i++){
            $id_ref_antecesor = $res_antecesores[$i]["id_referencia"];
            // Obtenemos los antecesores de un antecesores
            $res_antecesores_principales_antecesor = $ref_heredada->dameAntecesoresPrincipales($id_ref_antecesor);
            $ref_ant->cargaDatosReferenciaId($id_ref_antecesor);

            if($res_antecesores_principales_antecesor != NULL) {
                for($j=0;$j< count($res_antecesores_principales_antecesor);$j++) {
                    $id_ref_antecesor_antecesor = $res_antecesores_principales_antecesor[$j]["id_referencia"];
                    // Obtenemos las piezas de herencia de la referencia antecesor respecto a su antecesor
                    $piezas_antecesor = $ref_heredada->dameCantidadPiezaHeredada($id_ref_antecesor_antecesor,$id_ref_antecesor);
                    if ($ref_ant->pack_precio <> 0) {
                        $precio_unidad_antecesor = $ref_ant->pack_precio / $ref_ant->unidades;
                        $precio_referencia_antecesor = $precio_unidad_antecesor * $piezas_antecesor;
                    } else {
                        $precio_unidad_antecesor = 0;
                        $precio_referencia_antecesor = 0;
                    } ?>

                    <tr style="background: #eee; display: none;">
                        <td style="text-align: center;"><?php echo $id_ref_antecesor_antecesor; ?></td>
                        <td style="text-align:center;">
                            <?php echo $id_ref_antecesor; ?>
                            <input type="hidden" name="REFS_ANCESTRO[]" id="REFS_ANCESTRO[]" value="<?php echo $id_ref_antecesor; ?>"/>
                        </td>
                        <td>
                            <a href="mod_referencia.php?id=<?php echo $id_ref_antecesor; ?>" target="blank"/>
                            <?php
                            if (strlen($ref_ant->referencia) > $max_caracteres_ref) echo substr($ref_ant->referencia, 0, $max_caracteres_ref) . '...';
                            else echo $ref_ant->referencia; ?>
                            </a>
                        </td>
                        <td><?php echo $ref_ant->nombre_proveedor; ?></td>
                        <td><?php echo $ref_ant->part_proveedor_referencia; ?></td>
                        <td><?php echo $ref_ant->part_nombre; ?></td>
                        <td style="text-align: center"><?php echo $piezas_antecesor; ?></td>
                        <td style="text-align:center"><?php echo number_format($ref_ant->pack_precio, 2, '.', ''); ?></td>
                        <td style="text-align:center"><?php echo $ref_ant->unidades; ?></td>
                        <td style="text-align:center"><?php echo number_format($precio_unidad_antecesor, 2, '.', ''); ?></td>
                        <td style="text-align:center"><?php echo number_format($precio_referencia_antecesor, 2, '.', ''); ?></td>
                    </tr>
            <?php
                }
            }
            else {
                $id_ref_antecesor_antecesor = "-";
                $piezas_antecesor = "-";
                $precio_referencia_antecesor = "-";
                if ($ref_ant->pack_precio <> 0) $precio_unidad_antecesor = $ref_ant->pack_precio / $ref_ant->unidades;
                else $precio_unidad_antecesor = 0; ?>

                <tr style="background: #eee; display: none;">
                    <td style="text-align: center;"><?php echo $id_ref_antecesor_antecesor; ?></td>
                    <td style="text-align:center;">
                        <?php echo $id_ref_antecesor; ?>
                        <input type="hidden" name="REFS_ANCESTRO[]" id="REFS_ANCESTRO[]" value="<?php echo $id_ref_antecesor; ?>"/>
                    </td>
                    <td>
                        <a href="mod_referencia.php?id=<?php echo $id_ref_antecesor; ?>" target="blank"/>
                        <?php
                        if (strlen($ref_ant->referencia) > $max_caracteres_ref) echo substr($ref_ant->referencia, 0, $max_caracteres_ref) . '...';
                        else echo $ref_ant->referencia; ?>
                        </a>
                    </td>
                    <td><?php echo $ref_ant->nombre_proveedor; ?></td>
                    <td><?php echo $ref_ant->part_proveedor_referencia; ?></td>
                    <td><?php echo $ref_ant->part_nombre; ?></td>
                    <td style="text-align: center"><?php echo $piezas_antecesor; ?></td>
                    <td style="text-align:center"><?php echo number_format($ref_ant->pack_precio, 2, '.', ''); ?></td>
                    <td style="text-align:center"><?php echo $ref_ant->unidades; ?></td>
                    <td style="text-align:center"><?php echo number_format($precio_unidad_antecesor, 2, '.', ''); ?></td>
                    <td style="text-align:center"><?php echo $precio_referencia_antecesor; ?></td>
                </tr>
    <?php
            }
        }
    }
?>
            </table>
        </div>
    </div>
</div>
<br/>
<br/>
