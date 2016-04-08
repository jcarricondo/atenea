<?php
// Popup recepcion / desrecepcion referencia
include("../classes/mysql.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/almacen/albaran.class.php");
include("../classes/almacen/recepcion_material.class.php");

$db = new MySQL();
$orden_produccion = new Orden_Produccion();
$referencia = new Referencia();
$albaran = new Albaran();
$almacen = new Almacen();
$rm = new RecepcionMaterial();
$rs = new RecepcionMaterial();

$id_albaran = $_GET["id_albaran"];
$id_referencia = $_GET["id_referencia"];
$id_almacen = $_GET["id_almacen"];

// Cargamos los datos del almacen
$almacen->cargaDatosAlmacenId($id_almacen);
$nombre_almacen = strtoupper(utf8_decode($almacen->nombre));

// Cargamos los datos del albaran y de la referencia
$albaran->cargaDatosAlbaranId($id_albaran);
$nombre_albaran = $albaran->nombre_albaran;

$referencia->cargaDatosReferenciaId($id_referencia);
$nombre_referencia = $referencia->referencia;

// Obtenemos la sede a la que pertenece ese almacen
$id_sede = $almacen->dameSedeAlmacen($id_almacen);
$id_sede = $id_sede["id_sede"];

// Buscamos las ordenes de produccion iniciadas que tienen esa referencia
$orden_produccion->dameOPIniciadasReferencia($id_referencia,$id_sede);
$ids_produccion = $orden_produccion->ids_produccion;  
?>
<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
<div id="MuestraReferencias">
	<h1> Movimientos del albaran <?php echo " ".$nombre_albaran; ?> </h1>
    <h2> <?php echo "Referencia: ".$nombre_referencia." (ID REF: ".$id_referencia.")";?> </h2>
    <h2> <?php echo "Almacen: ".$nombre_almacen;?> </h2>
    <div id="CapaTablaReferencias">
    	<table>
        <tr>
        	<th style="width:30%;">ORDEN PRODUCCION</th>
   			<th style="width:10%; text-align:center">UND. PED</th>
    		<th style="width:10%; text-align:center">UND. REC</th>
   			<th style="width:10%; text-align:center">UND. PTE</th>
    		<th style="width:10%; text-align:center">UND. USA</th>
    		<th style="width:10%; text-align:center">UND. DIS</th>
    		<th style="width:20%; text-align:center;"></th>
        </tr>
        <?php
			for($i=0;$i<count($ids_produccion);$i++){
				$id_produccion = $ids_produccion[$i]["id_produccion"];

				// Cargamos los datos de la orden de produccion
				$orden_produccion->cargaDatosProduccionId($id_produccion);
				$alias = $orden_produccion->alias_op;

                $registro_ocr = $rm->dameRegistroOCR($id_produccion,$id_referencia);
               	$piezas_totales = $registro_ocr["total_piezas"];
               	$piezas_recibidas = $registro_ocr["piezas_recibidas"];
               	$piezas_pendientes = $piezas_totales - $piezas_recibidas;
               	$piezas_usadas = $registro_ocr["piezas_usadas"];
               	$piezas_disponibles = $piezas_recibidas - $piezas_usadas;
		?>
		<tr>
			<td style="width:30%;">
				<?php echo $alias; ?>
			</td>
			<td style="width:10%; text-align:center">
				<?php echo $piezas_totales; ?>
			</td>
			<td style="width:10%; text-align:center">
				<?php echo $piezas_recibidas; ?>
			</td>
			<td style="width:10%; text-align:center">
				<?php 
					if($piezas_pendientes > 0){
						echo '<span style="color:red;">';
					}
					else{
						echo '<span style="color:green;">'; 
					}
					echo $piezas_pendientes.'</span>'; 
				?>
            </td>
            <td style="width:10%; text-align:center">
            	<?php echo $piezas_usadas; ?>
            </td>
            <td style="width:10%; text-align:center">
            	<?php echo $piezas_disponibles; ?>
            </td>
            <td style="width:20%; text-align:center">
            	
            </td>
        </tr>
 		<?php
			}
			// Despues de cargar las OP mostramos STOCK
		    // Tenemos que ver si esa referencia tiene piezas en stock
		    $piezas_stock = $rs->damePiezasReferenciaStock($id_referencia,$id_almacen);
		                            
		    if($piezas_stock != NULL){
		    	$piezas_recibidas = $piezas_stock;
		        $piezas_recibidas_referencia = $piezas_recibidas_referencia + $piezas_recibidas;
		        $piezas_disponibles = $piezas_recibidas;
		        $piezas_disponibles_referencia = $piezas_disponibles_referencia + $piezas_disponibles;
		    }
		    else{
		    	$piezas_recibidas = "-";
		        $piezas_disponibles = "-";
		   	}
		?>
		<tr> 
			<td style="width:30%; color:#0B3861;">STOCK</td>
			<td style="width:10%; text-align:center;">-</td>
			<td style="width:10%; text-align:center;"><?php echo $piezas_recibidas; ?></td>
			<td style="width:10%; text-align:center;">-</td>
			<td style="width:10%; text-align:center;">-</td>
			<td style="width:10%; text-align:center;"><?php echo $piezas_disponibles; ?></td>
			<td style="width:20%; text-align:center;"></td>
		</tr>
		</table>                  
	</div>
	<?php
		// Ahora mostramos los movimientos producidos para esa referencia
		// Obtenemos el log del albaran 
		$resultados = $albaran->dameDatosLogReferencia($id_albaran,$id_referencia);
	?>	

	<div id="CapaTablaReferencias">
		<table>
			<th colspan="7">LOG</th>
			<?php
				for($i=0;$i<count($resultados);$i++){
					$id_produccion = $resultados[$i]["id_produccion"];
					$id_referencia = $resultados[$i]["id_referencia"];
					$piezas = $resultados[$i]["piezas"];
					$metodo = $resultados[$i]["metodo"];

					if($id_produccion != 0){
						// Cargamos los datos de la OP
						$orden_produccion->cargaDatosProduccionId($id_produccion);
						$alias = $orden_produccion->alias_op;
					}
					else {
						$alias = "STOCK";
					}

					$tabla_log .= '<tr>';

				  	if($metodo == "RECEPCIONAR"){
				  		$tabla_log .= '<td colspan="7">Se recepcionaron '.$piezas.' piezas en la Orden de Produccion '.$alias.' para la id_referencia '.$id_referencia.'</td>';
				  	}	
				  	else{
				  		$tabla_log .= '<td colspan="7">Se desrecepcionaron '.$piezas.' piezas en la Orden de Produccion '.$alias.' para la id_referencia '.$id_referencia.'</td>';
					}
					$tabla_log_end .= '</tr>';
				}
				echo $tabla_log.$tabla_log_end;
			?>
		</table>
	</div>
</div>