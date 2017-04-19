<?php
set_time_limit(10000);
// Este fichero modifica un kit de basicos
include("../includes/sesion.php");
include("../classes/basicos/kit.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/basicos/referencia_componente.class.php");
include("../classes/basicos/proveedor.class.php");
include("../classes/basicos/fabricante.class.php");
include("../classes/basicos/listado_referencias_componentes.class.php");
include("../classes/basicos/log_importacion_referencias_componente.class.php");
include("../classes/funciones/funciones.class.php");
include("../classes/kint/Kint.class.php");
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

$kt = new Kit();
$ref = new Referencia();
$ref_modificada = new Referencia();
$ref_componente = new Referencia_Componente();
$ref_kits = new listadoReferenciasComponentes();
$funciones = new Funciones();
$proveedor = new Proveedor();
$fabricante = new Fabricante(); 
$log = new LogImportacionReferenciasComponente();

if(isset($_POST["guardandoKit"]) and $_POST["guardandoKit"] == 1) {
	// Se reciben los datos
	$nombre = $_POST["nombre"];
	$referencia = $_POST["referencia"];
	$descripcion = $_POST["descripcion"];
	$version = $_POST["version"];
	$estado = $_POST["estado"];
	$prototipo = $_POST["prototipo"];
	$referencias = $_POST["REFS"];
	$piezas = $_POST["piezas"];
	$id_componente = $_GET["id"];
	$archivos_tabla = $_POST["archivos_tabla"];
	$duplicado = $_POST["duplicar_kit"];
	$act_version = $_POST["act_version"];
	$metodo = $_POST["metodo"]; 
	$seguir = true;

	if($prototipo == NULL) $prototipo = "0";
	if(($nombre == '') or ($referencia == '') or ($descripcion == '') or ($version == '')){
		echo '<script type="text/javascript">alert("Rellene los campos obligatorios")</script>';
	}
	else {
		$fallo = false;   // Campo vacio
		$fallo2 = false;  // Valor no numerico
		// Comprobamos si se realiza la importacion normal o masiva
		if($metodo == "normal") {
			// Validamos que los datos introducidos son correctos
			if(count($piezas) != 0) {
				$i=0;
				while($i<count($piezas) && !$fallo && !$fallo2) {
					if(empty($piezas[$i])) $fallo = true;
					elseif(!is_numeric($piezas[$i])) $fallo2 = true;
					$i++;
				}
				if($fallo) {
					$seguir = false;
					echo '<script type="text/javascript">alert("Introduzca numero de unidades para todas las referencias")</script>';
				}
				elseif($fallo2) {
					$seguir = false;
					echo '<script type="text/javascript">alert("El campo PIEZAS debe ser un entero o un decimal con punto")</script>';
				}
			}
		}
		else {
			// Comprobamos que las referencias del excel son correctas 
			if(isset($_FILES["archivo_importacion"])){
				$resultado = $funciones->comprobarDatosExcel($_FILES["archivo_importacion"]);
				switch ($resultado) {
					case 1:
						// OK
					break;
					case 2:
						// ERROR EXCEL NO VALIDO
						$seguir = false;
						echo '<script type="text/javascript">alert("El archivo excel no tiene un formato valido")</script>';	
					break;
					case 3:
						// ERROR CAMPOS SIN RELLENAR
						$seguir = false;
						echo '<script type="text/javascript">alert("Los campos en rojo son obligatorios para todas las referencias")</script>';	
					break;	
					default:
					# code...
					break;
				}
			}
		}

		if($seguir){
			// Comprobamos si se elimino algun archivo de la tabla 
			$resultado = $kt->compruebaTablaArchivos($id_componente,$archivos_tabla);
			if($resultado == 1){
				if(isset ($_FILES["archivos"])) {
					// Comprobamos que se han adjuntado nuevos archivos
					// Si hay archivos, los subimos.
					$i=0;
					$error_archivo = 0;
					while(($i<count($_FILES["archivos"]["name"])) and ($error_archivo == 0)) {
						if($_FILES["archivos"]["name"][$i] != NULL) {
							$uploaddir = "mecanica/";
							$num_ramdom = rand(0,10000).rand(0,10000);
							$nombre_archivo[] = $num_ramdom.'_'.basename($_FILES['archivos']['name'][$i]);
							$uploadfile = $uploaddir . $nombre_archivo[$i];
							$error_archivo = $_FILES['archivos']['error'][$i];
							$subido = false;
							if($error_archivo == UPLOAD_ERR_OK) {
								$subido = copy($_FILES['archivos']['tmp_name'][$i], $uploadfile);
							}
						}
						$i++;
					}
				}
		
				if((!$archivos) or (($subido) and ($error_archivo == 0))) {
					if($metodo == "masivo"){
						unset($referencias);
						unset($piezas);
						include("../basicos/preparar_referencias_importacion.php");
					}

					// Si hay referencias
					if($referencias != NULL){
						$refs_piezas = $ref->agruparReferencias($referencias,$piezas);
						$referencias = $refs_piezas["referencias"];
						$piezas = $refs_piezas["piezas"];
					}
				}
				
				$kt->datosNuevoKit($id_componente,$nombre,$referencia,$descripcion,$version,$referencias,$piezas,5,$nombre_archivo,$estado,$prototipo);
				$resultado = $kt->guardarCambios();
				if($resultado == 1) {
					// Guardamos un log en caso de importacion masiva
					if($metodo == "masivo"){
						$id_usuario = $_SESSION["AT_id_usuario"];
						$tipo_componente = "KIT";
						$proceso = "MODIFICACION KIT";
						include("../basicos/log_importacion_referencias_componente.php");
					}

					// Si el usuario ha seleccionado Duplicar Kit
					if ($duplicado == 1) {
					// Generamos un numero aleatorio y preparamos el nuevo nombre y el nuevo nombre de referencia
						$random_copia = rand(0,10000000);
						$nombre = $nombre.'_copia_'.$random_copia;
						$referencia = $referencia.'_copia_'.$random_copia;

						//Obtenemos los nombres de los archivos del kit original
						$kt->dameNombres_archivos($id_componente);
						$nombres = $kt->nombres_archivos;
						// Los guardamos en un array simple
						for ($i=0;$i<count($nombres);$i++) {
							$nombre_archivos[]= $nombres[$i]["nombre_archivo"];
							// Extrae el nombre del archivo sin el random
							$aux[] = explode('_',$nombre_archivos[$i],2);
							// Guarda en el array nombre_archivo los nombres de los archivos sin el numero random del los archivos del kit original
							$nombre_archivo[$i] = $aux[$i][1];
							// Le metemos un nuevo random para que no sea igual que el del kit original
							$nombre_archivo[$i] = rand(0,10000).rand(0,10000).'_'.$nombre_archivo[$i];
						}

						// Copiamos fisicamente los archivos de la carpeta al servidor
						for ($j=0;$j<count($nombre_archivos);$j++) {
							$uploaddir = "mecanica/";
							$uploadfile = $uploaddir . $nombre_archivo[$j];
							// Copia los archivos del servidor en la misma carpeta /mecanica pero con un nombre de archivo diferente.
							copy($uploaddir.$nombre_archivos[$j],$uploadfile);
						}

						$kt->datosNuevoKit(NULL,$nombre,$referencia,$descripcion,$version,$referencias,$piezas,5,$nombre_archivo,$estado,$prototipo);
						$resultado = $kt->guardarCambios();
						if($resultado == 1) {
							header("Location: kits.php?operacion_kit=duplicado");
						}
						else {
							$mensaje_error = $kt->getErrorMessage($resultado);
						}
					}
					// Duplica el kit y eleva la version al entero mas proximo
					else if ($act_version == 1){
						// Convertimos el string de version en un float para poder elevar la version
						$version = floatval($version);	
						// Elevamos la version al "entero" mas proximo y si es una version entera sumamos uno a la version
						if ((($version * 100) % 100) != 0){
							$version = number_format(ceil($version), 2, '.', '');
						}
						else {
							$version++;
							$version = number_format($version, 2, '.', '');
						}

						//Obtenemos los nombres de los archivos del kit original
						$kt->dameNombres_archivos($id_componente);
						$nombres = $kt->nombres_archivos;
						// Los guardamos en un array simple
						for ($i=0;$i<count($nombres);$i++) {
							$nombre_archivos[]= $nombres[$i]["nombre_archivo"];
							// Extrae el nombre del archivo sin el random
							$aux[] = explode('_',$nombre_archivos[$i],2);
							// Guarda en el array nombre_archivo los nombres de los archivos sin el numero random de los archivos del kit original
							$nombre_archivo[$i] = $aux[$i][1];
							// Le metemos un nuevo random para que no sea igual que el del kit original
							$nombre_archivo[$i] = rand(0,10000).rand(0,10000).'_'.$nombre_archivo[$i];
						}

						// Copiamos fisicamente los archivos de la carpeta al servidor
						for ($j=0;$j<count($nombre_archivos);$j++) {
							$uploaddir = "mecanica/";
							$uploadfile = $uploaddir . $nombre_archivo[$j];
							// Copia los archivos del servidor en la misma carpeta /mecanica pero con un nombre de archivo diferente.
							copy($uploaddir.$nombre_archivos[$j],$uploadfile);
						}

						// El componente con la versión actualizada está en estado BORRADOR
						$estado = "BORRADOR";
						$kt->datosNuevoKit(NULL,$nombre,$referencia,$descripcion,$version,$referencias,$piezas,5,$nombre_archivo,$estado,$prototipo);
						$resultado = $kt->guardarCambios();
						if($resultado == 1) {
							header("Location: kits.php?operacion_kit=actualizado");
						}
						else {
							$mensaje_error = $kt->getErrorMessage($resultado);
						}
					}
					else {
						header("Location: kits.php?operacion_kit=modificado");
					}
				}
				else {
				$mensaje_error = $kt->getErrorMessage($resultado);
				}
			}
		}
	}
}
// Se cargan los datos buscando por el ID
$kt->cargaDatosKitId($_GET["id"]);
$nombre = $kt->kit;
$referencia = $kt->referencia;
$descripcion = $kt->descripcion;
$version = $kt->version;
$estado = $kt->estado;
$prototipo = $kt->prototipo;
$referencias = $kt->referencias;

