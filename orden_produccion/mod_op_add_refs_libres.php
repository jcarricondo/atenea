<?php
// Este fichero muestra las referencias libres en el proceso de modificación de una Orden de Producción
?>
<div class="ContenedorCamposCreacionBasico">
    <div class="LabelCreacionBasico">Referencias Libres </div>
    <div class="CajaReferencias">
        <div id="CapaTablaIframe">
    	    <table id="mitablaRefsLibres">
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
	            // Obtenemos las piezas la tabla orden_produccion_referencias y los datos de la tabla referencias
	            $precio_total = 0;
				$fila = 0;

                $referencias_libres = $op->cargaDatosPorProduccionComponente($id_produccion,0);
				// Hacemos la carga de la tabla referencias
				for ($i=0;$i<count($referencias_libres);$i++){
                    $id_referencia_libre = $referencias_libres[$i]["id_referencia"];
                    $piezas = $referencias_libres[$i]["piezas"];

                    $ref->cargaDatosReferenciaId($id_referencia_libre);
                    if ($ref->pack_precio <> 0 and $ref->unidades <>0) $precio_unidad = $ref->pack_precio / $ref->unidades;
                    else $precio_unidad = 0;

                    $ref->calculaTotalPaquetes($ref->unidades,$piezas);
                    $total_paquetes = $ref->total_paquetes;
                    $precio_referencia = $piezas * $precio_unidad;
                    $precio_total = $precio_total + $precio_referencia; ?>

                    <tr>
                        <td style="text-align:center"><?php echo $ref->id_referencia;?></td>
                        <td id="enlaceComposites">
                            <a href="../basicos/mod_referencia.php?id=<?php echo $ref->id_referencia; ?>"/><?php echo $ref->referencia; ?></a>
                            <input type="hidden" name="REFS[]" id="REFS[]" value="<?php echo $ref->id_referencia; ?>"/>
                        </td>
                        <td><?php echo $ref->nombre_proveedor; ?></td>
                        <td><?php echo $ref->vincularReferenciaProveedor();?></td>
                        <td><?php echo $ref->part_nombre; ?></td>
                        <td style="text-align:center">
                            <input type="text" name="piezas[]" id="piezas[]" class="CampoPiezasInput" value="<?php echo $piezas; ?>" onblur="validarPiezasCorrectas(<?php echo $fila; ?>)"/>
                        </td>
                        <td style="text-align:center"><?php echo number_format($ref->pack_precio, 2, '.', ''); ?></td>
                        <td style="text-align:center"><?php echo $ref->unidades; ?></td>
                        <td style="text-align:center"><?php echo $total_paquetes; ?></td>
                        <td style="text-align:center"><?php echo number_format($precio_unidad, 2, '.', ''); ?></td>
                        <td style="text-align:center"><?php echo number_format($precio_referencia, 2, '.', ''); ?></td>
                        <td style="text-align:center"><input type="checkbox" name="chkbox" value="<?php echo $id_referencia_libre; ?>"/></td>
                    </tr>
                    <?php $fila = $fila + 1; ?>
                    <input type="hidden" name="fila" id="fila" value="<?php echo $fila;?>"/>
            <?php
                }
			?>
            </table>
        </div>
    </div>
    <input type="button" id="mas" name="mas" class="BotonMas"  value="+" onclick="Abrir_ventana('buscador_referencias_libres.php')"/>
    <input type="button" id="menos" name="menos" class="BotonMenos" value="-" onclick="removeRow(mitabla)"  />
</div>
<br/>
<div class="ContenedorCamposCreacionBasico">
    <div class="LabelCreacionBasico">Coste Referencias Libres </div>
    <label id="precio_refs_libres" class="LabelPrecio"><?php echo number_format($precio_total, 2, ',', '.').'€';?></label>
</div>
<br/>
<br/>