<?php
// Este fichero muestra las referencias de la orden de compra
include("../classes/mysql.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/orden_compra/orden_compra.class.php");
include("../classes/orden_compra/listado_referencias_oc.class.php");

$id_compra = $_GET["orden_compra"];
$id_proveedor = $_GET["id_proveedor"];
$codigo_oc = $_GET["codigo_oc"];

$db = new MySQL();
$ref_OC = new listadoReferenciasOC();

$ref_OC->setValores($id_compra,$id_proveedor);
$ref_OC->realizarConsulta();
$resultadosBusqueda = $ref_OC->referencias_OC;
// Obtenemos los datos de la Orden de Compra
$orden_compra = new Orden_Compra();
$orden_compra->cargaDatosOrdenCompraId($id_compra);
$nombre_oc = $orden_compra->numero_pedido;
$max_caracteres_ref = 50;
$max_caracteres = 25;
?>
<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
<div id="MuestraReferencias">
    <h1>Referencias asociadas a <?php echo $nombre_oc;?></h1>
    <div id="CapaTablaReferencias">
    	<table>
		<tr>
   			<th style="text-align:center">ID</th>
   			<th>NOMBRE</th>
        	<th>PROVEEDOR</th>
            <th>REF PROV</th>
        	<th style="text-align:center">PIEZAS</th>
            <th style="text-align:center">PRECIO PACK</th>
            <th style="text-align:center">UDS/P</th>
            <th style="text-align:center">TOTAL PAQS</th>
            <th style="text-align:center">PIEZAS PEDIDAS</th>
            <th style="text-align:center">PIEZAS RECIBIDAS</th>
            <th style="text-align:center">% RECIBIDO</th>
			<th style="text-align:center">PRECIO UNIDAD</th>
   			<th style="text-align:center">PRECIO</th>
		</tr>
		<?php
			for($i=0;$i<count($resultadosBusqueda);$i++) {
				$ref = new Referencia();
				$datoRef = $resultadosBusqueda[$i];
				$ref->cargaDatosReferenciaId($datoRef["id_referencia"]);
				$total_piezas = $datoRef["total_piezas"];
				$uds_paquete = $datoRef["uds_paquete"];
				$precio_pack = $datoRef["pack_precio"];
				$ref->calculaTotalPaquetes($uds_paquete,$total_piezas);
				$total_paquetes = $ref->total_paquetes;
				if (($precio_pack <> 0) and ($uds_paquete <> 0))
					$precio_unidad = $precio_pack / $uds_paquete;
				else $precio_unidad = 0;
				$precio_referencia = $precio_unidad * $total_piezas;
				$porcentaje_recepcion = $orden_compra->getPorcentajeRecepcionReferencia($id_compra,$datoRef["id_referencia"]);
				if($orden_compra->piezas_recibidas <> 0) {
					$porcentaje_recepcion = ($orden_compra->piezas_recibidas * 100 ) / $orden_compra->piezas_pedidas;
				} else {
					$porcentaje_recepcion = 0;
				}
				$porcentaje_recepcion = number_format($porcentaje_recepcion,2,',','.');
		?>
		<tr>
			<td style="text-align:center"><?php echo $ref->id_referencia; ?></td>
  			<td>
            	<a href="../basicos/mod_referencia.php?id=<?php echo $ref->id_referencia; ?>" target="_blank">
				<?php
					if (strlen($ref->referencia) > $max_caracteres_ref){
						echo substr($ref->referencia,0,$max_caracteres_ref).'...';
					}
					else {
						echo $ref->referencia;
					}
				?>
                </a>
            </td>
			<td><?php echo $ref->nombre_proveedor;?></td>
            <td><?php $ref->vincularReferenciaProveedor();?></td>
			<!--<td><?php //echo $ref->part_nombre;?></td>-->
			<td style="text-align:center"><?php echo $total_piezas?></td>
            <td style="text-align:center"><?php echo number_format($precio_pack, 2, ',', '.');?></td>
            <td style="text-align:center"><?php echo $uds_paquete;?></td>
            <td style="text-align:center"><?php echo $total_paquetes;?></td>
            <td style="text-align:center"><?php echo $orden_compra->piezas_pedidas;?></td>
            <td style="text-align:center"><?php echo $orden_compra->piezas_recibidas;?></td>
            <td style="text-align:center"><?php echo $porcentaje_recepcion;?></td>
            <td style="text-align:center"><?php echo number_format($precio_unidad, 2, ',', '.');?></td>
            <td style="text-align:center"><?php echo number_format($precio_referencia, 2, ',', '.');?></td>
        </tr>
		<?php
			}
		?>
		</table>
	</div>
</div>
