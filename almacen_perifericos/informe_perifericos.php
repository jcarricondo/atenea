<?php 
//Este fichero genera un excel con los periféricos del almacen
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/almacen/almacen.class.php");
include("../classes/almacen_perifericos/periferico_almacen.class.php");
include("../classes/almacen_perifericos/albaran_periferico.class.php");
include("../classes/almacen_perifericos/listado_perifericos_almacen.class.php");

$almacen = new Almacen();
$perifericoAlmacen = new PerifericoAlmacen();
$albaranPeriferico = new AlbaranPeriferico();
$listadoPerifericos = new listadoPerifericosAlmacen();

$num_serie = $_SESSION["num_serie_xls_perifericos_almacen"];
$tipo_periferico = $_SESSION["tipo_periferico_xls_perifericos_almacen"];
$estado = $_SESSION["estados_xls_perifericos_almacen"];
$id_almacen = $_SESSION["id_almacen_xls_perifericos_almacen"];
$id_sede = $_SESSION["id_sede_xls_perifericos_almacen"];

$listadoPerifericos->setValores($num_serie,$tipo_periferico,$estado,'',0,$id_almacen,$id_sede);
$listadoPerifericos->realizarConsulta();
$resultadosBusqueda = $listadoPerifericos->perifericos;

$table .= '<table>
		<tr>
	    	<th style="text-align:center;">NUM. SERIE</th>
            <th style="">TIPO PERIFERICO</th>
            <th style="">ALMACEN</td>
            <th style="">ESTADO</th>
            <th style="">COMENTARIOS</th>
        </tr>';

// Se cargan los datos de los periféricos de la búsqueda según su identificador
for($i=0;$i<count($resultadosBusqueda);$i++) {
	$id_periferico = $resultadosBusqueda[$i]["id_periferico"];

    $perifericoAlmacen->cargaDatosPerifericoId($id_periferico);
	$numero_serie = $perifericoAlmacen->numero_serie;
    $tipo_periferico = $perifericoAlmacen->tipo_periferico;
    $estado = $perifericoAlmacen->estado;
    $comentarios = $perifericoAlmacen->comentarios;
    $id_almacen = $perifericoAlmacen->id_almacen;

    $nombre_tipo = $perifericoAlmacen->dameNombreTipoPeriferico($tipo_periferico);
    $nombre_tipo = $nombre_tipo["nombre"];

    $almacen->cargaDatosAlmacenId($id_almacen);
    $nombre_almacen = $almacen->nombre;

    $salida .= '<tr>
                    <td style="text-align:center;">'.$numero_serie.'</td>
                    <td>'.$nombre_tipo.'</td>
                    <td>'.utf8_decode($nombre_almacen).'</td>
                    <td>'.$estado.'</td>
                    <td>'.utf8_decode($comentarios).'</td>
		        </tr>';
}
$table_end = '</table>';

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=informePerifericos.xls");
echo $table.$salida.$table_end;
?>
