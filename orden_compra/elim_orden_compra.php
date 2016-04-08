<?php
// Este fichero elimina las ordenes de compra 
include("../classes/mysql.class.php");
include("../classes/orden_compra/orden_compra.class.php");
include("../classes/productos/producto.class.php");
permiso(14);

$db = new MySQL();
$orden_compra = new Orden_Compra();

$orden_compra->cargaDatosOrdenCompraId($_GET["id"]);
$id_compra = $orden_compra->id_compra;
$id_produccion = $orden_compra->id_produccion;
$id_proveedor = $orden_compra->id_proveedor;
$nombre_orden_compra = $orden_compra->nombre_orden_compra;
$numero_pedido = $orden_compra->numero_pedido;
$fecha_pedido = $orden_compra->fecha_pedido;
$fecha_entrega = $orden_compra->fecha_entrega;
$fecha_requerida = $orden_compra->fecha_requerida;
$direccion_entrega = $orden_compra->direccion_entrega;
$fecha_factura = $orden_compra->fecha_factura;
$comentarios = $orden_compra->comentarios;
$estado = $orden_compra->estado;

$orden_compra->datosNuevaCompra($id_compra,$id_produccion,$id_proveedor,$numero_pedido,$fecha_pedido,$fecha_requerida,$direccion_entrega,$direccion_facturacion,$fecha_factura,$comentarios,$estado_anterior,$estado,$precio_tasas,$unidades,$fecha_entrega,$nombre_orden_compra);
$resultado = $orden_compra->eliminar($id_compra);
if ($resultado == 5 ) {
	$resultado = $orden_compra->desactivarOrden_Compra_Referencias($id_compra);
	if ($resultado == 1) {
		$resultado = $orden_compra->desactivarOrden_Compra_Facturas($id_compra);
		if ($resultado == 1) {
			header("Location: ordenes_compra.php?OCompra=eliminado");		
		}
		else {
			$mensaje_error = $orden_compra->getErrorMessage($resultado);	
		}
	}
	else {
		$mensaje_error = $orden_compra->getErrorMessage($resultado);		
	}
}
else {
	$mensaje_error = $orden_compra->getErrorMessage($resultado);	
}
?>