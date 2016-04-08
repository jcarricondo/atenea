<?php 
// Este fichero genera un excel con los resultados de busqueda de los movimientos
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/basicos/usuario.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/material_informatico/albaran_informatico.class.php");
include("../classes/material_informatico/material_informatico.class.php");

$almacen = new Almacen();
$albaranInformatico = new AlbaranInformatico();
$materialInformatico = new MaterialInformatico();
$usuario = new Usuario();

// Obtenemos los ids_movimientos_materiales por URL
$ids_albaranes_movimientos = $_GET["ids_movimientos"];
$ids_movimientos = explode(",",$ids_albaranes_movimientos);

$table= '<table>
		<tr>
            <th style="text-align:left">NUM. SERIE</th>
            <th style="text-align:left">TIPO MATERIAL</th>
            <th style="text-align:left">ALBARAN</th>
            <th style="text-align:left">TIPO ALBARAN</th>
            <th style="text-align:left">USUARIO</th>
            <th style="text-align:left">ALMACEN</th>
            <th style="text-align:left">ORIGEN / DESTINO</th>
            <th style="text-align:left">MOTIVO</th>
            <th style="text-align:center">FECHA CREACION</th>
            <th style="text-align:center">AVERIADO</th>
    	</tr>';

// Recorremos todos los movimientos de la busqueda
for($i=0;$i<count($ids_movimientos);$i++){
    $id = $ids_movimientos[$i];

    // Cargamos los datos de ese movimiento
    $resultados_movimiento = $albaranInformatico->dameMovimientoAlbaranPorId($id);
    $id_albaran = $resultados_movimiento["id_albaran"];
    $num_serie = $resultados_movimiento["num_serie"];
    $averiado = $resultados_movimiento["averiado"];
    $id_material = $resultados_movimiento["id_material"];

    // Carga mos los datos del albaran
    $albaranInformatico->cargaDatosAlbaranId($id_albaran);
    $nombre_albaran = $albaranInformatico->nombre_albaran;
    $id_almacen = $albaranInformatico->id_almacen;    
    $id_usuario = $albaranInformatico->id_usuario;
    $tipo_albaran = $albaranInformatico->tipo_albaran;
    $origen_destino = $albaranInformatico->origen_destino;
    $motivo = $albaranInformatico->motivo;

    // Cargamos el nombre del tipo de material
    $materialInformatico->cargaDatosMaterialId($id_material);
    $id_tipo = $materialInformatico->id_tipo;

    $nombre_tipo = $materialInformatico->dameTipoMaterial($id_tipo);
    $nombre_tipo = $nombre_tipo[0]["nombre"];

    // Cargamos el nombre del usuario
    $usuario->cargaDatosUsuarioId($id_usuario);
    $nombre_usuario = $usuario->usuario;

    $fecha_creado = $albaranInformatico->fecha_creado;
    $fecha_creado = $usuario->fechaHoraSpain($fecha_creado);

    // Cargamos el nombre del almacen 
    $almacen->cargaDatosAlmacenId($id_almacen);
    $nombre_almacen = $almacen->nombre; 

    $salida .= '<tr>   
                     <td style="text-align:left">'.$num_serie.'</td>
                     <td style="text-align:left">'.$nombre_tipo.'</td>
                     <td style="text-align:left">'.utf8_decode($nombre_albaran).'</td>
                     <td style="text-align:left">'.$tipo_albaran.'</td>
                     <td style="text-align:left">'.$nombre_usuario.'</td>
                     <td style="text-align:left">'.utf8_decode($nombre_almacen).'</td>
                     <td style="text-align:left">'.utf8_decode($origen_destino).'</td>
                     <td style="text-align:left">'.$motivo.'</td>
                     <td style="text-align:center">'.$fecha_creado.'</td>
                     <td style="text-align:center">';

    if($averiado == "NO"){
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
