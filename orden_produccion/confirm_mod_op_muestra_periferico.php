<?php
// Este fichero carga un periférico en el proceso de confirmación de modificación de una Orden de Producción
$max_caracteres_ref = 50;
$max_caracteres = 25;
$ref_perifericos->setValores($id_componente);
$ref_perifericos->realizarConsulta();
$resultadosBusqueda = $ref_perifericos->referencias_componentes;
?>

<div class="ContenedorCamposCreacionBasico">
	<div class="LabelCreacionBasico">Referencias Perif&eacute;rico</div>
	<div class="tituloComponente">
		<table id="tablaTituloPrototipo">
		<tr>
			<td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
				<span class="tituloComp"><?php echo $per->periferico.'_v'.$per->version;?></span>
			</td>
			<td style="text-align:left; background:#fff; vertical-align:top; padding:0px 5px 0px 5px;">
			<?php
				if ($per->prototipo == 1) { ?>
					<span class="ImagenPrototipo"><img src="../images/prototipo.jpg" width="20px" height="20px" alt="PROTOTIPO" title="PROTOTIPO"></span>
			<?php
				} else if ($per->prototipo == 0) { ?>
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
			<table id="mitabla_<?php echo $i;?>">
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
				<th style="text-align:center">ELIM</th>
			</tr>
			<?php
				for($j=0;$j<count($resultadosBusqueda);$j++) {
					$datoRef_Periferico = $resultadosBusqueda[$j];
					$ref_comp->cargaDatosReferenciaComponenteId($datoRef_Periferico["id"]);
					$ref->cargaDatosReferenciaId($ref_comp->id_referencia);
					$ref_comp->calculaTotalPaquetes($ref_comp->uds_paquete, $ref_comp->piezas);
					$total_paquetes = $ref_comp->total_paquetes;
			
					if($ref->pack_precio <> 0 and $ref->unidades <> 0) $precio_unidad = $ref->pack_precio / $ref->unidades;
					else $precio_unidad = 00;

					$precio_referencia = $ref_comp->piezas * $precio_unidad;
					$precio_periferico = $precio_periferico + $precio_referencia; ?>

					<tr>
						<td style="text-align:center"><?php echo $ref_comp->id_referencia; ?></td>
						<td id="enlaceComposites">
							<a href="../basicos/mod_referencia.php?id=<?php echo $ref_comp->id_referencia;?>" target="_blank"/>
							<?php
								if (strlen($ref->referencia) > $max_caracteres_ref) echo substr($ref->referencia,0,$max_caracteres_ref).'...';
								else echo $ref->referencia;
							?>
							</a>
							<input type="hidden" name="REFS_PER_<?php echo $i;?>[]" id="REFS_PER_<?php echo $i;?>[]" value="<?php echo $ref_comp->id_referencia;?>" />
						</td>
						<td><?php echo $ref->nombre_proveedor;?></td>
						<td><?php $ref->vincularReferenciaProveedor(); ?></td>
						<td><?php echo $ref->part_nombre;?></td>
						<td style="text-align:center">
							<input type="text"
								   name="piezas_perifericos_<?php echo $i;?>[]"
								   id="piezas_perifericos_<?php echo $i;?>[]"
								   class="CampoPiezasInput"
								   value="<?php echo trim($ref_comp->piezas);?>"
								   onblur="validarPiezasCorrectasPeriferico(<?php echo $j;?>,<?php echo $i;?>)" />
						</td>
						<td style="text-align:center"><?php echo number_format($ref->pack_precio, 2, '.', '');?></td>
						<td style="text-align:center">
							<?php echo $ref->unidades;?>
							<input type="hidden" name="UDS_PERS_<?php echo $i;?>[]" id="UDS_PERS_<?php echo $i;?>[]" value="<?php echo $ref->unidades;?>"/>
						</td>
						<td style="text-align:center"><?php echo $total_paquetes;?></td>
						<td style="text-align:center"><?php echo number_format($precio_unidad, 2, '.', '');?></td>
						<td style="text-align:center"><?php echo number_format($precio_referencia, 2, '.', '');?></td>
						<td style="text-align:center"><input type="checkbox" name="chkbox_per" value="<?php echo $ref_comp->id_referencia;?>" /></td>
					</tr>
			<?php
				}
			?>
			</table>
		</div>
	</div>
	<input type="button" id="mas" name="mas" class="BotonMas"  value="+" onclick="<?php echo 'Abrir_ventana('."'".'buscador_referencias_perifericos_mod_op.php?id='.$i."'".')';?>"/>
	<input type="hidden" id="periferico<?php echo $i;?>" name="periferico<?php echo $i;?>" value="<?php echo $i;?>" />
	<input type="button" id="menos" name="menos" class="BotonMenos" value="-" onclick="removeRowPeriferico('mitabla_<?php echo $i;?>',<?php echo $i;?>)"/>

	<div class="tituloComponente">
		<input type="checkbox" id="eliminar_periferico-<?php echo $i;?>" name="eliminar_periferico-<?php echo $i;?>" value="1" />
		<div class="label_check_precios">Eliminar perif&eacute;rico</div>
	</div>
	<br/>
</div>

<div class="ContenedorCamposCreacionBasico">
	<div class="LabelCreacionBasico">Coste Perif&eacute;rico</div>
	<div class="tituloComponente">
		<table id="tablaTituloPrototipo">
		<tr>
			<td id="precio_periferico_<?php echo $i;?>" style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
				<span class="tituloComp"><?php echo number_format($precio_periferico, 2, ',', '.').'€';?></span>
			</td>
		</tr>
		</table>
	</div>
</div>
<br/>