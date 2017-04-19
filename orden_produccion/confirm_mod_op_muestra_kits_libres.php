<?php
// Muestra el contenido de los kits_libres
$precio_todos_kits_libres = 0;
for ($i=0; $i<count($ids_kits_libres); $i++) {
	$kit->cargaDatosKitId($ids_kits_libres[$i]);
	$id_componente = $ids_kits_libres[$i];
	include("../orden_produccion/confirm_mod_op_muestra_kit_libre.php");
	$precio_todos_kits_libres = $precio_todos_kits_libres + $precio_kit_libre;
}
if($hay_alguna_heredada) $color_precio = ' style="color: orange"';
else $color_precio = ' style="color: #2998cc;"';
?>
<div class="ContenedorCamposCreacionBasico">
	<div class="LabelCreacionBasico">Coste Total Kits Libres</div>
	<div class="tituloComponente">
		<table id="tablaTituloPrototipo">
		<tr>
			<td id="precio_total_kits_libres" style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
				<span class="tituloComp" <?php echo $color_precio;?>><?php echo number_format($precio_todos_kits_libres, 2, ',', '.').'€';?></span>
				<input type="hidden" id="coste_total_kits_libres" name="coste_total_kits_libres" value="<?php echo $precio_todos_kits_libres;?>"/>
			</td>
		</tr>
		</table>
	</div>
</div>
<br/>



