<?php
// Este fichero muestra el listado de las interfaces
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/interface.class.php");
include("../classes/basicos/listado_interfaces.class.php");
permiso(1);

// Se obtienen los datos del formulario
if($_GET["interface"] == "creado" or $_GET["interface"] == "modificado" or $_GET["interface"] == "eliminado" or $_GET["interface"] == "duplicado" or $_GET["interface"] == "actualizado") {
	$realizarBusqueda = 1;
}
if(isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) {
	$mostrar_tabla = true;
	$interfaz = addslashes($_GET["interfaz"]);
	$referencia = addslashes($_GET["referencia"]);
	$version = addslashes($_GET["version"]);
	$descripcion = addslashes($_GET["descripcion"]);
	$estado = $_GET["estado"];
	$prototipo = $_GET["prototipo"];
	$fecha_desde = $_GET["fecha_desde"];
	$fecha_hasta = $_GET["fecha_hasta"];

	if (!is_numeric($version)) $version = NULL;

	$funciones = new Funciones();
	// Convierte la fecha a formato MySQL
	if ($fecha_desde != "") $fecha_desde = $funciones->cFechaMy($fecha_desde);
	if ($fecha_hasta != "") $fecha_hasta = $funciones->cFechaMy($fecha_hasta);

	// Guardamos en una variable el campo de referencia para mostrarlo despues de la busqueda
	$referencia_ant = $referencia;

	// Quitar guiones y espacios del campo de referencia
	for($i=0;$i<strlen($referencia);$i++){
		if (($referencia[$i] == '-') or ($referencia[$i] == ' ')) $referencia[$i] = '%';
	}

	$interfaces = new listadoInterfaces();
	// Se pasan los datos del buscador a la clase del listado y se realiza la consulta a la base de datos
	$interfaces->setValores($interfaz,$referencia,$version,$descripcion,$estado,$prototipo,$fecha_desde,$fecha_hasta);
	$interfaces->realizarConsulta();
	$resultadosBusqueda = $interfaces->interfaces;
	$num_resultados = count($resultadosBusqueda);

	// Convierte la fecha a formato HTML
	if ($fecha_desde != "") $fecha_desde = $funciones->cFechaNormal($fecha_desde);
	if ($fecha_hasta != "") $fecha_hasta = $funciones->cFechaNormal($fecha_hasta);

	// Mostramos los valores iniciales de busqueda
	$referencia = $referencia_ant;
	// Volvemos a reasignar la variable "version" en el caso de que su valor fuese NULL
	$version = $_GET["version"];

	// Guardar las variables del formulario en variable de sesion
	$_SESSION["interfaz_interfaces"] = stripslashes(htmlspecialchars($interfaz));
	$_SESSION["referencia_interfaces"] = stripslashes(htmlspecialchars($referencia));
	$_SESSION["version_interfaces"] = stripslashes(htmlspecialchars($version));
	$_SESSION["descripcion_interfaces"] = stripslashes(htmlspecialchars($descripcion));
	$_SESSION["estado_interfaces"] = $estado;
	$_SESSION["prototipo_interfaces"] = $prototipo;
	$_SESSION["fecha_desde_interfaces"] = $fecha_desde;
	$_SESSION["fecha_hasta_interfaces"] = $fecha_hasta;
}

$titulo_pagina = "Básicos > Interfaces";
$pagina = "interfaces";
include ("../includes/header.php");
?>

