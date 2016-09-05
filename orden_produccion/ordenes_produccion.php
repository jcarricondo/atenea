<?php
set_time_limit(10000);
// Este fichero muestra el listado de las órdenes de producción
include("../includes/sesion.php");
include("../classes/control_usuario.class.php");
include("../classes/sede/sede.class.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/cabina.class.php");
include("../classes/basicos/periferico.class.php");
/* include("../classes/basicos/software.class.php"); */
include("../classes/basicos/nombre_producto.class.php");
include("../classes/basicos/listado_cabinas.class.php");
include("../classes/basicos/listado_perifericos.class.php");
/* include("../classes/basicos/listado_softwares.class.php"); */
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/orden_produccion/listado_ordenes_produccion.class.php");
include("../classes/orden_produccion/incluir_referencia_libre.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/almacen/recepcion_material.class.php");
include("../classes/productos/producto.class.php");
permiso(8);

$control_usuario = new Control_Usuario();
$sede = new Sede();
$funciones = new Funciones();
$cab = new Cabina();
$perif = new Periferico();
/* $soft = new Software(); */
/* $softs = new listadoSoftwares(); */
$perifs = new listadoPerifericos();
$cabs = new listadoCabinas();
$op = new Orden_Produccion();
$orden_prod = new Orden_Produccion();
$ordenes_produccion = new listadoOrdenesProduccion();
$listadoRefLibres = new listadoOrdenesProduccion();
$listadoAlias = new listadoOrdenesProduccion();
$ref_libre = new Referencia_Libre();
$almacen = new Almacen();
$stock = new RecepcionMaterial();

$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$id_almacen = $_SESSION["AT_id_almacen"];
$esAdminGlobal = $control_usuario->esAdministradorGlobal($id_tipo_usuario);
$esUsuarioGes = $control_usuario->esUsuarioGes($id_tipo_usuario);

// Obtenemos la sede a la que pertenece el usuario 
$id_sede = $almacen->dameSedeAlmacen($id_almacen);
$id_sede = $id_sede["id_sede"];

