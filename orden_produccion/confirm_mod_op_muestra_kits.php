<?php
// Este fichero carga kits de un periférico en el proceso de confirmación de modificación de una Orden de Producción
$orden_produccion->dameIdsKitComponente($ids_perifericos[$i]);
$costeKitsPeriferico = 0;
for ($k=0; $k<count($orden_produccion->ids_kit); $k++) {
	$kit->cargaDatosKitId($orden_produccion->ids_kit[$k]["id_kit"]);
	$ref_kits->setValores($kit->id_componente);
	$ref_kits->realizarConsulta();
	$resultadosReferenciasKit = $ref_kits->referencias_componentes;

	// Obtenemos las referencias del componente para calcular el precio con sus heredadas
	$referencias_componente = $comp->dameRefsYPiezasComponente($kit->id_componente);
	$referencias_componente_her = $ref_heredada->obtenerHeredadas($referencias_componente);
	$precio_kit = $ref_heredada->damePrecioReferenciasHeredadas($referencias_componente_her);
	$hay_heredadas = count($referencias_componente) != count($referencias_componente_her);
	if($hay_heredadas) {
		$color_precio = ' style="color: orange"';
		$hay_alguna_heredada = true;
	}
	else $color_precio = ' style="color: #2998cc;"';?>

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
					$precio_kit_tabla = 0;
					for ($j=0; $j<count($resultadosReferenciasKit); $j++) {
						// Si el estado de la OP es BORRADOR o INICIADO obtenemos los datos de la tabla referencias
						$datoRef_Kit = $resultadosReferenciasKit[$j];

						$ref_comp->cargaDatosReferenciaComponenteId($datoRef_Kit["id"]);
						$ref->cargaDatosReferenciaId($ref_comp->id_referencia);

						if ($ref->pack_precio <> 0 and $ref->unidades <> 0) $precio_unidad = ($ref->pack_precio / $ref->unidades);
						else $precio_unidad = 00;

						$ref->calculaTotalPaquetes($ref->unidades, $ref_comp->piezas);
						$total_paquetes = $ref->total_paquetes;
						$precio_referencia = $ref_comp->piezas * $precio_unidad; ?>

						<tr>
							<td style="text-align:center"><?php echo $ref_comp->id_referencia;?></td>
							<td id="enlaceComposites">
								<a href="../basicos/mod_referencia.php?id=<?php echo $ref_comp->id_referencia;?>"/><?php echo $ref->referencia;?></a>
							</td>
							<td><?php echo $ref->nombre_proveedor;?></td>
							<td><?php $ref->vincularReferenciaProveedor();?></td>
							<td><?php echo $ref->part_nombre;?></td>
							<td style="text-align:center"><?php echo number_format($ref_comp->piezas, 2, ',', '.');?></td>
							<td style="text-align:center"><?php echo number_format($ref->pack_precio, 2, ',', '.');?></td>
							<td style="text-align:center"><?php echo $ref->unidades;?></td>
							<td style="text-align:center"><?php echo $ref->total_paquetes;?></td>
							<td style="text-align:center"><?php echo number_format($precio_unidad, 2, ',', '.');?></td>
							<td style="text-align:center"><?php echo number_format($precio_referencia, 2, ',', '.')?></td>
						</tr>
					<?php
						$precio_kit_tabla = $precio_kit_tabla + $precio_referencia;
					}
				?>
				</table>
			</div>
		</div>
	</div>

	<div class="ContenedorCamposCreacionBasico">
		<div class="LabelCreacionBasico">Coste Kit Perif&eacute;rico</div>
		<div class="tituloComponente">
			<table id="tablaTituloPrototipo">
			<tr>
				<td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
					<span class="tituloComp" <?php echo $color_precio;?>><?php echo number_format($precio_kit, 2, ',', '.').'€';?></span>
				</td>
			</tr>
			</table>
		</div>
	</div>
	<br/>
	<?php
	$costeKitsPeriferico = $costeKitsPeriferico + $precio_kit;
}
?>
