<?php
// Este fichero muestra el listado de proveedores
include("../includes/sesion.php");
include("../classes/basicos/listado_proveedores.class.php");
include("../classes/basicos/proveedor.class.php");
include("../classes/funciones/funciones.class.php");
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
if($_GET["prov"] == "creado" or $_GET["prov"] == "modificado" or $_GET ["prov"] == "eliminado") {
	$realizarBusqueda = 1;
}

if(isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) {
	$mostrar_tabla = true;
	$proveedor = addslashes($_GET["proveedor"]);
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
	
	// Se carga la clase para la base de datos y el listado de proveedores
	$proveedores = new listadoProveedores();
	// Se pasan los datos del buscador a la clase del listado y se realiza la consulta a la base de datos sin paginacion 
	$proveedores->setValores($proveedor,$direccion,$telefono,$email,$ciudad,$pais,$forma_pago,$metodo_pago,$tiempo_suministro,$provincia,$codigo_postal,$persona_contacto,$fecha_desde,$fecha_hasta,'');
	$proveedores->realizarConsulta();
	$resultadosBusqueda = $proveedores->proveedores; 
	$num_resultados = count($resultadosBusqueda);

	// Se realiza la consulta con paginacion 
	$pg_totalPaginas = ceil(count($resultadosBusqueda) / $pg_registros);
	$proveedores->setValores($proveedor,$direccion,$telefono,$email,$ciudad,$pais,$forma_pago,$metodo_pago,$tiempo_suministro,$provincia,$codigo_postal,$persona_contacto,$fecha_desde,$fecha_hasta,$paginacion);
	$proveedores->realizarConsulta();
	$resultadosBusqueda = $proveedores->proveedores; 
	
	// Convierte la fecha a formato HTML
	if ($fecha_desde != "") $fecha_desde = $funciones->cFechaNormal($fecha_desde);
	if ($fecha_hasta != "") $fecha_hasta = $funciones->cFechaNormal($fecha_hasta);
	
	// Guardar las variables del formulario en variable de sesion
	$_SESSION["proveedor_prov"] = stripslashes(htmlspecialchars($proveedor));
	$_SESSION["direccion_prov"] = stripslashes(htmlspecialchars($direccion));
	$_SESSION["telefono_prov"] = stripslashes(htmlspecialchars($telefono));
	$_SESSION["email_prov"] = stripslashes(htmlspecialchars($email));
	$_SESSION["ciudad_prov"] = stripslashes(htmlspecialchars($ciudad));
	$_SESSION["pais_prov"] = stripslashes(htmlspecialchars($pais));
	$_SESSION["forma_pago_prov"] = $forma_pago;
	$_SESSION["metodo_pago_prov"] = $metodo_pago;
	$_SESSION["tiempo_suministro_prov"] = $tiempo_suministro;
	$_SESSION["provincia_prov"] = stripslashes(htmlspecialchars($provincia));
	$_SESSION["codigo_postal_prov"] = stripslashes(htmlspecialchars($codigo_postal)); 
	$_SESSION["persona_contacto_prov"] = stripslashes(htmlspecialchars($persona_contacto));
	$_SESSION["fecha_desde_prov"] = $fecha_desde;
	$_SESSION["fecha_hasta_prov"] = $fecha_hasta;
}

