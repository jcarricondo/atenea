<?php
// Este fichero muestra las referencias de los kits de la cabina y de los perifericos en la modificacion de una OP.
// Cogemos las configuraciones de basicos, es decir, de la tabla referecias
$ref_kits = new listadoReferenciasComponentes();
$ref_kits->setValores($Kit->id_componente); 
$ref_kits->realizarConsulta();
$resultadosReferenciasKit = $ref_kits->referencias_componentes;
?>   
<tr>
	<th style="text-align:center">ID_REF</th>
	<th>NOMBRE</th>
   	<th>PROVEEDOR</th>
    <th>REF PROV</th>
   	<th>NOMBRE PIEZA</th>
   	<th style="text-align:center">PIEZAS</th>
    <th style="text-align:center">PACK PRECIO</th>
    <th style="text-align:center">UDS/P</th>
    <th style="text-align:center">TOTAL PAQS</th>
    <th style="text-align:center">PRECIO UNIDAD</th>
    <th style="text-align:center">PRECIO</th>                            
</tr>                     
<?php 
	$precio_kit = 0;
    for($j=0;$j<count($resultadosReferenciasKit);$j++) {	
		// Si el estado de la OP es BORRADOR o INICIADO tiramos de la tabla referencias
		$ref_kit = new Referencia_Componente();
		$ref_modificada = new Referencia();
		$datoRef_Kit = $resultadosReferenciasKit[$j];
		
		// Obtenemos los datos de las referencias de basicos
		$ref_kit->cargaDatosReferenciaComponenteId($datoRef_Kit["id"]);
		$ref_modificada->cargaDatosReferenciaId($ref_kit->id_referencia);
													
		if ($ref_modificada->pack_precio <> 0 and $ref_modificada->unidades <> 0){
			$precio_unidad = ($ref_modificada->pack_precio / $ref_modificada->unidades);
		}
		else {
			$precio_unidad = 00;
		}
		$ref_modificada->calculaTotalPaquetes($ref_modificada->unidades,$ref_kit->piezas);
		$total_paquetes = $ref_modificada->total_paquetes;
		$precio_referencia = $ref_kit->piezas * $precio_unidad;
			
		echo '<tr><td style="text-align:center">'.$ref_kit->id_referencia.'</td><td id="enlaceComposites"><a href="../basicos/mod_referencia.php?id='.$ref_kit->id_referencia.'"/>'.$ref_modificada->referencia.'</a></td><td>'.$ref_modificada->nombre_proveedor.'</td><td>';
		$ref_modificada->vincularReferenciaProveedor();
		echo '</td><td>'.$ref_modificada->part_nombre.'</td><td style="text-align:center">'.number_format($ref_kit->piezas, 2, ',', '.').'</td><td style="text-align:center">'.number_format($ref_modificada->pack_precio, 2, ',', '.').'</td><td style="text-align:center">'.$ref_modificada->unidades.'</td><td style="text-align:center">'.$ref_modificada->total_paquetes.'</td><td style="text-align:center">'.number_format($precio_unidad, 2, ',', '.').'</td><td style="text-align:center">'.number_format($precio_referencia, 2, ',', '.').'</td></tr>';
		$precio_kit = $precio_kit + $precio_referencia;
	}
?>  