<div class="separador"></div>
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>

    <h3>Interfaces</h3>
    <h4>Buscar interfaz</h4>

    <form id="BuscadorInterface" name="buscadorInterface" action="interfaces.php" method="get" class="Buscador">
    <table style="border:0;">
    	<tr style="border:0;">
        	<td>
            	<div class="Label">Nombre</div>
            	<input type="text" id="" name="interfaz" class="BuscadorInput" value="<?php echo $_SESSION["interfaz_interfaces"];?>"/>
            </td>
            <td>
            	<div class="Label">Referencia</div>
            	<input type="text" id="" name="referencia" class="BuscadorInput" value="<?php echo $_SESSION["referencia_interfaces"];?>"/>
            </td>
            <td>
            	<div class="Label">Versi&oacute;n</div>
            	<input type="text" id="" name="version" class="BuscadorInput" value="<?php echo $_SESSION["version_interfaces"];?>"/>
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">Descripcion</div>
           		<input type="text" id="" name="descripcion" class="BuscadorInput" value="<?php echo $_SESSION["descripcion_interfaces"];?>"/>
            </td>
            <td>
            	<div class="Label">Fecha desde</div>
           		<input type="text" name="fecha_desde" id="datepicker_interfaz_desde" class="fechaCal" value="<?php echo $_SESSION["fecha_desde_interfaces"];?>"/>
            </td>
            <td>
            	<div class="Label">Fecha hasta</div>
           		<input type="text" name="fecha_hasta" id="datepicker_interfaz_hasta" class="fechaCal" value="<?php echo $_SESSION["fecha_hasta_interfaces"];?>"/>
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">Estado</div>
           		<select id="estado" name="estado" class="BuscadorInput"/>
                	<option value=""></option>
                	<option value="BORRADOR"
						<?php if($_SESSION["estado_interfaces"] == "BORRADOR") { echo ' selected="selected"'; } ?>>BORRADOR</option>
                	<option value="PRODUCCIÓN"
						<?php if($_SESSION["estado_interfaces"] == "PRODUCCIÓN") { echo ' selected="selected"'; } ?>>PRODUCCIÓN</option>
                	<option value="LEGACY"
						<?php if($_SESSION["estado_interfaces"] == "LEGACY") { echo ' selected="selected"'; } ?>>LEGACY</option>
                </select>
            </td>
            <td>
            	<div class="Label">Prototipo</div>
				<select id="prototipo" name="prototipo" class="BuscadorInput"/>
                	<option value=""></option>
                    <option value="0"<?php if ($_SESSION["prototipo_interfaces"] == "0") { echo ' selected="selected"';}?>>NO</option>
                    <option value="1"<?php if ($_SESSION["prototipo_interfaces"] == "1") { echo ' selected="selected"';}?>>SI</option>
                </select>
            </td>
            <td>

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
    </form>

    <div class="ContenedorBotonCrear">
		<?php
			if($_GET["interface"] == "creado") {
				echo '<div class="mensaje">La interface se ha creado correctamente</div>';
			}
			if($_GET["interface"] == "modificado") {
				echo '<div class="mensaje">La interface se ha modificado correctamente</div>';
			}
			if($_GET["interface"] == "eliminado") {
				echo '<div class="mensaje">La interface se ha eliminado correctamente</div>';
			}
			if($_GET["interface"] == "duplicado") {
				echo '<div class="mensaje">La interface original se ha modificado y duplicado correctamente</div>';
			}
			if($_GET["interface"] == "actualizado") {
				echo '<div class="mensaje">La interface se ha actualizado correctamente</div>';
			}
			if($mostrar_tabla){
				if($num_resultados == NULL or $num_resultados == 0){
	               echo '<div class="mensaje">No se encontraron interfaces</div>';
	               $mostrar_tabla = false;
	            }
	            else if ($num_resultados == 1){
	            	echo '<div class="mensaje">Se encontró 1 interfaz</div>';
	            }
	            else{
	            	echo '<div class="mensaje">Se encontraron '.$num_resultados.' interfaces</div>';
	            }	
        	}
		?>
    </div>

    <?php
		if ($mostrar_tabla)
		{
		?>
   			<div class="CapaTabla">
    		<table>
        	<tr>
        		<th>NOMBRE</th>
            	<th>REFERENCIA</th>
            	<th style="text-align:center">VERSION</th>
                <th style="text-align:center">REFERENCIAS</th>
            	<th>DESCRIPCION</th>
                <th>ESTADO</th>
                <th style="text-align:center">PROTOTIPO</th>
                <?php 
                	if (permisoMenu(4)){ ?>
                		<th style="text-align:center">ELIMINAR</th>
                <?php 
                	}
                ?>
         	</tr>
        	<?php
				// Se cargan los datos de las interfaces según su identificador
				for($i=0;$i<count($resultadosBusqueda);$i++) {
					$interf = new Interfaz();
					$datoInterface = $resultadosBusqueda[$i];
					$interf->cargaDatosInterfazId($datoInterface["id_componente"]);
			?>
					<tr>
						<td>
							<a href="mod_interfaz.php?id=<?php echo $interf->id_componente; ?>">
								<?php echo $interf->interfaz; ?>
							</a>
						</td>
						<td><?php echo $interf->referencia; ?></td>
						<td style="text-align:center"><?php echo number_format($interf->version, 2, '.', '');?></td>
						<td style="text-align:center">
                        	<a href="javascript:abrir('muestra_referencias.php?nombre=<?php echo $interf->interfaz;?>&tipo=interfaz&id=<?php echo $interf->id_componente;?>')">
                            	REFERENCIAS
                            </a>
                            -
							<a href="../basicos/informe_referencias.php?tipo=interfaz&id=<?php echo $interf->id_componente; ?>">XLS</a>
                        </td>
                        <td><?php echo $interf->descripcion; ?></td>
                        <td><?php echo $interf->estado; ?></td>
                        <td style="text-align:center">
                        	<?php
								if ($interf->prototipo == 0){
									echo "NO";
								}
								else echo "SI";
							?>
						</td>
						<?php 
							if (permisoMenu(4)){ ?>
		                        <td style="text-align:center">
        		                	<input type="button" id="menos" name="menos" value="ELIMINAR" class="BotonEliminar" onclick="javascript: if (confirm('¿Desea eliminar la interface?')) { window.location.href='elim_interface.php?id=<?php echo $interf->id_componente;?>' } else { void('') };" />
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
		}
	?>
</div>
<?php include ("../includes/footer.php"); ?>
