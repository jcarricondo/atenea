<?php
// Este fichero modifica una referencia de basicos
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/proveedor.class.php");
include("../classes/basicos/referencia.class.php");
// include("../classes/basicos/cabina.class.php");
include("../classes/basicos/periferico.class.php");
include("../classes/basicos/kit.class.php");
include("../classes/basicos/fabricante.class.php");
include("../classes/basicos/listado_proveedores.class.php");
include("../classes/basicos/listado_fabricantes.class.php");
include("../classes/basicos/componente.class.php");
include("../classes/log/basicos/log_basicos_referencias.class.php");
permiso(34);

// Comprobamos si el usuario puede modificar el basico
if(!permisoMenu(3)){ 
    $modificar = false;
    $solo_lectura = 'readonly="readonly"';
}
else {
    $modificar = true;
    $solo_lectura = '';
}

$error = false;
$bbdd = new MySQL;
$db = new MySQL();
$referencias = new Referencia();
$ref = new Referencia();
$ref_archivo = new Referencia();
// $cabina = new Cabina();
$periferico = new Periferico();
$kit = new Kit();
$fab = new Fabricante();
$prov = new Proveedor();
$nf = new listadoFabricantes();
$np = new listadoProveedores();
$comp = new Componente();
$validacion = new Funciones();
$log = new LogBasicosReferencias();

