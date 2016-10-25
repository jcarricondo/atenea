<?php
// Este fichero permite la opcion de generar escandallos o recuperarlos. 
// El usuario establece el numero de unidades que se van a fabricar por cada componente 
// Se mostrarán solo los componentes que pertenezcan a la Orden de Produccion y se fabriquen en alguno de los boxes activos
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/control_usuario.class.php");
include("../classes/sede/sede.class.php");
include("../classes/funciones/funciones.class.php");
// include("../classes/basicos/cabina.class.php");
include("../classes/basicos/periferico.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/basicos/componente.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/orden_compra/orden_compra.class.php");
include("../classes/producciones/produccion.class.php");
include("../classes/producciones/box.class.php");
include("../classes/almacen/almacen.class.php");
require("../funciones/pclzip/pclzip.lib.php");
permiso(20);

$db = new MySQL();
$control_usuario = new Control_Usuario();
$sede = new Sede();
$funciones = new Funciones();
// $cabina = new Cabina();
$periferico = new Periferico();
$componente = new Componente();
$orden_produccion = new Orden_Produccion();
$produccion = new Produccion();
$box = new Box();
$almacen = new Almacen();

$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$id_almacen = $_SESSION["AT_id_almacen"];
// Comprobamos si es Administrador Global
$esAdminGlobal = $control_usuario->esAdministradorGlobal($id_tipo_usuario);
$esAdministradorGes = $control_usuario->esAdministradorGes($id_tipo_usuario);
// Obtenemos la sede a la que pertenece el usuario 
$id_sede = $almacen->dameSedeAlmacen($id_almacen);
$id_sede = $id_sede["id_sede"];

$hay_ops_activas = true;
$no_fabrican_componentes = false;

if($esAdminGlobal || $esAdministradorGes){
	// Si el Administrador Global cambio de sede cargamos las OP correspondientes 
	if($_GET["id_sede"] != NULL) $id_sede = $_GET["id_sede"]; 
	else if($_GET["cambio_op"] == 1 or $_GET["escandallo"] == "descargar"){
		$orden_produccion->cargaDatosProduccionId($_GET["id_produccion"]);
		$id_sede = $orden_produccion->id_sede;
	}
	else $id_sede = 1; 
}

// Si entramos en "Planificar Produccion" obtenemos la ultima produccion iniciada activa 
if($_GET["id_produccion"] == NULL){
	$orden_produccion->dameUltimaOpIniciadaActiva($id_sede);
	$id_produccion = $orden_produccion->id_produccion["id_produccion"];
	if($id_produccion == NULL) $hay_ops_activas = false;
}	
else{
	// Entramos desde el select de las OPs
	$id_produccion = $_GET["id_produccion"];
}

