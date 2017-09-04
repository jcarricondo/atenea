<?php
// Este fichero permite cambiar las fechas de inicio de producción y reajustar la recepción de las OP
// Se recopilan todas las piezas recibidas 
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/control_usuario.class.php");
include("../classes/sede/sede.class.php");
include("../classes/funciones/funciones.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/orden_produccion/gestion_prioridad.class.php");
include("../classes/orden_compra/orden_compra.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/almacen/recepcion_material.class.php");
permiso(12);

$control_usuario = new Control_Usuario();
$sede = new Sede();
$orden_produccion = new Orden_Produccion();
$orden_compra = new Orden_Compra();
$gestionPrioridad = new GestionPrioridad();
$rm = new RecepcionMaterial();
$funciones = new Funciones();
$almacen = new Almacen();

$fallo_conexion = false;

// Obtenemos el tipo de usuario para conocer su sede
$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$id_almacen = $_SESSION["AT_id_almacen"];
// Comprobamos si es Administrador Global
$esAdminGlobal = $control_usuario->esAdministradorGlobal($id_tipo_usuario);
// Comprobamos si es Administrador de Gestion
$esAdministradorGes = $control_usuario->esAdministradorGes($id_tipo_usuario);

if($esAdminGlobal || $esAdministradorGes){
	// Predeterminado
	if($_POST["sedes"] != NULL) $id_sede = $_POST["sedes"];
	else if($_GET["sedes"] != NULL) $id_sede = $_GET["sedes"];
	else $id_sede = 1;
}
else {
	$id_sede = $almacen->dameSedeAlmacen($id_almacen);
	$id_sede = $id_sede["id_sede"];
}

// Actualizar Fechas de Producción
if(isset($_POST["guardarFechasProduccion"]) and ($_POST["guardarFechasProduccion"] == 1)){
	// Proceso para guardar las fechas de inicio de producción 
	$ids_produccion = $_POST["ids_produccion"];
	$fechas_inicio_produccion = $_POST["fechas_inicio_produccion"];
	if($esAdminGlobal || $esAdministradorGes) $id_sede = $_POST["sedes"];

	// Recorremos las órdenes de producción y guardamos aquellas en las que se eligió una fecha
	for($i=0;$i<count($ids_produccion);$i++){
		$id_produccion = $ids_produccion[$i];

		if(!empty($fechas_inicio_produccion[$id_produccion])){
			// Convertimos la fecha a formato MySql
			$fecha = $funciones->cFechaMy($fechas_inicio_produccion[$id_produccion]);

			// Actualizamos la fecha de inicio de producción
			$resultado_fecha = $orden_produccion->cambiarFechaProduccion($id_produccion,$fecha);
			if($resultado_fecha != 1){
				$fallo_conexion = true;
				$mensaje_error = $orden_produccion->getErrorMessage($resultado_fecha);
			} 
		}
	}
}

// Cargamos las Órdenes de Producción de la sede que estén en estado INICIADO y ordenadas por fecha de inicio de construcción
$orden_produccion->dameOrdenesProduccionIniciadasPorConstruccion($id_sede);
$resultados_op_iniciadas = $orden_produccion->id_produccion;
$error = false;
$error_reset_piezas = false;
$error_recepcion_piezas = false;
$error_actualizar_agrupacion = false;

