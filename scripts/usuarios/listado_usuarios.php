<?php 
set_time_limit(10000);
// Script que genera un informe con los usuarios de la BBDD
include("../../classes/mysql.class.php");
include("../../classes/basicos/usuario.class.php");
include("../../classes/taller/taller.class.php");
include("../../classes/almacen/almacen.class.php");

$db = new MySql();
$user = new Usuario();
$taller = new Almacen();
$almacen = new Almacen();

// Generamos la tabla HTML style=""
$tabla = '<table>
	<tr>
		<th>LISTADO DE USUARIOS DE SIMUMAK</th>
	</tr>
	<tr>
		<th style="background-color: green; color: white; text-align: center;">ID Usuario</th>
    	<th style="background-color: green; color: white;">Nombre</th>
        <th style="background-color: green; color: white;">Email</th>
        <th style="background-color: green; color: white; text-align: center;">Fecha Creacion</th>
        <th style="background-color: green; color: white; text-align: left;">Fecha Login</th>
        <th style="background-color: green; color: white;">Tipo Usuario</th>
        <th style="background-color: green; color: white;">Almacen</th>
        <th style="background-color: green; color: white;">Almacen</th>
    </tr>';

$resultados = $user->dameUsuariosActivos();
for($i=0;$i<count($resultados);$i++){
	$id_usuario = $resultados[$i]["id_usuario"];

	if(($i % 2) == 0) {
		$color = ' background-color: #fff; ';
	} 
	else {
		$color = ' background-color: #eee; ';
	}

	$user->cargaDatosUsuarioId($id_usuario);
	$id_tipo = $user->id_tipo;
	$id_taller = $user->id_taller;
	$id_almacen = $user->id_almacen;

	$tipo_usuario = $user->dameNombreTipoUsuario($id_tipo);
	$tipo_usuario = $tipo_usuario["tipo"];

	$taller->cargaDatosAlmacenId($id_taller);
	$nombre_taller = $taller->nombre;
	
	$almacen->cargaDatosAlmacenId($id_almacen);
	$nombre_almacen = $almacen->nombre;

	if($nombre_taller == NULL) $nombre_taller = "-";
	if($nombre_almacen == NULL) $nombre_almacen = "-";

	$tabla .= '<tr>
				<td style="'.$color.'text-align: center;">'.$id_usuario.'</td>
				<td style="'.$color.'">'.$user->usuario.'</td>
				<td style="'.$color.'">'.$user->email.'</td>
				<td style="'.$color.'text-align: center;">'.$user->fecha_creado.'</td>
				<td style="'.$color.'text-align: left;">'.$user->fecha_login.'</td>
				<td style="'.$color.'text-align: center;">'.$tipo_usuario.'</td>
				<td style="'.$color.'text-align: center;">'.$nombre_taller.'</td>
				<td style="'.$color.'text-align: center;">'.$nombre_almacen.'</td>
			</tr>';
}

$tabla .= '</table>';

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=UsuariosSimumak.xls");
echo $tabla; 
?>

