<?php
// Listado de todos las familias de materiales informáticos en STOCK 
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/material_informatico/material_informatico.class.php");
include("../classes/material_informatico/listado_stock_informatico.class.php");
permiso(38);

$funciones = new Funciones(); 
$almacen = new Almacen();
$materialInformatico = new MaterialInformatico();
$listadoStockInformatico = new ListadoStockInformatico();

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
    $unidades = $_GET["unidades"];
    $min_unidades = $_GET["min_unidades"];
    $unidades_pedido = $_GET["unidades_pedido"];

    // Se pasan los datos del buscador a la clase del listado y se realiza la consulta a la base de datos sin paginación
    $listadoStockInformatico->setValores($id_tipo,$id_subtipo,$unidades,$min_unidades,$unidades_pedido,'');
    $listadoStockInformatico->realizarConsulta();
    $resultadosBusqueda = $listadoStockInformatico->materiales_informaticos;

    $_SESSION["tipo_material_xls_stock_informatico"] = $id_tipo;
    $_SESSION["subtipo_material_xls_stock_informatico"] = $id_subtipo;
    $_SESSION["unidades_xls_stock_informatico"] = $unidades;
    $_SESSION["min_unidades_xls_stock_informatico"] = $min_unidades;
    $_SESSION["unidades_pedido_xls_stock_informatico"] = $unidades_pedido;

    $num_resultados_encontrados = count($resultadosBusqueda);
    $pg_totalPaginas = ceil(count($resultadosBusqueda) / $pg_registros);

    // Se pasan los datos del buscador a la clase del listado y se realiza la consulta a la base de datos
    $listadoStockInformatico->setValores($id_tipo,$id_subtipo,$unidades,$min_unidades,$unidades_pedido,$paginacion);
    $listadoStockInformatico->realizarConsulta();
    $resultadosBusqueda = $listadoStockInformatico->materiales_informaticos;

    $_SESSION["tipo_material_stock_informatico"] = $id_tipo;
    $_SESSION["subtipo_material_stock_informatico"] = $id_subtipo;
    $_SESSION["unidades_stock_informatico"] = $unidades;
    $_SESSION["min_unidades_stock_informatico"] = $min_unidades;
    $_SESSION["unidades_pedido_stock_informatico"] = $unidades_pedido;

}
$max_caracteres = 35;
$titulo_pagina = "Material Informático > Stock Informática";
$pagina = "stock_informatica";
include("../includes/header.php");
echo '<script type="text/javascript" src="../js/material_informatico/material_informatico.js"></script>';
?>

<div class="separador"></div>
<?php include("../includes/menu_material_informatico.php"); ?>

