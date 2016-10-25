<?php 
// Script para obtener todos los componentes y referencias del último SIMESTRUCK creado 
// Los componentes y referencias obtenidos de ATENEA SIMUMAK se copiarán o actualizarán en ATENEA TORO
// Excel por componentes del SIMESTRUCK 
// Excel de todas las referencias agrupadas utilizadas en el SIMESTRUCK 

include("../classes/mysql.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/kint/Kint.class.php");

$db = new MySQL();
$ref = new Referencia();

// El ultimo SIMESTRUCK creado 
// Comprobar último SIMESTRUCK de SIMUMAK creado
$id_produccion = 167;
$id_producto = 1381;

// Obtenemos los componentes que forman el producto 
$consulta = sprintf("select * from orden_produccion_componentes where activo=1 and id_produccion_componente in
				(select id_produccion_componente from orden_produccion_referencias where id_produccion=%s)",
	$db->makeValue($id_produccion,"int"));	
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$resultados_simestruck = $db->getResultados();

d($resultados_simestruck);

$tabla = "";

// Obtenemos las referencias de cada componente
for($i=0;$i<count($resultados_simestruck);$i++){
	$id_componente = $resultados_simestruck[$i]["id_componente"];
	$num_serie = $resultados_simestruck[$i]["num_serie"];

	// Obtenemos el nombre del componente y sus referencias 
	$consulta = sprintf("select * from componentes where activo=1 and id_componente=%s",
		$db->makeValue($id_componente,"int"));
	$db->setConsulta($consulta);
	$db->ejecutarConsulta();
	$resultados_datos_componente = $db->getPrimerResultado();

	d($resultados_datos_componente);	

	$nombre = $resultados_datos_componente["nombre"];
	$version = $resultados_datos_componente["version"];
	$referencia_comp = $resultados_datos_componente["referencia"];
	$descripcion_comp = $resultados_datos_componente["descripcion"];
	$id_tipo_comp = $resultados_datos_componente["id_tipo"]; 
	$estado_comp = $resultados_datos_componente["estado"];


	$tabla .= "<table>
				<tr style='font-weight:bold; background: #F9FCAA;'>
					<th style='text-align: center;'>ID_COMPONENTE</th>	
					<th style='text-align: center;'>NOMBRE</th>
					<th style='text-align: center;'>VERSION</th>
				</tr>
				<tr>
					<td style='text-align: center;'>".$id_componente."</td>
					<td style='text-align: center;'>".$nombre."</td>
					<td style='text-align: center;'>".$version."</td>	
				</tr>		
			</table>";

	// Obtenemos las referencias del componente
	$consulta = sprintf("select cr.id_referencia, ref.referencia, cr.uds_paquete, cr.piezas, cr.total_paquetes, cr.pack_precio from componentes_referencias as cr inner join referencias as ref on (cr.id_referencia = ref.id_referencia) where cr.activo=1 and cr.id_componente=%s order by cr.id_referencia",
		$db->makeValue($id_componente,"int"));
	$db->setConsulta($consulta);
	$db->ejecutarConsulta();
	$resultados_refs_componente = $db->getResultados();	

	d($resultados_refs_componente);

	$tabla .= "<table>
				<tr style='background: #2998cc'>
					<th style='text-align: center;'>ID_REF</th>	
					<th style='text-align: center;'>REFERENCIA</th>
					<th style='text-align: center;'>UNIDADES_PAQ</th>
					<th style='text-align: center;'>PIEZAS</th>
					<th style='text-align: center;'>TOTAL_PAQ</th>
					<th style='text-align: center;'>PACK_PRECIO</th>
				</tr>";

	for($j=0;$j<count($resultados_refs_componente);$j++){
		if($resultados_refs_componente[$j] != NULL){	
			$id_referencia = $resultados_refs_componente[$j]["id_referencia"];
			$nombre_ref = $resultados_refs_componente[$j]["referencia"];
			$uds_paquete = $resultados_refs_componente[$j]["uds_paquete"];
			$piezas = $resultados_refs_componente[$j]["piezas"];
			$total_paquetes = $resultados_refs_componente[$j]["total_paquetes"];
			$pack_precio = $resultados_refs_componente[$j]["pack_precio"];

			if (($j % 2) == 0){
				$color = " background:#fff;";
			}
			else {
				$color = " background:#eee;";
			}	

			$tabla .= "<tr style='".$color."'>
						<td style='text-align: center;'>".$id_referencia."</td>	
						<td style='text-align: center;'>".$nombre_ref."</td>
						<td style='text-align: center;'>".$uds_paquete."</td>
						<td style='text-align: center;'>".$piezas."</td>
						<td style='text-align: center;'>".$total_paquetes."</td>
						<td style='text-align: center;'>".$pack_precio."</td>
					</tr>";		


			
			$array_refs_comp[] = $id_referencia;

			// Vamos a guardar todos los id_referencia utilizados en los componentes para poder obtener los datos de las referencias
			// Las cotejaremos con los de la BBDD de TORO
			if(!in_array($id_referencia,$array_total_referencias)){
				$array_total_referencias[] = $id_referencia;
				sort($array_total_referencias);
			}

			// Comprobamos que los datos son correctos
			if($pack_precio == NULL) $pack_precio = 0;
			if($uds_paquete == NULL) $uds_paquete = 1;

			// Insertamos las referencias de los componentes en la tabla auxiliar
			// Esta tabla la utilizaremos para actualizar o crear los componentes en TORO
			$consulta = sprintf("insert into zzz_componentes_referencias (id_componente,id_referencia,uds_paquete,piezas,total_paquetes,pack_precio,fecha_creado,activo)
									value(%s,%s,%s,%s,%s,%s,current_timestamp,1)",
				$db->makeValue($id_componente, "int"),
				$db->makeValue($id_referencia, "int"),
				$db->makeValue($uds_paquete, "int"),
				$db->makeValue($piezas, "float"),
				$db->makeValue($total_paquetes, "int"),
				$db->makeValue($pack_precio, "float"));
			$db->setConsulta($consulta);
			if($db->ejecutarSoloConsulta()){
				echo "<span style='color: green;'>Referencia [".$id_referencia."] insertada correctamente en zzz_componentes_referencias</span><br/>";
			}	
			else {
				echo "<span style='color: red;'>ERROR al insertar la referencia [".$id_referencia."] de un componente</span><br/>";
			}
		}	
	}
	$tabla .= "</table><br/><br/>";

	// Ordenamos el array
	sort($array_refs_comp);
	// Comprobamos que se generan bien los arrays de referencias de los componentes
	d($array_refs_comp); 
	// Limpiamos el array para el siguiente componente
	unset($array_refs_comp); 
}

