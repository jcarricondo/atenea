<?php
// Este fichero muestra el listado de los albaranes de informática
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/usuario.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/material_informatico/albaran_informatico.class.php");
include("../classes/material_informatico/listado_albaranes_informaticos.class.php");
include("../classes/kint/Kint.class.php");
permiso(38);

$funciones = new Funciones();
$usuario = new Usuario();
$almacen = new Almacen();
$albaranInformatico = new AlbaranInformatico();
$listadoAlbaranesInformaticos = new ListadoAlbaranesInformaticos();

// ALMACEN OFICINA SIMUMAK
$id_almacen = 22;

if(isset($_GET["cerrarAlbaran"]) and $_GET["cerrarAlbaran"] == 1) {
    $id_albaran = $_GET["id_albaran"];    
    $mostrar_tabla = true;
    $buscar = 1;   

    // Comprobamos si el albarán que se cerró estaba vacío
    $vacio = $_GET["vacio"];
    if($vacio == 1){
        $resultado = $albaranInformatico->desactivarAlbaran($id_albaran);
        if($resultado != 1){
            $mensaje_error = $albaranInformatico->getErrorMessage($resultado);
        }
    }
}

// Se obtienen los datos del formulario
if((isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1) or $buscar == 1) {
	$mostrar_tabla = true;
	$nombre_albaran = addslashes($_GET["nombre_albaran"]);
	$tipo_albaran = $_GET["tipo_albaran"];
	$origen_destino = addslashes($_GET["origen_destino"]);
	$id_usuario = $_GET["id_usuario"];
    $fecha_creacion = $_GET["fecha_creacion"];
    $num_serie = addslashes($_GET["num_serie"]);
    $motivo = $_GET["motivo"];

    // Preparamos la fecha para la consulta
    if($fecha_creacion != "") {
        $fecha_creacion_spa = $funciones->cFechaMy($fecha_creacion);
        $date = new DateTime($fecha_creacion_spa);
        $fecha_creacion_spa = $date->format('Y-m-d H:i:s');
    }

    $listadoAlbaranesInformaticos->setValores($nombre_albaran,$tipo_albaran,$origen_destino,$id_usuario,$num_serie,$motivo,$fecha_creacion_spa);
    $listadoAlbaranesInformaticos->realizarConsulta();
    $resultadosBusqueda = $listadoAlbaranesInformaticos->albaranes;
    $num_resultados = count($resultadosBusqueda); 

	// Guardar las variables del formulario en variable de sesión
	$_SESSION["nombre_albaran_albaran_informatico"] = stripslashes(htmlspecialchars($nombre_albaran));
	$_SESSION["tipo_albaran_albaran_informatico"] = $tipo_albaran;
	$_SESSION["origen_destino_albaran_informatico"] = stripslashes(htmlspecialchars($origen_destino));
	$_SESSION["id_usuario_albaran_informatico"] = $id_usuario;
    $_SESSION["fecha_creacion_albaran_informatico"] = $fecha_creacion;
    $_SESSION["num_serie_albaran_informatico"] = stripslashes(htmlspecialchars($num_serie)); 
    $_SESSION["tipo_motivo_albaran_informatico"] = $motivo;
}
$titulo_pagina = "Material Informático > Albaranes";
$pagina = "listado_albaranes";
include("../includes/header.php");
?>

