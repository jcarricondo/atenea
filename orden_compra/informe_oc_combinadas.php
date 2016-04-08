<?php 
//Este fichero genera un excel con el listado de las órdenes de compra seleccionadas
include("../includes/sesion.php");
include("../classes/basicos/proveedor.class.php");
include("../classes/basicos/nombre_producto.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/orden_compra/orden_compra.class.php");
include("../classes/productos/producto.class.php");

// Obtenemos los ids por url. Si hay varios, los extraemos uno a uno. 
$ids = $_GET["ids_compra"];
$ids_compra = explode(",",$ids);
$salida = "";

$db = new MySQL();
$orden_compra = new Orden_Compra(); 
$orden_produccion = new Orden_Produccion();
$producto = new Producto();
$nom_prod = new Nombre_Producto();
$proveedor = new Proveedor();
// Si no se ha seleccionado ninguna OC
if ($ids_compra[0] != ""){
	for($i=0; $i<count($ids_compra); $i++){
		// Cargamos los datos de las Ordenes de Compra 
		$orden_compra->cargaDatosOrdenCompraId($ids_compra[$i]);
		// Obtenemos el id_producto de la Orden de Produccion para poder sacar el nombre del producto  
		$orden_produccion->dameIdProductoSinActivo($orden_compra->id_produccion);
		$id_producto = $orden_produccion->id_producto["id_producto"];
		// Obtenemos el nombre del producto de la Orden de Produccion
		$producto->dameIdsNombreProducto($id_producto);
		$id_nombre_producto = $producto->id_nombre_producto["id_nombre_producto"];
		$orden_produccion->cargaDatosProduccionId($orden_compra->id_produccion);

		if($orden_produccion->id_tipo == 2) $nombre_produccion = $orden_produccion->codigo;
		else {
			$nom_prod->cargaDatosNombreProductoId($id_nombre_producto);
			$nombre_produccion = $nom_prod->nombre."_".$orden_compra->id_produccion;
		}
				
		// Hacemos la carga del proveedor de la Orden de Compra para poder calcular las fechas.
		$proveedor->cargaDatosProveedorId($orden_compra->id_proveedor);
		$tiempo_suministro = $proveedor->tiempo_suministro;
		$metodo_pago = $proveedor->metodo_pago;
				
		$fecha_pedido = $orden_produccion->cFechaNormal($orden_compra->fecha_pedido);
		$fecha_entrega = $orden_produccion->cFechaNormal($orden_compra->fecha_entrega);
				
		// Si la fecha_entrega no esta guardada en la BBDD la calculamos al vuelo
		// FP
		// FE = FP + tiempo suministro proveedor
		// Fpg = FE + metoddo de pago
	
		if ($fecha_entrega == NULL){
			$dias = 0;
			if ($tiempo_suministro == 0) {
				$dias = $dias + 0;
				$fe_no_calculada = true;
				$fp_no_calculada = true;
				$error_fechas = true;
			}
			else if ($tiempo_suministro == 1) $dias = $dias + 7;
			else if ($tiempo_suministro == 2) $dias = $dias + 14;
			else if ($tiempo_suministro == 3) $dias = $dias + 30;
			else if ($tiempo_suministro == 4) $dias = $dias + 60;
			else $dias = $dias + 90;
	
			$fecha_pedido = $orden_produccion->cFechaMyEsp($fecha_pedido);
			$fecha_entrega = date("m/d/Y", strtotime($fecha_pedido." +".$dias." days"));
			$fecha_pedido = $orden_produccion->cFechaMyEsp($fecha_pedido);	
		}
		else {
			$fecha_entrega = $orden_produccion->cFechaNormal($fecha_entrega);
			$fecha_entrega = $orden_produccion->cFechaMyEsp($fecha_entrega);
		}
				
		$dias = 0;
		if ($metodo_pago == 0 ){
			$dias = $dias + 0;
			$fp_no_calculada = true;
			$error_fechas = true;
		}
		else if ($metodo_pago == 1) $dias = $dias + 0;
		else if ($metodo_pago == 2) $dias = $dias + 30;
    	else if ($metodo_pago == 3) $dias = $dias + 60;
    	else $dias = $dias + 90;				
							
		$fecha_pago = date("m/d/Y", strtotime($fecha_entrega." +".$dias." days"));

		$fecha_entrega = $orden_produccion->cFechaMyEsp($fecha_entrega);
		$fecha_pago = $orden_produccion->cFechaMyEsp($fecha_pago);
	
		// Llamamos a la consulta para calcular el precio total de la Orden de Compra	
		$orden_compra->damePrecioOC($orden_compra->id_compra,$orden_compra->id_proveedor);
		$precio = $orden_compra->precio[0]["precio"];
		$precio_tasas = $orden_compra->tasas;
		$precio_total = $precio + $precio_tasas;
		$precio_total = number_format($precio_total, 2, ',', '');
	
		$salida .= '
		<table>
		<tr>
			<td>'.$nombre_produccion.'</td>
			<td>OP'.$orden_compra->id_produccion.$orden_compra->nombre_prov.'</td>
			<td>'.$orden_compra->nombre_prov.'</td>
			<td align="center">'.$orden_compra->id_produccion.'</td>
			<td align="center">'.$fecha_pedido.'</td>
			<td align="center">'.$fecha_entrega.'</td>
			<td align="center">'.$fecha_pago.'</td>
			<td>'.$orden_compra->estado.'</td>
			<td align="right">'.$precio_total.'</td>
		</tr>
		</table>';
	}
}

$table = '<table>
	<tr>
    	<th>NOMBRE PRODUCCION</th>
        <th>BOLSA DE GASTOS</th>
        <th>PROVEEDOR</th>   
        <th>ORDEN DE PRODUCCION</th>
        <th>FECHA DE PEDIDO</th>  
        <th>FECHA ENTREGA PREVISTA</th>
        <th>FECHA PAGO PREVISTA</th> 
        <th>ESTADO</th>  
        <th>IMPORTE</th>
    </tr>';
$table_end = '</table>';
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=informeOrdenesCompra.xls");
echo $table.$salida.$table_end; 
?>