<?php
// Este fichero muestra el listado de los centros logisticos
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/centro_logistico.class.php");
include("../classes/basicos/listado_centros_logisticos.class.php");
permiso(1);

// Se obtienen los datos del formulario
if($_GET["centro_logistico"] == "creado" or $_GET["centro_logistico"] == "modificado" or $_GET ["centro_logistico"] == "eliminado") {
	$realizarBusqueda = 1;
}

if(isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) {
	$mostrar_tabla = true;
	$centro = addslashes($_GET["centro"]);
	$direccion = addslashes($_GET["direccion"]);
	$telefono = addslashes($_GET["telefono"]);
	$email = addslashes($_GET["email"]);
	$ciudad = addslashes($_GET["ciudad"]);
	$pais = addslashes($_GET["pais"]);
	$forma_pago = $_GET["forma_pago"];
	$metodo_pago = $_GET["metodo_pago"];
	$tiempo_suministro = $_GET["tiempo_suministro"];
	$provincia = addslashes($_GET["provincia"]);
	$codigo_postal = addslashes($_GET["codigo_postal"]);
	$persona_contacto = addslashes($_GET["persona_contacto"]);
	$fecha_desde = $_GET["fecha_desde"];
	$fecha_hasta = $_GET["fecha_hasta"];

	$funciones = new Funciones();
	// Convierte la fecha a formato MySQL
	if ($fecha_desde != "") $fecha_desde = $funciones->cFechaMy($fecha_desde);
	if ($fecha_hasta != "") $fecha_hasta = $funciones->cFechaMy($fecha_hasta);
	
	// Se carga la clase para la base de datos y el listado de centros logisticos
	$listadoCentrosLogisticos = new listadoCentrosLogisticos();
	// Se pasan los datos del buscador a la clase del listado y se realiza la consulta a la base de datos
	$listadoCentrosLogisticos->setValores($centro,$direccion,$telefono,$email,$ciudad,$pais,$forma_pago,$metodo_pago,$tiempo_suministro,$provincia,$codigo_postal,$persona_contacto,$fecha_desde,$fecha_hasta);
	$listadoCentrosLogisticos->realizarConsulta();
	$resultadosBusqueda = $listadoCentrosLogisticos->centros;
	$num_resultados = count($resultadosBusqueda);
	
	// Convierte la fecha a formato HTML
	if ($fecha_desde != "") $fecha_desde = $funciones->cFechaNormal($fecha_desde);
	if ($fecha_hasta != "") $fecha_hasta = $funciones->cFechaNormal($fecha_hasta);
	
	// Guardar las variables del formulario en variable de sesion
	$_SESSION["centro_centrolog"] = stripslashes(htmlspecialchars($centro));
	$_SESSION["direccion_centrolog"] = stripslashes(htmlspecialchars($direccion));
	$_SESSION["telefono_centrolog"] = stripslashes(htmlspecialchars($telefono));
	$_SESSION["email_centrolog"] = stripslashes(htmlspecialchars($email));
	$_SESSION["ciudad_centrolog"] = stripslashes(htmlspecialchars($ciudad));
	$_SESSION["pais_centrolog"] = stripslashes(htmlspecialchars($pais));
	$_SESSION["forma_pago_centrolog"] = $forma_pago;
	$_SESSION["metodo_pago_centrolog"] = $metodo_pago;
	$_SESSION["tiempo_suministro_centrolog"] = $tiempo_suministro;
	$_SESSION["provincia_centrolog"] = stripslashes(htmlspecialchars($provincia));
	$_SESSION["codigo_postal_centrolog"] = stripslashes(htmlspecialchars($codigo_postal)); 
	$_SESSION["persona_contacto_centrolog"] = stripslashes(htmlspecialchars($persona_contacto));
	$_SESSION["fecha_desde_centrolog"] = $fecha_desde;
	$_SESSION["fecha_hasta_centrolog"] = $fecha_hasta;
}

