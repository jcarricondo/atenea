<?php 
//Este fichero genera un excel con las referencias del almacen
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/basicos/referencia.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/almacen/recepcion_material.class.php");
include("../classes/sede/sede.class.php");
include("../classes/almacen/listado_referencias_almacen.class.php");

$oprod = new Orden_Produccion();
$ref = new Referencia();
$almacen = new Almacen();
$ref = new Referencia();
$rm = new RecepcionMaterial();
$rs = new RecepcionMaterial();
$sede = new Sede();
$referencias = new listadoReferenciasAlmacen();

$metodo = $_GET["metodo"];

if($metodo == 0) {
    // LISTADO ALMACEN
    $busqueda_magica = $_SESSION["busqueda_magica_xls_almacen_material"];
    $orden_produccion = $_SESSION["orden_produccion_xls_almacen_material"];
    $orden_compra = $_SESSION["orden_compra_xls_almacen_material"];
    $proveedor = $_SESSION["proveedor_xls_almacen_material"];
    $id_ref = $_SESSION["id_ref_xls_almacen_material"];
    $id_almacen = $_SESSION["id_almacen_xls_almacen_material"];
    $id_sede = $_SESSION["id_sede_xls_almacen_material"];
}
else {
    // AJUSTE ALMACEN
    $busqueda_magica = $_SESSION["busqueda_magica_xls_ajuste_almacen"];
    $orden_produccion = $_SESSION["orden_produccion_xls_ajuste_almacen"];
    $orden_compra = $_SESSION["orden_compra_xls_ajuste_almacen"];
    $proveedor = $_SESSION["proveedor_xls_ajuste_almacen"];
    $id_ref = $_SESSION["id_ref_xls_ajuste_almacen"];
    $id_almacen = $_SESSION["id_almacen_xls_ajuste_almacen"];
    $id_sede = $_SESSION["id_sede_xls_ajuste_almacen"];
}

$referencias->setValores($busqueda_magica,$orden_produccion,$orden_compra,$proveedor,$id_ref,'',$id_almacen,$id_sede);
$referencias->realizarConsulta();
$resultadosBusqueda = $referencias->referencias;

$sede->cargaDatosSedeId($id_sede);
$name_sede = $sede->nombre; 

echo '<table>
		<tr>
			<th>Sede</th>
			<th>Almacen</th>
	    	<th>ID Ref.</th>
	        <th>Nombre</th>
			<th>Referencia Proveedor</th>
			<th>Proveedor</th>  
			<th>Piezas Pedidas</th>
	        <th>Piezas Recibidas</th>
			<th>Piezas Pendientes</th>
			<th>Piezas Usadas</th>
			<th>Piezas Disponibles</th>
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
			<th>Precio Pack</th>
			<th>Unidades Paquete</th>
			<th>Comentarios</th> 
        </tr>';

$piezas_totales_referencia = 0; 
$piezas_recibidas_referencia = 0; 
$piezas_pendientes_referencia = 0; 
$piezas_usadas_referencia = 0;
$piezas_disponibles_referencia = 0;

// Obtenemos los almacenes de esa sede
$res_almacenes = $sede->dameAlmacenesSede($id_sede);
// Si no se filtro por ningun almacenes preparamos los almacenes de la sede.
if($id_almacen != "") $ids_almacenes[0]["id_almacen"] = $id_almacen;
else $ids_almacenes = $res_almacenes;

