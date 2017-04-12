<?php
// Este fichero muestra las referencias libres en la confirmación de la creación de una Orden de Producción
unset($referencias_componente);
for($i=0;$i<count($referencias_libres);$i++){
    $referencias_componente[$i]["id_referencia"] = $referencias_libres[$i];
    $referencias_componente[$i]["piezas"] = $piezas[$i];
}
$referencias_componente_her = $ref_heredada->obtenerHeredadas($referencias_componente);
$precio_refs_libres = $ref_heredada->damePrecioReferenciasHeredadas($referencias_componente_her);
$hay_heredadas = count($referencias_componente) != count($referencias_componente_her);
if($hay_heredadas) {
    $color_precio = ' style="color: orange"';
    $hay_alguna_heredada = true;
}
else $color_precio = ' style="color: #2998cc;"'; ?>

<div class="ContenedorCamposCreacionBasico">
    <div class="LabelCreacionBasico">Referencias Libres</div>
    <div class="CajaReferencias">
        <div id="CapaTablaIframe">
    	    <table id="mitablaRefsLibres">
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
            </tr>
            <?php
                $precio_refs_libres_tabla = 0;
                // Copiamos el array para el input hidden
                $ref_libres = $referencias_libres;
                for($i=0; $i<count($referencias_libres); $i++) {
                    // Se cargan los datos de las referencias según su identificador
                    $ref_libre->cargaDatosReferenciaLibreId($referencias_libres[$i]); ?>
                    <input type="hidden" id="ref_libres[]" name="ref_libres[]" value="<?php echo $ref_libres[$i];?>"/>
                    <input type="hidden" id="uds_paquete[]" name="uds_paquete[]" value="<?php echo $ref_libre->cantidad;?>"/>

                    <?php
                        // ref->cantidad hace referencia a unidades/paquete de la referencia. Las referencias libres no estan insertadas en componentes referencias por lo que tendremos que obtener los campos de piezas
                        // mediante post y total paquetes llamando a la funcion
                        $ref->calculaTotalPaquetes($ref_libre->cantidad, $piezas[$i]);
                        $total_paquetes = $ref->total_paquetes;

                        if($ref_libre->pack_precio <> 0 and $ref_libre->cantidad <> 0) $precio_unidad = $ref_libre->pack_precio / $ref_libre->cantidad;
                        else $precio_unidad = 00;
                        $precio_referencia = $piezas[$i] * $precio_unidad;
                        $precio_refs_libres_tabla = $precio_refs_libres_tabla + $precio_referencia; ?>

                    <input type="hidden" id="Piezas[]" name="Piezas[]" value="<?php echo $piezas[$i];?>"/>
                    <input type="hidden" id="tot_paquetes[]" name="tot_paquetes[]" value="<?php echo $total_paquetes;?>"/>

                    <tr>
                        <td style="text-align:center;"><?php echo $ref_libre->id_referencia; ?></td>
                        <td id="enlaceComposites">
                            <a href="../basicos/mod_referencia.php?id=<?php echo $ref_libre->id_referencia; ?>" target="_blank"/>
                            <?php
                                if (strlen($ref_libre->referencia) > $max_caracteres_ref) echo substr($ref_libre->referencia, 0, $max_caracteres_ref) . '...';
                                else echo $ref_libre->referencia;
                            ?>
                            </a>
                        </td>
                        <td>
                        <?php
                            if(strlen($ref_libre->proveedor) > $max_caracteres) echo substr($ref_libre->proveedor, 0, $max_caracteres) . '...';
                            else echo $ref_libre->proveedor;
                        ?>
                        </td>
                        <td><?php $ref_libre->vincularReferenciaProveedor(); ?></td>
                        <td>
                        <?php
                            if (strlen($ref_libre->nombre_pieza) > $max_caracteres) echo substr($ref_libre->nombre_pieza, 0, $max_caracteres) . '...';
                            else echo $ref_libre->nombre_pieza;
                        ?>
                        </td>
                        <td style="text-align:center"><?php echo number_format($piezas[$i], 2, ',', '.'); ?></td>
                        <td style="text-align:center"><?php echo number_format($ref_libre->pack_precio, 2, ',', '.'); ?></td>
                        <td style="text-align:center"><?php echo $ref_libre->cantidad; ?></td>
                        <td style="text-align:center"><?php echo $total_paquetes; ?></td>
                        <td style="text-align:center"><?php echo number_format($precio_unidad, 2, ',', '.'); ?></td>
                        <td style="text-align:center"><?php echo number_format($precio_referencia, 2, ',', '.'); ?></td>
                    </tr>
                <?php
                }
            ?>
            </table>
        </div>
    </div>
</div>
<div class="ContenedorCamposCreacionBasico">
    <div class="LabelCreacionBasico">Coste Refs Libres</div>
    <div class="tituloComponente">
        <table id="tablaTituloPrototipo">
        <tr>
            <td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
				<span class="tituloComp" <?php echo $color_precio;?>>
                    <?php echo number_format($precio_refs_libres, 2, ',', '.').'€';?>
                </span>
            </td>
        </tr>
        </table>
    </div>
</div>
<br/>
