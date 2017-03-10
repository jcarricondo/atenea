<?php 
// Este fichero genera un excel con las referencias de un componente de basicos
include("../includes/sesion.php");
include("../classes/basicos/referencia.class.php");
include("../classes/basicos/referencia_componente.class.php");
include("../classes/basicos/listado_referencias_componentes.class.php");

$db = new MySQL();
$referencia = new Referencia();
$ref = new Referencia_Componente();
// Devuelve el tipo de componente: periferico o kit
$tipo = $_GET["tipo"]; 
// Devuelve el id_componente
$id	= $_GET["id"]; 

// Tenemos que comprobar el tipo de componente. Si el componente es kit mostrará sus referencias.
// Si el componente es un periferico hay que comprobar si tiene kits.

$salida = "";

// Obtenemos las referencias del componente
$ref->dameReferenciasPorIdComponente($id);
$referencias_componente = $ref->referencias_componente;

// Si el componente es un periferico tendremos que comprobar si tienen kits y añadir sus referencias a las referencias del componente
if (/*($tipo == "cabina") or */($tipo == "periferico")){
	// Creamos un array auxiliar de las referencias del componente
	$referencias_aux = $referencias_componente;
	
	// Obtenemos ahora los kits del componente
	$ref->dameIdsKitComponente($_GET["id"]);
	$ids_kits = $ref->ids_kits;
	
	for($i=0;$i<count($ids_kits);$i++){
		// Obtenemos las referencias de ese kit 
		$ref->dameReferenciasPorIdComponente($ids_kits[$i]["id_kit"]);
		$referencias_kit = $ref->referencias_componente;
		$referencias_componente = $ref->addReferenciasKitAlComponente($referencias_kit,$referencias_componente);
	}
}

// Generamos la tabla HTML 
$table = '<table>
	<tr>
		<th style="text-align: center;">ID Ref.</th>
    	<th style="text-align: left;">Nombre</th>
        <th style="text-align: left;">Referencia Proveedor</th>
        <th style="text-align: left;">Proveedor</th>
        <th style="text-align: right;">Piezas</th>
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
        <th style="text-align: right;">Precio Pack</th>
        <th style="text-align: right;">Unidades Paquete</th>
        <th style="text-align: left;">Comentarios</th>
    </tr>';
	
	
// Por cada referencia del componente generamos la fila y codificamos los campos
for($i=0;$i<count($referencias_componente);$i++){
	// De la tabla componentes_referencias solo nos interesa el campo piezas y el id_referencia. Los demas datos los obtenemos de la tabla referencias
	$id_referencia = $referencias_componente[$i]["id_referencia"];
	$total_piezas = $referencias_componente[$i]["piezas"];
	$referencia->cargaDatosReferenciaId($id_referencia);
	
	// Tenemos que calcular el precio de la referencia 
	$unidades_paquete = $referencia->unidades;
	$pack_precio = $referencia->pack_precio;
	if (($total_piezas != 0) and ($unidades_paquete != 0)){
		$precio_referencia = ($total_piezas / $unidades_paquete) * $pack_precio;
	}
	else $precio_referencia = 0;

	// Recalculamos los paquetes
	$referencia->calculaTotalPaquetes($unidades_paquete,$total_piezas);
	$total_paquetes = $referencia->total_paquetes;
	
	
	// Preparamos la codificacion de la referencia 
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
		
	// Generamos la fila HTML de la tabla correspondiente a una referencia
	$salida .= '
	<tr>
		<td style="text-align: center;">'.$referencia->id_referencia.'</td>
		<td style="text-align: left;">'.$nombre_ref.'</td>
		<td style="text-align: left;">'.$ref_prov.'</td>
		<td style="text-align: left;">'.utf8_decode($referencia->nombre_proveedor).'</td>
		<td style="text-align: right;">'.number_format($total_piezas,2,',','.').'</td>
		<td style="text-align: right;">'.number_format($precio_referencia,2,',','.').'</td>
		<td style="text-align: right;">'.number_format($total_paquetes,2,',','.').'</td>
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
		<td style="text-align: right;">'.number_format($referencia->pack_precio,2,',','.').'</td>
		<td style="text-align: right;">'.utf8_decode($referencia->unidades).'</td>
		<td style="text-align: left;">'.$coments.'</td>
	</tr>
	';
}
$table_end = '</table>';

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=informeReferencias.xls");
echo $table.$salida.$table_end;
?>