<?php
include("../includes/sesion.php");
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/orden_compra/orden_compra.class.php");
include("../classes/productos/producto.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/control_usuario.class.php");
permiso(11);

$db = new MySQL();
$orden_produccion = new Orden_Produccion();
$producto = new Producto();
$orden_compra = new Orden_Compra();
$almacen = new Almacen();
$control_usuario = new Control_Usuario();

// Obtenemos el tipo de usuario para conocer su sede
$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
// Obtenemos la sede a la que pertenece el usuario 
$id_almacen = $_SESSION["AT_id_almacen"];
$id_sede = $almacen->dameSedeAlmacen($id_almacen);
$id_sede = $id_sede["id_sede"];

$orden_produccion->cargaDatosProduccionId($_GET["id"]);
$id_produccion = $orden_produccion->id_produccion;
$unidades = $orden_produccion->unidades;
$codigo = $orden_produccion->codigo;
$fecha_inicio = $orden_produccion->fecha_inicio;
$fecha_entrega = $orden_produccion->fecha_entrega;
$fecha_entrega_deseada = $orden_produccion->fecha_entrega_deseada;
$estado = $orden_produccion->estado;
$comentarios = $orden_produccion->comentarios;
$fecha_creado = $orden_produccion->fecha_creado;
$activo = $orden_produccion->activo;
$alias_op = $orden_produccion->alias_op;
$id_tipo = $orden_produccion->id_tipo;
$fecha_inicio_construccion = $orden_produccion->fecha_inicio_construccion;

$orden_produccion->datosNuevaProduccion($id_produccion,$unidades,$codigo,$id_tipo,$fecha_inicio,$fecha_entrega,$fecha_entrega_deseada,$estado,$comentarios,$alias_op,$fecha_inicio_construccion,$id_sede);

// Eliminamos las Ordenes de Compra y las referencias de las ordenes de compra asociadas a esa Orden de Produccion
// Para ello buscamos las referencias de Ordenes de Compra con esa id_produccion
$orden_produccion->dameIdsOrdenesCompra($id_produccion);
$ids_orden_compra_aux = $orden_produccion->ids_orden_compra;
for($i=0;$i<count($ids_orden_compra_aux);$i++) {
	$ids_orden_compra[] = $ids_orden_compra_aux[$i]["id_orden_compra"];	
}

// Desactivar Referencias de Ordenes de Compra con id_compra perteneciente a id_produccion
$fallo = false;
$i=0;
while ($i<count($ids_orden_compra) && (!$fallo)){
	$resultado = $orden_compra->desactivarOrden_Compra_Referencias($ids_orden_compra[$i]);	
	$i++;
	if ($resultado != 1) $fallo = true;
}

// Desactivamos las facturas de las Ordenes de compra con id_compra perteneciente a la id_produccion
if (!$fallo) {
	$i=0;
	while ($i<count($ids_orden_compra) && (!$fallo)){
		$resultado = $orden_compra->desactivarOrden_Compra_Facturas($ids_orden_compra[$i]);
		$i++;
		if ($resultado != 1) $fallo = true;
	}
	
	if (!$fallo) {
		$resultado = $orden_produccion->desactivarOrdenCompraPorIdProduccion($id_produccion);
		if ($resultado != 1) $fallo = true;
		
		if (!$fallo) {
			$resultado = $orden_produccion->eliminar();
			if($resultado == 1) {
				// Ahora eliminamos los productos asociados a la Orden de Produccion eliminada
				$orden_produccion->dameIdsProductoOP($id_produccion);
				$ids_productos = $orden_produccion->ids_productos;

				// Poner activo=0 todos los componentes de las tablas de los productos asignados a la orden de produccion a modificar				
				for ($i=0;$i<count($ids_productos);$i++) {
					$array_productos[]=$ids_productos[$i]["id_producto"];
				}
	
				// Ponemos en la tabla productos activo=0, los productos de la orden de produccion que se va a modificar
				$fallo = false;
				$resultado = $orden_produccion->desactivarProductos($id_produccion);
				if ($resultado != 1) $fallo = true;
			
				// Ponemos activo=0, los componentes de los productos de la orden de produccion que se va a modificar 
				if (!$fallo) {
					$resultado = $orden_produccion->desactivarProductosComponentes($id_produccion);
					if ($resultado != 1) $fallo = true;
				}
	
				// Ponemos activo=0 las referencias de los componentes de la orden de produccion que se va a modificar 
				if (!$fallo) {
					$resultado = $orden_produccion->desactivarProductosReferencias($id_produccion);
					if ($resultado != 1) $fallo = true;
				}

				if ($resultado == 1) {
					header("Location: ordenes_produccion.php?OProduccion=eliminado&tipo=".$id_tipo);
				}
				else {
					$mensaje_error = $orden_produccion->getErrorMessage($resultado);
				}
			}
			else {
				// Fallo al borrar las orden de produccion
				$mensaje_error = $orden_produccion->getErrorMessage($resultado);	
			}
		} 
		else {
			// Fallo al borrar las ordenes de compra con id = id_produccion
			$mensaje_error = $orden_produccion->getErrorMessage($resultado);
		}
	}
	else {
		// Fallo al borrar las facturas de las ordenes de Compra
		$mensaje_error = $orden_compra->getErrorMessage($resultado);	
	}
}
else {
	// Fallo al borrar las referencias de las ordenes de Compra
	$mensaje_error = $orden_compra->getErrorMessage($resultado);
}
