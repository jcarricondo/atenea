<?php
// En este fichero se modificarán las Ordenes de Compra
// Carga de clases y funciones JavaScript
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/proveedor.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/basicos/direccion.class.php");
include("../classes/basicos/listado_direcciones.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/orden_compra/orden_compra.class.php");
include("../classes/orden_compra/listado_referencias_oc.class.php");
include("../classes/productos/producto.class.php");
permiso(13);

$funciones = new Funciones();
$db = new MySQL();
$orden_compra = new Orden_Compra();
$prov = new Proveedor();
$op = new Orden_Produccion();

// Comprobamos si el usuario puede modificar 
$modificar_basico = permisoMenu(3);
$modificar_oc = permisoMenu(14);

if(isset($_POST["guardandoOrdenCompra"]) and $_POST["guardandoOrdenCompra"] == 1) {
	// Se reciben los datos
	$proveedor = $_POST["proveedor"];
	$direccion_entrega = $_POST["direccion_entrega"];
	$direccion_facturacion = $_POST["direccion_facturacion"];
	$precio = $_POST["precio"];
	$precio_tasas = $_POST["precio_tasas"];
	$orden_produccion = $_POST["orden_produccion"];
	$fecha_requerida = $_POST["fecha_requerida"];
	$estado = $_POST["estado"];
	$estado_anterior = $_POST["estado_anterior"];
	$importe = $_POST["importe"];
	$id_compra = $_POST["id_compra"];
	$fecha_creado = $_POST["fecha_creado"];
	$fecha_pedido = $_POST["fecha_pedido"];
	$fecha_entrega = $_POST["fecha_entrega"];
	$archivos_tabla = $_POST["archivos_tabla"];
	$archivos_tabla_adjuntos = $_POST["archivos_tabla_adjuntos"];
	
	if (($direccion_entrega == '') or ($precio == '') or ($precio_tasas == '')){
		echo '<script type="text/javascript">alert("Rellene los campos obligatorios")</script>';
	}
	else {
		// Comprobamos que por cada factura adjuntado se tiene su correspondiente importe
		$i=0;
		$fallo = false;
		while (($i<count($_FILES["archivos"]["error"])) and (!$fallo)) {
			if ($_FILES["archivos"]["error"][$i] != 4) {
				$fallo = empty($importe[$i]);
			}
			$i++;
		}
		
		if ($fallo) {
			echo '<script type="text/javascript">alert("Rellene los importes de las facturas")</script>';
		}
		else {
			// Consulta para comprobar que los nombres de las facturas de la tabla son los mismos que los de la base de datos
			// Comprobamos que los nombres de la base de datos estan en archivos_tabla. Si alguno no esta (se elimino mediante checkbox) ponemos su activo a cero.
			$orden_compra->dameNombres_facturas($id_compra,$proveedor);
			$nombres_facturas = $orden_compra->nombres_factura;
			
			// Los guardamos en un array simple
			for ($i=0;$i<count($nombres_facturas);$i++) {
				$nombres_bbdd[]= $nombres_facturas[$i]["nombre_factura"]; 	
			}
			// Comprobamos que las facturas de la base de datos estan en facturas_tabla. Si alguno no esta, ponemos su activo a cero.
			for ($i=0;$i<count($nombres_bbdd);$i++) {
				$encontrado = false;
				$j=0;
				while (($j<count($archivos_tabla)) and (!$encontrado)) {
					$encontrado = $archivos_tabla[$j] == $nombres_bbdd[$i];
					$j++;
				}
				// Si no esta en la tabla es que se ha eliminado y por tanto procedemos a poner su activo a 0;
				if (!$encontrado) {
					$orden_compra->quitarArchivo($nombres_bbdd[$i],$id_compra);
					if ($resultado != 1) {
						$mensaje_error = $orden_compra->getErrorMessage($resultado);
					}	
				}
			}
			
			// Consulta para comprobar que los nombres de los archivos adjuntos de la tabla son los mismos que los de la base de datos
			// Comprobamos que los nombres de la base de datos estan en archivos_tabla_adjuntos. Si alguno no esta (se elimino mediante checkbox) ponemos su activo a cero.
			$orden_compra->dameNombresAdjuntos($id_compra,$proveedor);
			$nombres_adjuntos = $orden_compra->nombres_adjuntos;
			
			// Los guardamos en un array simple
			for ($i=0;$i<count($nombres_adjuntos);$i++) {
				$nombres_adj_bbdd[]= $nombres_adjuntos[$i]["nombre_adjunto"]; 	
			}
			// Comprobamos que los adjuntos de la base de datos estan en adjuntos_tabla. Si alguno no esta, ponemos su activo a cero.
			for ($i=0;$i<count($nombres_adj_bbdd);$i++) {
				$encontrado = false;
				$j=0;
				while (($j<count($archivos_tabla_adjuntos)) and (!$encontrado)) {
					$encontrado = $archivos_tabla_adjuntos[$j] == $nombres_adj_bbdd[$i];
					$j++;
				}
				// Si no esta en la tabla es que se ha eliminado y por tanto procedemos a poner su activo a 0;
				if (!$encontrado) {
					$orden_compra->quitarArchivoAdjuntos($nombres_adj_bbdd[$i],$id_compra);
					if ($resultado != 1) {
						$mensaje_error = $orden_compra->getErrorMessage($resultado);
					}	
				}
			}
			
			if (isset ($_FILES["archivos"])) {
				// Comprobamos que se han adjuntado nuevas facturas
				// Si hay facturas, las subimos.
				$i=0;
				$error_archivo = 0;
				$subido = true;
				while (($i<count($_FILES["archivos"]["name"])) and ($subido)) {
					$uploaddir = "facturas/"; 
					if (!empty($_FILES["archivos"]["name"][$i])) {
						// Quitamos los acentos y carateres especiales. Le añadimos un random al archivo y lo guardamos en el array
						$nombre_archivo[$i] = rand(0,10000).rand(0,10000).'_'.$orden_compra->limpiar(basename($_FILES['archivos']['name'][$i]));
						$neto_factura[$i] = $importe[$i];
						$uploadfile = $uploaddir . $nombre_archivo[$i]; 
						$error_archivo = $_FILES['archivos']['error'][$i]; 
						$subido = false;
						if($error_archivo == UPLOAD_ERR_OK) {
							$subido = copy($orden_compra->limpiar($_FILES['archivos']['tmp_name'][$i]), $uploadfile);
						}
					}
					$i++;
				}
			}
			if (!$subido) {
				$resultado = 12;
				$mensaje_error = $orden_compra->getErrorMessage($resultado); 
			}
			else {
				// Comprobamos que se han añadido archivos adjuntos
				// Si hay archivos los subimos 
				if (isset ($_FILES["archivos_adjuntos"])) {
					$i=0;
					$error_archivo = 0;
					$subido = true;
					while (($i<count($_FILES["archivos_adjuntos"]["name"])) and ($subido)) {
						$uploaddir = "archivos_adjuntos/"; 
						if (!empty($_FILES["archivos_adjuntos"]["name"][$i])) {
							// Quitamos los acentos y carateres especiales. Le añadimos un random al archivo y lo guardamos en el array
							$nombre_archivo_adjunto[$i] = rand(0,10000).rand(0,10000).'_'.$orden_compra->limpiar(basename($_FILES['archivos_adjuntos']['name'][$i]));
							$uploadfile = $uploaddir . $nombre_archivo_adjunto[$i]; 
							$error_archivo = $_FILES['archivos_adjuntos']['error'][$i]; 
							$subido = false;
							if($error_archivo == UPLOAD_ERR_OK) {
								$subido = copy($orden_compra->limpiar($_FILES['archivos_adjuntos']['tmp_name'][$i]), $uploadfile);
							}
						}
						$i++;
					}	
				}
				if (!$subido) {
					$resultado = 17;
					$mensaje_error = $orden_compra->getErrorMessage($resultado); 
				}
				else {
					// Convierte la fecha a formato MySQL para guardarla en la BBDD
					$fecha_pedido = $funciones->cFechaMy($fecha_pedido);
																			 
					$orden_compra->datosNuevaCompra($id_compra,$id_produccion,$proveedor,$numero_pedido,$fecha_pedido,$fecha_requerida,$direccion_entrega,$direccion_facturacion,$fecha_factura,$comentarios,$estado_anterior,$estado,$precio_tasas,$unidades,$fecha_entrega,$id_compra);
					$orden_compra->setFacturas($nombre_archivo,$neto_factura);
					$orden_compra->setArchivosAdjuntos($nombre_archivo_adjunto);
					$resultado = $orden_compra->guardarCambios();
					$resultado = 1;
					if ($resultado == 1) {
						// Obtenemos la sede 
						$orden_compra->cargaDatosOrdenCompraId($id_compra);
						$op->cargaDatosProduccionId($orden_compra->id_produccion);
						$id_sede = $op->id_sede;
						header("Location: ordenes_compra.php?OCompra=modificado&sedes=".$id_sede);
					}
					else {
						$mensaje_error = $orden_compra->getErrorMessage($resultado);	
					}
				}
			}
		}
	}
}

