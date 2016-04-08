<?php 
// Script que añade los componentes CABLEKIT del SIMESTRUCK de ATENEA-SIMUMAK a ATENEA-TORO
// Se guardan / actualizan los componentes 
// Se guardan / actualizan las referencias de los componentes
// Se guardan / actualizan las interfaces o kits de los componentes
include("../classes/mysql.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/kint/Kint.class.php");
set_time_limit(10000);

$db = new MySQL();
$ref = new Referencia();

// Comprobamos que componentes se utilizan en el SIMESCAR
$id_produccion_simescar = 129;

$consulta = sprintf("select * from orden_produccion_componentes where activo=1 and id_produccion_componente in
				(select id_produccion_componente from orden_produccion_referencias where id_produccion=%s)",
	$db->makeValue($id_produccion_simescar, "int"));
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$componentes_simescar = $db->getResultados();
d($componentes_simescar);

// Preparamos el array con los componentes del SIMESCAR 
for($i=0;$i<count($componentes_simescar);$i++){
	$id_comps_simescar[] = $componentes_simescar[$i]["id_componente"];
}


// REALIZAMOS LA ACTUALIZACION DE COMPONENTES Y REFERENCIAS
$componentes_del_simescar = 0;
$componentes_creados = 0;
$componentes_actualizados = 0;	

// Hemos hecho una copia de las tablas de componentes de la BBDD de SIMUMAK 
// Obtenemos los componentes CABLE KIT del SIMESTRUCK DE SIMUMAK
$array_id_componentes = array(417,418,419,420,421,422,423,424,425,426,427,429,430,431,432,433,434,435,436,437,438,439,440,441,442);
d($array_id_componentes);