// Guardamos las referencias agrupadas utilizadas en el SIMESTRUCK y ordernadas por id_ref
d(count($array_total_referencias));

for($i=0;$i<count($array_total_referencias);$i++){
	$id_referencia = $array_total_referencias[$i];

	// Cargamos los datos de las referencia 
	$ref->cargaDatosReferenciaId($id_referencia);
	$referencia = $ref->referencia;
	$fabricante = $ref->fabricante;
	$proveedor = $ref->proveedor;
	$part_nombre = $ref->part_nombre;
	$part_tipo = $ref->part_tipo;
	$part_proveedor_referencia = $ref->part_proveedor_referencia;
	$part_fabricante_referencia = $ref->part_fabricante_referencia;
	$part_valor_nombre = $ref->part_valor_nombre;
	$part_valor_cantidad = $ref->part_valor_cantidad;
	$part_valor_nombre_2 = $ref->part_valor_nombre_2;
	$part_valor_cantidad_2 = $ref->part_valor_cantidad_2;
	$part_valor_nombre_3 = $ref->part_valor_nombre_3;
	$part_valor_cantidad_3 = $ref->part_valor_cantidad_3;
	$part_valor_nombre_4 = $ref->part_valor_nombre_4;
	$part_valor_cantidad_4 = $ref->part_valor_cantidad_4;
	$part_valor_nombre_5 = $ref->part_valor_nombre_5;
	$part_valor_cantidad_5 = $ref->part_valor_cantidad_5;
	$pack_precio = $ref->pack_precio;
	$unidades = $ref->unidades;
	$nombre_archivo = $ref->nombre_archivo;
	$comentarios = $ref->comentarios;

	/*
	d($id_referencia);
	d($referencia);
	d($fabricante);
	d($proveedor);
	d($part_nombre);
	d($part_tipo);
	d($part_proveedor_referencia);
	d($part_fabricante_referencia);
	d($part_valor_nombre);
	d($part_valor_cantidad);
	d($part_valor_nombre_2);
	d($part_valor_cantidad_2);
	d($part_valor_nombre_3);
	d($part_valor_cantidad_4);
	d($part_valor_nombre_5);
	d($part_valor_cantidad_5);
	d($pack_precio);
	d($unidades);
	d($nombre_archivo);
	d($comentarios);
	*/

	// Insertamos las referencias utilizadas en la tabla auxiliar
	$consulta = sprintf("insert into zzz_referencias (id_referencia,referencia,id_proveedor,id_fabricante,part_tipo,part_nombre,part_fabricante_referencia,part_proveedor_referencia,
							part_valor_nombre,part_valor_cantidad,part_valor_nombre_2,part_valor_cantidad_2,part_valor_nombre_3,part_valor_cantidad_3,part_valor_nombre_4,
							part_valor_cantidad_4,part_valor_nombre_5,part_valor_cantidad_5,pack_precio,unidades,comentarios,fecha_creado,activo) 
							value (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,current_timestamp,1)",
    	$db->makeValue($id_referencia, "int"),
    	$db->makeValue($referencia, "text"),
		$db->makeValue($proveedor, "int"),
		$db->makeValue($fabricante, "int"),
		$db->makeValue($part_tipo, "text"),
		$db->makeValue($part_nombre, "text"),
		$db->makeValue($part_fabricante_referencia, "text"),
		$db->makeValue($part_proveedor_referencia, "text"),
		$db->makeValue($part_valor_nombre, "text"),
		$db->makeValue($part_valor_cantidad, "text"),
		$db->makeValue($part_valor_nombre_2, "text"),
		$db->makeValue($part_valor_cantidad_2, "text"),
		$db->makeValue($part_valor_nombre_3, "text"),
		$db->makeValue($part_valor_cantidad_3, "text"),
		$db->makeValue($part_valor_nombre_4, "text"),
		$db->makeValue($part_valor_cantidad_4, "text"),
		$db->makeValue($part_valor_nombre_5, "text"),
		$db->makeValue($part_valor_cantidad_5, "text"),
		$db->makeValue($pack_precio, "float"),
		$db->makeValue($unidades, "int"),
		$db->makeValue($comentarios, "text"));
	$db->setConsulta($consulta);
	if($db->ejecutarSoloConsulta()) {
		echo "<span style='color: green;'>Se ha guardado la referencia [".$id_referencia."] correctamente en zzz_referencias</span><br/>";	
	}
	else {
		echo "<span style='color: red;'>ERROR al insertar la referencia [".$id_referencia."]</span><br/>";
	}
}

