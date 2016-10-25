<?php
// Este fichero muestra el listado de las cabinas
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/cabina.class.php");
include("../classes/basicos/listado_cabinas.class.php");
permiso(1);

// Se obtienen los datos del formulario
if($_GET["cab"] == "creado" or $_GET["cab"] == "modificado" or $_GET["cab"] == "eliminado" or $_GET["cab"] == "duplicado" or $_GET["cab"] == "actualizado") {
	$realizarBusqueda = 1;
}
if(isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) {
	$mostrar_tabla = true;
	$cabina = addslashes($_GET["cabina"]);
	$referencia = addslashes($_GET["referencia"]);
	$version = addslashes($_GET["version"]);
	$descripcion = addslashes($_GET["descripcion"]);
	$estado = $_GET["estado"];
	$prototipo = $_GET["prototipo"];
	$fecha_desde = $_GET["fecha_desde"];
	$fecha_hasta = $_GET["fecha_hasta"];

	if (!is_numeric($version)) $version = NULL;

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

	$cabinas = new listadoCabinas();
	// Se pasan los datos del buscador a la clase del listado y se realiza la consulta a la base de datos
	$cabinas->setValores($cabina,$referencia,$version,$descripcion,$estado,$prototipo,$fecha_desde,$fecha_hasta);
	$cabinas->realizarConsulta();
	$resultadosBusqueda = $cabinas->cabinas;
    $num_resultados = count($resultadosBusqueda);

	// Convierte la fecha a formato HTML
	if ($fecha_desde != "") $fecha_desde = $funciones->cFechaNormal($fecha_desde);
	if ($fecha_hasta != "") $fecha_hasta = $funciones->cFechaNormal($fecha_hasta);

	// Mostramos los valores iniciales de busqueda
	$referencia = $referencia_ant;
	// Volvemos a reasignar la variable "version" en el caso de que su valor fuese NULL
	$version = $_GET["version"];

	// Guardar las variables del formulario en variable de sesion
	$_SESSION["cabina_cabina"] = stripslashes(htmlspecialchars($cabina));
	$_SESSION["referencia_cabina"] = stripslashes(htmlspecialchars($referencia));
	$_SESSION["version_cabina"] = stripslashes(htmlspecialchars($version));
	$_SESSION["descripcion_cabina"] = stripslashes(htmlspecialchars($descripcion));
	$_SESSION["estado_cabina"] = $estado;
	$_SESSION["prototipo_cabina"] = $prototipo;
	$_SESSION["fecha_desde_cabina"] = $fecha_desde;
	$_SESSION["fecha_hasta_cabina"] = $fecha_hasta;
}

$titulo_pagina = "Básicos > Cabinas";
$pagina = "cabinas";
include ("../includes/header.php");
?>

