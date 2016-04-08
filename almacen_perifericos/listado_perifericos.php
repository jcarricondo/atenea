<?php
// Listado de todos los periféricos de almacen
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/control_usuario.class.php");
include("../classes/sede/sede.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/almacen_perifericos/periferico_almacen.class.php");
include("../classes/almacen_perifericos/listado_perifericos_almacen.class.php");
permiso(31);

$titulo_pagina = "Almacen Periféricos > Listado Periféricos";
$pagina = "listado_perifericos";
include("../includes/header.php");
echo '<script type="text/javascript" src="../js/almacen_perifericos/almacen_perifericos.js"></script>';

$control_usuario = new Control_Usuario();
$sede = new Sede();
$almacen = new Almacen();
$perifericoAlmacen = new PerifericoAlmacen();
$listadoPerifericos = new listadoPerifericosAlmacen();

$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$id_almacen_usuario = $_SESSION["AT_id_almacen"];
$id_sede = $control_usuario->dameSedeSegunUsuario($id_tipo_usuario,$id_almacen_usuario);
$_SESSION["id_almacen_almacen_material"] = $id_almacen_usuario;

$esAdminGlobal = $control_usuario->esAdministradorGlobal($id_tipo_usuario);
$filtroSede = $esAdminGlobal;

// Predeterminado si el usuario sin sede asignada no escogió ninguna
if(empty($id_sede)) $id_sede = 1;
if(empty($id_almacen_usuario)) $_SESSION["id_almacen_perifericos_almacen"] = 1;
else $_SESSION["id_almacen_perifericos_almacen"] = $_SESSION["AT_id_almacen"];

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

if(isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) {
    $mostrar_tabla = true;
    $num_serie = addslashes($_GET["num_serie"]);
    $tipo_periferico = $_GET["tipo_periferico"];
    $estado = $_GET["estado_periferico"];
    if($filtroSede) $id_sede = $_GET["sedes"];

    if($_GET["id_periferico"] != NULL){
        $perifericoAlmacen->cargaDatosPerifericoId($_GET["id_periferico"]);
        $id_almacen = $perifericoAlmacen->id_almacen;
    } 
    else {
        $id_almacen = $_GET["almacenes"];
    }

    // Guardamos en una variable los campos para mostrarlos después de la búsqueda
    $num_serie_ant = $num_serie;
    $tipo_periferico_ant = $tipo_periferico;
    $estado_ant = $estado;
    $id_almacen_ant = $id_almacen;

    // Se pasan los datos del buscador a la clase del listado y se realiza la consulta a la base de datos sin parámetros de paginación
    $listadoPerifericos->setValores($num_serie,$tipo_periferico,$estado,'',0,$id_almacen,$id_sede);
    $listadoPerifericos->realizarConsulta();
    $resultadosBusqueda = $listadoPerifericos->perifericos;

    $_SESSION["num_serie_xls_perifericos_almacen"] = $num_serie;
    $_SESSION["tipo_periferico_xls_perifericos_almacen"] = $tipo_periferico;
    $_SESSION["estados_xls_perifericos_almacen"] = $estado;
    $_SESSION["id_almacen_xls_perifericos_almacen"] = $id_almacen;
    $_SESSION["id_sede_xls_perifericos_almacen"] = $id_sede;

    // Guardamos el número total de periféricos encontrados
    $num_perifericos_encontrados = count($resultadosBusqueda);
    $pg_totalPaginas = ceil(count($resultadosBusqueda) / $pg_registros);

    $listadoPerifericos->setValores($num_serie,$tipo_periferico,$estado,$paginacion,0,$id_almacen,$id_sede);
    $listadoPerifericos->realizarConsulta();
    $resultadosBusqueda = $listadoPerifericos->perifericos;

    // Mostramos los valores iniciales de búsqueda
    $num_serie = $num_serie_ant;
    $tipo_periferico = $tipo_periferico_ant;
    $estado = $estado_ant;
    $id_almacen = $id_almacen_ant;

    $_SESSION["num_serie_perifericos_almacen"] = stripslashes(htmlspecialchars($num_serie));
    $_SESSION["tipo_periferico_perifericos_almacen"] = $tipo_periferico;
    $_SESSION["estados_perifericos_almacen"] = $estado;
    $_SESSION["id_almacen_perifericos_almacen"] = $id_almacen;
    $_SESSION["id_sede_perifericos_almacen"] = $id_sede;
}
$max_caracteres = 35;
?>

