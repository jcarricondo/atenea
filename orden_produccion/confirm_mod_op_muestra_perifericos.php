<?php
// Muestra el contenido de los periféricos incluyendo sus kits
$precio_todos_perifericos = 0;
$es_periferico = true;
for ($i=0; $i<count($ids_perifericos); $i++) {
	$precio_periferico = 0;
	$per->cargaDatosPerifericoId($ids_perifericos[$i]);
	$id_componente = $ids_perifericos[$i];

	include("../orden_produccion/confirm_mod_op_muestra_periferico.php");
	include("../orden_produccion/confirm_mod_op_muestra_kits.php"); ?>

	<div class="ContenedorCamposCreacionBasico">
		<div class="LabelCreacionBasico">Coste Total Perif&eacute;rico</div>
		<div class="tituloComponente">
			<table id="tablaTituloPrototipo">
			<tr>
				<td id="precio_total_periferico_<?php echo $i; ?>" style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
				<?php
					$precio_total_periferico = $precio_periferico + $costeKitsPeriferico;
					$precio_todos_perifericos = $precio_todos_perifericos + $precio_total_periferico; ?>
					<span class="tituloComp"><?php echo number_format($precio_total_periferico, 2, ',', '.').'€';?></span>
					<input type="hidden" id="costeKitsPeriferico_<?php echo $i;?>" name="costeKitsPeriferico_<?php echo $i;?>" value="<?php echo $costeKitsPeriferico;?>"/>
					<input type="hidden" id="precio_tot_periferico_<?php echo $i;?>" name="precio_tot_periferico_<?php echo $i;?>" value="<?php echo $precio_total_periferico;?>"/>
				<?php
					$costeKitsPeriferico = 0;
				?>
				</td>
			</tr>
			</table>
		</div>
	</div>
	<br/>
<?php
}
// No hay periféricos
if (count($ids_perifericos) == 0) { ?>
	<div class="ContenedorCamposCreacionBasico">
		<div class="LabelCreacionBasico">Referencias Perif&eacute;ricos</div>
		<div class="tituloSinComponente"><?php echo "No hay periféricos"; ?></div>
	</div>
<?php
}
?>
<br/>

