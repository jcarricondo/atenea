<?php
// Listado de todas las referencias de los almacenes de las diferentes sedes
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/proveedor.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/basicos/listado_proveedores.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/orden_produccion/listado_ordenes_produccion.class.php");
include("../classes/orden_compra/listado_ordenes_compra.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/almacen/recepcion_material.class.php");
include("../classes/almacen/listado_referencias_almacen.class.php");
include("../classes/sede/sede.class.php");
include("../classes/control_usuario.class.php");
permiso(21);

$prov = new Proveedor();
$ref = new Referencia();
$np = new listadoProveedores();
$op = new Orden_Produccion();
$oprod = new Orden_Produccion();       
$listadoOP = new listadoOrdenesCompra();
$listado_orden_compra = new listadoOrdenesCompra();
$almacen = new Almacen();
$sede = new Sede();
$referencias = new listadoReferenciasAlmacen();
$rm = new RecepcionMaterial();
$rs = new RecepcionMaterial();
$control_usuario = new Control_Usuario();

$titulo_pagina = "Almacen > Listado Material";
$pagina = "listado_material";
include("../includes/header.php");
echo '<script type="text/javascript" src="../js/almacen/almacen.js"></script>';

$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$id_almacen_usuario = $_SESSION["AT_id_almacen"];
$id_sede = $control_usuario->dameSedeSegunUsuario($id_tipo_usuario,$id_almacen_usuario);
$_SESSION["id_almacen_almacen_material"] = $id_almacen_usuario;

$esAdminGlobal = $control_usuario->esAdministradorGlobal($id_tipo_usuario);
$esUsuarioGes = $control_usuario->esUsuarioGes($id_tipo_usuario);
$esUsuarioDis = $control_usuario->esUsuarioDis($id_tipo_usuario);
$filtroSede = $esAdminGlobal || $esUsuarioGes || $esUsuarioDis;

// Predeterminado si el usuario sin sede asignada no escogió ninguna
if(empty($id_sede)) $id_sede = 1;
if(empty($id_almacen_usuario)) $_SESSION["id_almacen_almacen_material"] = 1;
else $_SESSION["id_almacen_almacen_material"] = $_SESSION["AT_id_almacen"];

// Asignamos el método correspondiente con el Listado de Piezas
$metodo = 0;

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
    // Obtenemos la sede
    $mostrar_tabla = true;
    if($filtroSede) $id_sede = $_GET["sedes"];
    $busqueda_magica = addslashes($_GET["busqueda_magica"]);
    $orden_produccion = $_GET["orden_produccion"];
    $orden_compra = addslashes($_GET["orden_compra"]);
    $proveedor = $_GET["proveedor"];
    $id_ref = $_GET["id_ref"];
    $id_almacen = $_GET["almacenes"];

    // Se pasan los datos del buscador a la clase del listado y se realiza la consulta a la base de datos sin parámetros de paginación
    $referencias->setValores($busqueda_magica,$orden_produccion,$orden_compra,$proveedor,$id_ref,'',$id_almacen,$id_sede);
    $referencias->realizarConsulta();
    $resultadosBusqueda = $referencias->referencias;
    $num_resultados = count($resultadosBusqueda);

    // Guardamos variables de sesión para generar el informe de piezas
    $_SESSION["busqueda_magica_xls_almacen_material"] = $busqueda_magica;
    $_SESSION["orden_produccion_xls_almacen_material"] = $orden_produccion;
    $_SESSION["orden_compra_xls_almacen_material"] = $orden_compra;
    $_SESSION["proveedor_xls_almacen_material"] = $proveedor;
    $_SESSION["id_ref_xls_almacen_material"] = $id_ref;
    $_SESSION["id_almacen_xls_almacen_material"] = $id_almacen;
    $_SESSION["id_sede_xls_almacen_material"] = $id_sede;

    $pg_totalPaginas = ceil(count($resultadosBusqueda) / $pg_registros);

    // Se obtienen los 50 resultados correspondientes a la consulta
    $referencias->setValores($busqueda_magica,$orden_produccion,$orden_compra,$proveedor,$id_ref,$paginacion,$id_almacen,$id_sede);
    $referencias->realizarConsulta();
    $resultadosBusqueda = $referencias->referencias;

    // Cargamos el nombre del almacen y la sede
    $almacen->cargaDatosAlmacenId($id_almacen);
    $nombre_almacen = $almacen->nombre; 
    $sede->cargaDatosSedeId($id_sede);
    $name_sede = $sede->nombre; 

    // Guardamos variables de sesión para recordar el filtrado del buscador
    $_SESSION["busqueda_magica_almacen_material"] = stripslashes(htmlspecialchars($busqueda_magica));
    $_SESSION["orden_produccion_almacen_material"] = $orden_produccion;
    $_SESSION["orden_compra_almacen_material"] = stripslashes(htmlspecialchars($orden_compra));
    $_SESSION["proveedor_almacen_material"] = $proveedor;
    $_SESSION["id_ref_almacen_material"] = $id_ref;
    $_SESSION["id_almacen_almacen_material"] = $id_almacen;
    $_SESSION["id_sede_almacen_material"] = $id_sede;
}
$max_caracteres = 35; 
?>

