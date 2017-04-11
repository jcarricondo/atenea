<?php 
// Este fichero contiene el codigo HTML y PHP del contenedor central de la Confirmación de la Modificación de una Orden de Producción
?> 
<h3> Confirmaci&oacute;n de la nueva Orden de Producci&oacute;n </h3>
<form id="FormularioCreacionBasico" name="confirmModOrdenProduccion" onsubmit="return validarFormulario()" action="confirm_mod_op_her.php?id_produccion=<?php echo $id_produccion;?>" method="post">
<br />
<h5> Datos de la nueva Orden de Producción </h5>
<?php
	if($esAdminGlobal || $esAdminGes){
		// ADMINISTRADOR GLOBAL. Elige la sede de la OP
	    if($id_sede == 1) $nombre_sede = "SIMUMAK";
	    else if($id_sede == 2) $nombre_sede = "TORO"; ?>

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

	<div class="ContenedorCamposCreacionBasico">
		<div class="LabelCreacionBasico">Perif&eacute;ricos</div>
		<?php
			for ($i=0;$i<count($ids_perifericos);$i++){
				echo '<input type="hidden" id="IDS_PERS[]" name="IDS_PERS[]" value="'.$ids_perifericos[$i].'"/>';
				$per->cargaDatosPerifericoId($ids_perifericos[$i]);
				$nombres_per[] = $per->periferico.'_v'.$per->version;
				echo '<input type="hidden" id="perifericos_nombres[]" name="perifericos_nombres[]" value="'.$nombres_per[$i].'"/>';
			}
			if (count($ids_perifericos) == 0) echo '<div class="tituloSinComponente">Sin Periféricos</div>';
			else { ?>
				<textarea id="ids_perifericos[]" name="ids_perifericos[]" class="TextAreaOP" readonly="readonly" cols="1" rows="<?php echo count($ids_perifericos);?>"><?php for($i=0;$i<count($nombres_per);$i++) echo $nombres_per[$i]."\n";?></textarea>
		<?php
			}
		?>
	</div>
	<br/>

	<?php
		if ($tieneKitsLibres) { ?>
			<div class="ContenedorCamposCreacionBasico">
				<div class="LabelCreacionBasico">Kits Libres</div>
				<?php
					for ($i=0; $i<count($ids_kits_libres); $i++) {
						echo '<input type="hidden" id="IDS_KITS_LIBRES[]" name="IDS_KITS_LIBRES[]" value="'.$ids_kits_libres[$i].'"/>';
						$kit->cargaDatosKitId($ids_kits_libres[$i]);
						$nombres_kit[] = $kit->kit.'_v'.$kit->version;
						echo '<input type="hidden" id="kits_libres_nombres[]" name="kits_libres_nombres[]" value="'.$nombres_kit[$i].'"/>';
					} ?>
					<textarea id="ids_kits_libres[]" name="ids_kits_libres[]" class="TextAreaOP" readonly="readonly" cols="1" rows="<?php echo count($ids_kits_libres); ?>"><?php for ($i=0; $i<count($nombres_kit); $i++) echo $nombres_kit[$i]."\n"; ?></textarea>
			</div>
			<br/>
	<?php
		}
	?>

<?php
	include("confirm_mod_op_muestra_perifericos.php");
	if($tieneKitsLibres)include("confirm_mod_op_muestra_kits_libres.php");
	include("confirm_mod_op_muestra_refs_libres.php");
?>

	<div class="ContenedorCamposCreacionBasico">
	   	<div class="LabelCreacionBasico">Coste Total Producto</div> 
	    <div class="tituloComponente">
			<table id="tablaTituloPrototipo">
	    	<tr>
	        	<?php $precio_total_producto = 0;?>
	            <td id="celda_total_producto" style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
	   			<?php
					$precio_total_producto = $precio_todos_perifericos + $precio_todos_kits_libres + $precio_refs_libres;
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
	   	<input type="button" id="volver" name="volver" value="Volver" onclick="location='mod_orden_produccion.php?id_produccion=<?php echo $id_produccion;?>&id_producto=<?php echo $id_producto;?>'"/>
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
