<?php 
set_time_limit(10000);
// Envío de emails de pedidos al proveedor
session_start();
include("../includes/sesion.php");
include("../classes/basicos/proveedor.class.php");
include("../classes/basicos/direccion.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/orden_compra/orden_compra.class.php");
include("../classes/orden_compra/listado_referencias_oc.class.php");
require_once("dompdf/dompdf_config.inc.php");

function validar_email($email) {
	// Primero revisamos que exista el símbolo @, 
	// y que la longitud sea correcta.
	if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
		// Inválido por un incorrecto número de caracteres
		// en la misma sección o falta el #.
		return false;
	}
	
	// Lo separamos en secciones para hacerlo más facil
	$email_array = explode("@", $email);
	$local_array = explode(".", $email_array[0]);
	for ($i = 0; $i < sizeof($local_array); $i++) {
		if(!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&
		↪'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$",
			$local_array[$i])) {
		  return false;
		}
	}
	
	// Revisar si el domino es o no una IP, 
	// Si es debe de ser un dominio válido
	if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) {
		$domain_array = explode(".", $email_array[1]);
		if (sizeof($domain_array) < 2) {
		    return false; // No tiene suficientes partes el dominio
		}
		for ($i = 0; $i < sizeof($domain_array); $i++) {
			if(!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|
				↪([A-Za-z0-9]+))$",$domain_array[$i])) {
			    return false;
			}
		}
	}
	return true;
}

// Obtenemos los ids por url. Si hay varios, los extraemos uno a uno. 
$ids_compra = $_GET["ids_compra"];

$salida = "Se inicia el proceso de envio de emails";

$oc = new Orden_Compra();
$proveedores = $oc->getProveedoresOrdenCompra($ids_compra);
$numero_proveedores = count($proveedores);
require "../includes/class.phpmailer.php";

