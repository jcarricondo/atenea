<?php
// Popup recepción / desrecepción material informático
include("../includes/sesion.php");
include("../classes/almacen/almacen.class.php");
include("../classes/material_informatico/albaran_informatico.class.php");
include("../classes/material_informatico/material_informatico.class.php");

$almacen = new Almacen();
$albaranInformatico = new AlbaranInformatico();
$materialInformatico = new MaterialInformatico();

$id_albaran = $_GET["id_albaran"];
$num_serie = $_GET["num_serie"];
$averiado = $_GET["averiado"];
$metodo = $_GET["metodo"];

// Cargamos los datos del albaran y del material
$albaranInformatico->cargaDatosAlbaranId($id_albaran);
$nombre_albaran = $albaranInformatico->nombre_albaran;
$id_almacen = $albaranInformatico->id_almacen;

// Cargamos los datos del almacen
$almacen->cargaDatosAlmacenId($id_almacen);
$nombre_almacen = strtoupper($almacen->nombre);

$resultado_id_material = $materialInformatico->dameIdMaterialPorNumSerie($num_serie);
$id_material = $resultado_id_material["id_material"];
?>
<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
<div id="MuestraReferencias">
	<h1> Movimientos del albaran <?php echo " ".$nombre_albaran; ?> </h1>
    <h2> <?php echo "Material Inform&aacute;tico: ".$num_serie;?> </h2>
    <h2> <?php echo "Almacen: ".$nombre_almacen;?> </h2>

	<div id="CapaTablaReferencias">
		<table>
			<th colspan="7">LOG</th>
			<?php
				$tabla_log .= '<tr>';
			  	if($metodo == '"RECEPCIONAR"'){
			  		if($averiado == '"SI"'){
			  			$tabla_log .= '<td colspan="7">Se recepcion&oacute; el material inform&aacute;tico y ahora se encuentra en estado <span style="color:red">AVERIADO</span></td>';    
			  		}
			  		else {
			  			$tabla_log .= '<td colspan="7">Se recepcion&oacute; el material inform&aacute;tico y ahora se encuentra en estado <span style="color:green">STOCK</span></td>'; 
			  		}	 
			  	}	
			  	else{
			  		if($averiado == '"SI"'){
			  			$tabla_log .= '<td colspan="7">Se desrecepcion&oacute; el material inform&aacute;tico y ahora se encuentra en estado <span style="color:red">EN REPARACION</span></td>';
			  		}
			  		else {
			  			$tabla_log .= '<td colspan="7">Se desrecepcion&oacute; el material inform&aacute;tico y ahora se encuentra en estado <span style="color:green">EN USO</span></td>'; 
			  		}
				}
				$tabla_log_end .= '</tr>';
				echo $tabla_log.$tabla_log_end;
			?>
		</table>
	</div>
</div>