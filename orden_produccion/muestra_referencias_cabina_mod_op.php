 <?php
// Este fichero muestra las referencias de la cabina em la modificación de la Orden de Producción
include_once("../classes/basicos/referencia.class.php"); 
include_once("../classes/basicos/referencia_componente.class.php");  
include_once("../classes/basicos/listado_referencias_componentes.class.php");

echo '<script type="text/javascript" src="../js/orden_produccion/confirm_mod_orden_produccion_cabina.js"></script>';
// Cargamos las referencias de los componentes de basicos
$ref_cabinas = new listadoReferenciasComponentes();
$ref_cabinas->setValores($id_cabina); 
$ref_cabinas->realizarConsulta();
$resultadosBusquedaCabinas = $ref_cabinas->referencias_componentes;
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
	$precio_cabina = 0;
	$max_caracteres_ref = 50;
	$max_caracteres = 25;
	for($i=0;$i<count($resultadosBusquedaCabinas);$i++) {
		$ref = new Referencia_Componente();
		$ref_modificada = new Referencia();
		$datoRef_Cabina = $resultadosBusquedaCabinas[$i];
		$ref->cargaDatosReferenciaComponenteId($datoRef_Cabina["id"]);
		$ref->calculaTotalPaquetes($ref->uds_paquete, $ref->piezas);
		$total_paquetes = $ref->total_paquetes;
		$ref_modificada->cargaDatosReferenciaId($ref->id_referencia);
		if($ref_modificada->pack_precio <> 0 and $ref_modificada->unidades <> 0) {
			$precio_unidad = $ref_modificada->pack_precio / $ref_modificada->unidades;
		} 
		else {
			$precio_unidad = 00;
		}
		$precio_referencia = $ref->piezas * $precio_unidad;
		$precio_cabina = $precio_cabina + $precio_referencia;
?>
<tr>
	<td style="text-align:center"><?php echo $ref->id_referencia; ?></td>
	<td id="enlaceComposites">
		<a href="../basicos/mod_referencia.php?id=<?php echo $ref->id_referencia;?>" target="_blank"/>
			<?php 
            	if (strlen($ref_modificada->referencia) > $max_caracteres_ref) {
            		echo substr($ref_modificada->referencia,0,$max_caracteres_ref).'...'; 
            	}
            	else {
            		echo $ref_modificada->referencia;
            	}
            ?>
		</a>
		<input type="hidden" name="REFS_CAB[]" id="REFS_CAB[]" value="<?php echo $ref->id_referencia;?>" />
	</td>
	<td><?php echo $ref_modificada->nombre_proveedor;?></td>
	<td><?php $ref_modificada->vincularReferenciaProveedor();?></td>
	<td><?php echo $ref_modificada->part_nombre;?></td>
	<td style="text-align:center"><input type="text" name="piezas_cabina[]" id="piezas_cabina[]" class="CampoPiezasInput" value="<?php echo $ref->piezas; ?>" onblur="javascript:validarPiezasCorrectas(<?php echo $i;?>)"/></td>
	<td style="text-align:center"><?php echo number_format($ref_modificada->pack_precio, 2, '.', '');?></td>
	<td style="text-align:center">
		<?php echo $ref_modificada->unidades;?>
		<input type="hidden" name="UDS_CAB[]" id="UDS_CAB[]" value="<?php echo $ref_modificada->unidades;?>"/>
	</td>
	<td style="text-align:center"><?php echo $total_paquetes;?></td>
	<td style="text-align:center"><?php echo number_format($precio_unidad, 2, '.', '');?></td>
	<td style="text-align:center"><?php echo number_format($precio_referencia, 2, '.', '');?></td>
	<td style="text-align:center"><input type="checkbox" name="chkbox" value="<?php echo $ref->id_referencia;?>" /></td>
</tr>
<?php
	}
?>
