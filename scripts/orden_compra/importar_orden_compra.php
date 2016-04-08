<?php 
set_time_limit(10000);
// Script parar importar las ordenes de compra de TORO
include("../../classes/mysql.class.php");
include("../../classes/basicos/proveedor.class.php");
include("../../classes/basicos/nombre_producto.class.php");
include("../../classes/productos/producto.class.php");
include("../../classes/orden_produccion/orden_produccion.class.php");
include("../../classes/orden_compra/orden_compra.class.php");
include("../../classes/funciones/log_unificacion.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySql();
$op = new Orden_Produccion();
$oc = new Orden_Compra();
$proveedor = new Proveedor();
$nombre_producto = new Nombre_Producto();
$producto = new Producto();
$log = new Log_Unificacion();

echo '<br/>OCS de TORO<br/>';
// Importamos las OCs de TORO que no existan en SMK
$consulta = "select * from orden_compra_toro where activo=1";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res_toro = $db->getResultados();

for($i=0;$i<count($res_toro);$i++){
	$id_produccion = $res_toro[$i]["id_produccion"];
	$id_proveedor = $res_toro[$i]["id_proveedor"];
	$orden_compra = $res_toro[$i]["orden_compra"];
	$numero_pedido = $res_toro[$i]["numero_pedido"];
	$fecha_pedido = $res_toro[$i]["fecha_pedido"];
	$fecha_entrega = $res_toro[$i]["fecha_entrega"];
	$direccion_entrega = $res_toro[$i]["direccion_entrega"];
	$direccion_facturacion = $res_toro[$i]["direccion_facturacion"];
	$estado = $res_toro[$i]["estado"];
	$tasas = $res_toro[$i]["tasas"];
	$fecha_requerida = $res_toro[$i]["fecha_requerida"];
	$fecha_factura = $res_toro[$i]["fecha_factura"];

	// Comprobamos cual es la Orden de Produccion equivalente de TORO
	$consulta_op = sprintf("select * from orden_produccion_toro where activo=1 and id_produccion=%s",
		$db->makeValue($id_produccion, "int"));
	$db->setConsulta($consulta_op);
	$db->ejecutarConsulta();
	$res_op = $db->getPrimerResultado();
	$alias_toro = $res_op["alias"];

	// Si existe la OP 
	if($res_op != NULL){
		// Buscamos el nuevo id_produccion del OP con ese alias
		$consulta_op = sprintf("select * from orden_produccion where activo=1 and alias=%s",
			$db->makeValue($alias_toro,"text"));
		$db->setConsulta($consulta_op);
		$db->ejecutarConsulta();
		$res_op = $db->getPrimerResultado();
		$id_produccion_new = $res_op["id_produccion"];

		// Comprobamos si coincide el proveedor
		$consulta_prov = sprintf("select * from proveedores_toro where activo=1 and id_proveedor=%s",
			$db->makeValue($id_proveedor, "int"));
		$db->setConsulta($consulta_prov);
		$db->ejecutarConsulta();
		$res_prov = $db->getPrimerResultado();
		$nombre_prov = $res_prov["nombre_prov"];

		// Buscamos el proveedor actualizado
		$consulta_prov = sprintf("select * from proveedores where activo=1 and nombre_prov=%s",
			$db->makeValue($nombre_prov, "text"));
		$db->setConsulta($consulta_prov);
		$db->ejecutarConsulta();
		$res_prov = $db->getPrimerResultado();
		$id_proveedor_new = $res_prov["id_proveedor"];

		if($id_proveedor_new != NULL){
			if($id_proveedor != $id_proveedor_new){
				d($id_proveedor);
				d($id_proveedor_new);
			}	

			// Generamos el nuevo nombre de la OC
			$proveedor->cargaDatosProveedorId($id_proveedor_new);
			$nombre_orden_compra = 'OP'.$id_produccion_new.$proveedor->nombre;

			// Obtenemos las unidades de la OP
			$op->cargaDatosProduccionId($id_produccion_new);
			$unidades = $op->unidades;

			// Buscamos el id_nombre_producto para generar el numero de pedido
			$op->dameIdProducto($id_produccion_new);
			$id_producto = $op->id_producto;
			$id_producto = $id_producto["id_producto"];

			$producto->dameIdsNombreProducto($id_producto);
			$id_nombre_producto = $producto->id_nombre_producto;
			$id_nombre_producto = $id_nombre_producto["id_nombre_producto"];

			// Cargamos los datos de la clase Nombre_Producto
			$nombre_producto->cargaDatosNombreProductoId($id_nombre_producto);
			$nombre_producto_aux = $nombre_producto->nombre;
			$codigo_nombre_producto = $nombre_producto->codigo;

			$producto->dameUltimoContadorPedido();							
			$ultimo_id = $producto->id_contador_pedido["id"];

			$producto->AumentaContadorPedido($ultimo_id);
			$ultimo_id = $producto->id_contador_pedido;
			$numero_pedido = $nombre_producto_aux.'_'.$unidades.'_'.$ultimo_id;

			$consulta = sprintf("insert into orden_compra(id_produccion,id_proveedor,orden_compra,numero_pedido,fecha_pedido,fecha_requerida,fecha_factura,estado,tasas,fecha_creado,direccion_entrega,direccion_facturacion,activo) 
									value (%s,%s,%s,%s,%s,%s,%s,%s,%s,current_timestamp,%s,%s,1)",
	    		$db->makeValue($id_produccion_new, "int"),
	    		$db->makeValue($id_proveedor_new, "int"),
			    $db->makeValue($nombre_orden_compra, "text"),
			    $db->makeValue($numero_pedido, "text"),
			    $db->makeValue($fecha_pedido, "date"),
			    $db->makeValue($fecha_requerida, "date"),
			    $db->makeValue($fecha_factura, "date"),
			    $db->makeValue($estado, "text"),
			    $db->makeValue($tasas, "float"),
			    $db->makeValue($direccion_entrega, "text"),
			    $db->makeValue($direccion_facturacion, "text"));
			$db->setConsulta($consulta);
		   	if($db->ejecutarSoloConsulta()) {
		   		// Insertamos el log
				$mensaje_log = '<span style="color:green">La Orden de Compra ['.$nombre_orden_compra.'] se ha importado correctamente</span><br/><br/>';
				$log->datosNuevoLog(NULL,"IMPORTAR_OC (TORO)",$mensaje_log,$fecha);
				$res_log = $log->guardarLog();
				if($res_log == 1){
					echo $mensaje_log;
				}
				else echo 'Se produjo un error al guardar el LOG';
			}
			else {
				echo 'Se ha producido un error al importar las OCS';
			}
		}
	}
}
?>