// Se consultan las opciones del pedido
$consultaAsunto = "select valor from orden_compra_opciones where clave='asunto_email'";
$consultaTexto = "select valor from orden_compra_opciones where clave='texto_email'";
$oc->setConsulta($consultaAsunto);
$oc->ejecutarConsulta();
$datoAsunto = $oc->getPrimerResultado();
$asunto_email = $datoAsunto["valor"];
$oc->setConsulta($consultaTexto);
$oc->ejecutarConsulta();
$datoTexto = $oc->getPrimerResultado();
$texto_email = $datoTexto["valor"];
$mail = new phpmailer();
$mail->PluginDir = "../includes/";
for($p=0;$p<$numero_proveedores;$p++){
	$id_proveedor = $proveedores[$p]["id_proveedor"];
	// Se consulta si el proveedor tiene email
	$proveedor = new Proveedor();
	$proveedor->cargaDatosProveedorId($id_proveedor);
	$email_proveedor = $proveedor->email;
	if(validar_email($email_proveedor)) {
		$salida .= "<br />Se prepara el envío del PDF para el proveedor ".$proveedor->nombre;
		$ordenes_compra = $oc->getOrdenesCompraProveedor($proveedor->id_proveedor,$ids_compra);
		$numero_ordenes = count($ordenes_compra);
		$pdf_generado = false;
		$adjuntos = array();
		for($o=0;$o<$numero_ordenes;$o++){
			$codigo = "";
			$id_compra = $ordenes_compra[$o]["id_orden_compra"];
			$mostrar_precios = 0;

			$orden_compra = new Orden_Compra();
			$orden_compra->cargaDatosOrdenCompraId($id_compra);

			$salida .= "<br />Generando PDF del pedido ".$orden_compra->numero_pedido."... ";

			$proveedor = $orden_compra->id_proveedor;
			$id_produccion = $orden_compra->id_produccion;
			$fecha_pedido = $orden_compra->fecha_pedido;
			$orden_produccion = $orden_compra->id_produccion;
			$fecha_requerida = $orden_compra->fecha_requerida;
			$estado = $orden_compra->estado;
			$nombre_prov = $orden_compra->nombre_prov;
			$numero_pedido = $orden_compra->numero_pedido;
			$orden_compra->damePrecioOC($id_compra,$proveedor);
			$total = $orden_compra->precio[0]["precio"];
			$precio_total = $_GET["precio_total"];

			// Se obtienen los id de las direcciones
			$id_direccion_entrega = $orden_compra->direccion_entrega;
			$id_direccion_facturacion = $orden_compra->direccion_facturacion;
			// Si son NULL, se ponen las direcciones por defecto
			if($id_direccion_entrega == NULL) $id_direccion_entrega = 2;
			if($id_direccion_facturacion == NULL) $id_direccion_facturacion = 1;

			// Dirección de entrega
			$dir_entrega = new Direccion();
			$dir_entrega->cargaDatosDireccionId($id_direccion_entrega);
			// Dirección de facturación
			$dir_facturacion = new Direccion();
			$dir_facturacion->cargaDatosDireccionId($id_direccion_facturacion);

			$orden_produccion = new Orden_Produccion();
			$orden_produccion->cargaDatosProduccionId($id_produccion);
			$num_unidades = $orden_produccion->unidades;

			$listado_referencias_FR = new listadoReferenciasOC();
			$listado_referencias_FR->setValoresFRA_REQ($id_compra,$proveedor);
			$listado_referencias_FR->realizarConsultaFRA_REQ();
			$resultados_referencias_fr = $listado_referencias_FR->referencias_OC_FR;
			$num_referencias_fr = count($resultados_referencias_fr);

			// Guardar en la base de datos las direcciones seleccionadas 
			$resultado = $orden_compra->guardaDirecciones($id_compra,$id_direccion_entrega,$id_direccion_facturacion);
			if ($resultado == 1) {
				if($mostrar_precios == 1) {
					$tabla_coste = '<th>PACK PRECIO</th>
			        	  <th>PRECIO TOTAL</th>';
				}
				$codigo='<html><head>
				<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
				</head>
				<body style="text-align:center">
				<div id="factura">
					<table>
					<tr>
						<td width="235">
							<img src="../images/simumak-logo1.jpg" style="text-align:center; padding: 20px 0 0 40px;">
							<br />
							<div class="direccionSimumak">
								
							</div>
						</td>    
						<td width="175">
							<br /><br />
							<div class="tituloDatos">
								Direcci&oacute;n de Facturaci&oacute;n
							</div>
							<div class="datosFactura">
								'.$dir_facturacion->direccion.'<br />
								'.$dir_facturacion->cif.'<br />
								'.$dir_facturacion->codigo_postal.' - '.$dir_facturacion->localidad.' - ('.$dir_facturacion->provincia.')<br>
								'.$dir_facturacion->telefono.'
							</div>
						</td>
						<td width="175">
							<br /><br />
							<div class="tituloDatos">
								Direcci&oacute;n de Env&iacute;o
							</div>
							<div class="datosFactura">
								'.$dir_entrega->direccion.'<br />
								'.$dir_entrega->cif.'<br />
								'.$dir_entrega->codigo_postal.' - '.$dir_entrega->localidad.' - '.$dir_entrega->provincia.'<br />
								Persona Cto: '.$dir_entrega->persona_contacto.'<br />
								Tlf Cto: '.$dir_entrega->telefono.'<br />
							</div>
						</td>
			    	</tr>
			  	</table>
				<br />
				<div style="padding:0px 0px 0px 42px; text-align:left; font: 12px Helvetica, Verdana, Arial; font-weight: bold; text-transform: uppercase;">
					PROVEEDOR:&nbsp; <span style="font: 10px Helvetica, Verdana, Arial;">'.$nombre_prov.'</span>
				</div>
				<br />
				<div>
					<div class="tituloDatosProveedor">NUM. REF. PEDIDO:&nbsp;<span class="numeroReferenciaPedido">'.$numero_pedido.'</div>
				</div>
				<br />
			    <div id="CapaTablaDatos">
					<table>
			        <tr>
					  <th></th>
			          <th>REFERENCIA</th>
					  <th>NOMBRE</th>
					  <th>UNIDADES</th>
					  <th>UNIDADES PAQUETE</th>
			          <th>TOTAL PAQUETES</th>
					  '.$tabla_coste.'
			        </tr>';
				$num = 0;
				for ($i=0; $i<$num_referencias_fr; $i++) {
					++$num;
					if($mostrar_precios == 1) {
						$coste = '<td>'.$resultados_referencias_fr[$i]["pack_precio"].'</td>
									<td>'.$resultados_referencias_fr[$i]["coste"].'</td>';
					}
					$total_unidades = $resultados_referencias_fr[$i]["total_packs"];
					$unidades_paquete = $resultados_referencias_fr[$i]["uds_paquete"];
					$total_piezas = $resultados_referencias_fr[$i]["piezas"]*$num_unidades;
					$suma_paquetes = $total_piezas*$num_unidades/$unidades_paquete;
					$suma_paquetes = $suma_paquetes/$num_unidades;
					$exp = explode(".",$suma_paquetes);
					if($exp[0] == 0) {
						$totalPedir = 1;
					} else {
						if($exp[1] > 1) {
							$totalPedir = $exp[0]+1;
						} else {
							$totalPedir = $exp[0].$exp[1];
						}
					}
					$codigo .= '<tr>
			  						<td>'.$num.'</td>
						            <td>'.$resultados_referencias_fr[$i]["part_proveedor_referencia"].'</td>
									<td>'.$resultados_referencias_fr[$i]["referencia"].'</td>
									<td>'.$total_piezas.'</td>
									<td>'.$unidades_paquete.'</td>
			    					<td>'.$totalPedir.'</td>
			    					'.$coste.'
			    				</tr>';
				}
				$codigo .= '</table></div></div></body>';
				$dompdf=new DOMPDF();
				$dompdf->load_html($codigo);
				$dompdf->render();
				$output = $dompdf->output();
				$salida .= "<br />Completado";
	    		if(file_put_contents('pedidos/'.$numero_pedido.'.pdf', $output)) {
	    			$pdf_generado = true;
	    			$salida .= "<br/>PDF guardado en directorio";
	    			array_push($adjuntos,$numero_pedido);
	    		}
    		}	
		}
		if($pdf_generado) {
			$salida .= "<br />Se inicia el proceso del envío del email...";
			$mail->IsSMTP();
			$mail->SMTPDebug = 0; 
			$mail->SMTPAuth = true; 
			$mail->SMTPSecure = 'ssl';
			$mail->Host = 'smtp.gmail.com';
			$mail->Port = 465; 
			$mail->Username = "pedidos@simumak.com";
			$mail->Password = "Simumak2013";
			$mail->From = "pedidos@simumak.com";
			$mail->FromName = "Simumak";
			$mail->Subject = $asunto_email;
			$mail->Timeout=120;
			$mail->AddAddress($email_proveedor);
			$mail->Body = $texto_email;
			$total_adjuntos = count($adjuntos);
			for($a=0;$a<$total_adjuntos;$a++){
				$mail->AddAttachment("pedidos/".$adjuntos[$a].".pdf");
			}
			if($mail->Send()) {
				for($a=0;$a<$total_adjuntos;$a++){
					unlink("pedidos/".$adjuntos[$a].".pdf");
					$salida .= "<br />Email enviado";
					// Se pasa el pedido a PEDIDO INICIADO si está en GENERADA
					if($orden_compra->estado == "GENERADA") {
						$orden_compra->estado_anterior = "GENERADA";
						$orden_compra->estado = "PEDIDO INICIADO";
						$orden_compra->guardarCambios();
						$salida .= "<br />Cambiado el estado de la orden de compra a PEDIDO INICIADO";
					}
				}
			} else {
				$salida .= "<br />Error al enviar el email: ".$mail->ErrorInfo;
			}
			$mail->ClearAddresses();
			$mail->ClearAttachments();
			$salida .= "<br /><br />";
			$_SESSION["salida_email"] = $salida;
		}
	}
	header("Location: ordenes_compra.php?OCompra=pedidos_enviados");
}

?>