<div class="separador"></div>
<?php include("../includes/menu_almacen_piezas.php"); ?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>

    <h3>Listado Material</h3>
    <h4></h4>

    <form id="BuscadorReferencias" name="buscadorReferencias" action="listado_material.php" method="get" class="Buscador">
    <table style="border:0;">
    <?php
        if($filtroSede){?>
            <tr style="border:0;">
                <td style="width: 33%; vertical-align: top;">
                    <div class="Label">Sede</div>
                    <select id="sedes" name="sedes" class="BuscadorInputAlmacen" onchange="cargaOPsPorSede(this.value)" >
                    <?php
                        // Obtenemos todas las sedes
                        $resultados_sedes = $sede->dameSedes();
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
                 <td style="width: 33%;"></td>
                 <td style="width: 33%;"></td>
            </tr>
    <?php
        }
    ?>
    <tr style="border:0;">
        <td style="width: 33%;">
            <div class="Label">Almacen *</div>
            <div id="capaAlmacenes">
                <select id="almacenes" name="almacenes" class="BuscadorInputAlmacen" onchange="resetearCamposFormulario();">
                    <option value="">Seleccionar</option>
                    <?php
                        // Obtenemos los almacenes de esa sede
                        $res_almacenes = $sede->dameAlmacenesSede($id_sede);
                        for($i=0;$i<count($res_almacenes);$i++){
                            $id_almacen_bus = $res_almacenes[$i]["id_almacen"];
                            $nombre = $res_almacenes[$i]["almacen"]; ?>

                            <option value="<?php echo $id_almacen_bus;?>" <?php if($_SESSION["id_almacen_almacen_material"] == $id_almacen_bus) echo 'selected="selected" '?>>
                                <?php echo $nombre;?>
                            </option>
                    <?php
                        }
                    ?>
                </select>
            </div>
        </td>
        <td style="width: 33%; vertical-align: top;">
            <div class="Label">ID Ref</div>
            <input type="text" id="id_ref" name="id_ref" class="BuscadorInputAlmacen" maxlength="50" value="<?php echo $_SESSION["id_ref_almacen_material"];?>" onkeypress="return soloNumeros(event)"/>
        </td>
        <td style="width: 33%;">
            <div class="Label">Proveedor</div>
            <select id="proveedor" name="proveedor" class="BuscadorInputAlmacen" onchange="javascript:cargarOrdenesCompraVariasOP(<?php echo $id_sede;?>);">
            <?php
                $np->prepararConsulta();
                $np->realizarConsulta();
                $resultado_proveedores = $np->proveedores;
                for($i=-1;$i<count($resultado_proveedores);$i++) {
                    $datoProveedor = $resultado_proveedores[$i];
                    $prov->cargaDatosProveedorId($datoProveedor["id_proveedor"]); ?>

                    <option value="<?php echo $prov->nombre;?>" <?php if($prov->nombre == $_SESSION["proveedor_almacen_material"]) echo ' selected="selected"';?>>
                        <?php echo $prov->nombre;?>
                    </option>
            <?php
                }
            ?>
            </select>
        </td>
    </tr>
    <tr style="border:0;">
        <td style="width: 33%; vertical-align: top;">
            <div class="Label">Búsqueda Mágica</div>
            <input type="text" id="busqueda_magica" name="busqueda_magica" class="BuscadorInputAlmacen" maxlength="50" value="<?php echo $_SESSION["busqueda_magica_almacen_material"];?>"/>
        </td>
        <td id="celda_op" style="width: 33%; vertical-align: top;">
            <div class="Label">Orden Producción</div>
            <div id="capaOrdenProduccion">
                <select multiple="multiple" id="orden_produccion[]" name="orden_produccion[]" class="BuscadorOCEstadosOP" size="8" onchange="javascript:cargarOrdenesCompraVariasOP()">
                    <?php
                        // Sacar el listado de todas las OP.
                        $listadoOP->prepararOPIniciadas($id_sede);
                        $listadoOP->realizarConsultaOP();
                        $resultados_op = $listadoOP->orden_produccion;

                        // Si una OP tiene alias != NULL mostrar alias
                        for($i = -1; $i<count($resultados_op); $i++){
                            if($i == -1) {
                                echo '<option value=""';
                                if($_SESSION["orden_produccion_almacen_material"][0] == ""){ echo ' selected="selected"'; }
                                echo '></option>';
                            }
                            else{
                                $op->cargaDatosProduccionId($resultados_op[$i]["id_produccion"]);
                                if($op->alias_op != NULL){
                                    echo '<option value="'.$op->id_produccion.'"';
                                    for ($j=0;$j<count($_SESSION["orden_produccion_almacen_material"]);$j++){
                                        if ($_SESSION["orden_produccion_almacen_material"][$j] == $op->id_produccion) { echo ' selected="selected"'; }
                                    }
                                    echo '>'.$op->alias_op.'</option>';
                                }
                                else {
                                    echo '<option value="'.$op->id_produccion.'"';
                                    for ($j=0;$j<count($_SESSION["orden_produccion_almacen_material"]);$j++){
                                        if ($_SESSION["orden_produccion_almacen_material"][$j] == $op->id_produccion) { echo ' selected="selected"'; }
                                    }
                                    echo '>'.$op->codigo.'</option>';
                                }
                            }
                        }
                        // Incluimos la opción de STOCK
                        echo '<option value=0';
                        for($j=0;$j<count($_SESSION["orden_produccion_almacen_material"]);$j++){
                            if ($_SESSION["orden_produccion_almacen_material"][$j] == "0") { echo ' selected="selected"'; }
                        }
                        echo '>STOCK</option>';
                    ?>
                </select>
            </div>
        </td>
        <td id="celda_oc" style="width: 33%; vertical-align: top;">
            <div class="Label">Orden Compra</div>
            <div id="capaOrdenCompra">
                <?php
                    $orden_produccion = $_SESSION["orden_produccion_almacen_material"];
                    $proveedor = $_SESSION["proveedor_almacen_material"];

                    $listado_orden_compra->serValoresBusquedaProvOP($orden_produccion,$proveedor,$id_sede);
                    $listado_orden_compra->realizarConsulta();
                    $resultadosBusquedaOC = $listado_orden_compra->ordenes_compra;

                    $filtrado = !empty($orden_produccion) || !empty($proveedor);
                    $muestro_select = (!empty($resultadosBusquedaOC) && $filtrado);

                    if($muestro_select){ ?>
                        <select id="orden_compra" name="orden_compra" class="BuscadorInputAlmacen">
                            <option></option>
                            <?php
                                for($i=0;$i<count($resultadosBusquedaOC);$i++) { ?>
                                    <option value="<?php echo $resultadosBusquedaOC[$i]["numero_pedido"]; ?>"<?php if($_SESSION["orden_compra_almacen_material"] == $resultadosBusquedaOC[$i]["numero_pedido"]) { echo ' selected="selected"'; } ?>><?php echo $resultadosBusquedaOC[$i]["numero_pedido"]; ?> (<?php echo $resultadosBusquedaOC[$i]["nombre_prov"]; ?>)</option>
                            <?php
                                }
                            ?>
                        </select>
                <?php
                    }
                    else { ?>
                        <input type="text" id="" name="orden_compra" class="BuscadorInputAlmacen" maxlength="50" value="<?php echo $_SESSION["orden_compra_almacen_material"];?>"/>
                <?php
                    }
                ?>
            </div>
        </td>
    </tr>
    <tr style="border:0px;">
        <td style="width: 33%; vertical-align: top;">
            <div style="clear: both;">
                <input type="hidden" id="realizandoBusqueda" name="realizandoBusqueda" value="1" />
                <input type="submit" id="" name="" class="" value="Buscar" style="margin: 20px 0px 0px 0px;" />
            </div>
        </td>
        <td style="width: 33%;"></td>
        <td style="width: 33%;"></td>
    </tr>
    </table>
    <br />
    </form>
    <br/>

    <div class="ContenedorBotonCrear">
        <input type="hidden" id="ajuste" value="0" />
        <?php
            if($mostrar_tabla){
                if($num_resultados == NULL or $num_resultados == 0){
                   echo '<div class="mensaje">No se encontraron piezas</div>';
                   $mostrar_tabla = false;
                }
                else if ($num_resultados == 1){
                    echo '<div class="mensaje">Se encontró 1 pieza</div>';
                }
                else{
                    echo '<div class="mensaje">Se encontraron '.$num_resultados.' piezas</div>';
                }
            }
        ?>
    </div>

    <?php
        if($mostrar_tabla) { ?>
            <div class="CapaTabla">
                <table>
                <tr>
                    <th style="width:5%; text-align:center;">ID REF</th>
                    <?php
                        if($filtroSede) { ?>
                            <th style="width:10%;">NOMBRE</th>
                    <?php
                        }
                        else { ?>
                            <th style="width:15%;">NOMBRE</th>
                    <?php
                        }
                    ?>
                    <th style="width:10%;">PROVEEDOR</th>
                    <th style="width:10%;">REF. PROV.</th>
                    <?php
                        if($filtroSede) { ?>
                            <th style="width:5%;">SEDE</th>
                    <?php
                        }
                    ?>
                    <th style="width:10%;">ALMACEN</th>
                    <th style="width:15%; padding:5px;">ORDEN PRODUCCIÓN</th>
                    <th style="width:5%; padding:5px; text-align:center;">UND. PED</th>
                    <th style="width:5%; padding:5px; text-align:center;">UND. REC</th>
                    <th style="width:5%; padding:5px; text-align:center;">UND. PTE</th>
                    <th style="width:5%; padding:5px; text-align:center;">UND. USA</th>
                    <th style="width:5%; padding:5px; text-align:center;">UND. DIS</th>
                    <th style="width:10%; padding:5px; text-align:center;"></th>
                </tr>
                <?php
                    $piezas_totales_referencia = 0;
                    $piezas_recibidas_referencia = 0;
                    $piezas_pendientes_referencia = 0;
                    $piezas_usadas_referencia = 0;
                    $piezas_disponibles_referencia = 0;

                    // Si no se filtro por ningun almacenes preparamos los almacenes de la sede.
                    if($id_almacen != "") $ids_almacenes[0]["id_almacen"] = $id_almacen;
                    else $ids_almacenes = $res_almacenes;

                    // Se cargan los datos de las referencias de la busqueda según su identificador
                    for($i=0;$i<count($resultadosBusqueda);$i++) {
                        $datoReferencia = $resultadosBusqueda[$i];
                        $ref->cargaDatosReferenciaId($datoReferencia["id_referencia"]); ?>

                        <tr>
                            <td style="width:5%; text-align:center;"><?php echo $ref->id_referencia; ?></td>
                            <?php
                                if($filtroSede) { ?>
                                    <td style="width:10%;">
                            <?php
                                }
                                else { ?>
                                    <td style="width:15%;">
                            <?php
                                }
                            ?>
                                <a href="../basicos/mod_referencia.php?id=<?php echo $ref->id_referencia; ?>">
                            <?php
                                if(strlen($ref->referencia) > $max_caracteres){
                                    echo mb_strcut($ref->referencia, 0, 35, "UTF-8").'...';
                                }
                                else {
                                    echo $ref->referencia;
                                }
                            ?>
                                </a>
                            </td>
                            <td style="width:10%;">
                                <a href="../basicos/mod_proveedor.php?id=<?php echo $ref->proveedor;?>"><?php echo $ref->nombre_proveedor;?></a>
                            </td>
                            <td style="width:10%"><?php $ref->vincularReferenciaProveedor();?></td>
                            <?php
                                if($filtroSede) { ?>
                                    <td style="width:5%"><?php echo $name_sede;?></td>
                            <?php
                                }
                            ?>
                            <td colspan="8" style="width:60%; padding:0px;">
                            <?php
                                for($alm=0;$alm<count($ids_almacenes);$alm++){
                                    // Cargamos el nombre del almacen
                                    $id_almacen_tabla = $ids_almacenes[$alm]["id_almacen"];
                                    $almacen->cargaDatosAlmacenId($id_almacen_tabla);
                                    $nombre_almacen = $almacen->nombre;

                                    // Obtenemos las OP iniciadas de las que forma parte la referencia
                                    $oprod->dameOPIniciadasReferencia($ref->id_referencia,$id_sede);
                                    $ids_produccion = $oprod->ids_produccion;

                                    // Si se filtra por STOCK, se muestra sólo aquellos que tengan piezas
                                    $piezas_stock = $rs->damePiezasReferenciaStock($ref->id_referencia,$id_almacen_tabla);
                                    $muestro_almacen = !(($piezas_stock == NULL || $piezas_stock == "0") && $orden_produccion[0] == "0");

                                    if($muestro_almacen) { ?>
                                        <table style="border:0;">
                                            <tr style="border:0;">
                                                <td style="width:16.7%;"><?php echo $nombre_almacen; ?></td>
                                                <td colspan="7" style="width:83.3%; padding:0px;">
                                                    <table style="width:99%; margin: 1% 0.5% 1% 0.5%; border: 0px;">
                                                    <?php
                                                        if($orden_produccion != NULL and $orden_produccion[0] != "") {
                                                            // Mostramos solo las OP iniciadas que pertenecen a la referencia y han sido seleccionadas en el buscador
                                                            for($j = 0; $j < count($orden_produccion); $j++) {
                                                                $id_produccion_sel = $orden_produccion[$j];

                                                                for($k = 0; $k < count($ids_produccion); $k++) {
                                                                    $id_produccion_ini = $ids_produccion[$k]["id_produccion"];
                                                                    // Si coinciden las OP seleccionadas con las OP iniciadas mostramos el registro
                                                                    if ($id_produccion_sel == $id_produccion_ini) {
                                                                        $oprod->cargaDatosProduccionId($id_produccion_sel);

                                                                        $registro_ocr = $rm->dameRegistroOCR($id_produccion_sel, $ref->id_referencia);
                                                                        $piezas_totales = $registro_ocr["total_piezas"];
                                                                        $piezas_recibidas = $registro_ocr["piezas_recibidas"];
                                                                        $piezas_pendientes = $piezas_totales - $piezas_recibidas;
                                                                        $piezas_usadas = $registro_ocr["piezas_usadas"];
                                                                        $piezas_disponibles = $piezas_recibidas - $piezas_usadas;

                                                                        $piezas_totales_referencia = $piezas_totales_referencia + $piezas_totales;
                                                                        $piezas_recibidas_referencia = $piezas_recibidas_referencia + $piezas_recibidas;
                                                                        $piezas_pendientes_referencia = $piezas_pendientes_referencia + $piezas_pendientes;
                                                                        $piezas_usadas_referencia = $piezas_usadas_referencia + $piezas_usadas;
                                                                        $piezas_disponibles_referencia = $piezas_disponibles_referencia + $piezas_disponibles; ?>
                                                                        <tr>
                                                                            <td style="width:30%; padding:5px;">
                                                                                <?php
                                                                                    if ($oprod->alias_op != NULL) {
                                                                                        echo $oprod->alias_op;
                                                                                    }
                                                                                    else {
                                                                                        echo $oprod->codigo;
                                                                                    }
                                                                                ?>
                                                                            </td>
                                                                            <td style="width:10%; padding:5px; text-align:center;"><?php echo $piezas_totales; ?></td>
                                                                            <td style="width:10%; padding:5px; text-align:center;"><?php echo $piezas_recibidas; ?></td>
                                                                            <td style="width:10%; padding:5px; text-align:center;">
                                                                                <?php
                                                                                    if ($piezas_pendientes > 0) { ?>
                                                                                    <span style="color: red"><?php echo $piezas_pendientes; ?></span>
                                                                                <?php
                                                                                    }
                                                                                    else { ?>
                                                                                        <span style="color: green"><?php echo $piezas_pendientes; ?></span>
                                                                                <?php
                                                                                    }
                                                                                ?>
                                                                            </td>
                                                                            <td style="width:10%; padding:5px; text-align:center;"><?php echo $piezas_usadas; ?></td>
                                                                            <td style="width:10%; padding:5px; text-align:center;"><?php echo $piezas_disponibles; ?></td>
                                                                            <td style="width:20%; padding:5px; text-align:center;"></td>
                                                                        </tr>
                                                                <?php
                                                                    }
                                                                }

                                                                if($id_produccion_sel == 0) {
                                                                    $piezas_recibidas = 0;
                                                                    $piezas_disponibles = 0;
                                                                    // Después de cargar las OP mostramos STOCK
                                                                    // Tenemos que ver si esa referencia tiene piezas en stock
                                                                    $piezas_stock = $rs->damePiezasReferenciaStock($ref->id_referencia, $id_almacen_tabla);

                                                                    if($piezas_stock != NULL) {
                                                                        $piezas_recibidas = $piezas_stock;
                                                                        $piezas_recibidas_referencia = $piezas_recibidas_referencia + $piezas_recibidas;
                                                                        $piezas_disponibles = $piezas_recibidas;
                                                                        $piezas_disponibles_referencia = $piezas_disponibles_referencia + $piezas_disponibles; ?>

                                                                        <tr>
                                                                            <td style="width:30%; padding:5px; color:#0B3861;">STOCK</td>
                                                                            <td style="width:10%; padding:5px; text-align:center;">-</td>
                                                                            <td style="width:10%; padding:5px; text-align:center;"><?php echo $piezas_recibidas; ?></td>
                                                                            <td style="width:10%; padding:5px; text-align:center;">-</td>
                                                                            <td style="width:10%; padding:5px; text-align:center;">-</td>
                                                                            <td style="width:10%; padding:5px; text-align:center;"><?php echo $piezas_disponibles; ?></td>
                                                                            <td style="width:20%; padding:5px; text-align:center;"></td>
                                                                        </tr>
                                                                <?php
                                                                    }
                                                                }
                                                            } // FOR OP INICIADAS
                                                        ?>
                                                            <tr>
                                                                <td style="width:30%; padding:5px; background: #99CCFF; font-weight: bold;"><?php echo "TOTALES"; ?></td>
                                                                <td style="width:10%; padding:5px; text-align:center; background: #99CCFF; font-weight: bold;"><?php echo $piezas_totales_referencia; ?></td>
                                                                <td style="width:10%; padding:5px; text-align:center; background: #99CCFF; font-weight: bold;"><?php echo $piezas_recibidas_referencia; ?></td>
                                                                <td style="width:10%; padding:5px; text-align:center; background: #99CCFF; font-weight: bold;"><?php echo $piezas_pendientes_referencia; ?></td>
                                                                <td style="width:10%; padding:5px; text-align:center; background: #99CCFF; font-weight: bold;"><?php echo $piezas_usadas_referencia; ?></td>
                                                                <td style="width:10%; padding:5px; text-align:center; background: #99CCFF; font-weight: bold;"><?php echo $piezas_disponibles_referencia; ?></td>
                                                                <td style="width:20%; padding:5px; text-align:center; background: #99CCFF; font-weight: bold;"></td>
                                                            </tr>
                                                            <?php
                                                                $piezas_totales_referencia = 0;
                                                                $piezas_recibidas_referencia = 0;
                                                                $piezas_pendientes_referencia = 0;
                                                                $piezas_usadas_referencia = 0;
                                                                $piezas_disponibles_referencia = 0;
                                                        }
                                                        else {
                                                            // Por cada referencia mostraremos todas las OP a las que pertenezca y STOCK
                                                            for ($k = 0; $k < count($ids_produccion); $k++) {
                                                                $id_produccion_ini = $ids_produccion[$k]["id_produccion"];

                                                                // Si coinciden las OP seleccionadas con las OP iniciadas mostramos el registro
                                                                $oprod->cargaDatosProduccionId($id_produccion_ini);
                                                                $registro_ocr = $rm->dameRegistroOCR($id_produccion_ini, $ref->id_referencia);
                                                                $piezas_totales = $registro_ocr["total_piezas"];
                                                                $piezas_recibidas = $registro_ocr["piezas_recibidas"];
                                                                $piezas_pendientes = $piezas_totales - $piezas_recibidas;
                                                                $piezas_usadas = $registro_ocr["piezas_usadas"];
                                                                $piezas_disponibles = $piezas_recibidas - $piezas_usadas;

                                                                $piezas_totales_referencia = $piezas_totales_referencia + $piezas_totales;
                                                                $piezas_recibidas_referencia = $piezas_recibidas_referencia + $piezas_recibidas;
                                                                $piezas_pendientes_referencia = $piezas_pendientes_referencia + $piezas_pendientes;
                                                                $piezas_usadas_referencia = $piezas_usadas_referencia + $piezas_usadas;
                                                                $piezas_disponibles_referencia = $piezas_disponibles_referencia + $piezas_disponibles; ?>

                                                                <tr>
                                                                    <td style="width:30%; padding:5px;">
                                                                    <?php
                                                                        if ($oprod->alias_op != NULL) {
                                                                            echo $oprod->alias_op;
                                                                        }
                                                                        else {
                                                                            echo $oprod->codigo;
                                                                        }
                                                                    ?>
                                                                    </td>
                                                                    <td style="width:10%; padding:5px; text-align:center;"><?php echo $piezas_totales; ?></td>
                                                                    <td style="width:10%; padding:5px; text-align:center;"><?php echo $piezas_recibidas; ?></td>
                                                                    <td style="width:10%; padding:5px; text-align:center;">
                                                                    <?php
                                                                        if ($piezas_pendientes > 0) { ?>
                                                                            <span style="color: red"><?php echo $piezas_pendientes; ?></span>
                                                                        <?php
                                                                        }
                                                                        else { ?>
                                                                            <span style="color: green"><?php echo $piezas_pendientes; ?></span>
                                                                    <?php
                                                                        }
                                                                    ?>
                                                                    </td>
                                                                    <td style="width:10%; padding:5px; text-align:center;"><?php echo $piezas_usadas; ?></td>
                                                                    <td style="width:10%; padding:5px; text-align:center;"><?php echo $piezas_disponibles; ?></td>
                                                                    <td style="width:20%; padding:5px; text-align:center;"></td>
                                                                </tr>
                                                        <?php
                                                            }
                                                            // Después de cargar las OP mostramos STOCK
                                                            // Tenemos que ver si esa referencia tiene piezas en stock
                                                            $piezas_stock = $rs->damePiezasReferenciaStock($ref->id_referencia, $id_almacen_tabla);

                                                            if ($piezas_stock != NULL) {
                                                                $piezas_recibidas = $piezas_stock;
                                                                $piezas_recibidas_referencia = $piezas_recibidas_referencia + $piezas_recibidas;
                                                                $piezas_disponibles = $piezas_recibidas;
                                                                $piezas_disponibles_referencia = $piezas_disponibles_referencia + $piezas_disponibles;
                                                            }
                                                            else {
                                                                $piezas_recibidas = "-";
                                                                $piezas_disponibles = "-";
                                                            }
                                                        ?>
                                                            <tr>
                                                                <td style="width:30%; padding:5px;">STOCK</td>
                                                                <td style="width:10%; padding:5px; text-align:center;">-</td>
                                                                <td style="width:10%; padding:5px; text-align:center;"><?php echo $piezas_recibidas; ?></td>
                                                                <td style="width:10%; padding:5px; text-align:center;">-</td>
                                                                <td style="width:10%; padding:5px; text-align:center;">-</td>
                                                                <td style="width:10%; padding:5px; text-align:center;"><?php echo $piezas_disponibles; ?></td>
                                                                <td style="width:20%; padding:5px; text-align:center;"></td>
                                                            </tr>

                                                            <tr>
                                                                <td style="width:30%; padding:5px; background: #99CCFF; font-weight: bold;"><?php echo "TOTALES"; ?></td>
                                                                <td style="width:10%; padding:5px; text-align:center; background: #99CCFF; font-weight: bold;"><?php echo $piezas_totales_referencia; ?></td>
                                                                <td style="width:10%; padding:5px; text-align:center; background: #99CCFF; font-weight: bold;"><?php echo $piezas_recibidas_referencia; ?></td>
                                                                <td style="width:10%; padding:5px; text-align:center; background: #99CCFF; font-weight: bold;"><?php echo $piezas_pendientes_referencia; ?></td>
                                                                <td style="width:10%; padding:5px; text-align:center; background: #99CCFF; font-weight: bold;"><?php echo $piezas_usadas_referencia; ?></td>
                                                                <td style="width:10%; padding:5px; text-align:center; background: #99CCFF; font-weight: bold;"><?php echo $piezas_disponibles_referencia; ?></td>
                                                                <td style="width:20%; padding:5px; text-align:center; background: #99CCFF; font-weight: bold;"></td>
                                                            </tr>
                                                        <?php
                                                            $piezas_totales = 0;
                                                            $piezas_recibidas = 0;
                                                            $piezas_pendientes = 0;
                                                            $piezas_usadas = 0;
                                                            $piezas_disponibles = 0;

                                                            $piezas_totales_referencia = 0;
                                                            $piezas_recibidas_referencia = 0;
                                                            $piezas_pendientes_referencia = 0;
                                                            $piezas_usadas_referencia = 0;
                                                            $piezas_disponibles_referencia = 0;
                                                        }
                                                    ?>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                <?php
                                    }
                                // FOR ALMACENES
                                }
                            ?>
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
        if((isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) and $resultadosBusqueda != null) { ?>
            <div style="font: bold 11px Verdana,Arial; margin: 0 auto; padding: 10px 0; width: 350px; text-align: center;">
            <?php
                if(($pg_pagina - 1) > 0) { ?>
                    <a href="listado_material.php?pg=1&realizandoBusqueda=1&busqueda_magica=<?php echo $_SESSION["busqueda_magica_almacen_material"];for($i=0;$i<count($_SESSION["orden_produccion_almacen_material"]);$i++){echo '&orden_produccion[]='.$_SESSION["orden_produccion_almacen_material"][$i];}?>&orden_compra=<?php echo $_SESSION["orden_compra_almacen_material"]; ?>&proveedor=<?php echo $_SESSION["proveedor_almacen_material"] ?>&sedes=<?php echo $id_sede; ?>&almacenes=<?php echo $id_almacen; ?>">Primera&nbsp&nbsp&nbsp</a>
                    <a href="listado_material.php?pg=<?php echo $pg_pagina - 1;?>&realizandoBusqueda=1&busqueda_magica=<?php echo $_SESSION["busqueda_magica_almacen_material"];for($i=0;$i<count($_SESSION["orden_produccion_almacen_material"]);$i++){echo '&orden_produccion[]='.$_SESSION["orden_produccion_almacen_material"][$i];}?>&orden_compra=<?php echo $_SESSION["orden_compra_almacen_material"]; ?>&proveedor=<?php echo $_SESSION["proveedor_almacen_material"] ?>&sedes=<?php echo $id_sede; ?>&almacenes=<?php echo $id_almacen; ?>"> Anterior</a>
            <?php
                }
                else {
                    echo 'Primera&nbsp;&nbsp;&nbsp;Anterior';
                }

                echo ' &nbsp;&nbsp;&nbsp;['.$pg_pagina.' / '.$pg_totalPaginas.']&nbsp;&nbsp;&nbsp;';
                if($pg_pagina < $pg_totalPaginas) { ?>
                    <a href="listado_material.php?pg=<?php echo $pg_pagina + 1;?>&realizandoBusqueda=1&busqueda_magica=<?php echo $_SESSION["busqueda_magica_almacen_material"];for($i=0;$i<count($_SESSION["orden_produccion_almacen_material"]);$i++){echo '&orden_produccion[]='.$_SESSION["orden_produccion_almacen_material"][$i];}?>&orden_compra=<?php echo $_SESSION["orden_compra_almacen_material"]; ?>&proveedor=<?php echo $_SESSION["proveedor_almacen_material"] ?>&sedes=<?php echo $id_sede; ?>&almacenes=<?php echo $id_almacen; ?>">Siguiente&nbsp&nbsp&nbsp</a>
                    <a href="listado_material.php?pg=<?php echo $pg_totalPaginas; ?>&realizandoBusqueda=1&busqueda_magica=<?php echo $_SESSION["busqueda_magica_almacen_material"];for($i=0;$i<count($_SESSION["orden_produccion_almacen_material"]);$i++){echo '&orden_produccion[]='.$_SESSION["orden_produccion_almacen_material"][$i];}?>&orden_compra=<?php echo $_SESSION["orden_compra_almacen_material"]; ?>&proveedor=<?php echo $_SESSION["proveedor_almacen_material"] ?>&sedes=<?php echo $id_sede; ?>&almacenes=<?php echo $id_almacen; ?>">Última</a>
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
        if((isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) && $mostrar_tabla){ ?>
    		<div class="ContenedorBotonCrear"><input type="button" id="descargar_XLS" name="descargar_XLS" value="Descargar XLS" class="BotonEliminar" onclick="javascript:descargar_XLS(<?php echo $metodo; ?>);"/></div>
    <?php
		}
	?>
    </form>
</div>
<?php include ("../includes/footer.php"); ?>