$titulo_pagina = "Básicos > Centros Logísticos";
$pagina = "centros_logisticos";
include("../includes/header.php");
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include("../includes/sidebar.php"); ?>
    </div>
       	
    <h3>Centros Logísticos</h3>
    <h4>Buscar centro</h4>
    
    <form id="BuscadorCentroLogistico" name="buscadorCentroLogistico" action="centros_logisticos.php" method="get" class="Buscador">
    <table style="border:0;">
    	<tr style="border:0;">
            <td>
            	<div class="Label">Centro Logístico</div>
            	<input type="text" id="centro" name="centro" class="BuscadorInput" value="<?php echo $_SESSION["centro_centrolog"];?>"/>
            </td>
            <td>
            	<div class="Label">Direcci&oacute;n</div>
            	<input type="text" id="direccion" name="direccion" class="BuscadorInput" value="<?php echo $_SESSION["direccion_centrolog"];?>"/>
            </td>
            <td>
            	<div class="Label">Tel&eacute;fono</div>
           		<input type="text" id="telefono" name="telefono" class="BuscadorInput" value="<?php echo $_SESSION["telefono_centrolog"];?>"/>
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">Email</div>
           		<input type="text" id="email" name="email" class="BuscadorInput" value="<?php echo $_SESSION["email_centrolog"];?>"/>
            </td>
            <td>
            	<div class="Label">Ciudad</div>
           		<input type="text" id="ciudad" name="ciudad" class="BuscadorInput" value="<?php echo $_SESSION["ciudad_centrolog"];?>"/>
            </td>
            <td>
            	<div class="Label">Pais</div>
           		<input type="text" id="pais" name="pais" class="BuscadorInput" value="<?php echo $_SESSION["pais_centrolog"];?>"/>
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">Forma de pago</div>
          		<select id="forma_pago" name="forma_pago" class="BuscadorInput">
            		<option value="0">Todas</option>
                	<option value="1"<?php if($_SESSION["forma_pago_centrolog"] == 1) { echo ' selected="selected"'; } ?>>Transferencia bancaria</option>
                	<option value="2"<?php if($_SESSION["forma_pago_centrolog"] == 2) { echo ' selected="selected"'; } ?>>Tarjeta de crédito/débito</option>
                	<option value="3"<?php if($_SESSION["forma_pago_centrolog"] == 3) { echo ' selected="selected"'; } ?>>PayPal</option>
                	<option value="4"<?php if($_SESSION["forma_pago_centrolog"] == 4) { echo ' selected="selected"'; } ?>>Recibo domiciliado</option>
            	</select>
            </td>
            <td>
            	<div class="Label">M&eacute;todo de pago</div>
          		<select id="metodo_pago" name="metodo_pago" class="BuscadorInput">
            		<option value="0">Todos</option>
                	<option value="1"<?php if($_SESSION["metodo_pago_centrolog"] == 1) { echo ' selected="selected"'; } ?>>Pago previo</option>
                	<option value="2"<?php if($_SESSION["metodo_pago_centrolog"] == 2) { echo ' selected="selected"'; } ?>>30 días</option>
                	<option value="3"<?php if($_SESSION["metodo_pago_centrolog"] == 3) { echo ' selected="selected"'; } ?>>60 días</option>
                	<option value="4"<?php if($_SESSION["metodo_pago_centrolog"] == 4) { echo ' selected="selected"'; } ?>>90 días</option>
            	</select>
            </td>
            <td>
            	<div class="Label">Tiempo de suministro</div>
           		<select id="tiempo_suministro" name="tiempo_suministro" class="BuscadorInput">	
            		<option value="0">Todos</option>
                	<option value="1"<?php if($_SESSION["tiempo_suministro_centrolog"] == 1) { echo ' selected="selected"'; } ?>>7 días</option>
                	<option value="2"<?php if($_SESSION["tiempo_suministro_centrolog"] == 2) { echo ' selected="selected"'; } ?>>14 días</option>
                	<option value="3"<?php if($_SESSION["tiempo_suministro_centrolog"] == 3) { echo ' selected="selected"'; } ?>>30 días</option>
                	<option value="4"<?php if($_SESSION["tiempo_suministro_centrolog"] == 4) { echo ' selected="selected"'; } ?>>60 días</option>
                	<option value="5"<?php if($_SESSION["tiempo_suministro_centrolog"] == 5) { echo ' selected="selected"'; } ?>>90 días</option>
            	</select>
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">Provincia</div>
           		<input type="text" id="provincia" name="provincia" class="BuscadorInput" value="<?php echo $_SESSION["provincia_centrolog"];?>"/>
            </td>
            <td>
            	<div class="Label">Código Postal</div>
           		<input type="text" id="codigo_postal" name="codigo_postal" class="BuscadorInput" value="<?php echo $_SESSION["codigo_postal_centrolog"];?>"/>
            </td>
            <td>
            	<div class="Label">Contacto</div>
           		<input type="text" id="persona_contacto" name="persona_contacto" class="BuscadorInput" value="<?php echo $_SESSION["persona_contacto_centrolog"];?>"/>
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">Fecha desde</div>
           		<input type="text" name="fecha_desde" id="datepicker_proveedores_desde" class="fechaCal" value="<?php echo $_SESSION["fecha_desde_centrolog"];?>"/>
            </td>
            <td>
            	<div class="Label">Fecha hasta</div>
           		<input type="text" name="fecha_hasta" id="datepicker_proveedores_hasta" class="fechaCal" value="<?php echo $_SESSION["fecha_hasta_centrolog"];?>"/>
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
			if($_GET["centro_logistico"] == "creado") {
				echo '<div class="mensaje">El centro logístico se ha creado correctamente</div>';
			}
			if($_GET["centro_logistico"] == "modificado") {
				echo '<div class="mensaje">El centro logístico se ha modificado correctamente</div>';
			}
			if($_GET["centro_logistico"] == "eliminado") {
				echo '<div class="mensaje">El centro logístico se ha eliminado correctamente</div>';
			}	
			if($mostrar_tabla){
				if($num_resultados == NULL or $num_resultados == 0){
	               echo '<div class="mensaje">No se encontraron centros</div>';
	               $mostrar_tabla = false;
	            }
	            else if ($num_resultados == 1){
	            	echo '<div class="mensaje">Se encontró 1 centro</div>';
	            }
	            else{
	            	echo '<div class="mensaje">Se encontraron '.$num_resultados.' centros</div>';
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
                    <th>CP</th>
					<th>TELEFONO</th>
					<th>EMAIL</th>
					<th>CIUDAD</th>
                    <th>PROVINCIA</th>
					<th>PAIS</th>
					<th>FORMA DE PAGO</th>
					<th>METODO DE PAGO</th>
					<th>T. MEDIO SUMINISTRO</th>
                    <th>CONTACTO</th>
                    <?php 
						if(permisoMenu(37)){
					?>
                    		<th style="text-align:center">ELIMINAR</th>
                   	<?php 
                   		}
                   	?>
				</tr>
                <?php
					// Se cargan los datos de los centros según su identificador
					for($i=0;$i<count($resultadosBusqueda);$i++) {
						$centroLogistico = new centroLogistico();
						$datoCentroLogistico = $resultadosBusqueda[$i];
						$centroLogistico->cargaDatosCentroLogisticoId($datoCentroLogistico["id_centro_logistico"]);
				?>
				<tr>
					<td>
						<a href="mod_centro_logistico.php?id=<?php echo $centroLogistico->id_centro_logistico;?>"><?php echo $centroLogistico->nombre; ?></a>    
					</td>
					<td><?php echo $centroLogistico->direccion;?></td>
                    <td><?php echo $centroLogistico->codigo_postal;?></td>
					<td><?php echo $centroLogistico->telefono;?></td>
					<td><?php echo $centroLogistico->email;?></td>
					<td><?php echo $centroLogistico->ciudad;?></td>
                    <td><?php echo $centroLogistico->provincia;?></td>
					<td><?php echo $centroLogistico->pais;?></td>
					<td>
						<?php 
							switch($centroLogistico->forma_pago) {
								case 1:
									echo 'Transferencia bancaria';
									break;
								case 2:
									echo 'Tarjeta de crédito/débito';
									break;
								case 3:
									echo 'PayPal';
									break;
								case 4:
									echo 'Recibo domiciliado';
									break;
							}
						?>
					</td>
					<td>
						<?php 
							switch($centroLogistico->metodo_pago) {
								case 1:
									echo 'Pago Previo';
									break;
								case 2:
									echo '30 días';
									break;
								case 3:
									echo '60 días';
									break;
								case 4:
									echo '90 días';
									break;
							}
						?>
					</td>
					<td>
						<?php 
							switch($centroLogistico->tiempo_suministro) {
								case 1:
									echo '7 días';
									break;
								case 2:
									echo '14 días';
									break;
								case 3:
									echo '30 días';
									break;
								case 4:
									echo '60 días';
									break;
								case 5:
									echo '90 días';
									break;
							}
						?>
					</td>
                    <td><?php echo $centroLogistico->persona_contacto;?></td>
                    <?php 
						if(permisoMenu(37)){
					?>
		                    <td style="text-align:center;">
		                       	<input type="button" id="menos" name="menos" value="ELIMINAR" class="BotonEliminar" onclick="javascript: if (confirm('¿Desea eliminar el centro logístico?')) { window.location.href='elim_centro_logistico.php?id=<?php echo $centroLogistico->id_centro_logistico;?>' } else { void('') };" /> 
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
<?php include ('../includes/footer.php');?>