// ACTUALIZACION DE LA TABLA COMPONENTES
for($i=0;$i<count($array_id_componentes);$i++){
	$id_componente = $array_id_componentes[$i];

	// Obtenemos los datos del componente de la BBDD SIMUMAK
	$consulta = sprintf("select * from zzz_componentes where activo=1 and id_componente=%s",
		$db->makeValue($id_componente,"int"));
	$db->setConsulta($consulta);
	$db->ejecutarConsulta();
	$datos_componente = $db->getPrimerResultado();

	$id_componente_simumak = $datos_componente["id_componente"];
	$nombre_simumak = $datos_componente["nombre"];
	$referencia_simumak = $datos_componente["referencia"];
	$descripcion_simumak = $datos_componente["descripcion"];
	$version_simumak = $datos_componente["version"];
	$id_tipo_simumak = $datos_componente["id_tipo"];
	$estado_simumak = $datos_componente["estado"];
	$prototipo_simumak = $datos_componente["prototipo"];

	// SI EL COMPONENTE SE UTILIZA EN EL SIMESCAR NO ACTUALIZAMOS
	if(in_array($id_componente, $id_comps_simescar)){
		print_r("<span style='color: red;'>El componente [".$id_componente."] se utiliza en el SIMESCAR!! NO SE ACTUALIZA EL COMPONENTE"); echo "<br/><br/>"; 
		$componentes_del_simescar++;
	}
	else {
		print_r("<span style='color: green;'>El componente [".$id_componente."] no se utiliza en el SIMESCAR");	echo "<br/>";

		// Comprobamos si el componente coinciden en ambas BBDD
		$consulta = sprintf("select id_componente from componentes where nombre=%s and version=%s and activo=1 and id_tipo=%s and id_componente=%s",
			$db->makeValue($nombre_simumak, "text"),
			$db->makeValue($version_simumak, "float"),
			$db->makeValue($id_tipo_simumak, "int"),
			$db->makeValue($id_componente_simumak, "int"));
		$db->setConsulta($consulta);
		$db->ejecutarConsulta();
		if($db->getNumeroFilas() == 0) {
			// NO COINCIDEN EL COMPONENTE CON EL DE SIMUMAK
			print_r("<span style='color:red;'>No existe el componente ".$nombre_simumak."[".$id_componente_simumak."] en la BBDD de TORO</span>"); echo "<br/>";				
			echo "Comprobando si existe con otro id_componente..."; echo "<br/>";

			// Comprobamos si existe el componente en la BBDD 
			$consulta = sprintf("select id_componente from componentes where activo=1 and nombre=%s and version=%s",
				$db->makeValue($nombre_simumak,"text"),
				$db->makeValue($version_simumak,"float"));	
			$db->setConsulta($consulta);
			$db->ejecutarConsulta();
			$resultado_dif_id = $db->getPrimerResultado();
			$otro_id_componente = $resultado_dif_id["id_componente"];

			if(count($resultado_dif_id) == 0){
				echo "No existe el componente"; echo "<br/>";
				echo "Creando componente..."; echo "<br/>";	

				// Insertamos el nuevo componente
				$insertSql = sprintf("insert into componentes (nombre,referencia,descripcion,version,id_tipo,estado,prototipo,fecha_creacion,activo) values 
								(%s,%s,%s,%s,%s,%s,%s,current_timestamp,1)",
					$db->makeValue($nombre_simumak, "text"),
					$db->makeValue($referencia_simumak, "text"),
					$db->makeValue($descripcion_simumak, "text"),
					$db->makeValue($version_simumak, "float"),
					$db->makeValue($id_tipo_simumak, "int"),
					$db->makeValue($estado_simumak, "text"),
					$db->makeValue($prototipo_simumak, "int"));
				$db->setConsulta($insertSql);
				if($db->ejecutarSoloConsulta()){
					$ultimo_id = $db->getUltimoId();
					print_r("<span style='color:green;'>Se ha insertado el componente ".$nombre_simumak."[".$id_componente_simumak."] en la BBDD de TORO con id_componente[".$ultimo_id."]</span>"); echo "<br/><br/>";				
				}
				else {
					print_r("<span style='color:red;'>Se produjo un error al insertar el componente ".$nombre_simumak."[".$id_componente_simumak."] en la BBDD de TORO</span>"); echo "<br/><br/>";								
				}
				$componentes_creados++;
			}
			else {
				echo "Existe el componente [".$id_componente."] con otro id_componente[".$otro_id_componente."]"; echo "<br/>";
				echo "Actualizando componente...";echo "<br/>";	

				// Existe en TORO con diferente id_componente
				$id_componente_toro = $resultado_dif_id["id_componente"];
				$updateSql  = sprintf("update componentes set nombre=%s,referencia=%s,descripcion=%s,version=%s,id_tipo=%s,estado=%s,prototipo=%s where id_componente=%s",
					$db->makeValue($nombre_simumak, "text"),
					$db->makeValue($referencia_simumak, "text"),
					$db->makeValue($descripcion_simumak, "text"),
					$db->makeValue($version_simumak, "float"),
					$db->makeValue($id_tipo_simumak, "int"),
					$db->makeValue($estado_simumak, "text"),
					$db->makeValue($prototipo_simumak, "int"),
					$db->makeValue($id_componente_toro, "int"));
				$db->setConsulta($updateSql);
				if($db->ejecutarSoloConsulta()){
					print_r("<span style='color:green;'>Se ha actualizado el componente ".$nombre_simumak."[".$id_componente_toro."] en la BBDD de TORO</span>"); echo "<br/><br/>";				
				}
				else{
					print_r("<span style='color:red;'>Se produjo un error al actualizar el componente ".$nombre_simumak."[".$id_componente_toro."] en la BBDD de TORO</span>"); echo "<br/><br/>";								
				}
				$componentes_actualizados++;
			}
		}
		else{
			// ACTUALIZAMOS EL COMPONENTE EXISTEN CON ID COINCIDENTE
			echo "Existe el componente [".$id_componente."]"; echo "<br/>";
			echo "Actualizando componente...";echo "<br/>";	

			// Existe con el mismo id_componente
			$updateSql  = sprintf("update componentes set nombre=%s,referencia=%s,descripcion=%s,version=%s,id_tipo=%s,estado=%s,prototipo=%s where id_componente=%s",
				$db->makeValue($nombre_simumak, "text"),
				$db->makeValue($referencia_simumak, "text"),
				$db->makeValue($descripcion_simumak, "text"),
				$db->makeValue($version_simumak, "float"),
				$db->makeValue($id_tipo_simumak, "int"),
				$db->makeValue($estado_simumak, "text"),
				$db->makeValue($prototipo_simumak, "int"),
				$db->makeValue($id_componente_simumak, "int"));
			$db->setConsulta($updateSql);
			if($db->ejecutarSoloConsulta()){
				print_r("<span style='color:green;'>Se ha actualizado el componente ".$nombre_simumak."[".$id_componente_simumak."] en la BBDD de TORO</span>"); echo "<br/><br/>";				
			}
			else{
				print_r("<span style='color:red;'>Se produjo un error al actualizar el componente ".$nombre_simumak."[".$id_componente_simumak."] en la BBDD de TORO</span>"); echo "<br/><br/>";								
			}
			$componentes_actualizados++;
		}
	}
}

