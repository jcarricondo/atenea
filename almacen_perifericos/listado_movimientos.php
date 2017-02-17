<?php
// Este fichero muestra el listado de los movimientos de los albaranes de periféricos
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/control_usuario.class.php");
include("../classes/sede/sede.class.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/usuario.class.php");
include("../classes/basicos/centro_logistico.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/almacen_perifericos/albaran_periferico.class.php");
include("../classes/almacen_perifericos/periferico_almacen.class.php");
include("../classes/almacen_perifericos/listado_movimientos_perifericos.class.php");
permiso(31);

// Instancias de las clases
$control_usuario = new Control_Usuario();
$sede = new Sede();
$funciones = new Funciones();
$usuario = new Usuario();
$centroLogistico = new CentroLogistico();
$almacen = new Almacen();
$albaranPeriferico = new AlbaranPeriferico();
$perifericoAlmacen = new perifericoAlmacen();
$listadoMovimientos = new listadoMovimientosPeriferico();

$titulo_pagina = "Almacen Periféricos > Listado Movimientos";
$pagina = "listado_movimientos";
include("../includes/header.php");
echo '<script type="text/javascript" src="../js/almacen_perifericos/almacen_perifericos.js"></script>';

$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$id_almacen_usuario = $_SESSION["AT_id_almacen"];
$id_sede = $control_usuario->dameSedeSegunUsuario($id_tipo_usuario,$id_almacen_usuario);
$id_sede_usuario = $control_usuario->dameSedeSegunUsuario($id_tipo_usuario,$id_almacen_usuario);

$esAdminGlobal = $control_usuario->esAdministradorGlobal($id_tipo_usuario);
$esUsuarioBrasil = $control_usuario->esUsuarioBrasil($id_tipo_usuario,$id_sede);
$filtroSede = $esAdminGlobal;

// Predeterminado si el usuario sin sede asignada no escogió ninguna
if(empty($id_sede)) $id_sede = 1;
if(empty($id_sede_usuario)) $id_sede_usuario = 1;
if(empty($id_almacen_usuario)) $_SESSION["id_almacen_perifericos_movimientos"] = 1;
else $_SESSION["id_almacen_perifericos_movimientos"] = $_SESSION["AT_id_almacen"];

$esAlmacenBrasil = $almacen->esAlmacenBrasil($id_almacen_usuario);

// Establecemos los parámetros de la paginación
// Número de registros a mostrar por página
$pg_registros = 50; 
$pg_pagina = $_GET["pg"];
if(empty($pg_pagina)) {
    $pg_inicio = 0;
    $pg_pagina = 1;
} 
else $pg_inicio = ($pg_pagina - 1) * $pg_registros;
$paginacion = " limit ".$pg_inicio.', '.$pg_registros;

