<?php
// Este fichero muestra el listado de los albaranes
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/proveedor.class.php");
include("../classes/basicos/centro_logistico.class.php");
include("../classes/basicos/usuario.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/almacen/albaran.class.php");
include("../classes/almacen/listado_albaranes.class.php");
include("../classes/control_usuario.class.php");
include("../classes/sede/sede.class.php");
permiso(21);

$proveedor = new Proveedor();
$centroLogistico = new CentroLogistico();
$albaran = new Albaran();
$funciones = new Funciones();
$listadoAlbaranes = new listadoAlbaranes();
$usuario = new Usuario();
$almacen = new Almacen();
$sede = new Sede();
$control_usuario = new Control_Usuario();

$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$id_almacen_usuario = $_SESSION["AT_id_almacen"];
$id_sede = $control_usuario->dameSedeSegunUsuario($id_tipo_usuario,$id_almacen_usuario);
$id_sede_usuario = $control_usuario->dameSedeSegunUsuario($id_tipo_usuario,$id_almacen_usuario);
$_SESSION["id_almacen_albaran_albaran"] = $id_almacen_usuario;

$esAdminGlobal = $control_usuario->esAdministradorGlobal($id_tipo_usuario);
$esUsuarioGes = $control_usuario->esUsuarioGes($id_tipo_usuario);
$esUsuarioDis = $control_usuario->esUsuarioDis($id_tipo_usuario);
$esUsuarioBrasil = $control_usuario->esUsuarioBrasil($id_tipo_usuario,$id_sede_usuario);
$esUsuarioFab = $control_usuario->esUsuarioFab($id_tipo_usuario);
$esUsuarioMan = $control_usuario->esUsuarioMan($id_tipo_usuario);
$filtroSede = $esAdminGlobal || $esUsuarioGes || $esUsuarioDis;

// Predeterminado si el usuario sin sede asignada no escogió ninguna
if(empty($id_sede)) $id_sede = 1;
if(empty($id_sede_usuario)) $id_sede_usuario = 1;
if(empty($id_almacen_usuario)) $_SESSION["id_almacen_albaran_albaran"] = 1;
else $_SESSION["id_almacen_albaran_albaran"] = $_SESSION["AT_id_almacen"];

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

if(isset($_GET["cerrarAlbaran"]) and $_GET["cerrarAlbaran"] == 1) {
    $id_albaran = $_GET["id_albaran"];    
    $mostrar_tabla = true;
    $buscar = 1; 

    // Comprobamos si el albarán que se cerró estaba vacío
    $vacio = $_GET["vacio"];

    if($vacio == 1){
        $resultado = $albaran->desactivarAlbaran($id_albaran);
        if($resultado != 1){
            $mensaje_error = $albaran->getErrorMessage($resultado);
        }
    }   
}

// Desactivamos los albaranes vacíos
$res_desactivar = $albaran->desactivarAlbaranesVacios();
if($res_desactivar != 1) {
    $mensaje_error = $albaran->getErrorMessage($res_desactivar);
}

