<?php
// Este fichero muestra información sobre la Orden de Producción
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/componente.class.php");
include("../classes/basicos/periferico.class.php");
include("../classes/basicos/kit.class.php");
include("../classes/basicos/cliente.class.php");
include("../classes/basicos/nombre_producto.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/basicos/referencia_componente.class.php");  
include("../classes/basicos/listado_referencias_componentes.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/orden_produccion/incluir_referencia_libre.class.php");
include("../classes/productos/producto.class.php");
include("../classes/pedidos/pedido.class.php");
include("../classes/sede/sede.class.php");
include("../classes/control_usuario.class.php");
permiso(8);

$orden_produccion = new Orden_Produccion();
$producto = new Producto();
$comp = new Componente();
$periferico = new Periferico();
$kit = new Kit();
$referencia = new Referencia();
$nombre_producto = new Nombre_Producto();
$cliente = new Cliente();
$funciones = new Funciones();
$pedido = new Pedido();
$sede = new Sede();
$control_usuario = new Control_Usuario();

// Obtenemos los ids por url y hacemos la carga de la OP
$id_produccion = $_GET["id"];
$nombre = $_GET["nombre"];
$id_producto = $_GET["id_producto"];

$orden_produccion->cargaDatosProduccionId($id_produccion);
$alias_op = $orden_produccion->alias_op;
$unidades = $orden_produccion->unidades;
$id_tipo = $orden_produccion->id_tipo;
$fecha_inicio = $funciones->cFechaNormal($orden_produccion->fecha_inicio);
$fecha_entrega = $funciones->cFechaNormal($orden_produccion->fecha_entrega);
$fecha_entrega_deseada = $funciones->cFechaNormal($orden_produccion->fecha_entrega_deseada);
$fecha_inicio_construccion = $funciones->cFechaNormal($orden_produccion->fecha_inicio_construccion);
$fecha_creacion = $funciones->cFechaNormal($orden_produccion->fecha_creado);
$estado = $orden_produccion->estado;
$producto->cargaDatosProductoId($id_producto);
$id_nombre_producto = $producto->id_nombre_producto;
$id_sede = $orden_produccion->id_sede;

$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$esAdminGlobal = $control_usuario->esAdministradorGlobal($id_tipo_usuario);
$esUsuarioGes = $control_usuario->esUsuarioGes($id_tipo_usuario);

if($esAdminGlobal || $esUsuarioGes){
    // Cargamos el nombre de la sede segun su id
    $sede->cargaDatosSedeId($id_sede);
    $nombre_sede = $sede->nombre;
}

$max_caracteres_ref = 50;
$max_caracteres = 35;
$titulo_pagina = "Órdenes de Producción > Ver Orden de Producción";
$pagina = "ver_orden_produccion";
include ('../includes/header.php');
?>