// Si se quiere descargar o recuperar el zip con los excel
if($_GET["escandallo"] == "descargar"){
	// Obtenemos los datos de la OP y los componentes seleccionados por el usuario
	$id_produccion = $_GET["id_produccion"];
	$num_tecnicos = $_GET["num_tecnicos"];
	$codigo_escandallo = $_GET["codigo"];
	$array_id_componentes = $_GET["ids_componentes"];
	$array_unidades_componentes = $_GET["unidades_componentes"];

	$ids_componentes = explode(",",$array_id_componentes);
	$unidades_componentes = explode(",",$array_unidades_componentes);

	$num_boxes = 0;
	// En funcion de las unidades que se quieran fabricar por cada componente vemos cuantos boxes se van a utilizar
	// Contamos los boxes que se van a utilizar para poder asignarlos en funcion del numero de tecnicos
	for($i=0;$i<count($ids_componentes);$i++){
		$id_componente = $ids_componentes[$i];
		$unidades_componente = $unidades_componentes[$i];

		// Obtenemos los boxes en los que se fabrica ese componente
		$boxes_componente = $box->cargaDatosBoxPorComponente($id_componente,$id_sede);
		// Mientras queden unidades por fabricar contamos los boxes a utilizar
		// Si se completan todos los boxes y sobran unidades las reasignamos de nuevo a los primeros boxes
		$j=0; 
		while ($unidades_componente > 0){
			// Si se han asignado todos los boxes para ese componente y siguen sobrando unidades las asignamos de nuevo a los primeros boxes de ese componente
			if(($j % count($boxes_componente)) == 0){
				$j=0;
			}	
			
			$id_box = $boxes_componente[$j]["id_box"];
			$unidades_fabrican_box = $boxes_componente[$j]["unidades_fabrican"];	
			
			// Guardamos los boxes finales y las unidades finales que se van a fabricar en cada box
			// Puede haber boxes repetidos si el usuario introdujo más unidades en un componente de las que se fabrican en los boxes de ese componente
			// Tambien puede haber boxes repetidos en el caso de que un periférico este repetido en la OP.
			// Se tratan como componentes independientes dado que puede que se quiera fabricar unidades diferentes por cada periférico en una operación.
			$boxes_finales[] = $id_box;
			// Como en un mismo box se pueden fabricar varios componentes debemos llevar un control de que componente se fabrica en el box
			$ids_componentes_finales[] = $id_componente;
			if($unidades_componente > $unidades_fabrican_box){
				$unidades_finales[] = $unidades_fabrican_box;
			}
			else {
				$unidades_finales[] = $unidades_componente;	
			}
			
			$unidades_componente = $unidades_componente - $unidades_fabrican_box;
			$num_boxes++;
			$j++;
		}
	}

	// Obtenemos directorio actual y creamos la carpeta que contendra las carpetas de los proveedores
	$dir_actual = getcwd(); 
	$dir_producciones = getcwd();

	// $dir_actual = $dir_actual."\ESCANDALLOS"; // LOCAL
	$dir_actual = $dir_actual."/ESCANDALLOS"; // PRODUCCION 
	// mkdir($dir_actual.'\ESCANDALLO', 0777); // LOCAL
	mkdir($dir_actual."/ESCANDALLO", 0777); // PRODUCCION
	// $dir_descarga = $dir_actual.'\ESCANDALLO'; // LOCAL
	$dir_descarga = $dir_actual."/ESCANDALLO"; // PRODUCCION
	$dir_actual = $dir_descarga;	

	// Se reparten los boxes entre los tecnicos
	$num_boxes_asignados = $num_boxes;

	if($num_boxes < $num_tecnicos){
    	$tecnicos_asignados = $num_boxes;
    	$num_boxes_por_tecnico = 1;
    }
    else{
    	$tecnicos_asignados = $num_tecnicos;
    	$num_boxes_por_tecnico = floor($num_boxes / $num_tecnicos);
    }

	// Variable para llevar un recuento de los tecnicos asignados
    $tecnicos_por_asignar = $num_tecnicos;
    $tec=1;
    $contador_componente = 0;

    // Cargamos el nombre de la Orden de Produccion
	$orden_produccion->cargaDatosProduccionId($id_produccion);
	$codigo = $orden_produccion->codigo;
	$alias = $orden_produccion->alias_op;

    // Por cada tecnico asignado creamos su carpeta con sus dos excel
    for($i=0;$i<$tecnicos_asignados;$i++){
    	// mkdir($dir_actual.'\TECNICO_'.$tec, 0777); // LOCAL
    	mkdir($dir_actual.'/TECNICO_'.$tec, 0777); // PRODUCCION
    	// $dir_actual = $dir_actual.'\TECNICO_'.$tec; // LOCAL 
    	$dir_actual = $dir_actual.'/TECNICO_'.$tec; // PRODUCCION
    	// Generar el excel con los boxes asignados al tecnico
    	include("../producciones/genera_excel_reposicion.php");
    	// Generar el excel con las referencias agrupadas de los boxes asignados al tecnico
    	include("../producciones/genera_excel_piking.php");
    }

    if($_GET["escandallo"] == "descargar"){
    	// Actualizamos las piezas usadas
    	for($j=0;$j<count($referencias_componentes_seleccionados);$j++){
			$id_ref = $referencias_componentes_seleccionados[$j]["id_referencia"];
			$total_piezas = $referencias_componentes_seleccionados[$j]["piezas"];

			$resultado = $produccion->actualizaPiezasUsadas($id_produccion,$id_ref,$total_piezas);
			if($resultado != 1){
				// ERROR AL ACTUALIZAR LAS PIEZAS USADAS TRAS GENERAR LOS ESCANDALLOS
				$j=count($referencias_componentes_seleccionados);
				$mensaje_error = $produccion->getErrorMessage($resultado);
			}
		}
		
		// Guardamos el log de la operacion de escandallo
		// Guardamos un registro por cada componente a fabricar
		for($j=0;$j<count($ids_componentes);$j++){
			$id_componente = $ids_componentes[$j];
			$unidades_componente = $unidades_componentes[$j];

			$id_usuario = $_SESSION["AT_id_usuario"];
			$consulta = sprintf("insert into escandallo_log (id_usuario,id_produccion,id_componente,unidades_componente,numero_tecnicos,codigo,fecha_creacion) value (%s,%s,%s,%s,%s,%s,current_timestamp)",
				$db->makeValue($id_usuario, "int"),
				$db->makeValue($id_produccion, "int"),
				$db->makeValue($id_componente, "int"),
				$db->makeValue($unidades_componente, "int"),
				$db->makeValue($num_tecnicos, "int"),
				$db->makeValue($codigo_escandallo, "text"));
			$db->setConsulta($consulta);
			$db->ejecutarSoloConsulta();
		}	
		unset($unidades_componente);
	}
	
	// Cambiamos el directorio para que guarde el zip en la carpeta ESCANDALLOS
	// $dir_actual = getcwd()."\ESCANDALLOS"; // LOCAL
	$dir_actual = getcwd()."/ESCANDALLOS"; // PRODUCCION
	chdir($dir_actual);
	
	// Comprimimos la carpeta y generamos el zip 
	$filename = $codigo_escandallo.".zip";
	$zip = new PclZip($codigo_escandallo.'.zip');
	$zip->create("ESCANDALLO");

	// Llamada para abrir o descargar el zip
	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=".$filename);
	header("Expires: 0");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".filesize($filename));
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	header("Content-Description: File Transfer"); 
	readfile($filename);

	// Eliminamos la carpeta creada con sus archivos
	$funciones->eliminarDir($dir_descarga);    
	// Eliminamos el zip temporal
	// unlink($filename);
	$dir_actual = $dir_producciones;
	chdir($dir_actual);
}
if($_GET["escandallo"] == "recuperar"){
	// Obtenemos el nombre del archivo en funcion del codigo del escandallo
	$filename = $_GET["codigo"].".zip";
	// Cambiamos el directorio para buscar el zip en la carpeta ESCANDALLOS
	// $dir_actual = getcwd()."\ESCANDALLOS"; // LOCAL
	$dir_actual = getcwd()."/ESCANDALLOS"; // PRODUCCION
	chdir($dir_actual);

	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=".$filename);
	header("Expires: 0");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".filesize($filename));
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	header("Content-Description: File Transfer"); 
	readfile($filename);

	$dir_actual = $dir_producciones;
	chdir($dir_actual);
}

