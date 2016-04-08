<?php
// Este fichero inicia la Orden de Produccion
include("../classes/mysql.class.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/cliente.class.php");
include("../classes/basicos/nombre_producto.class.php");
include("../classes/productos/producto.class.php");
include("../classes/pedidos/pedido.class.php");

$id_producto = $_GET["id_producto"];

$bbdd = new MySQL;
$producto = new Producto();
$funciones = new Funciones();
$pedido = new Pedido();
// Carga de datos del producto
$producto->cargaDatosProductoId($id_producto);
$pedido->cargarPedidoId($producto->id_pedido);
$num_serie = $producto->num_serie;

?>
<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
<div id="MuestraReferencias">
    <h2><?php echo $num_serie;?></h2>
    <br/>
    <div id="CapaTablaReferencias">
		<table>
			<tr>
				<th colspan="2" style="text-align: center;">Datos Producto</th>
			</tr>
			<tr>
				<th width="100">Tipo Producto</th>
				<td><?php echo $producto->id_nombre_producto; ?></td>
			</tr>
			<tr>
				<th width="100">Número serie</th>
				<td><?php echo $producto->num_serie; ?></td>
			</tr>
			<tr>
				<th width="100">Fecha creado</th>
				<td><?php echo $funciones->cFechaNormal($producto->fecha_creado); ?></td>
			</tr>
			<tr>
				<th width="100">Fecha entregado</th>
				<td><?php echo $funciones->cFechaNormal($producto->fecha_entrega); ?></td>
			</tr>
		</table>
		<br />
		<table>
			<tr>
				<th colspan="2" style="text-align: center;">Datos Pedido</th>
			</tr>
			<tr>
				<th width="100">Número pedido</th>
				<td><?php echo $pedido->numero_pedido; ?></td>
			</tr>
			<tr>
				<th width="100">Fecha creado</th>
				<td><?php echo $funciones->cFechaNormal($pedido->fecha_pedido); ?></td>
			</tr>
			<tr>
				<th width="100">Estado</th>
				<td><?php echo $pedido->estado; ?></td>
			</tr>
		</table>
	</div>
</div>