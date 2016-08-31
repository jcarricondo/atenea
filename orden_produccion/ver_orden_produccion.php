<?php
// Este fichero muestra información sobre la Orden de Producción
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/cabina.class.php");
include("../classes/basicos/periferico.class.php");
include("../classes/basicos/software.class.php");
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
$cabina = new Cabina();
$periferico = new Periferico();
$software = new Software();
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

$ids_softwares = $orden_produccion->dameIdsSoftwares($id_produccion);
for($i=0;$i<count($ids_softwares);$i++){
    $id_software = $ids_softwares[$i]["id_componente"];
    $ids_softwares[$i] = $id_software; 
}

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
    // Obtenemos los id_produccion_componente 
    $ids_produccion_componente = $orden_produccion->dameIdsProduccionComponente($id_produccion);
    $coste_producto = 0;
    $coste_produccion = 0;

    // Mostramos la tabla con las referencias de los componentes que no sean software
    for($i=0;$i<count($ids_produccion_componente);$i++){
        $id_produccion_componente = $ids_produccion_componente[$i]["id_produccion_componente"];
        $id_componente = $orden_produccion->dameIdComponentePorIdProduccionComponente($id_produccion_componente);
        $id_componente = $id_componente[0]["id_componente"];
        // Obtenemos el tipo del componente
        $id_tipo = $orden_produccion->dameTipoComponente($id_componente);
        $id_tipo = $id_tipo["id_tipo"];
      
        switch ($id_tipo) {
            case '1':
                // CABINA
                $cabina->cargaDatosCabinaId($id_componente);
                $nombre_componente = "Cabina";
                $nombre_componente_principal = "Cabina";
                $titulo_componente = $cabina->cabina.'_v'.$cabina->version;
                $es_prototipo = ($cabina->prototipo == 1);
                $coste_total_componente = 0;
                break;
            case '2':
                // PERIFERICO
                $periferico->cargaDatosPerifericoId($id_componente);
                $nombre_componente = "Periferico";
                $nombre_componente_principal = "Periferico";
                $titulo_componente = $periferico->periferico.'_v'.$periferico->version;
                $es_prototipo = ($periferico->prototipo == 1);
                $coste_total_componente = 0;
                break;
            case '3':
                // SOFTWARE
                // Los mostramos despues de los componentes
            break;    
            case '4':
                // INTERFAZ
                // Deja de existir en Agosto de 2016
            break;
            case '5':
                // KIT
                $kit->cargaDatosKitId($id_componente);
                $nombre_componente = "Kit";
                $titulo_componente = $kit->kit.'_v'.$kit->version;
                $es_prototipo = ($kit->prototipo == 1);
                break;
            default:
                //
                break;
        }

        // Cargamos los datos de orden_produccion_referencias
        $resultados = $orden_produccion->cargaDatosPorProduccionComponente($id_produccion,$id_produccion_componente);

        if($id_tipo != 3){    
?>
            <div class="ContenedorCamposCreacionBasico">
                <div class="LabelCreacionBasico">Referencias <?php echo $nombre_componente;?></div> 
                <div class="tituloComponente">
                    <table id="tablaTituloPrototipo">
                    <tr>
                        <td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;"><?php echo '<span class="tituloComp">'.$titulo_componente.'</span>';?></td>
                        <td style="text-align:left; background:#fff; vertical-align:top; padding:0px 5px 0px 5px;">
                            <?php
                                if ($es_prototipo) {
                                    echo '<span class="ImagenPrototipo"><img src="../images/prototipo.jpg" width="20px" height="20px" alt="PROTOTIPO" title="PROTOTIPO"></span>';
                                }
                                else {
                                    echo '<span class="ImagenPrototipo"><img src="../images/engranaje.gif" width="20px" height="20px" alt="PRODUCCION" title="PRODUCCION"></span>';
                                }
                            ?>
                        </td>
                    </tr>  
                    </table>  
                </div>
                <div class="CajaReferencias">
                    <div id="CapaTablaIframe">
                        <table>
                            <tr>
                                <th style="text-align:center">ID_REF</th>
                                <th>NOMBRE</th>
                                <th>PROVEEDOR</th>
                                <th>REF PROV</th>
                                <th>NOMBRE PIEZA</th>
                                <th style="text-align:center">PIEZAS</th>
                                <th style="text-align:center">PACK PRECIO</th>
                                <th style="text-align:center">UDS/P</th>
                                <th style="text-align:center">TOTAL PAQS</th>
                                <th style="text-align:center">PRECIO UNIDAD</th>
                                <th style="text-align:center">PRECIO</th>
                            </tr>
                        <?php
                            $precio_componente = 0;
                            for($j=0;$j<count($resultados);$j++){
                                $id_referencia = $resultados[$j]["id_referencia"];
                                $uds_paquete = $resultados[$j]["uds_paquete"];
                                $piezas = $resultados[$j]["piezas"];
                                $total_paquetes = $resultados[$j]["total_paquetes"];
                                $pack_precio = $resultados[$j]["pack_precio"];

                                if($pack_precio != 0 and $uds_paquete != 0){
                                    $precio_unidad = $pack_precio / $uds_paquete;
                                }
                                else {
                                    $precio_unidad = 0;
                                }
                                $precio_referencia = $precio_unidad * $piezas;
                                $precio_componente = $precio_componente + $precio_referencia;

                                $referencia->cargaDatosReferenciaId($id_referencia);  
                        ?>    
                            <tr>
                                <td style="text-align:center"><?php echo $id_referencia; ?></td>
                                <td>
                                    <?php
                                        if (strlen($referencia->referencia) > $max_caracteres_ref){
                                            echo substr($referencia->referencia,0,$max_caracteres_ref).'...'; 
                                        }
                                        else echo $referencia->referencia;   
                                    ?>    
                                </td>
                                <td>
                                    <?php 
                                        if (strlen($referencia->nombre_proveedor) > $max_caracteres){
                                            echo substr($referencia->nombre_proveedor,0,$max_caracteres).'...'; 
                                        }
                                        else echo $referencia->nombre_proveedor;
                                    ?>    
                                </td>
                                <td><?php $referencia->vincularReferenciaProveedor(); ?></td>
                                <td>
                                    <?php 
                                        if (strlen($referencia->part_nombre) > $max_caracteres){
                                            echo substr($referencia->part_nombre,0,$max_caracteres).'...'; 
                                        }
                                        else echo $referencia->part_nombre;
                                    ?>
                                </td>            
                                <td style="text-align:center"><?php echo number_format($piezas, 2, ',', '.'); ?></td>
                                <td style="text-align:center"><?php echo number_format($pack_precio, 2, ',', '.'); ?></td>
                                <td style="text-align:center"><?php echo number_format($uds_paquete, 2, ',', '.'); ?></td>
                                <td style="text-align:center"><?php echo number_format($total_paquetes, 2, ',', '.'); ?></td>
                                <td style="text-align:center"><?php echo number_format($precio_unidad, 2, ',', '.'); ?></td>
                                <td style="text-align:center"><?php echo number_format($precio_referencia, 2, ',', '.'); ?></td>
                            </tr>    
                        <?php 
                            }
                            $coste_total_componente = $coste_total_componente + $precio_componente;
                        ?>
                        </table>
                    </div>
                </div> 
            </div>
            <div class="ContenedorCamposCreacionBasico">
                <div class="LabelCreacionBasico">Coste <?php echo $nombre_componente; ?></div> 
                <div class="tituloComponente">
                    <table id="tablaTituloPrototipo">
                        <tr>
                            <td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
                                <?php echo '<span class="tituloComp">'.number_format($precio_componente, 2, ',', '.').'€'.'</span>';?>
                            </td>
                        </tr>
                    </table>    
                </div>    
            </div>
        <?php
            // Calculamos el siguiente id_tipo_componente siguiente para asignar el coste total del componente principal (CABINA o PERIFERICO)
            if($i+1 <= count($ids_produccion_componente)){
                $id_tipo_siguiente = $orden_produccion->dameIdTipoPorIdProduccionComponente($ids_produccion_componente[$i+1]["id_produccion_componente"]);
                $id_tipo_siguiente = $id_tipo_siguiente[0]["id_tipo_componente"];

                // Solo se mostrara el coste total del componente cuando el siguiente sea un periferico o un software
                if($id_tipo_siguiente == 2 or $id_tipo_siguiente == NULL){
                    $coste_producto = $coste_producto + $coste_total_componente;
        ?>
                    <div class="ContenedorCamposCreacionBasico">
                        <div class="LabelCreacionBasico">Coste Total <?php echo $nombre_componente_principal;?></div> 
                        <div class="tituloComponente">
                            <table id="tablaTituloPrototipo">
                                <tr>
                                    <td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
                                        <?php 
                                            echo '<span class="tituloComp">'.number_format($coste_total_componente, 2, ',', '.').'€'.'</span>'; 
                                        ?>
                                    </td>
                                </tr>
                            </table>    
                        </div>
                    </div>
                    <br/>
            <?php        
                }
            }  
        }
     }
   
    // Mostramos los SOFTWARE