<div id="ContenedorCentral">
    <div id="ContenedorSidebar">
        <?php include ("../includes/sidebar.php"); ?>
    </div>

    <h3>Stock Material Inform&aacute;tico</h3>
    <h4></h4>

    <form id="buscadorStockInformatico" name="buscadorStockInformatico" action="stock_informatica.php" method="get" class="Buscador">
    <table style="border:0;">
    <tr style="border:0;">
        <td>
            <div class="Label">Tipo Material</div>
            <select id="tipo_material" name="tipo_material" class="BuscadorInput">
                <option></option>
            <?php
                // Cargamos los nombres de los tipos de material
                $resultados_tipo = $materialInformatico->dameTiposMateriales();
                for($i=0;$i<count($resultados_tipo);$i++){
                    $id_tipo = $resultados_tipo[$i]["id_tipo"];
                    $nombre_tipo = $resultados_tipo[$i]["nombre"];
                    $codigo = $resultados_tipo[$i]["codigo"];

                    echo '<option value="'.$id_tipo.'"';
                    if($id_tipo == $_SESSION["tipo_material_stock_informatico"]){
                        echo ' selected="selected" ';
                    }
                    echo '>'.$codigo.' - '.utf8_encode($nombre_tipo).'</option>';
                }
            ?>        
            </select>
        </td>
        <td>
            <div class="Label">Subtipo Material</div>
            <select id="subtipo_material" name="subtipo_material" class="BuscadorInput">
                <option></option>
            <?php
                // Cargamos los nombres de los subtipos 
                $resultados_subtipo = $materialInformatico->dameSubtiposMateriales();
                for($i=0;$i<count($resultados_subtipo);$i++){
                    $id_subtipo = $resultados_subtipo[$i]["id_subtipo"];
                    $nombre_subtipo = $resultados_subtipo[$i]["subtipo"];

                    echo '<option value="'.$id_subtipo.'"';
                    if($id_subtipo == $_SESSION["subtipo_material_stock_informatico"]){
                        echo ' selected="selected" ';
                    }
                    echo '>'.utf8_encode($nombre_subtipo).'</option>';
                }
            ?>        
            </select>
        </td>
        <td>
            <div class="Label">Unidades Stock</div>
            <input type="text" id="unidades" name="unidades" class="BuscadorInput" maxlength="50" value="<?php echo $_SESSION["unidades_stock_informatico"];?>"/>
        </td>
    </tr>
    <tr style="border:0;">
        <td>
            <div class="Label">Min. Unidades</div>
            <input type="text" id="min_unidades" name="min_unidades" class="BuscadorInput" maxlength="8" value="<?php echo $_SESSION["min_unidades_stock_informatico"];?>" onkeypress="return blockNonNumbers(this, event, true, true);" />
        </td>
        <td>
            <!--
            <div class="Label">Unidades pedido</div>
            <input type="text" id="unidades_pedido" name="unidades_pedido" class="BuscadorInput" maxlength="8" value="<?php // echo $_SESSION["unidades_pedido_stock_informatico"];?>" onkeypress="return blockNonNumbers(this, event, true, true);" />
            -->
        </td>
        <td></td>
    </tr>
    <tr style="border:0;">
        <td style="vertical-align: bottom;">
            <input type="hidden" id="realizandoBusqueda" name="realizandoBusqueda" value="1" />
            <input type="submit" id="" name="" class="" value="Buscar" />
        </td>
        <td></td>
        <td></td>
    </tr>
    </table>
    <br />
    </form>

    <div class="ContenedorBotonCrear">
    <?php
        if($mostrar_tabla){
            if($num_resultados_encontrados == NULL or $num_resultados_encontrados == 0){
                echo '<div class="mensaje">No se encontraron materiales informáticos</div>';
                $mostrar_tabla = false;
            }
            else if ($num_resultados_encontrados == 1){
                echo '<div class="mensaje">Se encontró 1 subtipo de material informático</div>';
            }
            else{
                echo '<div class="mensaje">Se encontraron '.$num_resultados_encontrados.' subtipos de materiales informáticos</div>';
            }
        }
        ?>
    </div>

    <?php
        if($mostrar_tabla) { ?>
            <div class="CapaTabla">
                <table>
                <tr>
                    <th style="text-align:center;">CODIGO</th>
                    <th style="text-align:left;">TIPO</th>                    
                    <th style="text-align:left;">SUBTIPO</th>
                    <th style="text-align:center;">UNID. STOCK</th>
                    <th style="text-align:center;">MIN. UNID</th>
                    <th style="text-align:center;">UNID. PEDIDO</th>
                    <th style="text-align:center;"></th>
                </tr>
                <?php
                    for($i=0;$i<count($resultadosBusqueda);$i++){ 
                        $id_material = $resultadosBusqueda[$i]["id_material"];
                        $unidades_stock = $resultadosBusqueda[$i]["unidades_stock"];

                        $materialInformatico->cargaDatosMaterialId($id_material);
                        $id_tipo = $materialInformatico->id_tipo; 
                        $id_subtipo = $materialInformatico->id_subtipo;

                        $res_tipo_material = $materialInformatico->dameTipoMaterial($id_tipo);
                        $nombre_tipo_material = utf8_encode($res_tipo_material[0]["nombre"]);
                        $codigo_tipo_material = $res_tipo_material[0]["codigo"];

                        $res_subtipo_material = $materialInformatico->dameSubtipoMaterial($id_subtipo);
                        $nombre_subtipo_material = utf8_encode($res_subtipo_material[0]["subtipo"]);
                        $min_unidades = $res_subtipo_material[0]["min_unidades"];

                        // Calculamos las unidades de un subtipo de material en STOCK
                        // $unidades_stock = $materialInformatico->dameUnidadesStock($id_tipo,$id_subtipo);
                        $unidades_pedido = $min_unidades - $unidades_stock;
                        if($unidades_pedido > 0)  {
                            $color_unid_pedido = "red";
                        }
                        else {
                            $color_unid_pedido = "black";
                            $unidades_pedido = "-";
                        }

                        // Establecemos valores predeterminados
                        if(empty($min_unidades)) $min_unidades = 1;
                        if(empty($nombre_subtipo_material)) $nombre_subtipo_material = "-"; ?>

                        <tr>
                            <td style="text-align:center;"><?php echo $codigo_tipo_material;?></td>        
                            <td style="text-align:left;"><?php echo $nombre_tipo_material;?></td>                    
                            <td style="text-align:left;"><?php echo $nombre_subtipo_material;?></td>
                            <td style="text-align:center; font-weight: bold;"><?php echo $unidades_stock;?></td>
                            <td style="text-align:center; font-weight: bold;"><?php echo $min_unidades;?></td>
                            <td style="text-align:center; font-weight: bold; color:<?php echo $color_unid_pedido;?>"><?php echo $unidades_pedido;?></td>
                            <td style="text-align:center;"></td>
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
                    <a href="stock_informatica.php?pg=1&realizandoBusqueda=1&tipo_material=<?php echo $_SESSION["tipo_material_stock_informatico"];?>&subtipo_material=<?php echo $_SESSION["subtipo_material_stock_informatico"];?>&unidades=<?php echo $_SESSION["unidades_stock_informatico"];?>&min_unidades=<?php echo $_SESSION["min_unidades_stock_informatico"];?>&unidades_pedido=<?php echo $_SESSION["unidades_pedido_stock_informatico"];?>">Primera&nbsp&nbsp&nbsp</a>
                    <a href="stock_informatica.php?pg=<?php echo $pg_pagina - 1;?>&realizandoBusqueda=1&tipo_material=<?php echo $_SESSION["tipo_material_stock_informatico"];?>&subtipo_material=<?php echo $_SESSION["subtipo_material_stock_informatico"];?>&unidades=<?php echo $_SESSION["unidades_stock_informatico"];?>&min_unidades=<?php echo $_SESSION["min_unidades_stock_informatico"];?>&unidades_pedido=<?php echo $_SESSION["unidades_pedido_stock_informatico"];?>"> Anterior</a>
            <?php
                }
                else {
                    echo 'Primera&nbsp;&nbsp;&nbsp;Anterior';
                }
                echo ' &nbsp;&nbsp;&nbsp;['.$pg_pagina.' / '.$pg_totalPaginas.']&nbsp;&nbsp;&nbsp;';
                if($pg_pagina < $pg_totalPaginas) { ?>
                    <a href="stock_informatica.php?pg=<?php echo $pg_pagina + 1;?>&realizandoBusqueda=1&tipo_material=<?php echo $_SESSION["tipo_material_stock_informatico"];?>&subtipo_material=<?php echo $_SESSION["subtipo_material_stock_informatico"];?>&unidades=<?php echo $_SESSION["unidades_stock_informatico"];?>&min_unidades=<?php echo $_SESSION["min_unidades_stock_informatico"];?>&unidades_pedido=<?php echo $_SESSION["unidades_pedido_stock_informatico"];?>">Siguiente&nbsp&nbsp&nbsp</a>
                    <a href="stock_informatica.php?pg=<?php echo $pg_totalPaginas;?>&realizandoBusqueda=1&tipo_material=<?php echo $_SESSION["tipo_material_stock_informatico"];?>&subtipo_material=<?php echo $_SESSION["subtipo_material_stock_informatico"];?>&unidades=<?php echo $_SESSION["unidades_stock_informatico"];?>&min_unidades=<?php echo $_SESSION["min_unidades_stock_informatico"];?>&unidades_pedido=<?php echo $_SESSION["unidades_pedido_stock_informatico"];?>">Última</a>
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
            if($num_resultados_encontrados != NULL and $num_resultados_encontrados != 0){ ?>
                <div class="ContenedorBotonCrear"><input type="button" id="descargar_XLS" name="descargar_XLS" value="Descargar XLS" class="BotonEliminar" onclick="javascript:descargar_XLS_Stock();"/></div>
        <?php
            }
        }
    ?>
</div>
<?php include ("../includes/footer.php"); ?>
