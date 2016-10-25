<?php
// Este fichero muestra el listado de los perifericos
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/periferico.class.php");
include("../classes/basicos/listado_perifericos.class.php");
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
if($_GET["per"] == "creado" or $_GET["per"] == "modificado" or $_GET["per"] == "eliminado" or $_GET["per"] == "duplicado" or $_GET["per"] == "actualizado") {
	$realizarBusqueda = 1;
}
if(isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) {
	$mostrar_tabla = true;
	$periferico = addslashes($_GET["periferico"]);
	$referencia = addslashes($_GET["referencia"]);
	$version = addslashes($_GET["version"]);
	$descripcion = addslashes($_GET["descripcion"]);
	$estado = $_GET["estado"];
	$prototipo = $_GET["prototipo"];
	$fecha_desde = $_GET["fecha_desde"];
	$fecha_hasta = $_GET["fecha_hasta"];

	if (!is_numeric($version)) $version = NULL;

	$funciones = new Funciones();
	// Este fichero convierte la fecha a formato MySql
	if ($fecha_desde != "") $fecha_desde = $funciones->cFechaMy($fecha_desde);
	if ($fecha_hasta != "") $fecha_hasta = $funciones->cFechaMy($fecha_hasta);

	// Guardamos en una variable el campo de referencia para mostrarlo despues de la busqueda
	$referencia_ant = $referencia;

	// Quitar guiones y espacios del campo de referencia
	for($i=0;$i<strlen($referencia);$i++){
		if (($referencia[$i] == '-') or ($referencia[$i] == ' ')) $referencia[$i] = '%';
	}

	$perifericos = new listadoPerifericos();
	// Se pasan los datos del buscador a la clase del listado y se realiza la consulta a la base de datos
	$perifericos->setValores($periferico,$referencia,$version,$descripcion,$estado,$prototipo,$fecha_desde,$fecha_hasta,'');
	$perifericos->realizarConsulta();
	$resultadosBusqueda = $perifericos->perifericos;
    $num_resultados = count($resultadosBusqueda); 

    // Se realiza la consulta con paginacion 
    $pg_totalPaginas = ceil(count($resultadosBusqueda) / $pg_registros);
    $perifericos->setValores($periferico,$referencia,$version,$descripcion,$estado,$prototipo,$fecha_desde,$fecha_hasta,$paginacion);
    $perifericos->realizarConsulta();
    $resultadosBusqueda = $perifericos->perifericos;

	// Este fichero convierte la fecha a formato HTML
	if($fecha_desde != "") $fecha_desde = $funciones->cFechaNormal($fecha_desde);
	if($fecha_hasta != "") $fecha_hasta = $funciones->cFechaNormal($fecha_hasta);

	// Mostramos los valores iniciales de busqueda
	$referencia = $referencia_ant;
	// Volvemos a reasignar la variable "version" en el caso de que su valor fuese NULL
	$version = $_GET["version"];

	// Guardar las variables del formulario en variable de sesion
	$_SESSION["periferico_periferico"] = stripslashes(htmlspecialchars($periferico));
	$_SESSION["referencia_periferico"] = stripslashes(htmlspecialchars($referencia));
	$_SESSION["version_periferico"] = stripslashes(htmlspecialchars($version));
	$_SESSION["descripcion_periferico"] = stripslashes(htmlspecialchars($descripcion));
	$_SESSION["estado_periferico"] = $estado;
	$_SESSION["prototipo_periferico"] = $prototipo;
	$_SESSION["fecha_desde_periferico"] = $fecha_desde;
	$_SESSION["fecha_hasta_periferico"] = $fecha_hasta;
}

$titulo_pagina = "Básicos > Periféricos";
$pagina = "perifericos";
include ("../includes/header.php");
?>