<div class="separador"></div> 
<?php include("../includes/menu_op.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar"><?php include ("../includes/sidebar.php");?></div>

    <h3> Orden de producción </h3>
    <form id="FormularioCreacionBasico" name="verOrdenProduccion" action="ordenes_produccion.php" method="post">
    <br />
    <h5> Componentes de la Orden de Producción </h5>
    <?php 
        if($esAdminGlobal || $esUsuarioGes){ ?>
            <div class="ContenedorCamposCreacionBasico">
                <div class="LabelCreacionBasico">Sede </div>
                <label id="id_op" class="LabelInfoOP"><?php echo $nombre_sede;?></label>
            </div>
    <?php 
        }
    ?>
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">ID </div>
            <label id="id_op" class="LabelInfoOP"><?php echo $orden_produccion->id_produccion;?></label>
        </div>
    	<div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Alias </div>
           	<label id="alias_op" class="LabelInfoOP"><?php if ($alias_op != NULL){ echo $alias_op;} else { echo $orden_produccion->codigo;}?></label>
        </div>
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Unidades</div>
            <label id="unidades" class="LabelInfoOP"><?php echo $unidades;?></label>
        </div> 
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Fecha inicio </div>
            <label id="fecha_inicio_op" class="LabelInfoOP"><?php echo $fecha_inicio;?></label>
        </div>
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Fecha entrega </div>
            <label id="fecha_entrega_op" class="LabelInfoOP"><?php echo $fecha_entrega;?></label>
        </div>  
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Fecha entrega deseada </div>
            <label id="fecha_entrega_deseada_op" class="LabelInfoOP"><?php echo $fecha_entrega_deseada;?></label>
        </div>
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Fecha inicio construcci&oacute;n </div>
            <label id="fecha_inicio_construccion_op" class="LabelInfoOP"><?php if (empty($fecha_inicio_construccion)) echo "-"; else { echo $fecha_inicio_construccion;}?></label>
        </div>
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Fecha creaci&oacute;n </div>
            <label id="fecha_inicio_construccion_op" class="LabelInfoOP"><?php if (empty($fecha_creacion)) echo "-"; else { echo $fecha_creacion;}?></label>
        </div>    
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Tipo </div>
            <label id="tipo_op" class="LabelInfoOP"><?php if ($id_tipo == 1){ echo "ORDEN PRODUCCI&Oacute;N";} else { echo "MANTENIMIENTO";}?></label>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Producto </div>
            <?php $nombre_producto->cargaDatosNombreProductoId($id_nombre_producto); ?>
           	<label id="producto" class="LabelInfoOP"><?php echo $nombre_producto->nombre;?></label>
        </div>
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Estado</div>
            <label id="estado" class="LabelInfoOP"><?php echo $estado;?></label>
        </div>
        <br/>

<?php
    include("ver_op_muestra_componentes.php");
    //include("ver_op_muestra_refs_libres.php");
    //include("ver_op_muestra_productos.php");

    // Anteriormente calculamos el coste total del producto y despues el coste total de la Orden de Produccion en funcion del precio unitario de cada referencia
    // Ahora mostraremos los precios reales que coincide con los excel de la Orden de Produccion. 
    // Es decir, mostraremos el precio total de la Orden de Produccion en funcion del total paquetes utilizado y no el del precio unitario de la referencia
    // Para ello obtenemos los costes de las referencias de las Ordenes de Compra 

    $coste_total_produccion = 0;
    $coste_total_producto = 0;
    $resultados = $orden_produccion->dameOCReferenciasPorProduccion($id_produccion);

    for($i=0;$i<count($resultados);$i++){
        $coste_referencia_oc = $resultados[$i]["coste"];
        $coste_total_produccion = $coste_total_produccion + $coste_referencia_oc; 
    }

    // Redondeamos el coste total de la Orden de Produccion
    $coste_total_produccion = round($coste_total_produccion,2);

    // Obtenemos el coste total del producto en funcion de las unidades de la Orden de Produccion
    $coste_total_producto = round(($coste_total_produccion / $unidades),2); ?>
		 
    <div class="ContenedorCamposCreacionBasico">
        <div class="LabelCreacionBasico">Coste Total Producto</div>
        <div class="tituloComponente">
            <table id="tablaTituloPrototipo">
            <tr>
                <td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
                <?php
                    $precio_total_producto = $precio_todos_perifericos + $precio_refs_libres;
                    echo '<span class="tituloComp">'.number_format($coste_total_producto, 2, ',', '.').'€'.'</span>';
                ?>
                </td>
            </tr>
            </table>
        </div>
    </div>
        
    <div class="ContenedorCamposCreacionBasico">
        <div class="LabelCreacionBasico">Coste Total Orden de Producción</div>
        <div class="tituloComponente">
            <table id="tablaTituloPrototipo">
            <tr>
                <td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
                <?php
                    $coste_produccion = $coste_producto * $unidades;
                    echo '<span class="tituloComp">'.number_format($coste_total_produccion, 2, ',', '.').'€'.'</span>';
                ?>
                </td>
            </tr>
            </table>
        </div>
    </div>
    <br />
    <div class="ContenedorBotonCreacionBasico">
        <input type="button" id="volver" name="volver" value="Volver" onclick="history.back()" />
    </div>
    </form>
</div>
<?php include ('../includes/footer.php');?>