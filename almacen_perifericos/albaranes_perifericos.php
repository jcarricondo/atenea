<?php
// Este fichero muestra el listado de los albaranes de periféricos
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/centro_logistico.class.php");
include("../classes/basicos/usuario.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/almacen_perifericos/albaran_periferico.class.php");
include("../classes/almacen_perifericos/listado_albaranes_periferico.class.php");
include("../classes/control_usuario.class.php");
include("../classes/sede/sede.class.php");
permiso(31);

$funciones = new Funciones();
$centroLogistico = new CentroLogistico();
$usuario = new Usuario();
$almacen = new Almacen();
$albaranPeriferico = new AlbaranPeriferico();
$listadoAlbaranesPeriferico = new listadoAlbaranesPeriferico();
$control_usuario = new Control_Usuario();
$sede = new Sede();

// Obtenemos la sede del usuario
$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$id_almacen_usuario = $_SESSION["AT_id_almacen"];
$id_sede = $control_usuario->dameSedeSegunUsuario($id_tipo_usuario,$id_almacen_usuario);
$id_sede_usuario = $control_usuario->dameSedeSegunUsuario($id_tipo_usuario,$id_almacen_usuario);
$_SESSION["id_almacen_albaran_perifericos"] = $id_almacen_usuario;

$esAdminGlobal = $control_usuario->esAdministradorGlobal($id_tipo_usuario);
$esUsuarioBrasil = $control_usuario->esUsuarioBrasil($id_tipo_usuario,$id_sede_usuario);
$filtroSede = $esAdminGlobal;

// Predeterminado si el usuario sin sede asignada no escogió ninguna
if(empty($id_sede)) $id_sede = 1;
if(empty($id_sede_usuario)) $id_sede_usuario = 1;
if(empty($id_almacen_usuario)) $_SESSION["id_almacen_albaran_perifericos"] = 1;
else $_SESSION["id_almacen_albaran_perifericos"] = $_SESSION["AT_id_almacen"];

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

if(isset($_GET["cerrarAlbaran"]) and $_GET["cerrarAlbaran"] == 1) {
    $id_albaran = $_GET["id_albaran"];    
    $mostrar_tabla = true;
    $buscar = 1;   

    // Comprobamos si el albarán que se cerró estaba vacío
    $vacio = $_GET["vacio"];
    if($vacio == 1){
        $resultado = $albaranPeriferico->desactivarAlbaran($id_albaran);
        if($resultado != 1){
            $mensaje_error = $albaranPeriferico->getErrorMessage($resultado);
        }
    }
}

// Desactivamos los albaranes vacíos
$res_desactivar = $albaranPeriferico->desactivarAlbaranesVacios();
if($res_desactivar != 1) {
    $mensaje_error = $albaranPeriferico->getErrorMessage($res_desactivar);
}