$total_componentes = $componentes_creados + $componentes_actualizados + $componentes_del_simescar;

echo "<br/><span style='color: green;'>COMPONENTES CREADOS: ".$componentes_creados."<br/>";
echo "<span style='color: green;'>COMPONENTES ACTUALIZADOS: ".$componentes_actualizados."<br/>";
echo "<span style='color: red;'>COMPONENTES SIMESTRUCK: ".$componentes_del_simescar."<br/>";
echo "<span style='color: green;'>TOTAL COMPONENTES: ".$total_componentes."<br/><br/><br/>";



$num_comp_refs_simescar = 0;
$num_refs_guardadas = 0;
$num_refs_reset = 0;

// INSERTAMOS LAS REFERENCIAS DE LOS COMPONENTES 
for($i=0;$i<count($array_id_componentes);$i++){
	$id_componente = $array_id_componentes[$i];

	// Obtenemos los datos del componente de la BBDD SIMUMAK
	$consulta = sprintf("select * from zzz_componentes where activo=1 and id_componente=%s",
		$db->makeValue($id_componente,"int"));
	$db->setConsulta($consulta);
	$db->ejecutarConsulta();
	$datos_componente = $db->getPrimerResultado();

	$id_componente_simumak = $datos_componente["id_componente"];
	$nombre_simumak = $datos_componente["nombre"];
	$referencia_simumak = $datos_componente["referencia"];
	$descripcion_simumak = $datos_componente["descripcion"];
	$version_simumak = $datos_componente["version"];
	$id_tipo_simumak = $datos_componente["id_tipo"];
	$estado_simumak = $datos_componente["estado"];
	$prototipo_simumak = $datos_componente["prototipo"];

	// Obtenemos el id_componente de TORO equivalente al id_componente de SIMUMAK 
	$consulta = sprintf("select id_componente from componentes where nombre=%s and version=%s and activo=1 and id_tipo=%s",
		$db->makeValue($nombre_simumak, "text"),
		$db->makeValue($version_simumak, "float"),
		$db->makeValue($id_tipo_simumak, "int"));
	$db->setConsulta($consulta);
	$db->ejecutarConsulta();
	$res_id_componente = $db->getPrimerResultado();

	$id_componente_toro = $res_id_componente["id_componente"];

	// Si el componente se utiliza en el SIMESCAR NO ACTUALIZAMOS LAS REFERENCIAS 
	if(in_array($id_componente_toro, $id_comps_simescar)){
		print_r("<span style='color: red;'>El componente [".$id_componente_toro."] se utiliza en el SIMESCAR!! REFERENCIAS NO ACTUALIZADAS"); echo "</span><br/><br/>"; 
		$num_comp_refs_simescar++;
	}
	else {
		print_r("<span style='color: green;'>El componente [".$id_componente_toro."] no se utiliza en el SIMESCAR");	echo "</span><br/>";

		// Obtenemos las referencias del componente de la tabla de Simumak 
		$consulta = sprintf("select * from zzz_componentes_referencias where activo=1 and id_componente=%s group by id_referencia",
			$db->makeValue($id_componente_simumak, "int"));
		$db->setConsulta($consulta);
		$db->ejecutarConsulta();
		$referencias_componente = $db->getResultados();

		// Reseteamos las referencias del componente para poder actualizarlo
		$consulta = sprintf("update componentes_referencias set activo=0 where id_componente=%s",
			$db->makeValue($id_componente_toro, "int"));
		$db->setConsulta($consulta);
		if($db->ejecutarSoloConsulta()){
			echo "<span style='color: green;'> Se resetearon las referencias del componente [".$id_componente_toro."] para su actualizacion"; echo "</span><br/>";
			$num_refs_reset++;
		}
		else {
			echo "<span style='color: red;'> Se produjo un error al resetear las referencias del componente [".$id_componente_toro."]";	echo "</span><br/>";
		}

		// Actualizamos las referencias del componente de toro con las referencias de la tabla de simumak
		for($j=0; $j<count($referencias_componente);$j++){
			// $id_componente = $referencias_componente[$j]["id_componente"];
			$id_referencia = $referencias_componente[$j]["id_referencia"];
			$uds_paquete = $referencias_componente[$j]["uds_paquete"];
			$piezas = $referencias_componente[$j]["piezas"];
			$total_paquetes = $referencias_componente[$j]["total_paquetes"];
			$pack_precio = $referencias_componente[$j]["pack_precio"];

			if($uds_paquete == NULL) {
				$uds_paquete = 1;
				echo "<span style='color: red;'> uds_paquete NULL en la referencia [".$id_referencia."]"; echo "</span><br/>";		
			}	
			if($pack_precio == NULL){
				$pack_precio = 0;
				echo "<span style='color: red;'> pack_precio NULL en la referencia [".$id_referencia."]"; echo "</span><br/>";		 
			}

			$insertSql = sprintf("insert into componentes_referencias (id_componente, id_referencia, uds_paquete, piezas, total_paquetes, pack_precio, fecha_creado, activo)
								values (%s, %s, %s, %s, %s, %s, current_timestamp,1)",
				$db->makeValue($id_componente_toro, "int"),
				$db->makeValue($id_referencia, "int"),
				$db->makeValue($uds_paquete, "float"),
				$db->makeValue($piezas, "float"),
				$db->makeValue($total_paquetes, "int"),
				$db->makeValue($pack_precio, "float"));
			$db->setConsulta($insertSql);

			if($db->ejecutarSoloConsulta()){
				echo "<span style='color: green;'> Se inserto la referencia [".$id_referencia."] en el componente [".$id_componente_toro."]"; echo "</span><br/>";
				$num_refs_guardadas++;
			}
			else {
				echo "<span style='color: red;'> Se produjo un error al guardar la referencia [".$id_referencia."] del componente [".$id_componente_toro."]"; echo "</span><br/>";
			}		
		}
	}
	echo"</br>";
}

