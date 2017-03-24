<?php
// Este fichero muestra el listado de los movimientos de los albaranes
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/proveedor.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/basicos/centro_logistico.class.php");
include("../classes/basicos/usuario.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/almacen/albaran.class.php");
include("../classes/almacen/listado_movimientos.class.php");
include("../classes/sede/sede.class.php");
include("../classes/control_usuario.class.php");
permiso(21);

// Instancias de las clases
$proveedor = new Proveedor();
$ref = new Referencia();
$centroLogistico = new CentroLogistico();
$albaran = new Albaran();
$funciones = new Funciones();
$listadoMovimientos = new listadoMovimientos();
$usuario = new Usuario();
$sede = new Sede();
$almacen = new Almacen();
$control_usuario = new Control_Usuario();

$titulo_pagina = "Almacen > Listado Movimientos";
$pagina = "listado_movimientos";
include("../includes/header.php");
echo '<script type="text/javascript" src="../js/almacen/almacen.js"></script>';
echo '<script type="text/javascript" src="../js/funciones.js"></script>';

$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$id_almacen_usuario = $_SESSION["AT_id_almacen"];
$id_sede = $control_usuario->dameSedeSegunUsuario($id_tipo_usuario,$id_almacen_usuario);
$id_sede_usuario = $control_usuario->dameSedeSegunUsuario($id_tipo_usuario,$id_almacen_usuario);

$esAdminGlobal = $control_usuario->esAdministradorGlobal($id_tipo_usuario);
$esUsuarioGes = $control_usuario->esUsuarioGes($id_tipo_usuario);
$esUsuarioDis = $control_usuario->esUsuarioDis($id_tipo_usuario);
$esUsuarioBrasil = $control_usuario->esUsuarioBrasil($id_tipo_usuario,$id_sede);
$esUsuarioFab = $control_usuario->esUsuarioFab($id_tipo_usuario);
$esUsuarioMan = $control_usuario->esUsuarioMan($id_tipo_usuario);
$filtroSede = $esAdminGlobal || $esUsuarioGes || $esUsuarioDis;

// Predeterminado si el usuario sin sede asignada no escogió ninguna
if(empty($id_sede)) $id_sede = 1;
if(empty($id_sede_usuario)) $id_sede_usuario = 1;
if(empty($id_almacen_usuario)) $_SESSION["id_almacen_almacen_movimientos"] = 1;
else $_SESSION["id_almacen_almacen_movimientos"] = $_SESSION["AT_id_almacen"];

