<?php 
// Este fichero se encargar de reagrupar las referencias en el caso de que se hayan metido referencias duplicadas en perifericos, kits o referencias libres
// dentro de la Confirmacion de la Modificacion de la Orden de Produccion

// Referencias duplicadas de los periféricos
if($tipo_componente == 2){
	if ($referencias_perifericos[$j] != NULL){
		// Hay que comprobar si las referencias de cada periferico estan duplicadas.
		// Calculamos las repeticiones de las referencias
		$array_repeticiones_referencias = array_count_values($referencias_perifericos[$j]);
		// Obtenemos los ids de las referencias (claves) sin repetir a partir del array con el número de repeticiones por referencia
		$id_refs_unicas = array_keys($array_repeticiones_referencias);
								
		// Guardamos en un array las claves de las referencias repetidas del array de referencias 
		for($m=0;$m<count($id_refs_unicas);$m++) $claves_repetidas_todas_refs[$id_refs_unicas[$m]] = array_keys($referencias_perifericos[$j],$id_refs_unicas[$m]);

		// Ahora recalculamos las piezas en el caso de que haya referencias repetidas
		for($m=0;$m<count($claves_repetidas_todas_refs);$m++){
			$piezas_por_referencia = 0;
			$claves_repetidas_referencia = $claves_repetidas_todas_refs[$id_refs_unicas[$m]];
														
			for($l=0;$l<count($claves_repetidas_referencia);$l++){ 
				$clave_pieza = $claves_repetidas_referencia[$l];
				$piezas_por_referencia = $piezas_por_referencia + $piezas_perifericos[$j][$clave_pieza]; 
								
				// Obtenemos la primera "unidad_paquete" de las referencias repetidas
				if ($l==0) $uds_paquete_final[] = $uds_paquete_perifericos[$j][$clave_pieza];
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
			
		// Calculamos el número total de paquetes de las referencias reagrupadas
		for ($m=0;$m<count($referencias_perifericos_aux[$j]);$m++){
			$ref->calculaTotalPaquetes($uds_paquete_perifericos_aux[$j][$m],$piezas_perifericos_aux[$j][$m]);
			$total_paquetes_final[] = $ref->total_paquetes;
		}
		$total_paquetes_perifericos_aux[$j] = $total_paquetes_final;
		unset($total_paquetes_final);
		unset($referencias_final);
	}
}
else if($tipo_componente == 6){

}
// Referencias duplicadas de las referencias libres
elseif($tipo_componente == 0){
	if ($referencias_libres != NULL){
		// Hay que comprobar si las referencias libres están duplicadas.
		// Calculamos las repeticiones de las referencias
		$array_repeticiones_referencias = array_count_values($referencias_libres);	
		// Obtenemos los ids de las referencias (claves) sin repetir a partir del array con el número de repeticiones por referencia
		$id_refs_unicas = array_keys($array_repeticiones_referencias);
							
		// Guardamos en un array las claves de las referencias repetidas del array de referencias 
		for($k=0;$k<count($id_refs_unicas);$k++) $claves_repetidas_todas_refs[$id_refs_unicas[$k]] = array_keys($referencias_libres,$id_refs_unicas[$k]);

		// Ahora recalculamos las piezas en el caso de que haya referencias repetidas
		for($k=0;$k<count($claves_repetidas_todas_refs);$k++){
			$piezas_por_referencia = 0;
			$claves_repetidas_referencia = $claves_repetidas_todas_refs[$id_refs_unicas[$k]];
														
			for($l=0;$l<count($claves_repetidas_referencia);$l++){ 
				$clave_pieza = $claves_repetidas_referencia[$l];
				$piezas_por_referencia = $piezas_por_referencia + $Piezas_Ref_Libres[$clave_pieza]; 
											
				// Obtenemos la primera "unidad_paquete" de las referencias repetidas
				if ($l==0) $uds_paquete_final[] = $uds_paquete_ref_libre[$clave_pieza];
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
			$ref->calculaTotalPaquetes($uds_paquete_ref_libre[$k],$Piezas_Ref_Libres[$k]);
			$total_paquetes_final[] = $ref->total_paquetes;
		}
		$total_paquetes_ref_libres = $total_paquetes_final;
		
		unset($array_repeticiones_referencias);
		unset($id_refs_unicas);
		unset($claves_repetidas_todas_refs);
		unset($claves_repetidas_referencia);
		unset($piezas_final); 
		unset($uds_paquete_final);
	}		

}
?>