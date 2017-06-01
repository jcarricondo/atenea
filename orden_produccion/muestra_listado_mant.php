<?php
	// Muestra el listado con las OP de mantenimiento
?>
<div class="CapaTabla">
	<table id="tabla_ordenes_produccion">
	<tr>
       	<th style="text-align:center">ID</th>
        <th style="text-align:center"><input type="checkbox" name="todas_op" id="todas_op" onclick="TodasOP();"/></th>
        <th>ORDEN PRODUCCIÓN</th>
        <?php if($esAdminGlobal || $esUsuarioGes) { ?> <th>SEDE</th> <?php } ?>
		<th style="text-align:center">F. CREADO</th>
		<th style="text-align:center">REF.</th>
		<th style="text-align:center">REF. LIBRES</th>
		<th style="text-align:center">OC</th>       
		<th style="text-align:center">RECEPCION</th>  
		<th colspan="4" style="text-align:center">OPCIONES</th>        
	</tr> 
<?php
	for($i=0;$i<count($resultadosBusqueda);$i++) {
		// Se cargan los datos de la orden de producción y el producto según su identificador
		$orden_produccion = new Orden_Produccion();
		$datoOrdenProduccion = $resultadosBusqueda[$i];
		$orden_produccion->cargaDatosProduccionId($datoOrdenProduccion["id_produccion"]);
		$id_produccion = $orden_produccion->id_produccion;
		// Buscar productos con Orden de Produccion: id_produccion
		// Guarda en orden_produccion->id_producto el id_producto asociado a esa ordem_produccion
		$orden_produccion->dameIdProducto($id_produccion);  
		$id_producto = $orden_produccion->id_producto["id_producto"];

		$producto = new Producto();
		$producto->cargaDatosProductoId($id_producto);
		$id_nombre_producto = $producto->id_nombre_producto;
				
		$nombre_producto = new Nombre_Producto();
		$nombre_producto->cargaDatosNombreProductoId($id_nombre_producto);
		
		$orden_produccion->fecha_creado = $orden_produccion->cFechaNormal($orden_produccion->fecha_creado);			
?>
	<tr>
      	<td style="text-align:center"><?php echo $orden_produccion->id_produccion;?></td>
        <td style="text-align:center"><input type="checkbox" id="chkbox[]" name="chkbox[]" value="<?php echo $orden_produccion->id_produccion;?>" /></td>
        <td>
			<a href="ver_op.php?id=<?php echo $id_produccion;?>&nombre=<?php echo $nombre_producto->nombre;?>&id_producto=<?php echo $id_producto; ?>">
				<?php 
					// Si tiene alias mostramos el alias. Si no mostramos la OP
					if (($orden_produccion->alias_op != NULL) && ($orden_produccion->alias_op != $orden_produccion->codigo)){
						echo $orden_produccion->alias_op;
					}
					else{
						echo $orden_produccion->codigo; 
					}
				?>
            </a>
        </td>
        <?php 
        	if($esAdminGlobal || $esUsuarioGes) {?> 
        		<td>
        			<?php 
        				$sede->cargaDatosSedeId($orden_produccion->id_sede);
        				$nombre_sede = $sede->nombre; 
        				echo $nombre_sede; ?>
        		</td> 
        <?php 
    		} 
    	?>
        <td style="text-align:center"><?php echo $orden_produccion->fecha_creado;?></td>
        <td style="text-align:center">
        	<a href="../orden_produccion/informe_referencias_op.php?id=<?php echo $id_produccion;?>">XLS</a>
            	- 
            <a href="../orden_produccion/informe_referencias_op_componentes.php?id=<?php echo $id_produccion;?>">XLS COM.</a>
        </td>                        
        <td style="text-align:center">
        	<a href="javascript:abrir('muestra_referencias_libres.php?id_producto=<?php echo $id_producto;?>')"><?php echo "R. LIBRES";?></a>
        </td>    
		<td style="text-align:center">
			<a href="../orden_compra/ordenes_compra.php?orden_produccion[]=<?php echo $id_produccion.'&realizandoBusqueda=1&enlace_op=1';?>" style="color:#ff0000">O. COMPRA</a><input type="hidden" id="metodo_get" name="metodo_get" value="1">
		</td>   
		<td style="text-align:center">
			<?php
				$orden_produccion->getPorcentajeRecepcion();
			?>
            <div align="center">
				<div class="barra_progreso">
					<div class="barra_progreso_activa" style="width: <?php echo $orden_produccion->porcentaje_recepcion; ?>px; !important"></div>
				</div>
            </div>    
		</td>
        <input type="hidden" name="hay_facturas-<?php echo $id_produccion;?>" id="hay_facturas-<?php echo $id_produccion;?>" value="<?php echo $error_facturas;?>" />
        <input type="hidden" name="estado_pedida-<?php echo $id_produccion;?>" id="estado_pedida-<?php echo $id_produccion;?>" value="<?php echo $error_estado;?>" />                                         
        <td style="text-align:center"><input type="button" id="eliminar" name="eliminar" value="DEL" class="BotonEliminar" onclick="return validarEliminacion(<?php echo $id_produccion;?>);"/></td>
        </tr> 
<?php
	} 
?>
	</table>                  
</div>