// Se obtienen los datos del formulario
if((isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1) or $buscar == 1) {
	$mostrar_tabla = true;
	$nombre_albaran = addslashes($_GET["nombre_albaran"]);
	$tipo_albaran = $_GET["tipo_albaran"];
	$nombre_participante = $_GET["nombre_participante"];
    $tipo_motivo = $_GET["tipo_motivo"];
	$id_usuario = $_GET["id_usuario"];
    $fecha_creacion = $_GET["fecha_creacion"];
	$id_ref = $_GET["id_ref"];
    if($filtroSede) $id_sede = $_GET["sedes"];

    if($_GET["cerrarAlbaran"] == 1){
        $albaran->cargaDatosAlbaranId($id_albaran);
        $id_almacen = $albaran->id_almacen;
    }
    else $id_almacen = $_GET["almacenes"];

    $esAlmacenBrasil = $almacen->esAlmacenBrasil($id_almacen);

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

    // Preparamos la fecha para la consulta
    if($fecha_creacion != "") {
        // Rango de fechas para la fecha de Brasil establecida
        $fecha_creacion_ini = $funciones->cFechaMy($fecha_creacion);
        $fecha_creacion_fin = $funciones->cFechaMy($fecha_creacion);

        if($esUsuarioBrasil || ($esAdminGlobal && $esAlmacenBrasil)) {
            $date = new DateTime($fecha_creacion_ini);
            $fecha_creacion_ini = $date->add(new DateInterval('PT5H'));
            $fecha_creacion_ini = $date->format('Y-m-d H:i:s');

            $date = new DateTime($fecha_creacion_fin . " + 1 days");
            $fecha_creacion_fin = $date->add(new DateInterval('PT5H'));
            $fecha_creacion_fin = $date->format('Y-m-d H:i:s');
        }
        else {
            $date = new DateTime($fecha_creacion_ini);
            $fecha_creacion_ini = $date->format('Y-m-d H:i:s');
            $date = new DateTime($fecha_creacion_fin . " + 1 days");
            $fecha_creacion_fin = $date->format('Y-m-d H:i:s');
        }
    }

    $listadoAlbaranes->setValores($nombre_albaran,$tipo_albaran,$id_tipo_participante,$id_participante,$tipo_motivo,$id_usuario,$fecha_creacion,$id_ref,$id_almacen,$id_sede,$fecha_creacion_ini,$fecha_creacion_fin,'');
    $listadoAlbaranes->realizarConsulta();
    $resultadosBusqueda = $listadoAlbaranes->albaranes;
    $num_resultados = count($resultadosBusqueda); 

    // Se realiza la consulta con paginación
    $pg_totalPaginas = ceil(count($resultadosBusqueda) / $pg_registros);
    $listadoAlbaranes->setValores($nombre_albaran,$tipo_albaran,$id_tipo_participante,$id_participante,$tipo_motivo,$id_usuario,$fecha_creacion,$id_ref,$id_almacen,$id_sede,$fecha_creacion_ini,$fecha_creacion_fin,$paginacion);
    $listadoAlbaranes->realizarConsulta();
    $resultadosBusqueda = $listadoAlbaranes->albaranes; 

	// Convierte la fecha a formato HTML
	if($fecha_creacion != "") $fecha_creacion = $funciones->cFechaNormal($fecha_creacion);
	
	// Guardar las variables del formulario en variables de sesión
	$_SESSION["nombre_albaran_albaran"] = stripslashes(htmlspecialchars($nombre_albaran));
	$_SESSION["tipo_albaran_albaran"] = $tipo_albaran;
	$_SESSION["nombre_participante_albaran"] = $nombre_participante;
	$_SESSION["id_usuario_albaran"] = $id_usuario;
    $_SESSION["tipo_motivos_albaran"] = $tipo_motivo;
	$_SESSION["id_ref_albaran"] = $id_ref;
	$_SESSION["fecha_creacion_albaran"] = $fecha_creacion;
    $_SESSION["id_almacen_albaran_albaran"] = $id_almacen;
    $_SESSION["id_sede_albaran_albaran"] = $id_sede;
}

$titulo_pagina = "Almacen > Albaranes";
$pagina = "listado_albaranes";
include("../includes/header.php");
echo '<script type="text/javascript" src="../js/almacen/almacen.js"></script>';
?>