// Carga los datos de la Orden de Produccion
$orden_produccion->cargaDatosProduccionId($id_produccion);
$alias_op = $orden_produccion->alias_op;
$unidades = $orden_produccion->unidades; 

// Obtenemos los componentes de la OP 
$id_componentes = $orden_produccion->dameComponentesOP($id_produccion); 
// Nos quedamos con los componentes que se esten fabricando en los boxes activos
for($i=0;$i<count($id_componentes);$i++){
	$id_componente = $id_componentes[$i]["id_componente"]; 

	// Guardamos unicamente los componentes que se fabrican en los boxes
	if($box->existeComponenteEnBoxes($id_componente,$id_sede)){
		$id_componente_boxes[] = $id_componente;
	}
	else {
		// Comprobamos que el componente no sea un kit
		$id_tipo_componente = $componente->dameTipoComponente($id_componente);  
		$esComponentePrincipal = $componente->esComponentePrincipal($id_tipo_componente); 
		if($esComponentePrincipal){
			$no_fabrican_componentes = true;
		}
	}
}

$titulo_pagina = "Producción > Ordenar Producción";
$pagina = "producciones";
include ('../includes/header.php');
echo '<script type="text/javascript" src="../js/funciones.js"></script>';
echo '<script type="text/javascript" src="../js/producciones/producciones.js"></script>';
?>