?>
    <div class="ContenedorCamposCreacionBasico">
        <div class="LabelCreacionBasico">Softwares</div>
        <div class="CajaReferencias">
            <div id="CapaTablaIframe">
                <table id="mitablaSoftwares">
                    <tr>
                        <th>NOMBRE</th>
                        <th>REFERENCIA</th>
                        <th style="text-align:center">VERSION</th>
                        <th>DESCRIPCION</th>
                    </tr>
                    <?php
                        for($j=0;$j<count($ids_softwares);$j++) {
                            // Se cargan los datos de los softwares según su identificador
                            $software->cargaDatosSoftwareId($ids_softwares[$j]);
                    ?>
                    <tr>
                        <td><?php echo $software->software;?></td>
                        <td><?php echo $software->referencia;?></td>
                        <td style="text-align:center"><?php echo $software->version;?></td>
                        <td><?php echo $software->descripcion;?></td>
                    </tr>
                    <?php
                        }
                    ?>
                </table>
            </div>
        </div> 
    </div>
    <br />
<?php
    // Cargamos las referencias libres
    // Cargamos los datos de orden_produccion_referencias
    $resultados = $orden_produccion->cargaDatosPorProduccionComponente($id_produccion,0);
?>

    <div class="ContenedorCamposCreacionBasico">
        <div class="LabelCreacionBasico">Referencias Libres</div> 
        <div class="CajaReferencias">
            <div id="CapaTablaIframe">
                <table>
                    <tr>
                        <th style="text-align:center">ID_REF</th>
                        <th>NOMBRE</th>
                        <th>PROVEEDOR</th>
                        <th>REF PROV</th>
                        <th>NOMBRE PIEZA</th>
                        <th style="text-align:center">PIEZAS</th>
                        <th style="text-align:center">PACK PRECIO</th>
                        <th style="text-align:center">UDS/P</th>
                        <th style="text-align:center">TOTAL PAQS</th>
                        <th style="text-align:center">PRECIO UNIDAD</th>
                        <th style="text-align:center">PRECIO</th>
                    </tr>
                    <?php
                        $precio_componente = 0;
                        for($j=0;$j<count($resultados);$j++){
                            $id_referencia = $resultados[$j]["id_referencia"];
                            $uds_paquete = $resultados[$j]["uds_paquete"];
                            $piezas = $resultados[$j]["piezas"];
                            $total_paquetes = $resultados[$j]["total_paquetes"];
                            $pack_precio = $resultados[$j]["pack_precio"];

                            if($pack_precio != 0 and $uds_paquete != 0){
                                $precio_unidad = $pack_precio / $uds_paquete;
                            }
                            else {
                                $precio_unidad = 0;
                            }
                            $precio_referencia = $precio_unidad * $piezas;
                            $precio_componente = $precio_componente + $precio_referencia;
                            $referencia->cargaDatosReferenciaId($id_referencia);  
                    ?>    
                    <tr>
                        <td style="text-align:center"><?php echo $id_referencia; ?></td>
                        <td>
                            <?php
                                if (strlen($referencia->referencia) > $max_caracteres_ref){
                                    echo substr($referencia->referencia,0,$max_caracteres_ref).'...'; 
                                }
                                else echo $referencia->referencia;   
                            ?>    
                        </td>
                        <td>
                            <?php 
                                if (strlen($referencia->nombre_proveedor) > $max_caracteres){
                                    echo substr($referencia->nombre_proveedor,0,$max_caracteres).'...'; 
                                }
                                else echo $referencia->nombre_proveedor;
                            ?>    
                        </td>
                        <td><?php $referencia->vincularReferenciaProveedor(); ?></td>
                        <td>
                            <?php 
                                if (strlen($referencia->part_nombre) > $max_caracteres){
                                    echo substr($referencia->part_nombre,0,$max_caracteres).'...'; 
                                }
                                else echo $referencia->part_nombre;
                            ?>
                        </td>            
                        <td style="text-align:center"><?php echo number_format($piezas, 2, ',', '.'); ?></td>
                        <td style="text-align:center"><?php echo number_format($pack_precio, 2, ',', '.'); ?></td>
                        <td style="text-align:center"><?php echo number_format($uds_paquete, 2, ',', '.'); ?></td>
                        <td style="text-align:center"><?php echo number_format($total_paquetes, 2, ',', '.'); ?></td>
                        <td style="text-align:center"><?php echo number_format($precio_unidad, 2, ',', '.'); ?></td>
                        <td style="text-align:center"><?php echo number_format($precio_referencia, 2, ',', '.'); ?></td>
                    </tr>    
                    <?php 
                        }
                        $coste_refs_libres = $precio_componente;
                        $coste_producto = $coste_producto + $coste_refs_libres;
                    ?>
                </table>
            </div>
        </div> 
    </div>
    <div class="ContenedorCamposCreacionBasico">
        <div class="LabelCreacionBasico">Coste Referencias Libres</div> 
        <div class="tituloComponente">
            <table id="tablaTituloPrototipo">
                <tr>
                    <td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
                        <?php echo '<span class="tituloComp">'.number_format($coste_refs_libres, 2, ',', '.').'€'.'</span>';?>
                    </td>
                </tr>
            </table>    
        </div>    
    </div>

