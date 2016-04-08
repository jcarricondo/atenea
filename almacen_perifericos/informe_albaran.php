<?php 
// Este fichero genera un excel del albarán de periféricos
include("../includes/sesion.php");
include("../classes/basicos/usuario.class.php");
include("../classes/basicos/centro_logistico.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/almacen_perifericos/albaran_periferico.class.php");
include("../classes/almacen_perifericos/periferico_almacen.class.php");

$user = new Usuario();
$almacen = new Almacen();
$centroLogistico = new centroLogistico();
$alb = new AlbaranPeriferico();
$per = new PerifericoAlmacen();

$id_albaran = $_GET["id_albaran"];

// Obtenemos los datos del albarán
$alb->cargaDatosAlbaranId($id_albaran);

$nombre_albaran = $alb->nombre_albaran;
$tipo_albaran = $alb->tipo_albaran;
$id_centro_logistico = $alb->id_centro_logistico;
$id_usuario = $alb->id_usuario;
$motivo = $alb->motivo;
$fecha_creado_alb = $alb->fecha_creado;

// Obtenemos el nombre del centro logístico
$centroLogistico->cargaDatosCentroLogisticoId($id_centro_logistico);
$nombre_centro = $centroLogistico->nombre;

// Obtenemos el nombre del usuario
$user->cargaDatosUsuarioId($id_usuario);
$nombre_usuario = $user->usuario;

$table = '<table>
		<tr>
	    	<th style="text-align:left;">Usuario</th>
	        <th style="text-align:center;">Fecha</th>
			<th style="text-align:left;">Tipo Albaran</th>
			<th style="text-align:left;">Nombre Albaran</th>
			<th style="text-align:left;">Nombre Almacen</th>
			<th style="text-align:left;">Origen / Destino</th>
			<th style="text-align:left;">Motivo</th>
			<th style="text-align:center;">NUM SERIE</th>
			<th style="text-align:left;">ESTADO</th>
        </tr>';


// Obtenemos los movimientos del albarán
$movimientos_albaran = $alb->dameMovimientosAlbaran($id_albaran);

for($i=0;$i<count($movimientos_albaran);$i++){
	// Preparamos los datos
	$id_periferico = $movimientos_albaran[$i]["id_periferico"];
	$estado = $movimientos_albaran[$i]["estado"];

	// Cargamos el número de serie según el id_periferico
	$per->cargaDatosPerifericoId($id_periferico);
	$numero_serie = $per->numero_serie;
	$id_almacen = $per->id_almacen;

	// Obtenemos el nombre del almacen
	$almacen->cargaDatosAlmacenId($id_almacen);
	$nombre_almacen = $almacen->nombre;

    // Obtenemos la sede del almacen al que pertenece el albarán
    $sede_almacen = $almacen->dameSedeAlmacen($id_almacen);
    $sede_almacen = $sede_almacen["id_sede"];
    $esAlbaranBrasil = $sede_almacen == 3;

    if($esAlbaranBrasil) $fecha_creado = $user->fechaHoraBrasil($fecha_creado_alb);
    else $fecha_creado = $user->fechaHoraSpain($fecha_creado_alb);

	$salida .= '<tr>
		  	<td style="text-align:left;">'.$nombre_usuario.'</td>
	      	<td style="text-align:center;">'.$fecha_creado.'</td>
			<td style="text-align:left;">'.$tipo_albaran.'</td>
			<td style="text-align:left;">'.utf8_decode($nombre_albaran).'</td>
			<td style="text-align:left;">'.utf8_decode($nombre_almacen).'</td>  
			<td style="text-align:left;">'.utf8_decode($nombre_centro).'</td>
			<td style="text-align:left;">'.$motivo.'</td>
			<td style="text-align:center;">'.$numero_serie.'</td>
			<td style="text-align:left;">'.$estado.'</td>
		  </tr>';	
}

$table_end = '</table>';
$nombre_albaran = str_replace(" ","_",$nombre_albaran);
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".utf8_decode($nombre_albaran).".xls");
echo $table.$salida.$table_end; 
?>