$esAlmacenBrasil = $almacen->esAlmacenBrasil($id_almacen);

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
	$nombre_participante = $_GET["nombre_participante"];
    $tipo_motivo = $_GET["tipo_motivo"];
	$id_usuario = $_GET["id_usuario"];
	$id_ref = $_GET["id_ref"];
    $fecha_desde = $_GET["fecha_desde"];
    $fecha_hasta = $_GET["fecha_hasta"];
    $id_almacen = $_GET["almacenes"];
    if($filtroSede) $id_sede = $_GET["sedes"];

    $id_tipo_participante = $albaran->dameTipoParticipante($nombre_participante);

    // Comprobamos que el id_tipo_participante es un PROVEEDOR o un CENTRO LOGISTICO
    if($id_tipo_participante == 1){
        $albaran->dameIdParticipante($id_tipo_participante,$nombre_participante);
        $id_participante = $albaran->id_participante["id_proveedor"];
    }
    else if($id_tipo_participante == 2){
        $albaran->dameIdParticipante($id_tipo_participante,$nombre_participante);
        $id_participante = $albaran->id_participante["id_centro_logistico"];
    }

    if($fecha_desde != ""){
        // Guardamos la fecha
        $fecha_desde_aux = $fecha_desde;
        $fecha_desde = $funciones->cFechaMy($fecha_desde);
        $date = new DateTime($fecha_desde);

        if($esUsuarioBrasil || ($esAdminGlobal && $esAlmacenBrasil)) {
            $fecha_desde = $date->add(new DateInterval('PT5H'));
        }
        $fecha_desde = $date->format('Y-m-d H:i:s');
    }
    if($fecha_hasta != ""){
        // Guardamos la fecha
        $fecha_hasta_aux = $fecha_hasta;
        $fecha_hasta = $funciones->cFechaMy($fecha_hasta);
        $date = new DateTime($fecha_hasta . " + 1 days");

        if($esUsuarioBrasil || ($esAdminGlobal && $esAlmacenBrasil)) {
            $fecha_hasta = $date->add(new DateInterval('PT5H'));
        }
        $fecha_hasta = $date->format('Y-m-d H:i:s');
    }

    // Se pasan los datos del buscador a la clase del listado y se realiza la consulta a la base de datos sin parámetros de paginación
    $listadoMovimientos->setValores($nombre_albaran,$tipo_albaran,$id_tipo_participante,$id_participante,$tipo_motivo,$id_usuario,$id_ref,$fecha_desde,$fecha_hasta,'',$id_almacen,$id_sede);
    $listadoMovimientos->realizarConsulta();
    $resultadosBusqueda = $listadoMovimientos->movimientos;
    $num_resultados = count($resultadosBusqueda);

    $_SESSION["nombre_albaran_xls_almacen_movimientos"] = $nombre_albaran;
    $_SESSION["tipo_albaran_xls_almacen_movimientos"] = $tipo_albaran;
    $_SESSION["nombre_participante_xls_almacen_movimientos"] = $nombre_participante;
    $_SESSION["id_usuario_xls_almacen_movimientos"] = $id_usuario;
    $_SESSION["tipo_motivos_xls_almacen_movimientos"] = $tipo_motivo;
    $_SESSION["id_ref_xls_almacen_movimientos"] = $id_ref;
    $_SESSION["fecha_desde_xls_almacen_movimientos"] = $fecha_desde;
    $_SESSION["fecha_hasta_xls_almacen_movimientos"] = $fecha_hasta;
    $_SESSION["id_tipo_participante_xls_almacen_movimientos"] = $id_tipo_participante;
    $_SESSION["id_participante_xls_almacen_movimientos"] = $id_participante;
    $_SESSION["id_almacen_xls_almacen_movimientos"] = $id_almacen;
    $_SESSION["id_sede_xls_almacen_movimientos"] = $id_sede;

    $pg_totalPaginas = ceil(count($resultadosBusqueda) / $pg_registros);

    // Se obtienen los 50 resultados correspondientes a la consulta
    $listadoMovimientos->setValores($nombre_albaran,$tipo_albaran,$id_tipo_participante,$id_participante,$tipo_motivo,$id_usuario,$id_ref,$fecha_desde,$fecha_hasta,$paginacion,$id_almacen,$id_sede);
    $listadoMovimientos->realizarConsulta();
    $resultadosBusqueda = $listadoMovimientos->movimientos;

    // Cargamos el nombre del almacen y la sede
    $almacen->cargaDatosAlmacenId($id_almacen);
    $nombre_almacen = $almacen->nombre; 
    $sede->cargaDatosSedeId($id_sede);
    $name_sede = $sede->nombre; 

	// Guardar las variables del formulario en variable de sesión
	$_SESSION["nombre_albaran_almacen_movimientos"] = stripslashes(htmlspecialchars($nombre_albaran));
	$_SESSION["tipo_albaran_almacen_movimientos"] = $tipo_albaran;
	$_SESSION["nombre_participante_almacen_movimientos"] = $nombre_participante;
	$_SESSION["id_usuario_almacen_movimientos"] = $id_usuario;
    $_SESSION["tipo_motivos_almacen_movimientos"] = $tipo_motivo;
	$_SESSION["id_ref_almacen_movimientos"] = $id_ref;
	$_SESSION["fecha_creacion_almacen_movimientos"] = $fecha_creacion;
    $_SESSION["fecha_desde_almacen_movimientos"] = $fecha_desde_aux;
    $_SESSION["fecha_hasta_almacen_movimientos"] = $fecha_hasta_aux;
    $_SESSION["id_almacen_almacen_movimientos"] = $id_almacen;
    $_SESSION["id_sede_almacen_movimientos"] = $id_sede;
}
?>

