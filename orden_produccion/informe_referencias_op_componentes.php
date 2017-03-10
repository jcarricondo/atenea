<?php 
// Este fichero genera un excel con las referencias de los componentes de una OP
include("../includes/sesion.php");
include("../classes/basicos/referencia.class.php");
include("../classes/basicos/periferico.class.php");
include("../classes/basicos/kit.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/productos/producto.class.php");
// include("../classes/basicos/cabina.class.php");

function truncateFloat($number, $digitos)
{
    $raiz = 10;
    $multiplicador = pow ($raiz,$digitos);
    $resultado = ((int)($number * $multiplicador)) / $multiplicador;
    return number_format($resultado, $digitos,",",".");

}
$id_produccion = $_GET["id"];

$db = new MySQL();
$referencia = new Referencia();
$op = new Orden_Produccion();
$producto = new Producto();
$periferico = new Periferico();
$kit = new Kit();
// $cabina = new Cabina();

$salida = "";
$salida .= '<table>
	<tr>
    	<th style="text-align: left;">Componente</th>
    	<th style="text-align: left;">Kit</th>
    	<th style="text-align: center;">ID Ref.</th>
		<th style="text-align: left;">Nombre</th>
        <th style="text-align: left;">Referencia Proveedor</th>
        <th style="text-align: left;">Proveedor</th>
        <th style="text-align: right;">Piezas</th>
        <th style="text-align: right;">Precio Paquete</th>
		<th style="text-align: right;">Unidades Paquete</th>
		<th style="text-align: right;">Precio Unidad</th>
		<th style="text-align: right;">Precio</th>
		<th style="text-align: right;">Total Paquetes</th>
        <th style="text-align: left;">Tipo Pieza</th>
        <th style="text-align: left;">Nombre Pieza</th>
        <th style="text-align: left;">Fabricante</th>
        <th style="text-align: left;">Referencia Fabricante</th>
        <th style="text-align: left;">Descripci&oacute;n</th>
        <th style="text-align: left;">Nombre</th>
        <th style="text-align: left;">Valor</th>
        <th style="text-align: left;">Nombre 2</th>
        <th style="text-align: left;">Valor 2</th>
        <th style="text-align: left;">Nombre 3</th>
        <th style="text-align: left;">Valor 3</th>
        <th style="text-align: left;">Nombre 4</th>
        <th style="text-align: left;">Valor 4</th>
        <th style="text-align: left;">Nombre 5</th>
        <th style="text-align: left;">Valor 5</th>
        <th style="text-align: left;">Comentarios</th>
    </tr>
	</table>';

$op->cargaDatosProduccionId($id_produccion);
$unidades = $op->unidades;

// Obtenemos los id_productos_componentes de la Orden de Produccion 
$componentes_produccion = $op->dameIdsProduccionComponente($id_produccion);