<div class="separador"></div>
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>

	<h3>Perif&eacute;ricos</h3>
    <h4>Buscar perif&eacute;ricos</h4>

    <form id="BuscadorPerifericos" name="buscadorPerifericos" action="perifericos.php" method="get" class="Buscador">
    <table style="border:0;">
    	<tr style="border:0;">
        	<td>
            	<div class="Label">Nombre</div>
           		<input type="text" id="" name="periferico" class="BuscadorInput" value="<?php echo $_SESSION["periferico_periferico"];?>"/>
            </td>
            <td>
            	<div class="Label">Referencia</div>
           		<input type="text" id="" name="referencia" class="BuscadorInput" value="<?php echo $_SESSION["referencia_periferico"];?>"/>
            </td>
            <td>
            	<div class="Label">Versi&oacute;n</div>
           		<input type="text" id="" name="version" class="BuscadorInput" value="<?php echo $_SESSION["version_periferico"];?>"/>
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">Descripcion</div>
           		<input type="text" id="" name="descripcion" class="BuscadorInput" value="<?php echo $_SESSION["descripcion_periferico"];?>"/>
            </td>
            <td>
            	<div class="Label">Fecha desde</div>
                <input type="text" name="fecha_desde" id="datepicker_perifericos_desde" class="fechaCal" value="<?php echo $_SESSION["fecha_desde_periferico"];?>"/>
            </td>
            <td>
            	<div class="Label">Fecha hasta</div>
                <input type="text" name="fecha_hasta" id="datepicker_perifericos_hasta" class="fechaCal" value="<?php echo $_SESSION["fecha_hasta_periferico"];?>"/>
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">Estado</div>
           		<select id="estado" name="estado" class="BuscadorInput"/>
                	<option value=""></option>
                	<option value="BORRADOR"
						<?php if($_SESSION["estado_periferico"] == "BORRADOR") { echo ' selected="selected"'; } ?>>BORRADOR</option>
                	<option value="PRODUCCIÓN"
						<?php if($_SESSION["estado_periferico"] == "PRODUCCIÓN") { echo ' selected="selected"'; } ?>>PRODUCCIÓN</option>
                	<option value="LEGACY"
						<?php if($_SESSION["estado_periferico"] == "LEGACY") { echo ' selected="selected"'; } ?>>LEGACY</option>
                </select>
            </td>
            <td>
            	<div class="Label">Prototipo</div>
				<select id="prototipo" name="prototipo" class="BuscadorInput"/>
                	<option value=""></option>
                    <option value="0"<?php if ($_SESSION["prototipo_periferico"] == "0") { echo ' selected="selected"';}?>>NO</option>
                    <option value="1"<?php if ($_SESSION["prototipo_periferico"] == "1") { echo ' selected="selected"';}?>>SI</option>
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
            if($_GET["per"] == "creado") {
                echo '<div class="mensaje">El periferico se ha creado correctamente</div>';
            }
            if($_GET["per"] == "modificado") {
                echo '<div class="mensaje">El periferico se ha modificado correctamente</div>';
            }
            if($_GET["per"] == "eliminado") {
                echo '<div class="mensaje">El periferico se ha eliminado correctamente</div>';
            }
            if($_GET["per"] == "duplicado") {
                echo '<div class="mensaje">El periferico original se ha modificado y duplicado correctamente</div>';
            }
            if($_GET["per"] == "actualizado") {
                echo '<div class="mensaje">El periferico se ha actualizado correctamente</div>';
            }
            if($mostrar_tabla){
                if($num_resultados == NULL or $num_resultados == 0){
                   echo '<div class="mensaje">No se encontraron periféricos</div>';
                   $mostrar_tabla = false;
                }
                else if ($num_resultados == 1){
                    echo '<div class="mensaje">Se encontró 1 periférico</div>';
                }
                else{
                    echo '<div class="mensaje">Se encontraron '.$num_resultados.' periféricos</div>';
                }   
            }
		?>
    </div>

    <?php
		if ($mostrar_tabla){
		?>
   			<div class="CapaTabla">
    		<table>
        	<tr>
        		<th>NOMBRE</th>
            	<th>REFERENCIA</th>
            	<th style="text-align:center">VERSION</th>
                <th style="text-align:center">REFERENCIAS</th>
            	<th>DESCRIPCION</th>
                <th style="text-align:center">KITS</th>
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
				for($i=0;$i<count($resultadosBusqueda);$i++) {
					// Se cargan los datos de los perifericos según su identificador
					$per = new Periferico();
					$datoPeriferico = $resultadosBusqueda[$i];
					$per->cargaDatosPerifericoId($datoPeriferico["id_componente"]);
					?>
					<tr>
						<td>
                            <a href="mod_periferico.php?id=<?php echo $per->id_componente; ?>"><?php echo $per->periferico; ?></a>
						</td>
						<td><?php echo $per->referencia; ?></td>
						<td style="text-align:center"><?php echo number_format($per->version, 2, '.', '');?></td>
                        <td style="text-align:center">
                        	<a href="javascript:abrir('muestra_referencias.php?nombre=<?php echo $per->periferico;?>&tipo=periferico&id=<?php echo $per->id_componente;?>')">
                            	REFERENCIAS
                            </a>
                            -
                            <a href="../basicos/informe_referencias.php?tipo=periferico&id=<?php echo $per->id_componente;?>">XLS</a>
                        </td>
						<td><?php echo $per->descripcion; ?></td>
                        <td style="text-align:center">
                        	<a href="javascript:abrir('muestra_kits.php?nombre=<?php echo $per->periferico;?>&id=<?php echo $per->id_componente;?>')">
                            	KITS
                            </a>
                        </td>
                        <td><?php echo $per->estado; ?></td>
                        <td style="text-align:center">
                        	<?php
								if ($per->prototipo == 0){
									echo "NO";
								}
								else echo "SI";
							?>
						</td>
                        <?php 
                            if(permisoMenu(4)){ ?>
                                <td style="text-align:center">
                                	<input type="button" id="eliminar" name="eliminar" value="ELIMINAR" class="BotonEliminar" onclick="javascript: if (confirm('¿Desea eliminar el periferico?')) { window.location.href='elim_periferico.php?id=<?php echo $per->id_componente;?>' } else { void('') };" />
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
                        <a href="perifericos.php?pg=1&realizandoBusqueda=1&periferico=<?php echo $_SESSION["periferico_periferico"];?>&referencia<?php echo $_SESSION["referencia_periferico"];?>&version=<?php echo $_SESSION["version_periferico"];?>&descripcion=<?php echo $_SESSION["descripcion_periferico"];?>&estado=<?php echo $_SESSION["estado_periferico"];?>&prototipo=<?php echo $_SESSION["prototipo_periferico"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_periferico"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_periferico"];?>">Primera&nbsp&nbsp&nbsp</a>
                        <a href="perifericos.php?pg=<?php echo $pg_pagina - 1;?>&realizandoBusqueda=1&periferico=<?php echo $_SESSION["periferico_periferico"];?>&referencia<?php echo $_SESSION["referencia_periferico"];?>&version=<?php echo $_SESSION["version_periferico"];?>&descripcion=<?php echo $_SESSION["descripcion_periferico"];?>&estado=<?php echo $_SESSION["estado_periferico"];?>&prototipo=<?php echo $_SESSION["prototipo_periferico"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_periferico"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_periferico"];?>"> Anterior</a>
                <?php  
                    }  
                    else {
                        echo 'Primera&nbsp;&nbsp;&nbsp;Anterior';
                    }
            
                    echo ' &nbsp;&nbsp;&nbsp;['.$pg_pagina.' / '.$pg_totalPaginas.']&nbsp;&nbsp;&nbsp;';
                    if($pg_pagina < $pg_totalPaginas) { ?>
                        <a href="perifericos.php?pg=<?php echo $pg_pagina + 1;?>&realizandoBusqueda=1&periferico=<?php echo $_SESSION["periferico_periferico"];?>&referencia<?php echo $_SESSION["referencia_periferico"];?>&version=<?php echo $_SESSION["version_periferico"];?>&descripcion=<?php echo $_SESSION["descripcion_periferico"];?>&estado=<?php echo $_SESSION["estado_periferico"];?>&prototipo=<?php echo $_SESSION["prototipo_periferico"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_periferico"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_periferico"];?>">Siguiente&nbsp&nbsp&nbsp</a>
                        <a href="perifericos.php?pg=<?php echo $pg_totalPaginas; ?>&realizandoBusqueda=1&periferico=<?php echo $_SESSION["periferico_periferico"];?>&referencia<?php echo $_SESSION["referencia_periferico"];?>&version=<?php echo $_SESSION["version_periferico"];?>&descripcion=<?php echo $_SESSION["descripcion_periferico"];?>&estado=<?php echo $_SESSION["estado_periferico"];?>&prototipo=<?php echo $_SESSION["prototipo_periferico"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_periferico"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_periferico"];?>">Última</a>
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

    
