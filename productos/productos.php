<?php
// Este fichero muestra el listado de los productos
include("../includes/sesion.php");
include("../classes/control_usuario.class.php");
include("../classes/sede/sede.class.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/nombre_producto.class.php");
include("../classes/basicos/cliente.class.php");
include("../classes/basicos/listado_nombre_producto.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/productos/producto.class.php");
include("../classes/productos/listado_productos.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/pedidos/pedido.class.php");
permiso(16);

$control_usuario = new Control_Usuario();
$sede = new Sede();
$funciones = new Funciones();
$nom_prod = new Nombre_Producto();
$cliente = new Cliente();
$list_nom_prod = new listadoNombreProducto();
$op = new Orden_Produccion();
$producto = new Producto();
$listado_productos = new listadoProductos;
$almacen = new Almacen();
$pedido = new Pedido();

$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$id_almacen = $_SESSION["AT_id_almacen"];
// Comprobamos si es Administrador Global
$esAdminGlobal = $control_usuario->esAdministradorGlobal($id_tipo_usuario);
// Comprobamos si es Administrador o Usuario de Gestion
$esUsuarioGes = $control_usuario->esUsuarioGes($id_tipo_usuario);
// Obtenemos la sede a la que pertenece el usuario 
$id_sede = $almacen->dameSedeAlmacen($id_almacen);
$id_sede = $id_sede["id_sede"];

// Establecemos los parametros de la paginacion
// Número de registros a mostrar por página
$pg_registros = 50; 
$pg_pagina = $_GET["pg"];
if(empty($pg_pagina)) {
    $pg_inicio = 0;
    $pg_pagina = 1;
} 
else {
    $pg_inicio = ($pg_pagina - 1) * $pg_registros;
}
$paginacion = " limit ".$pg_inicio.', '.$pg_registros;

// Si se ha asignado el cliente y entregado el producto se guardará la modificación en la BBDD 
if($_GET["entregado"] == "true"){
	$realizarBusqueda = 1;
}
		
if(isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) {
	$mostrar_tabla = true;
	$num_serie = addslashes($_GET["num_serie"]);
	$codigo_op = addslashes($_GET["codigo_op"]);
	$nombre_producto = $_GET["nombre_producto"];
	$orden_produccion = $_GET["orden_produccion"];
	$estado = $_GET["estado"];
	$fecha_desde = $_GET["fecha_desde"];
	$fecha_hasta = $_GET["fecha_hasta"];

	// Obtenemos la sede para el Admin Global
    if($esAdminGlobal || $esUsuarioGes){
        $id_sede = $_GET["sedes"]; 
    }

	if(!is_numeric($orden_produccion)) $orden_produccion = NULL;
	if($fecha_desde != "") $fecha_desde = $funciones->cFechaMy($fecha_desde);
	if($fecha_hasta != "") $fecha_hasta = $funciones->cFechaMy($fecha_hasta);

	// Se carga la clase para la base de datos y el listado de proveedores
	$listado_productos->setValores($num_serie,$codigo_op,$nombre_producto,NULL /*$cabina_bus*/,$orden_produccion,$estado,$fecha_desde,$fecha_hasta,$id_sede,'');
	$listado_productos->realizarConsulta();
	$resultadosBusqueda = $listado_productos->productos;
	$num_resultados = count($resultadosBusqueda); 
	
	// Se realiza la consulta con paginacion 
	$pg_totalPaginas = ceil(count($resultadosBusqueda) / $pg_registros);
	$listado_productos->setValores($num_serie,$codigo_op,$nombre_producto,NULL /*$cabina_bus*/,$orden_produccion,$estado,$fecha_desde,$fecha_hasta,$id_sede,$paginacion);
	$listado_productos->realizarConsulta();
	$resultadosBusqueda = $listado_productos->productos;	 

	if($fecha_desde != "") $fecha_desde = $funciones->cFechaNormal($fecha_desde);
	if($fecha_hasta != "") $fecha_hasta = $funciones->cFechaNormal($fecha_hasta);
	
	// Volvemos a reasignar la variable "orden_produccion" en el caso de que su valor fuese NULL
	$orden_produccion = $_GET["orden_produccion"];
	
	// Guardar las variables del formulario en variable de sesion
	$_SESSION["num_serie_productos"] = stripslashes(htmlspecialchars($num_serie));
	$_SESSION["codigo_op_productos"] = stripslashes(htmlspecialchars($codigo_op));
	$_SESSION["nombre_producto_productos"] = $nombre_producto;
	$_SESSION["orden_produccion_productos"] = $orden_produccion;
	$_SESSION["estado_productos"] = $estado;
	$_SESSION["fecha_desde_productos"] = $fecha_desde;
	$_SESSION["fecha_hasta_productos"] = $fecha_hasta;
}

