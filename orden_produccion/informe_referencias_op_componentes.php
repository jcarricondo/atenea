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
    	<th>Componente</th>
    	<th>Kit</th>
    	<th>ID Ref.</th>
		<th>Nombre</th>
        <th>Referencia Proveedor</th>
        <th>Proveedor</th>   
        <th>Piezas</th>
        <th>Precio Paquete</th>  
		<th>Unidades Paquete</th>
		<th>Precio Unidad</th>
		<th>Precio</th>
		<th>Total Paquetes</th>
        <th>Tipo Pieza</th>
        <th>Nombre Pieza</th> 
        <th>Fabricante</th>  
        <th>Referencia Fabricante</th>
        <th>Descripci&oacute;n</th>
        <th>Nombre</th>
        <th>Valor</th>
        <th>Nombre 2</th>
        <th>Valor 2</th>
        <th>Nombre 3</th>
        <th>Valor 3</th>
        <th>Nombre 4</th>
        <th>Valor 4</th>
        <th>Nombre 5</th>
        <th>Valor 5</th>
        <th>Comentarios</th>
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
					<td>'.utf8_decode($nombre_componente).'</td>
					<td>'.utf8_decode($nombre_subcomponente).'</td>
					<td align="center">'.$referencia->id_referencia.'</td>
					<td>'.$nombre_ref.'</td>
					<td align="center">'.$ref_prov.'</td>
					<td>'.utf8_decode($referencia->nombre_proveedor).'</td>
					<td>'.number_format($total_piezas,2,',','.').'</td>
					<td>'.number_format($pack_precio,2,',','.').'</td>
					<td>'.number_format($unidades_paquete,2,',','.').'</td>
					<td>'.number_format($precio_unidad,2,',','.').'</td>
					<td align="right">'.truncateFloat($precio_referencia,2).'</td>
					<td align="right">'.truncateFloat($total_paquetes,2).'</td>
					<td align="center">'.$tipo_pieza.'</td>
					<td>'.utf8_decode($referencia->part_nombre).'</td>
					<td>'.utf8_decode($referencia->nombre_fabricante).'</td>
					<td align="center">'.$ref_fab.'</td>
					<td>'.$descrip.'</td>
					<td>'.$valor_nombre.'</td>
					<td>'.utf8_decode($referencia->part_valor_cantidad).'</td>
					<td>'.$valor_nombre2.'</td>
					<td>'.utf8_decode($referencia->part_valor_cantidad_2).'</td>
					<td>'.$valor_nombre3.'</td>
					<td>'.utf8_decode($referencia->part_valor_cantidad_3).'</td>
					<td>'.$valor_nombre4.'</td>
					<td>'.utf8_decode($referencia->part_valor_cantidad_4).'</td>
					<td>'.$valor_nombre5.'</td>
					<td>'.utf8_decode($referencia->part_valor_cantidad_5).'</td>
					<td>'.$coments.'</td>
				</tr>
				</table>';
 	}
}
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=informeReferencias.xls");
echo $table.$salida.$table_end; 
?>