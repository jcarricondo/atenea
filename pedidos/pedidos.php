<?php
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/cliente.class.php");
include("../classes/basicos/nombre_producto.class.php");
include("../classes/basicos/listado_clientes.class.php");
include("../classes/basicos/listado_nombre_producto.class.php");
include("../classes/pedidos/pedido.class.php");
include("../classes/pedidos/listado_pedidos.class.php");
permiso(18);

// Se obtienen los datos del formulario
if($_GET["cab"] == "creado" or $_GET["cab"] == "modificado" or $_GET["cab"] == "eliminado") {
	$realizarBusqueda = 1;
}
if(isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) {
	$numero_pedido = $_GET["numero_pedido"];
    $id_cliente = $_GET["id_cliente"];
    $id_producto = $_GET["id_producto"];
    $fecha_pedido = $_GET["fecha_pedido"];
    $fecha_entrega_estimada = $_GET["fecha_entrega_estimada"];
    $fecha_entrega_planificada = $_GET["fecha_entrega_planificada"];
    $fecha_entrega = $_GET["fecha_entrega"];
    $estado = $_GET["estado"];

    $funciones = new Funciones();

    if($fecha_pedido != "") $fecha_pedido = $funciones->cFechaMy($fecha_pedido);
    if($fecha_entrega_estimada != "") $fecha_entrega_estimada = $funciones->cFechaMy($fecha_entrega_estimada);
    if($fecha_entrega_planificada != "") $fecha_entrega_planificada = $funciones->cFechaMy($fecha_entrega_planificada);
    if($fecha_entrega != "") $fecha_entrega = $funciones->cFechaMy($fecha_entrega);

    $listadoPedidos = new listadoPedidos();
    $listadoPedidos->setValores($numero_pedido,$id_cliente,$id_producto,$fecha_pedido,$fecha_entrega_estimada,$fecha_entrega_planificada,$fecha_entrega,$estado);
    $listadoPedidos->realizarConsulta();
    $resultadosPedidos = $listadoPedidos->resultados;
    $num_resultados = count($resultadosPedidos);

    // Volvemos a convertir las fechas al formato normal
    if($fecha_pedido != "") $fecha_pedido = $funciones->cFechaNormal($fecha_pedido);
    if($fecha_entrega_estimada != "") $fecha_entrega_estimada = $funciones->cFechaNormal($fecha_entrega_estimada);
    if($fecha_entrega_planificada != "") $fecha_entrega_planificada = $funciones->cFechaNormal($fecha_entrega_planificada);
    if($fecha_entrega != "") $fecha_entrega = $funciones->cFechaNormal($fecha_entrega);

    $mostrar_tabla = true;
} 
else {
    $numero_pedido = $_SESSION["pedido_numero_pedido"];
    $id_cliente = $_SESSION["pedido_id_cliente"];
    $id_producto = $_SESSION["pedido_id_producto"];
    $fecha_pedido = $_SESSION["pedido_fecha_pedido"];
    $fecha_entrega_estimada = $_SESSION["pedido_fecha_entrega_estimada"];
    $fecha_entrega_planificada = $_SESSION["pedido_fecha_entrega_planificada"];
    $fecha_entrega = $_SESSION["pedido_fecha_entrega"];
    $estado = $_SESSION["pedido_estado"];
}

$titulo_pagina = "Orden de Pedido > Listado";
$pagina = "pedidos";
include ("../includes/header.php");
echo '<script type="text/javascript" src="../js/pedidos/pedidos.js"></script>';
?>