/*
echo "<br/>"; echo "<br/>";
echo "REFERENCIAS UTILIZADAS EN COMPONENTES"; echo "<br/>";
print_r($array_total_referencias); 
*/


/*
// Generamos un excel con todas las referencias de los componentes de SIMESTRUCK de SIMUMAK para comprobar si existen las mismas referencias en TORO
// Generamos la tabla HTML 
$table = '<table>
	<tr>
		<th>ID Ref.</th>
    	<th>Nombre</th>
        <th>Id Proveedor</th>
        <th>Referencia Proveedor</th>
        <th>Part tipo</th>
        <th>Part Nombre</th>
        <th>Pack Precio</th>
        <th>Uds Paquete</th>
    </tr>';

for($i=0;$i<count($array_total_referencias);$i++){
	$id_referencia = $array_total_referencias[$i];

	$ref->cargaDatosReferenciaId($id_referencia);

	$id_proveedor = $ref->proveedor;
	$part_proveedor_referencia = $ref->part_proveedor_referencia;
	$part_tipo = $ref->part_tipo;
	$part_nombre = $ref->part_nombre;
	$pack_precio = $ref->pack_precio;
	$uds_paquete = $ref->unidades;

	// CODIFICACION
	$nombre_ref = '';
	$nombre_referencia_codificada = utf8_decode($ref->referencia);
	for($m=0;$m<strlen($nombre_referencia_codificada);$m++){
		if ($nombre_referencia_codificada[$m] == '?'){
			$nombre_ref .= '&#8364;'; 	
		}
		else {
			$nombre_ref .= $nombre_referencia_codificada[$m]; 
		}
	}

	$ref_prov = '';
	$ref_prov_codificada = utf8_decode($part_proveedor_referencia);
	for($m=0;$m<strlen($ref_prov_codificada);$m++){
		if ($ref_prov_codificada[$m] == '?'){
			$ref_prov .= '&#8364;'; 	
		}
		else {
			$ref_prov .= $ref_prov_codificada[$m]; 
		}
	}

	$tipo_pieza = '';
	$tipo_pieza_codificada = utf8_decode($part_tipo);
	for($m=0;$m<strlen($tipo_pieza_codificada);$m++){
		if ($tipo_pieza_codificada[$m] == '?'){
			$tipo_pieza .= '&#8364;'; 	
		}
		else {
			$tipo_pieza .= $tipo_pieza_codificada[$m]; 
		}
	}

	$nombre_pieza = '';
	$nombre_pieza_codificada = utf8_decode($part_nombre);
	for($m=0;$m<strlen($nombre_pieza_codificada);$m++){
		if ($nombre_pieza_codificada[$m] == '?'){
			$nombre_pieza .= '&#8364;'; 	
		}
		else {
			$nombre_pieza .= $nombre_pieza_codificada[$m]; 
		}
	}

	// Generamos la fila HTML de la tabla correspondiente a una referencia
	$salida .= '
	<tr>
		<td align="center">'.$id_referencia.'</td>
		<td>'.$nombre_ref.'</td>
		<td align="center">'.$id_proveedor.'</td>
		<td align="center">'.$ref_prov.'</td>
		<td align="center">'.$part_tipo.'</td>
		<td align="center">'.$part_nombre.'</td>
		<td>'.$pack_precio.'</td>
		<td>'.$uds_paquete.'</td>
	</tr>
	';
}
$table_end = '</table>';
*/

/*
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=informeReferencias.xls");
//echo $table.$salida.$table_end; 
echo $tabla; 
*/
?>