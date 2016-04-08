<?php
// Este fichero muestra el listado de las imputaciones
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/imputaciones/imputacion.class.php");
include("../classes/imputaciones/listado_imputaciones.class.php");
permiso(33);

// Se obtienen los datos del formulario
if($_GET["imputacion"] == "creado") {
	$realizarBusqueda = 1;
}

if($_GET["m"] == "eliminar") {
	$imputacion = new Imputacion();
	$imputacion->cargarDatosImputacionId($_POST["id"]);
	$imputacion->eliminar();
	$realizarBusqueda = 1;
}
if(isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) {
	$mostrar_tabla = true;
	$orden_produccion = addslashes($_GET["orden_produccion"]);
	$tipo_trabajo = $_GET["tipo_trabajo"];
	$id_usuario = $_SESSION["AT_id_usuario"];
	$fecha_desde = $_GET["fecha_desde"];
	$fecha_hasta = $_GET["fecha_hasta"];
	
	$funciones = new Funciones();
	if ($fecha_desde != "") $fecha_desde = $funciones->cFechaMy($fecha_desde);
	if ($fecha_hasta != "") $fecha_hasta = $funciones->cFechaMy($fecha_hasta);
		
	// Se carga la clase para la base de datos y el listado de las Ordenes de Produccion
	$imputaciones = new listadoImputaciones();
		
	// Se pasan los datos del buscador a la clase del listado y se realiza la consulta a la base de datos
	$imputaciones->setValores($id_usuario,$orden_produccion,$tipo_trabajo,$fecha_desde,$fecha_hasta);
	$imputaciones->realizarConsulta();
	$resultadosBusqueda = $imputaciones->imputaciones;
	$num_resultados = count($resultadosBusqueda);
	
	if ($fecha_desde != "") $fecha_desde = $funciones->cFechaNormal($fecha_desde);
	if ($fecha_hasta != "") $fecha_hasta = $funciones->cFechaNormal($fecha_hasta);
	
	// Guardar las variables del formulario en variable de sesion
	$_SESSION["orden_produccion_imputacion"] = stripslashes(htmlspecialchars($orden_produccion));
	$_SESSION["tipo_trabajo_imputacion"] = $tipo_trabajo;
	$_SESSION["fecha_desde_imputacion"] = $fecha_desde;
	$_SESSION["fecha_hasta_imputacion"] = $fecha_hasta;
}

$titulo_pagina = "Imputación de Horas";
$pagina = "imputaciones";
include("../includes/header.php");
?>

<div class="separador"></div> 
<?php include("../includes/menu_imputaciones.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include("../includes/sidebar.php"); ?>
    </div>
       	
    <h3>Imputación de Horas</h3>
    <h4>Opciones de filtrado</h4>
    
    <form id="filtrar_imputaciones" name="filtrar_imputaciones" action="imputacion.php" method="get" class="Buscador">
    <table style="border:0;">
    	<tr style="border:0;">
        	<td>
            <div class="Label">Orden de Producción</div>
            <input type="text" id="orden_produccion" name="orden_produccion" class="BuscadorInput" value="<?php echo $_SESSION["orden_produccion_imputacion"];?>"/>
            </td>
            <td>
            <div class="Label">Tipo de trabajo</div>
           	<select id="tipo_trabajo" name="tipo_trabajo" class="BuscadorInput">
            	<option value="0">Todos</option>
                <option value="1"<?php if($_SESSION["tipo_trabajo_imputacion"] == 1) { echo ' selected="selected"'; }?>>Mecánico</option>
                <option value="2"<?php if($_SESSION["tipo_trabajo_imputacion"] == 2) { echo ' selected="selected"'; }?>>Eléctrico</option>
                <option value="3"<?php if($_SESSION["tipo_trabajo_imputacion"] == 3) { echo ' selected="selected"'; }?>>Electrónico</option>
                <option value="4"<?php if($_SESSION["tipo_trabajo_imputacion"] == 4) { echo ' selected="selected"'; }?>>Gestión y supervisión</option>
            </select>
            </td>
            <td>
            	
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">Fecha desde</div>
           		<input type="text" name="fecha_desde" id="datepicker_imputaciones_desde" class="fechaCal" value="<?php echo $_SESSION["fecha_desde_imputacion"];?>"/>
            </td>
            <td>
            	<div class="Label">Fecha hasta</div>
           		<input type="text" name="fecha_hasta" id="datepicker_imputaciones_hasta" class="fechaCal" value="<?php echo $_SESSION["fecha_hasta_imputacion"];?>"/>
            </td>
            <td>
            
            </td>
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
    <div style="width: 135px; margin: 0 auto;">
    <br />
    <input type="button" id="nueva" name="nueva" value="Nueva imputación" onclick="javascript:location='nueva_imputacion.php'" /> 
    <br />
    </div>
    <div class="ContenedorBotonCrear" style="margin:0 auto;">
        <?php
			if($_GET["imputacion"] == "creado") {
				echo '<div class="mensaje">La imputación de horas se ha guardado correctamente</div>';
			}
			if($mostrar_tabla){
				if($num_resultados == NULL or $num_resultados == 0){
	               echo '<br/><div class="mensaje">No se encontraron imputaciones</div>';
	               $mostrar_tabla = false;
	            }
	            else if ($num_resultados == 1){
	            	echo '<br/><div class="mensaje">Se encontró 1 imputación</div>';
	            }
	            else{
	            	echo '<br/><div class="mensaje">Se encontraron '.$num_resultados.' imputaciones</div>';
	            }	
        	}
		?>
    </div>
    <?php
		if($mostrar_tabla) {
	?>
			<div class="CapaTabla">
				<table>
				<tr>
					<th>ORDEN PRODUCCIÓN</th>
					<th>TIPO TRABAJO</th>
					<th style="text-align:center">HORAS</th>
					<th style="text-align:center">FECHA</th>
					<th>DESCRIPCION</th>
                    <th style="text-align:center"></th>
				</tr> 
                <?php
				for($i=0;$i<count($resultadosBusqueda);$i++) {
					$imputacion = new Imputacion();
					$imputacion->cargarDatosImputacionId($resultadosBusqueda[$i]["id"]);
				?>
					<tr>
						<td>
							<?php echo $imputacion->codigo;?>
						</td>
						<td>
							<?php
                            switch($imputacion->tipo_trabajo) {
								case 1:
									echo 'Mecánico';
								break;
								case 2:
									echo 'Eléctrico';
								break;
								case 3:
									echo 'Electrónico';
								break;
								case 4:
									echo 'Gestión y supervisión';
								break;
							}
							?>
						</td>
						<td style="text-align:center">
							<?php echo $imputacion->horas; ?>
						</td>
						<td style="text-align:center">
							<?php echo $imputacion->fecha; ?>
						</td>
						<td>
							<?php echo $imputacion->descripcion; ?>
						</td>
                    <?php
					if($imputacion->fecha_eliminacion >= date("Y-m-d h:i:s")) {
						?>
                        <form name="eliminar" method="post" action="imputacion.php?m=eliminar">
                        <td>
                        	<input type="hidden" id="id" name="id" value="<?php echo $imputacion->id; ?>" />
                            <input type="submit" id="se" name="se" class="BotonEliminar" value="ELIMINAR" />
                        </td>
                        </form>
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
		}
		?>
</div>    

<?php include ('../includes/footer.php');  ?>