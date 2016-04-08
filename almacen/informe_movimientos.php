<?php 
// Este fichero genera un excel con los resultados de búsqueda de los movimientos
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/sede/sede.class.php");
include("../classes/basicos/proveedor.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/basicos/centro_logistico.class.php");
include("../classes/basicos/usuario.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/almacen/albaran.class.php");
include("../classes/almacen/listado_movimientos.class.php");

// Instancias de las clases
$sede = new Sede();
$proveedor = new Proveedor();
$ref = new Referencia();
$centroLogistico = new CentroLogistico();
$usuario = new Usuario();
$almacen = new Almacen();
$albaran = new Albaran();
$listadoMovimientos = new listadoMovimientos();

$nombre_albaran = $_SESSION["nombre_albaran_xls_almacen_movimientos"];
$tipo_albaran = $_SESSION["tipo_albaran_xls_almacen_movimientos"];
$nombre_participante = $_SESSION["nombre_participante_xls_almacen_movimientos"];
$id_usuario = $_SESSION["id_usuario_xls_almacen_movimientos"];
$tipo_motivo = $_SESSION["tipo_motivos_xls_almacen_movimientos"];
$id_ref = $_SESSION["id_ref_xls_almacen_movimientos"];
$id_almacen = $_SESSION["id_almacen_xls_almacen_movimientos"];
$fecha_desde = $_SESSION["fecha_desde_xls_almacen_movimientos"];
$fecha_hasta = $_SESSION["fecha_hasta_xls_almacen_movimientos"];
$id_tipo_participante = $_SESSION["id_tipo_participante_xls_almacen_movimientos"];
$id_participante = $_SESSION["id_participante_xls_almacen_movimientos"];
$id_sede = $_SESSION["id_sede_xls_almacen_movimientos"];

$listadoMovimientos->setValores($nombre_albaran,$tipo_albaran,$id_tipo_participante,$id_participante,$tipo_motivo,$id_usuario,$id_ref,$fecha_desde,$fecha_hasta,'',$id_almacen,$id_sede);
$listadoMovimientos->realizarConsulta();
$resultadosBusqueda = $listadoMovimientos->movimientos;

$sede->cargaDatosSedeId($id_sede);
$name_sede = $sede->nombre; 

$table= '<table>
		<tr>
            <th style="text-align:left">SEDE</th>
            <th style="text-align:left">ALMACEN</th>
			<th style="text-align:center">ID REF</th>
			<th style="text-align:left">NOMBRE REF</th>
            <th style="text-align:left">REF PROV</th>
        	<th style="text-align:left">PROVEEDOR</th>
            <th style="text-align:left">ALBARAN</th>
            <th style="text-align:center">TIPO ALBARAN</th>
            <th style="text-align:left">USUARIO</th>
            <th style="text-align:left">ORIGEN / DESTINO</th>
            <th style="text-align:center">MOTIVO</th>
    		<th style="text-align:center">FECHA CREACION</th>
    		<th style="text-align:left">UNIDADES</th>
    		<th></th>
    		<th></th>
    	</tr>';

