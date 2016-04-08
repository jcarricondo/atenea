<?php
// Este fichero muestra el listado de direcciones
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/direccion.class.php");
include("../classes/basicos/listado_direcciones.class.php");
permiso(1);

// Se obtienen los datos del formulario
if($_GET["dir"] == "creado" or $_GET["dir"] == "modificado" or $_GET ["dir"] == "eliminado") {
	$realizarBusqueda = 1;
}
if(isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) {
	$mostrar_tabla = true;
	$nombre_empresa = addslashes($_GET["nombre_empresa"]);
	$cif = addslashes($_GET["cif"]);
	$direccion = addslashes($_GET["direccion"]);
	$codigo_postal = addslashes($_GET["codigo_postal"]);
	$localidad = addslashes($_GET["localidad"]);
	$provincia = addslashes($_GET["provincia"]);
	$telefono = addslashes($_GET["telefono"]);
	$tipo_direccion = $_GET["tipo_direccion"];
	$persona_contacto = addslashes($_GET["persona_contacto"]);
	$comentarios = addslashes($_GET["comentarios"]);
	$fecha_desde = $_GET["fecha_desde"];
	$fecha_hasta = $_GET["fecha_hasta"];
	
	$funciones = new Funciones();
	// Convierte la fecha a formato MySQL
	if ($fecha_desde != "") $fecha_desde = $funciones->cFechaMy($fecha_desde);
	if ($fecha_hasta != "") $fecha_hasta = $funciones->cFechaMy($fecha_hasta);
		
	$listadir = new listadoDirecciones();
	// Se pasan los datos del buscador a la clase del listado y se realiza la consulta a la base de datos
	$listadir->setValores($nombre_empresa,$cif,$direccion,$codigo_postal,$localidad,$provincia,$telefono,$tipo_direccion,$persona_contacto,$fecha_desde,$fecha_hasta);
	$listadir->realizarConsulta();
	$resultadosBusqueda = $listadir->direcciones;
	$num_resultados = count($resultadosBusqueda);
	
	// Convierte la fecha a formato HTML
	if ($fecha_desde != "") $fecha_desde = $funciones->cFechaNormal($fecha_desde);
	if ($fecha_hasta != "") $fecha_hasta = $funciones->cFechaNormal($fecha_hasta);
	
	// Guardar las variables del formulario en variable de sesion
	$_SESSION["nombre_empresa_direcciones"] = stripslashes(htmlspecialchars($nombre_empresa));
	$_SESSION["cif_direcciones"] = stripslashes(htmlspecialchars($cif));
	$_SESSION["direccion_direcciones"] = stripslashes(htmlspecialchars($direccion));
	$_SESSION["codigo_postal_direcciones"] = stripslashes(htmlspecialchars($codigo_postal));
	$_SESSION["localidad_direcciones"] = stripslashes(htmlspecialchars($localidad));
	$_SESSION["provincia_direcciones"] = stripslashes(htmlspecialchars($provincia));
	$_SESSION["telefono_direcciones"] = stripslashes(htmlspecialchars($telefono));
	$_SESSION["tipo_direccion_direcciones"] = $tipo_direccion;
	$_SESSION["persona_contacto_direcciones"] = stripslashes(htmlspecialchars($persona_contacto));
	$_SESSION["comentarios_direcciones"] = stripslashes(htmlspecialchars($comentarios));
	$_SESSION["fecha_desde_direcciones"] = $fecha_desde;
	$_SESSION["fecha_hasta_direcciones"] = $fecha_hasta;
}

