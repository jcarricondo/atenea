<?php
set_time_limit(10000);
// Primer paso para la modificación de la Orden de Producción
include("../includes/sesion.php");
include("../classes/basicos/periferico.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/basicos/nombre_producto.class.php");
include("../classes/basicos/listado_perifericos.class.php");
include("../classes/basicos/cliente.class.php");
include("../classes/basicos/listado_clientes.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/orden_produccion/incluir_referencia_libre.class.php");
include("../classes/productos/producto.class.php");
include("../classes/control_usuario.class.php");
include("../classes/kint/Kint.class.php");
permiso(10);

$ref = new Referencia();
$per = new Periferico();
$listado_per = new listadoPerifericos();
$nombre_producto = new Nombre_Producto();
$client = new Cliente();
$listado_client = new listadoClientes();
$op = new Orden_Produccion();
$producto = new Producto();
$control_usuario = new Control_Usuario();


if(isset($_POST["guardandoOrdenProduccion"]) and $_POST["guardandoOrdenProduccion"] == 1) {
	// Se reciben los datos
	$alias_op = $_POST["alias_op"];
	$unidades = $_POST["unidades"];
	$nombre_producto = $_POST["nombre_producto"];
	$perifericos = $_POST["perifericos"];
	$ref_libres = $_POST["REFS"];
	$cliente= $_POST["cliente"];
	$id_produccion = $_GET["id_produccion"];
	$piezas = $_POST["piezas"];
}
else {
	// Se cargan los datos de la orden de produccion y los productos asociados en funcion de su ID
	$id_produccion = $_GET["id_produccion"];
	$op->cargaDatosProduccionId($id_produccion);
	$alias_op = $op->alias_op;
	$unidades = $op->unidades;
	$fecha_inicio = $op->fecha_inicio;
	$fecha_entrega = $op->fecha_entrega;
	$fecha_entrega_deseada = $op->fecha_entrega_deseada;
	$estado = $op->estado;
	$id_sede = $op->id_sede;
	$id_producto = $_GET["id_producto"];
	$producto->cargaDatosProductoId($id_producto);
	$id_nombre_producto = $producto->id_nombre_producto;

	$res_ids_perifericos = $op->dameIdsPerifericos($id_produccion);
	foreach($res_ids_perifericos as $array_perifericos) $ids_perifericos[] = intval($array_perifericos["id_componente"]);
}

$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$esAdminGlobal = $control_usuario->esAdministradorGlobal($id_tipo_usuario);
$esAdminGes = $control_usuario->esAdministradorGes($id_tipo_usuario);

$titulo_pagina = "Órdenes de Producción > Modifica Orden de Producción";
$pagina = "mod_orden_produccion";
include ('../includes/header.php');
echo '<script type="text/javascript" src="../js/orden_produccion/mod_op_03042017_1230.js"></script>';
?>

<div class="separador"></div> 
<?php include("../includes/menu_op.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar"><?php include ("../includes/sidebar.php"); ?></div>

    <h3> Modificación de orden de producción </h3>
    <form id="FormularioCreacionBasico" name="modificarOrdenProduccion" onsubmit="return validarFormulario()" action="confirm_mod_op.php?id_produccion=<?php echo $id_produccion;?>&id_producto=<?php echo $id_producto;?>" method="post">
    <br />
    <h5> Modifique los datos en el siguiente formulario </h5>
    <?php
    	if($esAdminGlobal || $esAdminGes){
    		// ADMINISTRADOR GLOBAL. Elige la sede de la OP
    		if($id_sede == 1) $nombre_sede = "SIMUMAK";
    		else if($id_sede == 2) $nombre_sede = "TORO"; ?>
		    <div class="ContenedorCamposCreacionBasico">
				<div class="LabelCreacionBasico">Sede</div>
				<input type="text" id="nombre_sede" name="nombre_sede" class="CreacionBasicoInput" readonly="readonly" value="<?php echo $nombre_sede; ?>"/>
				<input type="hidden" id="sede" name="sede" value="<?php echo $sede; ?>"/>
		    </div>
	<?php
		}
	?>
    <div class="ContenedorCamposCreacionBasico">
       	<div class="LabelCreacionBasico">Alias</div>
        <input type="text" id="alias_op" name="alias_op" class="CreacionBasicoInput" value="<?php echo $alias_op;?>" onblur="comprobarAliasCorrecto()" />
		<div id="alias_correcto"><input type="hidden" id="alias_validado" name="alias_validado" value="1" /></div>
    </div>
    <div class="ContenedorCamposCreacionBasico">
    	<div class="LabelCreacionBasico">Unidades *</div>
        <input type="text" id="unidades" name="unidades" class="CreacionBasicoInput" readonly="readonly" value="<?php echo $unidades;?>" />
    </div>
    <div class="ContenedorCamposCreacionBasico">
    	<div class="LabelCreacionBasico">Producto *</div>
        <?php
			// Primero cargamos el nombre de producto asociado a los productos de esa Orden de Producción para que se quede seleccionado de manera predeterminada
			$nombre_producto->cargaDatosNombreProductoId($id_nombre_producto);
		?>
        <input type="text" id="producto" name="producto" class="CreacionBasicoInput" readonly="readonly" value="<?php echo $nombre_producto->nombre;?>" />
        <input type="hidden" id="id_nombre_producto" name="id_nombre_producto" value="<?php echo $id_nombre_producto;?>" />
    </div>
	<br/>

	<?php include("mod_op_add_perifericos.php")	?>
	<?php
		// Mostrar kits libres sólo si existen en la Orden de Producción
	?>
	<?php include("mod_op_add_refs_libres.php") ?>
	<?php include("mod_op_add_productos.php") ?>


	<div class="ContenedorBotonCreacionBasico">
    	<input type="button" id="volver" name="volver" value="Volver" onclick="window.history.back()" />
        <input type="hidden" id="confirmarOrdenProduccion" name="confirmarOrdenProduccion" value="1" />
        <input type="submit" id="guardar" name="guardar" value="Continuar" />
        <input type="hidden" id="id_produccion" name="id_produccion" value="<?php echo $id_produccion;?>" />
    </div>
    <?php if($mensaje_error != "") echo '<div class="mensaje_error">'.$mensaje_error.'</div>'; ?>
    <br />
    </form>
</div>

<?php include ('../includes/footer.php');  ?> 