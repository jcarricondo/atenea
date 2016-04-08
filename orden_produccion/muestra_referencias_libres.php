<?php
// Muestra las referencias libres de la OP
include("../classes/mysql.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");

$id_produccion = $_GET["id_produccion"];

$db = new MySQL();
$orden_produccion = new Orden_Produccion();
$referencia = new Referencia();

$referencias_libres = $orden_produccion->cargaDatosPorProduccionComponente($id_produccion,0);
$max_caracteres = 50;
?>
<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
<div id="MuestraReferencias">
	<h1> Referencias Libres asociadas al producto </h1>
    <h2> <?php echo $_GET["producto"];?> </h2>
    <div id="CapaTablaReferencias">
    	<table>
        <tr>
        	<th style="text-align:center">ID_REF</th>
        	<th>NOMBRE</th>
        	<th>PROVEEDOR</th>
            <th>REF PROV</th>
        	<th style="text-align:center">PIEZAS</th>
            <th style="text-align:center">PRECIO PACK</th>
            <th style="text-align:center">UDS/P</th>
            <th style="text-align:center">TOTAL PAQS</th>
			<th style="text-align:center">PRECIO UNIDAD</th>
   			<th style="text-align:center">PRECIO</th> 
        </tr>
        <?php
			for($i=0;$i<count($referencias_libres);$i++) {
				// Se cargan los datos de las referencias libres según su identificador
				$id_referencia = $referencias_libres[$i]["id_referencia"];
				$uds_paquete = $referencias_libres[$i]["uds_paquete"];
				$piezas = $referencias_libres[$i]["piezas"];
				$total_paquetes = $referencias_libres[$i]["total_paquetes"];
				$pack_precio = $referencias_libres[$i]["pack_precio"];

				if (($pack_precio <> 0) and ($uds_paquete <> 0)){
					$precio_unidad = $pack_precio / $uds_paquete;
				}	
				else $precio_unidad = 0;

				$precio_referencia = $precio_unidad * $piezas;	
				$referencia->cargaDatosReferenciaId($id_referencia);
		?>
		<tr>
			<td style="text-align:center"><?php echo $id_referencia; ?></td>
			<td>
				<a href="../basicos/mod_referencia.php?id=<?php echo $id_referencia; ?>" target="_blank">
					<?php 
						if (strlen($referencia->referencia) > $max_caracteres){
							echo substr($referencia->referencia,0,$max_caracteres).'...';
						}
						else {
							echo $referencia->referencia;	
						}
					?>
                </a>
			</td>
			<td><?php echo $referencia->nombre_proveedor; ?></td>
			<td><?php $referencia->vincularReferenciaProveedor();?></td>
			<td style="text-align:center"><?php echo $piezas;?></td>
			<td style="text-align:center"><?php echo number_format($pack_precio, 2, ',', '.'); ?></td>
            <td style="text-align:center"><?php echo $uds_paquete; ?></td>
            <td style="text-align:center"><?php echo $total_paquetes;?></td>
            <td style="text-align:center"><?php echo number_format($precio_unidad, 2, ',', '.');?></td>
            <td style="text-align:center"><?php echo number_format($precio_referencia, 2, ',', '.');?></td>
        </tr>
 		<?php
			}
		?>
		</table>                  
	</div>
</div>