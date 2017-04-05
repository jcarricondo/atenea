<?php 
// Muestra el listado con las OP 
?>
<div class="CapaTabla">
	<table id="tabla_ordenes_produccion">
	<tr>
      	<th style="text-align:center">ID</th>
        <th style="text-align:center"><input type="checkbox" name="todas_op" id="todas_op" onclick="TodasOP();"/></th>
        <th>ORDEN PRODUCCIÓN</th>
        <?php if($esAdminGlobal || $esUsuarioGes) {?> <th>SEDE</th> <?php } ?>
		<th>PRODUCTO</th>
		<th style="text-align:center">F. INICIO</th>
		<th style="text-align:center">F. ENTREGA</th>
		<th style="text-align:center">F. DESEADA</th>
        <th style="text-align:center">REF.</th>
		<th style="text-align:center">UDS</th>
		<th style="text-align:center">PERIFS</th>
		<th style="text-align:center">PCS</th>
	    <!-- <th style="text-align:center">SOFT.</th> -->
        <th style="text-align:center">REF. LIBRES</th>
		<th style="text-align:center">OC</th>       
		<th style="text-align:center">RECEPCION</th>  
		<th>ESTADO</th>     
		<?php 
			if(permisoMenu(11)){ ?>
				<th colspan="4" style="text-align:center">OPCIONES</th>        
		<?php 
			}
		?>
	</tr> 
