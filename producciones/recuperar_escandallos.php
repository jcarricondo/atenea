<?php
// Este fichero muestra un listado con los codigo de los escandallos generados
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/usuario.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/producciones/produccion.class.php");
include("../classes/producciones/listado_recuperacion_escandallos.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/sede/sede.class.php");
include("../classes/control_usuario.class.php");
include("../classes/kint/Kint.class.php");
permiso(20);

$db = new MySQL();
$funciones = new Funciones();
$produccion = new Produccion();
$recuperacion = new ListadoRecuperacionEscandallos();
$usuario = new Usuario();
$almacen = new Almacen();
$sede = new Sede();
$orden_produccion = new Orden_Produccion();
$control_usuario = new Control_Usuario();

$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$id_almacen = $_SESSION["AT_id_almacen"];
// Comprobamos si es Administrador Global
$esAdminGlobal = $control_usuario->esAdministradorGlobal($id_tipo_usuario);
$esAdministradorGes = $control_usuario->esAdministradorGes($id_tipo_usuario);
$esUsuarioFabricaSimumak = $control_usuario->esUsuarioFabricaSimumak($id_tipo_usuario,$id_almacen);
$esUsuarioFabricaToro = $control_usuario->esUsuarioFabricaToro($id_tipo_usuario,$id_almacen);
// Obtenemos la sede a la que pertenece el usuario 
$id_sede = $almacen->dameSedeAlmacen($id_almacen);
$id_sede = $id_sede["id_sede"];
$res_sedes = $sede->dameSedes();

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

$dir_producciones = getcwd();

// Cargamos todos los codigos del log
if($esAdminGlobal || $esAdministradorGes) $produccion->dameTodosCodigos();
else {
    $produccion->dameTodosCodigosPorSede($id_sede);
}
$resultados_codigos = $produccion->resultados;

// Descarga de zip de escandallo
if($_GET["escandallo"] == "recuperar"){
    // Obtenemos el nombre del archivo en funcion del codigo del escandallo
    $filename = $_GET["codigo"].".zip";
    // Cambiamos el directorio para buscar el zip en la carpeta ESCANDALLOS
    // $dir_actual = getcwd()."\ESCANDALLOS"; // LOCAL
    $dir_actual = getcwd()."/ESCANDALLOS"; // PRODUCCION
    chdir($dir_actual);

    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=".$filename);
    header("Expires: 0");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".filesize($filename));
    header("Pragma: public");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private",false);
    header("Content-Description: File Transfer");
    readfile($filename);

    $dir_actual = $dir_producciones;
    chdir($dir_actual);
}

if(isset($_GET["realizandoBusqueda"]) && (($_GET["realizandoBusqueda"]) == 1)){
    $mostrar_tabla = true;
    if($esAdminGlobal || $esAdministradorGes) $id_sede = $_GET["sedes"];
    $id_usuario = $_GET["id_usuario"];
    $codigo = addslashes($_GET["codigo"]);
    $id_produccion = $_GET["id_produccion"];
    $alias_op = addslashes($_GET["alias_op"]);
    $num_tecnicos = $_GET["num_tecnicos"];
    $fecha_desde = $_GET["fecha_desde"];
    $fecha_hasta = $_GET["fecha_hasta"];

    if($fecha_desde != "") $fecha_desde = $funciones->cFechaMy($fecha_desde);
    if($fecha_hasta != "") $fecha_hasta = $funciones->cFechaMy($fecha_hasta);

    $recuperacion->setValores($id_sede,$id_usuario,$codigo,$id_produccion,$alias_op,$num_tecnicos,$fecha_desde,$fecha_hasta,'');
    $recuperacion->realizarConsulta();
    $resultadosBusqueda = $recuperacion->escandallos;
    $num_resultados = count($resultadosBusqueda);

    $pg_totalPaginas = ceil(count($resultadosBusqueda) / $pg_registros);
    $recuperacion->setValores($id_sede,$id_usuario,$codigo,$id_produccion,$alias_op,$num_tecnicos,$fecha_desde,$fecha_hasta,$paginacion);
    $recuperacion->realizarConsulta();
    $resultadosBusqueda = $recuperacion->escandallos;

    // Guardamos los valores de los campos del buscador
    $_SESSION["id_sede_recuperar_escandallos"] = $id_sede;
    $_SESSION["id_usuario_recuperar_escandallos"] = $id_usuario;
    $_SESSION["codigo_recuperar_escandallos"] = stripslashes(htmlspecialchars($codigo));
    $_SESSION["id_produccion_recuperar_escandallos"] = $id_produccion;
    $_SESSION["alias_op_recuperar_escandallos"] = stripslashes(htmlspecialchars($alias_op));
    $_SESSION["num_tecnicos_recuperar_escandallos"] = $num_tecnicos;

    if($fecha_desde != "") $fecha_desde = $funciones->cFechaNormal($fecha_desde);
    if($fecha_hasta != "") $fecha_hasta = $funciones->cFechaNormal($fecha_hasta);

    $_SESSION["fecha_desde_recuperar_escandallos"] = $funciones->cFechaNormal($fecha_desde);
    $_SESSION["fecha_hasta_recuperar_escandallos"] = $funciones->cFechaNormal($fecha_hasta);
}