if(isset($_POST["guardandoReferencia"]) and $_POST["guardandoReferencia"] == 1) {
	// Se reciben los datos
	$nombre = $_POST["nombre"];
	$fabricante = $_POST["fabricante"];
	$proveedor = $_POST["proveedor"];
	$nombre_pieza = $_POST["nombre_pieza"];
	$tipo_pieza = $_POST["tipo_pieza"];
	$ref_prov_pieza = $_POST["ref_prov_pieza"];
	$ref_fab_pieza = $_POST["ref_fab_pieza"];
	$part_value_name = $_POST["part_value_name"];
	$part_value_qty = $_POST["part_value_qty"];
	$part_value_name_2 = $_POST["part_value_name_2"];
	$part_value_qty_2 = $_POST["part_value_qty_2"];
	$part_value_name_3 = $_POST["part_value_name_3"];
	$part_value_qty_3 = $_POST["part_value_qty_3"];
	$part_value_name_4 = $_POST["part_value_name_4"];
	$part_value_qty_4 = $_POST["part_value_qty_4"];	
	$part_value_name_5 = $_POST["part_value_name_5"];
	$part_value_qty_5 = $_POST["part_value_qty_5"];
	$pack_precio = $_POST["pack_precio"];
	$unidades_paquete = $_POST["unidades_paquete"];
	$id_referencia = $_GET["id"];
	$archivos_tabla = $_POST["archivos_tabla"];
	$comentarios = $_POST["comentarios"];
	
	if ($nombre == '') $nombre = '-';
	if ($nombre_pieza == '') $nombre_pieza = '-';
	if ($tipo_pieza == '') $tipo_pieza = '-';

	if (($ref_prov_pieza == '') or ($ref_fab_pieza == '') or ($pack_precio == '') or ($unidades_paquete == '') ){
		echo '<script type="text/javascript">alert("Rellene los campos obligatorios")</script>';
		$error = true;
	}
	elseif (!$validacion->verificarSoloNumeros($pack_precio) && $pack_precio != 0) {
		echo '<script type="text/javascript">alert("El precio del pack introducido no es correcto")</script>';
		$error = true;
	}
	elseif (!$validacion->verificarSoloDigitos($unidades_paquete)) {
		echo '<script type="text/javascript">alert("El valor de unidades introducido no es correcto")</script>';
		$error = true;
	}
	else {
		// Consulta para comprobar que los nombres de la tabla son los mismos que los de la base de datos
		// Comprobamos que los nombres de la base de datos estan en archivos_tabla. Si alguno no esta ponemos su activo a cero.
		$referencias->dameNombres_archivos($id_referencia);
		$nombres = $referencias->nombres_archivos;
		// Los guardamos en un array simple
		for ($i=0;$i<count($nombres);$i++) {
			$nombres_bbdd[]= $nombres[$i]["nombre_archivo"]; 	
		}
		// Comprobamos que los archivos de la base de datos estan en archivos tabla. Si alguno no esta, pondemos su activo a cero.
		for ($i=0;$i<count($nombres_bbdd);$i++) {
			$encontrado = false;
			$j=0;
			while (($j<count($archivos_tabla)) and (!$encontrado)) {
				$encontrado = $archivos_tabla[$j] == $nombres_bbdd[$i];
				$j++;
			}
			// Si no esta en la tabla es que se ha eliminado y por tanto procedemos a poner su activo a 0;
			if (!$encontrado) {
				$referencias->quitarArchivo($nombres_bbdd[$i],$id_referencia);
				if ($resultado != 1) {
					$mensaje_error = $referencias->getErrorMessage($resultado);
				}
			}
		}
			
		if (isset ($_FILES["archivos"])) {
			// Comprobamos que se han adjuntado nuevos archivos
			// Si hay archivos, los subimos.
			$i=0;
			$error_archivo = 0;
			while (($i<count($_FILES["archivos"]["name"])) and ($error_archivo == 0)) {
				if ($_FILES["archivos"]["name"][$i] != NULL) {
					$uploaddir = "uploads/"; 
					$nombre_archivo[] = rand(0,100).rand(0,100).'_'.basename($_FILES['archivos']['name'][$i]);
					$uploadfile = $uploaddir . $nombre_archivo[$i]; 
					$error_archivo = $_FILES['archivos']['error'][$i]; 
					$subido = false;
					if($error_archivo == UPLOAD_ERR_OK) {
						$subido = copy($_FILES['archivos']['tmp_name'][$i], $uploadfile);
						//$subido = copy($uploadfile, $uploadfile);
					}
				}
				$i++;
			}
									
			$referencias->datosNuevaReferencia($id_referencia,$nombre,$fabricante,$proveedor,$nombre_pieza,$tipo_pieza,$ref_prov_pieza,$ref_fab_pieza,$part_value_name,$part_value_qty,$part_value_name_2,$part_value_qty_2,$part_value_name_3,$part_value_qty_3,$part_value_name_4,$part_value_qty_4,$part_value_name_5,$part_value_qty_5,$pack_precio,$unidades_paquete,$nombre_archivo,$comentarios);
			$resultado = $referencias->guardarCambios();
			if($resultado == 1) {
				$error = false;
				// Obtenemos los datos de la tabla componentes_referencias que tengan esa referencia
				$datos_componentes_referencias = $referencias->dameComponentesReferencias($id_referencia);

				for($i=0; $i<count($datos_componentes_referencias);$i++){
					$id = $datos_componentes_referencias[$i]["id"];
					$piezas_componente = $datos_componentes_referencias[$i]["piezas"];
	
					// Recalculamos los paquetes con las unidades_paquete modificadas
					$referencias->calculaTotalPaquetes($unidades_paquete,$piezas_componente);
					$total_paquetes = $referencias->total_paquetes;

					// Actualizamos (segun el ID) el pack_precio, las unidades_paquete, el total_paquetes y la fecha de creacion de la tabla componentes_referencias
					$resultado = $referencias->actualizarComponentesReferencias($id,$unidades_paquete,$total_paquetes,$pack_precio);	

					if($resultado != 1){
						$error = true;
						$i = count($datos_componentes_referencias); 
					}
				}
				if (!$error){
					// Guardamos el log de la operación
					$referencias->cargaDatosReferenciaId($id_referencia);
					$fecha_creado = $referencias->fecha_creado;

					$id_usuario = $_SESSION["AT_id_usuario"];
					$proceso = "MODIFICACION REFERENCIA";
					$descripcion = "-";
					$referencia_creada = "NO";
					$referencia_heredada = "NO";
					$referencia_compatible = "NO";
					$error = "NO";
					$codigo_error = "OK!";

					$log->setValores($id_usuario,$proceso,$id_referencia,$nombre,$proveedor,$fabricante,$tipo_pieza,$nombre_pieza,$ref_fab_pieza,$ref_prov_pieza,
							$descripcion,$part_value_name,$part_value_qty,$part_value_name_2,$part_value_qty_2,$part_value_name_3,$part_value_qty_3,$part_value_name_4,
							$part_value_qty_4,$part_value_name_5,$part_value_qty_5,$pack_precio,$unidades_paquete,NULL,$comentarios,$fecha_creado,$fecha_modificacion,$referencia_creada,
							$referencia_heredada,$referencia_compatible,$error,$codigo_error);

					$res_log = $log->guardarLog();
					if ($res_log == 0) echo '<script>alert("Se ha producido un error al guardar el log de la operación")</script>';
					header("Location: referencias.php?ref=modificado");
				}
				else{
					$mensaje_error = $referencias->getErrorMessage($resultado);

					// Guardamos el log de la operación
					$referencias->cargaDatosReferenciaId($id_referencia);
					$fecha_creado = $referencias->fecha_creado;

					$id_usuario = $_SESSION["AT_id_usuario"];
					$proceso = "MODIFICACION REFERENCIA";
					$descripcion = "-";
					$referencia_creada = "NO";
					$referencia_heredada = "NO";
					$referencia_compatible = "NO";
					$error = "SI";
					$codigo_error = $mensaje_error;

					$log->setValores($id_usuario,$proceso,$id_referencia,$nombre,$proveedor,$fabricante,$tipo_pieza,$nombre_pieza,$ref_fab_pieza,$ref_prov_pieza,
							$descripcion,$part_value_name,$part_value_qty,$part_value_name_2,$part_value_qty_2,$part_value_name_3,$part_value_qty_3,$part_value_name_4,
							$part_value_qty_4,$part_value_name_5,$part_value_qty_5,$pack_precio,$unidades_paquete,NULL,$comentarios,$fecha_creado,$fecha_modificacion,$referencia_creada,
							$referencia_heredada,$referencia_compatible,$error,$codigo_error);

					$res_log = $log->guardarLog();
					if ($res_log == 0) echo '<script>alert("Se ha producido un error al guardar el log de la operación")</script>';
				}
			}
			else {
				$mensaje_error = $referencias->getErrorMessage($resultado);	
			} 
		}
	}
} 

