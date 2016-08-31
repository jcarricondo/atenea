<?php
// Este fichero muestra informacion sobre el producto
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/basicos/cabina.class.php");
include("../classes/basicos/periferico.class.php");
include("../classes/basicos/kit.class.php");
include("../classes/basicos/software.class.php");
include("../classes/basicos/cliente.class.php");
include("../classes/basicos/nombre_producto.class.php");
include("../classes/basicos/referencia_componente.class.php");  
include("../classes/basicos/listado_referencias_componentes.class.php"); 
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/orden_produccion/incluir_referencia_libre.class.php");
include("../classes/productos/producto.class.php");
permiso(16);

$orden_produccion = new Orden_Produccion();
$producto = new Producto();
$nombre_producto = new Nombre_Producto();
$Cabina = new Cabina();
$Periferico = new Periferico();
$software = new Software();
$Cliente = new Cliente();
$funciones = new Funciones();
$kit = new Kit();
$referencia = new Referencia();

// Obtenemos el id_produccion y el id_producto por url
// y realizamos la carga de datos del producto y la orden de produccion
$id_produccion = $_GET["id_produccion"];
$id_producto = $_GET["id_producto"]; 
$orden_produccion->cargaDatosProduccionId($id_produccion);
$nombre_op = $orden_produccion->codigo;
$unidades = $orden_produccion->unidades;

$producto->cargaDatosProductoId($id_producto);
$id_nombre_producto = $producto->id_nombre_producto;
$num_serie = $producto->num_serie;
$id_cliente = $producto->id_cliente;
$Cliente->cargaDatosClienteId($id_cliente);
$nombre_cliente = $Cliente->nombre;
$fecha_entrega = $producto->fecha_entrega;
$fecha_entrega_prevista = $producto->fecha_entrega_prevista;
$fecha_entrega = $funciones->cFechaNormal($fecha_entrega);
$fecha_entrega_prevista = $funciones->cFechaNormal($fecha_entrega_prevista);
$estado = $producto->estado_producto;

$max_caracteres_ref = 50;
$max_caracteres = 35;
$titulo_pagina = "Productos > Ver Producto";
$pagina = "ver_producto";
include ('../includes/header.php');
?>

<div class="separador"></div> 
<?php include("../includes/menu_productos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>

    <h3> Producto </h3>
    
    <form id="FormularioCreacionBasico" name="verProducto" action="productos.php" method="post">
    	<br />
        <h5> Información del producto </h5>
    	<div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Número de serie </div>
           	<label id="num_serie" class="LabelInfoProducto"><?php echo $num_serie;?></label>
        </div>    
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Nombre del Producto </div>
            	<?php $nombre_producto->cargaDatosNombreProductoId($id_nombre_producto); ?>
           	<label id="producto" class="LabelInfoProducto"><?php echo $nombre_producto->nombre;?></label>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Orden de Producción</div>
       		<label id="nombre_op" class="LabelInfoProducto"><?php echo $nombre_op;?></label>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico"></div>
       		<label id="aux" class="LabelInfoProducto"></label>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Cliente</div>
       		<label id="cliente" class="LabelInfoProducto"><?php if ($nombre_cliente != "") echo $nombre_cliente; else echo "-";?></label>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Fecha de Entrega</div>
       		<label id="fecha_entrega" class="LabelInfoProducto"><?php echo $fecha_entrega;?></label>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Fecha de Entrega Prevista</div>
       		<label id="fecha_entrega_prevista" class="LabelInfoProducto"><?php echo $fecha_entrega_prevista;?></label>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Estado</div>
       		<label id="estado" class="LabelInfoProducto"><?php echo $estado;?></label>
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

        		// Cargamos los datos de orden_produccion_referencias
        		$resultados = $orden_produccion->cargaDatosPorProduccionComponente($id_produccion,$id_produccion_componente);
        		$id_tipo = $resultados[0]["id_tipo_componente"];

        		$id_componente = $resultados[0]["id_componente"];

        		switch ($id_tipo) {
		            case '1':
		                // CABINA
		                $Cabina->cargaDatosCabinaId($id_componente);
		                $nombre_componente = "Cabina";
		                $nombre_componente_principal = "Cabina";
		                $titulo_componente = $Cabina->cabina.'_v'.$Cabina->version;
		                $es_prototipo = ($Cabina->prototipo == 1);
		                $coste_total_componente = 0;
		                break;
		            case '2':
		                // PERIFERICO
		                $Periferico->cargaDatosPerifericoId($id_componente);
		                $nombre_componente = "Periferico";
		                $nombre_componente_principal = "Periferico";
		                $titulo_componente = $Periferico->periferico.'_v'.$Periferico->version;
		                $es_prototipo = ($Periferico->prototipo == 1);
		                $coste_total_componente = 0;
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

	       		if($id_componente != NULL){    
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
	                                <td><?php $referencia->vincularReferenciaProveedor();?></td>
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
		            // Calculamos el siguiente id_tipo_componente para ver asignar el coste total del componente principal (CABINA o PERIFERICO)
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
		$ids_softwares = $orden_produccion->dameIdsSoftwares($id_produccion);
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
	                            $software->cargaDatosSoftwareId($ids_softwares[$j]["id_componente"]);
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
	                        <td><?php $referencia->vincularReferenciaProveedor();?></td>
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
		<br/>
				 
		<div class="ContenedorCamposCreacionBasico">
		    <div class="LabelCreacionBasico">Coste Total Producto</div> 
		    <div class="tituloComponente">
				<table id="tablaTituloPrototipo">
		            <tr>
		              	<td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
		   		       		<?php 
								$precio_total_producto = $precio_total_cabina + $precio_todos_perifericos + $precio_refs_libres;
								echo '<span class="tituloComp">'.number_format($coste_producto, 2, ',', '.').'€'.'</span>'; 
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
								echo '<span class="tituloComp">'.number_format($coste_produccion, 2, ',', '.').'€'.'</span>'; 
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