// Se cargan los datos de las referencias de la busqueda según su identificador
for($i=0;$i<count($resultadosBusqueda);$i++) {
    $ref->cargaDatosReferenciaId($resultadosBusqueda[$i]["id_referencia"]);

	$ref_prov = '';
	$ref_prov_codificada = utf8_decode($ref->part_proveedor_referencia);
	
	// Codificamos los resultados
	for($m=0;$m<strlen($ref_prov_codificada);$m++){
		if ($ref_prov_codificada[$m] == '?'){
			$ref_prov .= '&#8364;'; 	
		}
		else {
			$ref_prov .= $ref_prov_codificada[$m]; 
		}
	}
	$tipo_pieza = '';
	$tipo_pieza_codificada = utf8_decode($ref->part_tipo);
	for($m=0;$m<strlen($tipo_pieza_codificada);$m++){
		if ($tipo_pieza_codificada[$m] == '?'){
			$tipo_pieza .= '&#8364;'; 	
		}
		else {
			$tipo_pieza .= $tipo_pieza_codificada[$m]; 
		}
	}
	$ref_fab = '';
	$ref_fab_codificada = utf8_decode($ref->part_fabricante_referencia);
	for($m=0;$m<strlen($ref_fab_codificada);$m++){
		if ($ref_fab_codificada[$m] == '?'){
			$ref_fab .= '&#8364;'; 	
		}
		else {
			$ref_fab .= $ref_fab_codificada[$m]; 
		}
	}
	$descrip = '';
	$descrip_codificada = utf8_decode($ref->part_descripcion);
	for($m=0;$m<strlen($descrip_codificada);$m++){
		if ($descrip_codificada[$m] == '?'){
			$descrip .= '&#8364;'; 	
		}
		else {
			$descrip .= $descrip_codificada[$m]; 
		}
	}
	$valor_nombre = '';
	$valor_nombre_codificada = utf8_decode($ref->part_valor_nombre);
	for($m=0;$m<strlen($valor_nombre_codificada);$m++){
		if ($valor_nombre_codificada[$m] == '?'){
			$valor_nombre .= '&#8364;'; 	
		}
		else {
			$valor_nombre .= $valor_nombre_codificada[$m]; 
		}
	}
	$valor_nombre2 = '';
	$valor_nombre2_codificada = utf8_decode($ref->part_valor_nombre_2);
	for($m=0;$m<strlen($valor_nombre2_codificada);$m++){
		if ($valor_nombre2_codificada[$m] == '?'){
			$valor_nombre2 .= '&#8364;'; 	
		}
		else {
			$valor_nombre2 .= $valor_nombre2_codificada[$m]; 
		}
	}
	$valor_nombre3 = '';
	$valor_nombre3_codificada = utf8_decode($ref->part_valor_nombre_3);
	for($m=0;$m<strlen($valor_nombre3_codificada);$m++){
		if ($valor_nombre3_codificada[$m] == '?'){
			$valor_nombre3 .= '&#8364;'; 	
		}
		else {
			$valor_nombre3 .= $valor_nombre3_codificada[$m]; 
		}
	}
	$valor_nombre4 = '';
	$valor_nombre4_codificada = utf8_decode($ref->part_valor_nombre_4);
	for($m=0;$m<strlen($valor_nombre4_codificada);$m++){
		if ($valor_nombre4_codificada[$m] == '?'){
			$valor_nombre4 .= '&#8364;'; 	
		}
		else {
			$valor_nombre4 .= $valor_nombre4_codificada[$m]; 
		}
	}
	$valor_nombre5 = '';
	$valor_nombre5_codificada = utf8_decode($ref->part_valor_nombre_5);
	for($m=0;$m<strlen($valor_nombre5_codificada);$m++){
		if ($valor_nombre5_codificada[$m] == '?'){
			$valor_nombre5 .= '&#8364;'; 	
		}
		else {
			$valor_nombre5 .= $valor_nombre5_codificada[$m]; 
		}
	}
	$coments = '';
	$coments_codificada = utf8_decode($ref->comentarios);
	for($m=0;$m<strlen($coments_codificada);$m++){
		if ($coments_codificada[$m] == '?'){
			$coments .= '&#8364;'; 	
		}
		else {
			$coments .= $coments_codificada[$m]; 
		}
	}

    for($alm=0;$alm<count($ids_almacenes);$alm++){
        // Cargamos el nombre del almacen
        $id_almacen_tabla = $ids_almacenes[$alm]["id_almacen"];
        $almacen->cargaDatosAlmacenId($id_almacen_tabla);
        $nombre_almacen = $almacen->nombre;

        // Obtenemos las OP iniciadas de las que forma parte la referencia
        $oprod->dameOPIniciadasReferencia($ref->id_referencia,$id_sede);
        $ids_produccion = $oprod->ids_produccion;

        // Si se filtra por STOCK, se muestra sólo aquellos que tengan piezas
        $piezas_stock = $rs->damePiezasReferenciaStock($ref->id_referencia,$id_almacen_tabla);
        $muestro_almacen = !($piezas_stock == NULL && $orden_produccion[0] == "0");

        if($muestro_almacen) {
            if($orden_produccion != NULL and $orden_produccion[0] != ""){
                // Mostramos sólo las OP iniciadas que pertenecen a la referencia y han sido seleccionadas en el buscador
                for($j=0;$j<count($orden_produccion);$j++){
                    $id_produccion_sel = $orden_produccion[$j];

                    if($id_produccion_sel != 0){
                        for($k=0;$k<count($ids_produccion);$k++){
                            $id_produccion_ini = $ids_produccion[$k]["id_produccion"];

                            // Si coinciden las OP seleccionadas con las OP iniciadas mostramos el registro
                            if($id_produccion_sel == $id_produccion_ini){
                                $oprod->cargaDatosProduccionId($id_produccion_sel);

                                $registro_ocr = $rm->dameRegistroOCR($id_produccion_sel,$ref->id_referencia);
                                $piezas_totales = $registro_ocr["total_piezas"];
                                $piezas_recibidas = $registro_ocr["piezas_recibidas"];
                                $piezas_pendientes = $piezas_totales - $piezas_recibidas;
                                $piezas_usadas = $registro_ocr["piezas_usadas"];
                                $piezas_disponibles = $piezas_recibidas - $piezas_usadas;

                                $piezas_totales_referencia = $piezas_totales_referencia + $piezas_totales;
                                $piezas_recibidas_referencia = $piezas_recibidas_referencia + $piezas_recibidas;
                                $piezas_pendientes_referencia = $piezas_pendientes_referencia + $piezas_pendientes;
                                $piezas_usadas_referencia = $piezas_usadas_referencia + $piezas_usadas;
                                $piezas_disponibles_referencia = $piezas_disponibles_referencia + $piezas_disponibles;
                            }
                        }
                    }
                    else {
                        $piezas_recibidas = 0;
                        $piezas_disponibles = 0;

                        // Después de cargar las OP mostramos STOCK
                        // Tenemos que ver si esa referencia tiene piezas en stock
                        $piezas_stock = $rs->damePiezasReferenciaStock($ref->id_referencia,$id_almacen_tabla);

                        if($piezas_stock != NULL){
                            $piezas_recibidas = $piezas_stock;
                            $piezas_recibidas_referencia = $piezas_recibidas_referencia + $piezas_recibidas;
                            $piezas_disponibles = $piezas_recibidas;
                            $piezas_disponibles_referencia = $piezas_disponibles_referencia + $piezas_disponibles;
                        }
                    }
                }

                // Tenemos que calcular el precio de la referencia
                $unidades_paquete = $ref->unidades;
                $pack_precio = $ref->pack_precio;
                if (($piezas_totales_referencia != 0) and ($unidades_paquete != 0)){
                    $coste = ($piezas_totales_referencia / $unidades_paquete) * $pack_precio;
                }
                else $coste = 0;

                // Recalculamos los paquetes
                $ref->calculaTotalPaquetes($unidades_paquete,$piezas_totales_referencia);
                $total_paquetes = $ref->total_paquetes;

                $salida .= '<tr>
                            <td>'.$name_sede.'</td>
                            <td>'.utf8_decode($nombre_almacen).'</td>
                            <td align="center">'.$ref->id_referencia.'</td>
                            <td>'.utf8_decode($ref->referencia).'</td>
                            <td align="center">'.$ref_prov.'</td>
                            <td>'.utf8_decode($ref->nombre_proveedor).'</td>
                            <td>'.number_format($piezas_totales_referencia,2,',','.').'</td>
                            <td>'.number_format($piezas_recibidas_referencia,2,',','.').'</td>
                            <td>'.number_format($piezas_pendientes_referencia,2,',','.').'</td>
                            <td>'.number_format($piezas_usadas_referencia,2,',','.').'</td>
                            <td>'.number_format($piezas_disponibles_referencia,2,',','.').'</td>
                            <td align="right">'.number_format($coste,2,',','.').'</td>
                            <td align="right">'.number_format($total_paquetes,2,',','.').'</td>
                            <td align="center">'.$tipo_pieza.'</td>
                            <td>'.utf8_decode($ref->part_nombre).'</td>
                            <td>'.utf8_decode($ref->nombre_fabricante).'</td>
                            <td align="center">'.$ref_fab.'</td>
                            <td>'.$descrip.'</td>
                            <td>'.$valor_nombre.'</td>
                            <td>'.utf8_decode($ref->part_valor_cantidad).'</td>
                            <td>'.$valor_nombre2.'</td>
                            <td>'.utf8_decode($ref->part_valor_cantidad_2).'</td>
                            <td>'.$valor_nombre3.'</td>
                            <td>'.utf8_decode($ref->part_valor_cantidad_3).'</td>
                            <td>'.$valor_nombre4.'</td>
                            <td>'.utf8_decode($ref->part_valor_cantidad_4).'</td>
                            <td>'.$valor_nombre5.'</td>
                            <td>'.utf8_decode($ref->part_valor_cantidad_5).'</td>
                            <td align="right">'.number_format($ref->pack_precio,2,',','.').'</td>
                            <td>'.utf8_decode($ref->unidades).'</td>
                            <td>'.$coments.'</td>
                        </tr>';

                $piezas_totales_referencia=0;
                $piezas_recibidas_referencia=0;
                $piezas_pendientes_referencia=0;
                $piezas_usadas_referencia=0;
                $piezas_disponibles_referencia=0;
            }
            else {
                // Por cada referencia mostraremos todas las OP a las que pertenezca y STOCK
                for($k=0;$k<count($ids_produccion);$k++){
                    $id_produccion_ini = $ids_produccion[$k]["id_produccion"];
                    // Si coinciden las OP seleccionadas con las OP iniciadas mostramos el registro
                    $oprod->cargaDatosProduccionId($id_produccion_ini);

                    $registro_ocr = $rm->dameRegistroOCR($id_produccion_ini,$ref->id_referencia);
                    $piezas_totales = $registro_ocr["total_piezas"];
                    $piezas_recibidas = $registro_ocr["piezas_recibidas"];
                    $piezas_pendientes = $piezas_totales - $piezas_recibidas;
                    $piezas_usadas = $registro_ocr["piezas_usadas"];
                    $piezas_disponibles = $piezas_recibidas - $piezas_usadas;

                    $piezas_totales_referencia = $piezas_totales_referencia + $piezas_totales;
                    $piezas_recibidas_referencia = $piezas_recibidas_referencia + $piezas_recibidas;
                    $piezas_pendientes_referencia = $piezas_pendientes_referencia + $piezas_pendientes;
                    $piezas_usadas_referencia = $piezas_usadas_referencia + $piezas_usadas;
                    $piezas_disponibles_referencia = $piezas_disponibles_referencia + $piezas_disponibles;
                }

                // Después de cargar las OP mostramos STOCK
                // Tenemos que ver si esa referencia tiene piezas en stock
                $piezas_stock = $rs->damePiezasReferenciaStock($ref->id_referencia,$id_almacen_tabla);

                if($piezas_stock != NULL){
                    $piezas_recibidas = $piezas_stock;
                    $piezas_recibidas_referencia = $piezas_recibidas_referencia + $piezas_recibidas;
                    $piezas_disponibles = $piezas_recibidas;
                    $piezas_disponibles_referencia = $piezas_disponibles_referencia + $piezas_disponibles;
                }
                else{
                    $piezas_totales = "-";
                    $piezas_disponibles = "-";
                }

                // Tenemos que calcular el precio de la referencia
                $unidades_paquete = $ref->unidades;
                $pack_precio = $ref->pack_precio;
                if (($piezas_totales_referencia != 0) and ($unidades_paquete != 0)){
                    $coste = ($piezas_totales_referencia / $unidades_paquete) * $pack_precio;
                }
                else $coste = 0;

                // Recalculamos los paquetes
                $ref->calculaTotalPaquetes($unidades_paquete,$piezas_totales_referencia);
                $total_paquetes = $ref->total_paquetes;

                $salida .= '<tr>
                            <td>'.$name_sede.'</td>
                            <td>'.utf8_decode($nombre_almacen).'</td>
                            <td align="center">'.$ref->id_referencia.'</td>
                            <td>'.$ref->referencia.'</td>
                            <td align="center">'.$ref_prov.'</td>
                            <td>'.utf8_decode($ref->nombre_proveedor).'</td>
                            <td>'.number_format($piezas_totales_referencia,2,',','.').'</td>
                            <td>'.number_format($piezas_recibidas_referencia,2,',','.').'</td>
                            <td>'.number_format($piezas_pendientes_referencia,2,',','.').'</td>
                            <td>'.number_format($piezas_usadas_referencia,2,',','.').'</td>
                            <td>'.number_format($piezas_disponibles_referencia,2,',','.').'</td>
                            <td align="right">'.number_format($coste,2,',','.').'</td>
                            <td align="right">'.number_format($total_paquetes,2,',','.').'</td>
                            <td align="center">'.$tipo_pieza.'</td>
                            <td>'.utf8_decode($ref->part_nombre).'</td>
                            <td>'.utf8_decode($ref->nombre_fabricante).'</td>
                            <td align="center">'.$ref_fab.'</td>
                            <td>'.$descrip.'</td>
                            <td>'.$valor_nombre.'</td>
                            <td>'.utf8_decode($ref->part_valor_cantidad).'</td>
                            <td>'.$valor_nombre2.'</td>
                            <td>'.utf8_decode($ref->part_valor_cantidad_2).'</td>
                            <td>'.$valor_nombre3.'</td>
                            <td>'.utf8_decode($ref->part_valor_cantidad_3).'</td>
                            <td>'.$valor_nombre4.'</td>
                            <td>'.utf8_decode($ref->part_valor_cantidad_4).'</td>
                            <td>'.$valor_nombre5.'</td>
                            <td>'.utf8_decode($ref->part_valor_cantidad_5).'</td>
                            <td align="right">'.number_format($ref->pack_precio,2,',','.').'</td>
                            <td>'.utf8_decode($ref->unidades).'</td>
                            <td>'.$coments.'</td>
                        </tr>';

                $piezas_totales=0;
                $piezas_recibidas=0;
                $piezas_pendientes=0;
                $piezas_usadas=0;
                $piezas_disponibles=0;

                $piezas_totales_referencia=0;
                $piezas_recibidas_referencia=0;
                $piezas_pendientes_referencia=0;
                $piezas_usadas_referencia=0;
                $piezas_disponibles_referencia=0;
            }
        }
    }
}
$table_end = '</table>';
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=informeReferencias.xls");
echo $table.$salida.$table_end;
?>