//$orden_compra = new Orden_Compra(); 
//$prov = new Proveedor();
$id_compra = $_GET["id_compra"];
// Se cargan los datos de la orden de compra y los datos del proveedor asociado a esa orden de compra 
$orden_compra->cargaDatosOrdenCompraId($id_compra);
$proveedor = $orden_compra->id_proveedor;
$fecha_creado = $orden_compra->fecha_creado;
$fecha_pedido = $orden_compra->fecha_pedido;
$fecha_entrega = $orden_compra->fecha_entrega;
$direccion_entrega = $orden_compra->direccion_entrega;
$direccion_facturacion = $orden_compra->direccion_facturacion;
$orden_produccion = $orden_compra->id_produccion;
$fecha_requerida = $orden_compra->fecha_requerida;
$estado = $orden_compra->estado;
$estado_anterior = $estado;
$nombre_prov = $orden_compra->nombre_prov;
$orden_compra->damePrecioOC($id_compra,$proveedor);
$precio = $orden_compra->precio[0]["precio"];
$IVA = 21/100;
//$precio_tasas = $precio + $precio*$IVA; 
$precio_tasas = $orden_compra->tasas;
$precio_total = $precio + $precio_tasas;

$prov->cargaDatosProveedorId($proveedor);
$direccion_prov = $prov->direccion;
$telefono_prov = $prov->telefono;
$email_prov = $prov->email;
$ciudad_prov = $prov->ciudad;
$pais_prov = $prov->pais;
$forma_pago_prov = $prov->forma_pago;
$tiempo_suministro_prov = $prov->tiempo_suministro;
$metodo_pago_prov = $prov->metodo_pago;
$cp_prov = $prov->codigo_postal;
$provincia_prov = $prov->provincia;
$contacto_prov = $prov->persona_contacto;

