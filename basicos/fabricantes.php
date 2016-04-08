<?php
// Este fichero muestra el listado de los fabricantes
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/fabricante.class.php");
include("../classes/basicos/listado_fabricantes.class.php");
permiso(1);

// Se obtienen los datos del formulario
if($_GET["fab"] == "creado" or $_GET["fab"] == "modificado" or $_GET ["fab"] == "eliminado") {
	$realizarBusqueda = 1;
}
if(isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) {
	$mostrar_tabla = true;
	$fabricante = addslashes($_GET["fabricante"]);
	$direccion = addslashes($_GET["direccion"]);
	$telefono = addslashes($_GET["telefono"]);
	$email = addslashes($_GET["email"]);
	$ciudad = addslashes($_GET["ciudad"]);
	$pais = addslashes($_GET["pais"]);
	$fecha_desde = $_GET["fecha_desde"];
	$fecha_hasta = $_GET["fecha_hasta"];
	
	$funciones = new Funciones();
	// Convierte la fecha a formato MySql
	if($fecha_desde != "") $fecha_desde = $funciones->cFechaMy($fecha_desde);
	if($fecha_hasta != "") $fecha_hasta = $funciones->cFechaMy($fecha_hasta);

	$fabricantes = new listadoFabricantes();
	// Se pasan los datos del buscador a la clase del listado y se realiza la consulta a la base de datos
	$fabricantes->setValores($fabricante,$direccion,$telefono,$email,$ciudad,$pais,$fecha_desde,$fecha_hasta);
	$fabricantes->realizarConsulta();
	$resultadosBusqueda = $fabricantes->fabricantes;
	$num_resultados = count($resultadosBusqueda);
	
	// Convierte la fecha a formato HTML
	if($fecha_desde != "") $fecha_desde = $funciones->cFechaNormal($fecha_desde);
	if($fecha_hasta != "") $fecha_hasta = $funciones->cFechaNormal($fecha_hasta);
	
	// Guardar las variables del formulario en variable de sesion
	$_SESSION["fabricante_fabricante"] = stripslashes(htmlspecialchars($fabricante));
	$_SESSION["direccion_fabricante"] = stripslashes(htmlspecialchars($direccion));
	$_SESSION["telefono_fabricante"] = stripslashes(htmlspecialchars($telefono));
	$_SESSION["email_fabricante"] = stripslashes(htmlspecialchars($email));
	$_SESSION["ciudad_fabricante"] = stripslashes(htmlspecialchars($ciudad));
	$_SESSION["pais_fabricante"] = stripslashes(htmlspecialchars($pais));
	$_SESSION["fecha_desde_fabricante"] = $fecha_desde;
	$_SESSION["fecha_hasta_fabricante"] = $fecha_hasta;
}

$titulo_pagina = "Básicos > Fabricantes";
$pagina = "fabricantes";
include("../includes/header.php");
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include("../includes/sidebar.php"); ?>
    </div>
       	
    <h3>Fabricantes</h3>
    <h4>Buscar fabricante</h4>
    
    <form id="BuscadorFabricante" name="buscadorfabricante" action="fabricantes.php" method="get" class="Buscador">
    <table style="border:0;">
    	<tr style="border:0;">
        	<td>
            	<div class="Label">Fabricante</div>
            	<input type="text" id="fabricante" name="fabricante" class="BuscadorInput" value="<?php echo $_SESSION["fabricante_fabricante"];?>"/>
            </td>
            <td>
            	<div class="Label">Direcci&oacute;n</div>
            	<input type="text" id="direccion" name="direccion" class="BuscadorInput" value="<?php echo $_SESSION["direccion_fabricante"];?>"/>
            </td>
            <td>
            	<div class="Label">Tel&eacute;fono</div>
           		<input type="text" id="telefono" name="telefono" class="BuscadorInput" value="<?php echo $_SESSION["telefono_fabricante"];?>"/>
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">Email</div>
           		<input type="text" id="email" name="email" class="BuscadorInput" value="<?php echo $_SESSION["email_fabricante"];?>"/>
            </td>
            <td>
            	<div class="Label">Ciudad</div>
           		<input type="text" id="ciudad" name="ciudad" class="BuscadorInput" value="<?php echo $_SESSION["ciudad_fabricante"];?>"/>
            </td>
            <td>
            	<div class="Label">Pais</div>
           		<input type="text" id="pais" name="pais" class="BuscadorInput" value="<?php echo $_SESSION["pais_fabricante"];?>"/>
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">Fecha desde</div>
           		<input type="text" name="fecha_desde" id="datepicker_fabricantes_desde" class="fechaCal" value="<?php echo $_SESSION["fecha_desde_fabricante"];?>"/>
            </td>
            <td>
            	<div class="Label">Fecha hasta</div>
           		<input type="text" name="fecha_hasta" id="datepicker_fabricantes_hasta" class="fechaCal" value="<?php echo $_SESSION["fecha_hasta_fabricante"];?>"/>
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
			if($_GET["fab"] == "creado") {
				echo '<div class="mensaje">El fabricante se ha creado correctamente</div>';
			}
			if($_GET["fab"] == "modificado") {
				echo '<div class="mensaje">El fabricante se ha modificado correctamente</div>';
			}
			if($_GET["fab"] == "eliminado") {
				echo '<div class="mensaje">El fabricante se ha eliminado correctamente</div>';
			}	
			if($mostrar_tabla){
				if($num_resultados == NULL or $num_resultados == 0){
	               echo '<div class="mensaje">No se encontraron fabricantes</div>';
	               $mostrar_tabla = false;
	            }
	            else if ($num_resultados == 1){
	            	echo '<div class="mensaje">Se encontró 1 fabricante</div>';
	            }
	            else{
	            	echo '<div class="mensaje">Se encontraron '.$num_resultados.' fabricantes</div>';
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
					<th>NOMBRE</th>
					<th>DIRECCION</th>
					<th>TELEFONO</th>
					<th>EMAIL</th>
					<th>CIUDAD</th>
					<th>PAIS</th>
					<?php 
						if(permisoMenu(4)){
					?>
							<th style="text-align:center">ELIMINAR</th>
					<?php 
						}
					?>
				</tr>
                <?php
				// Se cargan los datos de los fabricantes según su identificador
				for($i=0;$i<count($resultadosBusqueda);$i++) {
					$fab = new Fabricante();
					$datoFabricante = $resultadosBusqueda[$i];
					$fab->cargaDatosFabricanteId($datoFabricante["id_fabricante"]);
				?>
				<tr>
					<td>
						<a href="mod_fabricante.php?id=<?php echo $fab->id_fabricante;?>"><?php echo $fab->nombre;?></a>    
					</td>
					<td><?php echo $fab->direccion;?></td>
					<td><?php echo $fab->telefono;?></td>
					<td><?php echo $fab->email;?></td>
					<td><?php echo $fab->ciudad;?></td>
					<td><?php echo $fab->pais;?></td>
					<?php 
						if(permisoMenu(4)){
					?>
						    <td style="text-align:center">
		                       	<input type="button" id="eliminar" name="eliminar" value="ELIMINAR" class="BotonEliminar" onclick="javascript: if (confirm('¿Desea eliminar el fabricante?')) { window.location.href='elim_fabricante.php?id=<?php echo $fab->id_fabricante;?>' } else { void('') };" /> 
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
<?php include ('../includes/footer.php');  ?>
