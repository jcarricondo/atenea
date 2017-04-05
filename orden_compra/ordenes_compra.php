<?php
// Este fichero muestra el listado de las órdenes de compra
// En este fichero se podrán seleccionar varias órdenes de compra para su modificación, descargar los XLS, mostrar sus referencias, etc.
// Carga de clases y funciones JavaScript
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/control_usuario.class.php");
include("../classes/sede/sede.class.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/proveedor.class.php");
include("../classes/basicos/nombre_producto.class.php");
include("../classes/basicos/direccion.class.php");
include("../classes/basicos/listado_proveedores.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/orden_produccion/listado_ordenes_produccion.class.php");
include("../classes/orden_compra/orden_compra.class.php");
include("../classes/orden_compra/listado_ordenes_compra.class.php");
include("../classes/orden_compra/listado_referencias_oc.class.php");
include("../classes/productos/producto.class.php");
include("../classes/almacen/almacen.class.php");
require("../funciones/pclzip/pclzip.lib.php");
permiso(13);

$control_usuario = new Control_Usuario();
$sede = new Sede();
$funciones = new Funciones();
$prov = new Proveedor();
$nom_prod = new Nombre_Producto();
$np = new listadoProveedores();
$op = new Orden_Produccion();
$o_produccion = new Orden_Produccion();
$oc = new Orden_Compra();
$orden_compra = new Orden_Compra();
$listado_orden_compra = new listadoOrdenesCompra();
$listadoOP = new listadoOrdenesCompra();
$producto = new Producto();
$almacen = new Almacen();

$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$id_almacen = $_SESSION["AT_id_almacen"];
// Comprobamos si es Administrador Global
$esAdminGlobal = $control_usuario->esAdministradorGlobal($id_tipo_usuario);
// Comprobamos si es Administrador o Usuario de Gestion
$esUsuarioGes = $control_usuario->esUsuarioGes($id_tipo_usuario);
$esAdministradorGes = $control_usuario->esAdministradorGes($id_tipo_usuario);

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


