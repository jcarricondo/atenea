<?php
// Este fichero muestra el listado de los kits
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/kit.class.php");
include("../classes/basicos/listado_kits.class.php");
include("../classes/kint/Kint.class.php");
permiso(1);

// Establecemos los parametros de la paginacion
// Número de registros a mostrar por página
$pg_registros = 50; 
$pg_pagina = $_GET["pg"];
if(empty($pg_pagina)) {
    $pg_inicio = 0;
    $pg_pagina = 1;
} 
else {
    $pg_inicio = ($pg_pagina - 1) * $pg_registros;
}
$paginacion = " limit ".$pg_inicio.', '.$pg_registros; 

// Se obtienen los datos del formulario
if($_GET["operacion_kit"] == "creado" or $_GET["operacion_kit"] == "modificado" or $_GET["operacion_kit"] == "eliminado" or $_GET["operacion_kit"] == "duplicado" or $_GET["operacion_kit"] == "actualizado") {
	$realizarBusqueda = 1;
}
if(isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) {
	$mostrar_tabla = true;
	$kit = addslashes($_GET["kit"]);
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

	$kits = new listadoKits();
	// Se pasan los datos del buscador a la clase del listado y se realiza la consulta a la base de datos
	$kits->setValores($kit,$referencia,$version,$descripcion,$estado,$prototipo,$fecha_desde,$fecha_hasta,'');
	$kits->realizarConsulta();
	$resultadosBusqueda = $kits->kits;
	$num_resultados = count($resultadosBusqueda); 

	// Se realiza la consulta con paginacion 
	$pg_totalPaginas = ceil(count($resultadosBusqueda) / $pg_registros);
	$kits->setValores($kit,$referencia,$version,$descripcion,$estado,$prototipo,$fecha_desde,$fecha_hasta,$paginacion);
	$kits->realizarConsulta();
	$resultadosBusqueda = $kits->kits; 

	// Convierte la fecha a formato HTML
	if ($fecha_desde != "") $fecha_desde = $funciones->cFechaNormal($fecha_desde);
	if ($fecha_hasta != "") $fecha_hasta = $funciones->cFechaNormal($fecha_hasta);

	// Mostramos los valores iniciales de busqueda
	$referencia = $referencia_ant;
	// Volvemos a reasignar la variable "version" en el caso de que su valor fuese NULL
	$version = $_GET["version"];

	// Guardar las variables del formulario en variable de sesion
	$_SESSION["kit_kits"] = stripslashes(htmlspecialchars($kit));
	$_SESSION["referencia_kits"] = stripslashes(htmlspecialchars($referencia));
	$_SESSION["version_kits"] = stripslashes(htmlspecialchars($version));
	$_SESSION["descripcion_kits"] = stripslashes(htmlspecialchars($descripcion));
	$_SESSION["estado_kits"] = $estado;
	$_SESSION["prototipo_kits"] = $prototipo;
	$_SESSION["fecha_desde_kits"] = $fecha_desde;
	$_SESSION["fecha_hasta_kits"] = $fecha_hasta;
}

$titulo_pagina = "Básicos > Kits";
$pagina = "kits";
include ("../includes/header.php");
?>