for($i=0;$i<count($componentes_produccion);$i++){
	$id_produccion_componente = $componentes_produccion[$i]["id_produccion_componente"];
	$array_id_componente = $op->dameIdComponentePorIdProduccionComponente($id_produccion_componente);
	$id_componente = $array_id_componente[0]["id_componente"];

	// Obtenemos el id_tipo del id_produccion_componente
	$resultados = $op->dameTipoComponente($id_componente);

	$id_tipo = $resultados["id_tipo"];

    switch ($id_tipo) {
    	case '0':
    		// REFERENCIAS LIBRES
    		$nombre_componente = "REFERENCIAS LIBRES";
    		$nombre_subcomponente = "";
    	break;
    	case '1':
        	// CABINA
			/*
            $cabina->cargaDatosCabinaId($id_componente);
			$nombre_componente = $cabina->cabina."_v".$cabina->version;
			$nombre_subcomponente = "";
			$componente_principal = $nombre_componente;
			*/
		break;
        case '2':
            // PERIFERICO
            $periferico->cargaDatosPerifericoId($id_componente);
            $nombre_componente = $periferico->periferico."_v".$periferico->version;
            $nombre_subcomponente = "";	
            $componente_principal = $nombre_componente;
        break;
        case '4':
            // INTERFAZ
			// Deja de existir en Agosto de 2016
        break;
        case '5':
            // KIT
            $kit->cargaDatosKitId($id_componente);
            $nombre_subcomponente = $kit->kit."_v".$kit->version;
            $nombre_componente = $componente_principal;
        break;
        default:
            //
        break;
    }

    // Obtenemos las referencias del id_produccion_componente
    $referencias_componente = $op->cargaDatosPorProduccionComponente($id_produccion,$id_produccion_componente);
    // $referencias_componente = $op->dameIdReferenciaPiezasPorIdProduccionComponente($id_produccion,$id_produccion_componente);

    for($j=0;$j<count($referencias_componente);$j++){
    	$id_referencia = $referencias_componente[$j]["id_referencia"];
    	$total_piezas = $referencias_componente[$j]["piezas"] * $unidades;
    	$total_piezas = round($total_piezas,2);
    	$pack_precio = $referencias_componente[$j]["pack_precio"];
    	$unidades_paquete = $referencias_componente[$j]["uds_paquete"];

    	$referencia->cargaDatosReferenciaId($id_referencia);
    	/*
    	$pack_precio = $referencia->pack_precio;
    	$unidades_paquete = $referencia->unidades;
    	*/

    	if($unidades_paquete != 0 and $total_piezas != 0){
			$precio_referencia = ($total_piezas / $unidades_paquete) * $pack_precio;
		}
		else $precio_referencia = 0;

		$referencia->calculaTotalPaquetes($unidades_paquete,$total_piezas);
		$total_paquetes = $referencia->total_paquetes;
	
		if($pack_precio != 0 and $unidades_paquete != 0) {
			$precio_unidad = $pack_precio / $unidades_paquete;
		} 
		else {
			$precio_unidad = 00;
		}

    	$nombre_ref = '';
		$nombre_referencia_codificada = utf8_decode($referencia->referencia);
		for($m=0;$m<strlen($nombre_referencia_codificada);$m++){
			if ($nombre_referencia_codificada[$m] == '?'){
				$nombre_ref .= '&#8364;'; 	
			}
			else {
				$nombre_ref .= $nombre_referencia_codificada[$m]; 
			}
		}

		$ref_prov = '';
		$ref_prov_codificada = utf8_decode($referencia->part_proveedor_referencia);
		for($m=0;$m<strlen($ref_prov_codificada);$m++){
			if ($ref_prov_codificada[$m] == '?'){
				$ref_prov .= '&#8364;'; 	
			}
			else {
				$ref_prov .= $ref_prov_codificada[$m]; 
			}
		}

		$tipo_pieza = '';
		$tipo_pieza_codificada = utf8_decode($referencia->part_tipo);
		for($m=0;$m<strlen($tipo_pieza_codificada);$m++){
			if ($tipo_pieza_codificada[$m] == '?'){
				$tipo_pieza .= '&#8364;'; 	
			}
			else {
				$tipo_pieza .= $tipo_pieza_codificada[$m]; 
			}
		}
	
		$ref_fab = '';
		$ref_fab_codificada = utf8_decode($referencia->part_fabricante_referencia);
		for($m=0;$m<strlen($ref_fab_codificada);$m++){
			if ($ref_fab_codificada[$m] == '?'){
				$ref_fab .= '&#8364;'; 	
			}
			else {
				$ref_fab .= $ref_fab_codificada[$m]; 
			}
		}
	
		$descrip = '';
		$descrip_codificada = utf8_decode($referencia->part_descripcion);
		for($m=0;$m<strlen($descrip_codificada);$m++){
			if ($descrip_codificada[$m] == '?'){
				$descrip .= '&#8364;'; 	
			}
			else {
				$descrip .= $descrip_codificada[$m]; 
			}
		}
	
		$valor_nombre = '';
		$valor_nombre_codificada = utf8_decode($referencia->part_valor_nombre);
		for($m=0;$m<strlen($valor_nombre_codificada);$m++){
			if ($valor_nombre_codificada[$m] == '?'){
				$valor_nombre .= '&#8364;'; 	
			}
			else {
				$valor_nombre .= $valor_nombre_codificada[$m]; 
			}
		}
	
		$valor_nombre2 = '';
		$valor_nombre2_codificada = utf8_decode($referencia->part_valor_nombre_2);
		for($m=0;$m<strlen($valor_nombre2_codificada);$m++){
			if ($valor_nombre2_codificada[$m] == '?'){
				$valor_nombre2 .= '&#8364;'; 	
			}
			else {
				$valor_nombre2 .= $valor_nombre2_codificada[$m]; 
			}
		}
	
		$valor_nombre3 = '';
		$valor_nombre3_codificada = utf8_decode($referencia->part_valor_nombre_3);
		for($m=0;$m<strlen($valor_nombre3_codificada);$m++){
			if ($valor_nombre3_codificada[$m] == '?'){
				$valor_nombre3 .= '&#8364;'; 	
			}
			else {
				$valor_nombre3 .= $valor_nombre3_codificada[$m]; 
			}
		}

		$valor_nombre4 = '';
		$valor_nombre4_codificada = utf8_decode($referencia->part_valor_nombre_4);
		for($m=0;$m<strlen($valor_nombre4_codificada);$m++){
			if ($valor_nombre4_codificada[$m] == '?'){
				$valor_nombre4 .= '&#8364;'; 	
			}
			else {
				$valor_nombre4 .= $valor_nombre4_codificada[$m]; 
			}
		}
	
		$valor_nombre5 = '';
		$valor_nombre5_codificada = utf8_decode($referencia->part_valor_nombre_5);
		for($m=0;$m<strlen($valor_nombre5_codificada);$m++){
			if ($valor_nombre5_codificada[$m] == '?'){
				$valor_nombre5 .= '&#8364;'; 	
			}
			else {
				$valor_nombre5 .= $valor_nombre5_codificada[$m]; 
			}
		}
	
		$coments = '';
		$coments_codificada = utf8_decode($referencia->comentarios);
		for($m=0;$m<strlen($coments_codificada);$m++){
			if ($coments_codificada[$m] == '?'){
				$coments .= '&#8364;'; 	
			}
			else {
				$coments .= $coments_codificada[$m]; 
			}
		}

	    $salida .= '
				<table>
				<tr>
					<td style="text-align: left;">'.utf8_decode($nombre_componente).'</td>
					<td style="text-align: left;">'.utf8_decode($nombre_subcomponente).'</td>
					<td style="text-align: center;">'.$referencia->id_referencia.'</td>
					<td style="text-align: left;">'.$nombre_ref.'</td>
					<td style="text-align: left;">'.$ref_prov.'</td>
					<td style="text-align: left;">'.utf8_decode($referencia->nombre_proveedor).'</td>
					<td style="text-align: right;">'.number_format($total_piezas,2,',','.').'</td>
					<td style="text-align: right;">'.number_format($pack_precio,2,',','.').'</td>
					<td style="text-align: right;">'.number_format($unidades_paquete,2,',','.').'</td>
					<td style="text-align: right;">'.number_format($precio_unidad,2,',','.').'</td>
					<td style="text-align: right;">'.truncateFloat($precio_referencia,2).'</td>
					<td style="text-align: right;">'.truncateFloat($total_paquetes,2).'</td>
					<td style="text-align: left;">'.$tipo_pieza.'</td>
					<td style="text-align: left;">'.utf8_decode($referencia->part_nombre).'</td>
					<td style="text-align: left;">'.utf8_decode($referencia->nombre_fabricante).'</td>
					<td style="text-align: left;">'.$ref_fab.'</td>
					<td style="text-align: left;">'.$descrip.'</td>
					<td style="text-align: left;">'.$valor_nombre.'</td>
					<td style="text-align: left;">'.utf8_decode($referencia->part_valor_cantidad).'</td>
					<td style="text-align: left;">'.$valor_nombre2.'</td>
					<td style="text-align: left;">'.utf8_decode($referencia->part_valor_cantidad_2).'</td>
					<td style="text-align: left;">'.$valor_nombre3.'</td>
					<td style="text-align: left;">'.utf8_decode($referencia->part_valor_cantidad_3).'</td>
					<td style="text-align: left;">'.$valor_nombre4.'</td>
					<td style="text-align: left;">'.utf8_decode($referencia->part_valor_cantidad_4).'</td>
					<td style="text-align: left;">'.$valor_nombre5.'</td>
					<td style="text-align: left;">'.utf8_decode($referencia->part_valor_cantidad_5).'</td>
					<td style="text-align: left;">'.$coments.'</td>
				</tr>
				</table>';
 	}
}
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=informeReferencias.xls");
echo $table.$salida.$table_end; 
?>