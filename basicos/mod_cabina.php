<?php
set_time_limit(10000);
// Este fichero modifica la cabina de basicos
include("../includes/sesion.php");
include("../classes/basicos/cabina.class.php");
include("../classes/basicos/interface.class.php");
include("../classes/basicos/kit.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/basicos/referencia_componente.class.php");
include("../classes/basicos/proveedor.class.php");
include("../classes/basicos/fabricante.class.php");
include("../classes/basicos/listado_interfaces.class.php");
include("../classes/basicos/listado_kits.class.php");
include("../classes/basicos/listado_referencias_componentes.class.php");
include("../classes/basicos/log_importacion_referencias_componente.class.php");
include("../classes/funciones/funciones.class.php");
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

$cabinas = new Cabina();
$interfs = new listadoInterfaces();
$listado_kits = new listadoKits();
$ref_cabinas = new listadoReferenciasComponentes();
$ref_interfaces = new listadoReferenciasComponentes();
$ref_kits = new listadoReferenciasComponentes();
$ref_componente = new Referencia_Componente();
$ref_int = new Referencia_Componente();
$ref_kit = new Referencia_Componente();
$ref = new Referencia();
$ref_modificada = new Referencia();
$Interfaz = new Interfaz();
$Kit = new Kit();
$funciones = new Funciones();
$proveedor = new Proveedor();
$fabricante = new Fabricante(); 
$log = new LogImportacionReferenciasComponente();

