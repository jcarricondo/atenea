<?php
// Este fichero muestra el listado de las familias
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/familia.class.php");
include("../classes/basicos/listado_familias.class.php");
include("../classes/basicos/usuario.class.php");
include("../classes/control_usuario.class.php");
permiso(1);

$user = new Usuario();
$controlUser = new Control_Usuario();

$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$id_almacen_usuario = $_SESSION["AT_id_almacen"];
$id_sede_usuario = $controlUser->dameSedeSegunUsuario($id_tipo_usuario,$id_almacen_usuario);

// Comprobamos si es usuario de Brasil para mostrar la hora correcta
$esUsuarioBrasil = $controlUser->esUsuarioBrasil($id_tipo_usuario,$id_sede_usuario);

// Se obtienen los datos del formulario
if($_GET["familia"] == "creado" or $_GET["familia"] == "modificado" or $_GET["familia"] == "eliminado") {
	$realizarBusqueda = 1;
}
if(isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) {
	$mostrar_tabla = true;
	$nombre = addslashes($_GET["nombre_familia"]);
	$fecha_desde = $_GET["fecha_desde"];
	$fecha_hasta = $_GET["fecha_hasta"];
	
	$funciones = new Funciones();
	// Convierte la fecha a formato MySql
	if($fecha_desde != "") $fecha_desde = $funciones->cFechaMy($fecha_desde);
	if($fecha_hasta != "") $fecha_hasta = $funciones->cFechaMy($fecha_hasta);
		
	$familias = new listadoFamilias();
	// Se pasan los datos del buscador a la clase del listado y se realiza la consulta a la base de datos
	$familias->setValores($nombre,$fecha_desde,$fecha_hasta);
	$familias->realizarConsulta();
	$resultadosBusqueda = $familias->familias;
	$num_resultados = count($resultadosBusqueda);
	
	// Convierte la fecha a formato HTML
	if($fecha_desde != "") $fecha_desde = $funciones->cFechaNormal($fecha_desde);
	if($fecha_hasta != "") $fecha_hasta = $funciones->cFechaNormal($fecha_hasta);
	
	// Guardar las variables del formulario en variable de sesion
	$_SESSION["nombre_familias"] = stripslashes(htmlspecialchars($nombre));
	$_SESSION["fecha_desde_familias"] = $fecha_desde;
	$_SESSION["fecha_hasta_familias"] = $fecha_hasta;
}

$titulo_pagina = "Básicos > Familias";
$pagina = "familias";
include ("../includes/header.php");
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>
    
   	<h3> Listado familias </h3>
    <h4> Buscar familias </h4>
    
    <form id="BuscadorFamilias" name="buscadorFamilias" action="familias.php" method="get" class="Buscador">
		<table style="border:0;">
		<tr style="border:0;">
        	<td>
				<div class="Label">Nombre</div>
				<input type="text" id="nombre_familia" name="nombre_familia" class="BuscadorInput" value="<?php echo $_SESSION["nombre_familias"]; ?>" />
            </td>
            <td>
            	<div class="Label">Fecha desde</div>
           		<input type="text" name="fecha_desde" id="datepicker_familias_desde" class="fechaCal" value="<?php echo $_SESSION["fecha_desde_familias"];?>"/>
            </td>
            <td>
            	<div class="Label">Fecha hasta</div>
           		<input type="text" name="fecha_hasta" id="datepicker_familias_hasta" class="fechaCal" value="<?php echo $_SESSION["fecha_hasta_familias"];?>"/>
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
    
    <div class="ContenedorBotonCrear">
  		<?php
			if($_GET["familia"] == "creado") {
				echo '<div class="mensaje">La familia se ha creado correctamente</div>';
			}
			if($_GET["familia"] == "modificado") {
				echo '<div class="mensaje">La familia se ha modificado correctamente</div>';
			}
			if($_GET["familia"] == "eliminado") {
				echo '<div class="mensaje">La familia se ha eliminado correctamente</div>';
			}
			if($mostrar_tabla){
				if($num_resultados == NULL or $num_resultados == 0){
	               echo '<div class="mensaje">No se encontraron familias</div>';
	               $mostrar_tabla = false;
	            }
	            else if ($num_resultados == 1){
	            	echo '<div class="mensaje">Se encontró 1 familia</div>';
	            }
	            else{
	            	echo '<div class="mensaje">Se encontraron '.$num_resultados.' familias</div>';
	            }	
        	}	
		?>
    </div>
    
    <?php 
    	if($mostrar_tabla){	?>
    		<div class="CapaTabla">
    			<table>
        		<tr>
          			<th>NOMBRE</th>
          			<th style="text-align:center">FECHA CREADO</th>
          			<?php 
          				if(permisoMenu(4)){
          			?>
            				<th style="text-align:center">ELIMINAR</th>
            		<?php 
            			}
            		?>
        		</tr>
        
        	<?php
				// Se cargan los datos de las familias según su identificador
				for($i=0;$i<count($resultadosBusqueda);$i++) {
					$fam = new Familia();
					$datoFamilias = $resultadosBusqueda[$i];
					$fam->cargaDatosFamiliaId($datoFamilias["id_familia"]);

					if($esUsuarioBrasil){
                    	$fecha_creado = $user->fechaBrasil($fam->fecha_creado);
                     
                    }
                    else{
                        $fecha_creado = $user->fechaSpain($fam->fecha_creado);
                    }
			?>
					<tr>
						<td>
							<a href="mod_familia.php?id=<?php echo $fam->id_familia;?>"><?php echo $fam->nombre; ?></a>    
						</td>
						<td style="text-align:center"><?php echo $fecha_creado;?></td>
						<?php 
							if(permisoMenu(4)){
						?>
								<td style="text-align:center">
		                        	<input type="button" id="eliminar" name="eliminar" value="ELIMINAR" class="BotonEliminar" onclick="javascript: if (confirm('¿Desea eliminar la familia?')) { window.location.href='elim_familia.php?id=<?php echo $fam->id_familia;?>' } else { void('') };" /> 
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