$titulo_pagina="Flota de productos > Listado";
$pagina  = "productos";
include ("../includes/header.php");
echo '<script type="text/javascript" src="../js/productos/productos.js"></script>';
?>

<div class="separador"></div>   
<?php include("../includes/menu_productos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar"><?php include ("../includes/sidebar.php"); ?></div>
    
   	<h3> Listado de la flota de productos </h3>
    <h4> Buscar productos </h4>
    <form id="BuscadorNombreProducto" name="buscadorNombreProducto" action="productos.php" method="get" class="Buscador">
    	<table style="border:0;">
    	<?php 
	        if($esAdminGlobal || $esUsuarioGes){?>
	            <tr style="border:0;">
	                <td style="vertical-align: top;">
	                    <div class="Label">Sede</div>
	                    <select id="sedes" name="sedes" class="BuscadorInput">
                            <option value="0"></option>
                            <?php
                                // Obtenemos todas las sedes
                                $resultados_sedes = $sede->dameSedesFabrica();
                                for($i=0;$i<count($resultados_sedes);$i++) {
                                    $id_sede_res = $resultados_sedes[$i]["id_sede"];
                                    $nombre_sede = $resultados_sedes[$i]["sede"];

                                    echo '<option value="'.$id_sede_res.'"';
                                    if($id_sede_res == $id_sede){
                                        echo ' selected="selected"';
                                    }
                                    echo '>'.$nombre_sede.'</option>';
                                }
                            ?>
	                    </select>
	                 </td>
	                 <td></td>
	                 <td></td>
	            </tr>
	    <?php 
	        } ?>
    		<tr style="border:0;">
        		<td>
            		<div class="Label">Num. Serie</div>
           			<input type="text" id="num_serie" name="num_serie" class="BuscadorInput" value="<?php echo $_SESSION["num_serie_productos"];?>" />
            	</td>
            	<td>
            		<div class="Label">Codigo OP</div>
            		<input type="text" id="codigo_op" name="codigo_op" class="BuscadorInput" value="<?php echo $_SESSION["codigo_op_productos"];?>" />
            	</td>
            	<td>
            		<div class="Label">ID Producción</div>
            		<input type="text" id="orden_produccion" name="orden_produccion" class="BuscadorInput" value="<?php echo $_SESSION["orden_produccion_productos"]; ?>" onkeypress="return soloNumeros(event)" />
            	</td>
        	</tr>
        	<tr style="border:0;">
            	<td>
            		<div class="Label">Producto</div>
            		<select id="nombre_producto" name="nombre_producto" class="BuscadorInput">
            		    <option></option>
                        <?php
                            $list_nom_prod->prepararConsulta();
                            $list_nom_prod->realizarConsulta();
                            $resultado_nombres_producto = $list_nom_prod->nombre_productos;

                            for($i=0;$i<count($resultado_nombres_producto);$i++) {
                                $datoNomProd = $resultado_nombres_producto[$i];
                                $nom_prod->cargaDatosNombreProductoId($datoNomProd["id_nombre_producto"]);
                                echo '<option value="'.$nom_prod->id_nombre_producto.'"';
                                if ($_SESSION["nombre_producto_productos"] == $nom_prod->id_nombre_producto) { echo ' selected="selected"';}
                                echo '>'.$nom_prod->nombre.'_v'.$nom_prod->version.'</option>';
                            }
                        ?>
            		</select>
            	</td>
            	<td>
            		<div class="Label">Estado</div>
            		<select id="estado" name="estado" class="BuscadorInput">
            		   	<option value=""></option>
                		<option value="BORRADOR"<?php if($_SESSION["estado_productos"] == "BORRADOR") { echo ' selected="selected"'; } ?>>BORRADOR</option>
                		<option value="EN CONSTRUCCION"<?php if($_SESSION["estado_productos"] == "EN CONSTRUCCION") { echo ' selected="selected"'; } ?>>EN CONSTRUCCIÓN</option>
                		<option value="FINALIZADO"<?php if($_SESSION["estado_productos"] == "FINALIZADO") { echo ' selected="selected"'; } ?>>FINALIZADO</option>
                		<option value="ENTREGADO"<?php if($_SESSION["estado_productos"] == "ENTREGADO") { echo ' selected="selected"'; } ?>>ENTREGADO</option>
                	</select>
            	</td>
            	<td></td>
        	</tr>
        	<tr style="border:0">
        		<td>
            		<div class="Label">Fecha desde</div>
           			<input type="text" name="fecha_desde" id="datepicker_productos_desde" class="fechaCal" value="<?php echo $_SESSION["fecha_desde_productos"];?>"/>
            	</td>
            	<td>
            		<div class="Label">Fecha hasta</div>
           			<input type="text" name="fecha_hasta" id="datepicker_productos_hasta" class="fechaCal" value="<?php echo $_SESSION["fecha_hasta_productos"];?>"/>
            	</td>
        	</tr>    
        	<tr style="border:0;">
        		<td colspan="3">
            		<input type="hidden" id="realizandoBusqueda" name="realizandoBusqueda" value="1" />
            		<input type="submit" id="buscar_producto" name="buscar_producto" value="Buscar" />
            	</td>
        	</tr>
    	</table>
    	<br />               
    </form>
    
    <div class="ContenedorBotonCrear">
    	<?php
        	if($_GET["entregado"] == true) {
				echo '<div class="mensaje">El producto ha cambiado su estado a ENTREGADO correctamente</div>';
			}
			if($mostrar_tabla){
				if($num_resultados == NULL or $num_resultados == 0){
	               echo '<div class="mensaje">No se encontraron productos</div>';
	               $mostrar_tabla = false;
	            }
	            else if ($num_resultados == 1){
	            	echo '<div class="mensaje">Se encontró 1 producto</div>';
	            }
	            else{
	            	echo '<div class="mensaje">Se encontraron '.$num_resultados.' productos</div>';
	            }	
        	}
		?>	
    </div>
    
    <?php 
		if($mostrar_tabla)	{ ?>
    		<div class="CapaTabla">
    			<table>
        			<tr>
          				<th>NUM. SERIE</th>
            			<?php if($esAdminGlobal || $esUsuarioGes) { ?> <th>SEDE</th> <?php } ?>
                        <th style="text-align: center;">ID PRODUCTO</th>
                        <th>NOMBRE PRODUCTO</th>
                        <th style="text-align:center;">FECHA E. PREV.</th>
                        <th style="text-align:center;">FECHA ENTREGA</th>
            			<th style="text-align:center;">ID PRODUCCIÓN</th>
            			<th>COD. ORDEN PRODUCCIÓN</th>
                        <th style="text-align:center;">ID PEDIDO</th>
                        <th>NUM. PEDIDO</th>
                        <th style="text-align:center;">FECHA PEDIDO</th>
                        <th>CLIENTE</th>
                    	<th>ESTADO</th>
                        <th> </th>
                    </tr>
          	<?php
				for($i=0;$i<count($resultadosBusqueda);$i++) {
					$datoProducto = $resultadosBusqueda[$i];
					$producto->cargaDatosProductoId($datoProducto["id_producto"]);
					$id_nombre_producto = $producto->id_nombre_producto;
                    $id_pedido = $producto->id_pedido;
                    $fecha_ent_prev_prod = $funciones->cFechaNormal($producto->fecha_entrega_prevista);
                    $fecha_ent_prod = $funciones->cFechaNormal($producto->fecha_entrega);

					$nom_prod->cargaDatosNombreProductoId($id_nombre_producto);
					$op->cargaDatosProduccionId($producto->id_produccion);

                    // Cargamos el pedido asociado al producto
                    $pedido->cargarPedidoId($id_pedido);
                    $num_pedido = $pedido->numero_pedido;
                    $fecha_pedido = $pedido->fecha_pedido;
                    $id_cliente = $pedido->id_cliente;

                    // Cargamos el nombre del cliente
                    $cliente->cargaDatosClienteId($id_cliente);
                    $nombre_cliente = $cliente->nombre;

                    if(empty($id_pedido)) $id_pedido = "-";
                    if(empty($num_pedido)) $num_pedido = "-";
                    if(empty($fecha_pedido)) $fecha_pedido = "-";
                    else $fecha_pedido = $funciones->cFechaNormal($fecha_pedido);
                    if(empty($nombre_cliente)) $nombre_cliente = "-"; ?>

                    <tr>
						<td>
                        	<a href="ver_producto.php?id_producto=<?php echo $producto->id_producto;?>&id_produccion=<?php echo $producto->id_produccion;?>"><?php echo $producto->num_serie;?></a>
						</td>
						<?php 
				        	if($esAdminGlobal || $esUsuarioGes) {?> 
				        		<td>
				        			<?php 
				        				$sede->cargaDatosSedeId($op->id_sede);
				        				$nombre_sede = $sede->nombre; 
				        				echo $nombre_sede; ?>
				        		</td> 
				        <?php 
				    		} 
				    	?>
                        <td style="text-align: center;"><?php echo $producto->id_producto; ?></td>
                        <td><?php echo $nom_prod->nombre.'_v'.$nom_prod->version;?></td>
                        <td style="text-align: center;"><?php echo $fecha_ent_prev_prod; ?></td>
                        <td style="text-align: center;"><?php echo $fecha_ent_prod; ?></td>
						<td style="text-align:center;"><?php echo $op->id_produccion; ?></td>
						<td>
	                        <a href="../orden_produccion/ver_op.php?id=<?php echo $op->id_produccion;?>&nombre=<?php echo $nom_prod->nombre;?>&id_producto=<?php echo $producto->id_producto; ?>">
								<?php echo $op->codigo; ?>
                            </a>
						</td>
						<td style="text-align:center;">
                            <?php echo $id_pedido; ?>
						</td>
                        <td>
                            <?php echo $num_pedido;?>
                        </td>
                        <td style="text-align:center;">
                            <?php echo $fecha_pedido;?>
                        </td>
                        <td>
                            <?php echo $nombre_cliente;?>
                        </td>
						<td>
							<?php 
								if ($producto->estado_producto == "ENTREGADO"){
									echo '<span style="color:green">'.$producto->estado_producto.'</span>';	
								}
								else {
									echo $producto->estado_producto; 
								}
							?>
                        </td>
                        <td>
                        	<?php 
								if($producto->estado_producto == "FINALIZADO"){
							?>		 
                                    <input type="button" id="cambiar_a_entregado" name="cambiar_a_entregado" value="ENT" class="BotonEliminar" onclick="javascript:abrir('entregar_producto.php?id_producto=<?php echo $producto->id_producto;?>')"/></td>
							<?php 
								} 
								if ($producto->estado_producto == "ENTREGADO"){ ?>
									<input type="button" id="ver_datos" name="ver_datos" value="VER" class="BotonEliminar" onclick="javascript:abrir('ver_datos_producto.php?id_producto=<?php echo $producto->id_producto;?>')"/></td>
							<?php
								}
							?>
                    </tr>
            <?php
				}
			?>
        		</table>
      		</div>
        	<br/>
        <?php
		    // PAGINACIÓN
            if((isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) and $resultadosBusqueda != NULL) { ?>
        	    <div style="font: bold 11px Verdana,Arial; margin: 0 auto; padding: 10px 0; width: 350px; text-align: center;">
	                <?php
	            	    if(($pg_pagina - 1) > 0) { ?>
	                	    <a href="productos.php?pg=1&realizandoBusqueda=1&num_serie=<?php echo $_SESSION["num_serie_productos"];?>&codigo_op=<?php echo $_SESSION["codigo_op_productos"];?>&nombre_producto=<?php echo $_SESSION["nombre_producto_productos"];?>&orden_produccion=<?php echo $_SESSION["orden_produccion_productos"];?>&estado=<?php echo $_SESSION["estado_productos"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_productos"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_productos"];?>&sedes=<?php echo $id_sede;?>">Primera&nbsp&nbsp&nbsp</a>
	                        <a href="productos.php?pg=<?php echo $pg_pagina - 1;?>&realizandoBusqueda=1&num_serie=<?php echo $_SESSION["num_serie_productos"];?>&codigo_op=<?php echo $_SESSION["codigo_op_productos"];?>&nombre_producto=<?php echo $_SESSION["nombre_producto_productos"];?>&orden_produccion=<?php echo $_SESSION["orden_produccion_productos"];?>&estado=<?php echo $_SESSION["estado_productos"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_productos"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_productos"];?>&sedes=<?php echo $id_sede;?>"> Anterior</a>
	                <?php
	                    }
	                    else {
	            	        echo 'Primera&nbsp;&nbsp;&nbsp;Anterior';
    	                }
	        
	                    echo ' &nbsp;&nbsp;&nbsp;['.$pg_pagina.' / '.$pg_totalPaginas.']&nbsp;&nbsp;&nbsp;';
	                    if($pg_pagina < $pg_totalPaginas) { ?>
	                	    <a href="productos.php?pg=<?php echo $pg_pagina + 1;?>&realizandoBusqueda=1&num_serie=<?php echo $_SESSION["num_serie_productos"];?>&codigo_op=<?php echo $_SESSION["codigo_op_productos"];?>&nombre_producto=<?php echo $_SESSION["nombre_producto_productos"];?>&orden_produccion=<?php echo $_SESSION["orden_produccion_productos"];?>&estado=<?php echo $_SESSION["estado_productos"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_productos"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_productos"];?>&sedes=<?php echo $id_sede;?>">Siguiente&nbsp&nbsp&nbsp</a>
	                        <a href="productos.php?pg=<?php echo $pg_totalPaginas; ?>&realizandoBusqueda=1&num_serie=<?php echo $_SESSION["num_serie_productos"];?>&codigo_op=<?php echo $_SESSION["codigo_op_productos"];?>&nombre_producto=<?php echo $_SESSION["nombre_producto_productos"];?>&orden_produccion=<?php echo $_SESSION["orden_produccion_productos"];?>&estado=<?php echo $_SESSION["estado_productos"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_productos"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_productos"];?>&sedes=<?php echo $id_sede;?>">Última</a>
    	            <?php
        	    	    }
            	        else {
                		    echo 'Siguiente&nbsp;&nbsp;&nbsp;Última';
            	        }
		      	    ?>
        	    </div>
        	    <br/>
   	    <?php
    	    }
        ?>
    <?php
        }
    ?>
</div>    
<?php include ("../includes/footer.php"); ?>