// INICIAR REAJUSTE DE RECEPCION
if(isset($_GET["iniciar"]) and ($_GET["iniciar"] == "YES")){
	// En este proceso solo nos quedaremos con aquellas referencias que cuyas piezas puedan ser transferidas a otras Ordenes de Producción
	// Primero descartamos aquellas referencias que solo pertenezan a una OP iniciada dado que no se traspasará a ninguna OP
	$resultado_referencias = $gestionPrioridad->dameReferenciasVariasOP($id_sede);

	for($i=0;$i<count($resultado_referencias);$i++){
		$id_referencia = $resultado_referencias[$i]["id_referencia"];

		// Si la referencia se recibió completamente en todas las Órdenes de Producción también la descartamos
		$recibida_en_todas = false;
		$recibida_en_todas = $gestionPrioridad->refCompletaEnTodasOP($id_referencia,$id_sede);
		if(!$recibida_en_todas){
			// Si nunca se recibió las piezas de la referencia en ninguna de las Órdenes de Producción también la descartamos
			$vacia_en_todas = false;
			$vacia_en_todas = $gestionPrioridad->refVaciaEnTodasOP($id_referencia,$id_sede);
			if(!$vacia_en_todas){
				// Referencia susceptible de poder ser traspasada sus piezas a otra OP
				$referencias_traspaso[] = $id_referencia;
			}	
		}
	}

	// REALIZAMOS EL BACKUP DE LAS ORDENES DE COMPRA DE CADA REFERENCIA AFECTADA
	for($i=0;$i<count($referencias_traspaso);$i++){	
		$id_referencia = $referencias_traspaso[$i];
		// Obtenemos las ordenes de compra de las referencias susceptibles de ser traspasadas para realizar el BACKUP 
		$resultados_ordenes_backup = $gestionPrioridad->dameOrdenesCompraBACKUP($id_referencia,$id_sede);

		// REALIZAMOS EL BACKUP
		$j=0;
		$error = false;
		while($j<count($resultados_ordenes_backup) and !$error){
			$id_ocr = $resultados_ordenes_backup[$j]["id"];
			$piezas_recibidas_ocr = $resultados_ordenes_backup[$j]["piezas_recibidas"];

			// Hacemos el backup de las referencias de compra de las OP iniciadas
			$gestionPrioridad->setOCReferencia($id_ocr,$piezas_recibidas_ocr);
			$resultado_copia = $gestionPrioridad->guardarReferenciaOC();

			if($resultado_copia != 1){
				// FALLO AL HACER EL BACKUP
				$error = true;
				$mensaje_error .= $gestionPrioridad->getErrorMessage($resultado_copia);
			}
			else {
				// LOG DE LA OPERACION DE BACKUP 
				// $mensaje_error .= '<span style="color: green;">&nbsp;Se ha guardado en el backup</span><span style="color: black;">&nbsp;'.$piezas_recibidas_ocr.'</span><span style="color: green;"> piezas recibidas para el ID compra ['.$id_ocr.']<br/>';  
			}
			$j++;
		}
		if($error){
			$i = count($referencias_traspaso);
			$fallo_conexion = true;
		}
	}

	if(!$fallo_conexion){
		for($i=0;$i<count($referencias_traspaso);$i++){
			$id_referencia = $referencias_traspaso[$i];
			// Obtenemos las ordenes de compra de las referencias susceptibles de ser traspasadas en funcion de su id_referencia
			$resultados_ordenes = $gestionPrioridad->dameOrdenesCompraRecibidasPorIdReferencia($id_referencia,$id_sede);

			// SUMAMOS EL TOTAL DE PIEZAS RECIBIDAS POR REFERENCIA PARA SU AGRUPACION
			$j=0;
			$error = false;
			$total_piezas = 0;
			while($j<count($resultados_ordenes) and !$error){
				$id_ocr = $resultados_ordenes[$j]["id"];
				$piezas_recibidas_ocr = $resultados_ordenes[$j]["piezas_recibidas"];
				// Sumamos las piezas_recibidas de cada OP para poder guardarlas en la tabla de Agrupacion
				$total_piezas = $total_piezas + $piezas_recibidas_ocr;
				$j++;
			}
			if(!$error){
				// REALIZAMOS LA AGRUPACION DE REFERENCIAS
				$gestionPrioridad->setReferencia($id_referencia,$total_piezas);
				$resultado_agrupacion = $gestionPrioridad->guardarReferenciaAgrupacion();

				if($resultado_agrupacion != 1){
					$i = count($referencias_traspaso);
					$fallo_conexion = true;
					$mensaje_error = $gestionPrioridad->getErrorMessage($resultado_agrupacion);
				}
			}
			else{
				$i = count($referencias_traspaso);
				$fallo_conexion = true;
			}
		}
	}

	// COMPROBAMOS SI HUBO FALLO DE CONEXION AL HACER EL BACKUP Y LA AGRUPACION
	if(!$fallo_conexion){
		// Una vez ordenadas las OP obtenemos las referencias de compra de cada OP
		for($i=0;$i<count($resultados_op_iniciadas);$i++){
			// OBTENEMOS LAS REFERENCIAS DE COMPRA DE LA ORDEN DE PRODUCCION QUE SE RECIBIERON
			$id_produccion = $resultados_op_iniciadas[$i]["id_produccion"];
			$referencias_op = $gestionPrioridad->dameReferenciasCompraOrderReferencia($id_produccion);

			// Recorremos las referencias de compra de cada orden de producción
			for($j=0;$j<count($referencias_op);$j++){
				$id = $referencias_op[$j]["id"];
				$id_referencia = $referencias_op[$j]["id_referencia"];
				$total_piezas = $referencias_op[$j]["total_piezas"];

				// Comprobamos si la referencia de la orden de compra pertenece a las que sufrirán reajuste
				if(in_array($id_referencia,$referencias_traspaso)){
					// Buscamos el total de piezas agrupadas de esa referencia 
					$res_total_piezas_agrupacion = $gestionPrioridad->dameTotalPiezasReferenciaAgrupacion($id_referencia);
					$piezas_agrupadas_referencia = $res_total_piezas_agrupacion["total_piezas"];	

					// Si es la primera OP (la más prioritaria) no hace falta que resetemos sus referencias de compra dado que solo podrán aumentar 
					// el número de piezas recibidas por referencia
					if($i == 0){
						// Si quedan piezas en la tabla de agrupación
						if($piezas_agrupadas_referencia > 0){
							// Obtenemos las piezas recibidas para calcular las piezas que quedan por entrar
							$piezas_recibidas = $referencias_op[$j]["piezas_recibidas"];
							$piezas_por_entrar = $total_piezas - $piezas_recibidas;

							if($piezas_por_entrar == 0){
								// Si ya tiene el maximo de piezas recepcionado solo descontamos de la tabla de agrupación. No recepcionamos
								$piezas_agrupadas_referencia = $piezas_agrupadas_referencia - $total_piezas;
							}
							else {
								// Si no ha recibido todas las piezas debemos realizar la recepción y la actualización de las piezas
								$piezas_recibidas = 0;
								$piezas_por_entrar = $total_piezas;

								// Recepcionamos las piezas que faltan a la referencia de la OP más prioritaria
								// Intentamos recepcionar todas las piezas posibles de la referencia
								// Si hay suficientes piezas en la agrupacion de referencias
								if($piezas_por_entrar < $piezas_agrupadas_referencia){
									// Restamos las piezas de la agrupacion de esa referencia
									$piezas_agrupadas_referencia = $piezas_agrupadas_referencia - $piezas_por_entrar;
									// Se recepcionan todas las piezas que faltan
									$piezas_recibidas = $total_piezas;
						    	}
								else {
									// No hay suficientes piezas en la tabla de agrupacion. 
									// Recepcionamos las que queden para esa referencia
									$piezas_recibidas = $piezas_recibidas + $piezas_agrupadas_referencia;
									// Restamos las piezas de la agrupacion de esa referencia
									$piezas_agrupadas_referencia = 0;
								}

								// RECEPCIONAMOS LAS PIEZAS DE LA AGRUPACION POR REFERENCIA
								$resultado_recepcion = $rm->recepcionarPorId($id,$id_referencia,$piezas_recibidas);
								if($resultado_recepcion != 1){
									// ERROR RECEPCION
									$fallo_conexion = true;
									$i = count($resultados_op_iniciadas);
									$j = count($referencias_op);
									$mensaje_error .= $rm->getErrorMessage($resultado_recepcion);
								}
							}

							// SI NO HAY FALLO DE CONEXION ACTUALIZAMOS LA TABLA DE AGRUPACION
							if(!$fallo_conexion){
								// Actualizamos el total de piezas de la referencia agrupada
								$resultado_actualizar_piezas = $gestionPrioridad->actualizarPiezasReferenciaAgrupada($piezas_agrupadas_referencia,$id_referencia);
								if($resultado_actualizar_piezas != 1){
									// ERROR ACTUALIZAR REFERENCIA AGRUPADA
									$fallo_conexion = true;
									$i = count($resultados_op_iniciadas);
									$j = count($referencias_op);
									$mensaje_error .= $gestionPrioridad->getErrorMessage($resultado_actualizar_piezas);
								}							
							}
						}	
					}
					else{
						// NO ES LA OP MAS PRIORITARIA. RESETEAMOS LAS PIEZAS RECIBIDAS 
						$piezas_recibidas = 0;
						$piezas_por_entrar = $total_piezas;

						// Si quedan piezas en la tabla de agrupación
						if($piezas_agrupadas_referencia > 0){
							// Si hay suficientes piezas en la agrupacion de referencias
							if($piezas_por_entrar < $piezas_agrupadas_referencia){
								// Restamos las piezas de la agrupacion de esa referencia
								$piezas_agrupadas_referencia = $piezas_agrupadas_referencia - $piezas_por_entrar;
								// Se recepcionan todas las piezas que faltan
								$piezas_recibidas = $total_piezas;
						    }
							else {
								$piezas_recibidas = $piezas_recibidas + $piezas_agrupadas_referencia;
								// Restamos las piezas de la agrupacion de esa referencia
								$piezas_agrupadas_referencia = 0;
							}

							// RECEPCIONAMOS LAS PIEZAS DE LA AGRUPACION POR REFERENCIA
							$resultado_recepcion = $rm->recepcionarPorId($id,$id_referencia,$piezas_recibidas);
							if($resultado_recepcion != 1){
								// ERROR RECEPCION
								$fallo_conexion = true;
								$i = count($resultados_op_iniciadas);
								$j = count($referencias_op);
								$mensaje_error .= $rm->getErrorMessage($resultado_recepcion);
							}

							// SI NO HAY ERROR DE RECEPCION ACTUALIZAMOS LA TABLA DE AGRUPACION
							if(!$fallo_conexion){
								// Actualizamos el total de piezas de la referencia agrupada
								$resultado_actualizar_piezas = $gestionPrioridad->actualizarPiezasReferenciaAgrupada($piezas_agrupadas_referencia,$id_referencia);
								if($resultado_actualizar_piezas != 1){
									// ERROR ACTUALIZAR REFERENCIA AGRUPADA
									$fallo_conexion = true;
									$i = count($resultados_op_iniciadas);
									$j = count($referencias_op);
									$mensaje_error .= $gestionPrioridad->getErrorMessage($resultado_actualizar_piezas);
								}							
							}

						}
						else {
							// Si no quedan piezas en la tabla de agrupación para esa referencia se han recepcionado en otras OP
							// RESETEAMOS 
							$resultado_recepcion = $rm->recepcionarPorId($id,$id_referencia,0);
							if($resultado_recepcion != 1){
								// ERROR RESET PIEZAS RECIBIDAS
								$fallo_conexion = true;
								$i = count($resultados_op_iniciadas);
								$j = count($referencias_op);
								$mensaje_error .= $rm->getErrorMessage($resultado_recepcion);
							}	
						}
					}
				}	
			}	
		}
	
		if(!$fallo_conexion){
			// OPERACION COMPLETADA
			// BORRAMOS LAS TABLAS DE AGRUPACION DE PIEZAS Y BACKUP
			$resultado_borrado_tablas = $gestionPrioridad->borrarTablas();
			if($resultado_borrado_tablas != 1){
				$mensaje_error .= $gestionPrioridad->getErrorMessage($resultado_borrado_tablas);
			}
			else{
				header("Location: gestionar_produccion_optimizado.php?sedes=$id_sede&ordenado=YES");
			}
			
		}
		else {
			// ERROR AL RECEPCIONAR O AL ACTUALIZAR LA TABLA DE AGRUPACION DE REFERENCIAS
			// RESTAURAMOS EL BACKUP
			$resultado_restaurar_backup = $gestionPrioridad->restaurarBackup();
			if($resultado_restaurar_backup != 1){
				// BORRAMOS LAS TABLAS DE AGRUPACION DE PIEZAS Y BACKUP
				$resultado_borrado_tablas = $gestionPrioridad->borrarTablas();
				if($resultado_borrado_tablas != 1){
					$mensaje_error .= $gestionPrioridad->getErrorMessage($resultado_borrado_tablas);
				}		
			}
			else {
				$mensaje_error .= $gestionPrioridad->getErrorMessage($resultado_restaurar_backup);
			}
		}	
	}
	else {
		// ERROR EN BACKUP O EN AGRUPACION DE PIEZAS
		$fallo_conexion = true;
		// BORRAMOS LAS TABLAS DE AGRUPACION DE PIEZAS Y BACKUP
		$resultado_borrado_tablas = $gestionPrioridad->borrarTablas();
		if($resultado_borrado_tablas != 1){
			$mensaje_error .= $gestionPrioridad->getErrorMessage($resultado_borrado_tablas);
		}
	}
}

