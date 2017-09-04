<?php
// Este fichero muestra el listado de las referencias
include("../includes/sesion.php");
include("../classes/basicos/listado_referencias.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/basicos/referencia_heredada.class.php");
include("../classes/funciones/funciones.class.php");
require("../funciones/pclzip/pclzip.lib.php");
permiso(1);

$ref = new Referencia();
$ref_heredada = new Referencia_Heredada();
$referencias = new listadoReferencias();
$funciones = new Funciones();

// Establecemos los parametros de la paginacion
// Número de registros a mostrar por página
$pg_registros = 50; 
$pg_pagina = $_GET["pg"];
if(empty($pg_pagina)) {
    $pg_inicio = 0;
    $pg_pagina = 1;
} 
else {
    $pg_inicio = ($pg_pagina - 1) * $pg_registros;
}
$paginacion = " limit ".$pg_inicio.', '.$pg_registros;

if($_GET["op"] == "descargar_documentacion") {
	include("../basicos/descargar_documentacion_referencias.php");
}

// Se obtienen los datos del formulario
if($_GET["ref"] == "creado" or $_GET["ref"] == "modificado" or $_GET["ref"] == "eliminado" or $_GET["op"] == "descargar_documentacion") {
	$realizarBusqueda = 1;
}
if(isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) {
	$mostrar_tabla = true;
	$referencia = addslashes($_GET["referencia"]);
	$proveedor = addslashes($_GET["proveedor"]);
	$ref_prov_pieza = addslashes($_GET["ref_prov_pieza"]);
	$fabricante = addslashes($_GET["fabricante"]);
	$ref_fab_pieza = addslashes($_GET["ref_fab_pieza"]);
	$nombre_pieza = addslashes($_GET["nombre_pieza"]);
	$tipo_pieza = addslashes($_GET["tipo_pieza"]);
	$part_value_name = addslashes($_GET["part_value_name"]);	
	$part_value_qty = addslashes($_GET["part_value_qty"]);
	$busqueda_magica = addslashes($_GET["busqueda_magica"]);
	$precio_pack = addslashes($_GET["precio_pack"]);
	$unidades_paquete = addslashes($_GET["unidades_paquete"]);
	$ordenar_referencias = $_GET["ordenar_referencias"];
	$fecha_desde = $_GET["fecha_desde"];
	$fecha_hasta = $_GET["fecha_hasta"];
	$id_referencia = addslashes($_GET["id_ref"]);

	if(!is_numeric($unidades_paquete)) $unidades_paquete = NULL;
	if(!is_numeric($precio_pack)) $precio_pack = NULL;
	if(!is_numeric($id_referencia)) $id_referencia = NULL;

	// Convierte la fecha a formato MySql
	if ($fecha_desde != "") $fecha_desde = $funciones->cFechaMy($fecha_desde);
	if ($fecha_hasta != "") $fecha_hasta = $funciones->cFechaMy($fecha_hasta);
	
	// Guardamos en una variable los campos para mostrarlos despues de la busqueda
	$busqueda_magica_ant = $busqueda_magica;
	$ref_prov_pieza_ant = $ref_prov_pieza;
	$ref_fab_pieza_ant = $ref_fab_pieza;	
	
	// Quitar guiones y espacios del campo de busqueda magica y referencias de proveedor y fabricante
	for($i=0;$i<strlen($busqueda_magica);$i++){
		if (($busqueda_magica[$i] == '-') or ($busqueda_magica[$i] == ' ')) $busqueda_magica[$i] = '%'; 
	}
	
	for($i=0;$i<strlen($ref_prov_pieza);$i++){
		if (($ref_prov_pieza[$i] == '-') or ($ref_prov_pieza[$i] == ' ')) $ref_prov_pieza[$i] = '%'; 	
	}
	
	for($i=0;$i<strlen($ref_fab_pieza);$i++){
		if (($ref_fab_pieza[$i] == '-') or ($ref_fab_pieza[$i] == ' ')) $ref_fab_pieza[$i] = '%'; 	
	}
	
	// Se pasan los datos del buscador a la clase del listado y se realiza la consulta a la base de datos
	$referencias->setValores($referencia,$proveedor,$ref_prov_pieza,$precio_pack,$fabricante,$ref_fab_pieza,$tipo_pieza,$part_value_name,$unidades_paquete,$nombre_pieza,$part_value_qty,$busqueda_magica,$ordenar_referencias,$fecha_desde,$fecha_hasta,$id_referencia,'');
	$referencias->realizarConsulta();
	$resultadosBusqueda = $referencias->referencias;
	$num_resultados = count($resultadosBusqueda); 

	// Se realiza la consulta con paginacion 
	$pg_totalPaginas = ceil(count($resultadosBusqueda) / $pg_registros);
	$referencias->setValores($referencia,$proveedor,$ref_prov_pieza,$precio_pack,$fabricante,$ref_fab_pieza,$tipo_pieza,$part_value_name,$unidades_paquete,$nombre_pieza,$part_value_qty,$busqueda_magica,$ordenar_referencias,$fecha_desde,$fecha_hasta,$id_referencia,$paginacion);
	$referencias->realizarConsulta();
	$resultadosBusqueda = $referencias->referencias;
		
	// Convierte la fecha a formato HTML
	if ($fecha_desde != "") $fecha_desde = $funciones->cFechaNormal($fecha_desde);
	if ($fecha_hasta != "") $fecha_hasta = $funciones->cFechaNormal($fecha_hasta);
	
	// Mostramos los valores iniciales de busqueda
	$busqueda_magica = $busqueda_magica_ant;
	$ref_prov_pieza = $ref_prov_pieza_ant;
	$ref_fab_pieza = $ref_fab_pieza_ant;
	// Volvemos a reasignar las variables de precio_pack y unidades_paquete en el caso de que su valor fuese NULL
	$id_referencia = $_GET["id_ref"];
	$precio_pack = $_GET["precio_pack"];
	$unidades_paquete = $_GET["unidades_paquete"]; 
	
	if($busqueda_magica != "") {
		$referencia = "";
		$proveedor = "";
		$ref_prov_pieza = "";
		$precio_pack = "";
		$fabricante = "";
		$ref_fab_pieza = "";
		$tipo_pieza = "";
		$part_value_name = "";
		$unidades_paquete = "";
		$nombre_pieza = "";
		$part_value_qty = "";
		$fecha_desde = "";
		$fecha_hasta = "";
		$id_referencia = "";
	}

	// Guardar las variables del formulario en variable de sesion
	$_SESSION["referencia_ref"] = stripslashes(htmlspecialchars($referencia));
	$_SESSION["proveedor_ref"] = stripslashes(htmlspecialchars($proveedor));
	$_SESSION["ref_prov_pieza_ref"] = stripslashes(htmlspecialchars($ref_prov_pieza));
	$_SESSION["fabricante_ref"] = stripslashes(htmlspecialchars($fabricante));
	$_SESSION["ref_fab_pieza_ref"] = stripslashes(htmlspecialchars($ref_fab_pieza));
	$_SESSION["nombre_pieza_ref"] = stripslashes(htmlspecialchars($nombre_pieza));
	$_SESSION["tipo_pieza_ref"] = stripslashes(htmlspecialchars($tipo_pieza));
	$_SESSION["part_value_name_ref"] = stripslashes(htmlspecialchars($part_value_name));	
	$_SESSION["part_value_qty_ref"] = stripslashes(htmlspecialchars($part_value_qty));
	$_SESSION["busqueda_magica_ref"] = stripslashes(htmlspecialchars($busqueda_magica));
	$_SESSION["precio_pack_ref"] = stripslashes(htmlspecialchars($precio_pack));
	$_SESSION["unidades_paquete_ref"] = stripslashes(htmlspecialchars($unidades_paquete)); 
	$_SESSION["ordenar_referencias_ref"] = $ordenar_referencias;
	$_SESSION["fecha_desde_ref"] = $fecha_desde;
	$_SESSION["fecha_hasta_ref"] = $fecha_hasta;	
	$_SESSION["id_referencia_ref"] = stripslashes(htmlspecialchars($id_referencia));	
}