// Se cargan los datos buscando por el ID
$referencias->cargaDatosReferenciaId($_GET["id"]);
if (!$error) {
	$fabricante = $referencias->fabricante;
	$proveedor = $referencias->proveedor;
}
$id_referencia = $referencias->id_referencia;
$nombre = htmlspecialchars($referencias->referencia);
$ref_prov_pieza = htmlspecialchars($referencias->part_proveedor_referencia);
$ref_fab_pieza = htmlspecialchars($referencias->part_fabricante_referencia);
$nombre_pieza = htmlspecialchars($referencias->part_nombre);
$tipo_pieza = htmlspecialchars($referencias->part_tipo);
$part_value_name = htmlspecialchars($referencias->part_valor_nombre);
$part_value_qty = htmlspecialchars($referencias->part_valor_cantidad);
$part_value_name_2 = htmlspecialchars($referencias->part_valor_nombre_2);
$part_value_qty_2 = htmlspecialchars($referencias->part_valor_cantidad_2);
$part_value_name_3 = htmlspecialchars($referencias->part_valor_nombre_3);
$part_value_qty_3 = htmlspecialchars($referencias->part_valor_cantidad_3);
$part_value_name_4 = htmlspecialchars($referencias->part_valor_nombre_4);
$part_value_qty_4 = htmlspecialchars($referencias->part_valor_cantidad_4);
$part_value_name_5 = htmlspecialchars($referencias->part_valor_nombre_5);
$part_value_qty_5 = htmlspecialchars($referencias->part_valor_cantidad_5);
$nombre_proveedor = htmlspecialchars($referencias->nombre_proveedor);
$nombre_fabricante = htmlspecialchars($referencias->nombre_fabricante);
$pack_precio = $referencias->pack_precio;
$unidades_paquete = $referencias->unidades; 
$comentarios = htmlspecialchars($referencias->comentarios);

