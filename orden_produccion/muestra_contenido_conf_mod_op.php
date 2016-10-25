<?php 
// Este fichero contiene el codigo HTML y PHP del contenedor central de la Confirmacion de la Modificacion de una Orden de Produccion	
?> 
<h3> Confirmación de la nueva Orden de Producción </h3>
<form id="FormularioCreacionBasico" name="confirmModOrdenProduccion" onsubmit="return validarFormulario()" action="confirm_mod_orden_produccion.php?id_produccion=<?php echo $id_produccion;?>" method="post">
	<br />
	<h5> Datos de la nueva Orden de Producción </h5>
	 <?php 
    	if($esAdminGlobal || $esAdminGes){
	    	// ADMINISTRADOR GLOBAL. Elige la sede de la OP 
	    	if($id_sede == 1) $nombre_sede = "SIMUMAK";
	    	else if($id_sede == 2) $nombre_sede = "TORO";
 	?>
			<div class="ContenedorCamposCreacionBasico">
			   	<div class="LabelCreacionBasico">Sede</div>
			    <input type="text" id="nombre_sede" name="nombre_sede" class="CreacionBasicoInput" readonly="readonly" value="<?php echo $nombre_sede;?>" />
			    <input type="hidden" id="sede" name="sede" value="<?php echo $sede;?>"/> 
			</div>
	<?php
		}
		else { ?>
			<input type="hidden" id="sede" name="sede" value="<?php echo $sede;?>"/> 
	<?php	
		}			
	?> 	
	
	<div class="ContenedorCamposCreacionBasico">
	   	<div class="LabelCreacionBasico">Alias</div>
	  	<input type="text" id="alias_op" name="alias_op" class="CreacionBasicoInput" readonly="readonly" value="<?php echo $alias_op;?>" />
	</div>
	<div class="ContenedorCamposCreacionBasico">
	  	<div class="LabelCreacionBasico">Unidades *</div>
	  	<input type="text" id="unidades" name="unidades" class="CreacionBasicoInput" readonly="readonly" value="<?php echo $unidades;?>" />
	    <input type="hidden" id="numero_unidades" name="numero unidades" value="<?php echo $unidades;?>" />
	</div>    
	<div class="ContenedorCamposCreacionBasico">
	  	<div class="LabelCreacionBasico">Producto *</div>
	  	<input type="text" id="producto" name="producto" class="CreacionBasicoInput" readonly="readonly" value="<?php echo $nombre_producto;?>" />
	  	<input type="hidden" id="id_nombre_producto" name="id_nombre_producto" value="<?php echo $id_nombre_producto;?>"/>
	</div>

<?php
	/*
	// Si hay cabina
	if (($id_cabina != 0) and ($id_cabina != -1)) { ?>
		<div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Cabina</div>
            <?php $cabina->cargaDatosCabinaId($id_cabina); ?>
          	<input type="text" id="cabina" name="cabina" class="CreacionBasicoInput" readonly="readonly" value="<?php echo $cabina->cabina.'_v'.$cabina->version;?>" />
            <input type="hidden" id="id_cabina" name="id_cabina" value="<?php echo $id_cabina;?>"/>
        </div>
<?php
	}
	else { ?>
		<div class="ContenedorCamposCreacionBasico">
    		<div class="LabelCreacionBasico">Cabina</div>
        	<div class="tituloSinComponente">Sin Cábina</div> 
        </div>
<?php 
	}
	*/
?>

