<?php
// Este fichero muestra el listado de los movimientos de los albaranes de informática 
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/usuario.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/material_informatico/albaran_informatico.class.php");
include("../classes/material_informatico/material_informatico.class.php");
include("../classes/material_informatico/listado_movimientos_informaticos.class.php");
permiso(38);

$titulo_pagina = "Material Informático > Listado Movimientos";
$pagina = "listado_movimientos";
include("../includes/header.php");
echo '<script type="text/javascript" src="../js/material_informatico/material_informatico.js"></script>';

$funciones = new Funciones();
$usuario = new Usuario();
$almacen = new Almacen();
$albaranInformatico = new AlbaranInformatico();
$materialInformatico = new MaterialInformatico();
$listadoMovimientos = new listadoMovimientosInformaticos();

// ALMACEN OFICINA SIMUMAK
$id_almacen = 22;

// Se obtienen los datos del formulario
if((isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1) or $buscar == 1) {
    $mostrar_tabla = true;
	$nombre_albaran = addslashes($_GET["nombre_albaran"]);
	$tipo_albaran = $_GET["tipo_albaran"];
	$origen_destino = addslashes($_GET["origen_destino"]);
    $id_usuario = $_GET["id_usuario"];
    $num_serie = addslashes($_GET["num_serie"]);
    $averiado = $_GET["averiado"];
    $tipo_material = $_GET["tipo_material"];
	$fecha_desde = $_GET["fecha_desde"];
    $fecha_hasta = $_GET["fecha_hasta"];
    $tipo_motivo = $_GET["tipo_motivo"];

	if($fecha_desde != ""){
        $fecha_desde = $funciones->cFechaMy($fecha_desde);
        $date = new DateTime($fecha_desde);
        $fecha_desde = $date->format('Y-m-d H:i:s');
    }
    if($fecha_hasta != ""){
        $fecha_hasta = $funciones->cFechaMy($fecha_hasta);
        $date = new DateTime($fecha_hasta);
        $fecha_hasta = $date->format('Y-m-d H:i:s');    
    }
    
    $listadoMovimientos->setValores($nombre_albaran,$tipo_albaran,$origen_destino,$id_usuario,$num_serie,$averiado,$tipo_material,$tipo_motivo,$fecha_desde,$fecha_hasta);
    $listadoMovimientos->realizarConsulta();
    $resultadosBusqueda = $listadoMovimientos->movimientos;
    $num_resultados = count($resultadosBusqueda);

    // Guardamos en un campo oculto los id de los movimientos de los materiales
    for($i=0;$i<count($resultadosBusqueda);$i++){
        $ids_movimientos_materiales[$i] = $resultadosBusqueda[$i]["id"];
        echo '<input type="hidden" id="ids_movimientos_materiales[]" name="ids_movimientos_materiales[]" value="'.$ids_movimientos_materiales[$i].'"/>';
    }

    // Guardar las variables del formulario en variable de sesión
	$_SESSION["nombre_albaran_material_movimientos"] = stripslashes(htmlspecialchars($nombre_albaran));
	$_SESSION["tipo_albaran_material_movimientos"] = $tipo_albaran;
	$_SESSION["origen_destino_material_movimientos"] = stripslashes(htmlspecialchars($origen_destino));
	$_SESSION["id_usuario_material_movimientos"] = $id_usuario;
    $_SESSION["num_serie_material_movimientos"] = stripslashes(htmlspecialchars($num_serie));
    $_SESSION["averiado_material_movimientos"] = $averiado;
    $_SESSION["tipo_material_material_movimientos"] = $tipo_material;
    $_SESSION["tipo_motivo_material_movimientos"] = $tipo_motivo;  
    $_SESSION["fecha_desde_material_movimientos"] = $fecha_desde;
    $_SESSION["fecha_hasta_material_movimientos"] = $fecha_hasta;
}
?>