<div class="separador"></div> 
<?php include("../includes/menu_almacen_piezas.php"); ?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include("../includes/sidebar.php"); ?>
    </div>
       	
    <h3>Albaranes</h3>
    <h4>Buscar albarán</h4>

    <form id="BuscadorAlbaran" name="buscadorAlbaran" action="albaranes.php" method="get" class="Buscador">
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
                    <select id="almacenes" name="almacenes" class="BuscadorInputAlmacen">
                        <option value="">Seleccionar</option>
                    <?php
                        if($esUsuarioFab){
                            // Obtenemos los almacenes de fabrica de esa sede
                            $res_almacenes = $sede->dameAlmacenesFabricaSede($id_sede);
                        }
                        else if($esUsuarioMan){
                            // Obtenemos los almacenes de mantenimiento de esa sede
                            $res_almacenes = $sede->dameAlmacenesMantenimientoSede($id_sede);
                        }
                        else {
                            // Obtenemos los almacenes de esa sede
                            $res_almacenes = $sede->dameAlmacenesSede($id_sede);
                        }
                        for($i=0;$i<count($res_almacenes);$i++){
                            $id_almacen_bus = $res_almacenes[$i]["id_almacen"];
                            $nombre = $res_almacenes[$i]["almacen"]; ?>

                            <option value="<?php echo $id_almacen_bus;?>"
                                <?php if($_SESSION["id_almacen_albaran_albaran"] == $id_almacen_bus) echo 'selected="selected" '?>><?php echo $nombre;?>
                            </option>
                    <?php
                        }
                    ?>
                    </select>
                </div>
            </td>
        	<td style="width: 33%;">
            	<div class="Label">Albarán</div>
            	<input type="text" id="nombre_albaran" name="nombre_albaran" class="BuscadorInputAlmacen" value="<?php echo $_SESSION["nombre_albaran_albaran"];?>"/>
            </td>
            <td style="width: 33%;">
                <div class="Label">Origen / Destino</div>
                <select id="nombre_participante" name="nombre_participante" class="BuscadorInputAlmacen">
                    <option></option>
                    <?php
                    // Listado de Proveedores
                    $resultado_proveedores = $proveedor->dameProveedores();
                    for($i=0;$i<count($resultado_proveedores);$i++){
                        $nombre_proveedor = $resultado_proveedores[$i]["nombre_prov"];
                        echo '<option value="'.$nombre_proveedor.'" ';
                        if($nombre_proveedor == $_SESSION["nombre_participante_albaran"]){
                            echo ' selected="selected"';
                        }
                        echo '>'.$nombre_proveedor.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(PROVEEDOR)</option>';
                    }

                    $resultado_centros = $centroLogistico->dameCentrosLogisticos();
                    for($i=0;$i<count($resultado_centros);$i++){
                        $nombre_centro = $resultado_centros[$i]["centro_logistico"];
                        echo '<option value="'.$nombre_centro.'" ';
                        if($nombre_centro == $_SESSION["nombre_participante_albaran"]){
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
                            if($id_usuario == $_SESSION["id_usuario_albaran"]){
                                echo ' selected="selected"';
                            }
                            echo '>'.$nombre_usuario.'</option>';      
                        }    
                    ?>
                </select>
            </td>
            <td style="width: 33%;">
            	<div class="Label">Fecha creación</div>
                <input type="text" name="fecha_creacion" id="datepicker_albaranes_desde" class="fechaCal" style="width: 175px;" value="<?php echo $_SESSION["fecha_creacion_albaran"];?>"/>
            </td>
            <td style="width: 33%;">
                <div class="Label">ID Ref</div>
                <input type="text" id="id_ref" name="id_ref" class="BuscadorInputAlmacen" value="<?php echo $_SESSION["id_ref_albaran"];?>" onkeypress="return soloNumeros(event)"/>
            </td>
        </tr>
        <tr style="border:0;">
            <td style="width: 33%;">
                <div class="Label">Tipo</div>
                <select id="tipo_albaran" name="tipo_albaran"  class="BuscadorInputAlmacen">
                    <option></option>
                    <?php
                        $array_tipos_albaran = array("ENTRADA","SALIDA");
                        for($i=0;$i<count($array_tipos_albaran);$i++){
                            echo '<option';
                            if($array_tipos_albaran[$i] == $_SESSION["tipo_albaran_albaran"]){
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
                            <option <?php if($_SESSION["tipo_motivos_albaran"] == $motivo_bus) echo "selected"; ?>><?php echo $motivo_bus;?></option>
                    <?php
                        }
                    ?>
                </select>
                <input type="hidden" id="motivo_session" value="<?php echo $_SESSION["tipo_motivos_albaran"]; ?>"
            </td>
            <td style="width: 33%;"></td>
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
                        <?php if($filtroSede){ ?> <th>SEDE</th> <?php } ?>
                        <th>ALMACEN</th>
    					<th>ORIGEN / DESTINO</th>
    					<th style="text-align:center">MOTIVO</th>
    					<th style="text-align:center">FECHA CREACION</th>
    					<th style="text-align:center"></th>
    				</tr>
                <?php
        			// Se cargan los datos de los albaranes según su identificador
        			for($i=0;$i<count($resultadosBusqueda);$i++) {
                        $id_albaran = $resultadosBusqueda[$i]["id_albaran"];
                        $albaran->cargaDatosAlbaranId($id_albaran);

                        $nombre_albaran = $albaran->nombre_albaran;
                        $tipo_albaran = $albaran->tipo_albaran;
                        $id_tipo_participante = $albaran->id_tipo_participante;
                        $id_participante = $albaran->id_participante;
                        $id_usuario = $albaran->id_usuario;
                        $id_almacen = $albaran->id_almacen;
                        $fecha_creacion = $albaran->fecha_creado;

                        // Cargamos el nombre del usuario
                        $usuario->cargaDatosUsuarioId($id_usuario);
                        $nombre_usuario = $usuario->usuario;

                        // Cargar nombre del participante según si es proveedor o centro logístico
                        if($id_tipo_participante == 1){
                            // PROVEEDOR
                            $proveedor->cargaDatosProveedorId($id_participante);
                            $nombre_participante = $proveedor->nombre;
                        }
                        else if($id_tipo_participante == 2){ 
                            // CENTRO LOGISTICO
                            $centroLogistico->cargaDatosCentroLogisticoId($id_participante);
                            $nombre_participante = $centroLogistico->nombre;
                        }
                        else{
                            // ERROR
                        }

                        $motivo = $albaran->motivo;
                        if ($motivo == ""){
                            $motivo = "-";
                        }

                        if($esUsuarioBrasil) $fecha_creacion = $usuario->fechaHoraBrasil($fecha_creacion);
                        else $fecha_creacion = $usuario->fechaHoraSpain($fecha_creacion);

                        // Cargamos el nombre del almacen
                        $almacen->cargaDatosAlmacenId($id_almacen);
                        $nombre_almacen = $almacen->nombre;

                        // Para el Admin GLobal cargamos el nombre de la sede
                        $id_sede_alb = $almacen->dameSedeAlmacen($albaran->id_almacen);
                        $sede->cargaDatosSedeId($id_sede_alb["id_sede"]);
                        $nombre_sede = $sede->nombre;
       			?>
    				<tr>
    					<td><?php echo $nombre_albaran;?></td>
    					<td><?php echo $tipo_albaran;?></td>
                        <td><?php echo $nombre_usuario;?></td>
                        <?php if($filtroSede) { ?> <td><?php echo $nombre_sede; ?></td><?php } ?>
                        <td><?php echo $nombre_almacen;?></td>
    					<td><?php echo $nombre_participante;?></td>
    					<td style="text-align:center;"><?php echo $motivo;?></td>
                        <td style="text-align:center;"><?php echo $fecha_creacion;?></td>
    					<td style="text-align:center;">
                            <a href="../almacen/informe_albaran.php?id_albaran=<?php echo $id_albaran;?>&id_almacen=<?php echo $id_almacen;?>">XLS</a>
                        </td> 
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
                        <a href="albaranes.php?pg=1&realizandoBusqueda=1&nombre_albaran=<?php echo $_SESSION["nombre_albaran_albaran"];?>&tipo_albaran=<?php echo $_SESSION["tipo_albaran_albaran"];?>&nombre_participante=<?php echo $_SESSION["nombre_participante_albaran"];?>&tipo_motivo=<?php echo $_SESSION["tipo_motivos_albaran"];?>&id_usuario=<?php echo $_SESSION["id_usuario_albaran"];?>&fecha_creacion=<?php echo $_SESSION["fecha_creacion_albaran"];?>&id_ref=<?php echo $_SESSION["id_ref_albaran"];?>&sedes=<?php echo $_SESSION["id_sede_albaran_albaran"]; ?>&almacenes=<?php echo $_SESSION["id_almacen_albaran_albaran"];?>">Primera&nbsp&nbsp&nbsp</a>
                        <a href="albaranes.php?pg=<?php echo $pg_pagina - 1;?>&realizandoBusqueda=1&nombre_albaran=<?php echo $_SESSION["nombre_albaran_albaran"];?>&tipo_albaran=<?php echo $_SESSION["tipo_albaran_albaran"];?>&nombre_participante=<?php echo $_SESSION["nombre_participante_albaran"];?>&tipo_motivo=<?php echo $_SESSION["tipo_motivos_albaran"];?>&id_usuario=<?php echo $_SESSION["id_usuario_albaran"];?>&fecha_creacion=<?php echo $_SESSION["fecha_creacion_albaran"];?>&id_ref=<?php echo $_SESSION["id_ref_albaran"];?>&sedes=<?php echo $_SESSION["id_sede_albaran_albaran"]; ?>&almacenes=<?php echo $_SESSION["id_almacen_albaran_albaran"];?>"> Anterior</a>
                <?php  
                    }  
                    else {
                        echo 'Primera&nbsp;&nbsp;&nbsp;Anterior';
                    }
            
                    echo ' &nbsp;&nbsp;&nbsp;['.$pg_pagina.' / '.$pg_totalPaginas.']&nbsp;&nbsp;&nbsp;';
                    if($pg_pagina < $pg_totalPaginas) { ?>
                        <a href="albaranes.php?pg=<?php echo $pg_pagina + 1;?>&realizandoBusqueda=1&nombre_albaran=<?php echo $_SESSION["nombre_albaran_albaran"];?>&tipo_albaran=<?php echo $_SESSION["tipo_albaran_albaran"];?>&nombre_participante=<?php echo $_SESSION["nombre_participante_albaran"];?>&tipo_motivo=<?php echo $_SESSION["tipo_motivos_albaran"];?>&id_usuario=<?php echo $_SESSION["id_usuario_albaran"];?>&fecha_creacion=<?php echo $_SESSION["fecha_creacion_albaran"];?>&id_ref=<?php echo $_SESSION["id_ref_albaran"];?>&sedes=<?php echo $_SESSION["id_sede_albaran_albaran"]; ?>&almacenes=<?php echo $_SESSION["id_almacen_albaran_albaran"];?>">Siguiente&nbsp&nbsp&nbsp</a>
                        <a href="albaranes.php?pg=<?php echo $pg_totalPaginas; ?>&realizandoBusqueda=1&nombre_albaran=<?php echo $_SESSION["nombre_albaran_albaran"];?>&tipo_albaran=<?php echo $_SESSION["tipo_albaran_albaran"];?>&nombre_participante=<?php echo $_SESSION["nombre_participante_albaran"];?>&tipo_motivo=<?php echo $_SESSION["tipo_motivos_albaran"];?>&id_usuario=<?php echo $_SESSION["id_usuario_albaran"];?>&fecha_creacion=<?php echo $_SESSION["fecha_creacion_albaran"];?>&id_ref=<?php echo $_SESSION["id_ref_albaran"];?>&sedes=<?php echo $_SESSION["id_sede_albaran_albaran"]; ?>&almacenes=<?php echo $_SESSION["id_almacen_albaran_albaran"];?>">Última</a>
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

    