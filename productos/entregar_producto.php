<?php
// Este fichero inicia la Orden de Produccion
include("../includes/sesion.php");
include("../classes/basicos/cliente.class.php");
include("../classes/basicos/nombre_producto.class.php");
include("../classes/basicos/listado_clientes.class.php");
include("../classes/basicos/listado_nombre_producto.class.php");
include("../classes/productos/producto.class.php");
include("../classes/pedidos/pedido.class.php");
include("../classes/pedidos/listado_pedidos.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");
permiso(17);

$id_producto = $_GET["id_producto"];

$bbdd = new MySQL;
$clientes = new listadoClientes();
$producto = new Producto();
$cliente = new Cliente(); 
$op = new Orden_Produccion();
// Carga de datos del producto
$producto->cargaDatosProductoId($id_producto);
$num_serie = $producto->num_serie;
$buscarPedidos = false;
if(isset($_POST["entregarProducto"]) and $_POST["entregarProducto"] == 2) {
	$id_cliente = $_POST["id_cliente"];
	$id_pedido = $_POST["id_pedido"];
	$id_producto = $_GET["id_producto"];

	$op->cargaDatosProduccionId($producto->id_produccion);
	$id_sede = $op->id_sede;

	$resultado = $producto->consultarPedidosProducto($id_producto,$id_pedido);
	if($resultado == 8) {
		$c_pedido = new Pedido();
		// Comprobamos si el estado del pedido esta completamente entregado
		$resultado = $c_pedido->comprobarPedidoEntregado($id_pedido);

		if($resultado == 1){
			// El pedido esta en estado ENTREGADO. Actualizamos la fecha de entrega
			$resultado = $c_pedido->actualizarFechaEntregaPedido($id_pedido);

			if($resultado == 1){
				echo '<script type="text/javascript">opener.location.href="productos.php?entregado=true&sedes='.$id_sede.'";window.close();</script>'; 
			}
			else{
				// ERROR al actualizar la fecha de entrega del pedido
				$mensaje_error = $c_pedido->getErrorMessage($resultado);
			}
		}
		else if($resultado == 8){
			// El pedido no se encuentra en estado ENTREGADO. No actualizamos la fecha de entrega
			echo '<script type="text/javascript">opener.location.href="productos.php?entregado=true&sedes='.$id_sede.'";window.close();</script>'; 
		}
		else {
			// ERROR al comprobar si el pedido esta en estado ENTREGADO
			$mensaje_error = $c_pedido->getErrorMessage($resultado);
		}
	} 
	else {
		// ERROR al consultar los pedidos de los productos
		$mensaje_error = $producto->getErrorMessage($resultado);
	}
}
if(isset($_POST["entregarProducto"]) and $_POST["entregarProducto"] == 1) {
	$id_cliente = $_POST["id_cliente"];
	if($id_cliente == 0) {

	} else {
		$buscarPedidos = true;
		$listadoPedidos = new listadoPedidos();
		$listadoPedidos->buscarPedidosCliente($id_cliente);
    	$resultadosPedidos = $listadoPedidos->resultados;
	}
}
?>
<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
<div id="MuestraEntregarProducto">
	<h1>Entrega del producto</h1>
    <h2><?php echo $num_serie;?></h2>
    <br/><br/>
    <?php 
		if ($mensaje_error != ""){
			echo '<br/>'.$mensaje_error.'<br/>';	
		}
	?>
    <div id="CapaFormularioEntregarProducto">
    	<form id="FormularioEntregarProducto" name="entregarProducto" action="entregar_producto.php?id_producto=<?php echo $id_producto;?>" method="post">
       	<br />
        <h3>Asigne el cliente al que se le entregará el producto</h3>
        <br /><br />
        <div class="ContenedorCamposEntregarProducto">
        	<table width="100%">
            <tr>
            	<td width="50%" align="right"><div class="LabelEntregarProducto">Cliente</div></td>
               	<td width="50%" align="center">    	
            		<select id="id_cliente" name="id_cliente" class="EntregarProductoInput">
            			<option value="0">Selecciona</option>
            		<?php 
						$clientes->consultaProductoPedido($id_producto);
						$clientes->realizarConsulta();
						$resultado_clientes = $clientes->clientes;

						for($i=0;$i<count($resultado_clientes);$i++) {
							$datoCliente = $resultado_clientes[$i];
							$cliente->cargaDatosClienteId($datoCliente["id_cliente"]);
							echo '<option value="'.$cliente->id_cliente.'" '; if ($cliente->id_cliente == $id_cliente) echo 'selected="selected"'; echo '>'.$cliente->nombre.'</option>';
						}
					?> 
            		</select>
            	</td>
            </tr>
            <?php
            if($buscarPedidos) {
            	?>
				<tr>
	            	<td width="50%" align="right"><div class="LabelEntregarProducto">Pedido</div></td>
	               	<td width="50%" align="center">    	
	            		<select id="id_pedido" name="id_pedido" class="EntregarProductoInput">
	            			<option value="0">Selecciona</option>
						<?php
						for($i=0;$i<count($resultadosPedidos);$i++) {
							$datos = $resultadosPedidos[$i];
			                $pedido = new Pedido();
			                $pedido->cargarPedidoId($datos["id_pedido"]);
			                $producto = new Nombre_Producto();
                			$producto->cargaDatosNombreProductoId($pedido->id_producto);
			                ?>
							<option value="<?php echo $pedido->id_pedido; ?>"><?php echo $pedido->numero_pedido; ?> (<?php echo $producto->nombre.' '.$producto->version; ?>)</option>
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
        <br/>
        <div class="CapaBotonesEntregarProducto">
          	<?php
          	if($buscarPedidos) {
          		?>
				<input type="hidden" id="entregarProducto" name="entregarProducto" value="2" />
				<input type="button" id="cerrar" name="cerrar" value="Volver" onclick="javascript:history.back(-1)" /> 
				<input type="submit" id="guardar" name="guardar" value="Finalizar" />
          		<?php
          	} else {
          		?>
          		<input type="button" id="cerrar" name="cerrar" value="Cerrar" onclick="javascript:window.close()" /> 
				<input type="hidden" id="entregarProducto" name="entregarProducto" value="1" />
            	<input type="submit" id="guardar" name="guardar" value="Siguiente" />
          		<?php
          	}
          	?>
        </div>
        <br />
        </form>
    </div> 
</div>