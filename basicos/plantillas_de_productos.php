<?php
// Este fichero muestra el listado de las plantillas de productos
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/plantilla_producto.class.php");
include("../classes/basicos/nombre_producto.class.php");
include("../classes/basicos/listado_plantilla_producto.class.php");
include("../classes/basicos/listado_nombre_producto.class.php");
permiso(1);

$funciones = new Funciones();
$plant = new Plantilla_Producto();
$np = new Nombre_Producto();
$listado_pl = new ListadoPlantillaProducto();
$listado_np = new ListadoNombreProducto();

// Se obtienen los datos del formulario
if($_GET["plantilla"] == "creado" or $_GET["plantilla"] == "modificado" or $_GET["plantilla"] == "eliminado") {
	$realizarBusqueda = 1;
}
if(isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) {
	$mostrar_tabla = true;
	$nombre = addslashes($_GET["nombre"]);
	$version = addslashes($_GET["version"]);
	$id_nombre_producto = $_GET["select_nombre_producto"];
	$fecha_desde = $_GET["fecha_desde"];
	$fecha_hasta = $_GET["fecha_hasta"];
		
	// Convierte la fecha en formato MySQL
	if($fecha_desde != "") $fecha_desde = $funciones->cFechaMy($fecha_desde);
	if($fecha_hasta != "") $fecha_hasta = $funciones->cFechaMy($fecha_hasta);

	$listado_pl->setValores($nombre,$version,$id_nombre_producto,$fecha_desde,$fecha_hasta);
	$listado_pl->realizarConsulta();
	$resultadosBusqueda = $listado_pl->plantillas;
    $num_resultados = count($resultadosBusqueda);
	
	// Convierte la fecha en formato HTML
	if($fecha_desde != "") $fecha_desde = $funciones->cFechaNormal($fecha_desde);
	if($fecha_desde != "") $fecha_hasta = $funciones->cFechaNormal($fecha_hasta);
	
	// Guardar las variables del formulario en variable de sesion
	$_SESSION["nombre_plantilla_producto"] = stripslashes(htmlspecialchars($nombre));
	$_SESSION["version_plantilla_producto"] = stripslashes(htmlspecialchars($version));
    $_SESSION["id_np_plantilla_producto"] = $id_nombre_producto;
	$_SESSION["fecha_desde_plantilla_producto"] = $fecha_desde;
	$_SESSION["fecha_hasta_plantilla_producto"] = $fecha_hasta;
}

