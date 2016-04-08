<?php
// Este fichero muestra el listado de los clientes
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/cliente.class.php");
include("../classes/basicos/listado_clientes.class.php");
permiso(1);

// Se obtienen los datos del formulario
if($_GET["client"] == "creado" or $_GET["client"] == "modificado" or $_GET ["client"] == "eliminado") {
	$realizarBusqueda = 1;
}
if(isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) {
	$mostrar_tabla = true;
	$cliente = addslashes($_GET["cliente"]);
	$telefono = addslashes($_GET["telefono"]);
	$email = addslashes($_GET["email"]);
	$direccion = addslashes($_GET["direccion"]);
	$codigo_postal = addslashes($_GET["codigo_postal"]);
	$ciudad = addslashes($_GET["ciudad"]);
	$pais = addslashes($_GET["pais"]);
	$fecha_alta = $_GET["fecha_alta"];
	$fecha_desde = $_GET["fecha_desde"];
	$fecha_hasta = $_GET["fecha_hasta"];
	
	$funciones = new Funciones();
	// Convierte la fecha a formato MySql
	if ($fecha_desde != "") $fecha_desde = $funciones->cFechaMy($fecha_desde);
	if ($fecha_hasta != "") $fecha_hasta = $funciones->cFechaMy($fecha_hasta);	
	
	$clientes = new listadoClientes();
	// Se pasan los datos del buscador a la clase del listado y se realiza la consulta a la base de datos
	$clientes->setValores($cliente,$telefono,$email,$direccion,$codigo_postal,$ciudad,$pais,$fecha_alta,$fecha_desde,$fecha_hasta);
	$clientes->realizarConsulta();
	$resultadosBusqueda = $clientes->clientes;
    $num_resultados = count($resultadosBusqueda);
	
	// Convierte la fecha a formato HTML
	if ($fecha_desde != "") $fecha_desde = $funciones->cFechaNormal($fecha_desde);
	if ($fecha_hasta != "") $fecha_hasta = $funciones->cFechaNormal($fecha_hasta);	
	
	// Guardar las variables del formulario en variable de sesion
	$_SESSION["cliente_cliente"] = stripslashes(htmlspecialchars($cliente));
	$_SESSION["telefono_cliente"] = stripslashes(htmlspecialchars($telefono));
	$_SESSION["direccion_cliente"] = stripslashes(htmlspecialchars($direccion));
	$_SESSION["email_cliente"] = stripslashes(htmlspecialchars($email));
	$_SESSION["ciudad_cliente"] = stripslashes(htmlspecialchars($ciudad));
	$_SESSION["pais_cliente"] = stripslashes(htmlspecialchars($pais));
	$_SESSION["codigo_postal_cliente"] = stripslashes(htmlspecialchars($codigo_postal)); 
	$_SESSION["fecha_alta_cliente"] = $fecha_alta;
	$_SESSION["fecha_desde_cliente"] = $fecha_desde;
	$_SESSION["fecha_hasta_cliente"] = $fecha_hasta;
}

$titulo_pagina = "Básicos > Clientes";
$pagina = "clientes";
include ("../includes/header.php");
?>	

