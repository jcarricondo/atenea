<?php 
// Este fichero crea una nueva orden de pedido
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/cliente.class.php");
include("../classes/basicos/nombre_producto.class.php");
include("../classes/basicos/listado_clientes.class.php");
include("../classes/basicos/listado_nombre_producto.class.php");
include("../classes/pedidos/pedido.class.php");
permiso(19);

if(isset($_POST["guardandoPedido"]) and $_POST["guardandoPedido"] == 1) {
    $id_cliente = $_POST["id_cliente"];
    $id_producto = $_POST["id_producto"];
    $numero_pedido = $_POST["numero_pedido"];
    $unidades = $_POST["unidades"];
    $fecha_entrega_estimada = $_POST["fecha_entrega_estimada"];

    if($id_cliente == 0 or $id_producto == 0 or $numero_pedido == "" or $unidades == 0 or $unidades == "") {
        $mensaje_error = "Se tienen que completar todos los datos obligatorios";
    } else {
        $funciones = new Funciones();
        if($fecha_entrega_estimada == "") {
            $fecha_entrega_estimada = NULL;
        } else {
            $fecha_entrega_estimada = $funciones->cFechaMy($fecha_entrega_estimada);
        }
        // La fecha de entrega planificada se crea con la misma fecha_entrega_estimada
        $fecha_entrega_planificada = $fecha_entrega_estimada;
        $fecha_entrega = NULL;

        $orden_pedido = new Pedido();
        $orden_pedido->setValores(NULL,$id_cliente,$id_producto,$numero_pedido,$unidades,$fecha_entrega_estimada,$fecha_entrega_planificada,$fecha_entrega,"CREADO","");
        $resultado = $orden_pedido->guardarCambios();
        if($resultado == 1) {
            header("Location: pedidos.php?cab=creado");
        } else {
            $mensaje_error = $orden_pedido->getErrorMessage($resultado);
        }
    }

} else {
    $id_cliente = 0;
    $id_producto = 0;
    $numero_pedido = "";
    $unidades = 1;
    $fecha_entrega = "";
}
$titulo_pagina = "Orden de Pedido > Nueva";
$pagina = "new_pedido";
include ('../includes/header.php');
?>

<div class="separador"></div> 
<?php include("../includes/menu_pedidos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>
   	
    <h3>Nueva orden de pedido</h3>
    <form id="FormularioCreacionBasico" name="crearUsuario" action="nuevo_pedido.php" method="post" class="">
    	<br />
    	<div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">N&uacute;mero de Pedido *</div>
            <input type="text" id="numero_pedido" name="numero_pedido" class="CreacionBasicoInput" value="<?php echo $numero_pedido;?>" />
        </div>    
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Cliente *</div>
          	<select id="id_cliente" name="id_cliente" class="CreacionBasicoInput">
                <option value="0">Selecciona...</option>
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
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Producto *</div>
            <select id="id_producto" name="id_producto" class="CreacionBasicoInput">
                <option value="0">Selecciona...</option>
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
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Unidades *</div>
          	<input type="text" id="unidades" name="unidades" class="CreacionBasicoInput" value="<?php echo $unidades;?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Fecha entrega estimada</div>
            <input type="text" id="fecha_entrega_estimada" name="fecha_entrega_estimada" class="fechaCal" value="<?php echo $fecha_entrega_estimada;?>" />
        </div>
        <div class="ContenedorBotonCreacionBasico">
           	<input type="button" id="volver" name="volver" value="Volver" onClick="javascript:window.history.back()"/> 
            <input type="hidden" id="guardandoPedido" name="guardandoPedido" value="1" />
            <input type="submit" id="guardar" name="guardar" value="Continuar" />
        </div>
        <br />
        <div class="mensajeCamposObligatorios">
        	* Campos obligatorios
        </div>
		<?php 
		if($mensaje_error != "") {
			echo '<div class="mensaje_error">'.$mensaje_error.'</div>'; 
		}
		?>
        <br />
    </form>
</div>    
<?php include ("../includes/footer.php"); ?>