// Modificación de una orden de compra
if($_GET["OCompra"] == "modificado") {
	$realizarBusqueda = 1;
}
// Cambio de estado de varias órdenes de compra desde el listado.
// Las órdenes de compra seleccionadas serán modificadas en función de su estado y el estado global que elija el usuario. 
// Obtenemos los ids de las órdenes de compra seleccionadas y cargamos los datos de la orden de compra actual. Después para cada orden de compra se hará la modificación en función de sus estados.  
else if ($_GET["OCompra"] == "cambiar_estado"){
	// Obtenemos los ids por url. Si hay varios, los extraemos uno a uno. 
	// Obtenemos las fechas de entrega. Si hay varias, las extraemos una a una
	$ids = $_GET["ids_compra"];
	$fechas_entrega_vuelo = $_GET["fecha_entrega_vuelo"];
	$ids_compra = explode(",",$ids);
	$fecha_entrega_vuelo = explode(",",$fechas_entrega_vuelo);
	$estado_modificado = $_GET["estado_modificado"];
	$fallo = false;
	
	if($estado_modificado != ""){
		for ($i=0; $i<count($ids_compra); $i++){
			// Cargamos los datos de las Ordenes de Compra
			$oc->cargaDatosOrdenCompraId($ids_compra[$i]);
			$id_produccion = $oc->id_produccion;
			$id_proveedor = $oc->id_proveedor;
			$numero_pedido = $oc->numero_pedido;
			$direccion_entrega = $oc->direccion_entrega;
			$direccion_facturacion = $oc->direccion_facturacion;
			$comentarios = $oc->comentarios;
			$estado_anterior = $oc->estado;
			$tasas = $oc->tasas;
			// En formato BBDD
			$fecha_pedido = $oc->fecha_pedido;
			// Dependiendo del estado, fecha_entrega puede estar en la base de datos (Ya pasó de PEDIDA a RECIBIDA) o calculada al vuelo desde el listado. 
			$fecha_entrega = $oc->fecha_entrega;
			// Si la fecha esta guardada en la base de datos, la ponemos en formato listado. En caso contrario, utilizamos la fecha del listado calculada al vuelo y obtenida por url 
			if($fecha_entrega != "") $fecha_entrega = $funciones->cFechaNormal($fecha_entrega);
			else {
				$fecha_entrega = $fecha_entrega_vuelo[$i];
			}
			$fecha_requerida = $oc->fecha_requerida;
			$fecha_factura = $oc->fecha_factura;
				
			// Guardamos los datos en la clase y actualizamos la base de datos de la misma manera que la modificación de una Orden de Compra
			$oc->datosNuevaCompra($ids_compra[$i],$id_produccion,$id_proveedor,$numero_pedido,$fecha_pedido,$fecha_requerida,$direccion_entrega,$direccion_facturacion,$fecha_factura,$comentarios,$estado_anterior,$estado_modificado,$tasas,$unidades,$fecha_entrega,$nombre_orden_compra);
			$resultado = $oc->guardarCambios();
			if ($resultado != 1){
				$mensaje_error = $oc->getErrorMessage($resultado);
				$fallo = true;
				break;
			}
		}
		if(!$fallo){
			// Las OC que no se modificaran seran aquellas que pasen de GEN -> REC y REC -> GEN
			$modificado_estados = true;
			for ($i=0;$i<count($oc->oc_no_modificadas);$i++){
				// Obtenemos los nombres de las oc no modificadas
				$oc->cargaDatosOrdenCompraId($oc->oc_no_modificadas[$i]);
				$nomOCnoModificadas[] = $oc->orden_compra; 	
			}	
			$modificado_estados = true;
		}
		// En caso de que se trate del Admin Global obtenemos la sede de la operacion
		if($esAdminGlobal || $esUsuarioGes){
			$op->cargaDatosProduccionId($oc->id_produccion);
			$id_sede = $op->id_sede;
		}
		$realizarBusqueda = 1;
	}
	else {
		echo '<script type="text/javascript">alert("Debe elegir un estado para modificar las órdenes de compra seleccionadas");</script>';
	}
} 
else if ($_GET["OCompra"] == "multiple"){
	$ids = $_GET["ids_compra"];
	$ids_compra = explode(",",$ids);

	// Comprobamos que se ha seleccionado alguna orden de compra 
	if(!((count($ids_compra) == 1) and ($ids_compra[0] == NULL))){
		// Obtenemos directorio actual y creamos la carpeta que contendra las carpetas de los proveedores
		$dir_actual = getcwd(); 
		//mkdir($dir_actual."\ORDENES_COMPRA", 0700); //LOCAL
		mkdir($dir_actual."/ORDENES_COMPRA", 0700);
		//$dir_descarga = $dir_actual."\ORDENES_COMPRA"; //LOCAL
		$dir_descarga = $dir_actual."/ORDENES_COMPRA";
		$dir_actual = $dir_descarga;

		// Por cada Orden de Compra obtenemos su proveedor, generamos su carpeta (si no existe) y guardamos sus pdf OC
		for ($k=0; $k<count($ids_compra); $k++){
			// Obtenemos el id y el nombre del proveedor de la OC
			$oc->dameProveedorOC($ids_compra[$k]);
			$id_proveedor = $oc->id_proveedor;
			$prov->cargaDatosProveedorId($id_proveedor["id_proveedor"]);
			$nombre_prov = utf8_decode(strtoupper($prov->nombre));
		
			// Comprobamos si ya esta creada la carpeta del proveedor
			if (count($proveedores) == 0){
				//mkdir($dir_descarga.'\\'.$nombre_prov, 0700); //LOCAL
				mkdir($dir_descarga.'/'.$nombre_prov, 0700);
				//$dir_actual = $dir_actual.'\\'.$nombre_prov; //LOCAL
				$dir_actual = $dir_actual.'/'.$nombre_prov;
				include("../orden_compra/fra_request_multiple.php");
				// Guarda en un array los proveedores
				$proveedores[] = $id_proveedor["id_proveedor"];
			}
			else {
				// Si no esta el proveedor creamos la carpeta
				if (in_array($id_proveedor["id_proveedor"], $proveedores, true)) {
    				//$dir_actual = $dir_actual.'\\'.$nombre_prov; //LOCAL
    				$dir_actual = $dir_actual.'/'.$nombre_prov;
					include("../orden_compra/fra_request_multiple.php");
				}
				else {
					//mkdir($dir_descarga.'\\'.$nombre_prov, 0700); //LOCAL
					mkdir($dir_descarga.'/'.$nombre_prov, 0700);
					//$dir_actual = $dir_actual.'\\'.$nombre_prov; //LOCAL
					$dir_actual = $dir_actual.'/'.$nombre_prov;
					include("../orden_compra/fra_request_multiple.php");
					// Guarda en un array los proveedores
					$proveedores[] = $id_proveedor["id_proveedor"];
				}		
			}	
			$dir_actual = $dir_descarga;
		}	

		// Comprimimos la carpeta y generamos el zip 
		$filename = "ORDENES_COMPRA.zip";
		$zip = new PclZip('ORDENES_COMPRA.zip');
		$zip->create("ORDENES_COMPRA");

		// Llamada para abrir o descargar el zip
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename=".$filename);
		header("Expires: 0");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($filename));
		header("Pragma: public");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private, false");
		header("Content-Description: File Transfer"); 
		readfile($filename);
	
		// Eliminamos la carpeta creada con sus archivos
   		$funciones->eliminarDir($dir_descarga);    
		// Eliminamos el zip temporal
		unlink($filename);
	}	
	else {
		echo '<script type="text/javascript">alert("No ha seleccionado ninguna Orden de Compra para descargar");</script>';	
	}
}