<div class="separador"></div> 
<?php include("../includes/menu_producciones.php"); ?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar"><?php include ("../includes/sidebar.php");?></div>

    <h3> Ordenar Producción </h3>
    <h4> Asignación de componentes para la producción</h4>
    <div style="width: auto; height:auto; margin: 5px 10px 10px 195px; float:none; background:#fff; min-height: 500px;">
    	<form id="formDescargarEscandallo" name="formDescargarEscandallo" action="escandallo_por_componentes.php" method="post">
			<br/>
        	<?php 
        		if($esAdminGlobal || $esAdministradorGes){ ?>
		        	<div class="ContenedorCamposCreacionBasico">
		        		<div class="LabelCreacionBasico">Sede</div>
		           		<select id="sedes" name="sedes" class="BuscadorInput" onchange="javascript:cargaSedesEscandallo(this.value)">
		           			<?php 
								// Obtenemos todas las sedes
		                        $resultados_sedes = $sede->dameSedesFabrica();
		                        for($i=0;$i<count($resultados_sedes);$i++) {
		                            $id_sede_res = $resultados_sedes[$i]["id_sede"];
		                            $nombre_sede = $resultados_sedes[$i]["sede"];

	                                echo '<option value="'.$id_sede_res.'"';
	                                if($id_sede_res == $id_sede){
	                                    echo ' selected="selected"';
	                                }
	                                echo '>'.$nombre_sede.'</option>';
		                        }
							?>
			           	</select>
			        </div>
			<?php 
				}
				if($hay_ops_activas){ ?>
			       	<div class="ContenedorCamposCreacionBasico">
		        		<div class="LabelCreacionBasico">Alias</div>
		           		<label id="alias_op" class="LabelInfoOP"><?php if ($alias_op != NULL){ echo $alias_op;} else { echo $orden_produccion->codigo;}?></label>
		        	</div>    
		        	<div class="ContenedorCamposCreacionBasico">
		        		<div class="LabelCreacionBasico">Unidades</div>
		           		<label id="unidades" class="LabelInfoOP"><?php echo $unidades;?></label>
		        	</div>	
			       	<div class="ContenedorCamposCreacionBasico">
		        		<div class="LabelCreacionBasico">Orden de Producción (INICIADA)</div>
		           		<select id="orden_produccion" name="orden_produccion" class="BuscadorInput" onchange="javascript:cargaEscandalloPorComponentes(this.value)">
		           			<?php 
		           				$orden_produccion->dameOrdenesProduccionIniciadas($id_sede);
		           				$resultados_op = $orden_produccion->id_produccion;

								for($i=0;$i<count($resultados_op);$i++){
									$orden_produccion->cargaDatosProduccionId($resultados_op[$i]["id_produccion"]);
									$alias_op = $orden_produccion->alias_op;
									$codigo = $orden_produccion->codigo;

									echo '<option value="'.$resultados_op[$i]["id_produccion"].'" ';
									if ($resultados_op[$i]["id_produccion"] == $id_produccion){
											echo ' selected="selected" ';
										}
									if ($alias_op != null){ 								
										echo '>'.$alias_op.'</option>';	
									}
									else {
										echo '>'.$codigo.'</option>';		
									}	
								}
							?>
			           	</select>
			        </div>
			        <div class="ContenedorCamposCreacionBasico">
						<div class="LabelCreacionBasico">Técnicos Almacen:</div>
						<input type="text" id="num_tecnicos" value="" class="BuscadorInput" onkeypress="return soloNumeros(event)">
					</div>		
		        	<br/>
		        	<br/>

		        	<?php 
						if($id_componente_boxes != NULL){ 
					?>
							<div class="ContenedorCamposCreacionBasico">
								<span style="font: 13px Helvetica,Verdana,Arial; font-weight:bold; color: #2998CC; padding: 0px;">AVISO: Si ya realizó una operación de escandallo sobre esta Orden de Producción compruebe las unidades restantes por fabricar de cada componente</span>			
							</div>
						<?php
							// Mostramos un aviso en el caso de que haya componentes de la OP que no se fabriquen en ningun box
							if($no_fabrican_componentes){ ?>
								<div class="ContenedorCamposCreacionBasico">
									<span style="font: 13px Helvetica,Verdana,Arial; font-weight:bold; color: orange; padding: 0px;">AVISO: Algunos de los componentes de la Orden de Producción no se fabrican en ningun box. No aparecerán en la lista de componentes, ni en la operación de escandallo</span>			
								</div> 
						<?php 
							}
						?>
				        	<table>
								<tr>
									<th width="80%"><?php if ($alias_op != NULL){ echo $alias_op;} else { echo $orden_produccion->codigo;}?></th>
									<th width="10%" style="text-align: center"><input type="text" id="todas_unidades" name="todas_unidades" style="width: 50px; text-align:center;" onkeypress="return soloNumeros(event)" onblur="rellenaUnidades();" /></th>
									<th width="10%" style="text-align:center"><input type="checkbox" name="todos_Checkbox" id="todos_Checkbox" onclick="todosCheckbox();"/></th>
								</tr>
						<?php
							// Cargamos todos los elementos de la OP que se esten fabricando 
							for($i=0;$i<count($id_componente_boxes);$i++){
								$id_componente = $id_componente_boxes[$i];

								$id_tipo_componente = $orden_produccion->dameTipoComponente($id_componente);
								$id_tipo_componente = $id_tipo_componente["id_tipo"];
						?>
								<tr>
							<?php				
								// El id_tipo solo puede ser 1 o 2 dependiendo de si es una cabina o un periferico
								if($id_tipo_componente == 1){
									// $cabina->cargaDatosCabinaId($id_componente);
							?>
									<!-- <td width="80%"><?php // echo $cabina->cabina; ?></td>-->
							<?php
								}
								else {
									$periferico->cargaDatosPerifericoId($id_componente);
							?>
									<td width="80%"><?php echo $periferico->periferico; ?></td>						
							<?php			
								}
							?>	
									<td width="10%" style="text-align: center"><input type="text" id="unidades_componente[]" name="unidades_componente[]" style="width: 50px; text-align:center;" value="<?php echo $unidades_componente;?>" onkeypress="return soloNumeros(event)" /></td>
									<td width="10%" style="text-align: center"><input type="checkbox" id="chkbox[]" name="chkbox[]" value="<?php echo $id_componente_boxes[$i];?>"/ ></td>
								</tr>		
						<?php
							}
						?>		
							</table>
							</br>
							<div class="ContenedorCamposCreacionBasico">
					        	<div class="LabelCreacionBasico">Descargar Escandallo</div>
					          	<input type="button" id="descargar" name="descargar" value="DESCARGAR" class="BotonEliminar" onclick="javascript:validarDescargaEscandalloPorComponentes();">
								<input type="hidden" id="id_op" name="id_op" value="<?php echo $id_produccion;?>">
								<input type="hidden" id="unidades_op" name="unidades_op" value="<?php echo $unidades;?>">
					        </div> 

					        <div class="ContenedorCamposCreacionBasico">
					        	<div class="LabelCreacionBasico">Recuperar Escandallo</div>
					       		<input type="button" id="recuperar" name="recuperar" value="RECUPERAR" class="BotonEliminar" style="vertical-align: middle;" onclick="window.location.href = 'recuperar_escandallos.php'" />
					        </div>
					<?php
						}
						else {
					?>   
							<div class="ContenedorCamposCreacionBasico">
								<span style="font: 13px Helvetica,Verdana,Arial; font-weight:bold; color: red; padding: 0px;">AVISO: No hay ningún componente de la Orden de Producción que se fabrique en algunos de los boxes de fabricación habilitados</span>			
							</div>
					<?php 
						}
					?>
				<?php
					if($_GET["escandallo"] == "recuperar"){
						$mensaje_error = '<span style="color: green"> Se han recuperado los escandallos correctamente</span>';
						echo '<div class="mensaje_error">'.$mensaje_error.'</div>';
					}
					else if	($_GET["escandallo"] == "descargar"){
						$mensaje_error = '<span style="color: green"> Se han generado los escandallos y actualizado las piezas usadas correctamente</span>';	
						echo '<div class="mensaje_error">'.$mensaje_error.'</div>';
					}	
				?>
			<?php 
				}
				else { ?>
					<br/>
					<br/>
					<div class="ContenedorCamposCreacionBasico">
						<span style="font: 13px Helvetica,Verdana,Arial; font-weight:bold; color: red; margin-left: 5px; padding: 0px;">AVISO: No hay ninguna Orden de Producción en estado "INICIADO". No hay ninguna Orden de Producción lista para ordenar</span>			
					</div>
			<?php 
				}
			?>
        <br />
        </form>
    </div>
</div>
<?php include ('../includes/footer.php');?>