<div class="separador"></div>
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>

    <h3>Cabinas</h3>
    <h4>Buscar cabina</h4>

    <form id="BuscadorCabina" name="buscadorCabina" action="cabinas.php" method="get" class="Buscador">
    <table style="border:0;">
    	<tr style="border:0;">
        	<td>
            	<div class="Label">Nombre</div>
            	<input type="text" id="" name="cabina" class="BuscadorInput" value="<?php echo $_SESSION["cabina_cabina"];?>"/>
            </td>
            <td>
            	<div class="Label">Referencia</div>
            	<input type="text" id="" name="referencia" class="BuscadorInput" value="<?php echo $_SESSION["referencia_cabina"];?>"/>
            </td>
            <td>
            	<div class="Label">Versi&oacute;n</div>
            	<input type="text" id="" name="version" class="BuscadorInput" value="<?php echo  $_SESSION["version_cabina"];?>"/>
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">Descripcion</div>
           		<input type="text" id="" name="descripcion" class="BuscadorInput" value="<?php echo $_SESSION["descripcion_cabina"];?>"/>
            </td>
            <td>
            	<div class="Label">Fecha desde</div>
                <input type="text" name="fecha_desde" id="datepicker_cabinas_desde" class="fechaCal" value="<?php echo $_SESSION["fecha_desde_cabina"];?>"/>
            </td>
            <td>
            	<div class="Label">Fecha hasta</div>
                <input type="text" name="fecha_hasta" id="datepicker_cabinas_hasta" class="fechaCal" value="<?php echo $_SESSION["fecha_hasta_cabina"];?>"/>
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">Estado</div>
           		<select id="estado" name="estado" class="BuscadorInput"/>
                	<option value=""></option>
                	<option value="BORRADOR"
						<?php if($_SESSION["estado_cabina"] == "BORRADOR") { echo ' selected="selected"'; } ?>>BORRADOR</option>
                	<option value="PRODUCCIÓN"
						<?php if($_SESSION["estado_cabina"] == "PRODUCCIÓN") { echo ' selected="selected"'; } ?>>PRODUCCIÓN</option>
                	<option value="LEGACY"
						<?php if($_SESSION["estado_cabina"] == "LEGACY") { echo ' selected="selected"'; } ?>>LEGACY</option>
                </select>
            </td>
            <td>
            	<div class="Label">Prototipo</div>
				<select id="prototipo" name="prototipo" class="BuscadorInput"/>
                	<option value=""></option>
                    <option value="0"<?php if ($_SESSION["prototipo_cabina"] == "0") { echo ' selected="selected"';}?>>NO</option>
                    <option value="1"<?php if ($_SESSION["prototipo_cabina"] == "1") { echo ' selected="selected"';}?>>SI</option>
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
		   if($_GET["cab"] == "creado") {
		      echo '<div class="mensaje">La cabina se ha creado correctamente</div>';
		   }
		   if($_GET["cab"] == "modificado") {
		      echo '<div class="mensaje">La cabina se ha modificado correctamente</div>';
		   }
		   if($_GET["cab"] == "eliminado") {
		      echo '<div class="mensaje">La cabina se ha eliminado correctamente</div>';
		   }
		   if($_GET["cab"] == "duplicado") {
		      echo '<div class="mensaje">La cabina original se ha modificado y duplicado correctamente</div>';
		   }
		   if($_GET["cab"] == "actualizado") {
		      echo '<div class="mensaje">La cabina se ha actualizado correctamente</div>';
		   }
           if($mostrar_tabla){
                if($num_resultados == NULL or $num_resultados == 0){
                   echo '<div class="mensaje">No se encontraron cabinas</div>';
                   $mostrar_tabla = false;
                }
                else if ($num_resultados == 1){
                    echo '<div class="mensaje">Se encontró 1 cabina</div>';
                }
                else{
                    echo '<div class="mensaje">Se encontraron '.$num_resultados.' cabinas</div>';
                }   
            }
		?>
    </div>

    <?php
		if ($mostrar_tabla) {
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
				// Se cargan los datos de las cabinas según su identificador
				for($i=0;$i<count($resultadosBusqueda);$i++) {
					$cab = new Cabina();
					$datoCabina = $resultadosBusqueda[$i];
					$cab->cargaDatosCabinaId($datoCabina["id_componente"]);
					?>
					<tr>
						<td>
  							<a href="mod_cabina.php?id=<?php echo $cab->id_componente; ?>"><?php echo $cab->cabina;?></a>
                        </td>
                        <td><?php echo $cab->referencia;?></td>
						<td style="text-align:center"><?php echo number_format($cab->version, 2, '.', '');?></td>
						<td style="text-align:center">
                        	<a href="javascript:abrir('muestra_referencias.php?nombre=<?php echo $cab->cabina;?>&tipo=cabina&id=<?php echo $cab->id_componente;?>')">                             	REFERENCIAS
                            </a>
                            -
							<a href="../basicos/informe_referencias.php?tipo=cabina&id=<?php echo $cab->id_componente;?>">XLS</a>
                        </td>
                        <td><?php echo $cab->descripcion;?></td>
                        <td style="text-align:center">
                        	<a href="javascript:abrir('muestra_kits.php?nombre=<?php echo $cab->cabina;?>&id=<?php echo $cab->id_componente;?>')">
                            	KITS
                            </a>
                        </td>
                        <td><?php echo $cab->estado;?></td>
                        <td style="text-align:center">
                          	<?php
								if ($cab->prototipo == 0){
									echo "NO";
								}
								else echo "SI";
							?>
						</td>
                        <?php 
                            if(permisoMenu(4)){
                        ?>
                                <td style="text-align:center">
                                	<input type="button" id="menos" name="menos" value="ELIMINAR" class="BotonEliminar" onclick="javascript: if (confirm('¿Desea eliminar la cabina?')) { window.location.href='elim_cabina.php?id=<?php echo $cab->id_componente;?>' } else { void('') };" />
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