if(isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) {
	$mostrar_tabla = true;
	
	// Obtenemos la sede seleccionada por el Admin Global
    if(($esAdminGlobal || $esUsuarioGes) and $_GET["OCompra"] != "cambiar_estado"){
        if($_GET["enlace_op"] == 1){
        	// Obtenemos la sede segun la OP
        	$orden_produccion = $_GET["orden_produccion"];
        	$id_produccion = $orden_produccion[0];
        	$op->cargaDatosProduccionId($id_produccion);
        	$id_sede = $op->id_sede;
        }	
        else {
        	$id_sede = $_GET["sedes"];
        }
    }

	// Buscador órdenes de compra
	if($_GET["OCompra"] != "cambiar_estado"){
		$proveedor = $_GET["proveedor"];
		$fecha_pedido = $_GET["fecha_pedido"];
		$fecha_entrega = $_GET["fecha_entrega"];
		$estado = $_GET["estado"];
		$orden_produccion = $_GET["orden_produccion"];
		$n_pedido = addslashes($_GET["n_pedido"]);
		$fecha_desde = $_GET["fecha_desde"];
		$fecha_hasta = $_GET["fecha_hasta"];
		$estado_op = $_GET["estado_op"];

		// Si las fechas del buscador no estan vacias las convertimos a formato MySQL para poder sacar los resultados 
		if($fecha_pedido != "") $fecha_pedido = $funciones->cFechaMy($fecha_pedido);
		if($fecha_entrega != "") $fecha_entrega = $funciones->cFechaMy($fecha_entrega);
		if($fecha_desde != "") $fecha_desde = $funciones->cFechaMy($fecha_desde);
		if($fecha_hasta != "") $fecha_hasta = $funciones->cFechaMy($fecha_hasta);
		
		$listado_orden_compra->setValores($proveedor,$fecha_pedido,$dir_entrega,$orden_produccion,$fecha_requerida,$estado,$n_pedido,$fecha_desde,$fecha_hasta,$fecha_entrega,$estado_op,$id_sede,'');
		$listado_orden_compra->realizarConsulta();
		$resultadosBusqueda = $listado_orden_compra->ordenes_compra; 
		$num_resultados = count($resultadosBusqueda);

		// Se realiza la consulta con paginacion 
		$pg_totalPaginas = ceil(count($resultadosBusqueda) / $pg_registros);
		$listado_orden_compra->setValores($proveedor,$fecha_pedido,$dir_entrega,$orden_produccion,$fecha_requerida,$estado,$n_pedido,$fecha_desde,$fecha_hasta,$fecha_entrega,$estado_op,$id_sede,$paginacion);
		$listado_orden_compra->realizarConsulta();
		$resultadosBusqueda = $listado_orden_compra->ordenes_compra; 
	
		// Volvemos a convertir las fechas a formato listado
		if($fecha_pedido != "") $fecha_pedido = $funciones->cFechaNormal($fecha_pedido);
		if($fecha_entrega != "") $fecha_entrega = $funciones->cFechaNormal($fecha_entrega);
		if($fecha_desde != "") $fecha_desde = $funciones->cFechaNormal($fecha_desde);
		if($fecha_hasta != "") $fecha_hasta = $funciones->cFechaNormal($fecha_hasta);
	}
	// Modificación de varias órdenes de compra
	else{
		// Reseteamos las fechas para que se muestren todas las órdenes de compra en el listado
		$fecha_pedido = "";
		$fecha_entrega = "";
		$listado_orden_compra->setValores($proveedor,$fecha_pedido,$dir_entrega,$orden_produccion,$fecha_requerida,$estado,$n_pedido,$fecha_desde,$fecha_hasta,$fecha_entrega,$estado_op,$id_sede,'');
		$listado_orden_compra->realizarConsulta();
		$resultadosBusqueda = $listado_orden_compra->ordenes_compra;
		$num_resultados = count($resultadosBusqueda);

		// Se realiza la consulta con paginacion 
		$pg_totalPaginas = ceil(count($resultadosBusqueda) / $pg_registros);
	}
		
	// Volvemos a reasignar la variables para que se muestren correctamente en el buscador
	$proveedor = $_GET["proveedor"];
	$fecha_pedido = $_GET["fecha_pedido"];
	$fecha_entrega = $_GET["fecha_entrega"];
	$estado = $_GET["estado"];
	$orden_produccion = $_GET["orden_produccion"];
	$n_pedido = addslashes($_GET["n_pedido"]);
	$fecha_desde = $_GET["fecha_desde"];
	$fecha_hasta = $_GET["fecha_hasta"];
	$estado_op = $_GET["estado_op"];
				
	// Guardar las variables del formulario en variable de sesion
	$_SESSION["proveedor_orden_compra"] = $proveedor;
	$_SESSION["fecha_pedido_orden_compra"] = $fecha_pedido;
	$_SESSION["fecha_entrega_orden_compra"] = $fecha_entrega;
	$_SESSION["estado_orden_compra"] = $estado;
	$_SESSION["orden_produccion_orden_compra"] = $orden_produccion;
	$_SESSION["n_pedido_orden_compra"] = stripslashes(htmlspecialchars($n_pedido));
	$_SESSION["estado_op_orden_compra"] = $estado_op;
	$_SESSION["fecha_desde_orden_compra"] = $fecha_desde;
	$_SESSION["fecha_hasta_orden_compra"] = $fecha_hasta;
}
$titulo_pagina="ÓRDENES DE COMPRA";
$pagina = "ordenes_compra";
include ("../includes/header.php");
echo '<script type="text/javascript" src="../js/orden_compra/ordenes_compra.js"></script>';
?>

