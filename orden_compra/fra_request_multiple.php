<?php 
// Este es el fichero que genera las facturas de forma multiple
require("dompdf/dompdf_config.inc.php");

$dir_entrega = new Direccion();
$dir_facturacion = new Direccion();
$listado_referencias_FR = new listadoReferenciasOC();
$orden_produccion = new Orden_Produccion();

// Cargamos los datos de la OC
$oc->cargaDatosOrdenCompraId($ids_compra[$k]);
$nombre_oc = utf8_decode($oc->orden_compra);
$proveedor = $oc->id_proveedor;
$numero_pedido = utf8_decode($oc->numero_pedido);
$id_dir_entrega = $oc->direccion_entrega;
$id_dir_facturacion = $oc->direccion_facturacion;
$id_produccion = $oc->id_produccion;

$oc->damePrecioOC($ids_compra[$k],$proveedor);
$total = $oc->precio[0]["precio"];
$tasas = $oc->tasas;
$precio_total = $total + $tasas;

// Si no se guardo las direcciones al generar la OC ponemos las predeterminadas
if($id_dir_facturacion == NULL) $id_dir_facturacion = 1;
if($id_dir_entrega == NULL) $id_dir_entrega = 2;

$datos_agencia_transporte = $oc->dameDatosAgenciaTransporte();
$agencia_transporte = utf8_decode($datos_agencia_transporte["nombre"]);
$cuenta_importacion = utf8_decode($datos_agencia_transporte["cuenta_importacion"]);

// Cargamos los datos de las direcciones de entrega y facturacion
$dir_entrega->cargaDatosDireccionId($id_dir_entrega);
$nombre_empresa_entrega = utf8_decode(strtoupper($dir_entrega->nombre_empresa));
$cif_empresa_entrega = utf8_decode(strtoupper($dir_entrega->cif));
$dir_empresa_entrega = utf8_decode(strtoupper($dir_entrega->direccion));
$localidad_empresa_entrega = utf8_decode(strtoupper($dir_entrega->localidad));
$provincia_empresa_entrega = utf8_decode(strtoupper($dir_entrega->provincia));
$cp_empresa_entrega = strtoupper($dir_entrega->codigo_postal);
$contacto_empresa_entrega = utf8_decode(strtoupper($dir_entrega->persona_contacto));
$telefono_empresa_entrega = $dir_entrega->telefono;

$datos_entrega = $nombre_empresa_entrega.'<br/>'.$cif_empresa_entrega.'<br/>'.$dir_empresa_entrega.'<br/>'.$localidad_empresa_entrega.' - '.$provincia_empresa_entrega.' - '.$cp_empresa_entrega.'<br/>PERSONA DE CONTACTO: '.$contacto_empresa_entrega.'<br/>'.$telefono_empresa_entrega;

$dir_facturacion->cargaDatosDireccionId($id_dir_facturacion);
$nombre_empresa_facturacion = utf8_decode(strtoupper($dir_facturacion->nombre_empresa));
$cif_empresa_facturacion = utf8_decode(strtoupper($dir_facturacion->cif));
$dir_empresa_facturacion = utf8_decode(strtoupper($dir_facturacion->direccion));
$localidad_empresa_facturacion = utf8_decode(strtoupper($dir_facturacion->localidad));
$provincia_empresa_facturacion = utf8_decode(strtoupper($dir_facturacion->provincia));
$cp_empresa_facturacion = strtoupper($dir_facturacion->codigo_postal);
$contacto_empresa_facturacion = utf8_decode(strtoupper($dir_facturacion->persona_contacto));
$telefono_empresa_facturacion = strtoupper($dir_facturacion->telefono);

$datos_facturacion = $nombre_empresa_facturacion.'<br/>'.$cif_empresa_facturacion.'<br/>'.$dir_empresa_facturacion.'<br/>'.$localidad_empresa_facturacion.' - '.$provincia_empresa_facturacion.' - '.$cp_empresa_facturacion.'<br/>PERSONA DE CONTACTO: '.$contacto_empresa_facturacion.'<br/>'.$telefono_empresa_facturacion.'<br/>';

// Obtenemos las referencias de la FRA_REQ
$listado_referencias_FR->setValoresFRA_REQ($ids_compra[$k],$proveedor);
$listado_referencias_FR->realizarConsultaFRA_REQ();
$resultados_referencias_fr = $listado_referencias_FR->referencias_OC_FR;
$num_referencias_fr = count($resultados_referencias_fr);
$euro = mb_convert_encoding('€', "HTML-ENTITIES", 'UTF-8');

$datos_agencia_transporte ='AGENCIA DE TRANSPORTE: '.$agencia_transporte.'<br/>CUENTA DE IMPORTACI&Oacute;N: '.$cuenta_importacion;

$orden_produccion->cargaDatosProduccionId($id_produccion);
$num_unidades = $orden_produccion->unidades;

$texto_observaciones = utf8_decode($oc->dameObservacionesFraRequest());