<div class="separador"></div>
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>

    <h3>Kits</h3>
    <h4>Buscar kits</h4>

    <form id="BuscadorKits" name="buscadorKits" action="kits.php" method="get" class="Buscador">
    <table style="border:0;">
    	<tr style="border:0;">
        	<td>
            	<div class="Label">Nombre</div>
            	<input type="text" id="" name="kit" class="BuscadorInput" value="<?php echo $_SESSION["kit_kits"];?>"/>
            </td>
            <td>
            	<div class="Label">Referencia</div>
            	<input type="text" id="" name="referencia" class="BuscadorInput" value="<?php echo $_SESSION["referencia_kits"];?>"/>
            </td>
            <td>
            	<div class="Label">Versi&oacute;n</div>
            	<input type="text" id="" name="version" class="BuscadorInput" value="<?php echo $_SESSION["version_kits"];?>"/>
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">Descripcion</div>
           		<input type="text" id="" name="descripcion" class="BuscadorInput" value="<?php echo $_SESSION["descripcion_kits"];?>"/>
            </td>
            <td>
            	<div class="Label">Fecha desde</div>
           		<input type="text" name="fecha_desde" id="datepicker_kit_desde" class="fechaCal" value="<?php echo $_SESSION["fecha_desde_kits"];?>"/>
            </td>
            <td>
            	<div class="Label">Fecha hasta</div>
           		<input type="text" name="fecha_hasta" id="datepicker_kit_hasta" class="fechaCal" value="<?php echo $_SESSION["fecha_hasta_kits"];?>"/>
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">Estado</div>
           		<select id="estado" name="estado" class="BuscadorInput"/>
                	<option value=""></option>
                	<option value="BORRADOR"
						<?php if($_SESSION["estado_kits"] == "BORRADOR") { echo ' selected="selected"'; } ?>>BORRADOR</option>
                	<option value="PRODUCCIÓN"
						<?php if($_SESSION["estado_kits"] == "PRODUCCIÓN") { echo ' selected="selected"'; } ?>>PRODUCCIÓN</option>
                	<option value="LEGACY"
						<?php if($_SESSION["estado_kits"] == "LEGACY") { echo ' selected="selected"'; } ?>>LEGACY</option>
                </select>
            </td>
            <td>
            	<div class="Label">Prototipo</div>
				<select id="prototipo" name="prototipo" class="BuscadorInput"/>
                	<option value=""></option>
                    <option value="0"<?php if ($_SESSION["prototipo_kits"] == "0") { echo ' selected="selected"';}?>>NO</option>
                    <option value="1"<?php if ($_SESSION["prototipo_kits"] == "1") { echo ' selected="selected"';}?>>SI</option>
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
			if($_GET["operacion_kit"] == "creado") {
				echo '<div class="mensaje">El kit se ha creado correctamente</div>';
			}
			if($_GET["operacion_kit"] == "modificado") {
				echo '<div class="mensaje">El kit se ha modificado correctamente</div>';
			}
			if($_GET["operacion_kit"] == "eliminado") {
				echo '<div class="mensaje">El kit se ha eliminado correctamente</div>';
			}
			if($_GET["operacion_kit"] == "duplicado") {
				echo '<div class="mensaje">El kit original se ha modificado y duplicado correctamente</div>';
			}
			if($_GET["operacion_kit"] == "actualizado") {
				echo '<div class="mensaje">El kit se ha actualizado correctamente</div>';
			}
			if($mostrar_tabla){
				if($num_resultados == NULL or $num_resultados == 0){
	               echo '<div class="mensaje">No se encontraron kits</div>';
	               $mostrar_tabla = false;
	            }
	            else if ($num_resultados == 1){
	            	echo '<div class="mensaje">Se encontró 1 kit</div>';
	            }
	            else{
	            	echo '<div class="mensaje">Se encontraron '.$num_resultados.' kits</div>';
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
                <th style="text-align:center">REFERENCIAS</th>
            	<th>DESCRIPCION</th>
                <th>ESTADO</th>
                <th style="text-align:center">PROTOTIPO</th>
                <?php 
                	if(permisoMenu(4)){
                ?>
                		<th style="text-align:center">ELIMINAR</th>
                <?php 
                	}
                ?>
         	</tr>
        	<?php
				// Se cargan los datos de los kits según su identificador
				for($i=0;$i<count($resultadosBusqueda);$i++) {
					$kt = new Kit();
					$datoKit = $resultadosBusqueda[$i];
					$kt->cargaDatosKitId($datoKit["id_componente"]);
			?>
					<tr>
						<td>
							<a href="mod_kit.php?id=<?php echo $kt->id_componente; ?>"><?php echo $kt->kit; ?></a>
						</td>
						<td><?php echo $kt->referencia; ?></td>
						<td style="text-align:center"><?php echo number_format($kt->version, 2, '.', ' '); ?></td>
						<td style="text-align:center">
                        	<a href="javascript:abrir('muestra_referencias.php?nombre=<?php echo $kt->kit;?>&tipo=kit&id=<?php echo $kt->id_componente;?>')">
                            	REFERENCIAS
                            </a>
                            -
							<a href="../basicos/informe_referencias.php?tipo=kit&id=<?php echo $kt->id_componente; ?>">XLS</a>
                        </td>
                        <td><?php echo $kt->descripcion; ?></td>
                        <td><?php echo $kt->estado; ?></td>
                        <td style="text-align:center">
                        	<?php
								if ($kt->prototipo == 0){
									echo "NO";
								}
								else echo "SI";
							?>
						</td>
						<?php 
							if(permisoMenu(4)){
						?>
		                        <td style="text-align:center">
		                        	<input type="button" id="menos" name="menos" value="ELIMINAR" class="BotonEliminar" onclick="javascript: if (confirm('¿Desea eliminar el kit?')) { window.location.href='elim_kit.php?id=<?php echo $kt->id_componente;?>' } else { void('') };" />
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
		// PAGINACIÓN
        if((isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) and $resultadosBusqueda != NULL) { ?>
        		<div style="font: bold 11px Verdana,Arial; margin: 0 auto; padding: 10px 0; width: 350px; text-align: center;"> 
	            <?php    
	                if(($pg_pagina - 1) > 0) { ?>
	                    <a href="kits.php?pg=1&realizandoBusqueda=1&kit=<?php echo $_SESSION["kit_kits"];?>&referencia=<?php echo $_SESSION["referencia_kits"];?>&version=<?php echo $_SESSION["version_kits"];?>&descripcion=<?php echo $_SESSION["descripcion_kits"];?>&estado=<?php echo $_SESSION["estado_kits"];?>&prototipo=<?php echo $_SESSION["prototipo_kits"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_kits"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_kits"];?>">Primera&nbsp&nbsp&nbsp</a>
	                    <a href="kits.php?pg=<?php echo $pg_pagina - 1;?>&realizandoBusqueda=1&kit=<?php echo $_SESSION["kit_kits"];?>&referencia=<?php echo $_SESSION["referencia_kits"];?>&version=<?php echo $_SESSION["version_kits"];?>&descripcion=<?php echo $_SESSION["descripcion_kits"];?>&estado=<?php echo $_SESSION["estado_kits"];?>&prototipo=<?php echo $_SESSION["prototipo_kits"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_kits"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_kits"];?>"> Anterior</a>
	            <?php  
	                }  
	                else {
	                    echo 'Primera&nbsp;&nbsp;&nbsp;Anterior';
	                }
	        
	                echo ' &nbsp;&nbsp;&nbsp;['.$pg_pagina.' / '.$pg_totalPaginas.']&nbsp;&nbsp;&nbsp;';
	                if($pg_pagina < $pg_totalPaginas) { ?>
	                    <a href="kits.php?pg=<?php echo $pg_pagina + 1;?>&realizandoBusqueda=1&kit=<?php echo $_SESSION["kit_kits"];?>&referencia=<?php echo $_SESSION["referencia_kits"];?>&version=<?php echo $_SESSION["version_kits"];?>&descripcion=<?php echo $_SESSION["descripcion_kits"];?>&estado=<?php echo $_SESSION["estado_kits"];?>&prototipo=<?php echo $_SESSION["prototipo_kits"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_kits"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_kits"];?>">Siguiente&nbsp&nbsp&nbsp</a>
	                    <a href="kits.php?pg=<?php echo $pg_totalPaginas; ?>&realizandoBusqueda=1&kit=<?php echo $_SESSION["kit_kits"];?>&referencia=<?php echo $_SESSION["referencia_kits"];?>&version=<?php echo $_SESSION["version_kits"];?>&descripcion=<?php echo $_SESSION["descripcion_kits"];?>&estado=<?php echo $_SESSION["estado_kits"];?>&prototipo=<?php echo $_SESSION["prototipo_kits"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_kits"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_kits"];?>">Última</a>
	            <?php        
    	            } 
        	        else {
            	        echo 'Siguiente&nbsp;&nbsp;&nbsp;Última'; 
            	    }
		    	?>
        		</div>
        		<br/>
   		<?php
        	}
    	}
	?>		
</div>
<?php include ("../includes/footer.php"); ?>
	