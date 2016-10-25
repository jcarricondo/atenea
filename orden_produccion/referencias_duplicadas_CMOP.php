<?php 
// Este fichero se encargar de reagrupar las referencias en el caso de que se hayan metido referencias duplicadas en la cabina, perifericos y referencias libres
// dentro de la Confirmacion de la Modificacion de la Orden de Produccion

$ref_aux = new Referencia();
// Referencias duplicadas de la cabina
if ($tipo_componente == 1){
	/*print_r("REFS CABINA: ");print_r($referencias_cabina);echo"<br/>";		
	print_r("PIEZAS CABINA: ");print_r($piezas_cabina);echo"<br/>";		
	print_r("UDSP CABINA: ");print_r($uds_paquete_cabina);echo"<br/>";		
	print_r("TOTAL PAQS CABINA: ");print_r($total_paquetes_cabina);echo"<br/>";
	echo"<br/>";*/

	/*
	// Si hay referencias
	if ($referencias_cabina[0] != NULL){
		// Hay que comprobar si las referencias de la cabina estan duplicadas. 
		// Calculamos las repeticiones de las referencias
		$array_repeticiones_referencias = array_count_values($referencias_cabina[0]);	
		// Obtenemos los ids de las referencias (claves) sin repetir a partir del array con el numero de repeticiones por referencia
		$id_refs_unicas = array_keys($array_repeticiones_referencias);
							
		// Guardamos en un array las claves de las referencias repetidas del array de referencias 
		for($k=0;$k<count($id_refs_unicas);$k++){
			$claves_repetidas_todas_refs[$id_refs_unicas[$k]] = array_keys($referencias_cabina[0],$id_refs_unicas[$k]);					
		}
				
		// Ahora recalculamos las piezas en el caso de que haya referencias repetidas
		for($k=0;$k<count($claves_repetidas_todas_refs);$k++){
			$piezas_por_referencia = 0;
			$claves_repetidas_referencia = $claves_repetidas_todas_refs[$id_refs_unicas[$k]];
														
			for($l=0;$l<count($claves_repetidas_referencia);$l++){ 
				$clave_pieza = $claves_repetidas_referencia[$l];
				$piezas_por_referencia = $piezas_por_referencia + $piezas_cabina[0][$clave_pieza]; 
											
				// Obtenemos la primera "unidad_paquete" de las referencias repetidas
				if ($l==0) {
					$uds_paquete_final[] = $uds_paquete_cabina[0][$clave_pieza];
				}
			}
			// Guardamos en un nuevo array la suma de las piezas de las referencias repetidas
			$piezas_final[] = $piezas_por_referencia;
		}
				
		// Guardamos en un nuevo array las referencias sin repeticiones
		$referencias_final = array_unique($referencias_cabina[0]);
		$referencias_final = array_merge($referencias_final); 
						
		// Reseteamos los arrays y copiamos los obtenidos 
		unset($referencias_cabina);
		unset($piezas_cabina);
		unset($uds_paquete_cabina);
		unset($tot_paquetes);
		$referencias_cabina[] = $referencias_final;
		$piezas_cabina[] = $piezas_final;
		$uds_paquete_cabina[] = $uds_paquete_final;
		
		// Calculamos el numero total de paquetes de las referencias reagrupadas
		for ($k=0;$k<count($referencias_cabina[0]);$k++){
			$ref_aux->calculaTotalPaquetes($uds_paquete_cabina[0][$k],$piezas_cabina[0][$k]);
			$total_paquetes_final[] = $ref_aux->total_paquetes;
		}
		$total_paquetes_cabina = $total_paquetes_final;
		
		unset($array_repeticiones_referencias);
		unset($id_refs_unicas);
		unset($claves_repetidas_todas_refs);
		unset($claves_repetidas_referencia);
		unset($referencias_final);
		unset($piezas_final);
		unset($uds_paquete_final);
		unset($total_paquetes_final);
	}		
	echo"<br/>";
	print_r("REFS CABINA: ");print_r($referencias_cabina);echo"<br/>";		
	print_r("PIEZAS CABINA: ");print_r($piezas_cabina);echo"<br/>";		
	print_r("UDSP CABINA: ");print_r($uds_paquete_cabina);echo"<br/>";		
	print_r("TOTAL PAQS CABINA: ");print_r($total_paquetes_cabina);echo"<br/>";
	echo"<br/>";echo"<br/>";*/
}
// Referencias duplicadas de los perifericos
elseif($tipo_componente == 2){
	// Si hay referencias
	if ($referencias_perifericos[$j] != NULL){
		/*print_r("REFS PERIFERICO_".$j.": ");print_r($referencias_perifericos[$j]);echo"<br/>";		
		print_r("PIEZAS PERIFERICO_".$j.": ");print_r($piezas_perifericos[$j]);echo"<br/>";		
		print_r("UDSP PERIFERICO_".$j.": ");print_r($uds_paquete_perifericos[$j]);echo"<br/>";			
		print_r("TOTAL PAQS PERIFERICO_".$j.": ");print_r($total_paquetes_perifericos[$j]);echo"<br/>";	
		echo"<br/>";echo"<br/>";*/	
		
		// Hay que comprobar si las referencias de cada periferico estan duplicadas. 
		// Calculamos las repeticiones de las referencias
		$array_repeticiones_referencias = array_count_values($referencias_perifericos[$j]);
		// Obtenemos los ids de las referencias (claves) sin repetir a partir del array con el numero de repeticiones por referencia
		$id_refs_unicas = array_keys($array_repeticiones_referencias);
								
		// Guardamos en un array las claves de las referencias repetidas del array de referencias 
		for($m=0;$m<count($id_refs_unicas);$m++){
			$claves_repetidas_todas_refs[$id_refs_unicas[$m]] = array_keys($referencias_perifericos[$j],$id_refs_unicas[$m]);
		}
				
		// Ahora recalculamos las piezas en el caso de que haya referencias repetidas
		for($m=0;$m<count($claves_repetidas_todas_refs);$m++){
			$piezas_por_referencia = 0;
			$claves_repetidas_referencia = $claves_repetidas_todas_refs[$id_refs_unicas[$m]];
														
			for($l=0;$l<count($claves_repetidas_referencia);$l++){ 
				$clave_pieza = $claves_repetidas_referencia[$l];
				$piezas_por_referencia = $piezas_por_referencia + $piezas_perifericos[$j][$clave_pieza]; 
								
				// Obtenemos la primera "unidad_paquete" de las referencias repetidas
				if ($l==0) {
					$uds_paquete_final[] = $uds_paquete_perifericos[$j][$clave_pieza];
				}
			}
			// Guardamos en un nuevo array la suma de las piezas de las referencias repetidas
			$piezas_final[] = $piezas_por_referencia;
			$piezas_perifericos_aux[$j] = $piezas_final;
			$uds_paquete_perifericos_aux[$j] = $uds_paquete_final;
		}
		unset($array_repeticiones_referencias);
		unset($id_refs_unicas);
		unset($claves_repetidas_todas_refs);
		unset($claves_repetidas_referencia);
		unset($piezas_final); 
		unset($uds_paquete_final);
				
		// Guardamos en un nuevo array las referencias sin repeticiones
		$referencias_final = array_unique($referencias_perifericos[$j]);
		$referencias_final = array_merge($referencias_final); 
						
		$referencias_perifericos_aux[$j] = $referencias_final;
			
		// Calculamos el numero total de paquetes de las referencias reagrupadas
		for ($m=0;$m<count($referencias_perifericos_aux[$j]);$m++){
			$ref_aux->calculaTotalPaquetes($uds_paquete_perifericos_aux[$j][$m],$piezas_perifericos_aux[$j][$m]);
			$total_paquetes_final[] = $ref_aux->total_paquetes;
		}
		$total_paquetes_perifericos_aux[$j] = $total_paquetes_final;
		unset($total_paquetes_final);
		unset($referencias_final);
		/*echo"<br/>";
		print_r("REFS PERIFERICO_".$j.": ");print_r($referencias_perifericos_aux[$j]);echo"<br/>";		
		print_r("PIEZAS PERIFERICO_".$j.": ");print_r($piezas_perifericos_aux[$j]);echo"<br/>";		
		print_r("UDSP PERIFERICO_".$j.": ");print_r($uds_paquete_perifericos_aux[$j]);echo"<br/>";			
		print_r("TOTAL PAQS PERIFERICO_".$j.": ");print_r($total_paquetes_perifericos_aux[$j]);echo"<br/>";	
		echo"<br/>";echo"<br/>";*/
	}
}
// Referencias duplicadas de las referencias libres
elseif($tipo_componente == 0){
	/*print_r("REFS LIBRES: ");print_r($referencias_libres);echo"<br/>";		
	print_r("PIEZAS LIBRES: ");print_r($Piezas_Ref_Libres);echo"<br/>";		
	print_r("UDSP LIBRES: ");print_r($uds_paquete_ref_libre);echo"<br/>";		
	print_r("TOTAL PAQS LIBRES: ");print_r($total_paquetes_ref_libres);echo"<br/>";	
	echo"<br/>";	echo"<br/>";*/		
	
	// Si hay referencias
	if ($referencias_libres != NULL){
		// Hay que comprobar si las referencias libres estan duplicadas. 
		// Calculamos las repeticiones de las referencias
		$array_repeticiones_referencias = array_count_values($referencias_libres);	
		// Obtenemos los ids de las referencias (claves) sin repetir a partir del array con el numero de repeticiones por referencia
		$id_refs_unicas = array_keys($array_repeticiones_referencias);
							
		// Guardamos en un array las claves de las referencias repetidas del array de referencias 
		for($k=0;$k<count($id_refs_unicas);$k++){
			$claves_repetidas_todas_refs[$id_refs_unicas[$k]] = array_keys($referencias_libres,$id_refs_unicas[$k]);					
		}
				
		// Ahora recalculamos las piezas en el caso de que haya referencias repetidas
		for($k=0;$k<count($claves_repetidas_todas_refs);$k++){
			$piezas_por_referencia = 0;
			$claves_repetidas_referencia = $claves_repetidas_todas_refs[$id_refs_unicas[$k]];
														
			for($l=0;$l<count($claves_repetidas_referencia);$l++){ 
				$clave_pieza = $claves_repetidas_referencia[$l];
				$piezas_por_referencia = $piezas_por_referencia + $Piezas_Ref_Libres[$clave_pieza]; 
											
				// Obtenemos la primera "unidad_paquete" de las referencias repetidas
				if ($l==0) {
					$uds_paquete_final[] = $uds_paquete_ref_libre[$clave_pieza];
				}
			}
			// Guardamos en un nuevo array la suma de las piezas de las referencias repetidas
			$piezas_final[] = $piezas_por_referencia;
		}
		// Guardamos en un nuevo array las referencias sin repeticiones
		$referencias_final = array_unique($referencias_libres);
		$referencias_final = array_merge($referencias_final); 
						
		// Reseteamos los arrays y copiamos los obtenidos 
		unset($referencias_libres);
		unset($Piezas_Ref_Libres);
		unset($uds_paquete_ref_libre);
		unset($total_paquetes_ref_libres);
		$referencias_libres = $referencias_final;
		$Piezas_Ref_Libres = $piezas_final;
		$uds_paquete_ref_libre = $uds_paquete_final;
	
		// Calculamos el numero total de paquetes de las referencias reagrupadas
		for ($k=0;$k<count($referencias_libres);$k++){
			$ref_aux->calculaTotalPaquetes($uds_paquete_ref_libre[$k],$Piezas_Ref_Libres[$k]);
			$total_paquetes_final[] = $ref_aux->total_paquetes;
		}
		$total_paquetes_ref_libres = $total_paquetes_final;
		
		unset($array_repeticiones_referencias);
		unset($id_refs_unicas);
		unset($claves_repetidas_todas_refs);
		unset($claves_repetidas_referencia);
		unset($piezas_final); 
		unset($uds_paquete_final);
	}		
	/*print_r("REFS LIBRES: ");print_r($referencias_libres);echo"<br/>";		
	print_r("PIEZAS LIBRES: ");print_r($Piezas_Ref_Libres);echo"<br/>";		
	print_r("UDSP LIBRES: ");print_r($uds_paquete_ref_libre);echo"<br/>";		
	print_r("TOTAL PAQS LIBRES: ");print_r($total_paquetes_ref_libres);echo"<br/>";*/ 
}
?>