// Se obtienen los datos del formulario
if((isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1) or $buscar == 1) {
	$mostrar_tabla = true;
	$nombre_albaran = addslashes($_GET["nombre_albaran"]);
	$tipo_albaran = $_GET["tipo_albaran"];
	$id_centro_logistico = $_GET["id_centro_logistico"];
    $id_usuario = $_GET["id_usuario"];
    $numero_serie = addslashes($_GET["num_serie"]);
    $averiado = $_GET["averiado"];
    $tipo_periferico = $_GET["tipo_periferico"];
	$fecha_desde = $_GET["fecha_desde"];
    $fecha_hasta = $_GET["fecha_hasta"];
    $tipo_motivo = $_GET["tipo_motivo"];
    $id_almacen = $_GET["almacenes"];
    if($filtroSede) $id_sede = $_GET["sedes"];

    $sede_almacen = $almacen->dameSedeAlmacen($id_almacen);
    $sede_almacen = $sede_almacen["id_sede"];
    $esAlmacenBrasil = $sede_almacen == 3;

	// Preparamos la fecha para la consulta
    if($fecha_desde != ""){
        // Guardamos la fecha
        $fecha_desde_aux = $fecha_desde;
        $fecha_desde = $funciones->cFechaMy($fecha_desde);
        $date = new DateTime($fecha_desde);

        if($esUsuarioBrasil || ($esAdminGlobal && $esAlmacenBrasil)) $fecha_desde = $date->add(new DateInterval('PT5H'));
        $fecha_desde = $date->format('Y-m-d H:i:s');
    }
    if($fecha_hasta != ""){
        // Guardamos la fecha
        $fecha_hasta_aux = $fecha_hasta;
        $fecha_hasta = $funciones->cFechaMy($fecha_hasta);
        $date = new DateTime($fecha_hasta . " + 1 days");

        if($esUsuarioBrasil || ($esAdminGlobal && $esAlmacenBrasil)) $fecha_hasta = $date->add(new DateInterval('PT5H'));
        $fecha_hasta = $date->format('Y-m-d H:i:s');
    }

    // Se pasan los datos del buscador a la clase del listado y se realiza la consulta a la base de datos sin parámetros de paginación
    $listadoMovimientos->setValores($nombre_albaran,$tipo_albaran,$id_centro_logistico,$id_usuario,$numero_serie,$averiado,$tipo_periferico,$tipo_motivo,$fecha_desde,$fecha_hasta,'',$id_almacen,$id_sede);
    $listadoMovimientos->realizarConsulta();
    $resultadosBusqueda = $listadoMovimientos->movimientos;
    $num_resultados = count($resultadosBusqueda);

    // Se guardan las variables de sesión para preparar el informe de movimientos
    $_SESSION["nombre_albaran_xls_perifericos_movimientos"] = $nombre_albaran;
    $_SESSION["tipo_albaran_xls_perifericos_movimientos"] = $tipo_albaran;
    $_SESSION["id_centro_logistico_xls_perifericos_movimientos"] = $id_centro_logistico;
    $_SESSION["id_usuario_xls_perifericos_movimientos"] = $id_usuario;
    $_SESSION["numero_serie_xls_perifericos_movimientos"] = $numero_serie;
    $_SESSION["averiado_xls_perifericos_movimientos"] = $averiado;
    $_SESSION["tipo_periferico_xls_perifericos_movimientos"] = $tipo_periferico;
    $_SESSION["tipo_motivo_xls_perifericos_movimientos"] = $tipo_motivo;
    $_SESSION["id_almacen_xls_perifericos_movimientos"] = $id_almacen;
    $_SESSION["fecha_desde_xls_perifericos_movimientos"] = $fecha_desde;
    $_SESSION["fecha_hasta_xls_perifericos_movimientos"] = $fecha_hasta;
    $_SESSION["id_sede_xls_perifericos_movimientos"] = $id_sede;

    $pg_totalPaginas = ceil(count($resultadosBusqueda) / $pg_registros);

    // Se obtienen los 50 resultados correspondientes a la consulta
    $listadoMovimientos->setValores($nombre_albaran,$tipo_albaran,$id_centro_logistico,$id_usuario,$numero_serie,$averiado,$tipo_periferico,$tipo_motivo,$fecha_desde,$fecha_hasta,$paginacion,$id_almacen,$id_sede);
    $listadoMovimientos->realizarConsulta();
    $resultadosBusqueda = $listadoMovimientos->movimientos;

    // Convierte la fecha a formato HTML
    if($fecha_desde != "") $fecha_desde = $fecha_desde_aux;
    if($fecha_hasta != "") $fecha_hasta = $fecha_hasta_aux;

	// Guardar las variables del formulario en variable de sesión
	$_SESSION["nombre_albaran_perifericos_movimientos"] = stripslashes(htmlspecialchars($nombre_albaran));
	$_SESSION["tipo_albaran_perifericos_movimientos"] = $tipo_albaran;
	$_SESSION["id_centro_logistico_perifericos_movimientos"] = $id_centro_logistico;
	$_SESSION["id_usuario_perifericos_movimientos"] = $id_usuario;
    $_SESSION["numero_serie_perifericos_movimientos"] = stripslashes(htmlspecialchars($numero_serie));
    $_SESSION["averiado_perifericos_movimientos"] = $averiado;
    $_SESSION["tipo_periferico_perifericos_movimientos"] = $tipo_periferico;
    $_SESSION["tipo_motivo_perifericos_movimientos"] = $tipo_motivo;  
    $_SESSION["id_almacen_perifericos_movimientos"] = $id_almacen;
    $_SESSION["fecha_desde_perifericos_movimientos"] = $fecha_desde;
    $_SESSION["fecha_hasta_perifericos_movimientos"] = $fecha_hasta;
    $_SESSION["id_sede_perifericos_movimientos"] = $id_sede;
}
?>

