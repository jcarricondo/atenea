<?php 
// Este fichero muestra las referencias libres en la modificación de la Orden de Producción
$precio_refs_libres = 0;
$ref_libres = $referencias_libres;
?>
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
	$precio_refs_libres = 0;
	$ref_libres = $referencias_libres;
	for($i=0;$i<count($referencias_libres);$i++) {
		// Se cargan los datos de las referencias según su identificador
		$ref = new Referencia_Libre();
		$ref_modificada = new Referencia();
		echo '<input type="hidden" id="ref_libres[]" name="ref_libres[]" value="'.$ref_libres[$i].'"/>';
		$ref->cargaDatosReferenciaLibreId($referencias_libres[$i]);
		echo '<input type="hidden" id="uds_paquete[]" name="uds_paquete[]" value="'.$ref->cantidad.'"/>';
					
		$ref_modificada->cargaDatosReferenciaId($referencias_libres[$i]);
		$ref_modificada->calculaTotalPaquetes($ref_modificada->unidades,$piezas[$i]);
		$total_paquetes = $ref_modificada->total_paquetes;
		$precio_unidad = ($ref_modificada->pack_precio / $ref_modificada->unidades);
		$precio_referencia = $precio_unidad * $piezas[$i];
		$precio_refs_libres = $precio_refs_libres + $precio_referencia;
		echo '<input type="hidden" id="tot_paquetes[]" name="tot_paquetes[]" value="'.$total_paquetes.'"/>';
?>
		<tr>
			<td style="text-align:center;"><?php echo $ref_modificada->id_referencia; ?></td>
			<td id="enlaceComposites">
				<a href="../basicos/mod_referencia.php?id=<?php echo $ref_modificada->id_referencia;?>" target="_blank"/>
					<?php 
           				if (strlen($ref_modificada->referencia) > $max_caracteres_ref) {
           					echo substr($ref_modificada->referencia,0,$max_caracteres_ref).'...'; 
           				}
           				else {
           					echo $ref_modificada->referencia;
           				}
           			?>
				</a>
				<input type="hidden" name="REFS_LIBRES[]" id="REFS_LIBRES[]" value="<?php echo $ref_modificada->id_referencia;?>" />
			</td>
			<td><?php echo $ref_modificada->nombre_proveedor; ?></td>		
			<td><?php $ref_modificada->vincularReferenciaProveedor(); ?></td>
			<td><?php echo $ref_modificada->part_nombre;?></td>
			<td style="text-align:center"><input type="text" name="piezas_ref_libres[]" id="piezas_ref_libres[]" class="CampoPiezasInput" value="<?php echo $piezas[$i];?>" onblur="javascript:validarPiezasCorrectasRefsLibres(<?php echo $i;?>)"/></td>
			<td style="text-align:center"><?php echo number_format($ref_modificada->pack_precio, 2, '.', '');?></td>	
			<td style="text-align:center">
				<?php echo $ref_modificada->unidades;?>
				<input type="hidden" id="UDS_REF_LIBRES[]" name="UDS_REF_LIBRES[]" value="<?php echo $ref_modificada->unidades;?>"/>
			</td>
			<td style="text-align:center"><?php echo $total_paquetes;?></td>
			<td style="text-align:center"><?php echo number_format($precio_unidad, 2, '.', '');?></td>	
			<td style="text-align:center"><?php echo number_format($precio_referencia, 2, '.', '');?></td>	
			<td style="text-align:center"><input type="checkbox" name="chkbox" value="<?php echo $ref_modificada->id_referencia;?>" /></td>
		</tr>	
<?php
	}
?>