<div class="separador"></div>
<?php include("../includes/menu_pedidos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>
    <h3>Listado de pedidos</h3>
    <form id="buscador_pedido" name="buscador_pedido" action="pedidos.php" method="get" class="Buscador">
    <table style="border:0;">
    	<tr style="border:0;">
        	<td>
            	<div class="Label">Nº Pedido</div>
            	<input type="text" id="numero_pedido" name="numero_pedido" class="BuscadorInput" value="<?php echo $numero_pedido; ?>"/>
            </td>
            <td>
            	<div class="Label">Cliente</div>
            	<select id="id_cliente" name="id_cliente" class="CreacionBasicoInput">
                    <option value="0">Todos</option>
                <?php
                $clientes = new listadoClientes();
                $clientes->setValores("","","","","","","","","","");
                $clientes->realizarConsulta();
                $resultadosClientes = $clientes->getResultados();
                for($i=0;$i<count($resultadosClientes);$i++) {
                    $cliente = new Cliente();
                    $datosCliente = $resultadosClientes[$i];
                    $cliente->cargaDatosClienteId($datosCliente["id_cliente"]);
                    ?>
                    <option value="<?php echo $cliente->id_cliente; ?>"<?php if($id_cliente == $cliente->id_cliente) { echo ' selected="selected"'; } ?>><?php echo $cliente->nombre; ?></option>
                    <?php
                }
                ?>
                </select>
            </td>
            <td>
            	<div class="Label">Producto</div>
            	<select id="id_producto" name="id_producto" class="CreacionBasicoInput">
                <option value="0">Todos</option>
                <?php
                $productos = new listadoNombreProducto();
                $productos->setValores("","","","","","");
                $productos->realizarConsulta();
                $resultadosProductos = $productos->getResultados();
                for($i=0;$i<count($resultadosProductos);$i++) {
                    $nomProd = new Nombre_Producto();
                    $datoNombreProducto = $resultadosProductos[$i];
                    $nomProd->cargaDatosNombreProductoId($datoNombreProducto["id_nombre_producto"]);
                    ?>
                    <option value="<?php echo $nomProd->id_nombre_producto; ?>"<?php if($id_producto == $nomProd->id_nombre_producto) { echo ' selected="selected"'; } ?>><?php echo $nomProd->nombre; ?> (<?php echo $nomProd->version; ?>)</option>
                    <?php
                }
                ?>
                </select>
            </td>
        </tr>
        <tr style="border:0;">
            <td>
            	<div class="Label">Fecha Pedido</div>
                <input type="text" name="fecha_pedido" id="fecha_pedido" class="fechaCal" value="<?php echo $fecha_pedido;?>"/>
            </td>
            <td>
            	<div class="Label">Fecha Entrega Esti.</div>
                <input type="text" name="fecha_entrega_estimada" id="fecha_entrega_estimada" class="fechaCal" value="<?php echo $fecha_entrega_estimada;?>"/>
            </td>
            <td>
                <div class="Label">Fecha Entrega Plan.</div>
                <input type="text" name="fecha_entrega_planificada" id="fecha_entrega_planificada" class="fechaCal" value="<?php echo $fecha_entrega_planificada;?>"/>
            </td>
        </tr>
        <tr style="border:0;">
            <td>
                <div class="Label">Fecha Entrega</div>
                <input type="text" name="fecha_entrega" id="fecha_entrega" class="fechaCal" value="<?php echo $fecha_entrega;?>"/>
            </td>
            <td>
                <div class="Label">Estado</div>
                <select id="estado" name="estado" class="BuscadorInput"/>
                    <option value="">Todos</option>
                    <option value="CREADO"<?php if ($estado == "CREADO") { echo ' selected="selected"';}?>>CREADO</option>
                    <option value="PARCIALMENTE ENTREGADO"<?php if ($estado == "PARCIALMENTE ENTREGADO") { echo ' selected="selected"';}?>>PARCIALMENTE ENTREGADO</option>
                    <option value="ENTREGADO"<?php if ($estado == "ENTREGADO") { echo ' selected="selected"';}?>>ENTREGADO</option>
                </select>
            </td>
            <td>
                <input type="hidden" id="realizandoBusqueda" name="realizandoBusqueda" value="1" />
                <input type="submit" id="" name="" class="" style="float: left;margin: 5px 20px 5px 4px;" value="Buscar" />
            </td>
        </tr>
    </table>
   	<br />
    </form>

    <div class="ContenedorBotonCrear">
		<?php
		   if($_GET["cab"] == "creado") {
		      echo '<div class="mensaje">La pedido se ha creado correctamente</div>';
		   }
		   if($_GET["cab"] == "modificado") {
		      echo '<div class="mensaje">La pedido se ha modificado correctamente</div>';
		   }
		   if($_GET["cab"] == "eliminado") {
		      echo '<div class="mensaje">La pedido se ha eliminado correctamente</div>';
		   }
		   if($_GET["cab"] == "eliminado_error") {
		      echo '<div class="mensaje">No se ha podido eliminar el pedido</div>';
		   }
           if($mostrar_tabla){
                if($num_resultados == NULL or $num_resultados == 0){
                   echo '<div class="mensaje">No se encontraron pedidos</div>';
                   $mostrar_tabla = false;
                }
                else if ($num_resultados == 1){
                    echo '<div class="mensaje">Se encontró 1 pedido</div>';
                }
                else{
                    echo '<div class="mensaje">Se encontraron '.$num_resultados.' pedidos</div>';
                }   
            }
		?>
    </div>

    <?php
		if ($mostrar_tabla) {
		?>
   		<div class="CapaTabla">
        <table>
            <tr>
                <th>Nº PEDIDO</th>
                <th>CLIENTE</th>
                <th>PRODUCTO</th>
                <th style="text-align: center;">FECHA PEDIDO</th>
                <th style="text-align: center;">UNIDADES</th>
                <th style="text-align: center;">FECHA ESTIM.</th>
                <th style="text-align: center;">FECHA PLANIF.</th>
                <th style="text-align: center;">FECHA ENTREGA</th>
                <th>ESTADO</th>
                <th style="text-align: center;"></th>
            </tr>
        <?php
            for($i=0;$i<count($resultadosPedidos);$i++) {
                $datos = $resultadosPedidos[$i];
                $pedido = new Pedido();
                $pedido->cargarPedidoId($datos["id_pedido"]);
                $cliente = new Cliente();
                $cliente->cargaDatosClienteId($pedido->id_cliente);
                $producto = new Nombre_Producto();
                $producto->cargaDatosNombreProductoId($pedido->id_producto);
                ?>
                <tr>
                    <td><a href="mod_pedido.php?id=<?php echo $pedido->id_pedido; ?>"><?php echo $pedido->numero_pedido; ?></a></td>
                    <td><?php echo $cliente->nombre; ?></td>
                    <td><?php echo $producto->nombre; ?> (<?php echo $producto->version; ?>)</td>
                    <td style="text-align: center;"><?php echo $funciones->cFechaNormal($pedido->fecha_pedido); ?></td>
                    <td style="text-align: center;"><?php echo $pedido->unidades; ?></td>
                    <td style="text-align: center;"><?php echo $funciones->cFechaNormal($pedido->fecha_entrega_estimada); ?></td>
                    <td style="text-align: center;"><?php echo $funciones->cFechaNormal($pedido->fecha_entrega_planificada); ?></td>
                    <td style="text-align: center;">
                        <?php
                            if($pedido->fecha_entrega == NULL) echo $fecha_entrega = "-";
                            else echo $funciones->cFechaNormal($pedido->fecha_entrega);
                        ?>
                    </td>
                    <td><?php echo $pedido->estado; ?></td>
                    <td style="text-align: center;"><input type="button" id="eliminar" name="eliminar" value="ELIMINAR" class="BotonEliminar" onclick="return validarEliminacion(<?php echo $pedido->id_pedido;?>);"/></td>
                </tr>
                <?php
            }
        ?>
        </table>
        </div>
	<?php
		}
	?>
</div>
<?php include ("../includes/footer.php"); ?>