if($_GET["OProduccion"] == "creado" or $_GET["OProduccion"] == "modificado" or $_GET ["OProduccion"] == "eliminado" or $_GET ["OProduccion"] == "iniciado" or $_GET["fechas_mod"] == "YES" or $_GET["realizarBusqueda"] == 1) {
	$realizarBusqueda = 1;
}
// Si se ha pulsado un boton de cambio de estado (FIN) se modificará su estado
if($_GET["cambiar_estado"] == "true"){
	// Obtenemos la id_producción y el estado de la Orden de Producción por url 
	$id_prod = $_GET["id_produccion"];
	$estado_anterior = $_GET["estado_anterior"];

	// Obtenemos la sede 
	$op->cargaDatosProduccionId($id_prod);
	$id_sede_ce = $op->id_sede;
		
	// En esta transición se debe finalizar tambien los productos de la Orden de Producción
	if($estado_anterior == "INICIADO") {
		$estado_siguiente = "FINALIZADO";	
		$resultado = $op->finalizarProductos($id_prod);
		
		if($resultado == 1){
			// Actualizamos las piezas recibidas. En este punto se reciben todas las piezas de la OP
			$resultado = $op->recibirTodasPiezasOP($id_prod);

			if($resultado == 1){
				// Actualiza el estado de la Orden de Producción
				$resultado_actualizar_estado = $op->actualizaEstadoOP($id_prod, $estado_siguiente);
				if ($resultado_actualizar_estado == 1) {
					$estado_cambiado = 1;	
					header("Location: ordenes_produccion.php?OProduccion=cambio_estado_message&realizandoBusqueda=1&sedes=$id_sede_ce");				
				}
				else {
					$mensaje_error = $op->getErrorMessage($resultado_actualizar_estado);
					echo '<script>alert("'.$mensaje_error.'")</script>';
				}	
			}
			else{
				$mensaje_error = $op->getErrorMessage($resultado);
				echo '<script>alert("'.$mensaje_error.'")</script>';			
			}
		}
		else {
			$mensaje_error = $op->getErrorMessage($resultado);
			echo '<script>alert("'.$mensaje_error.'")</script>';		
		}
	}
}
if($_GET ["OProduccion"] == "iniciado"){
	// Si la Orden de Produccion se ha iniciado debemos recepcionar todas las piezas de Stock que formen parte de la Orden de Produccion
	$id_produccion = $_GET["id_produccion"];

	 // Obtenemos la sede y el almacen del usario 
    if($esAdminGlobal || $esUsuarioGes){
        $id_sede = $_GET["sedes"];
        $id_almacen = $_GET["almacenes"];
    }
    else {
        $id_almacen = $_SESSION["AT_id_almacen"];
    }

	// Recorremos todas las referencias de las Ordenes de Compra generadas para esa Orden de Produccion
	$res = $op->dameReferenciasCompra($id_produccion);
	for($i=0;$i<count($res);$i++){
		$id = $res[$i]["id"];
		$id_referencia = $res[$i]["id_referencia"];
		$total_piezas = $res[$i]["total_piezas"];

		// Comprobamos si existen piezas en stock de esa referencia
		$piezas_en_stock = $stock->damePiezasReferenciaStock($id_referencia,$id_almacen);
		if($piezas_en_stock == NULL) $piezas_en_stock = 0;
		
		// Si hay piezas en STOCK recepcionamos en la OC y descontamos de STOCK
		if($piezas_en_stock > 0){
			// Recepcionamos y descontamos de STOCK
			if($piezas_en_stock >= $total_piezas){
				// Recepcionamos las piezas de Stock
				$piezas_recibidas = $total_piezas;
				$resultado = $stock->recepcionarPorId($id,$id_referencia,$piezas_recibidas);
				if($resultado == 1){
					// Descontamos las piezas de stock
					$resultado = $stock->quitarDelStock($id_referencia,$total_piezas,$id_almacen);
				}
				else {
					$mensaje_error = $stock->getErrorMessage($resultado);
				}
			}
			else {
				// Recepcionamos todas las piezas que quedan en stock
				$piezas_recibidas = $piezas_en_stock;
				$resultado = $stock->recepcionarPorId($id,$id_referencia,$piezas_recibidas);
				if($resultado == 1){
					// Ponemos las pieza de stock a 0
					$resultado = $stock->retiraPiezaStock($id_referencia,$id_almacen);
				}
				else {
					$mensaje_error = $stock->getErrorMessage($resultado);
				}
			}
		}
	}
	header("Location: ordenes_produccion.php?OProduccion=iniciado_message&realizandoBusqueda=1&sedes=$id_sede");
}
if(($_GET ["OProduccion"] == "iniciado_message") || ($_GET ["OProduccion"] == "cambiar_estado_message")){
	 // Obtenemos la sede y el almacen del usario 
    if($esAdminGlobal || $esUsuarioGes){
        $id_sede = $_GET["sedes"];
        $id_almacen = $_GET["almacenes"];
    }
    else {
        $id_almacen = $_SESSION["AT_id_almacen"];
    }	
}