<div class="separador"></div> 
<?php include("../includes/menu_oc.php");?> 

<div id="ContenedorCentral">
	<div id="ContenedorSidebar"><?php include ("../includes/sidebar.php"); ?></div>
    
  	<h3> Listado &Oacute;rdenes de Compra</h3>
    <h4> Buscar &Oacute;rdenes de Compra </h4>
    <form id="BuscadorOrdenCompra" name="buscadorOrdenCompra" action="ordenes_compra.php" method="get" class="Buscador">
    <table style="border:0;">
    <?php 
       	if($esAdminGlobal || $esUsuarioGes){ 
			$res_sedes = $sede->dameSedesFabrica(); ?>
    		<tr style="border:0;">
    			<td style="vertical-align:top;">
    				<div class="Label">Sede</div>
        			<select id="sedes" name="sedes" class="BuscadorInput" onchange="cargaOPsPorSede(this.value)">
                        <option value="0"></option>
                        <?php
                            for($i=0;$i<count($res_sedes);$i++){
                                $id_sede_bus = $res_sedes[$i]["id_sede"];
                                $nombre_sede = $res_sedes[$i]["sede"];

                                echo '<option value='.$id_sede_bus;
                                if($id_sede_bus == $id_sede){
                                    echo ' selected="selected"';
                                }
                                echo '>'.$nombre_sede.'</option>';
                            }
                        ?>
        			</select>
        		</td>
        	</tr>
    <?php 
    	}
    ?>

    <tr style="border:0;">
      	<td>
          	<div class="Label">Nº Pedido</div>
           	<input type="text" id="n_pedido" name="n_pedido" value="<?php echo $_SESSION["n_pedido_orden_compra"]; ?>" class="BuscadorInput">           	
		</td>
        <td>
           	<div class="Label">Fecha desde</div>
        	<input type="text" name="fecha_desde" id="datepicker_orden_compra_desde" class="fechaCal" value="<?php echo $_SESSION["fecha_desde_orden_compra"];?>"/>
        </td>
        <td>
          	<div class="Label">Fecha hasta</div>
        	<input type="text" name="fecha_hasta" id="datepicker_orden_compra_hasta" class="fechaCal" value="<?php echo $_SESSION["fecha_hasta_orden_compra"];?>"/>
        </td>
    </tr>
    <tr style="border:0;">
       	<td>
       		<div class="Label">Estado</div>
           	<select id="estado" name="estado" class="BuscadorInput">
           	<?php 
				$num_estados = 6;
				$estado = array ("GENERADA","PEDIDO INICIADO","PEDIDO CERRADO","PARCIALMENTE RECIBIDO","RECIBIDO","STOCK");
				for($i=-1;$i<$num_estados;$i++) {
					echo '<option value="'.$estado[$i].'"';
						if ($estado[$i] == $_SESSION["estado_orden_compra"])
							echo ' selected="selected" ';
						echo '>'.$estado[$i].'</option>';
				}
			?>
           	</select>
        </td>
        <td>
        	<div class="Label">Fecha pedido</div>
           	<input type="text" name="fecha_pedido" id="datepicker_orden_compra_pedido" class="fechaCal" value="<?php echo $_SESSION["fecha_pedido_orden_compra"];?>"/>
        </td>
        <td>
        	<div class="Label">Fecha entrega</div>
           	<input type="text" name="fecha_entrega" id="datepicker_orden_compra_entrega" class="fechaCal" value="<?php echo $_SESSION["fecha_entrega_orden_compra"];?>"/>
        </td>
    </tr>
    <tr style="border:0;">
      	<td>
           	<div class="Label">Estado OP</div>
           	<select id="estado_op" name="estado_op" class="BuscadorInput">
           	<?php 
				$num_estados = 3;
				$estado = array ("BORRADOR","INICIADO","FINALIZADO");
				for ($i=-1;$i<$num_estados;$i++){
					echo '<option value="'.$estado[$i].'"';
						if ($estado[$i] == $_SESSION["estado_op_orden_compra"])
							echo ' selected="selected"';
					echo '>'.$estado[$i].'</option>';		
				}
			?>
           	</select>
        </td>
        <td></td>
        <td></td>
	</tr>
    <tr id="fila_cambios_sede" style="border:0;">
    	<td>
			<div class="Label">Orden de Producción</div>
           	<select multiple="multiple" id="orden_produccion[]" name="orden_produccion[]" class="BuscadorOCEstadosOP" size="4">
           	<?php 
				// Sacar el listado de todas las OP. 
				$listadoOP->prepararOP($id_sede);
				$listadoOP->realizarConsultaOP();
				$resultados_op = $listadoOP->orden_produccion; 
					
				//Si una OP tiene alias != NULL mostrar alias
				for ($i=-1; $i<count($resultados_op); $i++){
					if ($i == -1) echo '<option value=""></option>';
					else{
						$op->cargaDatosProduccionId($resultados_op[$i]["id_produccion"]);	
						if ($op->alias_op != NULL){
							echo '<option value="'.$op->id_produccion.'"';
							for ($j=0;$j<count($_SESSION["orden_produccion_orden_compra"]);$j++){
								if ($_SESSION["orden_produccion_orden_compra"][$j] == $op->id_produccion) { echo ' selected="selected"'; }
							}
							echo '>'.$op->alias_op.'</option>';	
						}
						else {
							echo '<option value="'.$op->id_produccion.'"';
							for ($j=0;$j<count($_SESSION["orden_produccion_orden_compra"]);$j++){
								if ($_SESSION["orden_produccion_orden_compra"][$j] == $op->id_produccion) { echo ' selected="selected"'; }
							}
							echo '>'.$op->codigo.'</option>';	
						}
					}
				}
			?>
           	</select>	        
        </td>
        <td>
        	<div class="Label">Proveedor</div>
        	<select multiple="multiple" id="proveedor[]" name="proveedor[]" class="BuscadorOCEstadosOP" size="4">
       	 	<?php 
				// Sacamos el listado de los proveedores
				$np->prepararConsulta();
				$np->realizarConsulta();
				$resultado_proveedores = $np->proveedores;

				for($i=-1;$i<count($resultado_proveedores);$i++) {
					if ($i == -1) echo '<option value=""></option>';
					else {
						$datoProveedor = $resultado_proveedores[$i];
						$prov->cargaDatosProveedorId($datoProveedor["id_proveedor"]);
						echo '<option value="'.$prov->nombre.'"';
						for ($j=0;$j<count($_SESSION["proveedor_orden_compra"]);$j++){
							if ($_SESSION["proveedor_orden_compra"][$j] == $prov->nombre) { echo ' selected="selected"'; }
						}
						echo '>'.$prov->nombre.'</option>';	
					}
				}
			?> 	
           	</select>
        </td>
        <td></td>
    </tr>
    <tr style="border:0;">	       
        <td colspan="3">
           	<input type="hidden" id="realizandoBusqueda" name="realizandoBusqueda" value="1" />
           	<input type="submit" id="buscar_orden_compra" name="buscar_orden_compra" value="Buscar" />
        </td>
    </tr>
    </table>
   	<br />
    </form>
                    
    <div class="ContenedorBotonCrear">
		<?php
			if($_GET["OCompra"] == "modificado") {
				echo '<div class="mensaje">La orden de compra se ha modificado correctamente</div>';
			}
			if($modificado_estados){
				// Llamada a javascript para mostrar las ordenes de compra que no se han modificado.
				for($i=0;$i<count($nomOCnoModificadas);$i++){
					echo '<input type="hidden" name="oc_no_modificadas[]" id="oc_no_modificadas[]" value="'.$nomOCnoModificadas[$i].'"/>';		
				}
				if (count($nomOCnoModificadas) != 0){
					echo '<script type="text/javascript">muestraOCnoModificadas();</script>';
				}
				echo '<div class="mensaje">Se han modificado correctamente los estados de las órdenes de compra</div>';	
			}
			if($mensaje_error != "") {
				echo '<div class="mensaje_error">'.$mensaje_error.'</div>';
			}
		?>
    </div>
    <?php 
    	if($_GET["OCompra"] == "pedidos_enviados") { ?>
    		<div style="width: 600px; margin: 0 auto;">
				<div class="mensaje">El proceso de envío de emails de pedidos se ha completado correctamente (<a href="javascript:abrir('ver_detalle_envio.php')">Ver registro</a>)</div>
			</div>
	<?php
		}
	?>
	<div class="ContenedorBotonCrear">
		<?php
			if($mostrar_tabla){
				if($num_resultados == NULL or $num_resultados == 0){
	               echo '<div class="mensaje">No se encontraron órdenes de compra</div>';
	               $mostrar_tabla = false;
	            }
	            else if ($num_resultados == 1){
	            	echo '<div class="mensaje">Se encontró 1 orden de compra</div>';
	            }
	            else{
	            	echo '<div class="mensaje">Se encontraron '.$num_resultados.' órdenes de compra</div>';
	            }	
        	}
		?>
	</div>
	<?php	
		if($mostrar_tabla) { ?>
    		<div class="CapaTabla">
    		<table>
        	<tr>
            	<th><input type="checkbox" name="todas_oc" id="todas_oc" onclick="TodasOC();"/></th>
            	<th>ORDEN DE COMPRA</th>
        		<th>ORDEN PRODUCCIÓN</th>
        		<?php if($esAdminGlobal || $esUsuarioGes){ ?> <th>SEDE</th> <?php } ?>
        		<th>ESTADO OP</th>
	           	<th>PROVEEDOR</th>       
                <th style="text-align:center">REFERENCIAS</th>          
            	<th style="text-align:center">FECHA PEDIDO</th>           
            	<th style="text-align:center">FECHA ENTREGA PREVISTA</th>
                <th style="text-align:center">FECHA PAGO PREVISTA</th>              
            	<th>ESTADO</th> 
            	<th style="text-align:center">RECEPCION</th>  
                <th style="text-align:center">IMPORTE</th>                                                                
        	</tr>
        <?php
			$error_fechas = false;
			for($i=0;$i<count($resultadosBusqueda);$i++) {
				$fe_no_calculada = false;
				$fp_no_calculada = false;
				$datoOrdenCompra = $resultadosBusqueda[$i];
				$orden_compra->cargaDatosOrdenCompraId($datoOrdenCompra["id_orden_compra"]);
				$o_produccion->cargaDatosProduccionId($orden_compra->id_produccion);
				//$o_produccion->dameIdProductoSinActivo($orden_compra->id_produccion);
				$o_produccion->dameIdProducto($orden_compra->id_produccion);
				$id_producto = $o_produccion->id_producto["id_producto"];
				$producto->dameIdsNombreProducto($id_producto);
				$id_nombre_producto = $producto->id_nombre_producto["id_nombre_producto"];
				$nom_prod->cargaDatosNombreProductoId($id_nombre_producto);
				$prov->cargaDatosProveedorId($orden_compra->id_proveedor);
				$tiempo_suministro = $prov->tiempo_suministro;
				$metodo_pago = $prov->metodo_pago;
				// Convertimos las fechas de la BBDD con formato MySql a formato del listado
				$fecha_pedido = $o_produccion->cFechaNormal($orden_compra->fecha_pedido);
				$fecha_entrega = $o_produccion->cFechaNormal($orden_compra->fecha_entrega);
				
				// Si la fecha_entrega no esta guardada en la BBDD la calculamos al vuelo
				if ($fecha_entrega == NULL){
					$dias = 0;
					if ($tiempo_suministro == 0) {
						$dias = $dias + 0;
						$fe_no_calculada = true;
						$error_fechas = true;
					}
					else if ($tiempo_suministro == 1) $dias = $dias + 7;
					else if ($tiempo_suministro == 2) $dias = $dias + 14;
					else if ($tiempo_suministro == 3) $dias = $dias + 30;
					else if ($tiempo_suministro == 4) $dias = $dias + 60;
					else $dias = $dias + 90;
	
					// Convertimos la fecha de pedido para poder calcular la fecha de entrega y la volvemos a reconvertir a formato listado
					$fecha_pedido = $funciones->cFechaMyEsp($fecha_pedido);
					$fecha_entrega = date("m/d/Y", strtotime($fecha_pedido." +".$dias." days"));
					$fecha_pedido = $funciones->cFechaMyEsp($fecha_pedido);	
				}
				else {
					$fecha_entrega = $o_produccion->cFechaNormal($fecha_entrega);
					$fecha_entrega = $o_produccion->cFechaMyEsp($fecha_entrega);
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
				
				// Convertimos la fecha de pedido para poder calcular la fecha de pago y la volvemos a reconvertir a formato listado
				$fecha_pedido = $funciones->cFechaMyEsp($fecha_pedido);
				$fecha_pago = date("m/d/Y", strtotime($fecha_pedido." +".$dias." days"));
				$fecha_pedido = $funciones->cFechaMyEsp($fecha_pedido);

				$fecha_entrega = $o_produccion->cFechaMyEsp($fecha_entrega);
				$fecha_pago = $o_produccion->cFechaMyEsp($fecha_pago);
		?>
			<tr>
	           	<td><input type="checkbox" id="chkbox[]" name="chkbox[]" value="<?php echo $orden_compra->id_compra;?>" /></td>
				<td><a href="mod_orden_compra.php?id_compra=<?php echo $orden_compra->id_compra;?>"><?php echo /*$orden_compra->orden_compra*/ $orden_compra->numero_pedido; ?></a></td>
                <td>
					<a href="../orden_produccion/ver_op.php?id=<?php echo $o_produccion->id_produccion;?>&nombre=<?php echo $nom_prod->nombre;?>&id_producto=<?php echo $id_producto;?>&id_compra=<?php echo $orden_compra->id_compra;?>">
					<?php 
						// Si tiene alias mostramos el alias. Si no mostramos la OP
						if (($o_produccion->alias_op != NULL) && ($o_produccion->alias_op != $o_produccion->codigo)){
							echo $o_produccion->alias_op;
						}
						else{
							echo $o_produccion->codigo; 
						}
					?>
                    </a>
				</td>
				<?php 
        			if($esAdminGlobal || $esUsuarioGes) {?> 
        				<td>
		        			<?php 
		        				$sede->cargaDatosSedeId($o_produccion->id_sede);
		        				$nombre_sede = $sede->nombre; 
		        				echo $nombre_sede; ?>
		        		</td> 
		        <?php 
		    		} 
		    	?>
				<td><?php echo $o_produccion->estado; ?></td>
				<td>
					<a href="../basicos/mod_proveedor.php?id=<?php echo $orden_compra->id_proveedor;?>">
						<?php echo $orden_compra->nombre_prov; ?>
					</a>	
				</td>
               	<td style="text-align:center">
                	<a href="javascript:abrir('muestra_referencias_oc.php?orden_compra=<?php echo $orden_compra->id_compra;?>&id_proveedor=<?php echo $orden_compra->id_proveedor;?>&codigo_oc=<?php echo 'OP'.$orden_compra->id_produccion.$orden_compra->nombre_prov;?>')">VER</a>
                    -
                    <a href="../orden_compra/informe_referencias_oc.php?id_compra=<?php echo $orden_compra->id_compra;?>">XLS</a>
                </td>
                <td style="text-align:center"><?php echo $fecha_pedido;?></td>
                <td style="text-align:center">
                	<?php 
						if (!$fe_no_calculada) echo $fecha_entrega;
						else echo '<span style="color:red">'.$fecha_entrega.'</span>';
					?>
   					<input type="hidden" id="fecha_entrega_vuelo[]" name="fecha_entrega_vuelo[]" value="<?php echo $fecha_entrega;?>"/>
                </td>
                <td style="text-align:center">
					<?php 
						if (!$fp_no_calculada) echo $fecha_pago;
						else echo '<span style="color:red">'.$fecha_pago.'</span>';
					?>
				</td>
				<td><?php echo $orden_compra->estado; ?></td>
				<td style="text-align:center">
					<?php
						$orden_compra->getPorcentajeRecepcion();
					?>
                    <div align="center">
						<div class="barra_progreso">
							<div class="barra_progreso_activa" style="width: <?php echo $orden_compra->porcentaje_recepcion; ?>px; !important"></div>
						</div>
                    </div>    
				</td>
                <td style="text-align:center">
                	<?php 
                		$orden_compra->damePrecioOC($orden_compra->id_compra,$orden_compra->id_proveedor);
						$precio = $orden_compra->precio[0]["precio"];
						$precio_tasas = $orden_compra->tasas;
						$precio_total = $precio + $precio_tasas;
						echo number_format($precio_total, 2, '.', '').'€';
					?>
                </td>
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
		                    <a href="ordenes_compra.php?pg=1&realizandoBusqueda=1&proveedor=<?php echo $_SESSION["proveedor_orden_compra"];?>&fecha_pedido=<?php echo $_SESSION["fecha_pedido_orden_compra"];?>&fecha_entrega=<?php echo $_SESSION["fecha_entrega_orden_compra"];?>&estado=<?php echo $_SESSION["estado_orden_compra"];for($i=0;$i<count($_SESSION["orden_produccion_orden_compra"]);$i++){echo '&orden_produccion[]='.$_SESSION["orden_produccion_orden_compra"][$i];}?>&n_pedido=<?php echo $_SESSION["n_pedido_orden_compra"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_orden_compra"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_orden_compra"];?>&estado_op=<?php echo $_SESSION["estado_op_orden_compra"];?>&sedes=<?php echo $id_sede;?>">Primera&nbsp&nbsp&nbsp</a>
		                    <a href="ordenes_compra.php?pg=<?php echo $pg_pagina - 1;?>&realizandoBusqueda=1&proveedor=<?php echo $_SESSION["proveedor_orden_compra"];?>&fecha_pedido=<?php echo $_SESSION["fecha_pedido_orden_compra"];?>&fecha_entrega=<?php echo $_SESSION["fecha_entrega_orden_compra"];?>&estado=<?php echo $_SESSION["estado_orden_compra"];for($i=0;$i<count($_SESSION["orden_produccion_orden_compra"]);$i++){echo '&orden_produccion[]='.$_SESSION["orden_produccion_orden_compra"][$i];}?>&n_pedido=<?php echo $_SESSION["n_pedido_orden_compra"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_orden_compra"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_orden_compra"];?>&estado_op=<?php echo $_SESSION["estado_op_orden_compra"];?>&sedes=<?php echo $id_sede;?>"> Anterior</a>
		            <?php  
		                }  
		                else {
		                    echo 'Primera&nbsp;&nbsp;&nbsp;Anterior';
		                }
		        
		                echo ' &nbsp;&nbsp;&nbsp;['.$pg_pagina.' / '.$pg_totalPaginas.']&nbsp;&nbsp;&nbsp;';
		                if($pg_pagina < $pg_totalPaginas) { ?>
		                    <a href="ordenes_compra.php?pg=<?php echo $pg_pagina + 1;?>&realizandoBusqueda=1&proveedor=<?php echo $_SESSION["proveedor_orden_compra"];?>&fecha_pedido=<?php echo $_SESSION["fecha_pedido_orden_compra"];?>&fecha_entrega=<?php echo $_SESSION["fecha_entrega_orden_compra"];?>&estado=<?php echo $_SESSION["estado_orden_compra"];for($i=0;$i<count($_SESSION["orden_produccion_orden_compra"]);$i++){echo '&orden_produccion[]='.$_SESSION["orden_produccion_orden_compra"][$i];}?>&n_pedido=<?php echo $_SESSION["n_pedido_orden_compra"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_orden_compra"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_orden_compra"];?>&estado_op=<?php echo $_SESSION["estado_op_orden_compra"];?>&sedes=<?php echo $id_sede;?>">Siguiente&nbsp&nbsp&nbsp</a>
		                    <a href="ordenes_compra.php?pg=<?php echo $pg_totalPaginas; ?>&realizandoBusqueda=1&proveedor=<?php echo $_SESSION["proveedor_orden_compra"];?>&fecha_pedido=<?php echo $_SESSION["fecha_pedido_orden_compra"];?>&fecha_entrega=<?php echo $_SESSION["fecha_entrega_orden_compra"];?>&estado=<?php echo $_SESSION["estado_orden_compra"];for($i=0;$i<count($_SESSION["orden_produccion_orden_compra"]);$i++){echo '&orden_produccion[]='.$_SESSION["orden_produccion_orden_compra"][$i];}?>&n_pedido=<?php echo $_SESSION["n_pedido_orden_compra"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_orden_compra"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_orden_compra"];?>&estado_op=<?php echo $_SESSION["estado_op_orden_compra"];?>&sedes=<?php echo $id_sede;?>">Última</a>
		            <?php        
	    	            } 
	        	        else {
	            	        echo 'Siguiente&nbsp;&nbsp;&nbsp;Última'; 
	            	    }
			    	?>
	        		</div>
	   		<?php
	        	} 
	        	if ($error_fechas) {
			?>
					<div class="CapaTabla">
                    	<div class="mensaje_error">Algunas fechas no fueron calculadas debido a que hay proveedores sin tiempo de suministro o sin método de pago</div> 
                    </div>
            <?php 
				}
				if (count($resultadosBusqueda) != 0){ 
			?>
					<div class="CapaTabla">
            		<br />
            	<?php 
            		if($esAdministradorGes){ ?>	
	            		<div>
	                    	<div class="LabelOpcionesOC">Enviar emails de Pedidos</div>
	   						<input type="button" id="enviarEmailPedidos" name="enviarEmailPedidos" value="Enviar email de Pedidos" class="BotonOpcionesOC" onclick="javascript:enviarEmailPedidos();"/>                 
	                    </div>
	               		<br /><br />
				<?php 
					}
				?>	           
                    <div>
                    	<div class="LabelOpcionesOC">Descargar XLS de las Órdenes de Compra</div>
   						<input type="button" id="descargar_XLS_OC" name="descargar_XLS_OC" value="Descargar XLS" class="BotonOpcionesOC" onclick="javascript:descargar_XLS_OC();"/>                 
                    </div>
               		<br /><br />
            	<?php 
            		if($esAdministradorGes){ ?>
	                    <div>
	                    	<div class="LabelOpcionesOC">Cambiar estado de las Órdenes de Compra</div>
	                    	<input type="button" id="cambiar_estado_OC" name="cambiar_estado_OC" value="Cambiar estado" class="BotonOpcionesOC" onclick="javascript:cambiar_estado_OC();"/>
	                        <select id="estado_OC" name="estado_OC" class="SelectCambiarEstadoOC">
	                        	<?php 
									$num_estados = 4;
									$estado = array ("GENERADA","PEDIDO INICIADO","PEDIDO CERRADO","STOCK");
									for($i=-1;$i<$num_estados;$i++) {
										echo '<option value="'.$estado[$i].'">'.$estado[$i].'</option>';
									}
								?>
	                        </select>
	                    </div>
                    	<br /><br />
            	<?php 
            		}
            	?>
                    <div>
                    	<div class="LabelOpcionesOC">Descarga multiple de las Órdenes de Compra</div>
   						<input type="button" id="descarga_multiple" name="descarga_multiple" value="Descarga Multiple" class="BotonOpcionesOC" onclick="javascript:descarga_multiple();"/>                 
                    </div>
            </div>
            <?php
				}
		}
	?>   
</div>     
<?php include ("../includes/footer.php"); ?>