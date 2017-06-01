<?php
// Este fichero muestra un popup con las referencias del componente, conteniendo tambien las referencias de los kits.
include("../includes/sesion.php");
include("../classes/basicos/componente.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/basicos/referencia_heredada.class.php");
include("../classes/basicos/referencia_componente.class.php");

$comp = new Componente();
$ref = new Referencia();
$ref_heredada = new Referencia_Heredada();
$ref_componente = new Referencia_Componente();

// Devuelve el tipo de componente: periferico o kit
$tipo = $_GET["tipo"]; 
$id	= $_GET["id"];

// Tenemos que comprobar el tipo de componente. 
// Si el componente es kit mostrará sus referencias.
// Si el componente es un periférico hay que comprobar si tiene kits.

// Obtenemos las referencias del componente
$ref_componente->dameReferenciasPorIdComponente($id);
$referencias_componente = $ref_componente->referencias_componente;

// Si el componente es un periférico tendremos que comprobar si tienen kits y añadir sus referencias a las referencias del componente
if($tipo == "periferico"){
	// Creamos un array auxiliar de las referencias del componente
	$referencias_aux = $referencias_componente;
	
	// Obtenemos ahora los kits del componente
	$ref_componente->dameIdsKitComponente($_GET["id"]);
	$ids_kits = $ref_componente->ids_kits;
	
	for($i=0;$i<count($ids_kits);$i++){
		// Obtenemos las referencias de ese kit 
		$ref_componente->dameReferenciasPorIdComponente($ids_kits[$i]["id_kit"]);
		$referencias_kit = $ref_componente->referencias_componente;
		if($referencias_kit != NULL){
			if($referencias_componente != NULL){
				$referencias_componente = $ref_componente->addReferenciasKitAlComponente($referencias_kit,$referencias_componente);
			}	
			else {
				$referencias_componente = $referencias_kit;
			}
		}
	}
}

// Preparamos el array final con el id_referencia y las piezas
for($i=0;$i<count($referencias_componente);$i++) {
	$referencias_componente_final[$i]["id_referencia"] = intval($referencias_componente[$i]["id_referencia"]);
	$referencias_componente_final[$i]["piezas"] = floatval($referencias_componente[$i]["piezas"]);
}

$referencias_componente_final_aux = $referencias_componente_final;

// Comprobamos si las referencias tienen heredadas y multiplicamos sus piezas
for($i=0;$i<count($referencias_componente_final);$i++){
	$raiz = $referencias_componente_final[$i]["id_referencia"];
	$piezas = $referencias_componente_final[$i]["piezas"];

	// Obtenemos el grafo ordenado por BFS (Anchura) y después todas las piezas necesarias de cada referencia
	$heredadas_por_nivel = $ref_heredada->dameTodasHeredadasNivel($raiz);
	$referencias_heredadas_referencia = $ref_heredada->dameTodasHeredadasPiezas($heredadas_por_nivel);

	// Si tiene heredadas las agrupamos al array de referencias final con sus piezas correspondientes
	if(!empty($referencias_heredadas_referencia)){
		$cont = 0;
		foreach($referencias_heredadas_referencia as $id_ref_heredada => $piezas_heredada){
			$array_piezas_heredadas[$cont]["id_referencia"] = $id_ref_heredada;
			$array_piezas_heredadas[$cont]["piezas"] = $piezas * $piezas_heredada;
			$cont++;
		}

		// Agrupamos las referencias heredadas al array final
		$referencias_componente_final_aux = $comp->agruparReferenciasComponentes($array_piezas_heredadas,$referencias_componente_final_aux);
		unset($array_piezas_heredadas);
	}
}

$referencias_componente_final = $referencias_componente_final_aux;

if(!empty($referencias_componente_final)) {
	// Ordenamos el array de referencias
	array_multisort($referencias_componente_final);
}

$max_caracteres = 50;
?>
<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
<div id="MuestraReferencias">
	<h1>Referencias asociadas a <?php echo $_GET["nombre"];?></h1>
	<div id="CapaTablaReferencias">
    	<table>
        <tr>
        	<th style="text-align:center">ID</th>
        	<th>NOMBRE</th>
        	<th>PROVEEDOR</th>
            <th>REF PROV</th>
        	<th style="text-align:center">PIEZAS</th>
            <th style="text-align:center">PRECIO PACK</th>
            <th style="text-align:center">UDS/P</th>
            <th style="text-align:center">TOTAL PAQS</th>
			<th style="text-align:center">PRECIO UNIDAD</th>
   			<th style="text-align:center">PRECIO</th> 
        </tr>

<?php
	// Por cada referencia del componente generamos la fila y codificamos los campos
	for($i=0;$i<count($referencias_componente_final);$i++){
		// De la tabla componentes_referencias solo nos interesa el campo piezas y el id_referencia. Los demas datos los obtenemos de la tabla referencias
		$id_referencia = $referencias_componente_final[$i]["id_referencia"];
		$total_piezas = $referencias_componente_final[$i]["piezas"];
		$ref->cargaDatosReferenciaId($id_referencia);
	
		// Tenemos que calcular el precio de la referencia 
		$unidades_paquete = $ref->unidades;
		$pack_precio = $ref->pack_precio;
		if (($total_piezas != 0) and ($unidades_paquete != 0)){
			$precio_referencia = ($total_piezas / $unidades_paquete) * $pack_precio;
		}
		else $precio_referencia = 0;
		
		// Calculamos el precio unitario
		if (($unidades_paquete != 0) and ($pack_precio != 0)){
			$precio_unidad = ($pack_precio / $unidades_paquete);	
		}
		else {
			$precio_unidad = 0;	
		}

		// Recalculamos los paquetes
		$ref->calculaTotalPaquetes($unidades_paquete,$total_piezas);
		$total_paquetes = $ref->total_paquetes;
	
	
		// Preparamos la codificacion de la referencia 
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
		$ref_prov_codificada = utf8_decode($ref->part_proveedor_referencia);
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
		} ?>

		<tr>
			<td style="text-align:center"><?php echo $ref->id_referencia; ?></td>
			<td>
				<?php 
					if(permisoMenu(3)){ ?>
						<a href="mod_referencia.php?id=<?php echo $ref->id_referencia; ?>" target="_blank">
				<?php
					}
					if (strlen($ref->referencia) > $max_caracteres) echo substr($ref->referencia,0,50).'...';
					else echo $ref->referencia;

					if(permisoMenu(3)){ ?>
                		</a>
                <?php
                	}
                ?>
            </td>
            <td>
				<?php 
					if (strlen($ref->nombre_proveedor) > $max_caracteres) echo substr($ref->nombre_proveedor,0,50).'...';
					else echo $ref->nombre_proveedor;
				?>
            </td>
            <td><?php $ref->vincularReferenciaProveedor(); ?></td>
            <td style="text-align:center"><?php echo number_format($total_piezas, 2, ',', '.');?></td>		
            <td style="text-align:center"><?php echo number_format($pack_precio, 2, ',', '.');?></td>
            <td style="text-align:center"><?php echo number_format($unidades_paquete, 2, ',', '.');?></td>				
            <td style="text-align:center"><?php echo number_format($total_paquetes, 2, ',', '.');?></td>
            <td style="text-align:center"><?php echo number_format($precio_unidad, 2, ',', '.');?></td>
            <td style="text-align:center"><?php echo number_format($precio_referencia, 2, ',', '.');?></td>		
        </tr>    	
<?php
	}
?>	
	</table>
    </div>
</div>    