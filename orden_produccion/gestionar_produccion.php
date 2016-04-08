<?php
// Este fichero permite cambiar las fechas de inicio de produccion y reajustar la recepcion de las OP
// Se recopilan todas las piezas recibidas 
/*
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/orden_produccion/gestion_prioridad.class.php");
include("../classes/orden_compra/orden_compra.class.php");
include("../classes/almacen_piezas/recepcion_material.class.php");
include("../classes/control_usuario.class.php");
include("../classes/kint/Kint.class.php");
permiso(12);

// FALTA:  Definir permisos
$orden_produccion = new Orden_Produccion();
$orden_compra = new Orden_Compra();
$gestionPrioridad = new GestionPrioridad();
$rm = new RecepcionMaterial();
$funciones = new Funciones();
$control_usuario = new Control_Usuario();

// Obtenemos el tipo de usuario para conocer su sede
$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];

if(isset($_GET["id_sede"]) && $_GET["id_sede"] != NULL){
	$id_sede = $_GET["id_sede"];	
}
else {
	if($id_tipo_usuario == 1){
		$id_sede = 1;
	}
	else {
		$id_sede = $control_usuario->dameSedeSegunUsuario($id_tipo_usuario);
	}
}

// Actualizar Fechas de Produccion
if(isset($_POST["guardarFechasProduccion"]) and ($_POST["guardarFechasProduccion"] == 1)){
	// Proceso para guardar las fechas de inicio de produccion 
	$ids_produccion = $_POST["ids_produccion"];
	$fechas_inicio_produccion = $_POST["fechas_inicio_produccion"];

	// Recorremos las ordenes de produccion y guardamos aquellas en las que se eligió una fecha
	for($i=0;$i<count($ids_produccion);$i++){
		$id_produccion = $ids_produccion[$i];

		if(!empty($fechas_inicio_produccion[$id_produccion])){
			// Convertimos la fecha a formato MySql
			$fecha = $funciones->cFechaMy($fechas_inicio_produccion[$id_produccion]);

			// Actualizamos la fecha de inicio de produccion
			$resultado_fecha = $orden_produccion->cambiarFechaProduccion($id_produccion,$fecha);
			if($resultado_fecha != 1){
				$fallo_conexion = true;
				$mensaje_error = $orden_produccion->getErrorMessage($resultado_fecha);
			} 
		}
	}
}

// Cargamos las Ordenes de Produccion que esten en estado INICIADO y ordenadas por fecha de inicio de construcción
$orden_produccion->dameOrdenesProduccionIniciadasPorConstruccion($id_sede);
$resultados_op_iniciadas = $orden_produccion->id_produccion;
$fallo_conexion = false;
$error = false;
$error_reset_piezas = false;
$error_recepcion_piezas = false;
$error_actualizar_agrupacion = false;
$mensaje_error = "";

// INICIAR REAJUSTE DE RECEPCION
if(isset($_GET["iniciar"]) and ($_GET["iniciar"] == "YES")){
	$t_ini_proceso = microtime(true);
	$t_ini_backup = microtime(true);

	// BACKUP DE LAS REFERENCIAS DE COMPRA DE LAS OP INICIADAS
	for($i=0;$i<count($resultados_op_iniciadas);$i++){
		// Obtenemos las referencias de compra de las OP
		$id_produccion = $resultados_op_iniciadas[$i]["id_produccion"];
		$resultados_ocr_op = $gestionPrioridad->damePiezasRecibidasReferenciasCompra($id_produccion);

		$j=0;
		$error = false;
		while($j<count($resultados_ocr_op) and !$error){
			$id_ocr = $resultados_ocr_op[$j]["id"];
			$piezas_recibidas_ocr = $resultados_ocr_op[$j]["piezas_recibidas"];
			
			// Hacemos el backup de las referencias de compra de las OP iniciadas
			$gestionPrioridad->setOCReferencia($id_ocr,$piezas_recibidas_ocr);
			$resultado_copia = $gestionPrioridad->guardarReferenciaOC();

			if($resultado_copia != 1){
				// FALLO AL HACER EL BACKUP
				$error = true;
				$mensaje_error = $gestionPrioridad->getErrorMessage($resultado_copia);
			}
			$j++;
		}
		if($error){
			$i = count($resultados_op_iniciadas);
			$fallo_conexion = true;
		}
	}

	$t_fin_backup = microtime(true);
	$t_total_backup = $t_fin_backup - $t_ini_backup;
	d($t_total_backup);

	$t_ini_agrupacion = microtime(true);

	// COMPROBAMOS SI HUBO FALLO DE CONEXION AL HACER EL BACKUP
	if(!$fallo_conexion){
		// AGRUPAMOS TODAS LAS PIEZAS RECIBIDAS DE LAS OP INICIADAS
		$referencias_agrupadas = $gestionPrioridad->dameReferenciasAgrupadas();
		for($i=0;$i<count($referencias_agrupadas);$i++){
			$id_referencia = $referencias_agrupadas[$i]["id_referencia"];
			$total_piezas = $referencias_agrupadas[$i]["total_piezas"];
			
			$gestionPrioridad->setReferencia($id_referencia,$total_piezas);
			$resultado_agrupacion = $gestionPrioridad->guardarReferenciaAgrupacion();

			if($resultado_agrupacion != 1){
				$i = count($referencias_agrupadas);
				$error = true;
				$mensaje_error = $gestionPrioridad->getErrorMessage($resultado_agrupacion);
			}
		}	

		$t_fin_agrupacion = microtime(true);
		$t_total_agrupacion = $t_fin_agrupacion - $t_ini_agrupacion;
		d($t_total_agrupacion);

		$t_ini_recepcion = microtime(true);

		// COMPROBAMOS SI HUBO ERROR AL HACER LA AGRUPACION DE REFERENCIAS
		if(!$error){
			// Una vez ordenadas las OP obtenemos las referencias de compra de cada OP
			// Luego reseteamos las referencias de compra para el reajuste de recepcion
			// Por ultimo recepcionamos todas las referencias posibles y vamos descontando de la agrupacion
			for($i=0;$i<count($resultados_op_iniciadas);$i++){
				$id_produccion = $resultados_op_iniciadas[$i]["id_produccion"];
	
				// OBTENEMOS LAS REFERENCIAS DE COMPRA DE LA ORDEN DE PRODUCCION QUE SE RECIBIERON 
				$referencias_op = $gestionPrioridad->dameReferenciasCompraOrderReferencia($id_produccion);
				
				// RESETEAMOS LAS REFERENCIAS DE COMPRA RECIBIDA QUE TUVIESE LA ORDEN DE PRODUCCION 
				$resultado_resetear_piezas = $gestionPrioridad->resetearPiezasRecibidasOP($id_produccion);

				// COMPROBAMOS SI HUBO ERROR AL RESETEAR LAS REFERENCIAS DE COMPRA DE LA OP
				if($resultado_resetear_piezas == 1){
					for($j=0;$j<count($referencias_op);$j++){
						$id = $referencias_op[$j]["id"];
						$id_referencia = $referencias_op[$j]["id_referencia"];
						$total_piezas = $referencias_op[$j]["total_piezas"];
						$piezas_recibidas = 0;
						// $piezas_por_entrar = $total_piezas - $piezas_recibidas;
						$piezas_por_entrar = $total_piezas;

						// Buscamos el total de piezas agrupadas de esa referencia 
						$res_total_piezas_agrupacion = $gestionPrioridad->dameTotalPiezasReferenciaAgrupacion($id_referencia);
						$piezas_agrupadas_referencia = $res_total_piezas_agrupacion["total_piezas"];

						// Si quedan piezas agrupadas para esa referencia las intentamos recepcionar
						if($piezas_agrupadas_referencia > 0){
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
							if($resultado_recepcion == 1){
								// Actualizamos el total de piezas de la referencia agrupada
								$resultado_actualizar_piezas = $gestionPrioridad->actualizarPiezasReferenciaAgrupada($piezas_agrupadas_referencia,$id_referencia);
								if($resultado_actualizar_piezas != 1){
									// ERROR ACTUALIZAR REFERENCIA AGRUPADA
									$error = true;
									$error_actualizar_agrupacion = true;
									$i=count($ops_ordenadas);
									$j=count($referencias_op);	
								}
							}
							else {
								// ERROR RECEPCION
								$error = true;
								$error_recepcion_piezas = true;
								$i=count($ops_ordenadas);
								$j=count($referencias_op);
							}
						}
					}
				}
				else {
					// ERROR AL RESETEAR LAS PIEZAS
					$error = true;
					$error_reset_piezas = true;
					$i=count($ops_ordenadas);
					$j=count($referencias_op);
				}
			}
			if(!$error){
				$t_fin_recepcion = microtime(true);
				$t_total_recepcion = $t_fin_recepcion - $t_ini_recepcion;
				d($t_total_recepcion);

				$t_ini_borrado_tablas = microtime(true);

				// OPERACION COMPLETADA
				// BORRAMOS LAS TABLAS DE AGRUPACION DE PIEZAS Y BACKUP
				$mensaje_error .= "Se reajustó la recepción de las Órdenes de Producción";
				$resultado_borrado_tablas = $gestionPrioridad->borrarTablas();
				if($resultado_borrado_tablas != 1){
					$mensaje_error .= $gestionPrioridad->getErrorMessage($resultado_borrado_tablas);
				}

				$t_fin_borrado_tablas = microtime(true);
				$t_total_borrado_tablas = $t_fin_borrado_tablas - $t_ini_borrado_tablas;
				d($t_total_borrado_tablas);
			}
			else {
				// ERROR AL RESETEAR LAS PIEZAS O AL RECEPCIONAR O AL ACTUALIZAR LA TABLA DE AGRUPACION
				$fallo_conexion = true;
				if($error_reset_piezas){
					$mensaje_error .= $gestionPrioridad->getErrorMessage($resultado_resetear_piezas);
				}
				else if($error_recepcion_piezas){
					$mensaje_error .= 'Se produjo un error de conexión al recepcionar las piezas. Repita la operación pasados unos minutos<br/>';
				}	
				else if($error_actualizar_agrupacion){
					$mensaje_error .= $gestionPrioridad->getErrorMessage($resultado_actualizar_piezas);
				}
				else {
					$mensaje_error .= "ERROR INESPERADO. Repita la operación pasados unos minutos<br/>";
				}

				// RESTAURAMOS EL BACKUP
				$resultado_restaurar_backup = $gestionPrioridad->restaurarBackup();
				if($resultado_restaurar_backup != 1){
					$mensaje_error .= $gestionPrioridad->getErrorMessage($resultado_restaurar_backup);
				}

				// BORRAMOS LAS TABLAS DE AGRUPACION DE PIEZAS Y BACKUP
				$resultado_borrado_tablas = $gestionPrioridad->borrarTablas();
				if($resultado_borrado_tablas != 1){
					$mensaje_error .= $gestionPrioridad->getErrorMessage($resultado_borrado_tablas);
				}
			}
		}
		else {
			// ERROR AL AGRUPAR LAS PIEZAS RECIBIDAS DE LAS OP INICIADAS
			$fallo_conexion = true;
			// BORRAMOS LAS TABLAS DE AGRUPACION DE PIEZAS Y BACKUP
			$resultado_borrado_tablas = $gestionPrioridad->borrarTablas();
			if($resultado_borrado_tablas != 1){
				$mensaje_error .= $gestionPrioridad->getErrorMessage($resultado_borrado_tablas);
			}
		}
	}	
	else {
		// ERROR AL HACER EL BACKUP
		// Borramos la parte del backup que se haya guardado 
		$resultado_borrado_backup = $gestionPrioridad->borrarBackup();
		if($resultado_borrado_backup == 1){
			$mensaje_error .= $gestionPrioridad->getErrorMessage($resultado_borrado_backup);
		}
	}
	$t_fin_proceso = microtime(true);

	$t_total = $t_fin_proceso - $t_ini_proceso;
	d($t_total);
}

/*
// RESTAURAMOS EL BACKUP
$resultado_restaurar_backup = $gestionPrioridad->restaurarBackup();
if($resultado_restaurar_backup != 1){
	$mensaje_error .= $gestionPrioridad->getErrorMessage($resultado_restaurar_backup);
}
*-/

$titulo_pagina = "Órdenes de Producción > Gestionar Producción";
$pagina = "gestionar_produccion";
include ('../includes/header.php');
echo '<script type="text/javascript" src="../js/funciones.js"></script>';
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
		if(!$fallo_conexion){
			if($resultados_op_iniciadas != NULL){
	?>
	    	<form id="formularioGestionarProduccion" name="formularioGestionarProduccion" action="gestionar_produccion.php" method="post">
				<table>
					<tr>
						<th width="5%" style="text-align:center;">#</th>
						<th width="5%" style="text-align:center;">ID</th>
						<th width="40%" style="">NOMBRE</th>
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
							// FECHA INICIO PRODUCCION = FECHA INICIO CONSTRUCCION
							$fecha_inicio_construccion = $orden_produccion->fecha_inicio_construccion;
					?>
							<tr>
								<td width="5%" id="celda_prioridad-<?php echo $id_produccion;?>" width="15%" style="text-align:center;"><?php $j=$i+1;	echo $j;?></td>
								<td width="5%" style="text-align:center;"><?php echo $id_produccion; ?></td>
								<td width="40%" style=""><?php echo $alias; ?></td>
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
				<?php
					if($id_tipo_usuario == 1){
						// ADMINISTRADOR GLOBAL
				?>
					<div class="ContenedorCamposCreacionBasico">
						<div class="LabelCreacionBasico">Sede</div>
		            	<select id="id_sede" name="id_sede" class="CreacionBasicoInput" onchange="cargaOrdenesProduccion(this.value)" style="margin:5px 0px;">
		            		<option value="1" <?php if($id_sede == 1) echo ' selected="selected"';?>>SIMUMAK</option>';	
							<option value="2" <?php if($id_sede == 2) echo ' selected="selected"';?>>TORO</option>';		
						</select>	
			        </div>
				<?php 
					}
					if($_POST["guardarFechasProduccion"] != 1){
				?>
						<div class="ContenedorCamposCreacionBasico">
				        	<div class="LabelCreacionBasico">GUARDAR FECHAS</div>
				        	<input type="hidden" id="guardarFechasProduccion" name="guardarFechasProduccion" value="1" />
				        	<input type="submit" id="botonGuardarFechas" name="botonGuardarFechas" class="BotonEliminar" value="GUARDAR FECHAS" onclick="if (confirm('¿Está seguro de modificar las fechas de inicio de producción para las órdenes de producción señaladas?\n')){ return true; } else{return false;}" />	
				        </div>
		        <?php
		        	}
	        		// Solo reajusta si al menos hay mas de dos Orden de Produccion
		        	if(count($resultados_op_iniciadas) != 0 and count($resultados_op_iniciadas) != 1){
		        ?>
				        <div class="ContenedorCamposCreacionBasico">
				        	<div class="LabelCreacionBasico">INICIAR REAJUSTE</div>
				        	<input type="button" name="botonAsignarPrioridad" class="BotonEliminar" value="INICIAR REAJUSTE" onclick="if (confirm('¿Está seguro de reajustar la recepción de las Órdenes de Producción?\n\n Esta operación puede tardar varios minutos\n\n ¡IMPORTANTE! No interrumpa el proceso, manténgase a la espera. Si lo hace podrían perderse las piezas recepcionadas de las Órdenes de Producción')){ iniciarProcesoPrioridad() } else{}" />	
				        </div>
				<?php
				    }
				?> 
	        </form>
	        <br/>
    <?php
	    		if($_POST["guardarFechasProduccion"] == 1){
	    			// Mostramos el mensaje de exito de la operacion de guardado de fechas
	    			echo '<span style="color: green; font: bold 13px Helvetica,Verdana,Arial; padding: 5px 0 0 5px;">Se han actualizado las fechas de inicio de producción correctamente</span>';	
	    		}
	    		if($_GET["iniciar"] == "YES"){
	    			// Mostramos el mensaje de exito de la operacion de reajuste de recepcion
	    			echo '<span style="color: green; font: bold 13px Helvetica,Verdana,Arial; padding: 5px 0 0 5px;">'.$mensaje_error.'</span>';	
	    		}
	    	}
	    	else {
				// ERROR NO HAY ORDENES DE PRODUCCION EN ESTADO INICIADO	    		
	    		echo '<span style="color: red; font: bold 13px Helvetica,Verdana,Arial; padding: 5px 0 0 5px;">No hay Órdenes de Producción en estado INICIADO</span>';
	    	}
	    }
	    else{
	    	// ERROR FALLO CONEXION
	    	echo '<span style="color: red; font: bold 13px Helvetica,Verdana,Arial; padding: 5px 0 0 5px;">'.$mensaje_error.'</span>';
	    }	
    ?>
    </div>
</div>
<?php include ('../includes/footer.php');*/?>