<div class="separador"></div> 
<?php include("../includes/menu_material_informatico.php"); ?> 

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include("../includes/sidebar.php"); ?>
    </div>
       	
    <h3>Listado Movimientos</h3>
    <h4>Buscar movimiento</h4>

    <form id="BuscadorMovimiento" name="buscadorMovimiento" action="listado_movimientos.php" method="get" class="Buscador">
    <table style="border:0;">
    <tr style="border:0;">
        <td>
            <div class="Label">Albarán</div>
            <input type="text" id="nombre_albaran" name="nombre_albaran" class="BuscadorInput" value="<?php echo $_SESSION["nombre_albaran_material_movimientos"];?>"/>
        </td>
        <td>
            <div class="Label">Tipo</div>
            <select id="tipo_albaran" name="tipo_albaran" class="BuscadorInput">
                <option></option>
                <option value="ENTRADA" <?php if($_SESSION["tipo_albaran_material_movimientos"] == "ENTRADA") { ?> selected="selected" <?php } ?>>ENTRADA</option>
                <option value="SALIDA" <?php if($_SESSION["tipo_albaran_material_movimientos"] == "SALIDA") { ?> selected="selected" <?php } ?>>SALIDA</option>
            </select>
        </td>
        <td>
            <div class="Label">Origen / Destino</div>
            <input type="text" id="origen_destino" name="origen_destino" class="BuscadorInput" value="<?php echo $_SESSION["origen_destino_material_movimientos"];?>"/>
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
                        <option value=<?php echo $id_usuario; if($_SESSION["id_usuario_material_movimientos"] == $id_usuario) { ?> selected="selected" <?php } ?>><?php echo $nombre_usuario; ?></option>   
                <?php 
                    }
                ?>
            </select>
        </td>
        <td>
            <div class="Label">N&uacute;mero de Serie</div>
            <input type="text" id="num_serie" name="num_serie" class="BuscadorInput" maxlength="30" value="<?php echo $_SESSION["num_serie_material_movimientos"];?>"/>
        </td>
        <td>
            <div class="Label">Averiado</div>
            <select id="averiado" name="averiado" class="BuscadorInput">
                <option></option>
                <option value="SI" <?php if($_SESSION["averiado_material_movimientos"] == "SI") { ?> selected="selected" <?php } ?>>SI</option>
                <option value="NO" <?php if($_SESSION["averiado_material_movimientos"] == "NO") { ?> selected="selected" <?php } ?>>NO</option>
            </select>    
        </td>
    </tr>
    <tr style="border:0;">
        <td>
            <div class="Label">Motivo</div>
            <select id="tipo_motivo" name="tipo_motivo" class="BuscadorInput">
                <option></option>
                <?php
                    $array_tipos_motivo = array("COMPRA / SUMINISTRO","PENDIENTE REPARACION","MATERIAL REPARADO","SERVICIO REPARACION","MATERIAL ASIGNADO");
                    for($i=0;$i<count($array_tipos_motivo);$i++){ ?> 
                        <option value=<?php echo $array_tipos_motivo[$i]; if($array_tipos_motivo[$i] == $_SESSION["tipo_motivo_material_movimientos"]) { ?>
                                selected="selected" <?php } ?>><?php echo $array_tipos_motivo[$i];?></option> 
                <?php 
                    }
                ?>
            </select>
        </td>
        <td>
            <div class="Label">Fecha desde</div>
            <input type="text" name="fecha_desde" id="datepicker_movimientos_material_desde" class="fechaCal" value="<?php echo $_SESSION["fecha_desde_material_movimientos"];?>"/>                
        </td>
        <td>
            <div class="Label">Fecha hasta</div>
            <input type="text" name="fecha_hasta" id="datepicker_movimientos_material_hasta" class="fechaCal" value="<?php echo $_SESSION["fecha_hasta_material_movimientos"];?>"/>
        </td>
    </tr>
    <tr style="border:0;">
        <td>
            <div class="Label">Tipo Material</div>
            <select id="tipo_material" name="tipo_material" class="BuscadorInput">
                <option></option>
                <?php
                    // Cargamos los nombres de los tipos de material
                    $resultados_tipo = $materialInformatico->dameTiposMateriales();
                    for($i=0;$i<count($resultados_tipo);$i++){
                        $id_tipo = $resultados_tipo[$i]["id"];
                        $nombre_tipo = $resultados_tipo[$i]["nombre"]; ?>
                        <option value=<?php echo $id_tipo; if($id_tipo == $_SESSION["tipo_material_material_movimientos"]) { ?> selected="selected" <?php } ?>><?php echo utf8_encode($nombre_tipo); ?></option>
                <?php 
                    }
                ?>        
            </select>
        </td>
        <td style="text-align: center;">
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
        if($mostrar_tabla){
            if($num_resultados == NULL or $num_resultados == 0){
                echo '<div class="mensaje">No se encontraron movimientos</div>';
                $mostrar_tabla = false;
            }
            else if ($num_resultados == 1){
                echo '<div class="mensaje">Se encontró 1 movimiento</div>';
            }
            else{
                echo '<div class="mensaje">Se encontraron '.$num_resultados.' movimientos</div>';
            }   
        }
    ?>
    </div>
    <?php
        if($mostrar_tabla) { ?>
            <div class="CapaTabla">
				<table>
    			<tr>
    			    <th style="text-align:left">NUM. SERIE</th>
    				<th style="text-align:left">TIPO MATERIAL</th>
                    <th style="text-align:left">ALBARAN</th>
                    <th style="text-align:left">TIPO ALBARAN</th>
                    <th style="text-align:left">USUARIO</th>
                    <th style="text-align:left">ALMACEN</th>
                    <th style="text-align:left">ORIGEN / DESTINO</th>
                    <th style="text-align:left">MOTIVO</th>
    				<th style="text-align:center">FECHA CREACION</th>
                    <th style="text-align:center">AVERIADO</th>
    			</tr>
                <?php
                    // Se cargan los datos de los movimientos
                    for($i=0;$i<count($resultadosBusqueda);$i++) {
                        // Cargamos los materiales y los datos del albarán al que pertenece
                        $num_serie = $resultadosBusqueda[$i]["num_serie"];
                        $averiado = $resultadosBusqueda[$i]["averiado"];
                        $id_material = $resultadosBusqueda[$i]["id_material"];
                        $fecha_creado = $resultadosBusqueda[$i]["fecha_creado"];
                        $id_albaran = $resultadosBusqueda[$i]["id_albaran"];

                        // Cargamos los datos del albarán del material
                        $albaranInformatico->cargaDatosAlbaranId($id_albaran);
                        $nombre_albaran = $albaranInformatico->nombre_albaran;
                        $tipo_albaran = $albaranInformatico->tipo_albaran;
                        $origen_destino = $albaranInformatico->origen_destino;    
                        $id_usuario = $albaranInformatico->id_usuario;
                        $motivo = $albaranInformatico->motivo;
                        $id_almacen = $albaranInformatico->id_almacen;

                        // Cargamos el nombre del tipo de material
                        $materialInformatico->cargaDatosMaterialId($id_material);
                        $id_tipo = $materialInformatico->id_tipo;

                        $nombre_tipo = $materialInformatico->dameTipoMaterial($id_tipo);
                        $nombre_tipo = $nombre_tipo[0]["nombre"];

                        // Cargamos el nombre del usuario
                        $usuario->cargaDatosUsuarioId($id_usuario);
                        $nombre_usuario = $usuario->usuario;

                        $fecha_creado = $albaranInformatico->fecha_creado;
                        $fecha_creado = $usuario->fechaHoraSpain($fecha_creado);

                        // Cargamos el nombre del almacen 
                        $almacen->cargaDatosAlmacenId($id_almacen);
                        $nombre_almacen = $almacen->nombre; ?>
                        <tr>   
                            <td style="text-align:left"><?php echo $num_serie; ?></td>
                            <td style="text-align:left"><?php echo utf8_encode($nombre_tipo); ?></td>
                            <td style="text-align:left"><?php echo utf8_encode($nombre_albaran); ?></td>
                            <td style="text-align:left"><?php echo $tipo_albaran; ?></td>
                            <td style="text-align:left"><?php echo $nombre_usuario; ?></td>
                            <td style="text-align:left"><?php echo $nombre_almacen;?></td>
                            <td style="text-align:left"><?php echo utf8_encode($origen_destino); ?></td>
                            <td style="text-align:left"><?php echo $motivo; ?></td>
                            <td style="text-align:center"><?php echo $fecha_creado; ?></td>
                            <td style="text-align:center">
                            <?php 
                                if($averiado == "NO"){ ?>
                                    <span style="color: green;"><?php echo $averiado;?></span>        
                            <?php
                                }    
                                else { ?>        
                                    <span style="color: red;"><?php echo $averiado;?></span>    
                            <?php        
                                }
                            ?>
                            </td>
            			</tr> 
                <?php
    				}
    			?>
				</table>                  
			</div>
			<br/>
            <div class="ContenedorBotonCrear">
                <input type="button" id="descargar_XLS_Movimientos" name="descargar_XLS_Movimientos" value="Descargar XLS" class="BotonEliminar" onclick="javascript:descargar_XLS_Movimientos();"/>
            </div>
    <?php        
    	}
	?>
</div>    
<?php include ('../includes/footer.php');  ?>