$titulo_pagina = "Básicos > Plantillas de productos";
$pagina = "plantillas_de_productos";
include ("../includes/header.php");
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>
    
   	<h3>Plantillas de productos</h3>
    <h4>Buscar plantilla de producto</h4>
    
    <form id="BuscadorPlantillaProducto" name="BuscadorPlantillaProducto" action="plantillas_de_productos.php" method="get" class="Buscador">
        <table style="border:0;">
            <tr style="border:0;">
                <td>
                    <div class="Label">Nombre</div>
                    <input type="text" name="nombre" class="BuscadorInput" value="<?php echo $_SESSION["nombre_plantilla_producto"];?>"/>
                </td>
                <td>
                    <div class="Label">Versi&oacute;n</div>
                    <input type="text" name="version" class="BuscadorInput" value="<?php echo $_SESSION["version_plantilla_producto"];?>"/>
                </td>
                <td>
                    <div class="Label">Nombre Producto</div>
                    <select id="select_nombre_producto" name="select_nombre_producto" class="BuscadorInput">
                        <option></option>
                        <?php
                            $listado_np->prepararConsulta();
                            $listado_np->realizarConsulta();
                            $resultado_nombres = $listado_np->nombre_productos;

                            for($i=0;$i<count($resultado_nombres);$i++){
                                $datoNombre = $resultado_nombres[$i];
                                $np->cargaDatosNombreProductoId($datoNombre["id_nombre_producto"]);
                                echo '<option value="'.$np->id_nombre_producto.'"';
                                if($np->id_nombre_producto == $_SESSION["id_np_plantilla_producto"]) echo ' selected="selected"';
                                echo '>'.$np->nombre.'</option>';
                            }
                        ?>
                    </select>
                </td>
            </tr>
            <tr style="border:0;">
                <td>
                    <div class="Label">Fecha desde</div>
                    <input type="text" name="fecha_desde" id="datepicker_plantilla_desde" class="fechaCal" value="<?php echo $_SESSION["fecha_desde_plantilla_producto"];?>"/>
                </td>
                <td>
                    <div class="Label">Fecha hasta</div>
                    <input type="text" name="fecha_hasta" id="datepicker_plantilla_hasta" class="fechaCal" value="<?php echo $_SESSION["fecha_hasta_plantilla_producto"];?>"/>
                </td>
                <td>
                    <input type="hidden" id="realizandoBusqueda" name="realizandoBusqueda" value="1" />
                    <input type="submit" id="" name="" class="" value="Buscar" />
                </td>
            </tr>
        </table>
        <br />
    </form>
    
    <div class="ContenedorBotonCrear">
  		<?php
    		if($_GET["plantilla"] == "creado") {
    			echo '<div class="mensaje">La plantilla se ha creado correctamente</div>';
    		}
    		if($_GET["plantilla"] == "modificado") {
    			echo '<div class="mensaje">La plantilla se ha modificado correctamente</div>';
    		}
    		if($_GET["plantilla"] == "eliminado") {
    			echo '<div class="mensaje">La plantilla se ha eliminado correctamente</div>';
    		}
            if($mostrar_tabla){
                if($num_resultados == NULL or $num_resultados == 0){
                   echo '<div class="mensaje">No se encontraron plantillas</div>';
                   $mostrar_tabla = false;
                }
                else if($num_resultados == 1){
                    echo '<div class="mensaje">Se encontró 1 plantilla</div>';
                }
                else{
                    echo '<div class="mensaje">Se encontraron '.$num_resultados.' plantillas</div>';
                }   
            }	
		?>
    </div>
    
    <?php
        if($mostrar_tabla){ ?>
    		<div class="CapaTabla">
    			<table>
                    <tr>
                        <th>PLANTILLA</th>
                        <th style="text-align: center;">VERSI&Oacute;N</th>
                        <th>VINCULADA A</th>
                        <th style="text-align: center;">COMPONENTES</th>
                        <th style="text-align: center;">FECHA</th>
                        <?php
                            if(permisoMenu(4)){ ?>
                                <th style="text-align:center">ELIMINAR</th>
                        <?php
                            }
                        ?>
                    </tr>
                    <?php
                        for($i=0;$i<count($resultadosBusqueda);$i++) {
                            // Se cargan los datos de las plantillas de productos según su identificador
                            $datoPlantillaProducto = $resultadosBusqueda[$i];
                            $plant->cargaDatosPlantillaProductoId($datoPlantillaProducto["id_plantilla"]);
                            $fecha_creado = $funciones->fechaHoraSpain($plant->fecha_creado);
                            $id_np = $plant->id_nombre_producto;
                            $np->cargaDatosNombreProductoId($id_np); ?>
                            <tr>
                                <td>
                                    <a href="mod_plantilla_producto.php?id=<?php echo $plant->id_plantilla; ?>"><?php echo $plant->nombre;?></a>
                                </td>
                                <td style="text-align: center;"><?php echo number_format($plant->version,1,'.',',');?></td>
                                <td><?php echo strtoupper($np->nombre);?></td>
                                <td style="text-align: center;">
                                    <a href="javascript:abrir('muestra_componentes_plantilla.php?id_plantilla=<?php echo $plant->id_plantilla;?>')">VER</a> -
                                    <a href="javascript:window.location='informe_referencias_plantilla.php?id_plantilla=<?php echo $plant->id_plantilla;?>';">XLS</a> -
                                    <a href="javascript:window.location='informe_referencias_componentes.php?id_plantilla=<?php echo $plant->id_plantilla;?>';">XLS COM</a>
                                </td>
                                <td style="text-align:center;">
                                    <?php echo $fecha_creado; ?>
                                </td>
                                <?php
                                    if(permisoMenu(4)){ ?>
                                        <td style="text-align:center">
                                            <input type="button" id="menos" name="menos" value="ELIMINAR" class="BotonEliminar" onclick="javascript: if (confirm('¿Desea eliminar la plantilla de producto?')) { window.location.href='elim_plantilla_producto.php?id_plantilla=<?php echo $plant->id_plantilla;?>' } else { void('') };" />
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
<?php include ("../includes/footer.php");?>
