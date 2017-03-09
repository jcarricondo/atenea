<?php
// Este fichero muestra el contenido de las referencias antecesores de la referencia principal
?>
<div class="ContenedorCamposCreacionBasico" id="capa_referencias_antecesores">
	<div class="LabelCreacionBasico">Hereda de</div>
	<div class="CajaReferencias">
		<div id="ContenedorReferenciasAntecesores" class="ContenedorReferencias">
    		<table id="mitablaAntecesores">
			<tr>
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
                <?php if($modificar) { ?>
                    <th style="text-align:center">ELIMINAR</th>
                <?php } ?>
			</tr>

<?php
    $fila_antecesor = 0;
    for($i=0;$i<count($res_antecesores_principales);$i++){
        $id_ref_antecesor = $res_antecesores_principales[$i]["id_referencia"];
		$muestro_antecesor = "background: #eee; display: auto;";

		$id_ref_antecesor_antecesor = "-";
		$piezas_antecesor = "-";
		$precio_referencia_antecesor = "-";

		$ref_ant->cargaDatosReferenciaId($id_ref_antecesor);
		if ($ref_ant->pack_precio <> 0) $precio_unidad_antecesor = $ref_ant->pack_precio / $ref_ant->unidades;
		else $precio_unidad_antecesor = 0; ?>

		<tr style="<?php echo $muestro_antecesor; ?>">
			<td style="text-align:center;"><?php echo $id_ref_antecesor;?></td>
			<td>
				<a href="mod_referencia.php?id=<?php echo $id_ref_antecesor; ?>" target="blank"/>
				<?php
					if (strlen($ref_ant->referencia) > $max_caracteres_ref) echo substr($ref_ant->referencia, 0, $max_caracteres_ref) . '...';
					else echo $ref_ant->referencia; ?>
				</a>
				<input type="hidden" name="REFS_ANT[]" id="REFS_ANT[]" value="<?php echo $id_ref_antecesor;?>" />
			</td>
			<td><?php echo $ref_ant->nombre_proveedor; ?></td>
			<td><?php echo $ref_ant->part_proveedor_referencia; ?></td>
			<td><?php echo $ref_ant->part_nombre; ?></td>
			<td style="text-align: center"><?php echo $piezas_antecesor; ?></td>
			<td style="text-align:center"><?php echo number_format($ref_ant->pack_precio, 2, '.', ''); ?></td>
			<td style="text-align:center"><?php echo $ref_ant->unidades; ?></td>
			<td style="text-align:center"><?php echo number_format($precio_unidad_antecesor, 2, '.', ''); ?></td>
			<td style="text-align:center"><?php echo $precio_referencia_antecesor; ?></td>
			<?php if($modificar) { ?>
				<td style="text-align:center"><input type="checkbox" name="chkbox" value="<?php echo $id_ref_antecesor;?>" /></td>
			<?php } ?>
		</tr>
		<?php $fila_antecesor = $fila_antecesor + 1; ?>
		<input type="hidden" name="fila_antecesor" id="fila_antecesor" value="<?php echo $fila_antecesor;?>"/>
<?php
	}
?>
            </table>
        </div>
    </div>
	<?php
		if($modificar) { ?>
			<input type="button" id="menos_antecesor" name="menos_antecesor" class="BotonMenos" value="-" onclick="javascript:removeRowAntecesor(mitablaAntecesores)"/>
			<input type="button" id="quitar_antecesores" name="quitar_antecesores" class="BotonQuitarAntecesores" value="QUITAR ANTECESORES" onclick="javascript:quitarAntecesores(mitablaAntecesores)"/>
	<?php
		}
	?>
</div>
<div id="capa_input_referencias_antecesores_totales" style="display: none">
<?php
	// Guardamos en un array oculto todas las referencias antecesores de la referencia
	for($i=0;$i<count($res_antecesores);$i++){
		$id_ref_antecesor = $res_antecesores[$i]["id_referencia"];?>
		<input type="hidden" name="REFS_ANTECESORES_TOTALES[]" id="REFS_ANTECESORES_TOTALES[]" value="<?php echo $id_ref_antecesor;?>" />
<?php
	}
?>
</div>
<br/>
<br/>
<br/>