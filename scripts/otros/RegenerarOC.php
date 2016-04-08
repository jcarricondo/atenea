<?php 
include("../includes/sesion.php");
include_once("../classes/orden_produccion/orden_produccion.class.php");
include_once("../classes/orden_compra/orden_compra.class.php");
include_once("../classes/productos/producto.class.php"); 

$id_produccion = 6;
$unidades = 10;

// INICIAR ORDENES DE COMPRA EN ESTADO BORRADOR

// Tenemos que generar las Ordenes de compra en estado borrador. Para ello deberemos guardar:
// en la tabla Orden de Compra los proveedores asociados a esa orden de produccion.
// en la tabla Orden de Compra referencias las referencias totales agrupadas por id_componente
	
// Guardamos los ids de los productos de una Orden de Produccion
$orden_produccion = new Orden_Produccion();
$orden_produccion->dameIdsProductoOP($id_produccion); 
$ids_productos_op = $orden_produccion->ids_productos;
for($i=0;$i<$unidades;$i++) {
	$array_ids_productos[$i] = $ids_productos_op[$i]["id_producto"];
}	
			
// Guardamos los ids_proveedores asociados a cada producto. Como los productos tienen las mismas referencias nos vale con saber las referencias del primer producto
$producto = new Producto();
$producto->dameIdsProveedores($array_ids_productos[0]);
$ids_proveedores = $producto->ids_proveedores;
for ($i=0;$i<count($ids_proveedores);$i++) {
	$array_ids_proveedores[$i] = $ids_proveedores[$i]["id_proveedor"]; 
}
			
// Guardamos las ordenes de compra generadas en estado BORRADOR
		
$orden_compra = new Orden_Compra();
$fallo = false;
$i=0;
$j=0;
while ($i<count($array_ids_proveedores) and !$fallo) { 
	$j++;
	$numero_pedido = 'SMK_'.$j.'_'.$id_produccion.'_'.rand(0,10000);
	$estado = "GENERADA";
	$orden_compra->datosNuevaCompra($id_compra,$id_produccion,$array_ids_proveedores[$i],$numero_pedido,$fecha_pedido,$fecha_requerida,$direccion_entrega,$direccion_facturacion,$fecha_factura,$comentarios,$estado,$tasas,$unidades);
							
	$resultado = $orden_compra->guardarCambios();
							
	$fallo = $resultado != 1;
	$i++;
	if ($fallo == 1) {
		$mensaje_error = $orden_compra->getErrorMessage($resultado);
		$id_compra = $orden_compra->id_compra;
		$resultado = $orden_compra->eliminar($id_compra);
	}
}

if (!$fallo) {
	header("Location: OrdenesProduccion.php?OProduccion=regenerado");
}
?>