<div class="ContenedorCamposCreacionBasico">
   	<div class="LabelCreacionBasico">Perifericos</div>
  	<?php 
		for ($i=0;$i<count($ids_perifericos);$i++){
			echo '<input type="hidden" id="IDS_PERS[]" name="IDS_PERS[]" value="'.$ids_perifericos[$i].'"/>';
			$periferico->cargaDatosPerifericoId($ids_perifericos[$i]);
			$nombres_per[] = $periferico->periferico.'_v'.$periferico->version;
	        echo '<input type="hidden" id="perifericos_nombres[]" name="perifericos_nombres[]" value="'.$nombres_per[$i].'"/>';
		}
		if (count($ids_perifericos) == 0) echo '<div class="tituloSinComponente">Sin Periféricos</div>';
		else {
	?>
    		<textarea id="ids_perifericos[]" name="ids_perifericos[]" class="TextAreaOP" readonly="readonly" cols="1" rows="<?php echo count($ids_perifericos);?>"><?php for($i=0;$i<count($nombres_per);$i++) echo $nombres_per[$i]."\n";?></textarea>
    <?php
		}
	?>
</div>

<!--
<div class="ContenedorCamposCreacionBasico">
	<div class="LabelCreacionBasico">Software</div>
    <?php
		/*
		for ($i=0;$i<count($ids_softwares);$i++){
			echo '<input type="hidden" id="IDS_SOFT[]" name="IDS_SOFT[]" value="'.$ids_softwares[$i].'"/>';
			$soft->cargaDatosSoftwareId($ids_softwares[$i]);
			$nombres_soft[] = $soft->software;
		}
		if (count($ids_softwares) == 0) echo '<div class="tituloSinComponente">Sin Software</div>';
		else {*/
	?>
    		<textarea id="ids_softwares[]" name="ids_softwares[]" class="TextAreaOP" readonly="readonly" cols="1" rows="<?php // echo count($ids_softwares);?>"><?php // for($i=0;$i<count($nombres_soft);$i++) echo $nombres_soft[$i]."\n";?></textarea>
    	<?php
			// }
		?>
</div> -->
<br/>