// Se obtienen los datos del formulario
if((isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1) or $buscar == 1) {
	$mostrar_tabla = true;
	$nombre_albaran = addslashes($_GET["nombre_albaran"]);
	$tipo_albaran = $_GET["tipo_albaran"];
	$id_centro_logistico = $_GET["id_centro_logistico"];
	$id_usuario = $_GET["id_usuario"];
    $fecha_creacion = $_GET["fecha_creacion"];
    $num_serie = addslashes($_GET["num_serie"]);
    $motivo = $_GET["motivo"];
    if($filtroSede) $id_sede = $_GET["sedes"];

    if($_GET["cerrarAlbaran"] == 1){
        $albaranPeriferico->cargaDatosAlbaranId($id_albaran);
        $id_almacen = $albaranPeriferico->id_almacen;
    }
    else $id_almacen = $_GET["almacenes"];

    $sede_almacen = $almacen->dameSedeAlmacen($id_almacen);
    $sede_almacen = $sede_almacen["id_sede"];
    $esAlmacenBrasil = $sede_almacen == 3;

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

    $listadoAlbaranesPeriferico->setValores($nombre_albaran,$tipo_albaran,$id_centro_logistico,$id_usuario,$fecha_creacion,$num_serie,$motivo,$id_almacen,$id_sede,$fecha_creacion_ini,$fecha_creacion_fin,'');
    $listadoAlbaranesPeriferico->realizarConsulta();
    $resultadosBusqueda = $listadoAlbaranesPeriferico->albaranes;
    $num_resultados = count($resultadosBusqueda); 

	// Se realiza la consulta con paginación
    $pg_totalPaginas = ceil(count($resultadosBusqueda) / $pg_registros);
    $listadoAlbaranesPeriferico->setValores($nombre_albaran,$tipo_albaran,$id_centro_logistico,$id_usuario,$fecha_creacion,$num_serie,$motivo,$id_almacen,$id_sede,$fecha_creacion_ini,$fecha_creacion_fin,$paginacion);
    $listadoAlbaranesPeriferico->realizarConsulta();
    $resultadosBusqueda = $listadoAlbaranesPeriferico->albaranes; 

    // Guardar las variables del formulario en variable de sesión
	$_SESSION["nombre_albaran_albaran_perifericos"] = stripslashes(htmlspecialchars($nombre_albaran));
	$_SESSION["tipo_albaran_albaran_perifericos"] = $tipo_albaran;
	$_SESSION["id_centro_logistico_albaran_perifericos"] = $id_centro_logistico;
	$_SESSION["id_usuario_albaran_perifericos"] = $id_usuario;
    $_SESSION["fecha_creacion_albaran_perifericos"] = $fecha_creacion;
    $_SESSION["num_serie_albaran_perifericos"] = stripslashes(htmlspecialchars($num_serie)); 
    $_SESSION["tipo_motivo_albaran_perifericos"] = $motivo;
    $_SESSION["id_almacen_albaran_perifericos"] = $id_almacen;
    $_SESSION["id_sede_albaran_perifericos"] = $id_sede;

}

$titulo_pagina = "Almacen Periféricos > Albaranes";
$pagina = "listado_albaranes";
include("../includes/header.php");
echo '<script type="text/javascript" src="../js/almacen_perifericos/almacen_perifericos.js"></script>';
?>