/*
// RESTAURAMOS EL BACKUP
$resultado_restaurar_backup = $gestionPrioridad->restaurarBackup();
if($resultado_restaurar_backup != 1){
	$mensaje_error .= $gestionPrioridad->getErrorMessage($resultado_restaurar_backup);
}
else {
	$resultado_borrar_tablas = $gestionPrioridad->borrarTablas();
	if($resultado_borrar_tablas != 1){
		$mensaje_error .= $gestionPrioridad->getErrorMessage($resultado_borrar_tablas);
	}
}
*/

$titulo_pagina = "Órdenes de Producción > Gestionar Producción";
$pagina = "gestionar_produccion";
include ('../includes/header.php');
echo '<script type="text/javascript" src="../js/funciones_24052017_1515.js"></script>';
echo '<script type="text/javascript" src="../js/orden_produccion/gestionar_produccion.js"></script>';
?>

<div class="separador"></div>
<?php include("../includes/menu_op.php");?> 

<div id="ContenedorCentral">
	<div id="ContenedorSidebar"><?php include ("../includes/sidebar.php");?></div>

    <h3>Gestionar Producción</h3>
    <h4>Asignación de prioridad de las &Oacute;rdenes de Producci&oacute;n INICIADAS</h4>
    <br/>
	<br/>
	<div id="contenedorGestionarProduccion">
	<?php
		if(!$fallo_conexion){ ?>
	    	<form id="formularioGestionarProduccion" name="formularioGestionarProduccion" action="gestionar_produccion_optimizado.php" method="post">
	    	<?php 
	    		// Posibilidad de cambiar de sede si es Admin Global
	    		if($esAdminGlobal || $esAdministradorGes){
                    $res_sedes = $sede->dameSedesFabrica(); ?>
	    			<div class="ContenedorCamposCreacionBasico">
						<div class="LabelCreacionBasico">SEDE</div>
						<select id="sedes" name="sedes" class="CreacionBasicoInput" onchange="cargaOrdenesProduccion(this.value)" style="margin:5px 0px;">
                            <?php
                                for($i=0;$i<count($res_sedes);$i++){
                                    $id_sede_bus = $res_sedes[$i]["id_sede"];
                                    $nombre_sede = $res_sedes[$i]["sede"];

                                    echo '<option value='.$id_sede_bus;
                                    if($id_sede_bus == $id_sede){
                                        echo ' selected="selected"';
                                    }
                                    echo '>'.$nombre_sede.'</option>';
                                }
                            ?>
						</select>
					</div>
	    	<?php 
	    		}
	    		if($resultados_op_iniciadas != NULL){ ?>
					<table>
					<tr>
						<th width="5%" style="text-align:center;">#</th>
						<th width="5%" style="text-align:center;">ID</th>
						<th width="30%" style="">NOMBRE</th>
						<th width="10%" style="text-align:center;">UNIDADES</th>
						<th width="20%" style="text-align:center;">RECEPCI&Oacute;N</th>
						<th width="15%" style="text-align:center;">ESTADO</th>
						<th width="15%" style="text-align:center;">FECHA INICIO PRODUCCI&Oacute;N</th>
					</tr>
					<?php
						for($i=0;$i<count($resultados_op_iniciadas);$i++){
							// Obtenemos los id_produccion de las Ordenes de Produccion INICIADAS
							$id_produccion = $resultados_op_iniciadas[$i]["id_produccion"];
							
							// Cargamos los datos de la Orden de Produccion
							$orden_produccion->cargaDatosProduccionId($id_produccion);
							$alias = $orden_produccion->alias_op;
							$estado = $orden_produccion->estado;
							$unidades_op = $orden_produccion->unidades;
							// FECHA INICIO PRODUCCION = FECHA INICIO CONSTRUCCION
							$fecha_inicio_construccion = $orden_produccion->fecha_inicio_construccion; ?>

							<tr>
								<td width="5%" id="celda_prioridad-<?php echo $id_produccion;?>" width="15%" style="text-align:center;"><?php $j=$i+1;	echo $j;?></td>
								<td width="5%" style="text-align:center;"><?php echo $id_produccion; ?></td>
								<td width="30%" style=""><?php echo $alias; ?></td>
								<td width="10%" style="text-align:center;"><?php echo $unidades_op;?></td>
								<td width="20%" style="text-align:center; vertical-align: middle;">
									<?php $orden_produccion->getPorcentajeRecepcion(); ?>
	            					<div align="center">
										<div class="barra_progreso">
											<div class="barra_progreso_activa" style="width: <?php echo $orden_produccion->porcentaje_recepcion; ?>px; !important"></div>
										</div>
	            					</div>
								</td>
								<td width="15%" style="text-align:center;"><?php echo $estado; ?></td>
								<td width="15%" style="text-align:center;">
								<?php
									if($fecha_inicio_construccion != NULL){
										$fecha_inicio_construccion = $funciones->cFechaNormal($fecha_inicio_construccion);
									}
									else{
										$fecha_inicio_construccion = "";	
									}
								?>
									<input type="hidden" id="ids_produccion[]" name="ids_produccion[]" value="<?php echo $id_produccion;?>"> 
									<input type="text" name="fechas_inicio_produccion[<?php echo $id_produccion;?>]" id="datepicker_fecha_inicio_prod-<?php echo $id_produccion;?>" class="fechaCal" style="float:none; width:120px; margin:5px 0;text-align:center;" value="<?php echo $fecha_inicio_construccion;?>" readonly="readonly" />				
								</td>
							</tr>
					<?php
						}
					?>
					</table>
					<br/>
					<br/>
					<br/>
				
					<?php 
						if($_POST["guardarFechasProduccion"] != 1){ ?>
							<div class="ContenedorCamposCreacionBasico">
					        	<div class="LabelCreacionBasico">GUARDAR FECHAS</div>
					        	<input type="hidden" id="guardarFechasProduccion" name="guardarFechasProduccion" value="1" />
					        	<input type="submit" id="botonGuardarFechas" name="botonGuardarFechas" class="BotonEliminar" value="GUARDAR FECHAS" onclick="if (confirm('¿Está seguro de modificar las fechas de inicio de producción para las órdenes de producción señaladas?\n')){ return true; } else{return false;}" />	
					        </div>
			        <?php
			        	}
			        	// Solo reajusta si al menos hay mas de dos Orden de Produccion
			        	if(count($resultados_op_iniciadas) != 0 and count($resultados_op_iniciadas) != 1){ ?>
					        <div class="ContenedorCamposCreacionBasico">
					        	<div class="LabelCreacionBasico">INICIAR REAJUSTE</div>
					        	<input type="button" name="botonAsignarPrioridad" class="BotonEliminar" value="INICIAR REAJUSTE" onclick="if (confirm('¿Está seguro de reajustar la recepción de las Órdenes de Producción?\n\n Esta operación puede tardar varios minutos\n\n ¡IMPORTANTE! No interrumpa el proceso, manténgase a la espera. Si lo hace podrían perderse las piezas recepcionadas de las Órdenes de Producción')){ iniciarProcesoPrioridad(<?php echo $id_sede;?>) } else{}" />	
					        </div>
					<?php
					    }
			    	if($_POST["guardarFechasProduccion"] == 1){
			    		// Mostramos el mensaje de exito de la operacion de guardado de fechas
			    		echo '<span style="color: green; font: bold 13px Helvetica,Verdana,Arial; padding: 5px 0 0 5px;">Se han actualizado las fechas de inicio de producción correctamente</span>';	
			    	}
			    	if($_GET["ordenado"] == "YES"){
			    		// Mostramos el mensaje de exito de la operacion de reajuste de recepcion
			    		$mensaje_error .= '<span style="color: green;">REAJUSTE REALIZADO SATISFACTORIAMENTE</span><br/>';
			    		$mensaje_error .= '<span style="color: green;">&nbsp;BORRADO BACKUP REALIZADO SATISFACTORIAMENTE</span><br/>';
			    		echo '<span style="color: green; font: bold 13px Helvetica,Verdana,Arial; padding: 5px 0 0 5px;">'.$mensaje_error.'</span>';	
			    		}
			    	else {
			    		// Mostramos el mensaje del fallo
			    		echo '<span style="color: green; font: bold 13px Helvetica,Verdana,Arial; padding: 5px 0 0 5px;">'.$mensaje_error.'</span>';		
			    	}
		    	}
		    	else {
					// ERROR NO HAY ORDENES DE PRODUCCION EN ESTADO INICIADO	    		
		    		echo '<span style="color: red; font: bold 13px Helvetica,Verdana,Arial; padding: 5px 0 0 5px;">No hay Órdenes de Producción en estado INICIADO</span>';
		    	}  ?>
	    	</form>
	<?php 
		}
	    else{
	    	// ERROR FALLO CONEXION
	    	echo '<span style="color: red; font: bold 13px Helvetica,Verdana,Arial; padding: 5px 0 0 5px;">'.$mensaje_error.'</span>';
	    }	
    ?>
    </div>
</div>
<?php include ('../includes/footer.php');?>
