<?php
// Este fichero muestra las referencias de la cabina en la confirmación de la creación de una Orden de Producción
include_once("../classes/basicos/referencia.class.php");   
include_once("../classes/basicos/referencia_componente.class.php");
include_once("../classes/basicos/listado_referencias_componentes.class.php");

$ref_modificada = new Referencia();
$ref_cab = new Referencia_Componente();
$ref_cabinas = new listadoReferenciasComponentes();

$ref_cabinas->setValores($id_cabina); 
$ref_cabinas->realizarConsulta();
$resultadosBusquedaCabinas = $ref_cabinas->referencias_componentes;
?>
<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
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
  $max_caracteres_ref = 50;
  $max_caracteres = 25;
	$precio_cabina = 0;
	for($i=0;$i<count($resultadosBusquedaCabinas);$i++) {
		$datoRef_Cabina = $resultadosBusquedaCabinas[$i];
		// Obtenemos los datos de las referencias de basicos
		$ref_cab->cargaDatosReferenciaComponenteId($datoRef_Cabina["id"]);
		// Hacemos la carga de la referencia que haya podido sufrir alguna modificación
		$ref_modificada->cargaDatosReferenciaId($ref_cab->id_referencia);
		// Recalculamos los paquetes
		$ref_cab->calculaTotalPaquetes($ref_modificada->unidades,$ref_cab->piezas);
		if($ref_modificada->pack_precio <> 0 and $ref_modificada->unidades <> 0) {
			$precio_unidad = $ref_modificada->pack_precio / $ref_modificada->unidades;
		} 
		else {
			$precio_unidad = 00;
		}
		$precio_referencia = $ref_cab->piezas * $precio_unidad;
		$precio_cabina = $precio_cabina + $precio_referencia;
?>

<tr>
	<td style="text-align:center"><?php echo $ref_cab->id_referencia; ?></td>
  	<td id="enlaceComposites">
  		<a href="../basicos/mod_referencia.php?id=<?php echo $ref_cab->id_referencia;?>" target="_blank"/>
  			<?php
				if (strlen($ref_modificada->referencia) > $max_caracteres_ref){
            		echo substr($ref_modificada->referencia,0,$max_caracteres_ref).'...'; 
            	}
            	else echo $ref_modificada->referencia;	 
        	?>		
  		</a>
  	</td>
	<td>
		<?php
			if (strlen($ref_modificada->nombre_proveedor) > $max_caracteres){
            	echo substr($ref_modificada->nombre_proveedor,0,$max_caracteres).'...'; 
            }
            else echo $ref_modificada->nombre_proveedor;	 
        ?>
	</td>
	<td><?php $ref_modificada->vincularReferenciaProveedor();?>
   	<td>
   		<?php
			if (strlen($ref_modificada->part_nombre) > $max_caracteres){
            	echo substr($ref_modificada->part_nombre,0,$max_caracteres).'...'; 
            }
            else echo $ref_modificada->part_nombre;	 
        ?>
    </td>
	  <td style="text-align:center"><?php echo number_format($ref_cab->piezas, 2, ',', '.');?></td>
    <td style="text-align:center"><?php echo number_format($ref_modificada->pack_precio, 2, ',', '.');?></td>
    <td style="text-align:center"><?php echo $ref_modificada->unidades; ?></td>
    <td style="text-align:center"><?php echo $ref_cab->total_paquetes; ?></td>
    <td style="text-align:center"><?php echo number_format($precio_unidad, 2, ',', '.');?></td>
    <td style="text-align:center"><?php echo number_format($precio_referencia, 2, ',', '.');?></td>
</tr>
<?php
	}
?>                