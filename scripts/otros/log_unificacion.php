<?php 
set_time_limit(10000);
// Script que genera un informe con el log del proceso de unificacion
include("../../classes/mysql.class.php");
include("../../classes/funciones/log_unificacion.class.php");

$db = new MySql();
$log = new Log_Unificacion();

$fecha_actual = getdate();

// Generamos la tabla HTML style=""
$tabla = '<table>
	<tr>
		<th>LOG UNIFICACION</th>
	</tr>
	<tr>
		<th style="background-color: green; color: white; text-align: center;">ID Proceso</th>
    	<th style="background-color: green; color: white;">Proceso</th>
        <th style="background-color: green; color: white;">Comentarios</th>
        <th style="background-color: green; color: white; text-align: center;">Fecha Creacion</th>
    </tr>';

// Obtenemos todos los log de la unificacion
$logSql = "select * from log_unificacion";
$db->setConsulta($logSql);
$db->ejecutarConsulta();	
$resultados = $db->getResultados();

for($i=0;$i<count($resultados);$i++){
	$id = $resultados[$i]["id"];
	$proceso = $resultados[$i]["proceso"];
	$comentarios = $resultados[$i]["comentarios"];
	$fecha = $resultados[$i]["fecha"];

	if(($i % 2) == 0) {
		$color = ' background-color: #fff; ';
	} 
	else {
		$color = ' background-color: #eee; ';
	}

	$tabla .= '<tr>
				<td style="'.$color.'text-align: center;">'.$id.'</td>
				<td style="'.$color.'">'.$proceso.'</td>
				<td style="'.$color.'">'.$comentarios.'</td>
				<td style="'.$color.'text-align: center;">'.$fecha.'</td>
			</tr>';
}

$tabla .= '</table>';

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=LogUnificacion.xls");
echo $tabla; 
?>

