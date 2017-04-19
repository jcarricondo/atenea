<?php
unset($referencias_componente);
for($i=0;$i<count($referencias_libres);$i++){
$referencias_componente[$i]["id_referencia"] = $referencias_libres[$i];
$referencias_componente[$i]["piezas"] = $piezas[$i];
}
$referencias_componente_her = $ref_heredada->obtenerHeredadas($referencias_componente);
$precio_refs_libres = $ref_heredada->damePrecioReferenciasHeredadas($referencias_componente_her);
$hay_heredadas = count($referencias_componente) != count($referencias_componente_her);
if($hay_heredadas) {
	$color_precio = ' style="color: orange"';
	$hay_alguna_heredada = true;
}
else $color_precio = ' style="color: #2998cc;"'; ?>

<div class="ContenedorCamposCreacionBasico">
	<div class="LabelCreacionBasico">Referencias Libres</div>
	<div class="CajaReferencias">
		<div id="CapaTablaIframe">
			<table id="mitablaRefLibres">
			<tr>
				<th style="text-align:center">ID_REF</th>
				<th>NOMBRE</th>
				<th>PROVEEDOR</th>
				<th>REF. PROVEEDOR</th>
				<th>NOMBRE PIEZA</th>
				<th style="text-align:center">PIEZAS</th>
				<th style="text-align:center">PACK PRECIO</th>
				<th style="text-align:center">UDS/P</th>
				<th style="text-align:center">TOTAL PAQS</th>
				<th style="text-align:center">PRECIO UNIDAD</th>
				<th style="text-align:center">PRECIO</th>
				<th style="text-align:center">ELIMINAR</th>
			</tr>

			<?php
				$precio_refs_libres_tabla = 0;
				$ref_libres = $referencias_libres;
				for($i=0;$i<count($referencias_libres);$i++) {
					// Se cargan los datos de las referencias según su identificador
					echo '<input type="hidden" id="ref_libres[]" name="ref_libres[]" value="'.$ref_libres[$i].'"/>';
					$ref_libre->cargaDatosReferenciaLibreId($referencias_libres[$i]);
					echo '<input type="hidden" id="uds_paquete[]" name="uds_paquete[]" value="'.$ref_libre->cantidad.'"/>';

					$ref->cargaDatosReferenciaId($referencias_libres[$i]);
					$ref->calculaTotalPaquetes($ref->unidades,$piezas[$i]);
					$total_paquetes = $ref->total_paquetes;
					$precio_unidad = ($ref->pack_precio / $ref->unidades);
					$precio_referencia = $precio_unidad * $piezas[$i];
					$precio_refs_libres_tabla = $precio_refs_libres_tabla + $precio_referencia;
					echo '<input type="hidden" id="tot_paquetes[]" name="tot_paquetes[]" value="'.$total_paquetes.'"/>'; ?>

					<tr>
						<td style="text-align:center;"><?php echo $ref->id_referencia; ?></td>
						<td id="enlaceComposites">
							<a href="../basicos/mod_referencia.php?id=<?php echo $ref->id_referencia;?>" target="_blank"/>
							<?php
								if (strlen($ref->referencia) > $max_caracteres_ref) echo substr($ref->referencia,0,$max_caracteres_ref).'...';
								else echo $ref->referencia;
							?>
							</a>
							<input type="hidden" name="REFS_LIBRES[]" id="REFS_LIBRES[]" value="<?php echo $ref->id_referencia;?>" />
						</td>
						<td><?php echo $ref->nombre_proveedor; ?></td>
						<td><?php $ref->vincularReferenciaProveedor(); ?></td>
						<td><?php echo $ref->part_nombre;?></td>
						<td style="text-align:center"><input type="text" name="piezas_ref_libres[]" id="piezas_ref_libres[]" class="CampoPiezasInput" value="<?php echo $piezas[$i];?>" onblur="validarPiezasCorrectasRefsLibres(<?php echo $i;?>)"/></td>
						<td style="text-align:center"><?php echo number_format($ref->pack_precio, 2, '.', '');?></td>
						<td style="text-align:center">
							<?php echo $ref->unidades;?>
							<input type="hidden" id="UDS_REF_LIBRES[]" name="UDS_REF_LIBRES[]" value="<?php echo $ref->unidades;?>"/>
						</td>
						<td style="text-align:center"><?php echo $total_paquetes;?></td>
						<td style="text-align:center"><?php echo number_format($precio_unidad, 2, '.', '');?></td>
						<td style="text-align:center"><?php echo number_format($precio_referencia, 2, '.', '');?></td>
						<td style="text-align:center"><input type="checkbox" name="chkbox" value="<?php echo $ref->id_referencia;?>" /></td>
					</tr>
			<?php
				}
			?>
			</table>
		</div>
	</div>

	<input type="button" id="mas" name="mas" class="BotonMas" value="+" onclick="Abrir_ventana('buscador_referencias_libres_mod_op.php')"/>
	<input type="button" id="menos" name="menos" class="BotonMenos" value="-" onclick="removeRowRefLibres(mitablaRefLibres)"/>
</div>
<br/>

<div class="ContenedorCamposCreacionBasico">
	<div class="LabelCreacionBasico">Coste Refs Libres</div>
	<div class="tituloComponente">
		<table id="tablaTituloPrototipo">
		<tr>
			<td id="coste_ref_libres" style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
			<?php
				echo '<input type="hidden" id="coste_total_refs_libres" name="coste_total_refs_libres" value="'.$precio_refs_libres.'"/>';
				echo '<span id="span_precio_ref_libres" class="tituloComp" '.$color_precio.'>'.number_format($precio_refs_libres, 2, ',', '.').'€'.'</span>';
			?>
			</td>
		</tr>
		</table>
	</div>
</div>