$titulo_pagina = "Producción > Recuperar escandallos";
$pagina = "recuperar_escandallos";
include ('../includes/header.php');
?>

<div class="separador"></div>
<?php include("../includes/menu_producciones.php");?>

<div id="ContenedorCentral">
    <div id="ContenedorSidebar"><?php include ("../includes/sidebar.php"); ?></div>

    <h3>Recuperaci&oacute;n de escandallos</h3>
    <h4>Pulse sobre el código para recuperar el escandallo</h4>

    <form id="BuscadorEscandallos" name="BuscadorEscandallos" action="recuperar_escandallos.php" method="get" class="Buscador">
        <table style="border:0;">
        <?php
            if($esAdminGlobal || $esAdministradorGes){ ?>
                <tr style="border:0;">
                    <td>
                        <div class="Label">Sede</div>
                        <select id="sedes" name="sedes" class="BuscadorInput">
                            <option value="0"></option>
                            <?php
                                for($i=0;$i<count($res_sedes);$i++){
                                    $id_sede_bus = $res_sedes[$i]["id_sede"];
                                    $nombre_sede = $res_sedes[$i]["sede"];

                                    if($nombre_sede != 'BRASIL'){
                                        echo '<option value='.$id_sede_bus;
                                        if($id_sede_bus == $_SESSION["id_sede_recuperar_escandallos"]){
                                            echo ' selected="selected"';
                                        }
                                        echo '>'.$nombre_sede.'</option>';
                                    }
                                }
                            ?>
                        </select>
                    </td>
                    <td>
                        <div class="Label">Usuario</div>
                        <select id="id_usuario" name="id_usuario" class="BuscadorInput">
                            <option></option>
                            <?php
                                // Obtenemos todos los usuarios activos
                                if($esUsuarioFabricaSimumak) $resultado_usuarios = $usuario->dameUsuariosFabricaSimumak();
                                else if($esUsuarioFabricaToro) $resultado_usuarios = $usuario->dameUsuariosFabricaToro();
                                else $resultado_usuarios = $usuario->dameUsuariosFabrica();

                                for($i=0;$i<count($resultado_usuarios);$i++){
                                    $id_usuario = $resultado_usuarios[$i]["id_usuario"];
                                    $nombre_usuario = $resultado_usuarios[$i]["usuario"];
                                    echo '<option value='.$id_usuario;
                                    if($id_usuario == $_SESSION["id_usuario_recuperar_escandallos"]){
                                        echo ' selected="selected"';
                                    }
                                    echo '>'.$nombre_usuario.'</option>';
                                }
                            ?>
                        </select>
                    </td>
                    <td></td>
                </tr>
        <?php
            }
            else { ?>
                <tr style="border:0;">
                    <td>
                        <div class="Label">Usuario</div>
                        <select id="id_usuario" name="id_usuario" class="BuscadorInput">
                            <option></option>
                            <?php
                            // Obtenemos todos los usuarios activos
                            if($esUsuarioFabricaSimumak) $resultado_usuarios = $usuario->dameUsuariosFabricaSimumak();
                            else if($esUsuarioFabricaToro) $resultado_usuarios = $usuario->dameUsuariosFabricaToro();
                            else $resultado_usuarios = $usuario->dameUsuariosFabrica();

                            for($i=0;$i<count($resultado_usuarios);$i++){
                                $id_usuario = $resultado_usuarios[$i]["id_usuario"];
                                $nombre_usuario = $resultado_usuarios[$i]["usuario"];
                                echo '<option value='.$id_usuario;
                                if($id_usuario == $_SESSION["id_usuario_recuperar_escandallos"]){
                                    echo ' selected="selected"';
                                }
                                echo '>'.$nombre_usuario.'</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
        <?php
            }
        ?>

        <tr style="border:0;">
            <td>
                <div class="Label">C&oacute;digo</div>
                <input type="text" id="codigo" name="codigo" class="BuscadorInput" value="<?php echo $_SESSION["codigo_recuperar_escandallos"]; ?>" />
            </td>
            <td>
                <div class="Label">ID. Producci&oacute;n</div>
                <input type="text" id="id_produccion" name="id_produccion" class="BuscadorInput" value="<?php echo $_SESSION["id_produccion_recuperar_escandallos"]; ?>" onkeypress="return soloNumeros(event)" />
            </td>
            <td>
                <div class="Label">Orden Producci&oacute;n</div>
                <input type="text" id="alias_op" name="alias_op" class="BuscadorInput" value="<?php echo $_SESSION["alias_op_recuperar_escandallos"]; ?>" />
            </td>
        </tr>
        <tr style="border:0;">
            <td>
                <div class="Label">Num. T&eacute;cnicos</div>
                <input type="text" id="num_tecnicos" name="num_tecnicos" class="BuscadorInput" value="<?php echo $_SESSION["num_tecnicos_recuperar_escandallos"]; ?>" onkeypress="return soloNumeros(event)" />
            </td>
            <td>
                <div class="Label">Fecha desde</div>
                <input type="text" id="datepicker_recuperar_escandallos_desde" name="fecha_desde" class="fechaCal" value="<?php echo $_SESSION["fecha_desde_recuperar_escandallos"]; ?>" />
            </td>
            <td>
                <div class="Label">Fecha hasta</div>
                <input type="text" id="datepicker_recuperar_escandallos_hasta" name="fecha_hasta" class="fechaCal" value="<?php echo $_SESSION["fecha_hasta_recuperar_escandallos"]; ?>" />
            </td>
        </tr>
        <tr style="border:0;">
            <td>
                <input type="hidden" id="realizandoBusqueda" name="realizandoBusqueda" value="1" />
                <input type="submit" id="botonEnviar" name="botonEnviar" value="Buscar" />
            </td>
            <td></td>
            <td></td>
        </tr>
        </table>
        <br/>
    </form>
    <br/>

    <div class="ContenedorBotonCrear">
    <?php
        if($mostrar_tabla){
            if($num_resultados == NULL or $num_resultados == 0){
                echo '<div class="mensaje">No se encontraron c&oacute;digos</div>';
                $mostrar_tabla = false;
            }
            else if ($num_resultados == 1){
                echo '<div class="mensaje">Se encontró 1 c&oacute;digo</div>';
            }
            else{
                echo '<div class="mensaje">Se encontraron '.$num_resultados.' c&oacute;digos</div>';
            }
        }
    ?>
    </div>
    <?php
        if($mostrar_tabla){ ?>
            <div class="CapaTabla">
                <table>
                    <tr>
                        <th style="text-align:center">C&Oacute;DIGO</th>
                        <th style="text-align:center">ID PRODUCCI&Oacute;N</th>
                        <th>ORDEN PRODUCCI&Oacute;N</th>
                        <?php if($esAdminGlobal || $esAdministradorGes){ ?> <th>SEDE</th> <?php } ?>
                        <th style="text-align:center">NUM. T&Eacute;CNICOS</th>
                        <th>USUARIO</th>
                        <th style="text-align:center">FECHA CREACION</th>
                    </tr>
                <?php
                    // Se cargan los datos de los escandallos
                    for($i=0;$i<count($resultadosBusqueda);$i++) {
                        $codigo = $resultadosBusqueda[$i]["codigo"];
                        $id_produccion = $resultadosBusqueda[$i]["id_produccion"];
                        $alias_op = $resultadosBusqueda[$i]["alias"];

                        $orden_produccion->cargaDatosProduccionId($id_produccion);
                        $id_sede_op = $orden_produccion->id_sede;

                        $sede->cargaDatosSedeId($id_sede_op);
                        $nombre_sede = $sede->nombre;

                        $num_tecnicos = $resultadosBusqueda[$i]["numero_tecnicos"];
                        $id_usuario = $resultadosBusqueda[$i]["id_usuario"];
                        $fecha_creacion = $resultadosBusqueda[$i]["fecha_creacion"];

                        // Cargamos el nombre de la OP
                        $orden_produccion->cargaDatosProduccionId($id_produccion);
                        $alias_op = $orden_produccion->alias_op;

                        // Cargamos el nombre del usuario
                        $usuario->cargaDatosUsuarioId($id_usuario);
                        $nombre_usuario = $usuario->usuario; ?>

                        <tr>
                            <td style="text-align:center">
                               <a href="recuperar_escandallos.php?escandallo=recuperar&codigo=<?php echo $codigo;?>"><?php echo $codigo;?></a>
                            </td>
                            <td style="text-align:center"><?php echo $id_produccion;?></td>
                            <td><?php echo $alias_op;?></td>
                            <?php if($esAdminGlobal || $esAdministradorGes){ ?> <td><?php echo $nombre_sede; ?></td> <?php } ?>
                            <td style="text-align:center"><?php echo $num_tecnicos;?></td>
                            <td><?php echo $nombre_usuario;?> </td>
                            <td style="text-align:center"><?php echo $usuario->FechaHoraSpain($fecha_creacion);?></td>
                        </tr>
                <?php
                    }
                ?>
                </table>
            </div>

            <?php
            // PAGINACIÓN
            if((isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1 or $buscar == 1) and $resultadosBusqueda != NULL) { ?>
                <div style="font: bold 11px Verdana,Arial; margin: 0 auto; padding: 10px 0; width: 350px; text-align: center;">
                    <?php
                    if(($pg_pagina - 1) > 0) { ?>
                        <a href="recuperar_escandallos.php?pg=1&realizandoBusqueda=1<?php if($esAdminGlobal || $esAdministradorGes){?>&sedes=<?php echo $_SESSION["id_sede_recuperar_escandallos"];}?>&id_usuario=<?php echo $_SESSION["id_usuario_recuperar_escandallos"];?>&codigo=<?php echo $_SESSION["codigo_recuperar_escandallos"];?>&id_produccion=<?php echo $_SESSION["id_produccion_recuperar_escandallos"];?>&alias_op=<?php echo $_SESSION["alias_op_recuperar_escandallos"];?>&num_tecnicos=<?php echo $_GET["num_tecnicos"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_recuperar_escandallos"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_recuperar_escandallos"];?>">Primera&nbsp&nbsp&nbsp</a>
                        <a href="recuperar_escandallos.php?pg=<?php echo $pg_pagina - 1;?>&realizandoBusqueda=1<?php if($esAdminGlobal || $esAdministradorGes){?>&sedes=<?php echo $_SESSION["id_sede_recuperar_escandallos"];}?>&id_usuario=<?php echo $_SESSION["id_usuario_recuperar_escandallos"];?>&codigo=<?php echo $_SESSION["codigo_recuperar_escandallos"];?>&id_produccion=<?php echo $_SESSION["id_produccion_recuperar_escandallos"];?>&alias_op=<?php echo $_SESSION["alias_op_recuperar_escandallos"];?>&num_tecnicos=<?php echo $_GET["num_tecnicos"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_recuperar_escandallos"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_recuperar_escandallos"];?>">&nbspAnterior</a>
                    <?php
                    }
                    else {
                        echo 'Primera&nbsp;&nbsp;&nbsp;Anterior';
                    }
                    echo ' &nbsp;&nbsp;&nbsp;['.$pg_pagina.' / '.$pg_totalPaginas.']&nbsp;&nbsp;&nbsp;';
                    if($pg_pagina < $pg_totalPaginas) { ?>
                        <a href="recuperar_escandallos.php?pg=<?php echo $pg_pagina + 1;?>&realizandoBusqueda=1<?php if($esAdminGlobal || $esAdministradorGes){?>&sedes=<?php echo $_SESSION["id_sede_recuperar_escandallos"];}?>&id_usuario=<?php echo $_SESSION["id_usuario_recuperar_escandallos"];?>&codigo=<?php echo $_SESSION["codigo_recuperar_escandallos"];?>&id_produccion=<?php echo $_SESSION["id_produccion_recuperar_escandallos"];?>&alias_op=<?php echo $_SESSION["alias_op_recuperar_escandallos"];?>&num_tecnicos=<?php echo $_GET["num_tecnicos"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_recuperar_escandallos"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_recuperar_escandallos"];?>">Siguiente&nbsp&nbsp&nbsp</a>
                        <a href="recuperar_escandallos.php?pg=<?php echo $pg_totalPaginas;?>&realizandoBusqueda=1<?php if($esAdminGlobal || $esAdministradorGes){?>&sedes=<?php echo $_SESSION["id_sede_recuperar_escandallos"];}?>&id_usuario=<?php echo $_SESSION["id_usuario_recuperar_escandallos"];?>&codigo=<?php echo $_SESSION["codigo_recuperar_escandallos"];?>&id_produccion=<?php echo $_SESSION["id_produccion_recuperar_escandallos"];?>&alias_op=<?php echo $_SESSION["alias_op_recuperar_escandallos"];?>&num_tecnicos=<?php echo $_GET["num_tecnicos"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_recuperar_escandallos"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_recuperar_escandallos"];?>">Última</a>
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

<?php include ('../includes/footer.php'); ?>
