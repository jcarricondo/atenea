<?php
// Este fichero muestra los ordenadores asociados a un producto de la OP. Los ordenadores estaran dentro de los perifericos del producto de la OP
include("../classes/mysql.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");

$id_produccion = $_GET["id_produccion"];

$db = new MySQL();
$orden_produccion = new Orden_Produccion();
$ref = new Referencia();

// Consultamos las referencias de tipo ORDENADOR de la Orden de Produccion
$resultadosBusqueda = $orden_produccion->dameReferenciasOrdenadores($id_produccion);
?>
<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
<div id="MuestraReferencias">
	<h1> Ordenadores asociados al producto </h1>
    <h2> <?php echo $_GET["producto"];?> </h2>
    <div id="CapaTablaReferencias">
    	<table>
        <tr>
        	<th>NOMBRE</th>
   			<th>PROVEEDOR</th>
    		<th>REF PROV</th>
   			<th>NOMBRE PIEZA</th>
    		<th style="text-align:center">PACK PRECIO</th>
    		<th style="text-align:center">UDS/P</th>
        </tr>
        <?php
			for($i=0;$i<count($resultadosBusqueda);$i++){
				// Se cargan los ordenadores guardados como referencias
				$datoReferencia = $resultadosBusqueda[$i];
				$ref->cargaDatosReferenciaId($datoReferencia["id_referencia"]);
		?>
		<tr>
			<td>
				<?php echo $ref->referencia; ?>
			</td>
			<td>
				<?php echo $ref->nombre_proveedor; ?>
			</td>
			<td>
				<?php echo $ref->part_proveedor_referencia; ?>
			</td>
			<td>
				<?php echo $ref->part_nombre; ?>
            </td>
            <td style="text-align:center">
            	<?php echo $ref->pack_precio; ?>
            </td>
            <td style="text-align:center">
            	<?php echo $ref->unidades; ?>
            </td>
        </tr>
 		<?php
			}
		?>
		</table>                  
	</div>
</div>