if(isset($_POST["guardandoCabina"]) and $_POST["guardandoCabina"] == 1) {
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
	$duplicado = $_POST["duplicar_cabina"];
	$interfaces = $_POST["interfaz"];
	$kits = $_POST["kit"];
	$act_version = $_POST["act_version"];
	$metodo = $_POST["metodo"]; 
	$seguir = true;

	if($prototipo == NULL) $prototipo = "0";
	if(($nombre == '') || ($referencia == '') || ($descripcion == '') || ($version == '')){
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
				while ($i<count($piezas) && !$fallo && !$fallo2) {
					if (empty($piezas[$i])) $fallo = true;
					elseif (!is_numeric($piezas[$i])) $fallo2 = true;
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
			$resultado = $cabinas->compruebaTablaArchivos($id_componente,$archivos_tabla);
			if($resultado == 1){
				if(isset($_FILES["archivos"])) {
					// Comprobamos que se han adjuntado nuevos archivos
					// Si hay archivos, los subimos.
					$i=0;
					$error_archivo = 0;
					while(($i<count($_FILES["archivos"]["name"])) && ($error_archivo == 0)) {
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

				if((!$archivos) || (($subido) && ($error_archivo == 0))) {
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
				
				$cabinas->datosNuevoCabina($id_componente,$nombre,$referencia,$descripcion,$version,$referencias,$piezas,1,$nombre_archivo,$interfaces,$estado,$prototipo,$kits);
				$resultado = $cabinas->guardarCambios();
				if($resultado == 1) {
					// Guardamos un log en caso de importacion masiva
					if($metodo == "masivo"){
						$id_usuario = $_SESSION["AT_id_usuario"];
						$tipo_componente = "CABINA";
						$proceso = "MODIFICACION CABINA";
						include("../basicos/log_importacion_referencias_componente.php");
					}

					// Si el usuario ha pulsado Duplicar Cabina
					if($duplicado == 1) {
						// Generamos un numero aleatorio y preparamos el nuevo nombre y el nuevo nombre de referencia
						$random_copia = rand(0,10000000);
						$nombre = $nombre.'_copia_'.$random_copia;
						$referencia = $referencia.'_copia_'.$random_copia;

						// Obtenemos los nombres de los archivos de la cabina original
						$cabinas->dameNombres_archivos($id_componente);
						$nombres = $cabinas->nombres_archivos;
						// Los guardamos en un array simple
						for($i=0;$i<count($nombres);$i++) {
							$nombre_archivos[]= $nombres[$i]["nombre_archivo"];
							// Extrae el nombre del archivo sin el random
							$aux[] = explode('_',$nombre_archivos[$i],2);
							// Guarda en el array nombre_archivo los nombres de los archivos sin el numero random del los archivos de ls cabina original
							$nombre_archivo[$i] = $aux[$i][1];
							// Le metemos un nuevo random para que no sea igual que el de la cabina original
							$nombre_archivo[$i] = rand(0,10000).rand(0,10000).'_'.$nombre_archivo[$i];
						}

						// Copiamos fisicamente los archivos de la carpeta al servidor
						for($j=0;$j<count($nombre_archivos);$j++) {
							$uploaddir = "mecanica/";
							$uploadfile = $uploaddir . $nombre_archivo[$j];
							// Copia los archivos del servidor en la misma carpeta /mecanica pero con un nombre de archivo diferente.
							copy($uploaddir.$nombre_archivos[$j],$uploadfile);
						}

						$cabinas->datosNuevoCabina(NULL,$nombre,$referencia,$descripcion,$version,$referencias,$piezas,1,$nombre_archivo,$interfaces,$estado,$prototipo,$kits);
						$resultado = $cabinas->guardarCambios();
						if($resultado == 1) {
							header("Location: cabinas.php?cab=duplicado");
						}
						else {
							$mensaje_error = $cabinas->getErrorMessage($resultado);
						}
					}
					// Duplica la cabina y eleva la version al entero mas proximo
					else if($act_version == 1){
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
							
						// Obtenemos los nombres de los archivos de la cabina original
						$cabinas->dameNombres_archivos($id_componente);
						$nombres = $cabinas->nombres_archivos;
						// Los guardamos en un array simple
						for($i=0;$i<count($nombres);$i++) {
							$nombre_archivos[]= $nombres[$i]["nombre_archivo"];
							// Extrae el nombre del archivo sin el random
							$aux[] = explode('_',$nombre_archivos[$i],2);
							// Guarda en el array nombre_archivo los nombres de los archivos sin el numero random del los archivos de ls cabina original
							$nombre_archivo[$i] = $aux[$i][1];
							// Le metemos un nuevo random para que no sea igual que el de la cabina original
							$nombre_archivo[$i] = rand(0,10000).rand(0,10000).'_'.$nombre_archivo[$i];
						}

						// Copiamos fisicamente los archivos de la carpeta al servidor
						for($j=0;$j<count($nombre_archivos);$j++) {
							$uploaddir = "mecanica/";
							$uploadfile = $uploaddir . $nombre_archivo[$j];
							// Copia los archivos del servidor en la misma carpeta /mecanica pero con un nombre de archivo diferente.
							copy($uploaddir.$nombre_archivos[$j],$uploadfile);
						}

						// El componente con la versión actualizada está en estado BORRADOR
						$estado = "BORRADOR";
						$cabinas->datosNuevoCabina(NULL,$nombre,$referencia,$descripcion,$version,$referencias,$piezas,1,$nombre_archivo,$interfaces,$estado,$prototipo,$kits);
						$resultado = $cabinas->guardarCambios();
						if($resultado == 1) {
							header("Location: cabinas.php?cab=actualizado");
						}
						else {
							$mensaje_error = $cabinas->getErrorMessage($resultado);
						}
					}
					else {
						header("Location: cabinas.php?cab=modificado");
					}
				}
				else {
					$mensaje_error = $cabinas->getErrorMessage($resultado);
				}
			}
			else {
				$mensaje_error = $cabinas->getErrorMessage($resultado);
			}
		}
	}
}

// Se cargan los datos buscando por el ID
$cabinas->cargaDatosCabinaId($_GET["id"]);
$id_componente = $cabinas->id_componente;
$nombre = $cabinas->cabina;
$referencia = $cabinas->referencia;
$descripcion = $cabinas->descripcion;
$version = $cabinas->version;
$estado = $cabinas->estado;
$prototipo = $cabinas->prototipo;
$referencias = $cabinas->referencias;

// Titulo de pagina
$titulo_pagina = "Básico > Modifica cabina";
$pagina = "mod_cabina";
include ('../includes/header.php');
$ref_cabinas->setValores($_GET["id"]);
$ref_cabinas->realizarConsulta();
$resultadosBusqueda = $ref_cabinas->referencias_componentes;
$componente = "cabina";
echo '<script type="text/javascript" src="../js/basicos/mod_cabina.js"></script>';
echo '<script type="text/javascript" src="../js/basicos/mod_cabina_adjuntos.js"></script>';
?>

<div class="separador"></div>
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
 	</div>

    <h3> Modificacion de cabina </h3>
    <form id="FormularioCreacionBasico" name="modificarCabina" onsubmit="return SeleccionarComponentes()" action="mod_cabina.php?id=<?php echo $cabinas->id_componente; ?>" method="post" enctype="multipart/form-data">
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
          	<input type="text" id="version" name="version" class="CreacionBasicoInput" readonly="readonly" value="<?php echo number_format($version, 2, '.', '');?>" />
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
        <div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Interfaz</div>
            <div class="CajaInterfaces">
            	<table style="width:700px; height:208px; border:1px solid #fff; margin:5px 10px 0px 12px;">
                <tr>
                   	<td id= "listas_no_asignados" style="width:250px; border:1px solid #fff;">
            			<select multiple="multiple" id="interfaces_no_asignados[]" name="interfaces_no_asignados[]" class="SelectMultipleIntOrigen" >
            			<?php
							$interfs->prepararConsulta();
							$interfs->realizarConsulta();
							$resultado_interfaces = $interfs->interfaces;

							for($i=0;$i<count($resultado_interfaces);$i++) {
								$datoInterfaz = $resultado_interfaces[$i];
								$Interfaz->cargaDatosInterfazId($datoInterfaz["id_componente"]);
								echo '<option value="'.$Interfaz->id_componente.'">'.$Interfaz->interfaz.'_v'.$Interfaz->version.'</option>';
							}
						?>
            			</select>
                    </td>
                    <td style="border:1px solid #fff; vertical-align:middle">
						<table style="width:100%; border:1px solid #fff;">
                        <tr>
                        	<td style="border:1px solid #fff;">
                        		<?php if($modificar) { ?>
                        			<input type="button" id="añadirInterfaz" name="añadirInterfaz" class="BotonEliminar" onclick="AddToSecondList()" value="AÑADIR" />
                       			<?php } ?>
                       		</td>
                        </tr>
                        <tr>
                          	<td style="border:1px solid #fff;"></td>
                        </tr>
                        <tr>
                        	<td style="border:1px solid #fff;">
                        		<?php if($modificar) { ?>
                        			<input type="button" id="quitarInterfaz" name="quitarInterfaz" class="BotonEliminar" onclick="DeleteSecondListItem()" value="QUITAR" />
                        		<?php } ?>	
                        	</td>
                        </tr>
                        </table>
                    </td>
                    <td id="lista" style="width:250px; border:1px solid #fff;">
	                	<select multiple="multiple" id="interfaz[]" name="interfaz[]" class="SelectMultipleIntDestino">
                        	<?php
								$Interfaz->dameIdsInterfaces($id_componente);
								for($j=0;$j<count($Interfaz->ids_interfaces);$j++){
									$Interfaz->cargaDatosInterfazId($Interfaz->ids_interfaces[$j]["id_interfaz"]);
									echo '<option value="'.$Interfaz->id_componente.'">'.$Interfaz->interfaz.'_v'.$Interfaz->version.'</option>';
								}
							?>
                        </select>
                    </td>
                    <td style="border:1px solid #fff; vertical-align:middle">
						<table style="width:100%; border:1px solid #fff;">
                        <tr>
                        	<td style="border:1px solid #fff;"></td>
                        </tr>
                        <tr>
                          	<td style="border:1px solid #fff;"><input type="button" id="mas" name="mas" class="BotonInterfaz"  value="VER" onclick="javascript:AbrirVentanasInterfaces();"/></td>
                        </tr>
                        <tr>
                        	<td style="border:1px solid #fff;"></td>
                        </tr>
                        </table>
                    </td>
                </tr>
                </table>
        	</div>
        </div>
        <div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Kit</div>
            <div class="CajaKits">
            	<table style="width:700px; height:208px; border:1px solid #fff; margin:5px 10px 0px 12px;">
                <tr>
                   	<td id= "listas_kits_no_asignados" style="width:250px; border:1px solid #fff;">
            			<select multiple="multiple" id="kits_no_asignados[]" name="kits_no_asignados[]" class="SelectMultipleKitOrigen" >
            			<?php
							$listado_kits->prepararConsulta();
							$listado_kits->realizarConsulta();
							$resultado_kits = $listado_kits->kits;

							for($i=0;$i<count($resultado_kits);$i++) {
								$datoKit = $resultado_kits[$i];
								$Kit->cargaDatosKitId($datoKit["id_componente"]);
								echo '<option value="'.$Kit->id_componente.'">'.$Kit->kit.'_v'.$Kit->version.'</option>';
							}
						?>
            			</select>
                    </td>
                    <td style="border:1px solid #fff; vertical-align:middle">
						<table style="width:100%; border:1px solid #fff;">
                        <tr>
                        	<td style="border:1px solid #fff;">
                        		<?php if($modificar) { ?>
                        			<input type="button" id="añadirKit" name="añadirKit" class="BotonEliminar" onclick="AddKitToSecondList()" value="AÑADIR" />
                        		<?php } ?>	
                        	</td>
                        </tr>
                        <tr>
                          	<td style="border:1px solid #fff;"></td>
                        </tr>
                        <tr>
                        	<td style="border:1px solid #fff;">
                        		<?php if($modificar) { ?>	
                        			<input type="button" id="quitarKit" name="quitarKit" class="BotonEliminar" onclick="DeleteKitSecondListItem()" value="QUITAR" />
                        		<?php } ?>	
                        	</td>
                        </tr>
                        </table>
                    </td>
                    <td id="lista" style="width:250px; border:1px solid #fff;">
	                	<select multiple="multiple" id="kit[]" name="kit[]" class="SelectMultipleKitDestino">
                        	<?php
								$Kit->dameIdsKits($id_componente);
								for($j=0;$j<count($Kit->ids_kits);$j++){
									$Kit->cargaDatosKitId($Kit->ids_kits[$j]["id_kit"]);
									// Cargamos las referencias del kit para calcular su coste
									$coste_kit = 0;
									$ref_kits->setValores($Kit->ids_kits[$j]["id_kit"]);
									$ref_kits->prepararConsulta();
									$ref_kits->realizarConsulta();
									echo '<option value="'.$Kit->id_componente.'">'.$Kit->kit.'_v'.$Kit->version.'</option>';
								}
							?>
                        </select>
                    </td>
                    <td style="border:1px solid #fff; vertical-align:middle">
						<table style="width:100%; border:1px solid #fff;">
                        <tr>
                        	<td style="border:1px solid #fff;"></td>
                        </tr>
                        <tr>
                          	<td style="border:1px solid #fff;"><input type="button" id="mas" name="mas" class="BotonInterfaz"  value="VER" onclick="javascript:AbrirVentanasKits();"/>
                            </td>
                        </tr>
                        <tr>
                        	<td style="border:1px solid #fff;"></td>
                        </tr>
                        </table>
                    </td>
                </tr>
                </table>
        	</div>
        </div>
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
							// Antes se cargaban las referencias desde la tabla Componentes_Referencias. De esta manera si se actualizaba
							// alguna referencia de la cabina, no se veia reflejado el cambio.
							// Ahora para que se vea reflejado el cambio, se debe sacar las referencias de la propia tabla referencias.
							$precio_total = 0;
							$fila = 0;
							for($i=0;$i<count($resultadosBusqueda);$i++) {
								// Se cargan los datos de las referencias según su identificador
								$datoRef = $resultadosBusqueda[$i];
								$ref_componente->cargaDatosReferenciaComponenteId($datoRef["id"]); 
								// Hacemos la carga de la referencia que haya podido sufrir alguna modificacion y calculamos los costes en base a los datos de la referencia
								$ref_modificada->cargaDatosReferenciaId($ref_componente->id_referencia);
								if($ref_modificada->pack_precio <> 0) {
									$precio_unidad = ($ref_modificada->pack_precio / $ref_modificada->unidades);
									$precio_referencia = ($ref_modificada->pack_precio / $ref_modificada->unidades) * $ref_componente->piezas;
								}
								$precio_total = $precio_total + $precio_referencia;
						?>
						<tr>
							<td style="text-align:center"><?php echo $ref_modificada->id_referencia; ?></td> 
							<td id="enlaceComposites">
								<a href="mod_referencia.php?id=<?php echo $ref_componente->id_referencia;?>" target="_blank"/>
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
								if($modificar) { ?>
									<td style="text-align:center"><input type="text" name="piezas[]" id="piezas[]" class="CampoPiezasInput" value="<?php echo number_format($ref_componente->piezas, 2, '.', ''); ?>" onblur="javascript:validarHayCaracter(<?php echo $fila;?>)" /></td>
							<?php 
								}
								else { ?>
									<td style="text-align:center"><?php echo number_format($ref_componente->piezas, 2, '.', '');?></td>								
							<?php 
								}
							?>		
							<td style="text-align:center"><?php echo number_format($ref_modificada->pack_precio, 2, '.', '');?></td>
							<td style="text-align:center"><?php echo $ref_modificada->unidades; ?></td><td style="text-align:center"><?php echo number_format($precio_unidad, 2, '.', '');?></td>
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
        <br />
        <div id="coste_solo_componente" class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Coste Cabina</div>
            <div class="tituloComponente">
                <table id="tablaTituloPrototipo">
                <tr>
                    <td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
                        <span class="tituloComp"><div id="capa_coste_cabina"><?php echo number_format($precio_total, 2, ',', '.').'€';?></div></span>
                    </td>
                </tr>
                </table>
                <input type="hidden" id="coste_cabina" value="<?php echo $precio_total;?>" />
            </div>
        </div>
        <br/>
        <div id="capa_interfaces_kits" style="display: block;">
            <div id="capa_interfaces" style="display: block;">
            <?php
                // Si hay interfaces añadimos sus tablas de referencias
                $Interfaz->dameIdsInterfaces($id_componente);
                $costeInterfaces = 0;
                if(count($Interfaz->ids_interfaces) != 0){
                    for($i=0;$i<count($Interfaz->ids_interfaces);$i++){
                        $id_capa_interfaz = 'interfaz-'.$i;
                        $id_interfaz = $Interfaz->ids_interfaces[$i]["id_interfaz"];
                        echo '<div id="'.$id_capa_interfaz.'" class="ContenedorCamposCreacionBasico"><div class="LabelCreacionBasico">Referencias Interfaz</div>';
                        $Interfaz->cargaDatosInterfazId($id_interfaz);
                        echo '<div class="tituloComponente">'.$Interfaz->interfaz.'_v'.$Interfaz->version.'</div>';
                        echo '<div class="CajaReferencias"><div id="CapaTablaIframe"><table id="mitablainterfaz'.$i.'">';
                        echo '<tr><th style="text-align:center">ID REF</th><th>NOMBRE</th><th>PROVEEDOR</th><th>REF PROV</th><th>NOMBRE PIEZA</th><th style="text-align:center">PIEZAS</th><th style="text-align:center">PACK PRECIO</th><th style="text-align:center">UDS/P</th><th style="text-align:center">PRECIO UNIDAD</th><th style="text-align:center">PRECIO</th></tr>';

                        $ref_interfaces->setValores($id_interfaz);
                        $ref_interfaces->realizarConsulta();
                        $resultadosReferenciasInterfaz = $ref_interfaces->referencias_componentes;
                        $costerinterfaz = 0;
                        for($j=0;$j<count($resultadosReferenciasInterfaz);$j++) {
                            $datoRef_Interfaz = $resultadosReferenciasInterfaz[$j];
                            $ref_int->cargaDatosReferenciaComponenteId($datoRef_Interfaz["id"]);
                            $ref_modificada->cargaDatosReferenciaId($ref_int->id_referencia);

                            if ($ref_modificada->pack_precio <> 0 and $ref_modificada->unidades <> 0){
                                $precio_unidad = ($ref_modificada->pack_precio / $ref_modificada->unidades);
                            }
                            else {
                                $precio_unidad = 00;
                            }
                            $precio_referencia = $ref_int->piezas * $precio_unidad;
                            $costerinterfaz = $costerinterfaz + $precio_referencia;
                            echo '<tr><td style="text-align:center">'.$ref_int->id_referencia.'</td><td id="enlaceComposites"><a href="../basicos/mod_referencia.php?id='.$ref_int->id_referencia.'"/>'.$ref_modificada->referencia.'</a></td><td>'.$ref_modificada->nombre_proveedor.'</td>';
                            echo '<td>';
                                $ref_modificada->vincularReferenciaProveedor();
                            echo '</td></td><td>'.$ref_modificada->part_nombre.'</td><td style="text-align:center">'.number_format($ref_int->piezas, 2, '.', '').'</td><td style="text-align:center">'.number_format($ref_modificada->pack_precio, 2, '.', '').'</td><td style="text-align:center">'.$ref_modificada->unidades.'</td><td style="text-align:center">'.number_format($precio_unidad, 2, '.', '').'</td><td style="text-align:center">'.number_format($precio_referencia, 2, '.', '').'</td></tr>';
                            $costeInterfaces = $costeInterfaces + $precio_referencia;
                        }
                        echo '</table></div></div>
                            <div class="LabelCreacionBasico" style="margin-top:5px;">Coste Interfaz</div>
                            <div class="tituloComponente">
                                <table id="tablaTituloPrototipo">
                                <tr>
                                    <td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
                                        <span class="tituloComp">'.number_format($costerinterfaz, 2, ',', '.').'€'.'</span>'.'
                                    </td>
                                </tr>
                                </table>
                                <input type="hidden" id="coste_interfaz-'.$i.'" value="'.$costerinterfaz.'" />
                            </div>
                            <br />
                        </div>';
                    }
                }
                echo '<input type="hidden" id="costeInterfaces" name="costeInterfaces" value="'.$costeInterfaces.'"/>';
                $precio_total = $precio_total + $costeInterfaces;
            ?>
            </div>

            <div id="capa_kits" style="display: block;">
            <?php
                // Si hay kits añadimos sus tablas de referencias
                $Kit->dameIdsKits($id_componente);
                $costeKits = 0;
                if(count($Kit->ids_kits) != 0){
                    for($i=0;$i<count($Kit->ids_kits);$i++){
                        $id_capa_kit = 'kit-'.$i;
                        $id_kit = $Kit->ids_kits[$i]["id_kit"];
                        echo '<div id="'.$id_capa_kit.'" class="ContenedorCamposCreacionBasico"><div class="LabelCreacionBasico">Referencias Kit</div>';
                        $Kit->cargaDatosKitId($id_kit);
                        echo '<div class="tituloComponente">'.$Kit->kit.'_v'.$Kit->version.'</div>';
                        echo '<div class="CajaReferencias"><div id="CapaTablaIframe"><table id="mitablakit'.$i.'">';
                        echo '<tr><th style="text-align:center">ID REF</th><th>NOMBRE</th><th>PROVEEDOR</th><th>REF PROV</th><th>NOMBRE PIEZA</th><th style="text-align:center">PIEZAS</th><th style="text-align:center">PACK PRECIO</th><th style="text-align:center">UDS/P</th><th style="text-align:center">PRECIO UNIDAD</th><th style="text-align:center">PRECIO</th></tr>';

                        $ref_kits->setValores($id_kit);
                        $ref_kits->realizarConsulta();
                        $resultadosReferenciasKit = $ref_kits->referencias_componentes;
                        $coste_kit = 0;
                        for($j=0;$j<count($resultadosReferenciasKit);$j++) {
                            $datoRef_kit = $resultadosReferenciasKit[$j];
                            $ref_kit->cargaDatosReferenciaComponenteId($datoRef_kit["id"]);
                            $ref_modificada->cargaDatosReferenciaId($ref_kit->id_referencia);

                            if ($ref_modificada->pack_precio <> 0 and $ref_modificada->unidades <> 0){
                                $precio_unidad = ($ref_modificada->pack_precio / $ref_modificada->unidades);
                            }
                            else {
                                $precio_unidad = 00;
                            }
                            $precio_referencia = $ref_kit->piezas * $precio_unidad;
                            echo '<tr><td style="text-align:center">'.$ref_kit->id_referencia.'</td><td id="enlaceComposites"><a href="../basicos/mod_referencia.php?id='.$ref_kit->id_referencia.'"/>'.$ref_modificada->referencia.'</a></td><td>'.$ref_modificada->nombre_proveedor.'</td>';
                            echo '<td>';
                                $ref_modificada->vincularReferenciaProveedor();
                            echo '</td><td>'.$ref_modificada->part_nombre.'</td><td style="text-align:center">'.number_format($ref_kit->piezas, 2, '.', '').'</td><td style="text-align:center">'.number_format($ref_modificada->pack_precio, 2, '.', '').'</td><td style="text-align:center">'.$ref_modificada->unidades.'</td><td style="text-align:center">'.number_format($precio_unidad, 2, '.', '').'</td><td style="text-align:center">'.number_format($precio_referencia, 2, '.', '').'</td></tr>';
                            $coste_kit = $coste_kit + $precio_referencia;
                            $costeKits = $costeKits + $precio_referencia;
                        }
                        echo '</table></div></div>
                            <div class="LabelCreacionBasico" style="margin-top:5px;">Coste Kit</div>
                            <div class="tituloComponente">
                                <table id="tablaTituloPrototipo">
                                <tr>
                                    <td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">
                                        <span class="tituloComp">'.number_format($coste_kit, 2, ',', '.').'€'.'</span>'.'
                                    </td>
                                </tr>
                                </table>
                                <input type="hidden" id="coste_kit-'.$i.'" value="'.$coste_kit.'" />
                            </div>
                            <br />
                        </div>';
                    }
                }
                echo '<input type="hidden" id="costeKits" name="costeKits" value="'.$costeKits.'"/>';
                $precio_total = $precio_total + $costeKits;
            ?>
            </div>
        </div>
        <div id="coste_componente" class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Coste Total Cabina</div>
            <input type="hidden" id="coste_total" value="<?php echo $precio_total;?>" />
            <div id="CosteTotalComponente"><span class="fuenteSimumakNegrita"><?php echo number_format($precio_total, 2, ',', '.');?>€</span></div>
        </div>
        <br/>
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
	        <?php 
	        	}
	        ?>
        </div>
        <br/>
        <br/>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Mostrar archivos de la cabina</div>
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
							$cabinas->dameId_archivo($cabinas->id_componente);
							$ids_archivos = $cabinas->ids_archivos;

							for($i=0;$i<count($ids_archivos);$i++) {
								$array_ids_archivos[] = $ids_archivos[$i]["id_archivo"];
								$cabinas->cargaDatosArchivosCabinaId($array_ids_archivos[$i]);
						?>
						<tr><td><?php echo $cabinas->nombre_archivo;?><input type="hidden" name="archivos_tabla[]" id="archivos_tabla[]" value="<?php echo $cabinas->nombre_archivo;?>" /></td><td style="text-align:center"><?php echo $cabinas->fecha_subida;?></td><td style="text-align:center"><input type="button" id="descargar" name="descargar" class="BotonEliminar"  value="DESCARGAR" onclick="window.open('download.php?id=<?php echo $cabinas->nombre_archivo;?>')"/> </td><?php if($modificar) { ?><td style="text-align:center"><input type="checkbox" name="chkboxArch" value="<?php echo $cabinas->id_archivo;?>" /></td><?php } ?></tr>
                    <?php
					}
					?>
                    </table>
                    </div>
			    </div>
			    <?php if($modificar) { ?>
                	<input type="button" id="quitar" name="quitar" class="BotonQuitar" value="QUITAR" onclick="javascript:removeRowArchivos(mitablaArchivos)"  />
        		<?php } ?>
        </div>
        <br />
        <?php if($modificar) { ?>
        	<input type="checkbox" id="duplicar_cabina" name="duplicar_cabina" value="1" /> <div class="label_check_precios">Duplicar cabina</div>
        <?php } ?>
        <br/>
        <br/>
        <br/>
        <div class="ContenedorBotonCreacionBasico">
           	<input type="button" id="volver" name="volver" value="VOLVER" class="BotonEliminar" onclick="javascript:window.history.back()"/>
            <?php if($modificar) { ?>
	            <input type="hidden" id="guardandoCabina" name="guardandoCabina" value="1"/>
	            <input type="submit" id="continuar" name="continuar" value="CONTINUAR" class="BotonEliminar" />
        	<?php } ?>
        </div>
        <?php
        	if($mensaje_error != "") {
			echo '<div class="mensaje_error">'.$mensaje_error.'</div>';
		}
		?>
        <br />

        <div id="aux">
        	<input type="hidden" id="act_version" name="act_version" value="0"/>
        </div>
   </form>
</div>
<?php include ("../includes/footer.php"); ?>