$fecha_creado = $funciones->cFechaNormal($fecha_creado);
$fecha_pedido = $funciones->cFechaNormal($fecha_pedido);

$op->cargaDatosProduccionId($orden_produccion);
// Si tiene alias mostramos el alias. Si no mostramos la OP
if (($op->alias_op != NULL) && ($op->alias_op != $op->codigo)){
	$nombre_OP = $op->alias_op;
}
else{
	$nombre_OP = $op->codigo; 
}


// Si la fecha_entrega no esta guardada en la BBDD la calculamos al vuelo
if ($fecha_entrega == NULL){
	$dias = 0;
	if ($tiempo_suministro_prov == 0) $dias = $dias + 0;
	else if ($tiempo_suministro_prov == 1) $dias = $dias + 7;
	else if ($tiempo_suministro_prov == 2) $dias = $dias + 14;
	else if ($tiempo_suministro_prov == 3) $dias = $dias + 30;
	else if ($tiempo_suministro_prov == 4) $dias = $dias + 60;
	else $dias = $dias + 90;
	
	$fecha_pedido = $funciones->cFechaMyEsp($fecha_pedido);
	$fecha_entrega = date("m/d/Y", strtotime($fecha_pedido." +".$dias." days"));
}
else {
	$fecha_pedido = $funciones->cFechaMyEsp($fecha_pedido);
	$fecha_entrega = $funciones->cFechaNormal($fecha_entrega);
	$fecha_entrega = $funciones->cFechaMyEsp($fecha_entrega);
}

$dias = 0;
if ($metodo_pago_prov == 0 ) $dias = $dias + 0;
else if ($metodo_pago_prov == 1) $dias = $dias + 0;
else if ($metodo_pago_prov == 2) $dias = $dias + 30;
else if ($metodo_pago_prov == 3) $dias = $dias + 60;
else $dias = $dias + 90;

$fecha_pago = date("m/d/Y", strtotime($fecha_pedido." +".$dias." days"));
$fecha_pedido = $funciones->cFechaMyEsp($fecha_pedido);	
$fecha_entrega = $funciones->cFechaMyEsp($fecha_entrega);
$fecha_pago = $funciones->cFechaMyEsp($fecha_pago);
$titulo_pagina = "Órdenes de Compra > Modifica Orden de Compra";
$pagina = "mod_orden_compra";
include ('../includes/header.php');
echo '<script type="text/javascript" src="../js/orden_compra/mod_orden_compra.js"></script>';
?>

<div class="separador"></div> 
<?php include("../includes/menu_oc.php");?> 

