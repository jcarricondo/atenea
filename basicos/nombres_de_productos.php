<?php
// Este fichero muestra el listado de los nombre de productos
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/listado_nombre_producto.class.php");
include("../classes/basicos/nombre_producto.class.php");
permiso(1);

// Se obtienen los datos del formulario
if($_GET["producto"] == "creado" or $_GET["producto"] == "modificado" or $_GET["producto"] == "eliminado") {
	$realizarBusqueda = 1;
}
if(isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) {
	$mostrar_tabla = true;
	$nombre = addslashes($_GET["nombre"]);
	$codigo = addslashes($_GET["codigo"]);
	$version = addslashes($_GET["version"]);
	$familia = addslashes($_GET["familia"]);
	$fecha_desde = $_GET["fecha_desde"];
	$fecha_hasta = $_GET["fecha_hasta"];
		
	$funciones = new Funciones();
	// Convierte la fecha en formato MySQL
	if ($fecha_desde != "") $fecha_desde = $funciones->cFechaMy($fecha_desde);
	if ($fecha_hasta != "") $fecha_hasta = $funciones->cFechaMy($fecha_hasta);
	// Se carga la clase para la base de datos y el listado de nombre de productos
	$nombre_productos = new listadoNombreProducto();
	// Se pasan los datos del buscador a la clase del listado y se realiza la consulta a la base de datos
	$nombre_productos->setValores($nombre,$codigo,$version,$familia,$fecha_desde,$fecha_hasta);
	$nombre_productos->realizarConsulta();
	$resultadosBusqueda = $nombre_productos->nombre_productos;
    $num_resultados = count($resultadosBusqueda);
	
	// Convierte la fecha en formato HTML
	if ($fecha_desde != "") $fecha_desde = $funciones->cFechaNormal($fecha_desde);
	if ($fecha_desde != "") $fecha_hasta = $funciones->cFechaNormal($fecha_hasta);
	
	// Guardar las variables del formulario en variable de sesion
	$_SESSION["nombre_nombre_producto"] = stripslashes(htmlspecialchars($nombre));
	$_SESSION["codigo_nombre_producto"] = stripslashes(htmlspecialchars($codigo));
	$_SESSION["version_nombre_producto"] = stripslashes(htmlspecialchars($version));
	$_SESSION["familia_nombre_producto"] = stripslashes(htmlspecialchars($familia));
	$_SESSION["fecha_desde_nombre_producto"] = $fecha_desde;
	$_SESSION["fecha_hasta_nombre_producto"] = $fecha_hasta;
}

$titulo_pagina = "Básicos > Nombres de productos";
$pagina = "nombres_de_productos";
include ("../includes/header.php");
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>
    
   	<h3>Nombres de productos</h3>
    <h4>Buscar nombre de producto</h4>
    
    <form id="BuscadorNombreProducto" name="buscadorNombreProducto" action="nombres_de_productos.php" method="get" class="Buscador">
    <table style="border:0;">
    	<tr style="border:0;">
        	<td>
            	<div class="Label">Nombre</div>
           		<input type="text" id="" name="nombre" class="BuscadorInput" value="<?php echo $_SESSION["nombre_nombre_producto"];?>"/>
            </td>
            <td>
            	<div class="Label">Codigo</div>
           		<input type="text" id="" name="codigo" class="BuscadorInput" value="<?php echo $_SESSION["codigo_nombre_producto"];?>"/>
            </td>
            <td>
            	<div class="Label">Version</div>
           		<input type="text" id="" name="version" class="BuscadorInput" value="<?php echo $_SESSION["version_nombre_producto"];?>"/>
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
            	<div class="Label">Familia</div>
            	<input type="text" id="" name="familia" class="BuscadorInput" value="<?php echo $_SESSION["familia_nombre_producto"];?>"/>  
            </td>
           	<td>
            	<div class="Label">Fecha desde</div>
           		<input type="text" name="fecha_desde" id="datepicker_nombre_desde" class="fechaCal" value="<?php echo $_SESSION["fecha_desde_nombre_producto"];?>"/>
            </td>
            <td>
            	<div class="Label">Fecha hasta</div>
           		<input type="text" name="fecha_hasta" id="datepicker_nombre_hasta" class="fechaCal" value="<?php echo $_SESSION["fecha_hasta_nombre_producto"];?>"/>
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
    		if($_GET["producto"] == "creado") {
    			echo '<div class="mensaje">El producto se ha creado correctamente</div>';
    		}
    		if($_GET["producto"] == "modificado") {
    			echo '<div class="mensaje">El producto se ha modificado correctamente</div>';
    		}
    		if($_GET["producto"] == "eliminado") {
    			echo '<div class="mensaje">El producto se ha eliminado correctamente</div>';
    		}
            if($mostrar_tabla){
                if($num_resultados == NULL or $num_resultados == 0){
                   echo '<div class="mensaje">No se encontraron nombres</div>';
                   $mostrar_tabla = false;
                }
                else if ($num_resultados == 1){
                    echo '<div class="mensaje">Se encontró 1 nombre</div>';
                }
                else{
                    echo '<div class="mensaje">Se encontraron '.$num_resultados.' nombres</div>';
                }   
            }	
		?>
    </div>
    
    <?php if ($mostrar_tabla)
		{
	?>
    		<div class="CapaTabla">
    			<table>
        		<tr>
          			<th>NOMBRE DEL PRODUCTO</th>
            		<th>CODIGO</th>
            		<th style="text-align:center">VERSION</th>
                    <th>FAMILIA</th>
                    <?php
                    	if (permisoMenu(4)){
                    ?>
                    <th style="text-align:center">ELIMINAR</th>
                    <?php
                    	}
                    ?>
        		</tr>
        
        	<?php
				for($i=0;$i<count($resultadosBusqueda);$i++) {
					// Se cargan los datos de los nombre de productos según su identificador
					$nomProd = new Nombre_Producto();
					$datoNombreProducto = $resultadosBusqueda[$i];
					$nomProd->cargaDatosNombreProductoId($datoNombreProducto["id_nombre_producto"]);
					?>
					<tr>
						<td>
							<a href="mod_nombre_producto.php?id=<?php echo $nomProd->id_nombre_producto; ?>"><?php echo $nomProd->nombre;?></a>    
						</td>
						<td><?php echo $nomProd->codigo;?></td>
						<td style="text-align:center"><?php echo $nomProd->version;?></td>
                        <td><?php echo $nomProd->familia;?></td>
                        <?php 
                        	if(permisoMenu(4)){
                        ?>
                        <td style="text-align:center">
                            <input type="button" id="menos" name="menos" value="ELIMINAR" class="BotonEliminar" onclick="javascript: if (confirm('¿Desea eliminar el nombre de producto?')) { window.location.href='elim_nombre_producto.php?id=<?php echo $nomProd->id_nombre_producto;?>' } else { void('') };" /> 
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
<?php include ("../includes/footer.php");?>