$titulo_pagina = "Básicos > Referencias";
$pagina = "referencias";
include("../includes/header.php");
echo '<script type="text/javascript" src="../js/basicos/referencias_08032017_1050.js"></script>';
echo '<script type="text/javascript" src="../js/funciones_24052017_1515.js"></script>';
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>
    
    <h3>Referencias</h3>
    <h4>Buscar referencia</h4>
    
    <form id="BuscadorReferencias" name="buscadorReferencias" action="referencias.php" method="get" class="Buscador">
    <table style="border:0;">
    	<tr style="border:0;">
        	<td>
	            <div class="Label">Nombre</div>
    	        <input type="text" id="" name="referencia" class="BuscadorInput" value="<?php echo $_SESSION["referencia_ref"];?>"/>
            </td>
            <td>
        	    <div class="Label">Proveedor</div>
            	<input type="text" id="" name="proveedor" class="BuscadorInput" value="<?php echo $_SESSION["proveedor_ref"];?>"/>
            </td>
            <td>
            	<div class="Label">Ref. Proveedor Pieza</div>
            	<input type="text" id="" name="ref_prov_pieza" class="BuscadorInput" maxlength="50" value="<?php echo $_SESSION["ref_prov_pieza_ref"];?>"/>
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">Fabricante</div>
            	<input type="text" id="" name="fabricante" class="BuscadorInput" value="<?php echo	$_SESSION["fabricante_ref"];?>"/>
            </td>
            <td>
            	<div class="Label">Ref. Fabricante Pieza</div>
            	<input type="text" id="" name="ref_fab_pieza" class="BuscadorInput" maxlength="50" value="<?php echo $_SESSION["ref_fab_pieza_ref"];?>"/>
            </td>
            <td>
            	<div class="Label">Precio Pack</div>
            	<input type="text" id="" name="precio_pack" class="BuscadorInput" value="<?php echo	$_SESSION["precio_pack_ref"];?>"/>
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">Tipo de pieza</div>
           		<input type="text" id="" name="tipo_pieza" class="BuscadorInput" value="<?php echo $_SESSION["tipo_pieza_ref"];?>"/>
            </td>
            <td>
            	<div class="Label">Part value name</div>
          		<input type="text" id="" name="part_value_name" class="BuscadorInput" value="<?php echo $_SESSION["part_value_name_ref"]?>"/>
            </td>
            <td>
            	<div class="Label">Unidades por paquete</div>
          		<input type="text" id="" name="unidades_paquete" class="BuscadorInput" value="<?php echo $_SESSION["unidades_paquete_ref"];?>"/>	
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">Nombre de pieza</div>
          		<input type="text" id="" name="nombre_pieza" class="BuscadorInput" value="<?php echo $_SESSION["nombre_pieza_ref"];?>"/>
            </td>
            <td>
            	<div class="Label">Part value Qty</div>
            	<input type="text" id="" name="part_value_qty" class="BuscadorInput" value="<?php echo $_SESSION["part_value_qty_ref"];?>"/>
            </td>
            <td>
            	<div class="Label">ID Referencia</div>
            	<input type="text" id="" name="id_ref" class="BuscadorInput" value="<?php echo $_SESSION["id_referencia_ref"];?>" onkeypress="return soloNumeros(event)" onkeyup="cargaReferenciaIntro(event);"/>
            </td>

        </tr>
        
        <tr style="border:0;">
			<td><div class="LabelBusqueda">BUSQUEDA AVANZADA</div></td>
        </tr>
        
        <tr style="border:0;">
        	<td>
            	<div class="Label">Busqueda Mágica</div>
            	<input type="text" id="" name="busqueda_magica" class="BuscadorInput" maxlength="50" value="<?php echo $_SESSION["busqueda_magica_ref"];?>"/>
            </td>
        </tr>
        
        <tr style="border:0;">
			<td><div class="LabelBusqueda">ORDENAR</div></td>
        </tr>
        
        <tr style="border:0;">
        	<td>
            	<div class="Label">Ordenar por</div>
          		<select id="ordenar_referencias" name="ordenar_referencias" class="BuscadorInput"/>
            		<option value="0">Ordenar por...</option>
                	<option value="1">PRECIO</option>
                	<option value="2">PROVEEDOR</option>
                	<option value="3">FABRICANTE</option>
                	<option value="4">NOMBRE PIEZA</option>
                	<option value="5">TIPO PIEZA</option>
                	<option value="6">UNIDADES PAQUETE</option>
                	<option value="7">REF. PROVEEDOR</option>
                	<option value="8">REF. FABRICANTE</option>
                	<option value="9">ID REFERENCIA</option>                 
            	</select>
            </td>
            <td>
            	<div class="Label">Fecha desde</div>
                <input type="text" name="fecha_desde" id="datepicker_ref_desde" class="fechaCal" value="<?php echo $_SESSION["fecha_desde_ref"];?>"/>
            </td>
            <td>
            	<div class="Label">Fecha hasta</div>
                <input type="text" name="fecha_hasta" id="datepicker_ref_hasta" class="fechaCal" value="<?php echo $_SESSION["fecha_hasta_ref"];?>"/>
            </td>
		</tr>
        <tr style="border:0;">
        	<td colspan="3">
            	<input type="hidden" id="realizandoBusqueda" name="realizandoBusqueda" value="1" />
            	<input type="submit" id="" name="" class="" value="Buscar" />
            </td>
        </tr>
    </table>
    <br />
	<input type="hidden" id="nombreFormulario" name="nombreFormulario" value="BuscadorReferencias" />
    </form>
    
    <div class="ContenedorBotonCrear">
		<?php
		   	if($_GET["ref"] == "creado") {
		   		echo '<div class="mensaje">La referencia se ha creado correctamente</div>';
			}
		   	if($_GET["ref"] == "modificado") {
		  		echo '<div class="mensaje">La referencia se ha modificado correctamente</div>';
			}
		   	if($_GET["ref"] == "eliminado") {
		  		echo '<div class="mensaje">La referencia se ha eliminado correctamente</div>';
			}
			if($mostrar_tabla){
				if($num_resultados == NULL or $num_resultados == 0){
	               echo '<div class="mensaje">No se encontraron referencias</div>';
	               $mostrar_tabla = false;
	            }
	            else if ($num_resultados == 1){
	            	echo '<div class="mensaje">Se encontró 1 referencia</div>';
	            }
	            else{
	            	echo '<div class="mensaje">Se encontraron '.$num_resultados.' referencias</div>';
	            }	
        	}
		?>
    </div>
    
    <?php
		if($mostrar_tabla) {
			$max_caracteres_ref = 50;
			$max_caracteres = 25; ?>
			<div class="CapaTabla">
				<table>
        		<tr>
        			<th style="text-align:center">ID</th>
        			<th>NOMBRE</th>
            		<th>FABRIC.</th>
        			<th>PROVEEDOR</th>
                    <th style="text-align:center">LINK</th>                    
            		<th>NOMBRE PIEZA</th>
            		<th>TIPO PIEZA</th>
          			<th>REF. PROV.</th>
					<th style="text-align:center">DOC</th>
                    <th>REF. FABRIC.</th>
                    <th style="text-align:center">PACK PRECIO</th>
            		<th style="text-align:center">UND/PQ</th>
                    <?php 
                    	if(permisoMenu(4)){ ?>
                    		<th style="text-align:center">ELIMINAR</th>
                    <?php
                    	}
                    ?>
        		</tr>    
				<?php
					// Se cargan los datos de las referencias según su identificador
					for($i=0;$i<count($resultadosBusqueda);$i++) {
						$datoReferencia = $resultadosBusqueda[$i];
						$ref->cargaDatosReferenciaId($datoReferencia["id_referencia"]);
				?>
				<tr>
					<td style="text-align:center;"><?php echo $ref->id_referencia; ?></td>
					<td>
						<a href="mod_referencia.php?id=<?php echo $ref->id_referencia; ?>">
						<?php  
							if (strlen($ref->referencia) > $max_caracteres_ref){
								echo mb_strcut($ref->referencia, 0, 50, "UTF-8").'...';
							}
							else {
								echo $ref->referencia;	
							}
						?>
						</a>
					</td>
					<td><?php echo $ref->nombre_fabricante;?></td>
					<td>
						<a href="proveedores.php"><?php echo $ref->nombre_proveedor;?></a>    
					</td>
                    <td style="text-align:center">
                       	<?php 
							if ($ref->proveedor == 1) { ?>
                          		<a href="http://es.rs-online.com/web/c/?searchTerm=<?php echo $ref->part_proveedor_referencia;?>" target="_blank">web</a>
                        <?php
							}
						    elseif ($ref->proveedor == 2) { ?>
                          		<a href="http://es.farnell.com/webapp/wcs/stores/servlet/Search?catalogId=15001&langId=-5&storeId=10176&gs=true&st=<?php echo $ref->part_proveedor_referencia;?>" target="_blank">web</a>
                        <?php 
							}
						?>
				  	</td>
					<td>
						<?php 
 							if (strlen($ref->part_nombre) > $max_caracteres){
								echo mb_strcut($ref->part_nombre, 0, 25, "UTF-8").'...';
							}
							else {
								echo $ref->part_nombre;	
							}
						?>
                    </td>
					<td>
						<?php 
							if (strlen($ref->part_tipo) > $max_caracteres){
								echo mb_strcut($ref->part_tipo, 0, 25, "UTF-8").'...';
							}
							else {
								echo $ref->part_tipo;	
							}
						?>
					</td>
					<td>
						<?php 
							if (strlen($ref->part_proveedor_referencia) > $max_caracteres){
								echo mb_strcut($ref->part_proveedor_referencia, 0, 25, "UTF-8").'...';
							}
							else {
								echo $ref->part_proveedor_referencia;	
							}
						?>
					</td>
					<td style="text-align: center;">
						<?php
							// Obtenemos las referencias heredadas y descendecia de la referencia
							$res_heredadas = $ref_heredada->dameTodasHeredadas($ref->id_referencia);
							$array_todas_referencias[]["id_referencia"] = $ref->id_referencia;
							for($j=0;$j<count($res_heredadas);$j++){
								$id_ref_heredada = $res_heredadas[$j]["id_ref_heredada"];
								$array_todas_referencias[]["id_referencia"] = $id_ref_heredada;
							}
							$tiene_archivos = $ref->tieneDocumentacionAdjuntaReferencias($array_todas_referencias);
							if($tiene_archivos) { ?>
								<a href="#" onclick="descargar_documentacion(<?php echo $ref->id_referencia;?>)"><img src="../images/download_icon.jpg" style="vertical-align: middle;" /></a>
						<?php
							}
							unset($array_todas_referencias);
						?>
					</td>
                    <td>
						<?php 
							if (strlen($ref->part_fabricante_referencia) > $max_caracteres){
								echo mb_strcut($ref->part_fabricante_referencia, 0, 25, "UTF-8").'...';
							}
							else {
								echo $ref->part_fabricante_referencia;	
							}
						?>
					</td>
                    <td style="text-align:center"><?php echo number_format($ref->pack_precio, 2, ',', '.'); ?></td>
                    <td style="text-align:center"><?php echo $ref->unidades; ?></td>
                    <?php 
                    	if (permisoMenu(4)){
                    ?>
                    <td style="text-align:center">
	                   	<input type="button" id="menos" name="menos" value="ELIMINAR" style="font-size: 9px;" class="BotonEliminar" onclick="javascript: if (confirm('¿Desea eliminar la referencia?')) { window.location.href='elim_referencia.php?id=<?php echo $ref->id_referencia;?>&referencia_buscador=<?php echo $_SESSION["referencia_ref"];?>&proveedor_buscador=<?php echo $_SESSION["proveedor_ref"];?>&ref_prov_pieza_buscador=<?php echo $_SESSION["ref_prov_pieza_ref"];?>&precio_pack_buscador=<?php echo $_SESSION["precio_pack_ref"];?>&fabricante_buscador=<?php echo $_SESSION["fabricante_ref"];?>&ref_fab_pieza_buscador=<?php echo $_SESSION["ref_fab_pieza_ref"];?>&tipo_pieza_buscador=<?php echo $_SESSION["tipo_pieza_ref"];?>&part_value_name_buscador=<?php echo $_SESSION["part_value_name_ref"];?>&unidades_paquete_buscador=<?php echo $_SESSION["unidades_paquete_ref"];?>&nombre_pieza_buscador=<?php echo $_SESSION["nombre_pieza_ref"];?>&part_value_qty_buscador=<?php echo $_SESSION["part_value_qty_ref"];?>&busqueda_magica_buscador=<?php echo $_SESSION["busqueda_magica_ref"];?>&ordenar_referencias_buscador=<?php echo $_SESSION["ordenar_referencias_ref"];?>&fecha_desde_buscador=<?php echo $_SESSION["fecha_desde_ref"];?>&fecha_hasta_buscador=<?php echo $_SESSION["fecha_hasta_ref"];?>&id_referencia_buscador=<?php echo $_SESSION["id_referencia_ref"];?>' } else { void('') };" /> 
                    </td>
                    <?php
                    	}
                    ?>
				</tr> 
				<?php
					}
				?>
				</table>                  
			</div>
	<?php
		// PAGINACIÓN
        if((isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) and $resultadosBusqueda != NULL) { ?>
        	<div style="font: bold 11px Verdana,Arial; margin: 0 auto; padding: 10px 0; width: 350px; text-align: center;"> 
	        <?php    
	        	if(($pg_pagina - 1) > 0) { ?>
	            	<a href="referencias.php?pg=1&realizandoBusqueda=1&busqueda_magica=<?php echo $_SESSION["busqueda_magica_ref"];?>&referencia=<?php echo $_SESSION["referencia_ref"];?>&proveedor=<?php echo $_SESSION["proveedor_ref"];?>&ref_prov_pieza=<?php echo $_SESSION["ref_prov_pieza_ref"];?>&fabricante=<?php echo $_SESSION["fabricante_ref"];?>&ref_fab_pieza=<?php echo $_SESSION["ref_fab_pieza_ref"];?>&nombre_pieza=<?php echo $_SESSION["nombre_pieza_ref"];?>&tipo_pieza=<?php echo $_SESSION["tipo_pieza_ref"];?>&part_value_name=<?php echo $_SESSION["part_value_name_ref"];?>&part_value_qty=<?php echo $_SESSION["part_value_qty_ref"];?>&precio_pack=<?php echo $_SESSION["precio_pack_ref"];?>&unidades_paquete=<?php echo $_SESSION["unidades_paquete_ref"];?>&ordenar_referencias=<?php echo $_SESSION["ordenar_referencias_ref"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_ref"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_ref"];?>&id_referencia=<?php echo $_SESSION["id_referencia_ref"];?>">Primera&nbsp&nbsp&nbsp</a>
	                <a href="referencias.php?pg=<?php echo $pg_pagina - 1;?>&realizandoBusqueda=1&busqueda_magica=<?php echo $_SESSION["busqueda_magica_ref"];?>&referencia=<?php echo $_SESSION["referencia_ref"];?>&proveedor=<?php echo $_SESSION["proveedor_ref"];?>&ref_prov_pieza=<?php echo $_SESSION["ref_prov_pieza_ref"];?>&fabricante=<?php echo $_SESSION["fabricante_ref"];?>&ref_fab_pieza=<?php echo $_SESSION["ref_fab_pieza_ref"];?>&nombre_pieza=<?php echo $_SESSION["nombre_pieza_ref"];?>&tipo_pieza=<?php echo $_SESSION["tipo_pieza_ref"];?>&part_value_name=<?php echo $_SESSION["part_value_name_ref"];?>&part_value_qty=<?php echo $_SESSION["part_value_qty_ref"];?>&precio_pack=<?php echo $_SESSION["precio_pack_ref"];?>&unidades_paquete=<?php echo $_SESSION["unidades_paquete_ref"];?>&ordenar_referencias=<?php echo $_SESSION["ordenar_referencias_ref"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_ref"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_ref"];?>&id_referencia=<?php echo $_SESSION["id_referencia_ref"];?>"> Anterior</a>
	        <?php  
	            }  
	            else {
	            	echo 'Primera&nbsp;&nbsp;&nbsp;Anterior';
	            }
	        
	           	echo ' &nbsp;&nbsp;&nbsp;['.$pg_pagina.' / '.$pg_totalPaginas.']&nbsp;&nbsp;&nbsp;';
	           	if($pg_pagina < $pg_totalPaginas) { ?>
					<a href="referencias.php?pg=<?php echo $pg_pagina + 1;?>&realizandoBusqueda=1&busqueda_magica=<?php echo $_SESSION["busqueda_magica_ref"];?>&referencia=<?php echo $_SESSION["referencia_ref"];?>&proveedor=<?php echo $_SESSION["proveedor_ref"];?>&ref_prov_pieza=<?php echo $_SESSION["ref_prov_pieza_ref"];?>&fabricante=<?php echo $_SESSION["fabricante_ref"];?>&ref_fab_pieza=<?php echo $_SESSION["ref_fab_pieza_ref"];?>&nombre_pieza=<?php echo $_SESSION["nombre_pieza_ref"];?>&tipo_pieza=<?php echo $_SESSION["tipo_pieza_ref"];?>&part_value_name=<?php echo $_SESSION["part_value_name_ref"];?>&part_value_qty=<?php echo $_SESSION["part_value_qty_ref"];?>&precio_pack=<?php echo $_SESSION["precio_pack_ref"];?>&unidades_paquete=<?php echo $_SESSION["unidades_paquete_ref"];?>&ordenar_referencias=<?php echo $_SESSION["ordenar_referencias_ref"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_ref"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_ref"];?>&id_referencia=<?php echo $_SESSION["id_referencia_ref"];?>">Siguiente&nbsp&nbsp&nbsp</a>
					<a href="referencias.php?pg=<?php echo $pg_totalPaginas; ?>&realizandoBusqueda=1&busqueda_magica=<?php echo $_SESSION["busqueda_magica_ref"];?>&referencia=<?php echo $_SESSION["referencia_ref"];?>&proveedor=<?php echo $_SESSION["proveedor_ref"];?>&ref_prov_pieza=<?php echo $_SESSION["ref_prov_pieza_ref"];?>&fabricante=<?php echo $_SESSION["fabricante_ref"];?>&ref_fab_pieza=<?php echo $_SESSION["ref_fab_pieza_ref"];?>&nombre_pieza=<?php echo $_SESSION["nombre_pieza_ref"];?>&tipo_pieza=<?php echo $_SESSION["tipo_pieza_ref"];?>&part_value_name=<?php echo $_SESSION["part_value_name_ref"];?>&part_value_qty=<?php echo $_SESSION["part_value_qty_ref"];?>&precio_pack=<?php echo $_SESSION["precio_pack_ref"];?>&unidades_paquete=<?php echo $_SESSION["unidades_paquete_ref"];?>&ordenar_referencias=<?php echo $_SESSION["ordenar_referencias_ref"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_ref"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_ref"];?>&id_referencia=<?php echo $_SESSION["id_referencia_ref"];?>">Última</a>
	        <?php        
				} 
        	    else {
            		echo 'Siguiente&nbsp;&nbsp;&nbsp;Última'; 
            	}
		   	?>
        	</div>
        	<br/>
   	<?php
       	}
    }
?>
</div>
<?php include ("../includes/footer.php"); ?>