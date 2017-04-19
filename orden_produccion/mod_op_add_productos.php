<?php
// Este fichero muestra los productos en el proceso de modificación de una Orden de Producción
$op->dameIdsProductoOP($id_produccion);
?>

<div class="ContenedorCamposCreacionBasico">
    <div class="LabelCreacionBasico">Productos</div>
    <div class="CajaReferencias">
        <div id="CapaTablaIframe">
            <table id="mitabla-productos">
            <tr>
                <th>NUM SERIE</th>
                <th style="text-align:center">CLIENTE</th>
            </tr>
            <?php
                for ($i=0;$i<$unidades;$i++) {
                    $id_producto = $op->ids_productos[$i]["id_producto"];
                    $producto->dameNumSerie($id_producto);
                    $producto->dameIdCliente($id_producto);
                    $id_cliente = $producto->id_cliente["id_cliente"];
                    $client->cargaDatosClienteId($id_cliente);
                    $nombre_cliente = $client->nombre; ?>

                    <tr>
                        <td><?php echo $producto->num_serie["num_serie"]; ?></td>
                        <td style="text-align:center">
                            <select id="cliente[]" name="cliente[]" class="ListadoClientesOP">
                            <?php
                                $listado_client->prepararConsulta();
                                $listado_client->realizarConsulta();
                                $resultado_clientes = $listado_client->clientes;

                                for($j=-1;$j<count($resultado_clientes);$j++) {
                                    $datoCliente = $resultado_clientes[$j];
                                    $client->cargaDatosClienteId($datoCliente["id_cliente"]); ?>
                                    <option value="<?php echo $client->id_cliente;?>"
                                    <?php
                                        if($client->id_cliente == $id_cliente) echo 'selected="selected"';
                                        echo '>'.$client->nombre; ?>
                                    </option>
                            <?php
                                }
                            ?>
                            </select>
                        </td>
                    </tr>
            <?php
                }
            ?>
            </table>
        </div>
    </div>
</div>
<br/>
<br/>