// Titulo de pagina
$titulo_pagina = "B&aacutesico > Modifica Referencia";
$pagina = "mod_referencia";
include ('../includes/header.php');
echo '<script type="text/javascript" src="../js/basicos/mod_referencia.js"></script>';
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
 	</div>
      
    <h3> Modificacion de referencia </h3>
    <form id="FormularioCreacionBasico" name="modificarReferencia" action="mod_referencia.php?id=<?php echo $referencias->id_referencia; ?>" method="post" enctype="multipart/form-data">
    	<br />
        <h5> Modifique los datos en el siguiente formulario </h5>
        <div class="ContenedorCamposCreacionBasico">
	      	<div class="LabelCreacionBasico">ID referencia</div>
    	    <input type="text" id="id_ref" name="id_ref" class="CreacionBasicoInput" readonly="readonly" value="<?php echo $id_referencia;?>" />
        </div>
    	<div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Nombre *</div>
            <input type="text" id="nombre" name="nombre" class="CreacionBasicoInput" value="<?php echo $nombre;?>" <?php echo $solo_lectura; ?> />
        </div>    
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Fabricante *</div>
           	<?php 
           		$nf->prepararConsulta();
				$nf->realizarConsulta();
				$resultado_fabricantes = $nf->fabricantes;

                if($modificar){ ?>
		            <select id="fabricante" name="fabricante"  class="CreacionBasicoInput">
		            	<?php 
							for($i=0;$i<count($resultado_fabricantes);$i++) {
								$datoFabricante = $resultado_fabricantes[$i];
								$fab->cargaDatosFabricanteId($datoFabricante["id_fabricante"]);
								echo '<option value="'.$fab->id_fabricante.'"';if ($fab->id_fabricante == $fabricante) { echo 'selected="selected"'; } echo '>'.$fab->nombre.'</option>';
		                    }
						?>
		            </select>
			<?php 
				}
				else { 
					for($i=0;$i<count($resultado_fabricantes);$i++) {
						$datoFabricante = $resultado_fabricantes[$i];
						$fab->cargaDatosFabricanteId($datoFabricante["id_fabricante"]);
						if ($fab->id_fabricante == $fabricante) { ?>
							<input type="text" id="fabricante" name="fabricante" class="CreacionBasicoInput" value="<?php echo $fab->nombre;?>" <?php echo $solo_lectura; ?> />
					<?php			
						}
					}
				}	
			?>		   
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Proveedor *</div>
           	<?php 
           		$np->prepararConsulta();
				$np->realizarConsulta();
				$resultado_proveedores = $np->proveedores;

				if($modificar){ ?>
		           	<select id="proveedor" name="proveedor"  class="CreacionBasicoInput">
            			<?php 
            				for($i=0;$i<count($resultado_proveedores);$i++) {
								$datoProveedor = $resultado_proveedores[$i];
								$prov->cargaDatosProveedorId($datoProveedor["id_proveedor"]);
								if($prov->nombre != "0 - SIN ESPECIFICAR"){
									echo '<option value="'.$prov->id_proveedor.'"';if ($prov->id_proveedor == $proveedor) { echo 'selected="selected"'; } echo '>'.$prov->nombre.'</option>';
								}	
                    		}
						?>
            		</select>
            <?php 
            	}
            	else { 
            		for($i=0;$i<count($resultado_proveedores);$i++) {
						$datoProveedor = $resultado_proveedores[$i];
						$prov->cargaDatosProveedorId($datoProveedor["id_proveedor"]);
						if($prov->id_proveedor == $proveedor) { ?>
							<input type="text" id="proveedor" name="proveedor" class="CreacionBasicoInput" value="<?php echo $prov->nombre;?>" <?php echo $solo_lectura; ?> />	
					<?php 
						}
					}	
				}	
            ?>		
        </div>
        <div class="ContenedorCamposCreacionBasico">
	      	<div class="LabelCreacionBasico">Nombre pieza *</div>
    	    <input type="text" id="nombre_pieza" name="nombre_pieza" class="CreacionBasicoInput" value="<?php echo $nombre_pieza;?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
	      	<div class="LabelCreacionBasico">Tipo pieza *</div>
    	    <input type="text" id="tipo_pieza" name="tipo_pieza" class="CreacionBasicoInput" <?php if ($tipo_pieza == "ORDENADOR" || !$modificar) echo ' readonly="readonly" ';?> value="<?php echo $tipo_pieza;?>" />
    	    <input type="hidden" id="tipo_pieza_ant" name="tipo_pieza_ant" value="<?php echo $tipo_pieza;?>" />
           	<?php 
                if($modificar){ ?>
		           	<div class="LabelCreacionBasicoPCRef">PC</div>
		            <input type="checkbox" id="es_ordenador" name="es_ordenador" class="CreacionBasicoInputPCRef" <?php if ($tipo_pieza == "ORDENADOR") echo ' checked="checked" ';?> onclick="rellenarTipoPieza()" />
        	<?php 
        		}
        	?>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Ref. Proveedor Pieza *</div>
          	<input type="text" id="ref_prov_pieza" name="ref_prov_pieza" class="CreacionBasicoInput" value="<?php echo $ref_prov_pieza;?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
	      	<div class="LabelCreacionBasico">Ref. Fabricante Pieza *</div>
    	    <input type="text" id="ref_fab_pieza" name="ref_fab_pieza" class="CreacionBasicoInput" value="<?php echo $ref_fab_pieza;?>" <?php echo $solo_lectura; ?> />
        </div>
         <div class="ContenedorCamposCreacionBasico">
	      	<div class="LabelCreacionBasico">Esp. Nombre</div>
    	    <input type="text" id="part_value_name" name="part_value_name" class="CreacionBasicoInput" value="<?php echo $part_value_name;?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
	      	<div class="LabelCreacionBasico">Esp. Valor</div>
    	    <input type="text" id="part_value_qty" name="part_value_qty" class="CreacionBasicoInput" value="<?php echo $part_value_qty;?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">   
           	<div class="LabelCreacionBasico">Esp. Nombre 2</div>
        	<input type="text" id="part_value_name_2" name="part_value_name_2" class="CreacionBasicoInput" value="<?php echo $part_value_name_2;?>" <?php echo $solo_lectura; ?> />
        </div>    
        <div class="ContenedorCamposCreacionBasico">    
           	<div class="LabelCreacionBasico">Esp. Valor 2</div>
          	<input type="text" id="part_value_qty_2" name="part_value_qty_2" class="CreacionBasicoInput" value="<?php echo $part_value_qty_2;?>" <?php echo $solo_lectura; ?> />	
        </div>
        <div class="ContenedorCamposCreacionBasico">   
           	<div class="LabelCreacionBasico">Esp. Nombre 3</div>
        <input type="text" id="part_value_name_3" name="part_value_name_3" class="CreacionBasicoInput" value="<?php echo $part_value_name_3;?>" <?php echo $solo_lectura; ?> />
        </div>    
        <div class="ContenedorCamposCreacionBasico">    
           	<div class="LabelCreacionBasico">Esp. Valor 3 </div>
          	<input type="text" id="part_value_qty_3" name="part_value_qty_3" class="CreacionBasicoInput" value="<?php echo $part_value_qty_3;?>" <?php echo $solo_lectura; ?> />	
        </div>
        <div class="ContenedorCamposCreacionBasico">   
           	<div class="LabelCreacionBasico">Esp. Nombre 4</div>
        <input type="text" id="part_value_name_4" name="part_value_name_4" class="CreacionBasicoInput" value="<?php echo $part_value_name_4;?>" <?php echo $solo_lectura; ?> />
        </div>    
        <div class="ContenedorCamposCreacionBasico">    
           	<div class="LabelCreacionBasico">Esp. Valor 4</div>
          	<input type="text" id="part_value_qty_4" name="part_value_qty_4" class="CreacionBasicoInput" value="<?php echo $part_value_qty_4;?>" <?php echo $solo_lectura; ?> />	
        </div>
        <div class="ContenedorCamposCreacionBasico">   
           	<div class="LabelCreacionBasico">Esp. Nombre 5</div>
        <input type="text" id="part_value_name_5" name="part_value_name_5" class="CreacionBasicoInput" value="<?php echo $part_value_name_5;?>" <?php echo $solo_lectura; ?> />
        </div>    
        <div class="ContenedorCamposCreacionBasico">    
           	<div class="LabelCreacionBasico">Esp. Valor 5</div>
          	<input type="text" id="part_value_qty_5" name="part_value_qty_5" class="CreacionBasicoInput" value="<?php echo $part_value_qty_5;?>" <?php echo $solo_lectura; ?> />	
        </div>
        <div class="ContenedorCamposCreacionBasico">
	      	<div class="LabelCreacionBasico">Precio pack</div>
    	    <input type="text" id="pack_precio" name="pack_precio" class="CreacionBasicoInput" value="<?php echo $pack_precio; ?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
	      	<div class="LabelCreacionBasico">Unidades por paquete</div>
    	    <input type="text" id="unidades_paquete" name="unidades_paquete" class="CreacionBasicoInput" value="<?php echo $unidades_paquete;?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">    
           	<div class="LabelCreacionBasico">Comentarios</div>
          	<textarea type="text" id="comentarios" name="comentarios" rows="10" class="textareaInput" <?php echo $solo_lectura; ?> ><?php echo $comentarios; ?></textarea>	
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<?php 
                if($modificar){ ?>
		           	<div class="LabelCreacionBasico">Archivos adjuntos</div>
		            <div id="AñadirMasArchivos"><a href="#" onClick="addCampo()" class="SubirArchivo">Subir otro archivo</a></div>
		    <?php 
		    	}
		   	?>
        </div> 
        <div id="adjuntos"> 
        	<?php 
                if($modificar){ ?>
		        	<div class="ContenedorCamposAdjuntar">
		        		<input type="file" id="archivos[]" name="archivos[]" class="BotonAdjuntar"/>  
		        	</div>
		    <?php 
		    	}
		    ?>
        </div>
        
		<div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Mostrar archivos de la referencia</div>
          		<div class="CajaReferencias">
                    <div id="CapaTablaIframe">
    					<table id="mitabla">
        				<tr>
                        	<th>NOMBRE ARCHIVO</th>
        					<th style="text-align: center;">FECHA DE SUBIDA</th>
        					<th style="text-align: center;">DESCARGAR</th>
        					<th style="text-align: center;">ELIMINAR</th>
                        </tr>
                        <?php 
							$ref_archivo->dameId_archivo($referencias->id_referencia);
							$ids_archivos = $ref_archivo->ids_archivos;
							
							for($i=0;$i<count($ids_archivos);$i++) {
								$array_ids_archivos[] = $ids_archivos[$i]["id_archivo"];
								$ref_archivo->cargaDatosArchivosReferenciaId($array_ids_archivos[$i]);
						?>
						<tr><td><?php echo $ref_archivo->nombre_archivo;?><input type="hidden" name="archivos_tabla[]" id="archivos_tabla[]" value="<?php echo $ref_archivo->nombre_archivo;?>" /></td><td style="text-align: center;"><?php echo $ref_archivo->fecha_subida;?></td><td style="text-align: center;"><input type="button" id="descargar" name="descargar" class="BotonEliminar"  value="DESCARGAR" onclick="window.location.href='download_upload.php?id=<?php echo $ref_archivo->nombre_archivo;?>'"/> </td><td style="text-align: center;"><input type="checkbox" name="chkbox" value="<?php echo $ref_archivo->id_archivo;?>" /></td></tr>
                    <?php 
					}
					?> 
                    </table>   
                    </div> 
			    </div>
			    <?php 
                	if($modificar){ ?>
                		<input type="button" id="quitar" name="quitar" class="BotonQuitar" value="QUITAR" onclick="javascript:removeRow(mitabla)"  />
                <?php 
                	}
                ?>
        </div> 
        <br />
        
        <div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Utilizada en</div>
        	<div class="CajaReferencias">
        		<div id="CapaTablaIframe">
    				<table id="mitabla">
        				<tr>
                      		<th>COMPONENTE</th>
                            <th>TIPO</th>
                            <th style="text-align:center;">VERSIÓN</th>
                            <th>ESTADO</th>
                       		<th>DENTRO DE</th>
                            <th>TIPO</th>
                            <th style="text-align:center;">VERSIÓN</th>
                            <th>ESTADO</th>
        				</tr>
        				<?php
                            // Obtenemos los componentes que tienen esa referencia
                            $ref->dameComponentesConReferenciaId($id_referencia);
                            $resultados_componentes = $ref->resultados_componentes;

                            if(!empty($resultados_componentes)){
                                for($i=0;$i<count($resultados_componentes);$i++){
                                    $id_componente = $resultados_componentes[$i]["id_componente"];

                                    // Obtenemos el tipo de componente
                                    $id_tipo = $comp->dameTipoComponente($id_componente);
                                    $componente_principal = ($id_tipo == 1 || $id_tipo == 2);

                                    // Cargamos los datos del componente
                                    $comp->cargaDatosComponenteId($id_componente);
                                    $nombre_componente = $comp->nombre;
                                    $version = $comp->version;
                                    $nombre_tipo = $comp->dameNombreTipoComponente($id_tipo);
                                    $nombre_tipo_min = strtolower($nombre_tipo);
                                    $estado = $comp->estado;

                                    if($componente_principal){ ?>
                                        <tr>
                                            <td id="enlaceComposites">
                                                <a target="_blank" href="mod_<?php echo $nombre_tipo_min;?>.php?id=<?php echo $id_componente;?>"><?php echo $nombre_componente;?></a>
                                            </td>
                                            <td><?php echo $nombre_tipo;?></td>
                                            <td style="text-align:center;"><?php echo $version;?></td>
                                            <td><?php echo $estado;?></td>
                                            <td style="text-align:center; background: #eee";></td>
                                            <td style="text-align:center; background: #eee";></td>
                                            <td style="text-align:center; background: #eee";></td>
                                            <td style="text-align:center; background: #eee";></td>
                                        </tr>
                                <?php
                                    }
                                    else {
                                        // Obtenemos los componentes principales a los que pertenezca ese componente
                                        $es_kit = $id_tipo == "5";

                                        if($es_kit) {
                                            // Buscamos los componentes "padre" de ese kit
                                            $res_padres = $kit->dameComponentesConKit($id_componente);
                                        }
                                        else {
                                            // ERROR
                                            $res_padres = 0;
                                        }

                                        if(!empty($res_padres)){
                                            for($j=0;$j<count($res_padres);$j++) {
                                                $id_padre = $res_padres[$j]["id_componente"];
                                                $comp->cargaDatosComponenteId($id_padre);
                                                $nombre_padre = $comp->nombre;
                                                $version_padre = $comp->version;
                                                $estado_padre = $comp->estado;
                                                $id_tipo_padre = $comp->id_tipo;
                                                $tipo_padre = $comp->dameNombreTipoComponente($id_tipo_padre);
                                                $tipo_padre_min = strtolower($tipo_padre); ?>

                                                <tr>
                                                    <td id="enlaceComposites">
                                                        <a target="_blank" href="mod_<?php echo $tipo_padre_min;?>.php?id=<?php echo $id_padre; ?>"><?php echo $nombre_padre; ?></a>
                                                    </td>
                                                    <td><?php echo $tipo_padre; ?></td>
                                                    <td style="text-align:center;"><?php echo $version_padre; ?></td>
                                                    <td><?php echo $estado_padre; ?></td>
                                                    <td style="background: #99CCFF;"><?php echo $nombre_componente; ?></td>
                                                    <td style="background: #99CCFF;"><?php echo $nombre_tipo; ?></td>
                                                    <td style="background: #99CCFF; text-align: center;"><?php echo $version; ?></td>
                                                    <td style="background: #99CCFF;"><?php echo $estado; ?></td>
                                                </tr>
                                        <?php
                                            }
                                        ?>
                                    <?php
                                        }
                                        else { ?>
                                            <tr>
                                                <td id="enlaceComposites">
                                                    <a target="_blank" href="mod_<?php echo $nombre_tipo_min;?>.php?id=<?php echo $id_componente;?>"><?php echo $nombre_componente;?></a>
                                                </td>
                                                <td><?php echo $nombre_tipo;?></td>
                                                <td style="text-align:center;"><?php echo $version;?></td>
                                                <td><?php echo $estado;?></td>
                                                <td style="text-align:center; background: #eee";></td>
                                                <td style="text-align:center; background: #eee";></td>
                                                <td style="text-align:center; background: #eee";></td>
                                                <td style="text-align:center; background: #eee";></td>
                                            </tr>
                                    <?php
                                        }
                                    }
                                }
                            }
						?>
                    </table>   
            	</div>
			</div> 
        </div>
               
        <div class="ContenedorBotonCreacionBasico">
           	<input type="button" id="volver" name="volver" value="Volver" onclick="javascript:history.back()"/> 
            <?php 
                if($modificar){ ?>
		            <input type="hidden" id="guardandoReferencia" name="guardandoReferencia" value="1"/>
		            <input type="submit" id="continuar" name="continuar" value="Continuar" />
		    <?php 
		    	}
		    ?>
        </div>
        <?php
        	if($mensaje_error != "") {
			echo '<div class="mensaje_error">'.$mensaje_error.'</div>'; 
		}
		?>
        <br />
    </form>
</div>    
<?php include ("../includes/footer.php"); ?>