<div class="separador"></div>
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
 	</div>
    
	<h3>Clientes</h3>
    <h4>Buscar cliente</h4>
    
    <form id="BuscadorCliente" name="buscadorCliente" action="clientes.php" method="get" class="Buscador">
    <table style="border:0;">
    	<tr style="border:0;">
        	<td>
            	<div class="Label">Nombre</div>
                <input type="text" id="" name="cliente" class="BuscadorInput" value="<?php echo $_SESSION["cliente_cliente"]; ?>" />
            </td>
            <td>
            	<div class="Label">Tel&eacute;fono</div>
                <input type="text" id="" name="telefono" class="BuscadorInput" value="<?php echo $_SESSION["telefono_cliente"]; ?>" />
            </td>
            <td>
            	<div class="Label">Email</div>
                <input type="text" id="" name="email" class="BuscadorInput" value="<?php echo $_SESSION["email_cliente"]; ?>" />
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">Direcci&oacute;n</div>
                <input type="text" id="" name="direccion" class="BuscadorInput" value="<?php echo $_SESSION["direccion_cliente"]; ?>" />
            </td>
            <td>
            	<div class="Label">C&oacute;digo Postal</div>
                <input type="text" id="" name="codigo_postal" class="BuscadorInput" value="<?php echo $_SESSION["codigo_postal_cliente"]; ?>" />
            </td>
            <td>
            	<div class="Label">Ciudad</div>
                <input type="text" id="" name="ciudad" class="BuscadorInput" value="<?php echo $_SESSION["ciudad_cliente"]; ?>" />
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">Pa&Iacute;s</div>
                <input type="text" id="" name="pais" class="BuscadorInput" value="<?php echo $_SESSION["pais_cliente"]; ?>" />
            </td>
            <td>
                <div class="Label">Fecha desde</div>
           		<input type="text" name="fecha_desde" id="datepicker_clientes_desde" class="fechaCal" value="<?php echo $_SESSION["fecha_desde_cliente"];?>"/>
            </td>
            <td>
            	<div class="Label">Fecha hasta</div>
           		<input type="text" name="fecha_hasta" id="datepicker_clientes_hasta" class="fechaCal" value="<?php echo $_SESSION["fecha_hasta_cliente"];?>"/>
        </tr>
        <tr style="border:0;">
        	<td colspan="3">
                <input type="hidden" id="realizandoBusqueda" name="realizandoBusqueda" class="" value="1" />
                <input type="submit" id="" name="" class="" value="Buscar" />
            </td>
        </tr>
    </table>
    <br />
 	</form>
    
    <div class="ContenedorBotonCrear">
     	<?php
    		if($_GET["client"] == "creado") {
    			echo '<div class="mensaje">El cliente se ha creado correctamente</div>';
    		}
    		if($_GET["client"] == "modificado") {
    			echo '<div class="mensaje">El cliente se ha modificado correctamente</div>';
    		}
    		if($_GET["client"] == "eliminado") {
    			echo '<div class="mensaje">El cliente se ha eliminado correctamente</div>';
    		}		
            if($mostrar_tabla){
                if($num_resultados == NULL or $num_resultados == 0){
                   echo '<div class="mensaje">No se encontraron clientes</div>';
                   $mostrar_tabla = false;
                }
                else if ($num_resultados == 1){
                    echo '<div class="mensaje">Se encontró 1 cliente</div>';
                }
                else{
                    echo '<div class="mensaje">Se encontraron '.$num_resultados.' clientes</div>';
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
           		<th>TELEFONO</th>
           		<th>EMAIL</th>
           		<th>DIRECCI&Oacute;N</th>
           		<th>CP</th>
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
				// Se cargan los datos de los clientes según su identificador
				for($i=0;$i<count($resultadosBusqueda);$i++) {
					$client = new Cliente();
					$datoCliente = $resultadosBusqueda[$i];
					$client->cargaDatosClienteId($datoCliente["id_cliente"]);
					?>
					<tr>
						<td>
							<a href="mod_cliente.php?id=<?php echo $client->id_cliente;?>"><?php echo $client->nombre; ?></a>    
						</td>
						<td><?php echo $client->telefono; ?></td>
                        <td><?php echo $client->email; ?></td>
                        <td><?php echo $client->direccion; ?></td>
                        <td><?php echo $client->cp; ?></td>
                        <td><?php echo $client->ciudad; ?></td>
                        <td><?php echo $client->pais; ?></td>
                        <?php 
							if(permisoMenu(4)){
						?>
		                        <td style="text-align:center">
		                        	<input type="button" id="eliminar" name="eliminar" value="ELIMINAR" class="BotonEliminar" onclick="javascript: if (confirm('¿Desea eliminar el cliente?')) { window.location.href='elim_cliente.php?id=<?php echo $client->id_cliente;?>' } else { void('') };" />
		                        </td>
		                <?php 
		                	}
		                ?>
					</tr> 
					<?php
				}
				?>
				</table>   
    <?php 
		}
	?>
    </div>               
</div>    
<?php include ("../includes/footer.php"); ?>