<?php
	/* if (($id_cabina != 0 and ($id_cabina != -1))) { ?>
		<div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Referencias Cabina</div> 
           	<div class="tituloComponente">
               	<table id="tablaTituloPrototipo">
               		<tr>
               			<td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
               				<?php 
               					echo '<span class="tituloComp">'.$cabina->cabina.'_v'.$cabina->version.'</span>';
               				?>
               			</td>
                   		<td style="text-align:left; background:#fff; vertical-align:top; padding:0px 5px 0px 5px;">
                   			<?php
								if ($cabina->prototipo == 1) {
									echo '<span class="ImagenPrototipo"><img src="../images/prototipo.jpg" width="20px" height="20px" alt="PROTOTIPO" title="PROTOTIPO"></span>';
								}
								else if ($cabina->prototipo == 0){
									echo '<span class="ImagenPrototipo"><img src="../images/engranaje.gif" width="20px" height="20px" alt="PRODUCCION" title="PRODUCCION"></span>';
								}
							?>
                   		</td>
               		</tr>  
               	</table>
            </div>

            <div class="CajaReferencias">
				<div id="CapaTablaIframe">
    				<table id="mitablaCabina"><?php include ("../orden_produccion/muestra_referencias_cabina_mod_op.php") ?></table>
                    <?php $referencias_cabina = $resultadosBusquedaCabinas; ?>
                </div>
			</div> 
            <input type="button" id="mas" name="mas" class="BotonMas"  value="+" onclick="javascript:Abrir_ventana('buscador_referencias_cabina_mod_op.php')"/> 																																																																																																																																			
   			<input type="button" id="menos" name="menos" class="BotonMenos" value="-" onclick="javascript:removeRow(mitablaCabina)"  />
                
            <div class="tituloComponente">
               	<input type="checkbox" id="eliminar_cabina" name="eliminar_cabina" value="1" /> <div class="label_check_precios">Eliminar cabina</div>
            </div>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Coste Cabina</div> 
            <div class="tituloComponente">
				<table id="tablaTituloPrototipo">
                	<tr>
                		<td id="precio_cabina" style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;"><?php echo '<span class="tituloComp">'.number_format($precio_cabina, 2, ',', '.').'€'.'</span>';?></td>
                	</tr>
                </table>    
            </div>    
        </div>

	<?php
		$costeKitCabina = 0;
		// Obtenemos los ids de los kits de la cabina
		$orden_produccion->dameIdsKitComponente($id_cabina);
		for ($i=0;$i<count($orden_produccion->ids_kit);$i++){
			$Kit->cargaDatosKitId($orden_produccion->ids_kit[$i]["id_kit"]);
		?>
        	<div class="ContenedorCamposCreacionBasico">
           		<div class="LabelCreacionBasico">Referencias Kit</div>
                <div class="tituloComponente">
					<table id="tablaTituloPrototipo">
                		<tr>
                			<td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;"><?php echo '<span class="tituloComp">'.$Kit->kit.'</span>';?></td>
                    	</tr>    
                    </table>
    			</div>
                <div class="CajaReferencias">
					<div id="CapaTablaIframe">
    					<table id="mitablaKitCab<?php echo $i;?>">
							<?php include ("../orden_produccion/muestra_referencias_kits_mod_op.php"); ?> 
						</table>
                	</div>
                </div>
            </div>      
        	<div class="ContenedorCamposCreacionBasico">
           		<div class="LabelCreacionBasico">Coste Kit Cabina</div> 
            	<div class="tituloComponente">
					<table id="tablaTituloPrototipo">
                		<tr>
                			<td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;"><?php echo '<span class="tituloComp">'.number_format($precio_kit, 2, ',', '.').'€'.'</span>';?></td>
						</tr>
                	</table>    
            	</div>    
       		</div>
		<?php
			$costeKitsCabina = $costeKitsCabina + $precio_kit;
			}
		?>

		<div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Coste Total Cabina</div> 
            <div class="tituloComponente">
				<table id="tablaTituloPrototipo">
                	<tr>
                		<td id="precio_total_cabina" style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
   							<?php 
								$precio_total_cabina = $precio_cabina + $costeKitsCabina;
								echo '<input type="hidden" id="coste_total_cabina" name="coste_total_cabina" value="'.$precio_total_cabina.'"/>'; 
								echo '<input type="hidden" id="costeKitsCabina" name="costeKitsCabina" value="'.$costeKitsCabina.'"/>';
								echo '<span class="tituloComp">'.number_format($precio_total_cabina, 2, ',', '.').'€'.'</span>'; 
							?>
                    	</td>
                	</tr>
                </table>    
            </div>
        </div>
        <br/> 
<?php
    } // No hay cabina
	else {
?>
		<div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Referencias Cabina</div> 
           	<div class="tituloSinComponente"><?php echo "No hay cábina";?></div>
        </div>
<?php 
	}
	*/
	// Obtenemos el numero de perifericos para generar las tablas de referencias correspondientes a ese periferico
	$precio_todos_perifericos = 0;
	$es_periferico = true;
	for ($i=0;$i<count($ids_perifericos);$i++) {
		$precio_periferico = 0;
		$periferico->cargaDatosPerifericoId($ids_perifericos[$i]);
		$id_componente = $ids_perifericos[$i];

		echo '<br />';
		echo '<div class="ContenedorCamposCreacionBasico"><div class="LabelCreacionBasico">Referencias Periferico</div><div class="tituloComponente">';
		echo '<table id="tablaTituloPrototipo">
			<tr>
              	<td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">';
					echo '<span class="tituloComp">'.$periferico->periferico.'_v'.$periferico->version.'</span>
				</td>	
				<td style="text-align:left; background:#fff; vertical-align:top; padding:0px 5px 0px 5px;">';
					if ($periferico->prototipo == 1) {
						echo '<span class="ImagenPrototipo"><img src="../images/prototipo.jpg" width="20px" height="20px" alt="PROTOTIPO" title="PROTOTIPO"></span>';
					}
					else if ($periferico->prototipo == 0) {
						echo '<span class="ImagenPrototipo"><img src="../images/engranaje.gif" width="20px" height="20px" alt="PRODUCCION" title="PRODUCCION"></span>';
					}
		echo '</td></tr></table></div><div class="CajaReferencias"><div id="CapaTablaIframe"><table id="mitabla_'.$i.'">';	

		require ("../orden_produccion/muestra_referencias_perifericos_mod_op.php");
									
		echo '</table></div></div><input type="button" id="mas" name="mas" class="BotonMas"  value="+" onclick="javascript:Abrir_ventana('."'".'buscador_referencias_perifericos_mod_op.php?id='.$i."'".')"/> 																																																																																																																																			
   			<input type="hidden" id="periferico'.$i.'" name="periferico'.$i.'" value="'.$i.'" />
			<input type="button" id="menos" name="menos" class="BotonMenos" value="-" onclick="javascript:removeRowPeriferico(mitabla_'.$i.','.$i.')"/>';
			
		echo '<div class="tituloComponente"><input type="checkbox" id="eliminar_periferico-'.$i.'" name="eliminar_periferico-'.$i.'" value="1" /> <div class="label_check_precios">Eliminar periferico</div></div></div>';
		echo '<div class="ContenedorCamposCreacionBasico"><div class="LabelCreacionBasico">Coste Periferico</div><div class="tituloComponente"><table id="tablaTituloPrototipo"><tr><td id="precio_periferico_'.$i.'"  style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;"><span class="tituloComp">'.number_format($precio_periferico, 2, ',', '.').'€'.'</span></td></tr></table></div></div>';

		// Kits del Periferico			
		$orden_produccion->dameIdsKitComponente($ids_perifericos[$i]);
		$costeKitsPeriferico = 0;
		for ($k=0;$k<count($orden_produccion->ids_kit);$k++){
			$Kit->cargaDatosKitId($orden_produccion->ids_kit[$k]["id_kit"]); ?>
			<div class="ContenedorCamposCreacionBasico">
       			<div class="LabelCreacionBasico">Referencias Kit</div>
      			<div class="tituloComponente">
					<table id="tablaTituloPrototipo">
          				<tr>
          					<td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;"><?php echo '<span class="tituloComp">'.$Kit->kit.'</span>';?></td>
              			</tr>    
              		</table>
    			</div>
           		<div class="CajaReferencias">
					<div id="CapaTablaIframe">
    					<table id="mitablaKit<?php echo $k;?>Per<?php echo $i;?>">
							<?php include ("../orden_produccion/muestra_referencias_kits_mod_op.php");?>
                   		</table>
               		</div>
           		</div>
       		</div>

       		<div class="ContenedorCamposCreacionBasico">
        		<div class="LabelCreacionBasico">Coste Kit Periferico</div> 
        		<div class="tituloComponente">
					<table id="tablaTituloPrototipo">
            			<tr>
            				<td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;"><?php echo '<span class="tituloComp">'.number_format($precio_kit, 2, ',', '.').'€'.'</span>';?></td>
                		</tr>
                	</table>    
            	</div>    
        	</div>
    <?php
			$costeKitsPeriferico = $costeKitsPeriferico + $precio_kit;
		}
	?>   
			<div class="ContenedorCamposCreacionBasico">
           		<div class="LabelCreacionBasico">Coste Total Periferico</div> 
            	<div class="tituloComponente">
					<table id="tablaTituloPrototipo">
                		<tr>
                			<td id="precio_total_periferico_<?php echo $i;?>"style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
   								<?php 
									$precio_total_periferico = $precio_periferico + + $costeKitsPeriferico;
									$precio_todos_perifericos = $precio_todos_perifericos + $precio_total_periferico;
									echo '<span class="tituloComp">'.number_format($precio_total_periferico, 2, ',', '.').'€'.'</span>'; 
									echo '<input type="hidden" id="costeKitsPeriferico_'.$i.'" name="costeKitsPeriferico_'.$i.'" value="'.$costeKitsPeriferico.'"/>';
									echo '<input type="hidden" id="precio_tot_periferico_'.$i.'" name="precio_tot_periferico_'.$i.'" value="'.$precio_total_periferico.'"/>'; 
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
	// No hay perifericos 
	if(count($ids_perifericos) == 0) {
?>
		<div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Referencias Periféricos</div> 
           	<div class="tituloSinComponente"><?php echo "No hay periféricos";?></div>
        </div>
<?php 
	}
?>

	<br />
	<div class="ContenedorCamposCreacionBasico">
   		<div class="LabelCreacionBasico">Referencias Libres </div>
		<div class="CajaReferencias">
    		<div id="CapaTablaIframe">
   				<table id="mitablaRefLibres">
   					<?php include ("../orden_produccion/muestra_referencias_libres_mod_op.php");?>	
            	</table>  
        	</div>
    	</div> 
    
    	<input type="button" id="mas" name="mas" class="BotonMas"  value="+" onclick="javascript:Abrir_ventana('buscador_referencias_libres_mod_op.php')"/> 																																																																																																																																			
   		<input type="button" id="menos" name="menos" class="BotonMenos" value="-" onclick="javascript:removeRowRefLibres(mitablaRefLibres)"  />
	</div>
	<br/>

	<div class="ContenedorCamposCreacionBasico">
  		<div class="LabelCreacionBasico">Coste Refs Libres</div> 
    	<div class="tituloComponente">
			<table id="tablaTituloPrototipo">
    	    	<tr>
               		<td id="coste_ref_libres" style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
   						<?php 
							echo '<input type="hidden" id="coste_total_refs_libres" name="coste_total_refs_libres" value="'.$precio_refs_libres.'"/>'; 
							echo '<span class="tituloComp">'.number_format($precio_refs_libres, 2, ',', '.').'€'.'</span>'; 
						?>
	                </td>
	            </tr>
	        </table>    
	    </div>
	</div>

	<div class="ContenedorCamposCreacionBasico">
	   	<div class="LabelCreacionBasico">Coste Total Producto</div> 
	    <div class="tituloComponente">
			<table id="tablaTituloPrototipo">
	    	    <tr>
	              	<?php $precio_total_producto = 0;?>
	              	<td id="celda_total_producto" style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
	   					<?php 
							$precio_total_producto = /* $precio_total_cabina + */ $precio_todos_perifericos + $precio_refs_libres;
							echo '<span class="tituloComp">'.number_format($precio_total_producto, 2, ',', '.').'€'.'</span>'; 
						?>
	                </td>
	            </tr>
	        </table>    
	    </div>
	</div>
	<div class="ContenedorCamposCreacionBasico">
	  	<div class="LabelCreacionBasico">Coste Total Orden de Producción</div> 
	    <div class="tituloComponente">
			<table id="tablaTituloPrototipo">
	    		<tr>
	              	<td id="celda_coste_op" style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
	   				<?php 
						$precio_total_op = 0;
						$precio_total_op = $precio_total_producto * $unidades;
						echo '<span class="tituloComp">'.number_format($precio_total_op, 2, ',', '.').'€'.'</span>'; 
					?>
	            	</td>
	            </tr>
	        </table>    
	    </div>
	</div>
	<br />
	<div class="ContenedorBotonCreacionBasico">
	   	<input type="button" id="volver" name="volver" value="Volver" onclick="javascript:location='mod_orden_produccion.php?id_produccion=<?php echo $id_produccion;?>&id_producto=<?php echo $id_producto;?>'"/> 
	    <input type="hidden" id="guardandoOrdenProduccion" name="guardandoOrdenProduccion" value="1"/>
	    <input type="submit" id="continuar" name="continuar" value="Continuar" />
		<?php for ($i=0;$i<count($cliente);$i++) echo '<input type="hidden" id="ids_clientes[]" name="ids_clientes[]" value="'.$cliente[$i].'"/>';?>
	</div>
<?php
	if($mensaje_error != "") {
		echo '<div class="mensaje_error">'.$mensaje_error.'</div>'; 
	}
?>
<br />
</form>