echo "<br/><span style='color: green;'>NUM COMPONENTES RESETEAD0S: ".$num_refs_reset."<br/>";
echo "<span style='color: green;'>NUM REFS COMPONENTES GUARDADAS: ".$num_refs_guardadas."<br/><br/><br/>";



// INSERTAMOS LAS INTERFACES O KITS DE LOS COMPONENTES
for($i=0;$i<count($array_id_componentes);$i++){
	// KIT DE SIMUMAK
	$id_kit = $array_id_componentes[$i];

	// Obtenemos los datos del kit de la BBDD SIMUMAK
	$consulta = sprintf("select * from zzz_componentes where activo=1 and id_componente=%s",
		$db->makeValue($id_kit,"int"));
	$db->setConsulta($consulta);
	$db->ejecutarConsulta();
	$datos_componente = $db->getPrimerResultado();

	$id_kit_simumak = $datos_componente["id_componente"];
	$nombre_simumak = $datos_componente["nombre"];
	$referencia_simumak = $datos_componente["referencia"];
	$descripcion_simumak = $datos_componente["descripcion"];
	$version_simumak = $datos_componente["version"];
	$id_tipo_simumak = $datos_componente["id_tipo"];
	$estado_simumak = $datos_componente["estado"];
	$prototipo_simumak = $datos_componente["prototipo"];

	// Obtenemos el id_componente de TORO equivalente al id_componente de SIMUMAK 
	$consulta = sprintf("select id_componente from componentes where nombre=%s and version=%s and activo=1 and id_tipo=%s",
		$db->makeValue($nombre_simumak, "text"),
		$db->makeValue($version_simumak, "float"),
		$db->makeValue($id_tipo_simumak, "int"));
	$db->setConsulta($consulta);
	$db->ejecutarConsulta();
	$res_id_componente = $db->getPrimerResultado();

	// ID_KIT guardado en TORO 
	$id_kit_toro = $res_id_componente["id_componente"];	

	// Tenemos que buscar el componente que tiene integrado el KIT
	$consultaPadre = sprintf("select id_componente from zzz_componentes_kits where activo=1 and id_kit=%s",
		$db->makeValue($id_kit, "text"));
	$db->setConsulta($consultaPadre);
	$db->ejecutarConsulta();
	$res_id_padre = $db->getPrimerResultado();

	$id_componente_padre = $res_id_padre["id_componente"];

	// Obtenemos los datos del componente padre de la BBDD SIMUMAK
	$consulta = sprintf("select * from zzz_componentes where activo=1 and id_componente=%s",
		$db->makeValue($id_componente_padre,"int"));
	$db->setConsulta($consulta);
	$db->ejecutarConsulta();
	$datos_componente_padre = $db->getPrimerResultado();

	$id_padre_simumak = $datos_componente_padre["id_componente"];
	$nombre_padre_simumak = $datos_componente_padre["nombre"];
	$referencia_padre_simumak = $datos_componente_padre["referencia"];
	$descripcion_padre_simumak = $datos_componente_padre["descripcion"];
	$version_padre_simumak = $datos_componente_padre["version"];
	$id_tipo_padre_simumak = $datos_componente_padre["id_tipo"];
	$estado_padre_simumak = $datos_componente_padre["estado"];
	$prototipo_padre_simumak = $datos_componente_padre["prototipo"];

	// Obtenemos el id_componente_padre de TORO equivalente al id_componente_padre de SIMUMAK 
	$consulta = sprintf("select id_componente from componentes where nombre=%s and version=%s and activo=1 and id_tipo=%s",
		$db->makeValue($nombre_padre_simumak, "text"),
		$db->makeValue($version_padre_simumak, "float"),
		$db->makeValue($id_tipo_padre_simumak, "int"));
	$db->setConsulta($consulta);
	$db->ejecutarConsulta();
	$res_id_componente = $db->getPrimerResultado();

	// ID_COMPONENTE guardado en TORO 
	$id_padre_toro = $res_id_componente["id_componente"];	

	// Asociamos el kit al componente 
	$insertSql = sprintf("insert into componentes_kits (id_tipo_componente, id_componente, id_kit, fecha_creado, activo) values
							(2,%s,%s,current_timestamp,1)",
		$db->makeValue($id_padre_toro, "int"),
		$db->makeValue($id_kit_toro, "int"));
	$db->setConsulta($insertSql);
	if(!$db->ejecutarSoloConsulta()){
		echo "ERROR al insertar asociar los kits al componente";
	}
	else{
		print_r("<span style='color:green;'>Se ha vinculado el kit [".$id_kit_toro."] con el periferico [".$id_padre_toro."] en la BBDD de TORO</span>"); echo "<br/>";							
	}	
}

