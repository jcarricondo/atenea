<?php
// Este fichero muestra las referencias de los periféricos en la modificación de la Orden de Producción
include_once("../classes/basicos/referencia.class.php"); 
include_once("../classes/basicos/referencia_componente.class.php"); 
echo '<script type="text/javascript" src="../js/orden_produccion/confirm_mod_orden_produccion_perifericos.js"></script>';

// Cargamos las referencias de basicos
$ref_perifericos = new listadoReferenciasComponentes();
$ref_perifericos->setValores($id_componente); 
$ref_perifericos->realizarConsulta();
$resultadosBusqueda = $ref_perifericos->referencias_componentes;
?>
<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
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
	$max_caracteres_ref = 50;
	$max_caracteres = 25;
	
	for($j=0;$j<count($resultadosBusqueda);$j++) {
		$ref_per = new Referencia_Componente();
		$ref_modificada = new Referencia();
		$datoRef_Periferico = $resultadosBusqueda[$j];
		$ref_per->cargaDatosReferenciaComponenteId($datoRef_Periferico["id"]);
		$ref_modificada->cargaDatosReferenciaId($ref_per->id_referencia);
		$ref_per->calculaTotalPaquetes($ref_per->uds_paquete, $ref_per->piezas);
		$total_paquetes = $ref_per->total_paquetes;
			
		if($ref_modificada->pack_precio <> 0 and $ref_modificada->unidades <> 0) {
			$precio_unidad = $ref_modificada->pack_precio / $ref_modificada->unidades;
		} 
		else {
			$precio_unidad = 00;
		}
		$precio_referencia = $ref_per->piezas * $precio_unidad;
		$precio_periferico = $precio_periferico + $precio_referencia;
?>
<tr>
	<td style="text-align:center"><?php echo $ref_per->id_referencia; ?></td>
	<td id="enlaceComposites">
		<a href="../basicos/mod_referencia.php?id=<?php echo $ref_per->id_referencia;?>" target="_blank"/>
			<?php 
            	if (strlen($ref_modificada->referencia) > $max_caracteres_ref) {
            		echo substr($ref_modificada->referencia,0,$max_caracteres_ref).'...'; 
            	}
            	else {
            		echo $ref_modificada->referencia;
            	}
            ?>
		</a>
		<input type="hidden" name="REFS_PER_<?php echo $i;?>[]" id="REFS_PER_<?php echo $i;?>[]" value="<?php echo $ref_per->id_referencia;?>" />
	</td>
	<td><?php echo $ref_modificada->nombre_proveedor;?></td>
	<td><?php $ref_modificada->vincularReferenciaProveedor(); ?></td>
	<td><?php echo $ref_modificada->part_nombre;?></td>
	<td style="text-align:center"><input type="text" name="piezas_perifericos_<?php echo $i;?>[]" id="piezas_perifericos_<?php echo $i;?>[]" class="CampoPiezasInput" value="<?php echo $ref_per->piezas; ?>" onblur="javascript:validarPiezasCorrectasPeriferico(<?php echo $j;?>,<?php echo $i;?>)" /></td>
	<td style="text-align:center"><?php echo number_format($ref_modificada->pack_precio, 2, '.', '');?></td>
	<td style="text-align:center">
		<?php echo $ref_modificada->unidades;?>
		<input type="hidden" name="UDS_PERS_<?php echo $i;?>[]" id="UDS_PERS_<?php echo $i;?>[]" value="<?php echo $ref_modificada->unidades;?>"/>
	</td>
	<td style="text-align:center"><?php echo $total_paquetes;?></td>
	<td style="text-align:center"><?php echo number_format($precio_unidad, 2, '.', '');?></td>
	<td style="text-align:center"><?php echo number_format($precio_referencia, 2, '.', '');?></td>
	<td style="text-align:center"><input type="checkbox" name="chkbox_per" value="<?php echo $ref_per->id_referencia;?>" /></td>
</tr>
<?php			
	}
?>