<div class="separador"></div> 
<?php include("../includes/menu_almacen_perifericos.php"); ?> 

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include("../includes/sidebar.php"); ?>
    </div>
       	
    <h3>Listado Movimientos</h3>
    <h4>Buscar movimiento</h4>

    <form id="BuscadorMovimiento" name="buscadorMovimiento" action="listado_movimientos.php" method="get" class="Buscador">
    <table style="border:0;">
    <?php
        if($filtroSede){?>
            <tr style="border:0;">
                <td style="width: 33%; vertical-align: top;">
                    <div class="Label">Sede</div>
                    <select id="sedes" name="sedes" class="BuscadorInputAlmacen" onchange="cambiaCamposBuscadorAlbaran(this.value)" >
                        <?php
                            // Obtenemos todas las sedes
                            $resultados_sedes = $sede->dameSedesMantenimiento();
                            for($i=0;$i<count($resultados_sedes);$i++) {
                                $id_sede_res = $resultados_sedes[$i]["id_sede"];
                                $nombre_sede = $resultados_sedes[$i]["sede"];

                                echo '<option value="'.$id_sede_res.'"';
                                if($id_sede_res == $id_sede) echo ' selected="selected"';
                                echo '>'.$nombre_sede.'</option>';
                            }
                        ?>
                    </select>
                </td>
            </tr>
    <?php
        }
    ?>
    	<tr style="border:0;">
        	<td style="width: 33%;">
                <div class="Label">Almacen</div>
                <div id="capaAlmacenes">
                    <select id="almacenes" name="almacenes" class="BuscadorInputAlmacen">
                        <option value="">Seleccionar</option>
                        <?php
                            $resultados_almacenes = $sede->dameAlmacenesMantenimientoSede($id_sede);
                            for($i=0;$i<count($resultados_almacenes);$i++){
                                $id_almacen = $resultados_almacenes[$i]["id_almacen"];
                                $nombre_almacen = $resultados_almacenes[$i]["almacen"];

                                echo '<option value="'.$id_almacen.'"';
                                if($id_almacen == $_SESSION["id_almacen_perifericos_movimientos"]){
                                    echo ' selected="selected"';
                                }
                                echo '>'.$nombre_almacen.'</option>';
                            }
                        ?>
                    </select>
                </div>
            </td>
            <td style="width: 33%;">
                <div class="Label">Albarán</div>
                <input type="text" id="nombre_albaran" name="nombre_albaran" class="BuscadorInputAlmacen" value="<?php echo $_SESSION["nombre_albaran_perifericos_movimientos"];?>"/>
            </td>
            <td style="width: 33%;">
            	<div class="Label">Origen / Destino</div>
                <select id="id_centro_logisitico" name="id_centro_logistico" class="BuscadorInputAlmacen">
                    <?php 
                        echo '<option></option>';
                        $resultado_centros = $centroLogistico->dameCentrosLogisticos();
                        for($i=0;$i<count($resultado_centros);$i++){
                            $id_centro = $resultado_centros[$i]["id_centro_logistico"];
                            $nombre_centro = $resultado_centros[$i]["centro_logistico"];
                            echo '<option';
                            if($id_centro == $_SESSION["id_centro_logistico_perifericos_movimientos"]){
                                echo ' selected="selected"';
                            }
                            echo ' value='.$id_centro.'>'.$nombre_centro.'</option>';      
                        }                    
                    ?>        
                </select>
            </td>
        </tr>
        <tr style="border:0;">
        	<td style="width: 33%;">
            	<div class="Label">Usuario</div>
                <select id="id_usuario" name="id_usuario" class="BuscadorInputAlmacen">
                    <option></option>
                    <?php
                        // Obtenemos todos los usuarios activos
                        if($esAdminGlobal) $resultado_usuarios = $usuario->dameUsuariosAlmacen();
                        else $resultado_usuarios = $sede->dameUsuariosAlmacenSede($id_sede_usuario);

                        for($i=0;$i<count($resultado_usuarios);$i++){
                            $id_usuario = $resultado_usuarios[$i]["id_usuario"];
                            $nombre_usuario = $resultado_usuarios[$i]["usuario"];
                            echo '<option value='.$id_usuario;
                            if($id_usuario == $_SESSION["id_usuario_perifericos_movimientos"]){
                                echo ' selected="selected"';
                            }
                            echo '>'.$nombre_usuario.'</option>';      
                        }    
                    ?>
                </select>
            </td>
            <td style="width: 33%;">
                <div class="Label">Tipo</div>
                <select id="tipo_albaran" name="tipo_albaran" class="BuscadorInputAlmacen">
                    <option></option>
                    <?php
                    $array_tipos_albaran = array("ENTRADA","SALIDA");
                    for($i=0;$i<count($array_tipos_albaran);$i++){
                        echo '<option';
                        if($array_tipos_albaran[$i] == $_SESSION["tipo_albaran_perifericos_movimientos"]){
                            echo ' selected="selected"';
                        }
                        echo '>'.$array_tipos_albaran[$i].'</option>';
                    }
                    ?>
                </select>
            </td>
            <td style="width: 33%;">
                <div class="Label">Motivo</div>
                <select id="tipo_motivo" name="tipo_motivo" class="BuscadorInputAlmacen">
                    <option></option>
                    <?php
                        if(!empty($id_almacen)) $res_motivos = $albaranPeriferico->dameMotivosAlbaranPerifericos($id_almacen);
                        else $res_motivos = $sede->dameMotivosAlbaranPerifericosSede($id_sede);

                        for($i=0;$i<count($res_motivos);$i++) {
                            $motivo_bus = $res_motivos[$i]["motivo"]; ?>
                            <option <?php if($_SESSION["tipo_motivo_perifericos_movimientos"] == $motivo_bus) echo "selected"; ?>><?php echo $motivo_bus;?></option>
                        <?php
                    }
                    ?>
                </select>
            </td>
        </tr>
         <tr style="border:0;">
            <td style="width: 33%;">
                <div class="Label">N&uacute;mero de Serie</div>
                <input type="text" id="num_serie" name="num_serie" class="BuscadorInputAlmacen" maxlength="30" value="<?php echo $_SESSION["numero_serie_perifericos_movimientos"];?>"/>
            </td>
            <td style="width: 33%;">
                <div class="Label">Fecha desde</div>
                <input type="text" name="fecha_desde" id="datepicker_movimientos_perifericos_desde" class="fechaCal" style="width: 175px;" value="<?php echo $_SESSION["fecha_desde_perifericos_movimientos"];?>"/>
            </td>
            <td style="width: 33%;">
                <div class="Label">Fecha hasta</div>
                <input type="text" name="fecha_hasta" id="datepicker_movimientos_perifericos_hasta" class="fechaCal" style="width: 175px;" value="<?php echo $_SESSION["fecha_hasta_perifericos_movimientos"];?>"/>
            </td>
        </tr>
        <tr style="border:0;">
        	<td style="width: 33%;">
                <div class="Label">Averiado</div>
                <select id="averiado" name="averiado" class="BuscadorInputAlmacen">
                    <option></option>
                    <?php
                    if ($_SESSION["averiado_perifericos_movimientos"] == "SI"){
                        echo '<option selected="selected">SI</option>';
                        echo '<option>NO</option>';
                    }
                    else if ($_SESSION["averiado_perifericos_movimientos"] == "NO"){
                        echo '<option>SI</option>';
                        echo '<option selected="selected">NO</option>';
                    }
                    else {
                        echo '<option>SI</option>';
                        echo '<option>NO</option>';
                    }
                    ?>
                </select>
            </td>
            <td style="width: 33%;">
                <div class="Label">Tipo Perif&eacute;rico</div>
                <select id="tipo_periferico" name="tipo_periferico" class="BuscadorInputAlmacen">
                    <?php
                    // Cargamos los nombres de los tipos de perifericos
                    $resultados_tipo = $perifericoAlmacen->dameDatosTipoPerifericos();

                    echo '<option></option>';
                    for($i=0;$i<count($resultados_tipo);$i++){
                        $id_tipo_periferico = $resultados_tipo[$i]["id"];
                        $nombre_tipo = $resultados_tipo[$i]["nombre"];

                        echo '<option value="'.$id_tipo_periferico.'"';
                        if($id_tipo_periferico == $_SESSION["tipo_periferico_perifericos_movimientos"]){
                            echo ' selected="selected" ';
                        }
                        echo '>'.$nombre_tipo.'</option>';
                    }
                    ?>
                </select>
            </td>
            <td style="width: 33%;"></td>
        </tr>
        <tr style="border:0;">
            <td style="width: 33%; text-align: left;">
                <input type="hidden" id="realizandoBusqueda" name="realizandoBusqueda" value="1" />
                <input type="submit" id="botonEnviar" name="botonEnviar" value="Buscar" />
            </td>
            <td style="width: 33%;"></td>
            <td style="width: 33%;"></td>
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
    					<th style="text-align:left">TIPO PERIFERICO</th>
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
                        // Cargamos los perifericos y los datos del albaran al que pertenece
                        $numero_serie = $resultadosBusqueda[$i]["numero_serie"];
                        $averiado = $resultadosBusqueda[$i]["averiado"];
                        $id_periferico = $resultadosBusqueda[$i]["id_periferico"];
                        $fecha_creado = $resultadosBusqueda[$i]["fecha_creado"];
                        $id_albaran = $resultadosBusqueda[$i]["id_albaran"];

                        // Cargamos los datos del albaran del periferico
                        $albaranPeriferico->cargaDatosAlbaranId($id_albaran);
                        $nombre_albaran = $albaranPeriferico->nombre_albaran;
                        $tipo_albaran = $albaranPeriferico->tipo_albaran;
                        $id_centro_logistico = $albaranPeriferico->id_centro_logistico;    
                        $id_usuario = $albaranPeriferico->id_usuario;
                        $motivo = $albaranPeriferico->motivo;
                        $id_almacen = $albaranPeriferico->id_almacen;

                        // Obtenemos la sede del almacen al que pertenece el albaran
                        $sede_almacen = $almacen->dameSedeAlmacen($id_almacen);
                        $sede_almacen = $sede_almacen["id_sede"];
                        $esAlbaranBrasil = $sede_almacen == 3;

                        // Cargamos el nombre del tipo de periferico
                        $perifericoAlmacen->cargaDatosPerifericoId($id_periferico);
                        $tipo_periferico = $perifericoAlmacen->tipo_periferico;

                        $nombre_tipo = $perifericoAlmacen->dameNombreTipoPeriferico($tipo_periferico);
                        $nombre_tipo = $nombre_tipo["nombre"];

                        // Cargamos el nombre del usuario
                        $usuario->cargaDatosUsuarioId($id_usuario);
                        $nombre_usuario = $usuario->usuario;

                        // Cargamos el nombre del centro logistico
                        $centroLogistico->cargaDatosCentroLogisticoId($id_centro_logistico);
                        $nombre_centro = $centroLogistico->nombre;

                        // Convertimos la fecha
                        $fecha_creado = $albaranPeriferico->fecha_creado;
                        if($esAlbaranBrasil) $fecha_creado = $usuario->fechaHoraBrasil($fecha_creado);
                        else $fecha_creado = $usuario->fechaHoraSpain($fecha_creado);

                        // Cargamos el nombre del almacen
                        $almacen->cargaDatosAlmacenId($id_almacen);
                        $nombre_almacen = $almacen->nombre; ?>
                        <tr>   
                            <td style="text-align:left"><?php echo $numero_serie; ?></td>
                            <td style="text-align:left"><?php echo $nombre_tipo; ?></td>
                            <td style="text-align:left"><?php echo $nombre_albaran; ?></td>
                            <td style="text-align:left"><?php echo $tipo_albaran; ?></td>
                            <td style="text-align:left"><?php echo $nombre_usuario; ?></td>
                            <td style="text-align:left"><?php echo $nombre_almacen;?></td>
                            <td style="text-align:left"><?php echo $nombre_centro; ?></td>
                            <td style="text-align:left"><?php echo $motivo; ?></td>
                            <td style="text-align:center"><?php echo $fecha_creado; ?></td>
                            <td style="text-align:center">
                                <?php 
                                    if ($averiado == "NO"){ ?>
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
			<?php // Después de mostrar la tabla mostramos la paginación y el boton para descargar el resultado de búsqueda ?>

            <div style="font: bold 11px Verdana,Arial; margin: 0 auto; padding: 10px 0; width: 350px; text-align: center;"> 
                <?php
                    if(($pg_pagina - 1) > 0) { ?>
                        <a href="listado_movimientos.php?pg=1&realizandoBusqueda=1&nombre_albaran=<?php echo $_SESSION["nombre_albaran_perifericos_movimientos"];?>&tipo_albaran=<?php echo $_SESSION["tipo_albaran_perifericos_movimientos"];?>&id_centro_logistico=<?php echo $_SESSION["id_centro_logistico_perifericos_movimientos"];?>&id_usuario=<?php echo $_SESSION["id_usuario_perifericos_movimientos"];?>&num_serie=<?php echo $_SESSION["numero_serie_perifericos_movimientos"];?>&averiado=<?php echo $_SESSION["averiado_perifericos_movimientos"];?>&tipo_periferico=<?php echo $_SESSION["tipo_periferico_perifericos_movimientos"];?>&tipo_motivo=<?php echo $_SESSION["tipo_motivo_perifericos_movimientos"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_perifericos_movimientos"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_perifericos_movimientos"];?>&almacenes=<?php echo $_SESSION["id_almacen_perifericos_movimientos"];?>&sedes=<?php echo $_SESSION["id_sede_perifericos_movimientos"];?>">Primera&nbsp&nbsp&nbsp</a>
                        <a href="listado_movimientos.php?pg=<?php echo $pg_pagina - 1;?>&realizandoBusqueda=1&nombre_albaran=<?php echo $_SESSION["nombre_albaran_perifericos_movimientos"];?>&tipo_albaran=<?php echo $_SESSION["tipo_albaran_perifericos_movimientos"];?>&id_centro_logistico=<?php echo $_SESSION["id_centro_logistico_perifericos_movimientos"];?>&id_usuario=<?php echo $_SESSION["id_usuario_perifericos_movimientos"];?>&num_serie=<?php echo $_SESSION["numero_serie_perifericos_movimientos"];?>&averiado=<?php echo $_SESSION["averiado_perifericos_movimientos"];?>&tipo_periferico=<?php echo $_SESSION["tipo_periferico_perifericos_movimientos"];?>&tipo_motivo=<?php echo $_SESSION["tipo_motivo_perifericos_movimientos"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_perifericos_movimientos"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_perifericos_movimientos"];?>&almacenes=<?php echo $_SESSION["id_almacen_perifericos_movimientos"];?>&sedes=<?php echo $_SESSION["id_sede_perifericos_movimientos"];?>">Anterior</a>
                <?php
                    }  
                    else {
                        echo 'Primera&nbsp;&nbsp;&nbsp;Anterior';
                    }
            
                    echo ' &nbsp;&nbsp;&nbsp;['.$pg_pagina.' / '.$pg_totalPaginas.']&nbsp;&nbsp;&nbsp;';
                    if($pg_pagina < $pg_totalPaginas) { ?>
                        <a href="listado_movimientos.php?pg=<?php echo $pg_pagina + 1;?>&realizandoBusqueda=1&nombre_albaran=<?php echo $_SESSION["nombre_albaran_perifericos_movimientos"];?>&tipo_albaran=<?php echo $_SESSION["tipo_albaran_perifericos_movimientos"];?>&id_centro_logistico=<?php echo $_SESSION["id_centro_logistico_perifericos_movimientos"];?>&id_usuario=<?php echo $_SESSION["id_usuario_perifericos_movimientos"];?>&num_serie=<?php echo $_SESSION["numero_serie_perifericos_movimientos"];?>&averiado=<?php echo $_SESSION["averiado_perifericos_movimientos"];?>&tipo_periferico=<?php echo $_SESSION["tipo_periferico_perifericos_movimientos"];?>&tipo_motivo=<?php echo $_SESSION["tipo_motivo_perifericos_movimientos"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_perifericos_movimientos"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_perifericos_movimientos"];?>&almacenes=<?php echo $_SESSION["id_almacen_perifericos_movimientos"];?>&sedes=<?php echo $_SESSION["id_sede_perifericos_movimientos"];?>">Siguiente&nbsp&nbsp&nbsp</a>
                        <a href="listado_movimientos.php?pg=<?php echo $pg_totalPaginas;?>&realizandoBusqueda=1&nombre_albaran=<?php echo $_SESSION["nombre_albaran_perifericos_movimientos"];?>&tipo_albaran=<?php echo $_SESSION["tipo_albaran_perifericos_movimientos"];?>&id_centro_logistico=<?php echo $_SESSION["id_centro_logistico_perifericos_movimientos"];?>&id_usuario=<?php echo $_SESSION["id_usuario_perifericos_movimientos"];?>&num_serie=<?php echo $_SESSION["numero_serie_perifericos_movimientos"];?>&averiado=<?php echo $_SESSION["averiado_perifericos_movimientos"];?>&tipo_periferico=<?php echo $_SESSION["tipo_periferico_perifericos_movimientos"];?>&tipo_motivo=<?php echo $_SESSION["tipo_motivo_perifericos_movimientos"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_perifericos_movimientos"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_perifericos_movimientos"];?>&almacenes=<?php echo $_SESSION["id_almacen_perifericos_movimientos"];?>&sedes=<?php echo $_SESSION["id_sede_perifericos_movimientos"];?>">Última</a>
                <?php        
                    } 
                    else {
                        echo 'Siguiente&nbsp;&nbsp;&nbsp;Última';
                    }
                ?>
            </div>
            <br/>
            <div class="ContenedorBotonCrear"><input type="button" id="descargar_XLS_Movimientos" name="descargar_XLS_Movimientos" value="Descargar XLS" class="BotonEliminar" onclick="javascript:descargar_XLS_Movimientos();"/></div>
    <?php        
    	}
	?>
</div>    
<?php include ('../includes/footer.php');  ?>