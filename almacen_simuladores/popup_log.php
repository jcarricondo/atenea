<?php
// Popup recepción / desrecepción de simuladores
include("../classes/mysql.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/almacen_simuladores/albaran_simulador.class.php");
include("../classes/almacen_simuladores/simulador_almacen.class.php");

$db = new MySQL();
$almacen = new Almacen();
$albaranSimulador = new AlbaranSimulador();
$simuladorAlmacen = new SimuladorAlmacen();

$id_albaran = $_GET["id_albaran"];
$num_serie = $_GET["num_serie"];
$averiado = $_GET["averiado"];
$metodo = $_GET["metodo"];

// Cargamos los datos del albarán y del simulador
$albaranSimulador->cargaDatosAlbaranId($id_albaran);
$nombre_albaran = $albaranSimulador->nombre_albaran;
$id_almacen = $albaranSimulador->id_almacen;

// Cargamos los datos del almacen
$almacen->cargaDatosAlmacenId($id_almacen);
$nombre_almacen = strtoupper(utf8_decode($almacen->nombre));

$resultado_id_simulador = $simuladorAlmacen->dameIdSimuladorPorNumSerie($num_serie);
$id_simulador = $resultado_id_simulador["id_simulador"];
?>
<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
<div id="MuestraReferencias">
	<h1> Movimientos del albaran <?php echo " ".$nombre_albaran; ?> </h1>
    <h2> <?php echo "Simulador: ".$num_serie;?> </h2>
    <h2> <?php echo "Almacen: ".$nombre_almacen;?> </h2>

	<div id="CapaTablaReferencias">
		<table>
			<th colspan="7">LOG</th>
			<?php
				$tabla_log .= '<tr>';
			  	if($metodo == '"RECEPCIONAR"'){
			  		if($averiado == '"SI"'){
			  			$tabla_log .= '<td colspan="7">Se recepcion&oacute; el simulador y se encuentra en estado <span style="color:red">AVERIADO</span></td>';    
			  		}
			  		else {
			  			$tabla_log .= '<td colspan="7">Se recepcion&oacute; el simulador y se encuentra en estado <span style="color:green">OPERATIVO</span></td>'; 
			  		}	 
			  	}	
			  	else{
			  		$tabla_log .= '<td colspan="7">Se desrecepcion&oacute; el simulador correctamente</td>';
				}
				$tabla_log_end .= '</tr>';
				echo $tabla_log.$tabla_log_end;
			?>
		</table>
	</div>
</div>