// Se obtienen los datos del formulario
if(isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) {
	$mostrar_tabla = true;
	$unidades = $_GET["unidades"]; // numero de unidades del producto
	$cabina = $_GET["cabina"];	// id de la cabina 
	$periferico = $_GET["periferico"]; // id del periferico
	/* $software = $_GET["software"];*/  // id del software
	$fecha_inicio = $_GET["fecha_inicio"];
	$fecha_entrega = $_GET["fecha_entrega"];
	$fecha_entrega_deseada = $_GET["fecha_entrega_deseada"];
	$estado = $_GET["estado"];
	$ref_libres = $_GET["ref_libres"];
	$fecha_desde = $_GET["fecha_desde"];
	$fecha_hasta = $_GET["fecha_hasta"];
	$alias_op = $_GET["alias_op"];
	$id_tipo = $_GET["tipo"];

	// Obtenemos la sede a la que pertenece el usuario 
	if(($esAdminGlobal || $esUsuarioGes) && $_GET["sedes"] != NULL){
		$id_sede = $_GET["sedes"];
	}
	else if($_GET["cambiar_estado"]){
		$id_sede = $id_sede_ce;
	}
	else {
		$id_sede = $almacen->dameSedeAlmacen($id_almacen);
		$id_sede = $id_sede["id_sede"];
	}
	
	if (!is_numeric($unidades)) $unidades = NULL;
	if ($id_tipo == NULL or $id_tipo == 0) $id_tipo = 1;
	
	// Convertimos las fechas al tipo date de la base de datos
	if ($fecha_inicio != '') $fecha_inicio = $ordenes_produccion->cFechaMy($fecha_inicio);
	if ($fecha_entrega != '') $fecha_entrega = $ordenes_produccion->cFechaMy($fecha_entrega);
	if ($fecha_entrega_deseada != '') $fecha_entrega_deseada = $ordenes_produccion->cFechaMy($fecha_entrega_deseada);
	
	if ($fecha_desde != "") $fecha_desde = $funciones->cFechaMy($fecha_desde);
	if ($fecha_hasta != "") $fecha_hasta = $funciones->cFechaMy($fecha_hasta);

	// Se pasan los datos del buscador a la clase del listado y se realiza la consulta a la base de datos
	$ordenes_produccion->setValores($unidades,$cabina,$periferico,$num_ordenadores,$ordenador,$software,$fecha_inicio,$fecha_entrega,$fecha_entrega_deseada,$estado,$ref_libres,$fecha_desde,$fecha_hasta,$alias_op,$id_tipo,$id_sede);
	$ordenes_produccion->realizarConsulta();
	$resultadosBusqueda = $ordenes_produccion->ordenes_produccion;
	$num_resultados = count($resultadosBusqueda);
	
	if ($fecha_inicio != '') $fecha_inicio = $funciones->cFechaNormal($fecha_inicio);
	if ($fecha_entrega != '') $fecha_entrega = $funciones->cFechaNormal($fecha_entrega);
	if ($fecha_entrega_deseada != '') $fecha_entrega_deseada = $funciones->cFechaNormal($fecha_entrega_deseada);
	if ($fecha_desde != "") $fecha_desde = $funciones->cFechaNormal($fecha_desde);
	if ($fecha_hasta != "") $fecha_hasta = $funciones->cFechaNormal($fecha_hasta);
	
	// Volvemos a reasignar las variables de "unidades"
	$unidades = $_GET["unidades"];
	
	// Guardar las variables del formulario en variable de sesion
	$_SESSION["unidades_orden_produccion"] = $unidades;
	$_SESSION["cabina_orden_produccion"] = $cabina;
	$_SESSION["periferico_orden_produccion"] = $periferico;
	/* $_SESSION["software_orden_produccion"] = $software; */
	$_SESSION["fecha_inicio_orden_produccion"] = $fecha_inicio;
	$_SESSION["fecha_entrega_orden_produccion"] = $fecha_entrega;
	$_SESSION["fecha_entrega_deseada_orden_produccion"] = $fecha_entrega_deseada;
	$_SESSION["estado_orden_produccion"] = $estado;
	$_SESSION["ref_libres_orden_produccion"] = $ref_libres; 
	$_SESSION["fecha_desde_orden_produccion"] = $fecha_desde;
	$_SESSION["fecha_hasta_orden_produccion"] = $fecha_hasta;
	$_SESSION["alias_op_orden_produccion"] = $alias_op;
	$_SESSION["tipo_orden_produccion"] = $id_tipo;
	$_SESSION["id_sede_orden_produccion"] = $id_sede;
}

$titulo_pagina = "Órdenes de Producción";
$pagina = "ordenes_produccion";
include("../includes/header.php");
echo '<script type="text/javascript" src="../js/orden_produccion/ordenes_produccion.js"></script>';
?>