// Generar el pdf de esa OC
$mostrar_precios = 1;
$tabla_coste = '<th style="text-align: right;">PACK PRECIO</th>
	            <th style="text-align: right;">PRECIO TOTAL</th>';

$codigo='<html><head>
	<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
	</head>
	<body style="text-align:center">
	<div id="factura">
        <table>
            <tr>
                <td class="logo"><img src="../images/simumak-logo-new.jpg"></td>
                <td class="num_pedido">N&Uacute;MERO DE PEDIDO: <span style="font: bold 10px Helvetica,Verdana,Arial;">'.strtoupper($numero_pedido).'</span><br/>ORDEN DE COMPRA: <span style="font: bold 10px Helvetica,Verdana,Arial;">'.strtoupper($nombre_oc).'</span></td>
            </tr>
            <tr>
                <td class="titulo_dir_facturacion">DIRECCI&Oacute;N DE FACTURACI&Oacute;N</td>
                <td class="titulo_dir_entrega">DIRECCI&Oacute;N DE ENVIO</td>
            </tr>
            <tr>
                <td class="datos_facturacion">
                    <div class="datosFactura">'.$datos_facturacion.'</div>
                </td>
                <td class="datos_entrega">
                    <div class="datosFactura">'.$datos_entrega.'</div>
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="datos_agencia_transporte">'.$datos_agencia_transporte.'</td>
            </tr>
            <tr>
                <td class="titulo_proveedor">
                    PROVEEDOR:&nbsp;<span style="font-weight:normal;">'.strtoupper($nombre_prov).'</span>
                </td>
                <td>

                </td>
            </tr>
            <tr>
        </table>
        <table class="tabla_datos">
            <tr>
                <th>NUM</th>
                <th>ID_REF</th>
                <th style="text-align:left;">REFERENCIA</th>
                <th style="text-align:left;">NOMBRE</th>
                <th>UNIDADES</th>
                <th>UNIDADES PAQ.</th>
                <th>TOTAL PAQ.</th>
                '.$tabla_coste.'
            </tr>';

$num = 0;
for($i=0;$i<$num_referencias_fr;$i++) {
    $par = ($i%2 == 0);
    ++$num;
    if($mostrar_precios == 1) {
        $coste = '<td style="text-align: right;">'.number_format($resultados_referencias_fr[$i]["pack_precio"], 2, ',', '.').$euro.'</td>
                                    <td style="text-align: right;">'.number_format($resultados_referencias_fr[$i]["coste"], 2, ',', '.').$euro.'</td>';
    }
    $total_unidades = $resultados_referencias_fr[$i]["total_packs"];
    $unidades_paquete = $resultados_referencias_fr[$i]["uds_paquete"];
    $total_piezas = $resultados_referencias_fr[$i]["piezas"]*$num_unidades;
    $suma_paquetes = $total_piezas*$num_unidades/$unidades_paquete;
    $suma_paquetes = $suma_paquetes/$num_unidades;
    $exp = explode(".",$suma_paquetes);
    if($exp[0] == 0) {
        $totalPedir = 1;
    }
    else {
        if($exp[1] > 1) {
            $totalPedir = $exp[0]+1;
        }
        else {
            $totalPedir = $exp[0].$exp[1];
        }
    }

    $codigo .= '<tr>
                    <td>'.$num.'</td>
                    <td>'.$resultados_referencias_fr[$i]["id_referencia"].'</td>
                    <td style="text-align:left;">'.utf8_decode($resultados_referencias_fr[$i]["part_proveedor_referencia"]).'</td>
                    <td style="text-align:left;">'.utf8_decode($resultados_referencias_fr[$i]["referencia"]).'</td>
                    <td>'.$total_piezas.'</td>
                    <td>'.$unidades_paquete.'</td>
                    <td>'.$totalPedir.'</td>
                    '.$coste.'
                </tr>';
}

if($mostrar_precios == 1) {
    $codigo .= '<tr class="fila_precios">
                                <td colspan="8" class="titulo_total">TOTAL</td>
                                <td class="total">'.number_format($total, 2, ',', '.').$euro.'</td>
                                </tr>';
    $codigo .= '<tr class="fila_precios">
                                <td colspan="8" class="titulo_total">TOTAL + tasas</td>
                                <td class="total">'.number_format($precio_total, 2, ',', '.').$euro.'</td>
                        </tr>';
}

$codigo .= '</table>';

if(!empty($texto_observaciones)) {
    $codigo .= '<div style="width:760px; height:auto; background:#fff; font: 10px Helvetica, Verdana, Arial; font-weight: bold; color: #ff0000; margin:20px 0px 20px 35px; padding: 5px;
				text-align: justify; text-transform: uppercase; border: 1px solid #ff0000;">
					OBSERVACIONES:&nbsp;<br/><br/>'.$texto_observaciones.'
			</div>';
}

$codigo .= '</div></body>';

$dompdf=new DOMPDF();
$dompdf->load_html($codigo);
$dompdf->render();
$pdf = $dompdf->output();
file_put_contents($dir_actual."\\".$numero_pedido.".pdf", $pdf);
?>