$titulo_pagina = "Básicos > Direcciones";
$pagina = "direcciones";
include("../includes/header.php");
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include("../includes/sidebar.php"); ?>
    </div>
       	
    <h3> Listado de direcciones </h3>
    <h4> Buscar direcciones </h4>
    
    <form id="BuscadorDirecciones" name="buscadorDirecciones" action="direcciones.php" method="get" class="Buscador">
    <table style="border:0;">
    	<tr style="border:0;">
        	<td>
            	<div class="Label">Nombre Empresa</div>
                <input type="text" id="nombre_empresa" name="nombre_empresa" class="BuscadorInput" value="<?php echo $_SESSION["nombre_empresa_direcciones"];?>"/>
            </td>
            <td>
            	<div class="Label">CIF</div>
                <input type="text" id="cif" name="cif" class="BuscadorInput" value="<?php echo $_SESSION["cif_direcciones"];?>"/>
            </td>
            <td>
            	<div class="Label">Dirección</div>
                <input type="text" id="direccion" name="direccion" class="BuscadorInput" value="<?php echo $_SESSION["direccion_direcciones"];?>"/>
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">CP</div>
                <input type="text" id="codigo_postal" name="codigo_postal" class="BuscadorInput" value="<?php echo $_SESSION["codigo_postal_direcciones"];?>"/>
            </td>
            <td>
            	<div class="Label">Localidad</div>
                <input type="text" id="localidad" name="localidad" class="BuscadorInput" value="<?php echo $_SESSION["localidad_direcciones"];?>"/>
            </td>
            <td>
            	<div class="Label">Provincia</div>
                <input type="text" id="provincia" name="provincia" class="BuscadorInput" value="<?php echo $_SESSION["provincia_direcciones"];?>"/>
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">Telefono</div>
                <input type="text" id="telefono" name="telefono" class="BuscadorInput" value="<?php echo $_SESSION["telefono_direcciones"];?>"/>
            </td>
            <td>
            	<div class="Label">Persona de Contacto</div>
                <input type="text" id="persona_contacto" name="persona_contacto" class="BuscadorInput" value="<?php echo $_SESSION["persona_contacto_direcciones"];?>"/>
            </td>
            <td>
            	<div class="Label">Tipo</div>
                <select id="tipo_direccion" name="tipo_direccion" class="BuscadorInput">
                	<option></option>
                    <?php 
                        $num_tipos = 2;
                        $tipos = array ("0","1");
                        for($i=0;$i<$num_tipos;$i++) {
                            echo '<option value="'.$tipos[$i].'"';
                            if($tipos[$i] == $_SESSION["tipo_direccion_direcciones"]) echo ' selected="selected"';
                            echo '>';
                            if ($tipos[$i] == "0") echo "ENTREGA";
                            else echo "FACTURACION";
                            echo '</option>';
                        }
                    ?>
                </select> 
            </td>
            <tr style="border:0;">
				<td>
            	<div class="Label">Fecha desde</div>
           		<input type="text" name="fecha_desde" id="datepicker_direcciones_desde" class="fechaCal" value="<?php echo $_SESSION["fecha_desde_direcciones"];?>"/>
            </td>
            <td>
            	<div class="Label">Fecha hasta</div>
           		<input type="text" name="fecha_hasta" id="datepicker_direcciones_hasta" class="fechaCal" value="<?php echo $_SESSION["fecha_hasta_direcciones"];?>"/>
            </td>
            </tr>
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
			if($_GET["dir"] == "creado") {
				echo '<div class="mensaje">La dirección se ha creado correctamente</div>';
			}
			if($_GET["dir"] == "modificado") {
				echo '<div class="mensaje">La dirección se ha modificado correctamente</div>';
			}
			if($_GET["dir"] == "eliminado") {
				echo '<div class="mensaje">La dirección se ha eliminado correctamente</div>';
			}		
			if($mostrar_tabla){
				if($num_resultados == NULL or $num_resultados == 0){
	               echo '<div class="mensaje">No se encontraron direcciones</div>';
	               $mostrar_tabla = false;
	            }
	            else if ($num_resultados == 1){
	            	echo '<div class="mensaje">Se encontró 1 dirección</div>';
	            }
	            else{
	            	echo '<div class="mensaje">Se encontraron '.$num_resultados.' direcciones</div>';
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
					<th>NOMBRE EMPRESA</th>
					<th>CIF</th>
					<th>DIRECCI&Oacute;N</th>
					<th>CP</th>
                    <th>LOCALIDAD</th>
					<th>PROVINCIA</th>
                    <th>PERSONA DE CONTACTO</th>
                    <th>TELEFONO</th>
					<th>TIPO</th>

					<?php 
						if(permisoMenu(4)){
					?>	
							<th>ELIMINAR</th>
					<?php 
						}
					?>
				</tr>
                <?php
					// Se cargan los datos de las direcciones según su identificador
					for($i=0;$i<count($resultadosBusqueda);$i++) {
						$dir = new Direccion();
						$datoDireccion = $resultadosBusqueda[$i];
						$dir->cargaDatosDireccionId($datoDireccion["id_direccion"]);
				?>
				<tr>
					<td>
						<a href="mod_direccion.php?id=<?php echo $dir->id_direccion; ?>"><?php echo $dir->nombre_empresa; ?></a>    
					</td>
					<td><?php echo $dir->cif; ?></td>
					<td><?php echo $dir->direccion; ?></td>
					<td><?php echo $dir->codigo_postal; ?></td>
					<td><?php echo $dir->localidad; ?></td>
					<td><?php echo $dir->provincia; ?></td>
                    <td><?php echo $dir->persona_contacto; ?></td>
                    <td><?php echo $dir->telefono; ?></td>
					<td>
						<?php 
							if ($dir->tipo == "0") echo "ENTREGA";
							else echo "FACTURACIÓN"; 
						?>
					</td>
                    <?php 
						if(permisoMenu(4)){
					?>
		                    <td>
		                       	<input type="button" id="menos" name="menos" value="ELIMINAR" class="BotonEliminar" onclick="javascript: if (confirm('¿Desea eliminar la dirección?')) { window.location.href='elim_direccion.php?id=<?php echo $dir->id_direccion;?>' } else { void('') };" /> 
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