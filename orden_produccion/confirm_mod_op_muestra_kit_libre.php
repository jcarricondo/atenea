<?php
// Este fichero carga un kit libre en el proceso de confirmación de modificación de una Orden de Producción
$max_caracteres_ref = 50;
$max_caracteres = 25;
$ref_kits->setValores($id_componente);
$ref_kits->realizarConsulta();
$resultadosBusqueda = $ref_kits->referencias_componentes;
$precio_kit_libre = 0;
?>

<div class="ContenedorCamposCreacionBasico">
	<div class="LabelCreacionBasico">Referencias Kit Libre</div>
	<div class="tituloComponente">
		<table id="tablaTituloPrototipo">
		<tr>
			<td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;"><span class="tituloComp"><?php echo $kit->kit.'_v'.$kit->version;?></span></td>
			<td style="text-align:left; background:#fff; vertical-align:top; padding:0px 5px 0px 5px;">
			<?php
				if ($kit->prototipo == 1) { ?>
					<span class="ImagenPrototipo"><img src="../images/prototipo.jpg" width="20px" height="20px" alt="PROTOTIPO" title="PROTOTIPO"></span>
			<?php
				} else if ($kit->prototipo == 0) { ?>
					<span class="ImagenPrototipo"><img src="../images/engranaje.gif" width="20px" height="20px" alt="PRODUCCION" title="PRODUCCION"></span>
			<?php
				}
			?>
			</td>
		</tr>
		</table>
	</div>
	<div class="CajaReferencias">
		<div id="CapaTablaIframe">
			<table id="mitabla_kit_libre_<?php echo $i;?>">
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
				for($j=0;$j<count($resultadosBusqueda);$j++) {
					$datoRef_Kit = $resultadosBusqueda[$j];
					$ref_comp->cargaDatosReferenciaComponenteId($datoRef_Kit["id"]);
					$ref->cargaDatosReferenciaId($ref_comp->id_referencia);
					$ref_comp->calculaTotalPaquetes($ref_comp->uds_paquete, $ref_comp->piezas);
					$total_paquetes = $ref_comp->total_paquetes;
			
					if($ref->pack_precio <> 0 and $ref->unidades <> 0) $precio_unidad = $ref->pack_precio / $ref->unidades;
					else $precio_unidad = 00;

					$precio_referencia = $ref_comp->piezas * $precio_unidad;
					$precio_kit_libre = $precio_kit_libre + $precio_referencia; ?>

					<tr>
						<td style="text-align:center"><?php echo $ref_comp->id_referencia; ?></td>
						<td id="enlaceComposites">
							<a href="../basicos/mod_referencia.php?id=<?php echo $ref_comp->id_referencia;?>" target="_blank"/>
							<?php
								if (strlen($ref->referencia) > $max_caracteres_ref) echo substr($ref->referencia,0,$max_caracteres_ref).'...';
								else echo $ref->referencia;
							?>
							</a>
							<input type="hidden" name="REFS_KIT_LIBRE_<?php echo $i;?>[]" id="REFS_KIT_LIBRE_<?php echo $i;?>[]" value="<?php echo $ref_comp->id_referencia;?>" />
						</td>
						<td><?php echo $ref->nombre_proveedor;?></td>
						<td><?php $ref->vincularReferenciaProveedor(); ?></td>
						<td><?php echo $ref->part_nombre;?></td>
						<td style="text-align:center">
							<?php echo number_format($ref_comp->piezas, 2, '.', '');?>
							<input type="hidden" name="piezas_kits_libres_<?php echo $i;?>[]" id="piezas_kits_libres_<?php echo $i;?>[]" value="<?php echo $ref_comp->piezas;?>" />
						</td>
						<td style="text-align:center"><?php echo number_format($ref->pack_precio, 2, '.', '');?></td>
						<td style="text-align:center">
							<?php echo $ref->unidades;?>
							<input type="hidden" name="UDS_KIT_LIBRE_<?php echo $i;?>[]" id="UDS_KIT_LIBRE_<?php echo $i;?>[]" value="<?php echo $ref->unidades;?>"/>
						</td>
						<td style="text-align:center"><?php echo $total_paquetes;?></td>
						<td style="text-align:center"><?php echo number_format($precio_unidad, 2, '.', '');?></td>
						<td style="text-align:center"><?php echo number_format($precio_referencia, 2, '.', '');?></td>
					</tr>
			<?php
				}
			?>
			</table>
		</div>
	</div>
	<input type="hidden" id="kit_libre<?php echo $i;?>" name="kit_libre<?php echo $i;?>" value="<?php echo $i;?>" />
	<br/>
</div>

<div class="ContenedorCamposCreacionBasico">
	<div class="LabelCreacionBasico">Coste Kit Libre</div>
	<div class="tituloComponente">
		<table id="tablaTituloPrototipo">
		<tr>
			<td id="precio_kit_libre_<?php echo $i;?>" style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
				<span class="tituloComp"><?php echo number_format($precio_kit_libre, 2, ',', '.').'€';?></span>
			</td>
		</tr>
		</table>
	</div>
</div>
<br/>