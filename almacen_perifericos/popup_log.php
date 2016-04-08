<?php
// Popup recepción / desrecepción referencia
include("../classes/mysql.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/almacen_perifericos/albaran_periferico.class.php");
include("../classes/almacen_perifericos/periferico_almacen.class.php");

$db = new MySQL();
$almacen = new Almacen();
$albaranPeriferico = new AlbaranPeriferico();
$perifericoAlmacen = new PerifericoAlmacen();

$id_albaran = $_GET["id_albaran"];
$num_serie = $_GET["num_serie"];
$averiado = $_GET["averiado"];
$metodo = $_GET["metodo"];

// Cargamos los datos del albarán y del periférico
$albaranPeriferico->cargaDatosAlbaranId($id_albaran);
$nombre_albaran = $albaranPeriferico->nombre_albaran;
$id_almacen = $albaranPeriferico->id_almacen;

// Cargamos los datos del almacen
$almacen->cargaDatosAlmacenId($id_almacen);
$nombre_almacen = strtoupper(utf8_decode($almacen->nombre));

$resultado_id_periferico = $perifericoAlmacen->dameIdPerifericoPorNumSerie($num_serie);
$id_periferico = $resultado_id_periferico["id_periferico"];
?>
<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
<div id="MuestraReferencias">
	<h1> Movimientos del albaran <?php echo " ".$nombre_albaran; ?> </h1>
    <h2> <?php echo "Periferico: ".$num_serie;?> </h2>
    <h2> <?php echo "Almacen: ".$nombre_almacen;?> </h2>

	<div id="CapaTablaReferencias">
		<table>
			<th colspan="7">LOG</th>
			<?php
				$tabla_log .= '<tr>';
			  	if($metodo == '"RECEPCIONAR"'){
			  		if($averiado == '"SI"'){
			  			$tabla_log .= '<td colspan="7">Se recepcion&oacute; el perif&eacute;rico y se encuentra en estado <span style="color:red">AVERIADO</span></td>';    
			  		}
			  		else {
			  			$tabla_log .= '<td colspan="7">Se recepcion&oacute; el perif&eacute;rico y se encuentra en estado <span style="color:green">OPERATIVO</span></td>'; 
			  		}	 
			  	}	
			  	else{
			  		$tabla_log .= '<td colspan="7">Se desrecepcion&oacute; el perif&eacute;rico correctamente</td>';
				}
				$tabla_log_end .= '</tr>';
				echo $tabla_log.$tabla_log_end;
			?>
		</table>
	</div>
</div>