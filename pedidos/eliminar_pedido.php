<?php
include("../classes/mysql.class.php");
include("../classes/pedidos/pedido.class.php");
$db = new MySQL();
$pedido = new Pedido();
$pedido->cargarPedidoId($_GET["id"]);
if($pedido->eliminarPedido()) {
	header("Location: pedidos.php?cab=eliminado");
} else {
	header("Location: pedidos.php?cab=eliminado_error");
}