<div class="separador"></div> 
<?php include("../includes/menu_op.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar"><?php include("../includes/sidebar.php");?></div>
       	
    <h3> Listado de Órdenes de Producción </h3>
    <h4> Buscar Órden de Producción </h4>
    <form id="BuscadorOrdenProduccion" name="BuscadorOrdenProduccion" action="ordenes_produccion.php" method="get" class="Buscador">
    <table style="border:0;">
    <tr style="border:0;">
        <td>
            <div class="Label">Unidades</div>
            <input type="text" id="unidades" name="unidades" class="BuscadorInput" value="<?php echo $_SESSION["unidades_orden_produccion"];?>" onkeypress="return soloNumeros(event)"/>
        </td>
        <td>
			<div class="Label">Alias</div>
			<div id="capa_alias"> 
	            <select id="alias_op" name="alias_op" class="BuscadorInput">
	            	<?php 
	            		// Establecemos sede predeterminada para el Admin GLobal y para el Admin y usuarios de Gestion
	            		if(($esAdminGlobal || $esUsuarioGes) and $_GET["realizandoBusqueda"] != 1) $id_sede = 1;
						$listadoAlias->prepararAlias($id_sede);
						$listadoAlias->realizarConsultaAlias();
						$resultado_alias = $listadoAlias->alias_op;

						for($i=-1;$i<count($resultado_alias);$i++) {
							$orden_prod->cargaDatosProduccionId($resultado_alias[$i]["id_produccion"]);
							echo '<option value="'.$orden_prod->alias_op.'"';
								if ($orden_prod->alias_op == $_SESSION["alias_op_orden_produccion"])
									echo ' selected="selected"';
							echo '>'.$orden_prod->alias_op.'</option>';
						}
					?>
	            </select>            
            </div>
        </td>
        <td>
            <div class="Label">Estado</div>
			<select id="estado" name="estado" class="BuscadorInput">
            	<?php 
					$num_estados = 3;
					$estado = array ("BORRADOR","INICIADO","FINALIZADO");
					for($i=-1;$i<$num_estados;$i++) {
						echo '<option value="'.$estado[$i].'"';
							if ($estado[$i] == $_SESSION["estado_orden_produccion"])
								echo ' selected="selected"';
						echo '>'.$estado[$i].'</option>';
					}
				?>
            </select>             
        </td>
    </tr>
    <tr style="border:0;">
       	<td>
            <div class="Label">Cabinas</div>
            <select id="cabina" name="cabina" class="BuscadorInput">
            	<?php 
					$cabs->prepararConsulta();
					$cabs->realizarConsulta();
					$resultado_cabinas = $cabs->cabinas;

					for($i=-1;$i<count($resultado_cabinas);$i++) {
						$datoCab = $resultado_cabinas[$i];
						$cab->cargaDatosCabinaId($datoCab["id_componente"]);
						echo '<option value="'.$cab->id_componente.'"';
							if ($cab->id_componente == $_SESSION["cabina_orden_produccion"])
								echo ' selected="selected"';
						echo '>';
						if($i!=-1) echo $cab->cabina.'_v'.$cab->version.'</option>';
					}
				?>
            </select>
        </td>
        <td>
            <div class="Label">Periféricos</div>
            <select id="periferico" name="periferico" class="BuscadorInput" >
            	<?php 
    				$perifs->prepararConsulta();
					$perifs->realizarConsulta();
					$resultado_perifericos = $perifs->perifericos;

					for($i=-1;$i<count($resultado_perifericos);$i++) {
						$datoPerif = $resultado_perifericos[$i];
						$perif->cargaDatosPerifericoId($datoPerif["id_componente"]);
						echo '<option value="'.$perif->id_componente.'"';
							if ($perif->id_componente == $_SESSION["periferico_orden_produccion"])
								echo ' selected="selected"';
						echo '>';
						if($i!=-1) echo $perif->periferico.'_v'.$perif->version.'</option>';
					}
				?>
            </select>
        </td>
        <td>
			<div class="Label">Ref. Libres</div>
			<select id="ref_libres" name="ref_libres" class="BuscadorInput">
				<?php
				$listadoRefLibres->prepararReferenciasLibres();
				$listadoRefLibres->realizarConsultaReferenciasLibres();
				$resultado_ref_libres = $listadoRefLibres->ref_libres;

				for($i=-1;$i<count($resultado_ref_libres);$i++) {
					$ref_libre->cargaDatosReferenciaLibreId($resultado_ref_libres[$i]["id_referencia"]);
					echo '<option value="'.$ref_libre->id_referencia.'"';
					if ($ref_libre->id_referencia == $_SESSION["ref_libres_orden_produccion"])
						echo ' selected="selected"';
					echo '>'.$ref_libre->referencia.'</option>';
				}
				?>
			</select>
			<!--
        	<div class="Label">Software Sim.</div>
           	<select id="software" name="software" class="BuscadorInput">
            	<?php
					/*
					$softs->prepararConsulta();
					$softs->realizarConsulta();
					$resultado_softwares = $softs->softwares;

					for($i=-1;$i<count($resultado_softwares);$i++) {
						$datoSoft = $resultado_softwares[$i];
						$soft->cargaDatosSoftwareId($datoSoft["id_componente"]);
						echo '<option value="'.$soft->id_componente.'"';
							if ($soft->id_componente == $_SESSION["software_orden_produccion"])
								echo ' selected="selected"';
						echo '>'.$soft->software.'</option>';
					} */
				?>
            </select>  -->
        </td>
    </tr>
    <tr style="border:0;">
       	<td>
			<div class="Label">Fecha Inicio</div>
			<input type="text" name="fecha_inicio" id="datepicker_orden_produccion_inicio" class="fechaCal" value="<?php echo $_SESSION["fecha_inicio_orden_produccion"];?>"/>
        </td>
        <td>
			<div class="Label">Fecha Entrega</div>
			<input type="text" name="fecha_entrega" id="datepicker_orden_produccion_entrega" class="fechaCal" value="<?php echo $_SESSION["fecha_entrega_orden_produccion"];?>"/>
        </td>
        <td>
			<div class="Label">Fecha Ent. Deseada</div>
			<input type="text" name="fecha_entrega_deseada" id="datepicker_orden_produccion_deseada" class="fechaCal" value="<?php echo $_SESSION["fecha_entrega_deseada_orden_produccion"];?>"/>
        </td>
    </tr>
    <tr style="border:0;">
       	<td>
			<div class="Label">Tipo</div>
			<select id="tipo" name="tipo" class="BuscadorInput">
				<option value="1" <?php if($_SESSION["tipo_orden_produccion"] == 1) echo 'selected="selected"';?>>ORDEN PRODUCCIÓN</option>
				<option value="2" <?php if($_SESSION["tipo_orden_produccion"] == 2) echo 'selected="selected"';?>>MANTENIMIENTO</option>
			</select>
        </td>
        <td>
			<div class="Label">Fecha desde</div>
           	<input type="text" name="fecha_desde" id="datepicker_orden_produccion_desde" class="fechaCal" value="<?php echo $_SESSION["fecha_desde_orden_produccion"];?>"/>	
        </td>
        <td>
            <div class="Label">Fecha hasta</div>
        	<input type="text" name="fecha_hasta" id="datepicker_orden_produccion_hasta" class="fechaCal" value="<?php echo $_SESSION["fecha_hasta_orden_produccion"];?>"/>
        </td>
    </tr>
	<tr style="border:0;">
    	<td>
			<?php
			if($esAdminGlobal || $esUsuarioGes){
				$res_sedes = $sede->dameSedesFabrica(); ?>
				<div class="Label">Sede</div>
				<select id="sedes" name="sedes" class="BuscadorInput" onchange="cargaAlias(this.value);">
					<option value="0"></option>
					<?php
					for($i=0;$i<count($res_sedes);$i++){
						$id_sede_bus = $res_sedes[$i]["id_sede"];
						$nombre_sede = $res_sedes[$i]["sede"];

						echo '<option value='.$id_sede_bus;
						if($id_sede_bus == $_SESSION["id_sede_orden_produccion"]){
							echo ' selected="selected"';
						}
						echo '>'.$nombre_sede.'</option>';
					}
					?>
				</select>
				<?php
			}
			?>
		</td>
        <td>

        </td>
        <td></td>
    </tr>
    <tr style="border:0;">
      	<td colspan="3">
            <input type="hidden" id="realizandoBusqueda" name="realizandoBusqueda" value="1" />
           	<input type="submit" id="botonEnviar" name="botonEnviar" value="Buscar" />
        </td>
    </tr>
    </table>
    <br />    	
    </form>
        
    <div class="ContenedorBotonCrear">
        <?php
			if($_GET["OProduccion"] == "creado") {
				echo '<div class="mensaje">La orden de producción se ha creado correctamente</div>';
			}
			if($_GET["OProduccion"] == "modificado") {
				echo '<div class="mensaje">La orden de producción se ha modificado correctamente</div>';
			}
			if($_GET["OProduccion"] == "eliminado") {
				echo '<div class="mensaje">La orden de producción se ha eliminado correctamente</div>';
			}
			if($_GET["OProduccion"] == "iniciado_message") {
				echo '<div class="mensaje">La orden de producción se ha iniciado correctamente y se han generado las órdenes de compra</div>';
			}
			if($_GET["OProduccion"] == "cambio_estado_message") {
				echo '<div class="mensaje">Se ha actualizado correctamente el estado de la Orden de Producción</div>';
			}		
			if($_GET["fechas_mod"] == "YES"){
				echo '<div class="mensaje">Se ha actualizado correctamente las fechas de la Orden de Producción</div>';	
			}
			if($mostrar_tabla){
				if($num_resultados == NULL or $num_resultados == 0){
	               echo '<div class="mensaje">No se encontraron órdenes de producción</div>';
	               $mostrar_tabla = false;
	            }
	            else if ($num_resultados == 1){
	            	echo '<div class="mensaje">Se encontró 1 orden de producción</div>';
	            }
	            else{
	            	echo '<div class="mensaje">Se encontraron '.$num_resultados.' órdenes de producción</div>';
	            }	
        	}
		?>
    </div>
    <?php
		if($mostrar_tabla) {
			if ($id_tipo != 2){
				include("muestra_listado_op.php");
			}
			else {
				include("muestra_listado_mant.php");	
			}
		}
		if((isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) && $mostrar_tabla) { ?>
    		<div class="ContenedorBotonCrear"><input type="button" id="descargar_XLS_OP" name="descargar_XLS_OP" value="Descargar XLS" class="BotonEliminar" onclick="javascript:descargar_XLS_OP();"/></div>
    <?php
		}
	?>        
</div>    

<?php include ('../includes/footer.php');?>