<div class="separador"></div> 
<?php include("../includes/menu_almacen_piezas.php"); ?>

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
                            $resultados_sedes = $sede->dameSedes();
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
            <div class="Label">Almacen *</div>
            <div id="capaAlmacenes">
                <select id="almacenes" name="almacenes" class="BuscadorInputAlmacen">
                    <option value="">Seleccionar</option>
                    <?php
                        if($esUsuarioFab){
                            $res_almacenes = $sede->dameAlmacenesFabricaSede($id_sede);
                        }
                        else if($esUsuarioMan){
                            $res_almacenes = $sede->dameAlmacenesMantenimientoSede($id_sede);
                        }
                        else {
                            // Obtenemos los almacenes de esa sede
                            $res_almacenes = $sede->dameAlmacenesSede($id_sede);
                        }
                        for($i=0;$i<count($res_almacenes);$i++){
                            $id_almacen_bus = $res_almacenes[$i]["id_almacen"];
                            $nombre = $res_almacenes[$i]["almacen"]; ?>
                            <option value="<?php echo $id_almacen_bus; ?>" <?php if($_SESSION["id_almacen_almacen_movimientos"] == $id_almacen_bus) echo "selected";?>><?php echo $nombre;?></option>
                    <?php
                        }
                    ?>
                </select>
            </div>
        </td>
       	<td style="width: 33%;">
           	<div class="Label">Albarán</div>
           	<input type="text" id="nombre_albaran" name="nombre_albaran" class="BuscadorInputAlmacen" value="<?php echo $_SESSION["nombre_albaran_almacen_movimientos"];?>"/>
        </td>
        <td style="width: 33%;">
            <div class="Label">Origen / Destino</div>
            <select id="nombre_participante" name="nombre_participante"  class="BuscadorInputAlmacen">
                <option></option>
                <?php
                    // Listado de Proveedores
                    $resultado_proveedores = $proveedor->dameProveedores();
                    for($i=0;$i<count($resultado_proveedores);$i++){
                        $nombre_proveedor = $resultado_proveedores[$i]["nombre_prov"];
                        echo '<option value="'.$nombre_proveedor.'" ';
                        if($nombre_proveedor == $_SESSION["nombre_participante_almacen_movimientos"]){
                            echo ' selected="selected"';
                        }
                        echo '>'.$nombre_proveedor.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(PROVEEDOR)</option>';
                    }

                    $resultado_centros = $centroLogistico->dameCentrosLogisticos();
                    for($i=0;$i<count($resultado_centros);$i++){
                        $nombre_centro = $resultado_centros[$i]["centro_logistico"];
                        echo '<option value="'.$nombre_centro.'" ';
                        if($nombre_centro == $_SESSION["nombre_participante_almacen_movimientos"]){
                            echo ' selected="selected"';
                        }
                        echo '>'.$nombre_centro.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(CENTRO LOGISTICO)</option>';
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
                    if($filtroSede) $resultado_usuarios = $usuario->dameUsuariosAlmacen();
                    else $resultado_usuarios = $sede->dameUsuariosAlmacenSede($id_sede_usuario);

                    for($i=0;$i<count($resultado_usuarios);$i++){
                        $id_usuario = $resultado_usuarios[$i]["id_usuario"];
                        $nombre_usuario = $resultado_usuarios[$i]["usuario"];
                        echo '<option value='.$id_usuario;
                        if($id_usuario == $_SESSION["id_usuario_almacen_movimientos"]){
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
                    $array_tipos_albaran = array("ENTRADA","SALIDA","AJUSTE ENTRADA", "AJUSTE SALIDA");
                    for($i=0;$i<count($array_tipos_albaran);$i++){
                        echo '<option';
                        if($array_tipos_albaran[$i] == $_SESSION["tipo_albaran_almacen_movimientos"]){
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
                    if(!empty($id_almacen)) $res_motivos = $almacen->dameMotivosAlbaran($id_almacen);
                    else $res_motivos = $sede->dameMotivosAlbaranSede($id_sede);

                    for($i=0;$i<count($res_motivos);$i++) {
                        $motivo_bus = $res_motivos[$i]["motivo"]; ?>
                        <option <?php if($_SESSION["tipo_motivos_almacen_movimientos"] == $motivo_bus) echo "selected"; ?>><?php echo $motivo_bus;?></option>
                <?php
                    }
                ?>
            </select>
        </td>
    </tr>
    <tr style="border:0;">
        <td style="width: 33%;">
            <div class="Label">ID Ref</div>
            <input type="text" id="id_ref" name="id_ref" class="BuscadorInputAlmacen" value="<?php echo $_SESSION["id_ref_almacen_movimientos"];?>" onkeypress="return soloNumeros(event)" onkeyup="cargaReferenciaIntro(event);" />
            <input type="button" name="botonBuscadorIDRef" id="botonBuscadorIDRef" class="BotonEliminar" style="float: left; margin-top: 4px;" value="+" onclick="javascript:Abrir_ventana('buscador_referencias_movimientos.php')">
        </td>
        <td style="width: 33%;">
            <div class="Label">Fecha desde</div>
            <input type="text" name="fecha_desde" id="datepicker_movimientos_desde" class="fechaCal" style="width: 175px;" value="<?php echo $_SESSION["fecha_desde_almacen_movimientos"];?>"/>
        </td>
        <td style="width: 33%;">
            <div class="Label">Fecha hasta</div>
            <input type="text" name="fecha_hasta" id="datepicker_movimientos_hasta" class="fechaCal" style="width: 175px;" value="<?php echo $_SESSION["fecha_hasta_almacen_movimientos"];?>"/>
        </td>
    </tr>
    <tr style="border:0;">
        <td style="width: 33%;">
          	<input type="hidden" id="realizandoBusqueda" name="realizandoBusqueda" value="1" />
        	<input type="submit" id="botonEnviar" name="botonEnviar" value="Buscar" />
        </td>
        <td style="width: 33%;"></td>
        <td style="width: 33%;"></td>
    </tr>
    </table>
    <br />
    <input type="hidden" id="nombreFormulario" name="nombreFormulario" value="buscadorMovimiento" />
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
    					<th style="text-align:center">ID REF</th>
    					<th>NOMBRE REF</th>
                        <th style="text-align:left">REF PROV</th>
    					<th>PROVEEDOR</th>
                        <th>ALBARAN</th>
                        <th style="text-align:center">TIPO ALBARAN</th>
                        <th>USUARIO</th>
                        <th>ORIGEN / DESTINO</th>
                        <?php 
                            if($filtroSede){ ?>
                                <th>SEDE</th>
                        <?php 
                            } 
                        ?>
                        <th>ALMACEN</th>
                        <th style="text-align:center">MOTIVO</th>
    					<th style="text-align:center">FECHA CREACION</th>
    					<th style="text-align:center">UNIDADES</th>
    				</tr>
                <?php
                    // Se cargan los datos de los movimientos según su identificador
                    for($i=0;$i<count($resultadosBusqueda);$i++) {
                        // Cargamos las referencias y obtenemos los datos del albarán de cada referencia
                        $id_referencia = $resultadosBusqueda[$i]["id_referencia"];
                        $id_albaran = $resultadosBusqueda[$i]["id_albaran"];
                        $nombre_referencia = $resultadosBusqueda[$i]["nombre_referencia"];
                        $nombre_proveedor = $resultadosBusqueda[$i]["nombre_proveedor"];
                        $cantidad = $resultadosBusqueda[$i]["cantidad"];
                        $id_usuario = $resultadosBusqueda[$i]["id_usuario"];
                        $id_almacen = $resultadosBusqueda[$i]["id_almacen"];
                        $fecha_creacion = $resultadosBusqueda[$i]["fecha_creado"];
                        $metodo = $resultadosBusqueda[$i]["metodo"];
                        $valor_positivo = ($metodo == "RECEPCIONAR") || ($metodo == "AJUSTE RECEPCIONAR");
                        if($metodo == "AJUSTE RECEPCIONAR") $metodo = "AJUSTE ENTRADA";
                        if($metodo == "AJUSTE DESRECEPCIONAR") $metodo = "AJUSTE SALIDA";

                        // Cargamos el nombre del usuario
                        $usuario->cargaDatosUsuarioId($id_usuario);
                        $nombre_usuario = $usuario->usuario;

                        if($esUsuarioBrasil) $fecha_creacion = $usuario->fechaHoraBrasil($fecha_creacion);
                        else $fecha_creacion = $usuario->fechaHoraSpain($fecha_creacion);

                        if(!empty($id_albaran)) {
                            // Cargamos los datos del albarán de la referencia
                            $albaran->cargaDatosAlbaranId($id_albaran);
                            $nombre_albaran = $albaran->nombre_albaran;
                            $tipo_albaran = $albaran->tipo_albaran;
                            $id_tipo_participante = $albaran->id_tipo_participante;
                            $id_participante = $albaran->id_participante;

                            // Cargar nombre del participante según si es proveedor o centro logístico
                            if($id_tipo_participante == 1) {
                                // PROVEEDOR
                                $proveedor->cargaDatosProveedorId($id_participante);
                                $nombre_participante = $proveedor->nombre;
                            }
                            else if($id_tipo_participante == 2) {
                                // CENTRO LOGISTICO
                                $centroLogistico->cargaDatosCentroLogisticoId($id_participante);
                                $nombre_participante = $centroLogistico->nombre;
                            }

                            $motivo = $albaran->motivo;
                            if($motivo == "") $motivo = "-";
                        }
                        else {
                            $nombre_albaran = "-";
                            $nombre_participante = "-";
                            $tipo_albaran = '<span style="color: orange;">'.$metodo.'</span>';
                            $motivo = "-";
                        }

                        // Cargamos los datos de la referencia 
                        $ref->cargaDatosReferenciaId($id_referencia);

                        // Obtenemos el nombre del almacen
                        $almacen->cargaDatosAlmacenId($id_almacen);
                        $nombre_almacen = $almacen->nombre; ?>
            		    <tr>
                            <td style="text-align:center"><?php echo $id_referencia; ?></td>
                            <td><?php echo $nombre_referencia; ?></td>
                            <td style="text-align:left"><?php $ref->vincularReferenciaProveedor(); ?></td>
                            <td><?php echo $nombre_proveedor; ?></td>
                            <td><?php echo $nombre_albaran; ?></td>
                            <td style="text-align:center"><?php echo $tipo_albaran; ?></td>
                            <td><?php echo $nombre_usuario; ?></td>
                            <td><?php echo $nombre_participante; ?></td>
                            <?php 
                                if($filtroSede){ ?>
                                    <td><?php echo $name_sede; ?></td>
                            <?php 
                                }
                            ?>
                            <td><?php echo $nombre_almacen; ?></td>
                            <td style="text-align:center"><?php echo $motivo; ?></td>
                            <td style="text-align:center"><?php echo $fecha_creacion; ?></td>
                            <td style="text-align:center">
                                <?php 
                                    if($valor_positivo){ ?>
                                        <span style="color: green;"><?php echo $cantidad;?></span>        
                                <?php
                                    }    
                                    else { ?>
                                        <span style="color: red;"><?php echo "-".$cantidad;?></span>    
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

            <?php // Después de mostrar la tabla mostramos la paginación y el botón para descargar el resultado de búsqueda ?>

            <div style="font: bold 11px Verdana,Arial; margin: 0 auto; padding: 10px 0; width: 350px; text-align: center;"> 
                <?php    
                    if(($pg_pagina - 1) > 0) { ?>
                        <a href="listado_movimientos.php?pg=1&realizandoBusqueda=1&nombre_albaran=<?php echo $_SESSION["nombre_albaran_almacen_movimientos"];?>&tipo_albaran=<?php echo $_SESSION["tipo_albaran_almacen_movimientos"];?>&nombre_participante=<?php echo $_SESSION["nombre_participante_almacen_movimientos"];?>&tipo_motivo=<?php echo $_SESSION["tipo_motivos_almacen_movimientos"];?>&id_usuario=<?php echo $_SESSION["id_usuario_almacen_movimientos"];?>&id_ref=<?php echo $_SESSION["id_ref_almacen_movimientos"];?>&fecha_creacion=<?php echo $_SESSION["fecha_creacion_almacen_movimientos"];if($_SESSION["fecha_creacion_almacen_movimientos"] == ""){?>&fecha_desde=<?php echo $_SESSION["fecha_desde_almacen_movimientos"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_almacen_movimientos"];?><?php } ?>&sedes=<?php echo $id_sede; ?>&almacenes=<?php echo $_SESSION["id_almacen_almacen_movimientos"]; ?>">Primera&nbsp&nbsp&nbsp</a>
                        <a href="listado_movimientos.php?pg=<?php echo $pg_pagina - 1;?>&realizandoBusqueda=1&nombre_albaran=<?php echo $_SESSION["nombre_albaran_almacen_movimientos"];?>&tipo_albaran=<?php echo $_SESSION["tipo_albaran_almacen_movimientos"];?>&nombre_participante=<?php echo $_SESSION["nombre_participante_almacen_movimientos"];?>&tipo_motivo=<?php echo $_SESSION["tipo_motivos_almacen_movimientos"];?>&id_usuario=<?php echo $_SESSION["id_usuario_almacen_movimientos"];?>&id_ref=<?php echo $_SESSION["id_ref_almacen_movimientos"];?>&fecha_creacion=<?php echo $_SESSION["fecha_creacion_almacen_movimientos"];if($_SESSION["fecha_creacion_almacen_movimientos"] == ""){?>&fecha_desde=<?php echo $_SESSION["fecha_desde_almacen_movimientos"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_almacen_movimientos"];?><?php } ?>&sedes=<?php echo $id_sede; ?>&almacenes=<?php echo $_SESSION["id_almacen_almacen_movimientos"]; ?>">Anterior</a>
                <?php
                    }  
                    else {
                        echo 'Primera&nbsp;&nbsp;&nbsp;Anterior';
                    }
            
                    echo ' &nbsp;&nbsp;&nbsp;['.$pg_pagina.' / '.$pg_totalPaginas.']&nbsp;&nbsp;&nbsp;';
                    if($pg_pagina < $pg_totalPaginas) { ?>
                        <a href="listado_movimientos.php?pg=<?php echo $pg_pagina + 1;?>&realizandoBusqueda=1&nombre_albaran=<?php echo $_SESSION["nombre_albaran_almacen_movimientos"];?>&tipo_albaran=<?php echo $_SESSION["tipo_albaran_almacen_movimientos"];?>&nombre_participante=<?php echo $_SESSION["nombre_participante_almacen_movimientos"];?>&tipo_motivo=<?php echo $_SESSION["tipo_motivos_almacen_movimientos"];?>&id_usuario=<?php echo $_SESSION["id_usuario_almacen_movimientos"];?>&id_ref=<?php echo $_SESSION["id_ref_almacen_movimientos"];?>&fecha_creacion=<?php echo $_SESSION["fecha_creacion_almacen_movimientos"];if($_SESSION["fecha_creacion_almacen_movimientos"] == ""){?>&fecha_desde=<?php echo $_SESSION["fecha_desde_almacen_movimientos"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_almacen_movimientos"];?><?php } ?>&sedes=<?php echo $id_sede; ?>&almacenes=<?php echo $_SESSION["id_almacen_almacen_movimientos"]; ?>">Siguiente&nbsp&nbsp&nbsp</a>
                        <a href="listado_movimientos.php?pg=<?php echo $pg_totalPaginas;?>&realizandoBusqueda=1&nombre_albaran=<?php echo $_SESSION["nombre_albaran_almacen_movimientos"];?>&tipo_albaran=<?php echo $_SESSION["tipo_albaran_almacen_movimientos"];?>&nombre_participante=<?php echo $_SESSION["nombre_participante_almacen_movimientos"];?>&tipo_motivo=<?php echo $_SESSION["tipo_motivos_almacen_movimientos"];?>&id_usuario=<?php echo $_SESSION["id_usuario_almacen_movimientos"];?>&id_ref=<?php echo $_SESSION["id_ref_almacen_movimientos"];?>&fecha_creacion=<?php echo $_SESSION["fecha_creacion_almacen_movimientos"];if($_SESSION["fecha_creacion_almacen_movimientos"] == ""){?>&fecha_desde=<?php echo $_SESSION["fecha_desde_almacen_movimientos"];?>&fecha_hasta=<?php echo $_SESSION["fecha_hasta_almacen_movimientos"];?><?php } ?>&sedes=<?php echo $id_sede; ?>&almacenes=<?php echo $_SESSION["id_almacen_almacen_movimientos"]; ?>">Última</a>
                <?php        
                    } 
                    else {
                        echo 'Siguiente&nbsp;&nbsp;&nbsp;Última';
                    }
                ?>
            </div>
            <br/>
            <div class="ContenedorBotonCrear"><input type="button" id="descargar_XLS_Movimientos" name="descargar_XLS_Movimientos" value="Descargar XLS" class="BotonEliminar" onclick="javascript:descargar_XLS_Movimientos(<?php echo $_SESSION["id_almacen_almacen_movimientos"];?>);"/></div>
    <?php        
    	}
	?>
</div>    
<?php include ('../includes/footer.php');  ?>