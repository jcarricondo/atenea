<?php 
// Este fichero muestra el listado de los softwares
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/software.class.php");
include("../classes/basicos/listado_softwares.class.php");
permiso(1);

// Se obtienen los datos del formulario
if($_GET["soft"] == "creado" or $_GET["soft"] == "modificado" or $_GET["soft"] == "eliminado") {
	$realizarBusqueda = 1;
}
if(isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) {
	$mostrar_tabla = true;
	$software = addslashes($_GET["software"]);
	$referencia = addslashes($_GET["referencia"]);
	$version = addslashes($_GET["version"]);
	$descripcion = addslashes($_GET["descripcion"]);
	$fecha_desde = $_GET["fecha_desde"];
	$fecha_hasta = $_GET["fecha_hasta"];
	
	$funciones = new Funciones();
	// Convierte la fecha a formato MySql
	if ($fecha_desde != "") $fecha_desde = $funciones->cFechaMy($fecha_desde);
	if ($fecha_hasta != "") $fecha_hasta = $funciones->cFechaMy($fecha_hasta);
	
	// Guardamos en una variable el campo de referencia para mostrarlo despues de la busqueda
	$referencia_ant = $referencia;
	
	// Quitar guiones y espacios del campo de referencia
	for($i=0;$i<strlen($referencia);$i++){
		if (($referencia[$i] == '-') or ($referencia[$i] == ' ')) $referencia[$i] = '%'; 
	}
		
	$softwares = new listadoSoftwares();
	// Se pasan los datos del buscador a la clase del listado y se realiza la consulta a la base de datos
	$softwares->setValores($software,$referencia,$version,$descripcion,$fecha_desde,$fecha_hasta);
	$softwares->realizarConsulta();
	$resultadosBusqueda = $softwares->softwares;
	$num_resultados = count($resultadosBusqueda);
	
	// Convierte la fecha a formato HTML
	if($fecha_desde != "") $fecha_desde = $funciones->cFechaNormal($fecha_desde);
	if($fecha_hasta != "") $fecha_hasta = $funciones->cFechaNormal($fecha_hasta);
	
	// Mostramos los valores iniciales de busqueda
	$referencia = $referencia_ant;
	
	// Guardar las variables del formulario en variable de sesion
	$_SESSION["software_software"] = stripslashes(htmlspecialchars($software));
	$_SESSION["referencia_software"] = stripslashes(htmlspecialchars($referencia));
	$_SESSION["version_software"] = stripslashes(htmlspecialchars($version));
	$_SESSION["descripcion_software"] = stripslashes(htmlspecialchars($descripcion));
	$_SESSION["fecha_desde_software"] = $fecha_desde;
	$_SESSION["fecha_hasta_software"] = $fecha_hasta;
}

$titulo_pagina = "Básicos > Software Simulación";
$pagina = "softwares";
include ("../includes/header.php");
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>
   
    <h3>Software simulaci&oacute;n</h3>
    <h4>Software simulaci&oacute;n</h4>
    
    <form id="BuscadorSoftware" name="buscadorSoftware" action="software_simulacion.php" method="get" class="Buscador">
    <table style="border:0;">
    	<tr style="border:0;">
        	<td>
            	<div class="Label">Nombre</div>
           		<input type="text" id="" name="software" class="BuscadorInput" value="<?php echo $_SESSION["software_software"];?>"/>
            </td>
            <td>
            	<div class="Label">Referencia</div>
          		<input type="text" id="" name="referencia" class="BuscadorInput" value="<?php echo $_SESSION["referencia_software"];?>"/>
            </td>
            <td>
            	<div class="Label">Versi&oacute;n</div>
           		<input type="text" id="" name="version" class="BuscadorInput" value="<?php echo $_SESSION["version_software"];?>"/>
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">Descripcion</div>
           		<input type="text" id="" name="descripcion" class="BuscadorInput" value="<?php echo $_SESSION["descripcion_software"];?>"/>
            </td>
			<td>
            	<div class="Label">Fecha desde</div>
           		<input type="text" name="fecha_desde" id="datepicker_software_desde" class="fechaCal" value="<?php echo $_SESSION["fecha_desde_software"];?>"/>
            </td>
            <td>
            	<div class="Label">Fecha hasta</div>
           		<input type="text" name="fecha_hasta" id="datepicker_software_hasta" class="fechaCal" value="<?php echo $_SESSION["fecha_hasta_software"];?>"/>
            </td>
        </tr>
        <tr style="border:0;">
        	<td colspan="3">
            	<input type="hidden" id="realizandoBusqueda" name="realizandoBusqueda" value="1"/>
            	<input type="submit" id="" name="" class="" value="Buscar" />
            </td>
        </tr>
    </table>
    	<br />
	</form>

	<div class="ContenedorBotonCrear">
		<?php
			if($_GET["soft"] == "creado") {
				echo '<div class="mensaje">El software se ha creado correctamente</div>';
			}
			if($_GET["soft"] == "modificado") {
				echo '<div class="mensaje">El software se ha modificado correctamente</div>';
			}
			if($_GET["soft"] == "eliminado") {
				echo '<div class="mensaje">El software se ha eliminado correctamente</div>';
			}
			if($mostrar_tabla){
				if($num_resultados == NULL or $num_resultados == 0){
	               echo '<div class="mensaje">No se encontraron softwares</div>';
	               $mostrar_tabla = false;
	            }
	            else if ($num_resultados == 1){
	            	echo '<div class="mensaje">Se encontró 1 software</div>';
	            }
	            else{
	            	echo '<div class="mensaje">Se encontraron '.$num_resultados.' softwares</div>';
	            }	
        	}
		?>
    </div>
    
    <?php 
		if ($mostrar_tabla)	{
		?>
   			<div class="CapaTabla">
    		<table>
        	<tr>
        		<th>NOMBRE</th>
            	<th>REFERENCIA</th>
            	<th style="text-align:center">VERSION</th>
            	<th>DESCRIPCION</th>
                <?php 
                	if(permisoMenu(4)){ ?>
                		<th style="text-align:center">ELIMINAR</th>
               	<?php 
               		}
               	?>
         	</tr>
        	<?php
				// Se cargan los datos de los softwares según su identificador
				for($i=0;$i<count($resultadosBusqueda);$i++) {
					$soft = new Software();
					$datoSoftware = $resultadosBusqueda[$i];
					$soft->cargaDatosSoftwareId($datoSoftware["id_componente"]);
					?>
					<tr>
						<td>
							<a href="mod_software_simulacion.php?id=<?php echo $soft->id_componente; ?>"><?php echo $soft->software;?></a>    
						</td>
						<td><?php echo $soft->referencia;?></td>
						<td style="text-align:center"><?php echo $soft->version;?></td>
						<td><?php echo $soft->descripcion;?></td>
                        <?php 
                			if(permisoMenu(4)){
                		?>
		                        <td style="text-align:center">
		                            <input type="button" id="eliminar" name="eliminar" value="ELIMINAR" class="BotonEliminar" onclick="javascript: if (confirm('¿Desea eliminar el software?')) { window.location.href='elim_software_simulacion.php?id=<?php echo $soft->id_componente;?>' } else { void('') };" />
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
<?php include ("../includes/footer.php");?>