<div class="separador"></div> 
<?php include("../includes/menu_material_informatico.php"); ?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include("../includes/sidebar.php"); ?>
    </div>
       	
    <h3>Albaranes</h3>
    <h4></h4>

    <form id="BuscadorAlbaran" name="buscadorAlbaran" action="albaranes_informatica.php" method="get" class="Buscador">
    <table style="border:0;">
    <tr style="border:0;">
        <td>
            <div class="Label">Albarán</div>
            <input type="text" id="nombre_albaran" name="nombre_albaran" class="BuscadorInput" value="<?php echo $_SESSION["nombre_albaran_albaran_informatico"];?>"/>
        </td>
        <td>
            <div class="Label">Tipo</div>
            <select id="tipo_albaran" name="tipo_albaran"  class="BuscadorInput">
                <option></option>
                <option <?php if($_SESSION["tipo_albaran_albaran_informatico"] == "ENTRADA"){ ?> selected="selected" <?php } ?> value="ENTRADA">ENTRADA</option>
                <option <?php if($_SESSION["tipo_albaran_albaran_informatico"] == "SALIDA"){ ?> selected="selected" <?php } ?> value="SALIDA">SALIDA</option>
            </select>
        </td>
        <td>
            <div class="Label">Origen / Destino</div>
            <input type="text" id="origen_destino" name="origen_destino" class="BuscadorInput" value="<?php echo $_SESSION["origen_destino_albaran_informatico"];?>"/>
        </td>
    </tr>
    <tr style="border:0;">
        <td>
            <div class="Label">Usuario</div>
            <select id="id_usuario" name="id_usuario"  class="BuscadorInput">
                <option></option>
                <?php 
                    // Obtenemos todos los usuarios administradores
                    $resultado_usuarios = $usuario->dameUsuariosAdminGlobales();
                    for($i=0;$i<count($resultado_usuarios);$i++){
                        $id_usuario = $resultado_usuarios[$i]["id_usuario"];
                        $nombre_usuario = $resultado_usuarios[$i]["usuario"]; ?> 
                        <option value=<?php echo $id_usuario; if($_SESSION["id_usuario_albaran_informatico"] == $id_usuario) { ?> selected="selected" <?php } ?>><?php echo $nombre_usuario; ?></option>   
                <?php 
                    }
                ?>
            </select>
        </td>
        <td>
            <div class="Label">Fecha creación</div>
            <input type="text" name="fecha_creacion" id="datepicker_albaranes_informaticos_creacion" class="fechaCal" value="<?php echo $_SESSION["fecha_creacion_albaran_informatico"];?>"/>
        </td>
        <td>
            <div class="Label">Num. Serie</div>
            <input type="text" name="num_serie" id="num_serie" class="BuscadorInput" value="<?php echo $_SESSION["num_serie_albaran_informatico"];?>"/>
        </td>
    </tr>
    <tr style="border:0;">
        <td>
            <div class="Label">Motivo</div>
            <select id="motivo" name="motivo"  class="BuscadorInput">
                <option></option>
                <?php
                    $array_tipos_motivo = array("COMPRA / SUMINISTRO","PENDIENTE REPARACION","MATERIAL REPARADO","SERVICIO REPARACION","MATERIAL ASIGNADO");
                    for($i=0;$i<count($array_tipos_motivo);$i++){ ?>
                        <option value=<?php echo $array_tipos_motivo[$i]; if($array_tipos_motivo[$i] == $_SESSION["tipo_motivo_albaran_informatico"]){ ?> 
                                selected="selected" <?php } ?>><?php echo $array_tipos_motivo[$i];?></option>
                <?php 
                    }
                ?>
            </select>
        </td>
        <td style="text-align:center;">
           	<input type="hidden" id="realizandoBusqueda" name="realizandoBusqueda" value="1" />
            <input type="submit" id="botonEnviar" name="botonEnviar" value="Buscar" />
        </td>
        <td>
            
        </td>
    </tr>
    </table>
    <br />
    </form>
        
    <div class="ContenedorBotonCrear">
    <?php
        if($_GET["cerrarAlbaran"] == 1) {
            if($vacio == 0){
                echo '<div class="mensaje">El albarán se ha creado correctamente</div>';
            }
            else {
                echo '<div class="mensaje">El albarán se ha creado correctamente<br/><span style="color:red;">'.$mensaje_error.'</span></div>';   
            }    
        }
        if($mostrar_tabla){
            if($num_resultados == NULL or $num_resultados == 0){
                echo '<div class="mensaje">No se encontraron albaranes</div>';
                $mostrar_tabla = false;
            }
            else if ($num_resultados == 1){
                echo '<div class="mensaje">Se encontró 1 albarán</div>';
            }
            else{
                echo '<div class="mensaje">Se encontraron '.$num_resultados.' albaranes</div>';
            }   
        }          
    ?>    
    </div>
    <?php
		if($mostrar_tabla) { ?>
			<div class="CapaTabla">
				<table>
    			<tr>
    			 	<th>NOMBRE ALBARAN</th>
    				<th>TIPO ALBARAN</th>
                    <th>USUARIO</th>
                    <th>ALMACEN</th>
    			    <th>ORIGEN / DESTINO</th>
    				<th>MOTIVO</th>
                    <th>OBSERVACIONES</th>
                    <th style="text-align:center">FECHA CREACION</th>
                    <th style="text-align:center"></th>
    		    </tr>
                <?php
        			// Se cargan los datos de los albaranes según su identificador
        			for($i=0;$i<count($resultadosBusqueda);$i++) {
                        $id_albaran = $resultadosBusqueda[$i]["id_albaran"];
                        $albaranInformatico->cargaDatosAlbaranId($id_albaran);

                        $nombre_albaran = $albaranInformatico->nombre_albaran;
                        $tipo_albaran = $albaranInformatico->tipo_albaran;
                        $origen_destino = $albaranInformatico->origen_destino;
                        $id_usuario = $albaranInformatico->id_usuario;
                        $motivo = $albaranInformatico->motivo;
                        $id_almacen = $albaranInformatico->id_almacen;
                        $observaciones = $albaranInformatico->observaciones;

                        // Cargamos el nombre del usuario
                        $usuario->cargaDatosUsuarioId($id_usuario);
                        $nombre_usuario = $usuario->usuario;

                        // Cargamos el nombre del almacen
                        $almacen->cargaDatosAlmacenId($id_almacen);
                        $nombre_almacen = $almacen->nombre;

                        // Convertimos la fecha 
                        $fecha_creacion = $albaranInformatico->fecha_creado;
                        $fecha_creacion = $usuario->fechaHoraSpain($fecha_creacion);

                        if(empty($origen)) $origen = "-";
                        if(empty($observaciones)) $observaciones = "-";
    			?>
    				<tr>
    					<td><?php echo $nombre_albaran;?></td>
    					<td><?php echo $tipo_albaran;?></td>
                        <td><?php echo $nombre_usuario;?></td>
                        <td><?php echo $nombre_almacen;?></td>
    					<td><?php echo $origen_destino;?></td>
                        <td><?php echo $motivo;?></td>
                        <td><?php echo $observaciones;?></td>
                        <td style="text-align:center;"><?php echo $fecha_creacion;?></td>
    					<td style="text-align:center;">
                            <a href="../material_informatico/informe_albaran.php?id_albaran=<?php echo $id_albaran;?>">XLS</a>
                        </td> 
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
    