// Titulo de pagina
$titulo_pagina = "Básico > Modifica kit";
$pagina = "mod_kit";
include ('../includes/header.php');
echo '<script type="text/javascript" src="../js/basicos/mod_kit_08032017_1050.js"></script>';
echo '<script type="text/javascript" src="../js/basicos/mod_kit_adjuntos.js"></script>';
echo '<script type="text/javascript" src="../js/funciones.js"></script>';
$ref_kits->setValores($_GET["id"]);
$ref_kits->realizarConsulta();
$resultadosBusqueda = $ref_kits->referencias_componentes;
$componente = "kit";
?>

<div class="separador"></div>
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
 	</div>

    <h3> Modificacion de Kit </h3>
    <form id="FormularioCreacionBasico" name="modificarKit" action="mod_kit.php?id=<?php echo $kt->id_componente; ?>" method="post" enctype="multipart/form-data">
    	<br />
        <h5> Modifique los datos en el siguiente formulario </h5>
    	<div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Nombre *</div>
            <input type="text" id="nombre" name="nombre" class="CreacionBasicoInput" value="<?php echo $nombre;?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Referencia *</div>
           	<input type="text" id="referencia" name="referencia" class="CreacionBasicoInput" value="<?php echo $referencia;?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Descripción *</div>
          	<input type="text" id="descripcion" name="descripcion" class="CreacionBasicoInput" value="<?php echo $descripcion;?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Versión *</div>
          	<input type="text" id="version" name="version" class="CreacionBasicoInput" readonly="readonly" value="<?php echo number_format($version, 2, '.', ' ');?>" />
           	<?php 
           		if($modificar){ ?>
		           	<div class="LabelActualizaVersion">Actualizar versión</div>
		            <input type="button" id="actualizar_version" name="actualizar_version" class="BotonEliminar" value="UPGRADE" onclick="javascript:actualizarVersion();"/>
		            <input type="hidden" id="act_version" name="act_version" value="1"/>
		    <?php 
		    	} 
		    ?>
        </div>
        <div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Estado</div>
        	<?php 
   				$num_estados = 3;
				$estados = array ("BORRADOR","PRODUCCIÓN","LEGACY");

        		if($modificar){ ?>
		            <select id="estado" name="estado" class="CreacionBasicoInput">
		            	<?php
							if ($estado == "BORRADOR") {
								for($i=0;$i<$num_estados;$i++) {
									echo '<option value="'.$estados[$i].'"'; if ($estados[$i] == $estado) echo ' selected="selected"'; 	echo '>'.$estados[$i].'</option>';
								}
							}
							elseif ($estado == "PRODUCCIÓN") {
								for($i=0;$i<$num_estados;$i++) {
									echo '<option value="'.$estados[$i].'"'; if ($estados[$i] == $estado) echo ' selected="selected"'; 	echo '>'.$estados[$i].'</option>';
								}
							}
							else {
								for($i=0;$i<$num_estados;$i++) {
									echo '<option value="'.$estados[$i].'"'; if ($estados[$i] == $estado) echo ' selected="selected"'; 	echo '>'.$estados[$i].'</option>';
								}
							}
						?>
		            </select>
		   <?php 
		    	}
		    	else { ?>
		    		<input type="text" id="estado" name="estado" class="CreacionBasicoInput" value="<?php echo $estado;?>" <?php echo $solo_lectura; ?> />
		    <?php
		    	}		
            	if($modificar){ ?>
            		<div class="LabelPrototipo">Prototipo</div>
            <?php
	            	echo '<input type="checkbox" id="prototipo" name="prototipo" class="BotonEliminar" value="1"';
					if ($prototipo == 1) echo ' checked="checked" />';
					else echo ' />'; 
				}
				else if ($prototipo == 1) { ?>
					<div class="LabelPrototipo">Prototipo</div>
			<?php
				}
			?>
        </div>
        <br/>
        <br/>
        <?php 
        	if($modificar){ ?>
		        <div id="capa_metodo" class="ContenedorCamposCreacionBasico">
		           	<div class="LabelCreacionBasico"></div>
		           	<div id="capa_boton_metodo" class="ContenedorCamposAdjuntar">
		        		<input type="button" id="importar_excel" name="importar_excel" class="BotonEliminar" value="IMPORTAR DESDE EXCEL" onclick="cargaArchivoImportacion();" />  
		        		<input type="hidden" id="metodo" name="metodo" value="normal">
		        	</div>	
		        </div>
		<?php 
			}
		?>
        <div class="ContenedorCamposCreacionBasico" id="capa_referencias">
           	<div class="LabelCreacionBasico">Referencias</div>
           		<div class="CajaReferencias">
            		<div id="CapaTablaIframe">
    					<table id="mitabla">
        				<tr>
        					<th style="text-align:center">ID REF</th>
                        	<th>NOMBRE</th>
        					<th>PROVEEDOR</th>
        					<th>REF. PROVEEDOR</th>
        					<th>NOMBRE PIEZA</th>
                            <th style="text-align:center">PIEZAS</th>
                        	<th style="text-align:center">PACK PRECIO</th>
                            <th style="text-align:center">UDS/P</th>
                            <th style="text-align:center">PRECIO UNIDAD</th>
                            <th style="text-align:center">PRECIO</th>
                            <?php if($modificar) { ?>
                       			<th style="text-align:center">ELIMINAR</th>
                       		<?php } ?>
                        </tr>
                        <?php
                        	$max_caracteres_ref = 50;
                        	$max_caracteres = 25;
							$precio_total = 0;
							$fila = 0;
							for($i=0;$i<count($resultadosBusqueda);$i++) {
							// Se cargan los datos de las referencias según su identificador
							$datoRef = $resultadosBusqueda[$i];
							$ref_componente->cargaDatosReferenciaComponenteId($datoRef["id"]);
							$ref_modificada->cargaDatosReferenciaId($ref_componente->id_referencia);
							if($ref_modificada->pack_precio <> 0) {
								$precio_unidad = ($ref_modificada->pack_precio / $ref_modificada->unidades);
								$precio_referencia = ($ref_modificada->pack_precio / $ref_modificada->unidades) * $ref_componente->piezas;
							}
							$precio_total = $precio_total + $precio_referencia;
						?>
                        <tr>
                        	<td style="text-align:center;">
                        		<?php echo $ref_modificada->id_referencia; ?>
                        	</td>
                        	<td id="enlaceComposites">
                        		<a href="mod_referencia.php?id=<?php echo $ref_componente->id_referencia;?>" target="blank"/>
									<?php 
                        				if (strlen($ref_modificada->referencia) > $max_caracteres_ref){
                        					echo substr($ref_modificada->referencia,0,$max_caracteres_ref).'...'; 
                        				}
                        				else echo $ref_modificada->referencia;	 
                        			?>
								</a>
								<input type="hidden" name="REFS[]" id="REFS[]" value="<?php echo $ref_componente->id_referencia;?>" />
							</td>
                        	<td><?php echo $ref_modificada->nombre_proveedor; ?></td>
                        	<td><?php $ref_modificada->vincularReferenciaProveedor(); ?></td>
                        	<td><?php echo $ref_modificada->part_nombre; ?></td>
                        	<?php 
                        		if($modificar){ ?>
                        			<td style="text-align:center"><input type="text" name="piezas[]" id="piezas[]" class="CampoPiezasInput" value="<?php echo number_format($ref_componente->piezas, 2, '.', ''); ?>" onblur="javascript:validarHayCaracter(<?php echo $fila;?>)" /></td>
                       		<?php 
                       			} 
                       			else { ?>
                       				<td style="text-align:center"><?php echo number_format($ref_componente->piezas, 2, '.', ''); ?></td>
                       		<?php 
                       			}
                       		?>
                        	<td style="text-align:center"><?php echo number_format($ref_modificada->pack_precio, 2, ',', '.');?></td>
                        	<td style="text-align:center"><?php echo $ref_modificada->unidades; ?></td>
                        	<td style="text-align:center"><?php echo number_format($precio_unidad, 2, '.', '');?></td>
                        	<td style="text-align:center"><?php echo number_format($precio_referencia, 2, '.', ''); ?></td>
                        	<?php if($modificar) { ?>
                        		<td style="text-align:center"><input type="checkbox" name="chkbox" value="<?php echo $ref_componente->id_referencia;?>" /></td>
                        	<?php } ?>
                        </tr>
                    <?php $fila = $fila + 1; ?>
					<input type="hidden" name="fila" id="fila" value="<?php echo $fila;?>"/>
                    <?php
					}
					?>
                    </table>
                    </div>
			    </div>
			    <?php if($modificar) { ?>
	            	<input type="button" id="mas" name="mas" class="BotonMas"  value="+" onclick="javascript:Abrir_ventana('buscador_referencias.php?componente=<?php echo $componente;?>')"/>
	           		<input type="button" id="menos" name="menos" class="BotonMenos" value="-" onclick="javascript:removeRow(mitabla)"  />
	           	<?php } ?>
        </div>
        <div class="ContenedorCamposCreacionBasico" id="capa_opciones" style="display:none;">
        	<div class="LabelCreacionBasico">Referencias</div>
        	<div class="ContenedorCamposImportacion">
        		<a class="EnlaceDescarga" href="../documentos/importacion_masiva.zip"><span class="fuenteSimumakLittle">Descargar plantilla</span></a>
        	</div>
        	<div id="importar_archivo"> 
	        	<div class="ContenedorCamposAdjuntar">
	        		<input type="file" id="archivo_importacion" name="archivo_importacion" class="BotonAdjuntar" style="margin-left: 0px;" />  
	        	</div>
        	</div>
        </div>

        <br/>
        <div id="coste_componente" class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Coste Kit</div>
            <div id="CosteTotalComponente"><span class="fuenteSimumakNegrita"><?php // echo number_format($precio_total, 2, ',', '.'); ?> €</span></div>
        </div>
        <br/>
        <div class="ContenedorCamposCreacionBasico">
        	<?php if($modificar) { ?>
           		<div class="LabelCreacionBasico">Archivos adjuntos</div>
            	<div id="AñadirMasArchivos"><a href="#" onClick="addCampo()" class="SubirArchivo">Subir otro archivo</a></div>
            <?php } ?>
        </div>
        <div id="adjuntos">
        	<?php if($modificar) { ?>
	        	<div class="ContenedorCamposAdjuntar">
	        		<input type="file" id="archivos[]" name="archivos[]" class="BotonAdjuntar"/>
	        	</div>
	        <?php } ?>
        </div>
        <br/>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Mostrar archivos del kit</div>
          		<div class="CajaReferencias">
                    <div id="CapaTablaIframe">
    					<table id="mitablaArchivos">
        				<tr>
                        	<th>NOMBRE ARCHIVO</th>
        					<th style="text-align:center">FECHA DE SUBIDA</th>
        					<th style="text-align:center">DESCARGAR</th>
        					<?php if($modificar) { ?>
        						<th style="text-align:center">ELIMINAR</th>
        					<?php } ?>
                        </tr>
 						<?php
							$kt->dameId_archivo($kt->id_componente);
							$ids_archivos = $kt->ids_archivos;

							for($i=0;$i<count($ids_archivos);$i++) {
								$array_ids_archivos[] = $ids_archivos[$i]["id_archivo"];
								$kt->cargaDatosArchivosKitId($array_ids_archivos[$i]);
						?>
						<tr><td><?php echo $kt->nombre_archivo;?><input type="hidden" name="archivos_tabla[]" id="archivos_tabla[]" value="<?php echo $kt->nombre_archivo;?>" /></td><td style="text-align:center"><?php echo $kt->fecha_subida;?></td><td style="text-align:center"><input type="button" id="descargar" name="descargar" class="BotonEliminar"  value="DESCARGAR" onclick="window.open('download.php?id=<?php echo $kt->nombre_archivo;?>')"/></td><?php if($modificar){ ?><td style="text-align:center"><input type="checkbox" name="chkboxArch" value="<?php echo $kt->id_archivo;?>" /></td><?php } ?></tr>
                    <?php
					}
					?>
                    </table>
                    </div>
			    </div>
			    <?php if($modificar){ ?>
                	<input type="button" id="quitar" name="quitar" class="BotonQuitar" value="QUITAR" onclick="javascript:removeRowArchivos(mitablaArchivos)"  />
                <?php } ?>
        </div>
        <br/>
        <?php if($modificar){ ?>
        	<input type="checkbox" id="duplicar_kit" name="duplicar_kit" value="1" /> <div class="label_check_precios">Duplicar Kit</div>
        <?php } ?>
        <br/>
        <br/>
        <br/>
        <div class="ContenedorBotonCreacionBasico">
           	<input type="button" id="volver" name="volver" value="VOLVER" class="BotonEliminar" onclick="javascript:window.history.back()"/>
           	<?php if($modificar){ ?>
            	<input type="hidden" id="guardandoKit" name="guardandoKit" value="1"/>
            	<input type="submit" id="continuar" name="continuar" value="CONTINUAR" class="BotonEliminar" />
            <?php } ?>
        </div>
        <?php
        	if($mensaje_error != "") {
			echo '<div class="mensaje_error">'.$mensaje_error.'</div>';
		}
		?>
        <br/>
        <div id="aux">
        	<input type="hidden" id="act_version" name="act_version" value="0"/>
        </div>
    </form>
</div>
<script type="text/javascript">
	window.onload = function(){
		costeTotal = damePrecioComponenteConHeredadas(mitabla,"piezas[]");
		actualizarCoste(costeTotal);
	}
</script>
<?php include ("../includes/footer.php"); ?>