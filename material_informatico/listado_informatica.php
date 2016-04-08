<?php
// Listado de todos los componentes informáticos de la Oficina 
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/material_informatico/material_informatico.class.php");
include("../classes/material_informatico/listado_material_informatico.class.php");
permiso(38);

$funciones = new Funciones(); 
$almacen = new Almacen();
$materialInformatico = new MaterialInformatico();
$listadoMaterialInformatico = new ListadoMaterialInformatico();

// Establecemos los parámetros de la paginación
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

if(isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) {
    $mostrar_tabla = true;
    $id_tipo = $_GET["tipo_material"];
    $id_subtipo = $_GET["subtipo_material"];
    $num_serie = addslashes($_GET["num_serie"]);
    $descripcion = addslashes($_GET["descripcion"]);
    $precio = $_GET["precio"];
    $asignado_a = addslashes($_GET["asignado_a"]);
    $estado = $_GET["estado"];
    $observaciones = addslashes($_GET["observaciones"]);
    $fecha_desde = $_GET["fecha_desde"];
    $fecha_hasta = $_GET["fecha_hasta"];
    $unidades_creadas = $_GET["unidades"];

    // Preparamos las fechas para la consulta
    if($fecha_desde != "") $fecha_desde = $funciones->cFechaMy($fecha_desde);
    if($fecha_hasta != "") $fecha_hasta = $funciones->cFechaMy($fecha_hasta);

    // Se pasan los datos del buscador a la clase del listado y se realiza la consulta a la base de datos sin parámetros de paginación
    $listadoMaterialInformatico->setValores($id_tipo,$id_subtipo,$num_serie,$descripcion,$precio,$asignado_a,$estado,$observaciones,$fecha_desde,$fecha_hasta,'');
    $listadoMaterialInformatico->realizarConsulta();
    $resultadosBusqueda = $listadoMaterialInformatico->materiales_informaticos;

    $_SESSION["tipo_material_xls_material_informatico"] = $id_tipo;
    $_SESSION["subtipo_material_xls_material_informatico"] = $id_subtipo;
    $_SESSION["num_serie_xls_material_informatico"] = $num_serie;
    $_SESSION["descripcion_xls_material_informatico"] = $descripcion;
    $_SESSION["precio_xls_material_informatico"] = $precio;
    $_SESSION["asignado_a_xls_material_informatico"] = $asignado_a;
    $_SESSION["estado_xls_material_informatico"] = $estado;
    $_SESSION["observaciones_xls_material_informatico"] = $observaciones;
    $_SESSION["fecha_desde_xls_material_informatico"] = $fecha_desde;
    $_SESSION["fecha_hasta_xls_material_informatico"] = $fecha_hasta;

    $num_materiales_encontrados = count($resultadosBusqueda);
    $pg_totalPaginas = ceil(count($resultadosBusqueda) / $pg_registros);

    $listadoMaterialInformatico->setValores($id_tipo,$id_subtipo,$num_serie,$descripcion,$precio,$asignado_a,$estado,$observaciones,$fecha_desde,$fecha_hasta,$paginacion);
    $listadoMaterialInformatico->realizarConsulta();
    $resultadosBusqueda = $listadoMaterialInformatico->materiales_informaticos;

    // Covertimos de nuevo las fechas
    $fecha_desde = $funciones->cFechaNormal($fecha_desde);
    $fecha_hasta = $funciones->cFechaNormal($fecha_hasta);
    
    $_SESSION["tipo_material_material_informatico"] = $id_tipo;
    $_SESSION["subtipo_material_material_informatico"] = $id_subtipo;
    $_SESSION["num_serie_material_informatico"] = stripslashes(htmlspecialchars($num_serie));
    $_SESSION["descripcion_material_informatico"] = stripslashes(htmlspecialchars($descripcion));
    $_SESSION["precio_material_informatico"] = $precio;
    $_SESSION["asignado_a_material_informatico"] = stripslashes(htmlspecialchars($asignado_a));
    $_SESSION["estado_material_informatico"] = $estado;
    $_SESSION["observaciones_material_informatico"] = stripslashes(htmlspecialchars($observaciones));
    $_SESSION["fecha_desde_material_informatico"] = $fecha_desde;
    $_SESSION["fecha_hasta_material_informatico"] = $fecha_hasta;
}
$max_caracteres = 35;
$titulo_pagina = "Material Informático > Listado Informática";
$pagina = "listado_informatica";
include("../includes/header.php");
echo '<script type="text/javascript" src="../js/material_informatico/material_informatico.js"></script>';
$fecha_actual = date('d/m/Y');
?>