<div class="separador"></div>
<?php include("../includes/menu_almacen_perifericos.php"); ?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>

    <h3>Listado Perif&eacute;ricos</h3>
    <h4></h4>

    <form id="buscadorPerifericos" name="buscadorPerifericos" action="listado_perifericos.php" method="get" class="Buscador">
    <table style="border:0;">
    <?php
        if($filtroSede){?>
            <tr style="border:0;">
                <td style="width:33%; vertical-align: top;">
                    <div class="Label">Sede</div>
                    <select id="sedes" name="sedes" class="BuscadorInputAlmacen" onchange="cargaAlmacenes(this.value)" >
                        <?php
                            // Obtenemos todas las sedes
                            $resultados_sedes = $sede->dameSedesMantenimiento();
                            for($i=0;$i<count($resultados_sedes);$i++) {
                                $id_sede_res = $resultados_sedes[$i]["id_sede"];
                                $nombre_sede = $resultados_sedes[$i]["sede"]; ?>

                                <option value="<?php echo $id_sede_res;?>" <?php if($id_sede_res == $id_sede) echo 'selected="selected"'; ?>>
                                    <?php echo $nombre_sede;?>
                                </option>
                        <?php
                            }
                        ?>
                    </select>
                </td>
                <td style="width:33%;"></td>
                <td style="width:33%;"></td>
            </tr>
        <?php
        }
    ?>
        <tr style="border:0;">
            <td style="width:33%; vertical-align: top;">
                <div class="Label">Almacen</div>
                <div id="capaAlmacenes">
                    <select id="almacenes" name="almacenes" class="BuscadorInputAlmacen">
                        <option value="">Seleccionar</option>
                        <?php
                            // Obtenemos los almacenes de esa sede
                            $res_almacenes = $sede->dameAlmacenesMantenimientoSede($id_sede);
                            for($i=0;$i<count($res_almacenes);$i++){
                                $id_almacen_bus = $res_almacenes[$i]["id_almacen"];
                                $nombre_almacen = $res_almacenes[$i]["almacen"]; ?>
                                <option value="<?php echo $id_almacen_bus;?>" <?php if($_SESSION["id_almacen_perifericos_almacen"] == $id_almacen_bus) echo 'selected="selected" '?>>
                                    <?php echo $nombre_almacen;?>
                                </option>
                        <?php
                            }
                        ?>
                    </select>
                </div>
            </td>
            <td style="width:33%;">
                <div class="Label">N&uacute;mero de Serie</div>
                <input type="text" id="num_serie" name="num_serie" class="BuscadorInputAlmacen" maxlength="30" value="<?php echo $_SESSION["num_serie_perifericos_almacen"];?>"/>
            </td>
            <td style="width:33%;">
                <div class="Label">Estado</div>
                <select id="estado_periferico" name="estado_periferico" class="BuscadorInputAlmacen">
                    <option></option>
                    <?php
                    $array_estados = array("OPERATIVO","AVERIADO","EN REPARACION");
                    for($i=0;$i<count($array_estados);$i++){
                        echo '<option';
                        if($array_estados[$i] == $_SESSION["estados_perifericos_almacen"]){
                            echo ' selected="selected" ';
                        }
                        echo '>'.$array_estados[$i].'</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr style="border:0;">
            <td style="width:33%;">
                <div class="Label">Tipo</div>
                <select id="tipo_periferico" name="tipo_periferico" class="BuscadorInputAlmacen">
                    <?php
                    // Cargamos los nombres de los tipos de periféricos
                    $resultados_tipo = $perifericoAlmacen->dameDatosTipoPerifericos();

                    echo '<option></option>';
                    for($i=0;$i<count($resultados_tipo);$i++){
                        $id_tipo_periferico = $resultados_tipo[$i]["id"];
                        $nombre_tipo = $resultados_tipo[$i]["nombre"];

                        echo '<option value="'.$id_tipo_periferico.'"';
                        if($id_tipo_periferico == $_SESSION["tipo_periferico_perifericos_almacen"]){
                            echo ' selected="selected" ';
                        }
                        echo '>'.$nombre_tipo.'</option>';
                    }
                    ?>
                </select>
            </td>
            <td style="width:33%;"></td>
          	<td style="width:33%;"></td>
        </tr>
        <tr style="border:0;">
            <td style="width:33%; vertical-align: bottom;">
                <input type="hidden" id="realizandoBusqueda" name="realizandoBusqueda" value="1" />
                <input type="submit" id="" name="" class="" value="Buscar" />
            </td>
            <td style="width:33%; vertical-align: top;"></td>
            <td style="width:33%; vertical-align: top;"></td>
        </tr>
    </table>
    <br />
    </form>
    <br/>

    <div class="ContenedorBotonCrear">
        <?php
            if($mostrar_tabla){
                if($num_perifericos_encontrados == NULL or $num_perifericos_encontrados == 0){
                    echo '<div class="mensaje">No se encontraron periféricos</div>';
                    $mostrar_tabla = false;
                }
                else if ($num_perifericos_encontrados == 1){
                    echo '<div class="mensaje">Se encontró 1 periférico</div>';
                }
                else{
                    echo '<div class="mensaje">Se encontraron '.$num_perifericos_encontrados.' periféricos</div>';
                }
            }
        ?>
    </div>

    <?php
		if($mostrar_tabla) { ?>
           <div class="CapaTabla">
                <table>
                    <tr>
                        <th style="width:20%; text-align:center;">NUM. SERIE</th>
                        <th style="width:20%;">TIPO PERIFERICO</th>
                        <th style="width:20%;">ALMACEN</th>
                        <th style="width:20%;">ESTADO</th>
                        <th style="width:20%;">COMENTARIOS</th>
                    </tr>
                    <?php
                    	for($i=0;$i<count($resultadosBusqueda);$i++){
                            $id_periferico = $resultadosBusqueda[$i]["id_periferico"];
                            $perifericoAlmacen->cargaDatosPerifericoId($id_periferico);

                            $numero_serie = $perifericoAlmacen->numero_serie;
                            $tipo_periferico = $perifericoAlmacen->tipo_periferico;
                            $estado = $perifericoAlmacen->estado;
                            $comentarios = $perifericoAlmacen->comentarios;
                            $id_almacen = $perifericoAlmacen->id_almacen;

                            $almacen->cargaDatosAlmacenId($id_almacen);
                            $nombre_tllr = $almacen->nombre;

                            $nombre_tipo = $perifericoAlmacen->dameNombreTipoPeriferico($tipo_periferico);
                            $nombre_tipo = $nombre_tipo["nombre"]; ?>
		                    <tr>
		                        <td style="width:20%; text-align:center;">
                                    <a href="../almacen_perifericos/ficha_periferico.php?id_periferico=<?php echo $id_periferico; ?>"> 
                                        <?php echo $numero_serie;?>
                                    </a>    
                                </td>
		                        <td style="width:20%;"><?php echo $nombre_tipo;?></td>
                                <td style="width:20%;"><?php echo $nombre_tllr;?></td>
                                <td style="width:20%;"><?php echo $estado;?></td>
		                        <td style="width:20%"><?php echo $comentarios;?></td>
		                    </tr>
                    <?php
                        }
                    ?>
                </table>
            </div>
    <?php
		}
        // PAGINACIÓN
        if((isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) and $resultadosBusqueda != NULL) { ?>
            <div style="font: bold 11px Verdana,Arial; margin: 0 auto; padding: 10px 0; width: 350px; text-align: center;">
                <?php
                    if(($pg_pagina - 1) > 0) { ?>
                        <a href="listado_perifericos.php?pg=1&realizandoBusqueda=1&num_serie=<?php echo $_SESSION["num_serie_perifericos_almacen"];?>&tipo_periferico=<?php echo $_SESSION["tipo_periferico_perifericos_almacen"];?>&estado_periferico=<?php echo $_SESSION["estados_perifericos_almacen"];?>&almacenes=<?php echo $_SESSION["id_almacen_perifericos_almacen"];?>&sedes=<?php echo $_SESSION["id_sede_perifericos_almacen"];?>">Primera&nbsp&nbsp&nbsp</a>
                        <a href="listado_perifericos.php?pg=<?php echo $pg_pagina - 1;?>&realizandoBusqueda=1&num_serie=<?php echo $_SESSION["num_serie_perifericos_almacen"];?>&tipo_periferico=<?php echo $_SESSION["tipo_periferico_perifericos_almacen"];?>&estado_periferico=<?php echo $_SESSION["estados_perifericos_almacen"];?>&almacenes=<?php echo $_SESSION["id_almacen_perifericos_almacen"];?>&sedes=<?php echo $_SESSION["id_sede_perifericos_almacen"];?>"> Anterior</a>
                <?php
                    }
                    else {
                        echo 'Primera&nbsp;&nbsp;&nbsp;Anterior';
                    }

                    echo ' &nbsp;&nbsp;&nbsp;['.$pg_pagina.' / '.$pg_totalPaginas.']&nbsp;&nbsp;&nbsp;';
                    if($pg_pagina < $pg_totalPaginas) { ?>
                        <a href="listado_perifericos.php?pg=<?php echo $pg_pagina + 1;?>&realizandoBusqueda=1&num_serie=<?php echo $_SESSION["num_serie_perifericos_almacen"];?>&tipo_periferico=<?php echo $_SESSION["tipo_periferico_perifericos_almacen"];?>&estado_periferico=<?php echo $_SESSION["estados_perifericos_almacen"];?>&almacenes=<?php echo $_SESSION["id_almacen_perifericos_almacen"];?>&sedes=<?php echo $_SESSION["id_sede_perifericos_almacen"];?>">Siguiente&nbsp&nbsp&nbsp</a>
                        <a href="listado_perifericos.php?pg=<?php echo $pg_totalPaginas;;?>&realizandoBusqueda=1&num_serie=<?php echo $_SESSION["num_serie_perifericos_almacen"];?>&tipo_periferico=<?php echo $_SESSION["tipo_periferico_perifericos_almacen"];?>&estado_periferico=<?php echo $_SESSION["estados_perifericos_almacen"];?>&almacenes=<?php echo $_SESSION["id_almacen_perifericos_almacen"]?>&sedes=<?php echo $_SESSION["id_sede_perifericos_almacen"];?>">Última</a>
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
        if(isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) {
            if($num_perifericos_encontrados != NULL and $num_perifericos_encontrados != 0){ ?>
    		  <div class="ContenedorBotonCrear"><input type="button" id="descargar_XLS" name="descargar_XLS" value="Descargar XLS" class="BotonEliminar" onclick="javascript:descargar_XLS_Perifericos();"/></div>
    <?php
            }
		}
	?>  
    </form>
</div>
<?php include ("../includes/footer.php"); ?>
