<?php 
//Este fichero genera un excel con los simuladores del almacen
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/almacen/almacen.class.php");
include("../classes/almacen_simuladores/simulador_almacen.class.php");
include("../classes/almacen_simuladores/albaran_simulador.class.php");
include("../classes/almacen_simuladores/listado_simuladores_almacen.class.php");

$almacen = new Almacen();
$simuladorAlmacen = new SimuladorAlmacen();
$albaranSimulador = new AlbaranSimulador();
$listadoSimuladores = new listadoSimuladorAlmacen();

$num_serie = $_SESSION["num_serie_xls_simulador_almacen"];
$estado = $_SESSION["estados_xls_simulador_almacen"];
$id_almacen = $_SESSION["id_almacen_xls_simulador_almacen"];
$id_sede = $_SESSION["id_sede_xls_simulador_almacen"];

$listadoSimuladores->setValores($num_serie,$estado,'',0,$id_almacen,$id_sede);
$listadoSimuladores->realizarConsulta();
$resultadosBusqueda = $listadoSimuladores->simuladores;

$table .= '<table>
		<tr>
	    	<th style="text-align:center;">NUM. SERIE</th>
            <th style="">ALMACEN</td>
            <th style="">ESTADO</th>
            <th style="">COMENTARIOS</th>
        </tr>';

// Se cargan los datos de los simuladores de la búsqueda según su identificador
for($i=0;$i<count($resultadosBusqueda);$i++) {
	$id_simulador = $resultadosBusqueda[$i]["id_simulador"];

    $simuladorAlmacen->cargaDatosSimuladorId($id_simulador);
	$numero_serie = $simuladorAlmacen->numero_serie;
    $estado = $simuladorAlmacen->estado;
    $comentarios = $simuladorAlmacen->comentarios;
    $id_almacen = $simuladorAlmacen->id_almacen;

    $almacen->cargaDatosAlmacenId($id_almacen);
    $nombre_almacen = $almacen->nombre;

    $salida .= '<tr>
                    <td style="text-align:center;">'.$numero_serie.'</td>
                    <td>'.utf8_decode($nombre_almacen).'</td>
                    <td>'.$estado.'</td>
                    <td>'.utf8_decode($comentarios).'</td>
		        </tr>';
}
$table_end = '</table>';

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=informeSimuladores.xls");
echo $table.$salida.$table_end;
?>
