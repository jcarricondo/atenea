<?php 
// Este fichero genera un excel con los resultados de búsqueda de los movimientos
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/basicos/usuario.class.php");
include("../classes/basicos/centro_logistico.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/almacen_simuladores/simulador_almacen.class.php");
include("../classes/almacen_simuladores/albaran_simulador.class.php");
include("../classes/almacen_simuladores/listado_movimientos_simuladores.class.php");

// Instancias de las clases
$usuario = new Usuario();
$centroLogistico = new CentroLogistico();
$almacen = new Almacen();
$albaranSimulador = new AlbaranSimulador();
$simuladorAlmacen = new simuladorAlmacen();
$listadoMovimientos = new listadoMovimientosSimulador();

$nombre_albaran = $_SESSION["nombre_albaran_xls_simuladores_movimientos"];
$tipo_albaran = $_SESSION["tipo_albaran_xls_simuladores_movimientos"];
$id_centro_logistico = $_SESSION["id_centro_logistico_xls_simuladores_movimientos"];
$id_usuario = $_SESSION["id_usuario_xls_simuladores_movimientos"];
$numero_serie = $_SESSION["numero_serie_xls_simuladores_movimientos"];
$averiado = $_SESSION["averiado_xls_simuladores_movimientos"];
$tipo_motivo = $_SESSION["tipo_motivo_xls_simuladores_movimientos"];
$id_almacen = $_SESSION["id_almacen_xls_simuladores_movimientos"];
$fecha_desde = $_SESSION["fecha_desde_xls_simuladores_movimientos"];
$fecha_hasta = $_SESSION["fecha_hasta_xls_simuladores_movimientos"];
$id_sede = $_SESSION["id_sede_xls_simuladores_movimientos"];

$listadoMovimientos->setValores($nombre_albaran,$tipo_albaran,$id_centro_logistico,$id_usuario,$numero_serie,$averiado,$tipo_motivo,$fecha_desde,$fecha_hasta,'',$id_almacen,$id_sede);
$listadoMovimientos->realizarConsulta();
$resultadosBusqueda = $listadoMovimientos->movimientos;

$table= '<table>
		<tr>
            <th style="text-align:left">NUM. SERIE</th>
            <th style="text-align:left">ALBARAN</th>
            <th style="text-align:left">TIPO ALBARAN</th>
            <th style="text-align:left">USUARIO</th>
            <th style="text-align:left">ALMACEN</th>
            <th style="text-align:left">ORIGEN / DESTINO</th>
            <th style="text-align:left">MOTIVO</th>
            <th style="text-align:center">FECHA CREACION</th>
            <th style="text-align:center">AVERIADO</th>
    	</tr>';

// Recorremos todos los movimientos de la búsqueda
for($i=0;$i<count($resultadosBusqueda);$i++){
    $id_albaran = $resultadosBusqueda[$i]["id_albaran"];
    $numero_serie = $resultadosBusqueda[$i]["numero_serie"];
    $averiado = $resultadosBusqueda[$i]["averiado"];
    $id_simulador = $resultadosBusqueda[$i]["id_simulador"];
    $fecha_creado = $resultadosBusqueda[$i]["fecha_creado"];

    // Cargamos los datos del albarán del simulador
    $albaranSimulador->cargaDatosAlbaranId($id_albaran);
    $nombre_albaran = $albaranSimulador->nombre_albaran;
    $tipo_albaran = $albaranSimulador->tipo_albaran;
    $id_centro_logistico = $albaranSimulador->id_centro_logistico;    
    $id_usuario = $albaranSimulador->id_usuario;
    $motivo = $albaranSimulador->motivo;

    // Cargamos el nombre del tipo de simulador
    $simuladorAlmacen->cargaDatosSimuladorId($id_simulador);
    $id_almacen = $simuladorAlmacen->id_almacen;

    // Cargamos el nombre del usuario
    $usuario->cargaDatosUsuarioId($id_usuario);
    $nombre_usuario = $usuario->usuario;

    // Cargamos el nombre del centro logístico
    $centroLogistico->cargaDatosCentroLogisticoId($id_centro_logistico);
    $nombre_centro = $centroLogistico->nombre;

    // Cargamos el nombre del almacen
    $almacen->cargaDatosAlmacenId($id_almacen);
    $nombre_almacen = $almacen->nombre;

    // Obtenemos la sede del almacen al que pertenece el albarán
    $sede_almacen = $almacen->dameSedeAlmacen($id_almacen);
    $sede_almacen = $sede_almacen["id_sede"];
    $esAlbaranBrasil = $sede_almacen == 3;

    if($esAlbaranBrasil) $fecha_creado = $usuario->fechaHoraBrasil($fecha_creado);
    else $fecha_creado = $usuario->fechaHoraSpain($fecha_creado);

    $salida .= '<tr>   
                     <td style="text-align:left">'.$numero_serie.'</td>
                     <td style="text-align:left">'.utf8_decode($nombre_albaran).'</td>
                     <td style="text-align:left">'.$tipo_albaran.'</td>
                     <td style="text-align:left">'.$nombre_usuario.'</td>
                     <td style="text-align:left">'.utf8_decode($nombre_almacen).'</td>
                     <td style="text-align:left">'.utf8_decode($nombre_centro).'</td>
                     <td style="text-align:left">'.utf8_decode($motivo).'</td>
                     <td style="text-align:center">'.$fecha_creado.'</td>
                     <td style="text-align:center">';

    if ($averiado == "NO"){
        $salida .= '<span style="color: green;">'.$averiado.'</span>';        
    }
    else {
        $salida .= '<span style="color: red;">'.$averiado.'</span>';    
    }

    $salida .= '</td></tr>';
}
$table_end = '</table>';

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=informeMovimientos.xls");
echo $table.$salida.$table_end; 
?>