// Recorremos todos los movimientos de la búsqueda
for($i=0;$i<count($resultadosBusqueda);$i++){
    $id_referencia = $resultadosBusqueda[$i]["id_referencia"];
	$nombre_referencia = $resultadosBusqueda[$i]["nombre_referencia"];
	$nombre_proveedor = $resultadosBusqueda[$i]["nombre_proveedor"];
	$referencia_proveedor = $resultadosBusqueda[$i]["referencia_proveedor"];
	$cantidad = $resultadosBusqueda[$i]["cantidad"];
    $metodo = $resultadosBusqueda[$i]["metodo"];
	$id_albaran = $resultadosBusqueda[$i]["id_albaran"];
    $id_almacen = $resultadosBusqueda[$i]["id_almacen"];
    $id_usuario = $resultadosBusqueda[$i]["id_usuario"];
    $fecha_creacion = $resultadosBusqueda[$i]["fecha_creado"];

    $almacen->cargaDatosAlmacenId($id_almacen);
    $nombre_almacen = utf8_decode($almacen->nombre);

	if($id_albaran != 0){
        // Cargamos los datos del albarán de la referencia
        $albaran->cargaDatosAlbaranId($id_albaran);
        $nombre_albaran = $albaran->nombre_albaran;
        $tipo_albaran = $albaran->tipo_albaran;
        $id_tipo_participante = $albaran->id_tipo_participante;
        $id_participante = $albaran->id_participante;

        // Cargar nombre del participante según si es proveedor o centro logístico
        if($id_tipo_participante == 1){
            // PROVEEDOR
            $proveedor->cargaDatosProveedorId($id_participante);
            $nombre_participante = $proveedor->nombre;
        }
        else if ($id_tipo_participante == 2){
            // CENTRO LOGISTICO
            $centroLogistico->cargaDatosCentroLogisticoId($id_participante);
            $nombre_participante = $centroLogistico->nombre;
        }
        else{
            // ERROR
        }

        $motivo = $albaran->motivo;
        if ($motivo == ""){
            $motivo = "-";
        }
    }
    else {
        $nombre_albaran = "-";
        $nombre_participante = "-";
        $motivo = "-";
    }

    // Cargamos el nombre del usuario
    $usuario->cargaDatosUsuarioId($id_usuario);
    $nombre_usuario = $usuario->usuario;

    if ($metodo == "RECEPCIONAR" || $metodo == "AJUSTE RECEPCIONAR"){
        $color_unidad = '<span style="color: green;">'.number_format($cantidad,2,",",".").'</span>';
        if($metodo == "AJUSTE RECEPCIONAR") $tipo_albaran = "AJUSTE ENTRADA";
    }
    else {
        $color_unidad = '<span style="color: red;">'.'-'.number_format($cantidad,2,",",".").'</span>';
        if($metodo == "AJUSTE DESRECEPCIONAR") $tipo_albaran = "AJUSTE SALIDA";
    }

    // Codificamos los campos de la referencia
    $nombre_referencia_codificado = '';
	$nombre_referencia = utf8_decode($nombre_referencia);
    for($m=0;$m<strlen($nombre_referencia);$m++){
		if ($nombre_referencia[$m] == '?'){
			$nombre_referencia_codificado .= '&#8364;'; 	
		}
		else {
			$nombre_referencia_codificado .= $nombre_referencia[$m]; 
		}
	}

	$nombre_proveedor_codificado = '';
	$nombre_proveedor = utf8_decode($nombre_proveedor);
	for($m=0;$m<strlen($nombre_proveedor);$m++){
		if ($nombre_proveedor[$m] == '?'){
			$nombre_proveedor_codificado .= '&#8364;'; 	
		}
		else {
			$nombre_proveedor_codificado .= $nombre_proveedor[$m]; 
		}
	}
		
	$referencia_proveedor_codificado = '';
	$referencia_proveedor = utf8_decode($referencia_proveedor);
	for($m=0;$m<strlen($referencia_proveedor);$m++){
		if ($referencia_proveedor[$m] == '?'){
			$referencia_proveedor_codificado .= '&#8364;'; 	
		}
		else {
			$referencia_proveedor_codificado .= $referencia_proveedor[$m]; 
		}
	}

	$nombre_participante_codificado = '';
	$nombre_participante = utf8_decode($nombre_participante);
	for($m=0;$m<strlen($nombre_participante);$m++){
		if ($nombre_participante[$m] == '?'){
			$nombre_participante_codificado .= '&#8364;'; 	
		}
		else {
			$nombre_participante_codificado .= $nombre_participante[$m]; 
		}
	}

	$salida .= '<tr>
                    <td style="text-align:left">'.$name_sede.'</td>
                    <td style="text-align:left">'.$nombre_almacen.'</td>
                	<td style="text-align:center">'.$id_referencia.'</td>
                    <td style="text-align:left">'.$nombre_referencia_codificado.'</td>
                    <td style="text-align:left">'.$referencia_proveedor_codificado.'</td>
                    <td style="text-align:left">'.$nombre_proveedor_codificado.'</td>
                    <td style="text-align:left">'.$nombre_albaran.'</td>
                    <td style="text-align:center">'.$tipo_albaran.'</td>
                    <td style="text-align:left">'.$nombre_usuario.'</td>
                    <td style="text-align:left">'.$nombre_participante_codificado.'</td>
                    <td style="text-align:center">'.$motivo.'</td>
                    <td style="text-align:center">'.$fecha_creacion.'</td>
                    <td style="text-align:right">'.$color_unidad.'</td>';

    $salida .= '
    			<td></td>
    			<td></td>
    			</tr>';
}
$table_end = '</table>';

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=informeMovimientos.xls");
echo $table.$salida.$table_end; 
?>