<div class="separador"></div> 
<?php include("../includes/menu_almacen_perifericos.php"); ?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include("../includes/sidebar.php"); ?>
    </div>
       	
    <h3>Albaranes</h3>
    <h4></h4>

    <form id="BuscadorAlbaran" name="buscadorAlbaran" action="albaranes_perifericos.php" method="get" class="Buscador">
    <table style="border:0;">
    <?php
        if($filtroSede){?>
            <tr style="border:0;">
                <td style="width: 33%; vertical-align: top;">
                    <div class="Label">Sede</div>
                    <select id="sedes" name="sedes" class="BuscadorInputAlmacen" onchange="cambiaCamposBuscadorAlbaran(this.value)">
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
                <td style="width: 33%;"></td>
                <td style="width: 33%;"></td>
            </tr>
    <?php
        }
    ?>
    	<tr style="border:0;">
        	<td style="width:33%;">
                <div class="Label">Almacen</div>
                <div id="capaAlmacenes">
                    <select id="almacenes" name="almacenes" class="BuscadorInputAlmacen">
                        <option value="">Seleccionar</option>
                        <?php
                            // Cargamos todos los almacenes existentes según la sede del usuario
                            $resultados_almacenes = $sede->dameAlmacenesMantenimientoSede($id_sede);
                            for($i=0;$i<count($resultados_almacenes);$i++){
                                $id_almacen = $resultados_almacenes[$i]["id_almacen"];
                                $nombre_almacen = $resultados_almacenes[$i]["almacen"];

                                echo '<option value="'.$id_almacen.'"';
                                if($id_almacen == $_SESSION["id_almacen_albaran_perifericos"]){
                                    echo ' selected="selected"';
                                }
                                echo '>'.$nombre_almacen.'</option>';
                            }
                        ?>
                    </select>
                </div>
            </td>
            <td style="width:33%;">
                <div class="Label">Albarán</div>
                <input type="text" id="nombre_albaran" name="nombre_albaran" class="BuscadorInputAlmacen" value="<?php echo $_SESSION["nombre_albaran_albaran_perifericos"];?>"/>
            </td>
            <td style="width:33%;">
            	<div class="Label">Origen / Destino</div>
                <select id="id_centro_logistico" name="id_centro_logistico" class="BuscadorInputAlmacen">
                    <?php 
                        echo '<option></option>';
                        $resultado_centros = $centroLogistico->dameCentrosLogisticos();
                        for($i=0;$i<count($resultado_centros);$i++){
                            $id_centro = $resultado_centros[$i]["id_centro_logistico"];
                            $nombre_centro = $resultado_centros[$i]["centro_logistico"];
                            echo '<option value="'.$id_centro.'" ';
                            if($id_centro == $_SESSION["id_centro_logistico_albaran_perifericos"]){
                                echo ' selected="selected"';
                            }
                            echo '>'.$nombre_centro.'</option>';      
                        }                    
                    ?>        
                </select>
            </td>
        </tr>
        <tr style="border:0;">
        	<td style="width:33%;">
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
                            if($id_usuario == $_SESSION["id_usuario_albaran_perifericos"]){
                                echo ' selected="selected"';
                            }
                            echo '>'.$nombre_usuario.'</option>';      
                        }    
                    ?>
                </select>
            </td>
            <td style="width:33%;">
            	<div class="Label">Fecha creación</div>
                <input type="text" name="fecha_creacion" id="datepicker_albaranes_perifericos_creacion" class="fechaCal" style="width: 175px;" value="<?php echo $_SESSION["fecha_creacion_albaran_perifericos"];?>"/>
            </td>
            <td style="width:33%;">
                <div class="Label">Num. Serie</div>
                <input type="text" name="num_serie" id="num_serie" class="BuscadorInputAlmacen" value="<?php echo $_SESSION["num_serie_albaran_perifericos"];?>"/>
            </td>
        </tr>
        <tr style="border:0;">
        	<td style="width:33%;">
                <div class="Label">Tipo</div>
                <select id="tipo_albaran" name="tipo_albaran" class="BuscadorInputAlmacen">
                    <option></option>
                    <?php
                        $array_tipos_albaran = array("ENTRADA","SALIDA");
                        for($i=0;$i<count($array_tipos_albaran);$i++){
                            echo '<option';
                            if($array_tipos_albaran[$i] == $_SESSION["tipo_albaran_albaran_perifericos"]){
                                echo ' selected="selected"';
                            }
                            echo '>'.$array_tipos_albaran[$i].'</option>';
                        }
                    ?>
                </select>
            </td>
            <td style="width:33%;">
                <div class="Label">Motivo</div>
                <select id="motivo" name="motivo" class="BuscadorInputAlmacen">
                    <option></option>
                    <?php
                        $array_tipos_motivo = array("AJUSTE DESVIACION","COMPRA / SUMINISTRO","SERVICIO REPARACION","MERMA");
                        for($i=0;$i<count($array_tipos_motivo);$i++){
                            echo '<option';
                            if($array_tipos_motivo[$i] == $_SESSION["tipo_motivo_albaran_perifericos"]){
                                echo ' selected="selected"';
                            }
                            echo '>'.$array_tipos_motivo[$i].'</option>';
                        }
                    ?>
                </select>
            </td>
            <td style="width:33%;"></td>
        </tr>
        <tr style="border:0;">
            <td style="width:33%">
            	<input type="hidden" id="realizandoBusqueda" name="realizandoBusqueda" value="1" />
           		<input type="submit" id="botonEnviar" name="botonEnviar" value="Buscar" />
            </td>
            <td style="width:33%;"></td>
            <td style="width:33%;"></td>
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
    					<th>ORIGEN</th>
    					<th>MOTIVO</th>
                        <th style="text-align:center">FECHA CREACION</th>
                        <th style="text-align:center"></th>
    				</tr>
                <?php
        			// Se cargan los datos de los albaranes según su identificador
        			for($i=0;$i<count($resultadosBusqueda);$i++) {
                        $id_albaran = $resultadosBusqueda[$i]["id_albaran"];
                        $albaranPeriferico->cargaDatosAlbaranId($id_albaran);

                        $nombre_albaran = $albaranPeriferico->nombre_albaran;
                        $tipo_albaran = $albaranPeriferico->tipo_albaran;
                        $id_centro_logistico = $albaranPeriferico->id_centro_logistico;
                        $id_usuario = $albaranPeriferico->id_usuario;
                        $motivo = $albaranPeriferico->motivo;
                        $id_almacen = $albaranPeriferico->id_almacen;

                        // Obtenemos la sede del almacen al que pertenece el albarán
                        $sede_almacen = $almacen->dameSedeAlmacen($id_almacen);
                        $sede_almacen = $sede_almacen["id_sede"];
                        $esAlmacenBrasil = $sede_almacen == 3;

                        // Cargamos el nombre del usuario
                        $usuario->cargaDatosUsuarioId($id_usuario);
                        $nombre_usuario = $usuario->usuario;

                        // Cargar nombre del centro logístico
                        $centroLogistico->cargaDatosCentroLogisticoId($id_centro_logistico);
                        $nombre_centro = $centroLogistico->nombre;

                        // Cargamos el nombre del almacen
                        $almacen->cargaDatosAlmacenId($id_almacen);
                        $nombre_almacen = $almacen->nombre;

                        // Convertimos la fecha
                        $fecha_creacion = $albaranPeriferico->fecha_creado;
                        if($esAlmacenBrasil) $fecha_creacion = $usuario->fechaHoraBrasil($fecha_creacion);
                        else $fecha_creacion = $usuario->fechaHoraSpain($fecha_creacion); ?>

                        <tr>
                            <td><?php echo $nombre_albaran;?></td>
                            <td><?php echo $tipo_albaran;?></td>
                            <td><?php echo $nombre_usuario;?></td>
                            <td><?php echo $nombre_almacen;?></td>
                            <td><?php echo $nombre_centro;?></td>
                            <td><?php echo $motivo;?></td>
                            <td style="text-align:center;"><?php echo $fecha_creacion;?></td>
                            <td style="text-align:center;">
                                <a href="../almacen_perifericos/informe_albaran.php?id_albaran=<?php echo $id_albaran;?>">XLS</a>
                            </td>
                        </tr>
                <?php
    				}
    			?>
				</table>                  
			</div>
		<?php
            // PAGINACIÓN
            if((isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) and $resultadosBusqueda != NULL) { ?>
                <div style="font: bold 11px Verdana,Arial; margin: 0 auto; padding: 10px 0; width: 350px; text-align: center;"> 
                <?php    
                    if(($pg_pagina - 1) > 0) { ?>
                        <a href="albaranes_perifericos.php?pg=1&realizandoBusqueda=1&nombre_albaran=<?php echo $_SESSION["nombre_albaran_albaran_perifericos"];?>&tipo_albaran=<?php echo $_SESSION["tipo_albaran_albaran_perifericos"];?>&id_centro_logistico=<?php echo $_SESSION["id_centro_logistico_albaran_perifericos"];?>&id_usuario=<?php echo $_SESSION["id_usuario_albaran_perifericos"];?>&fecha_creacion=<?php echo $_SESSION["fecha_creacion_albaran_perifericos"];?>&num_serie=<?php echo $_SESSION["num_serie_albaran_perifericos"];?>&motivo=<?php echo $_SESSION["tipo_motivo_albaran_perifericos"];?>&almacenes=<?php echo $_SESSION["id_almacen_albaran_perifericos"];?>&sedes=<?php echo $_SESSION["id_sede_albaran_perifericos"];?>">Primera&nbsp&nbsp&nbsp</a>
                        <a href="albaranes_perifericos.php?pg=<?php echo $pg_pagina - 1;?>&realizandoBusqueda=1&nombre_albaran=<?php echo $_SESSION["nombre_albaran_albaran_perifericos"];?>&tipo_albaran=<?php echo $_SESSION["tipo_albaran_albaran_perifericos"];?>&id_centro_logistico=<?php echo $_SESSION["id_centro_logistico_albaran_perifericos"];?>&id_usuario=<?php echo $_SESSION["id_usuario_albaran_perifericos"];?>&fecha_creacion=<?php echo $_SESSION["fecha_creacion_albaran_perifericos"];?>&num_serie=<?php echo $_SESSION["num_serie_albaran_perifericos"];?>&motivo=<?php echo $_SESSION["tipo_motivo_albaran_perifericos"];?>&almacenes=<?php echo $_SESSION["id_almacen_albaran_perifericos"];?>&sedes=<?php echo $_SESSION["id_sede_albaran_perifericos"];?>"> Anterior</a>
                <?php  
                    }  
                    else {
                        echo 'Primera&nbsp;&nbsp;&nbsp;Anterior';
                    }
            
                    echo ' &nbsp;&nbsp;&nbsp;['.$pg_pagina.' / '.$pg_totalPaginas.']&nbsp;&nbsp;&nbsp;';
                    if($pg_pagina < $pg_totalPaginas) { ?>
                        <a href="albaranes_perifericos.php?pg=<?php echo $pg_pagina + 1;?>&realizandoBusqueda=1&nombre_albaran=<?php echo $_SESSION["nombre_albaran_albaran_perifericos"];?>&tipo_albaran=<?php echo $_SESSION["tipo_albaran_albaran_perifericos"];?>&id_centro_logistico=<?php echo $_SESSION["id_centro_logistico_albaran_perifericos"];?>&id_usuario=<?php echo $_SESSION["id_usuario_albaran_perifericos"];?>&fecha_creacion=<?php echo $_SESSION["fecha_creacion_albaran_perifericos"];?>&num_serie=<?php echo $_SESSION["num_serie_albaran_perifericos"];?>&motivo=<?php echo $_SESSION["tipo_motivo_albaran_perifericos"];?>&almacenes=<?php echo $_SESSION["id_almacen_albaran_perifericos"];?>&sedes=<?php echo $_SESSION["id_sede_albaran_perifericos"];?>">Siguiente&nbsp&nbsp&nbsp</a>
                        <a href="albaranes_perifericos.php?pg=<?php echo $pg_totalPaginas; ?>&realizandoBusqueda=1&nombre_albaran=<?php echo $_SESSION["nombre_albaran_albaran_perifericos"];?>&tipo_albaran=<?php echo $_SESSION["tipo_albaran_albaran_perifericos"];?>&id_centro_logistico=<?php echo $_SESSION["id_centro_logistico_albaran_perifericos"];?>&id_usuario=<?php echo $_SESSION["id_usuario_albaran_perifericos"];?>&fecha_creacion=<?php echo $_SESSION["fecha_creacion_albaran_perifericos"];?>&num_serie=<?php echo $_SESSION["num_serie_albaran_perifericos"];?>&motivo=<?php echo $_SESSION["tipo_motivo_albaran_perifericos"];?>&almacenes=<?php echo $_SESSION["id_almacen_albaran_perifericos"];?>&sedes=<?php echo $_SESSION["id_sede_albaran_perifericos"];?>">Última</a>
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
<?php include ('../includes/footer.php');  ?>
    