<?php 
	// Se cargan los productos de la orden de produccion "id_produccion"
	$orden_produccion = new Orden_Produccion();
	$orden_produccion->dameIdsProductoOP($id_produccion);
?>
    <br/>
    <div class="ContenedorCamposCreacionBasico">
        <div class="LabelCreacionBasico">Productos</div>
        <div class="CajaReferencias">
            <div id="CapaTablaIframe">
                <table id="mitabla">
                <tr>
                    <th>NUM SERIE</th>
                    <th>NOMBRE</th>
                    <th>F. ENT</th>
                    <th>F. ENT. PREV.</th>
                    <th>CLIENTE</th>
                    <th>PEDIDO</th>
                    <th>ESTADO</th>
                </tr>
<?php
	for ($i=0;$i<$unidades;$i++) { 
		$id_producto = $orden_produccion->ids_productos[$i]["id_producto"];
		$producto->cargaDatosProductoId($id_producto);
		$producto->dameNumSerie($id_producto);
		$producto->dameIdCliente($id_producto);
				
		$id_nombre_producto = $producto->id_nombre_producto;
        $nombre_producto->cargaDatosNombreProductoId($id_nombre_producto);
        $nombre_prod = $nombre_producto->nombre;
        $id_cliente = $producto->id_cliente["id_cliente"];
		$cliente->cargaDatosClienteId($id_cliente);
		$nombre_cliente = $cliente->nombre;	
        $id_pedido = $producto->id_pedido;
        $pedido->cargarPedidoId($id_pedido);
        $numero_pedido = $pedido->numero_pedido;
?>
        <tr>
            <td><?php echo $producto->num_serie["num_serie"]; ?></td>   
            <td><?php echo $nombre_prod; ?></td>
            <td><?php echo $funciones->cFechaNormal($producto->fecha_entrega); ?></td>
            <td><?php echo $funciones->cFechaNormal($producto->fecha_entrega_prevista); ?></td>
            <td>
                <?php 
                    if($nombre_cliente != NULL) echo $nombre_cliente; 
                    else echo "-";
                ?>
            </td>
            <td>
                <?php 
                    if($nombre_pedido != NULL) echo $numero_pedido; 
                    else echo "-";
                ?>
            </td>
            <td>
                <?php 
                    if ($producto->estado_producto == "ENTREGADO") echo '<span style="color:green;">'.$producto->estado_producto.'</span>';  
                    else echo $producto->estado_producto;
                ?>
            </td>
        </tr>
<?php 
    } 
?>
        </table>
        </div>
    </div>
</div>
<br/>

<?php 
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
    $coste_total_producto = round(($coste_total_produccion / $unidades),2);
?> 
		 
<div class="ContenedorCamposCreacionBasico">
    <div class="LabelCreacionBasico">Coste Total Producto</div> 
    <div class="tituloComponente">
		<table id="tablaTituloPrototipo">
            <tr>
              	<td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
   		       		<?php 
						$precio_total_producto = $precio_total_cabina + $precio_todos_perifericos + $precio_refs_libres;
						// echo '<span class="tituloComp">'.number_format($coste_producto, 2, ',', '.').'€'.'</span>'; 
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
						// echo '<span class="tituloComp">'.number_format($coste_produccion, 2, ',', '.').'€'.'</span>'; 
                        echo '<span class="tituloComp">'.number_format($coste_total_produccion, 2, ',', '.').'€'.'</span>'; 
					?>
                </td>
            </tr>
        </table>    
    </div>
</div>
<br />
<div class="ContenedorBotonCreacionBasico">
   	<input type="button" id="volver" name="volver" value="Volver" onclick="javascript: history.back()" />
</div>
</form>
</div>
<?php include ('../includes/footer.php');?>