/*
// Insertamos los componentes CABLE KIT y las referencias en la Orden de Produccion 131 de TORO
$id_produccion = 131; 
$contador_componente = 73;
$unidades = 3;
$ult_id_produccion_componente = 2979;

for($i=0;$i<count($array_id_componentes);$i++){
	$id_kit = $array_id_componentes[$i];

	// Guardamos los componentes en la Orden de Produccion
	$kit->cargaDatosKitId($id_kit);
	$num_serie_componente = $kit->referencia."_".$kit->version."_".$id_produccion."_".$contador_componente;
	$resultado = $orden_produccion->guardarComponenteProduccion($id_produccion,$id_kit,$num_serie_componente);

	if($resultado != 1){
		$error = true; 
		$i = count($array_id_componentes);
		$mensaje_error = $kit->getErrorMessage($resultado);
	}
	else {
		// Guardamos las referencias del componente
		$id_produccion_componente = $orden_produccion->dameUltimoIdProduccionComponente();
		// Guardamos las referencias de los componentes
		$ref_comp->dameReferenciasPorIdComponente($id_kit);
		$referencias_componente = $ref_comp->referencias_componente;
		for($j=0;$j<count($referencias_componente);$j++){
			$id_referencia = $referencias_componente[$j]["id_referencia"];
			$uds_paquete = $referencias_componente[$j]["uds_paquete"];
			$piezas = $referencias_componente[$j]["piezas"];
			// Calculamos el total_paquetes para la referencia
			$ref_modificada->calculaTotalPaquetes($uds_paquete,$piezas);
			$total_paquetes = $ref_modificada->total_paquetes;
			$pack_precio = $referencias_componente[$j]["pack_precio"];
			$resultado = $orden_produccion->guardarReferenciasProduccion($id_produccion,5,$id_produccion_componente,$id_kit,$id_referencia,$uds_paquete,$piezas,$total_paquetes,$pack_precio);		
			if ($resultado != 1){
				$j = count($referencias_componente);
				$error = true;
			}	
		}		
	}
	$contador_componente++;
	$i++;
}
*/


?>
