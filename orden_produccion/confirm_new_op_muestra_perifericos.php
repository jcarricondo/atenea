<?php
// Este fichero muestra las referencias de los periféricos en la confirmación de la creación de una Orden de Producción
$listado_ref_comp->setValores($id_componente);
$listado_ref_comp->realizarConsulta();
$resultadosBusqueda = $listado_ref_comp->referencias_componentes;
?>

<div class="ContenedorCamposCreacionBasico">
	<div class="LabelCreacionBasico">Referencias Perif&eacute;rico</div>
	<div class="tituloComponente">
		<table id="tablaTituloPrototipo">
		<tr>
			<td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
				<span class="tituloComp"><?php echo $periferico->periferico.'_v'.$periferico->version; ?></span>
			</td>
			<td style="text-align:left; background:#fff; vertical-align:top; padding:0px 5px 0px 5px;">
			<?php
				if($periferico->prototipo == 1) { ?>
					<span class="ImagenPrototipo"><img src="../images/prototipo.jpg" width="20px" height="20px" alt="PROTOTIPO" title="PROTOTIPO"></span>
			<?php
				} else if ($periferico->prototipo == 0) { ?>
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
			<table id="mitabla-<?php echo $id_componente; ?>">
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
				$max_caracteres_ref = 50;
				$max_caracteres = 25;
				for($j=0;$j<count($resultadosBusqueda);$j++) {
					$datoRef_Periferico = $resultadosBusqueda[$j];
					$ref_comp->cargaDatosReferenciaComponenteId($datoRef_Periferico["id"]);
					$ref->cargaDatosReferenciaId($ref_comp->id_referencia);
					$ref_comp->calculaTotalPaquetes($ref->unidades,$ref_comp->piezas);
					if($ref->pack_precio <> 0 and $ref->unidades <> 0) $precio_unidad = $ref->pack_precio / $ref->unidades;
					else $precio_unidad = 00;
					$precio_referencia = $ref_comp->piezas * $precio_unidad;
					$precio_periferico = $precio_periferico + $precio_referencia; ?>

					<tr>
						<td style="text-align:center"><?php echo $ref_comp->id_referencia;?></td>
						<td id="enlaceComposites">
							<a href="../basicos/mod_referencia.php?id=<?php echo $ref_comp->id_referencia;?>" target="_blank"/>
							<?php
								if (strlen($ref->referencia) > $max_caracteres_ref) echo substr($ref->referencia,0,$max_caracteres_ref).'...';
								else echo $ref->referencia;
							?>
							</a>
						</td>
						<td>
						<?php
							if (strlen($ref->nombre_proveedor) > $max_caracteres) echo substr($ref->nombre_proveedor,0,$max_caracteres).'...';
							else echo $ref->nombre_proveedor;
						?>
						</td>
						<td><?php $ref->vincularReferenciaProveedor(); ?></td>
					  	<td>
						<?php
							if (strlen($ref->part_nombre) > $max_caracteres) echo substr($ref->part_nombre,0,$max_caracteres).'...';
							else echo $ref->part_nombre;
						?>
						</td>
						<td style="text-align:center"><?php echo number_format($ref_comp->piezas, 2, ',', '.');?></td>
						<td style="text-align:center"><?php echo number_format($ref->pack_precio, 2, ',', '.');?></td>
						<td style="text-align:center"><?php echo $ref->unidades; ?></td>
						<td style="text-align:center"><?php echo $ref_comp->total_paquetes; ?></td>
						<td style="text-align:center"><?php echo number_format($precio_unidad, 2, ',', '.');?></td>
						<td style="text-align:center"><?php echo number_format($precio_referencia, 2, ',', '.');?></td>
					</tr>
			<?php
				}
			?>
			</table>
		</div>
	</div>
</div>
<div class="ContenedorCamposCreacionBasico">
	<div class="LabelCreacionBasico">Coste Perif&eacute;rico</div>
	<div class="tituloComponente">
		<table id="tablaTituloPrototipo">
		<tr>
			<td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
				<span class="tituloComp"><?php echo number_format($precio_periferico,2,',','.').'€'?></span>
			</td>
		</tr>
		</table>
	</div>
</div>
<br/>