<div class="separador"></div>
<?php include("../includes/menu_material_informatico.php"); ?>

<div id="ContenedorCentral">
    <div id="ContenedorSidebar">
        <?php include ("../includes/sidebar.php"); ?>
    </div>

    <h3>Listado Material Inform&aacute;tico</h3>
    <h4></h4>

    <form id="buscadorMaterialInformatico" name="buscadorMaterialInformatico" action="listado_informatica.php" method="get" class="Buscador">
    <table style="border:0;">
    <tr style="border:0;">
        <td>
            <div class="Label">Tipo Material</div>
            <select id="tipo_material" name="tipo_material" class="BuscadorInput">
                <?php
                // Cargamos los nombres de los tipos de material
                $resultados_tipo = $materialInformatico->dameTiposMateriales();

                echo '<option></option>';
                for($i=0;$i<count($resultados_tipo);$i++){
                    $id_tipo = $resultados_tipo[$i]["id_tipo"];
                    $nombre_tipo = $resultados_tipo[$i]["nombre"];

                    echo '<option value="'.$id_tipo.'"';
                    if($id_tipo == $_SESSION["tipo_material_material_informatico"]){
                        echo ' selected="selected" ';
                    }
                    echo '>'.utf8_encode($nombre_tipo).'</option>';
                }
                ?>
            </select>
        </td>
        <td>
            <div class="Label">Subtipo Material</div>
            <select id="subtipo_material" name="subtipo_material" class="BuscadorInput">
                <?php
                    // Cargamos los nombres de los tipos de material
                    $resultados_subtipo = $materialInformatico->dameSubtiposMateriales();

                    echo '<option></option>';
                    for($i=0;$i<count($resultados_subtipo);$i++){
                        $id_subtipo = $resultados_subtipo[$i]["id_subtipo"];
                        $nombre_subtipo = $resultados_subtipo[$i]["subtipo"];

                        echo '<option value="'.$id_subtipo.'"';
                        if($id_subtipo == $_SESSION["subtipo_material_material_informatico"]){
                            echo ' selected="selected" ';
                        }
                        echo '>'.utf8_encode($nombre_subtipo).'</option>';
                    }
                ?>
            </select>
        </td>
        <td>
            <div class="Label">N&uacute;mero de Serie</div>
            <input type="text" id="num_serie" name="num_serie" class="BuscadorInput" maxlength="12" value="<?php echo $_SESSION["num_serie_material_informatico"];?>"/>
        </td>
    </tr>
    <tr style="border:0;">
        <td>
            <div class="Label">Descripci&oacute;n</div>
            <input type="text" id="descripcion" name="descripcion" class="BuscadorInput" maxlength="50" value="<?php echo $_SESSION["descripcion_material_informatico"];?>"/>
        </td>
        <td>
            <div class="Label">Precio</div>
            <input type="text" id="precio" name="precio" class="BuscadorInput" maxlength="8" value="<?php echo $_SESSION["precio_material_informatico"];?>" onkeypress="return blockNonNumbers(this, event, true, true);" />
        </td>
        <td>
            <div class="Label">Asignado a</div>
            <input type="text" id="asignado_a" name="asignado_a" class="BuscadorInput" maxlength="25" value="<?php echo $_SESSION["asignado_a_material_informatico"];?>"/>
        </td>
    </tr>
    <tr style="border:0;">
        <td>
            <div class="Label">Observaciones</div>
            <input type="text" id="observaciones" name="observaciones" class="BuscadorInput" maxlength="50" value="<?php echo $_SESSION["observaciones_material_informatico"];?>"/>
        </td>
        <td>
            <div class="Label">Fecha desde</div>
            <input type="text" name="fecha_desde" id="datepicker_material_informatico_desde" class="fechaCal" value="<?php echo $_SESSION["fecha_desde_material_informatico"];?>"/>
        </td>
        <td>
            <div class="Label">Fecha hasta</div>
            <input type="text" name="fecha_hasta" id="datepicker_material_informatico_hasta" class="fechaCal" value="<?php echo $_SESSION["fecha_hasta_material_informatico"];?>"/>
        </td>
    </tr>
    <tr style="border:0;">
        <td>
            <div class="Label">Estado</div>
            <select id="estado" name="estado" class="BuscadorInput">
                <option></option>
                <option <?php if($_SESSION["estado_material_informatico"] == "CREADO") { ?> selected="selected" <?php } ?> value="STOCK">CREADO</option>
                <option <?php if($_SESSION["estado_material_informatico"] == "STOCK") { ?> selected="selected" <?php } ?> value="STOCK">STOCK</option>
                <option <?php if($_SESSION["estado_material_informatico"] == "AVERIADO") { ?> selected="selected" <?php } ?> value="AVERIADO">AVERIADO</option>
                <option <?php if($_SESSION["estado_material_informatico"] == "EN REPARACION") { ?> selected="selected" <?php } ?> value="EN REPARACION">EN REPARACI&Oacute;N</option>
                <option <?php if($_SESSION["estado_material_informatico"] == "EN USO") { ?> selected="selected" <?php } ?> value="EN USO">EN USO</option>
            </select>
        </td>
        <td style="vertical-align: bottom; text-align:center;">
            <input type="hidden" id="realizandoBusqueda" name="realizandoBusqueda" value="1" />
            <input type="submit" id="" name="" class="" value="Buscar" />
        </td>
        <td></td>
    </tr>    
    </table>
    <br />
    </form>

    <div class="ContenedorBotonCrear">
    <?php
        if($mostrar_tabla){
            if($_GET["matInf"] == "creado"){
                echo '<div class="mensaje">El material inform&aacute;tico se ha creado correctamente</div>';
            }
            if($_GET["matInf"] == "modificado"){
                echo '<div class="mensaje">El material inform&aacute;tico se ha modificado correctamente</div>';

                if($num_serie != ""){?>
                    <script type="text/javascript">alert("IMPRIMA LA NUEVA PEGATINA\n\n NUM_SERIE:<?php echo $num_serie;?>");</script>
            <?php
                }
            }

            if($_GET["matInf"] == "eliminado"){
                echo '<div class="mensaje">El material inform&aacute;tico se ha eliminado correctamente</div>';
            }

            if($num_materiales_encontrados == NULL or $num_materiales_encontrados == 0){
                echo '<div class="mensaje">No se encontraron materiales inform&aacute;ticos</div>';
                $mostrar_tabla = false;
            }
            else if ($num_materiales_encontrados == 1){
                echo '<div class="mensaje">Se encontr&oacute; 1 material inform&aacute;tico</div>';
            }
            else{
                echo '<div class="mensaje">Se encontraron '.$num_materiales_encontrados.' materiales inform&aacute;ticos</div>';
            }
        }
        ?>
    </div>

    <?php
		if($mostrar_tabla) { ?>
           <div class="CapaTabla">
                <table>
                <tr>
                    <th style="text-align:center;">NUM. SERIE</th>
                    <th style="text-align:left;">TIPO MATERIAL</th>                    
                    <th style="text-align:left;">SUBTIPO</th>                    
                    <th style="text-align:left;">DESCRIPCI&Oacute;N</th>
                    <th style="text-align:right;">PRECIO</th>
                    <th style="text-align:left;">ASIGNADO</th>                    
                    <th style="text-align:left;">ESTADO</th>
                    <th style="text-align:left;">OBSERVACIONES</th>
                    <th style="text-align:left;">F. CREADO</th>
                    <th style="text-align:center;">ELIMINAR</th>
                </tr>
                <?php
                    for($i=0;$i<count($resultadosBusqueda);$i++){ 
                        $id_material = $resultadosBusqueda[$i]["id_material"];

                        $materialInformatico->cargaDatosMaterialId($id_material);
                        $num_serie = $materialInformatico->num_serie; 
                        $id_tipo = $materialInformatico->id_tipo;
                        $id_subtipo = $materialInformatico->id_subtipo; 
                        $descripcion = $materialInformatico->descripcion;
                        $precio = $materialInformatico->precio;
                        $asignado_a = $materialInformatico->asignado_a;
                        $estado = $materialInformatico->estado;
                        $observaciones = $materialInformatico->observaciones;
                        $fecha_creacion = $funciones->cFechaNormal($materialInformatico->fecha_creado);
                        
                        $res_tipo_material = $materialInformatico->dameTipoMaterial($id_tipo);
                        $nombre_tipo_material = utf8_encode($res_tipo_material[0]["nombre"]);
                        $res_subtipo_material = $materialInformatico->dameSubtipoMaterial($id_subtipo);
                        $nombre_subtipo_material = utf8_encode($res_subtipo_material[0]["subtipo"]);
                        if(empty($nombre_subtipo_material)) $nombre_subtipo_material = "-";

                        if(!empty($unidades_creadas) && $unidades_creadas != 0 && $dif==0) $color = "green";
                        else $color = "black";

                        if($precio > 200){
                            $color_precio = "peru";
                        }
                        else $color_precio = "black";  ?>

                        <tr style="color:<?php echo $color_fila; ?>">
                            <td style="text-align:center;">
                                <a href="../material_informatico/mod_material.php?id_material=<?php echo $id_material;?>"><?php echo $num_serie;?></a>
                            </td>        
                            <td style="text-align:left; color:<?php echo $color;?>"><?php echo $nombre_tipo_material;?></td>
                            <td style="text-align:left; color:<?php echo $color;?>"><?php echo $nombre_subtipo_material;?></td>                    
                            <td style="text-align:left; color:<?php echo $color;?>"><?php echo $descripcion;?></td>
                            <td style="text-align:right; color:<?php echo $color_precio;?>"><?php echo $precio;?></td>
                            <td style="text-align:left; color:<?php echo $color;?>"><?php echo $asignado_a;?></td>                    
                            <td style="text-align:left; color:<?php echo $color;?>"><?php echo $estado;?></td>
                            <td style="text-align:left; color:<?php echo $color;?>"><?php echo $observaciones;?></td>
                            <td style="text-align:left; color:<?php echo $color;?>"><?php echo $fecha_creacion;?></td>
                            <td style="text-align:center;">
                                <input type="button" class="BotonEliminar" value="ELIMINAR" onclick="javascript: if(confirm('¿Desea eliminar el material?')) { window.location.href='eliminar_material.php?id=<?php echo $materialInformatico->id_material;?>' } else { void('') };" />
                            </td>
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
                        <a href="listado_informatica.php?pg=1&realizandoBusqueda=1&tipo_material=<?php echo $_SESSION["tipo_material_material_informatico"];?>&subtipo_material=<?php echo $_SESSION["subtipo_material_material_informatico"];?>&num_serie=<?php echo $_SESSION["num_serie_material_informatico"];?>&descripcion=<?php echo $_SESSION["descripcion_material_informatico"];?>&precio=<?php echo $_SESSION["precio_material_informatico"];?>&asignado_a=<?php echo $_SESSION["asignado_a_material_informatico"];?>&observaciones=<?php echo $_SESSION["observaciones_material_informatico"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_material_informatico"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_material_informatico"];?>&estado=<?php echo $_SESSION["estado_material_informatico"];?>">Primera&nbsp&nbsp&nbsp</a>
                        <a href="listado_informatica.php?pg=<?php echo $pg_pagina - 1;?>&realizandoBusqueda=1&tipo_material=<?php echo $_SESSION["tipo_material_material_informatico"];?>&subtipo_material=<?php echo $_SESSION["subtipo_material_material_informatico"];?>&num_serie=<?php echo $_SESSION["num_serie_material_informatico"];?>&descripcion=<?php echo $_SESSION["descripcion_material_informatico"];?>&precio=<?php echo $_SESSION["precio_material_informatico"];?>&asignado_a=<?php echo $_SESSION["asignado_a_material_informatico"];?>&observaciones=<?php echo $_SESSION["observaciones_material_informatico"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_material_informatico"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_material_informatico"];?>&estado=<?php echo $_SESSION["estado_material_informatico"];?>"> Anterior</a>
                <?php
                    }
                    else {
                        echo 'Primera&nbsp;&nbsp;&nbsp;Anterior';
                    }
                    echo ' &nbsp;&nbsp;&nbsp;['.$pg_pagina.' / '.$pg_totalPaginas.']&nbsp;&nbsp;&nbsp;';
                    if($pg_pagina < $pg_totalPaginas) { ?>
                        <a href="listado_informatica.php?pg=<?php echo $pg_pagina + 1;?>&realizandoBusqueda=1&tipo_material=<?php echo $_SESSION["tipo_material_material_informatico"];?>&subtipo_material=<?php echo $_SESSION["subtipo_material_material_informatico"];?>&num_serie=<?php echo $_SESSION["num_serie_material_informatico"];?>&descripcion=<?php echo $_SESSION["descripcion_material_informatico"];?>&precio=<?php echo $_SESSION["precio_material_informatico"];?>&asignado_a=<?php echo $_SESSION["asignado_a_material_informatico"];?>&observaciones=<?php echo $_SESSION["observaciones_material_informatico"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_material_informatico"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_material_informatico"];?>&estado=<?php echo $_SESSION["estado_material_informatico"];?>">Siguiente&nbsp&nbsp&nbsp</a>
                        <a href="listado_informatica.php?pg=<?php echo $pg_totalPaginas;?>&realizandoBusqueda=1&tipo_material=<?php echo $_SESSION["tipo_material_material_informatico"];?>&subtipo_material=<?php echo $_SESSION["subtipo_material_material_informatico"];?>&num_serie=<?php echo $_SESSION["num_serie_material_informatico"];?>&descripcion=<?php echo $_SESSION["descripcion_material_informatico"];?>&precio=<?php echo $_SESSION["precio_material_informatico"];?>&asignado_a=<?php echo $_SESSION["asignado_a_material_informatico"];?>&observaciones=<?php echo $_SESSION["observaciones_material_informatico"];?>&fecha_desde=<?php echo $_SESSION["fecha_desde_material_informatico"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_material_informatico"];?>&estado=<?php echo $_SESSION["estado_material_informatico"];?>">Última</a>
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
            if($num_materiales_encontrados != NULL and $num_materiales_encontrados != 0){ ?>
                <div class="ContenedorBotonCrear"><input type="button" id="descargar_XLS" name="descargar_XLS" value="Descargar XLS" class="BotonEliminar" onclick="javascript:descargar_XLS_Materiales();"/></div>
    <?php
            }
        }
    ?>  
</div>
<?php include ("../includes/footer.php"); ?>