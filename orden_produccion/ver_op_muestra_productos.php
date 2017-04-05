<?php
// Este fichero muestra los productos en la ficha de una orden de producción
?>
<div class="ContenedorCamposCreacionBasico">
    <div class="LabelCreacionBasico">Productos</div>
    <div class="CajaReferencias">
        <div id="CapaTablaIframe">
            <table id="mitabla">
            <tr>
                <th>NUM SERIE</th>
                <th>NOMBRE</th>
                <th>F. ENT</th>
                <th>F. ENT. PREV.</th>
                <th>CLIENTE</th>
                <th>PEDIDO</th>
                <th>ESTADO</th>
            </tr>
            <?php
                // Se cargan los productos de la orden de producción "id_produccion"
                $orden_produccion->dameIdsProductoOP($id_produccion);
                for ($i=0;$i<$unidades;$i++) {
                    $id_producto = $orden_produccion->ids_productos[$i]["id_producto"];
                    $producto->cargaDatosProductoId($id_producto);
                    $producto->dameNumSerie($id_producto);
                    $producto->dameIdCliente($id_producto);

                    $id_nombre_producto = $producto->id_nombre_producto;
                    $nombre_producto->cargaDatosNombreProductoId($id_nombre_producto);
                    $nombre_prod = $nombre_producto->nombre;
                    $id_cliente = $producto->id_cliente["id_cliente"];
                    $cliente->cargaDatosClienteId($id_cliente);
                    $nombre_cliente = $cliente->nombre;
                    $id_pedido = $producto->id_pedido;
                    $pedido->cargarPedidoId($id_pedido);
                    $numero_pedido = $pedido->numero_pedido; ?>

                    <tr>
                        <td><?php echo $producto->num_serie["num_serie"]; ?></td>
                        <td><?php echo $nombre_prod; ?></td>
                        <td><?php echo $funciones->cFechaNormal($producto->fecha_entrega); ?></td>
                        <td><?php echo $funciones->cFechaNormal($producto->fecha_entrega_prevista); ?></td>
                        <td>
                        <?php
                            if($nombre_cliente != NULL) echo $nombre_cliente;
                            else echo "-";
                        ?>
                        </td>
                        <td>
                        <?php
                            if($nombre_pedido != NULL) echo $numero_pedido;
                            else echo "-";
                        ?>
                        </td>
                        <td>
                        <?php
                            if ($producto->estado_producto == "ENTREGADO") echo '<span style="color:green;">'.$producto->estado_producto.'</span>';
                            else echo $producto->estado_producto;
                        ?>
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