<?php
	for($i=0;$i<count($resultadosBusqueda);$i++) {
		// Se cargan los datos de la orden de producción y el producto según su identificador
		$orden_produccion = new Orden_Produccion();
		$datoOrdenProduccion = $resultadosBusqueda[$i];
		$orden_produccion->cargaDatosProduccionId($datoOrdenProduccion["id_produccion"]);
		$id_produccion = $orden_produccion->id_produccion;
		
		// Obtenemos el primer id_producto de la Orden de Produccion		
		$orden_produccion->dameIdProducto($id_produccion);  
		$id_producto = $orden_produccion->id_producto["id_producto"];
			
		$producto = new Producto();
		$producto->cargaDatosProductoId($id_producto);
		$id_nombre_producto = $producto->id_nombre_producto;
				
		$nombre_producto = new Nombre_Producto();
		$nombre_producto->cargaDatosNombreProductoId($id_nombre_producto);
				
		/*$id_cabina = $orden_produccion->dameIdCabina($id_produccion);
		$id_cabina = $id_cabina["id_componente"];
					
		$cabina = new Cabina();
		$cabina->cargaDatosCabinaId($id_cabina);*/
					
		$orden_produccion->fecha_inicio = $orden_produccion->cFechaNormal($orden_produccion->fecha_inicio);
		$orden_produccion->fecha_entrega = $orden_produccion->cFechaNormal($orden_produccion->fecha_entrega);
		$orden_produccion->fecha_entrega_deseada = $orden_produccion->cFechaNormal($orden_produccion->fecha_entrega_deseada);
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
        <td><?php echo $nombre_producto->nombre;?></td>
		<td style="text-align:center"><?php echo $orden_produccion->fecha_inicio;?></td>
		<td style="text-align:center">
			<?php 
				if($orden_produccion->estado == "INICIADO"){
					if(permisoMenu(10)){
			?>		
					<a href="javascript:abrirLittlePopup('fecha_entrega.php?id_produccion=<?php echo $id_produccion;?>&unidades=<?php echo $orden_produccion->unidades;?>&id_producto=<?php echo $id_producto;?>')"><?php echo $orden_produccion->fecha_entrega;?></a>
			<?php
					}
					else echo $orden_produccion->fecha_entrega;	
				}
				else {
					echo $orden_produccion->fecha_entrega;
				}
			?>			
		</td>
		<td style="text-align:center"><?php echo $orden_produccion->fecha_entrega_deseada;?></td>
		<td style="text-align:center">
	       	<a href="../orden_produccion/informe_referencias_op.php?id=<?php echo $id_produccion;?>">XLS</a> -
            <a href="../orden_produccion/informe_referencias_op_componentes.php?id=<?php echo $id_produccion;?>">XLS COM.</a>
		</td>
        <td style="text-align:center"><?php echo $orden_produccion->unidades; ?></td>
		<td style="text-align:center"><a href="javascript:abrir('muestra_perifericos.php?producto=<?php echo $nombre_producto->nombre;?>&id_produccion=<?php echo $id_produccion;?>')"><?php echo "PERIFS";?></a></td>
		<td style="text-align:center"><a href="javascript:abrir('muestra_ordenadores.php?producto=<?php echo $nombre_producto->nombre;?>&id_produccion=<?php echo $id_produccion;?>')"><?php echo "PCS";?></a></td>
		<!-- <td style="text-align:center"><a href="javascript:abrir('muestra_softwares.php?producto=<?php // echo $nombre_producto->nombre;?>&id_produccion=<?php // echo $id_produccion;?>')"><?php // echo "SOFT";?></a></td> -->
		<td style="text-align:center"><a href="javascript:abrir('muestra_referencias_libres.php?producto=<?php echo $nombre_producto->nombre;?>&id_produccion=<?php echo $id_produccion;?>')"><?php echo "R. LIBRES";?></a></td>    
		<td style="text-align:center">
			<?php 
				if($orden_produccion->estado != "BORRADOR") {
					if(permisoMenu(13)){
          				echo '<a href="../orden_compra/ordenes_compra.php?orden_produccion[]='.$id_produccion.'&realizandoBusqueda=1&enlace_op=1">O. COMPRA</a><input type="hidden" id="metodo_get" name="metodo_get" value="1">';
          			}
          			else echo "-";
            	}
				else {
					if(permisoMenu(13)){
						echo '<a href="../orden_compra/ordenes_compra.php?orden_produccion[]='.$id_produccion.'&realizandoBusqueda=1&enlace_op=1" style="color:#ff0000">O. COMPRA</a><input type="hidden" id="metodo_get" name="metodo_get" value="1">';
					}
					else echo "-";
				}
        	?>
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
        <td><?php echo $orden_produccion->estado;?></td>
        <input type="hidden" name="hay_facturas-<?php echo $id_produccion;?>" id="hay_facturas-<?php echo $id_produccion;?>" value="<?php echo $error_facturas;?>" />
        <input type="hidden" name="estado_pedida-<?php echo $id_produccion;?>" id="estado_pedida-<?php echo $id_produccion;?>" value="<?php echo $error_estado;?>" />                                         
        <?php 
			// OPCIONES
        	if(permisoMenu(11)){
				if($orden_produccion->estado == "BORRADOR") { ?>
					<td style="text-align:center">
	           			<input type="button" id="iniciar" name="iniciar" value="INI" class="BotonEliminar" onclick="return validarInicio(<?php echo $id_produccion;?>,<?php echo $orden_produccion->unidades;?>,<?php echo $id_producto;?>);"/>
                    </td>
                    <td style="text-align:center">
						<?php 
							$hay_facturas = $orden_produccion->comprobarHayFacturasOrdenCompra($id_produccion);
							if ($hay_facturas) {
								$error_facturas = 1;
							}
							else $error_facturas = 0;
							$estado_pedida = $orden_produccion->comprobarHayOrdenesCompraEnPedida($id_produccion);
							if ($estado_pedida) {
								$error_estado = 1;	
							}
							else $error_estado = 0;
						?>
						<?php 
							if(permisoMenu(10)){ ?>	
			            		<input type="button" id="modificar" name="modificar" value="MOD" class="BotonEliminar" onclick="return validarModificacion(<?php echo $id_produccion;?>,<?php echo $id_producto;?>);"/>
			        	<?php 
			        		}
			        	?>
			        </td>
			        <td style="text-align:center">
			        	<?php 
							if(permisoMenu(10)){ ?>		
			        			<input type="button" id="eliminar" name="eliminar" value="DEL" class="BotonEliminar" onclick="return validarEliminacion(<?php echo $id_produccion;?>);"/>
			        	<?php 
			        		}
			        	?>
			        </td>
			        <td width="75px" style="text-align:center"></td>
        	<?php
	        	}
				elseif($orden_produccion->estado == "INICIADO"){ ?>
			        <td style="text-align:center"></td>
			        <td style="text-align:center"></td>
			        <td style="text-align:center"></td>
			        <td style="text-align:center"><input type="button" id="cambiar_a_finalizada" name="cambiar_a_finalizada" value="FIN" class="BotonEliminar" onclick="return validarCambioEstado(<?php echo $id_produccion;?>,'<?php echo $orden_produccion->estado?>');"/></td>
			<?php
				}
				elseif($orden_produccion->estado == "FINALIZADO"){ ?>
			        <td style="text-align:center"> </td>
			        <td style="text-align:center"> </td>
			        <td style="text-align:center"> </td>                                
			        <td style="text-align:center"> </td>
        <?php
				}
			}
		?>		
		</tr> 
<?php
	} 
?>
	</table>                  
</div>