<div id="ContenedorCentral">
	<div id="ContenedorSidebar"><?php include ("../includes/sidebar.php");?></div>

    <h3> Modificación Orden de Compra </h3>
    <form id="FormularioModOC" name="modificarOrdenCompra" action="mod_orden_compra.php?id_compra=<?php echo $orden_compra->id_compra; ?>" method="post" enctype="multipart/form-data">
    	<br />
        <table>
        	<tr>
            	<th colspan="4"><h1>Modifique los datos en el siguiente formulario</h1></th>
            </tr>
            <tr>
            	<td colspan="2"><h2>Datos de la orden de compra</h2></td>
                <td colspan="2"></td>
            </tr>
            <tr>
            	<td><div class="LabelModOC">Orden de Compra *</div></td>
                <td><input type="text" id="BolsaGastos" name="BolsaGastos" readonly="readonly" class="DatosModOC" value="<?php echo $orden_compra->numero_pedido;//$orden_compra->orden_compra;?>"/></td>
                <td><div class="LabelModOC">Forma de pago *</div></td>
                <td>    
                    <input type="text" id="forma_pago_prov" name="forma_pago_prov" readonly="readonly" class="DatosModOC" value="<?php if($forma_pago_prov == 1) { echo "Transferencia bancaria";}
					else if ($forma_pago_prov == 2) { echo "Tarjeta de crédito/débito"; } 
					else if ($forma_pago_prov == 3) { echo "PayPal"; } 
					else if ($forma_pago_prov == 4) { echo "Recibo domiciliado";}?>"/>
                </td>
            </tr>
            <tr>
            	<td><div class="LabelModOC">O. Produccion *</div></td>
                <td><input type="text" id="orden_produccion" name="orden_produccion" class="DatosModOC" readonly="readonly" value="<?php echo $nombre_OP;?>" /></td>
                <td><div class="LabelModOC">Método de pago *</div></td>
                <td>
                	<input type="text" id="metodo_pago_prov" name="metodo_pago_prov" readonly="readonly" class="DatosModOC" value="<?php if($metodo_pago_prov == 1) { echo "Pago previo";}
					else if ($metodo_pago_prov == 2) { echo "30 dias"; } 
					else if ($metodo_pago_prov == 3) { echo "60 dias"; } 
					else if ($metodo_pago_prov == 4) { echo "90 dias";}?>"/>
                </td>
            </tr>
            <tr>
            	<td><div class="LabelModOC">Proveedor *</div></td>
                <td>
                    <input type="text" id="nombre_proveedor" name="nombre_proveedor" class="DatosModOC" readonly="readonly" value="<?php echo $orden_compra->nombre_prov;?>" />
		            <input type="hidden" id="proveedor" name="proveedor" value="<?php echo $proveedor;?>" />    
                </td>
                <td><div class="LabelModOC">Tiempo Suministro *</div></td>
                <td>
                	<input type="text" id="tiempo_suministro_prov" name="tiempo_suministro_prov" readonly="readonly" class="DatosModOC" value="<?php if($tiempo_suministro_prov == 1) { echo "7 dias";}
					else if ($tiempo_suministro_prov == 2) { echo "14 dias"; } 
					else if ($tiempo_suministro_prov == 3) { echo "30 dias"; } 
					else if ($tiempo_suministro_prov == 4) { echo "60 dias"; }
					else if ($tiempo_suministro_prov == 5) { echo "90 dias"; }?>"/>
                </td>
            </tr>
            <tr>
            	<td><div class="LabelModOC">Dirección *</div></td>
                <td><input type="text" id="direccion_prov" name="direccion_prov" class="DatosModOC" readonly="readonly" value="<?php echo $direccion_prov;?>" /></td>
                <td><div class="LabelModOC">Dirección Entrega *</div></td>
                <td>    
	   				<select id="direccion_entrega" name="direccion_entrega"  class="DatosModOC">
            		<?php 
						$bbdd = new MySQL;
						$nd = new listadoDirecciones();
						$nd->prepararConsultaDireccionesEntrega();
						$nd->realizarConsultaDireccionesEntrega();
						$resultado_direcciones_entrega = $nd->direcciones;

						for($i=0;$i<count($resultado_direcciones_entrega);$i++) {
							$dir_entrega = new Direccion();
							$datoDirecciones = $resultado_direcciones_entrega[$i];
							$dir_entrega->cargaDatosDireccionId($datoDirecciones["id_direccion"]);
							echo '<option value="'.$dir_entrega->id_direccion.'" '; if ($dir_entrega->id_direccion == $direccion_entrega) echo 'selected="selected"'; echo '>'.utf8_encode ($dir_entrega->direccion).' - '.$dir_entrega->nombre_empresa.'</option>';
                    	}
					?>
            		</select>
                </td>
            </tr>
            <tr>
            	<td><div class="LabelModOC">Telefono *</div></td>
                <td><input type="text" id="telefono_prov" name="telefono_prov" class="DatosModOC" readonly="readonly" value="<?php echo $telefono_prov;?>" /></td>
            	<td><div class="LabelModOC">Dirección Facturación *</div></td>
                <td>
	   				<select id="direccion_facturacion" name="direccion_facturacion"  class="DatosModOC">
     				<?php 
						$ndf = new listadoDirecciones();
						$ndf->prepararConsultaDireccionesFacturacion();
						$ndf->realizarConsultaDireccionesfacturacion();
						$resultado_direcciones_facturacion = $ndf->direcciones;

						for($i=0;$i<count($resultado_direcciones_facturacion);$i++) {
							$dir_facturacion = new Direccion();
							$datoDireccionesF = $resultado_direcciones_facturacion[$i];
							$dir_facturacion->cargaDatosDireccionId($datoDireccionesF["id_direccion"]);
							echo '<option value="'.$dir_facturacion->id_direccion.'" '; if ($dir_facturacion->id_direccion == $direccion_facturacion) echo 'selected="selected"'; echo '>'.utf8_encode($dir_facturacion->direccion).' - '.$dir_facturacion->nombre_empresa.'</option>';
                    	}
					?>
            	</select>
               </td>
            </tr>
            <tr>
            	<td><div class="LabelModOC">Email *</div></td>
                <td><input type="text" id="email_prov" name="email_prov" class="DatosModOC" readonly="readonly" value="<?php echo $email_prov;?>" /></td>
                <td><div class="LabelModOC">CP *</div></td>
                <td><input type="text" id="cp_prov" name="cp_prov" class="DatosModOC" readonly="readonly" value="<?php echo $cp_prov;?>" /></td>
            </tr>
            <tr>
            	<td><div class="LabelModOC">Ciudad *</div></td>
                <td><input type="text" id="ciudad_prov" name="ciudad_prov" class="DatosModOC" readonly="readonly" value="<?php echo $ciudad_prov;?>" /></td>
                <td><div class="LabelModOC">Provincia </div></td>
                <td><input type="text" id="provincia_prov" name="provincia_prov" class="DatosModOC" readonly="readonly" value="<?php echo $provincia_prov;?>" /></td>
            </tr>
            <tr>
            	<td><div class="LabelModOC">Pais *</div></td>
                <td><input type="text" id="pais_prov" name="pais_prov" class="DatosModOC" readonly="readonly" value="<?php echo $pais_prov;?>" /></td>
                <td><div class="LabelModOC">Contacto</div></td>
                <td><input type="text" id="contacto_prov" name="contacto_prov" class="DatosModOC" readonly="readonly" value="<?php echo $contacto_prov;?>" /></td>
            </tr>
            <tr style="height: 20px;"></tr>
            <tr>
            	<td colspan="2"></td>
                <td>
                   	<div class="LabelModOCProv">
                   	<?php 
                   		if($modificar_basico){ ?>
	                		<a href="../basicos/mod_proveedor.php?id=<?php echo $proveedor;?>"> Modifique opciones del proveedor</a>
	                <?php 
	                	}
	                ?>
                	</div>
                </td>
                <td></td>
            </tr>
            <tr style="height: 20px;"></tr>
            <tr>
            	<td colspan="2"><h2>Precios y estado de la Orden de Compra</h2></td>
                <td colspan="2"><h2>Modificación de las fechas</h2></td>
            </tr>
            <tr>
            	<td><div class="LabelModOC">Precio *</div></td>
                <td><input type="text" id="precio" name="precio" class="DatosModOC" readonly="readonly" value="<?php echo number_format($precio, 2, '.', '');?>" /></td>
                <td><div class="LabelModOC">Fecha de creación</div></td>
                <td><input type="text" name="fecha_creacion" id="fecha_creacion" class="fechas_OC" readonly="readonly" value="<?php echo $fecha_creado;?>" /></td>
            </tr>
            <tr>
            	<td><div class="LabelModOC">Tasas *</div></td>
                <td><input type="text" id="precio_tasas" name="precio_tasas" class="DatosModOC" value="<?php echo number_format($precio_tasas, 2, '.', '');?>" onblur="javascript:validarHayCaracter();" /></td>
                <td><div class="LabelModOC">Fecha de pedido</div></td>
                <td><input type="text" name="fecha_pedido" id="datepicker_mod_oc_fpedido" class="fechaCal" value="<?php echo $fecha_pedido;?>" /></td>
            </tr>
            <tr>
            	<td><div class="LabelModOC">Precio + Tasas</div></td>
                <td><label id="precio_total" class="LabelPrecio"><?php echo number_format($precio_total, 2, ',', '.').'€';?></label></td>
                <td><div class="LabelModOC">Fecha entrega</div></td>
                <td><input type="text" name="fecha_entrega" id="datepicker_mod_oc_fentrega" class="fechaCal" readonly="readonly" value="<?php echo $fecha_entrega;?>" /></td>
            </tr>
            <tr>
            	<td><div class="LabelModOC">Estado *</div></td>
                <td>
            		<?php 
					$estados = array ("GENERADA","PEDIDO INICIADO","PEDIDO CERRADO", "PARCIALMENTE RECIBIDO", "RECIBIDO", "STOCK");
					$num_estados = count($estados);
					if($estado == "PARCIALMENTE RECIBIDO" or $estado == "RECIBIDO") {
						?>
						<select id="estadoDisabled" name="estadoDisabled" class="DatosModOC" disabled="disabled">
							<option value="<?php echo $estado; ?>"><?php echo $estado; ?></option>
						</select>
						<input type="hidden" id="estado" name="estado" value="<?php echo $estado; ?>" />
						<?php
					} else {
						if($modificar_oc) {	?>
							<select id="estado" name="estado" class="DatosModOC">
								<option value="GENERADA"<?php if($estado == "GENERADA") { echo ' selected="selected"'; } ?>>GENERADA</option>
								<option value="PEDIDO INICIADO"<?php if($estado == "PEDIDO INICIADO") { echo ' selected="selected"'; } ?>>PEDIDO INICIADO</option>
								<option value="PEDIDO CERRADO"<?php if($estado == "PEDIDO CERRADO") { echo ' selected="selected"'; } ?>>PEDIDO CERRADO</option>
								<option value="STOCK"<?php if($estado == "STOCK") { echo ' selected="selected"'; } ?>>STOCK</option>
							</select>
						<?php
						}
						else { ?>
							<input id="estado" name="estado" class="DatosModOC" readonly="readonly" value="<?php echo $estado;?>"/>
					<?php	
						}
					}
					?>
                    <input type="hidden" id="estado_anterior" name="estado_anterior" value="<?php echo $estado_anterior;?>" />
                </td>
                <td><div class="LabelModOC">Fecha de pago</div></td>
                <td><input type="text" name="fecha_pago" id="fecha_pago" class="fechas_OC" value="<?php echo $fecha_pago;?>" /></td>
            </tr>
            
            <tr>
            	<td colspan="4"><h2>Referencias de la Orden de Compra</h2></td>
            </tr>
            <tr>
            	<td><div class="LabelModOC">Referencias</div></td>
                <td colspan="3" id="TablaRefModOC">
                	<div class="CajaReferenciasOC">
                	<table>
                    <tr>
                    	<th style="text-align:center">ID</th>
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
						$oc = new Orden_Compra();
						$ref = new Referencia();
						// Obtenemos las referencias de la Orden de Compra
						// Obtenemos las unidades paquete y las piezas de las referencias
						$oc->dameDatosOrdenCompraReferencias($id_compra);
						for($i=0;$i<count($oc->referencias);$i++){
							$ref->cargaDatosReferenciaId($oc->referencias[$i]["id_referencia"]);
							if($oc->referencias[$i]["pack_precio"] <> 0 and $oc->referencias[$i]["uds_paquete"] <> 0) {
							$precio_unidad = $oc->referencias[$i]["pack_precio"] / $oc->referencias[$i]["uds_paquete"];
						} 
						else {
							$precio_unidad = 00;
						}
						$precio_referencia = $oc->referencias[$i]["total_piezas"] * $precio_unidad;
					?>
                    <tr>
                    	<td style="text-align:center"><?php echo $ref->id_referencia;?></td>
                    	<td><?php echo $ref->referencia;?></td>
                        <td><?php echo $ref->nombre_proveedor;?></td>
                        <td><?php $ref->vincularReferenciaProveedor();?></td>
        				<td><?php echo $ref->part_nombre;?></td>
                        <td style="text-align:center"><?php echo $oc->referencias[$i]["total_piezas"];?></td>
                        <td style="text-align:center"><?php echo $oc->referencias[$i]["pack_precio"];?></td>
        				<td style="text-align:center"><?php echo $oc->referencias[$i]["uds_paquete"];?></td>
                        <td style="text-align:center"><?php echo $oc->referencias[$i]["total_packs"];?></td>
                        <td style="text-align:center"><?php echo $precio_unidad;?></td>
                        <td style="text-align:center"><?php echo $oc->referencias[$i]["coste"]."€";?></td>
                    </tr>
                    <?php
						}
					?>
                    </table>
                    </div> 
	           </td>
            </tr>
            <tr>
            	<td colspan="4"><h2>Módulo de facturas</h2></td>    	            
            </tr>
            <tr>
            <?php 
            	if($modificar_oc){ ?>
	            	<td><div class="LabelModOC">FACTURAS</div></td>
	                <td colspan="3"><div id="AñadirMasFacturas"><a href="#" onClick="addCampo()" class="SubirArchivo">Subir otro archivo</a></div></td>
		            </tr>
		            <tr>
		            	<td></td>
		                <td colspan="3">
		                	<div id="adjuntos"> 
		        				<div class="ContenedorCamposAdjuntarFacturas">
		        					<input type="file" id="archivos[]" name="archivos[]" class="BotonAdjuntar"/> 
		               				<div class="LabelImporte">IMPORTE</div> 
		                			<input type="text" id="importe[]" name="importe[]" class="CreacionBasicoInput" /> 
		        				</div>
		        			</div>
		                </td>
		            </tr>
            <?php 
	        	}
	        ?>
            <tr>
            	<td><div class="LabelModOC">Mostrar Facturas</div></td>
                <td colspan="2" id="TablaRefModOC">
                	<div class="CajaFacturas">
            			<div id="CapaTablaIframe">
                    		<table id="mitabla">
        					<tr>
                        		<th>Nº FACTURA</th>
        						<th style="text-align:center">IMPORTE</th>
        						<th style="text-align:center">DESCARGAR</th>
        						<th style="text-align:center">ELIMINAR</th>
                       		</tr>   
                    <?php 
						$facturas = new Orden_Compra();
						$facturas->dameIds_factura($orden_compra->id_compra,$orden_compra->id_proveedor);
						$ids_facturas = $facturas->ids_facturas;
														
						for($i=0;$i<count($ids_facturas);$i++) {
							$array_ids_facturas[] = $ids_facturas[$i]["id_factura"];
							$facturas->cargaDatosFacturasId($array_ids_facturas[$i]);
					?>
					<tr><td><?php echo $facturas->nombre_factura;?><input type="hidden" name="archivos_tabla[]" id="archivos_tabla[]" value="<?php echo $facturas->nombre_factura;?>" /></td><td style="text-align:center"><?php echo $facturas->neto;?></td><td style="text-align:center"><input type="button" id="descargar" name="descargar" class="BotonEliminar"  value="DESCARGAR" onclick="window.location.href='download.php?id=<?php echo $facturas->nombre_factura;?>'"/></td><td style="text-align:center"><input type="checkbox" name="chkbox" value="<?php echo $facturas->id_factura;?>" /></td></tr>
                    <?php
						}
					?>
                    </table>
                    </div></div>
                </td> 
                <td>
            <?php 
            	if($modificar_oc){ ?>
            		<input type="button" id="quitar" name="quitar" class="BotonQuitarFacturas" value="QUITAR" onclick="javascript:removeRow(mitabla)"  />
            <?php 
            	}
            ?>
                </td>
            </tr>
            <tr>
            	<td colspan="4" style="padding:40px 0px 0px 0px;">            
                	<div class="LabelModOC">FRA REQUEST *</div>
            		<?php $fecha = date("d.m.Y"); ?>
          			<input type="button" id="fra_request" name="fra_request" class="BotonFacturas" value="Imprimir" style="margin:0px 0px 0px 85px;" onclick="javascript:abrirFactura(<?php echo $id_compra;?>,'<?php echo 'OP'.$orden_produccion.$nombre_prov;?>','<?php echo $fecha;?>','<?php echo $dir_entrega->telefono;?>','<?php echo $dir_facturacion->telefono;?>','<?php echo $dir_entrega->cif;?>','<?php echo $dir_facturacion->cif;?>','<?php echo $dir_entrega->localidad;?>','<?php echo $dir_facturacion->localidad;?>','<?php echo $dir_entrega->codigo_postal;?>','<?php echo $dir_facturacion->codigo_postal;?>','<?php echo $dir_entrega->provincia;?>','<?php echo $dir_facturacion->provincia;?>','<?php echo $precio_total;?>');" />
        			<input type="checkbox" id="con_precios" name="con_precios" value="1" style="margin:5px 0px 0px 25px;" /> 
                    <div class="label_check_precios">Con precios</div>
                </td>
            </tr>   
            <tr>
            	<td colspan="4"></td>
            </tr>  
            
            <tr>
            	<td colspan="4"><h2>Módulo de archivos adjuntos</h2></td>    	            
            </tr>   
        <?php 
        	if($modificar_oc){ ?> 
	            <tr>
	            	<td><div class="LabelModOC">ADJUNTOS</div></td>
	                <td colspan="3"><div id="AñadirMasAdjuntos"><a href="#" onClick="addCampoAdjunto()" class="SubirArchivo">Subir otro archivo</a></div></td>
	            </tr>
            
	            <tr>
	            	<td></td>
	                <td colspan="3">
	                	<div id="ArchivosAdjuntos"> 
	        				<div class="ContenedorCamposArchivosAdjuntos">
	        					<input type="file" id="archivos_adjuntos[]" name="archivos_adjuntos[]" class="BotonAdjuntarAdjuntos"/> 
	               			</div>
	        			</div>
	                </td>
	            </tr>
	    <?php 
	    	}
	    ?>
            <tr>
            	<td><div class="LabelModOC">Mostrar Adjuntos</div></td>
                <td colspan="2" id="TablaRefModOC">
                	<div class="CajaFacturas">
            			<div id="CapaTablaIframe">
                    		<table id="mitablaAdjuntos">
        					<tr>
                        		<th>NOMBRE ARCHIVO</th>
        						<th style="text-align:center">DESCARGAR</th>
        						<th style="text-align:center">ELIMINAR</th>
                       		</tr>   
                    <?php 
						$archivos_adjuntos = new Orden_Compra();
						$archivos_adjuntos->dameIdsArchivosAdjuntos($orden_compra->id_compra,$orden_compra->id_proveedor);				
						$ids_adjuntos = $archivos_adjuntos->ids_adjuntos;

						for($i=0;$i<count($ids_adjuntos);$i++) {
							$array_ids_adjuntos[] = $ids_adjuntos[$i]["id_adjuntos"];
							$archivos_adjuntos->cargaDatosAdjuntosId($array_ids_adjuntos[$i]);
					?>
					<tr><td><?php echo $archivos_adjuntos->nombre_adjunto;?><input type="hidden" name="archivos_tabla_adjuntos[]" id="archivos_tabla_adjuntos[]" value="<?php echo $archivos_adjuntos->nombre_adjunto;?>" /></td><td style="text-align:center"><input type="button" id="descargar" name="descargar" class="BotonEliminar"  value="DESCARGAR" onclick="window.location.href='download_adjuntos.php?id=<?php echo $archivos_adjuntos->nombre_adjunto;?>'"/></td><td style="text-align:center"><input type="checkbox" name="chkbox" value="<?php echo $archivos_adjuntos->id_adjuntos;?>" /></td></tr>
                    <?php
						}
					?>
                    </table>
                    </div></div>
                </td> 
		<?php 
        	if($modificar_oc){ ?>                 
        		<td><input type="button" id="quitar" name="quitar" class="BotonQuitarFacturas" value="QUITAR" onclick="javascript:removeRowAdjuntos(mitablaAdjuntos)"  /></td>
        <?php
        	}
        ?>
            </tr>
             
        	<tr>
            	<td colspan="4" style="padding:40px 0px 0px 0px; float:left;">
            		<input type="button" id="volver" name="volver" value="Volver" onclick="javascript:window.history.back()" /> 
            		<input type="hidden" id="guardandoOrdenCompra" name="guardandoOrdenCompra" value="1" />
				<?php 
        			if($modificar_oc){ ?> 
            			<input type="submit" id="guardar" name="guardar" value="Continuar" />    
            	<?php 
            		}
            	?>
                </td>
            </tr>
     	</table>
        <?php 
			if($mensaje_error != "") {
				echo '<div class="mensaje_error">'.$mensaje_error.'</div>'; 
			}
		?>
        <br />
        <input type="hidden" id="id_compra" name="id_compra" value="<?php echo $orden_compra->id_compra;?>"  /><strong></strong>
    </form>
</div>
<?php include ('../includes/footer.php');?>