$titulo_pagina = "Básicos > Proveedores";
$pagina = "proveedores";
include("../includes/header.php");
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include("../includes/sidebar.php"); ?>
    </div>
       	
    <h3>Proveedores</h3>
    <h4>Buscar proveedor</h4>
    
    <form id="BuscadorProveedor" name="buscadorProveedor" action="proveedores.php" method="get" class="Buscador">
    <table style="border:0;">
    	<tr style="border:0;">
            <td>
            	<div class="Label">Proveedor</div>
            	<input type="text" id="proveedor" name="proveedor" class="BuscadorInput" value="<?php echo $_SESSION["proveedor_prov"];?>"/>
            </td>
            <td>
            	<div class="Label">Direcci&oacute;n</div>
            	<input type="text" id="direccion" name="direccion" class="BuscadorInput" value="<?php echo $_SESSION["direccion_prov"];?>"/>
            </td>
            <td>
            	<div class="Label">Tel&eacute;fono</div>
           		<input type="text" id="telefono" name="telefono" class="BuscadorInput" value="<?php echo $_SESSION["telefono_prov"];?>"/>
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">Email</div>
           		<input type="text" id="email" name="email" class="BuscadorInput" value="<?php echo $_SESSION["email_prov"];?>"/>
            </td>
            <td>
            	<div class="Label">Ciudad</div>
           		<input type="text" id="ciudad" name="ciudad" class="BuscadorInput" value="<?php echo $_SESSION["ciudad_prov"];?>"/>
            </td>
            <td>
            	<div class="Label">Pais</div>
           		<input type="text" id="pais" name="pais" class="BuscadorInput" value="<?php echo $_SESSION["pais_prov"];?>"/>
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">Forma de pago</div>
          		<select id="forma_pago" name="forma_pago" class="BuscadorInput">
            		<option value="0">Todas</option>
                	<option value="1"<?php if($_SESSION["forma_pago_prov"] == 1) { echo ' selected="selected"'; } ?>>Transferencia bancaria</option>
                	<option value="2"<?php if($_SESSION["forma_pago_prov"] == 2) { echo ' selected="selected"'; } ?>>Tarjeta de crédito/débito</option>
                	<option value="3"<?php if($_SESSION["forma_pago_prov"] == 3) { echo ' selected="selected"'; } ?>>PayPal</option>
                	<option value="4"<?php if($_SESSION["forma_pago_prov"] == 4) { echo ' selected="selected"'; } ?>>Recibo domiciliado</option>
            	</select>
            </td>
            <td>
            	<div class="Label">M&eacute;todo de pago</div>
          		<select id="metodo_pago" name="metodo_pago" class="BuscadorInput">
            		<option value="0">Todos</option>
                	<option value="1"<?php if($_SESSION["metodo_pago_prov"] == 1) { echo ' selected="selected"'; } ?>>Pago previo</option>
                	<option value="2"<?php if($_SESSION["metodo_pago_prov"] == 2) { echo ' selected="selected"'; } ?>>30 días</option>
                	<option value="3"<?php if($_SESSION["metodo_pago_prov"] == 3) { echo ' selected="selected"'; } ?>>60 días</option>
                	<option value="4"<?php if($_SESSION["metodo_pago_prov"] == 4) { echo ' selected="selected"'; } ?>>90 días</option>
            	</select>
            </td>
            <td>
            	<div class="Label">Tiempo de suministro</div>
           		<select id="tiempo_suministro" name="tiempo_suministro" class="BuscadorInput">	
            		<option value="0">Todos</option>
                	<option value="1"<?php if($_SESSION["tiempo_suministro_prov"] == 1) { echo ' selected="selected"'; } ?>>7 días</option>
                	<option value="2"<?php if($_SESSION["tiempo_suministro_prov"] == 2) { echo ' selected="selected"'; } ?>>14 días</option>
                	<option value="3"<?php if($_SESSION["tiempo_suministro_prov"] == 3) { echo ' selected="selected"'; } ?>>30 días</option>
                	<option value="4"<?php if($_SESSION["tiempo_suministro_prov"] == 4) { echo ' selected="selected"'; } ?>>60 días</option>
                	<option value="5"<?php if($_SESSION["tiempo_suministro_prov"] == 5) { echo ' selected="selected"'; } ?>>90 días</option>
            	</select>
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">Provincia</div>
           		<input type="text" id="provincia" name="provincia" class="BuscadorInput" value="<?php echo $_SESSION["provincia_prov"];?>"/>
            </td>
            <td>
            	<div class="Label">Código Postal</div>
           		<input type="text" id="codigo_postal" name="codigo_postal" class="BuscadorInput" value="<?php echo $_SESSION["codigo_postal_prov"];?>"/>
            </td>
            <td>
            	<div class="Label">Contacto</div>
           		<input type="text" id="persona_contacto" name="persona_contacto" class="BuscadorInput" value="<?php echo $_SESSION["persona_contacto_prov"];?>"/>
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">Fecha desde</div>
           		<input type="text" name="fecha_desde" id="datepicker_proveedores_desde" class="fechaCal" value="<?php echo $_SESSION["fecha_desde_prov"];?>"/>
            </td>
            <td>
            	<div class="Label">Fecha hasta</div>
           		<input type="text" name="fecha_hasta" id="datepicker_proveedores_hasta" class="fechaCal" value="<?php echo $_SESSION["fecha_hasta_prov"];?>"/>
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
			if($_GET["prov"] == "creado") {
				echo '<div class="mensaje">El proveedor se ha creado correctamente</div>';
			}
			if($_GET["prov"] == "modificado") {
				echo '<div class="mensaje">El proveedor se ha modificado correctamente</div>';
			}
			if($_GET["prov"] == "eliminado") {
				echo '<div class="mensaje">El proveedor se ha eliminado correctamente</div>';
			}	
			if($mostrar_tabla){
				if($num_resultados == NULL or $num_resultados == 0){
	               echo '<div class="mensaje">No se encontraron proveedores</div>';
	               $mostrar_tabla = false;
	            }
	            else if ($num_resultados == 1){
	            	echo '<div class="mensaje">Se encontró 1 proveedor</div>';
	            }
	            else{
	            	echo '<div class="mensaje">Se encontraron '.$num_resultados.' proveedores</div>';
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
                    	if (permisoMenu(4)){
                    ?>
                    		<th style="text-align:center">ELIMINAR</th>
                    <?php
                    	}
                    ?>
				</tr>
                <?php
					// Se cargan los datos de los proveedores según su identificador
					for($i=0;$i<count($resultadosBusqueda);$i++) {
						$prov = new Proveedor();
						$datoProveedor = $resultadosBusqueda[$i];
						$prov->cargaDatosProveedorId($datoProveedor["id_proveedor"]);
				?>
				<tr>
					<td>
						<a href="mod_proveedor.php?id=<?php echo $prov->id_proveedor;?>"><?php echo $prov->nombre; ?></a>    
					</td>
					<td><?php echo $prov->direccion;?></td>
                    <td><?php echo $prov->codigo_postal;?></td>
					<td><?php echo $prov->telefono;?></td>
					<td><?php echo $prov->email;?></td>
					<td><?php echo $prov->ciudad;?></td>
                    <td><?php echo $prov->provincia;?></td>
					<td><?php echo $prov->pais;?></td>
					<td>
						<?php 
							switch($prov->forma_pago) {
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
							switch($prov->metodo_pago) {
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
							switch($prov->tiempo_suministro) {
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
                    <td><?php echo $prov->persona_contacto;?></td>
    				<?php 
    					if (permisoMenu(4)){
    				?>
		                    <td>
		                       	<input type="button" id="menos" name="menos" value="ELIMINAR" class="BotonEliminar" onclick="javascript: if (confirm('¿Desea eliminar el proveedor?')) { window.location.href='elim_proveedor.php?id=<?php echo $prov->id_proveedor;?>' } else { void('') };" /> 
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
	                    <a href="proveedores.php?pg=1&realizandoBusqueda=1&proveedor=<?php echo $_SESSION["proveedor_prov"];?>&direccion=<?php echo $_SESSION["direccion_prov"];?>&telefono=<?php echo $_SESSION["telefono_prov"];?>&email=<?php echo $_SESSION["email_prov"];?>&ciudad=<?php echo $_SESSION["ciudad_prov"];?>&pais=<?php echo $_SESSION["pais_prov"];?>&forma_pago=<?php echo $_SESSION["forma_pago_prov"];?>&metodo_pago=<?php echo $_SESSION["metodo_pago_prov"];?>&tiempo_suministro=<?php echo $_SESSION["tiempo_suministro_prov"];?>&provincia=<?php echo $_SESSION["provincia_prov"];?>&codigo_postal=<?php echo $_SESSION["codigo_postal_prov"];?>&persona_contacto=<?php echo $_SESSION["persona_contacto_prov"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_prov"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_prov"];?>">Primera&nbsp&nbsp&nbsp</a>
	                    <a href="proveedores.php?pg=<?php echo $pg_pagina - 1;?>&realizandoBusqueda=1&proveedor=<?php echo $_SESSION["proveedor_prov"];?>&direccion=<?php echo $_SESSION["direccion_prov"];?>&telefono=<?php echo $_SESSION["telefono_prov"];?>&email=<?php echo $_SESSION["email_prov"];?>&ciudad=<?php echo $_SESSION["ciudad_prov"];?>&pais=<?php echo $_SESSION["pais_prov"];?>&forma_pago=<?php echo $_SESSION["forma_pago_prov"];?>&metodo_pago=<?php echo $_SESSION["metodo_pago_prov"];?>&tiempo_suministro=<?php echo $_SESSION["tiempo_suministro_prov"];?>&provincia=<?php echo $_SESSION["provincia_prov"];?>&codigo_postal=<?php echo $_SESSION["codigo_postal_prov"];?>&persona_contacto=<?php echo $_SESSION["persona_contacto_prov"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_prov"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_prov"];?>"> Anterior</a>
	            <?php  
	                }  
	                else {
	                    echo 'Primera&nbsp;&nbsp;&nbsp;Anterior';
	                }
	        
	                echo ' &nbsp;&nbsp;&nbsp;['.$pg_pagina.' / '.$pg_totalPaginas.']&nbsp;&nbsp;&nbsp;';
	                if($pg_pagina < $pg_totalPaginas) { ?>
	                    <a href="proveedores.php?pg=<?php echo $pg_pagina + 1;?>&realizandoBusqueda=1&proveedor=<?php echo $_SESSION["proveedor_prov"];?>&direccion=<?php echo $_SESSION["direccion_prov"];?>&telefono=<?php echo $_SESSION["telefono_prov"];?>&email=<?php echo $_SESSION["email_prov"];?>&ciudad=<?php echo $_SESSION["ciudad_prov"];?>&pais=<?php echo $_SESSION["pais_prov"];?>&forma_pago=<?php echo $_SESSION["forma_pago_prov"];?>&metodo_pago=<?php echo $_SESSION["metodo_pago_prov"];?>&tiempo_suministro=<?php echo $_SESSION["tiempo_suministro_prov"];?>&provincia=<?php echo $_SESSION["provincia_prov"];?>&codigo_postal=<?php echo $_SESSION["codigo_postal_prov"];?>&persona_contacto=<?php echo $_SESSION["persona_contacto_prov"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_prov"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_prov"];?>">Siguiente&nbsp&nbsp&nbsp</a>
	                    <a href="proveedores.php?pg=<?php echo $pg_totalPaginas; ?>&realizandoBusqueda=1&proveedor=<?php echo $_SESSION["proveedor_prov"];?>&direccion=<?php echo $_SESSION["direccion_prov"];?>&telefono=<?php echo $_SESSION["telefono_prov"];?>&email=<?php echo $_SESSION["email_prov"];?>&ciudad=<?php echo $_SESSION["ciudad_prov"];?>&pais=<?php echo $_SESSION["pais_prov"];?>&forma_pago=<?php echo $_SESSION["forma_pago_prov"];?>&metodo_pago=<?php echo $_SESSION["metodo_pago_prov"];?>&tiempo_suministro=<?php echo $_SESSION["tiempo_suministro_prov"];?>&provincia=<?php echo $_SESSION["provincia_prov"];?>&codigo_postal=<?php echo $_SESSION["codigo_postal_prov"];?>&persona_contacto=<?php echo $_SESSION["persona_contacto_prov"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_prov"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_prov"];?>">Última</a>
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
<?php include ('../includes/footer.php');?>
	 