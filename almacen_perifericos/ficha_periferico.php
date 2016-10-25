<?php
// Ficha del periférico del almacen con la posibilidad de cambiar su estado
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/basicos/usuario.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/almacen_perifericos/periferico_almacen.class.php");
include("../classes/almacen_perifericos/albaran_periferico.class.php");
include("../classes/almacen_perifericos/listado_perifericos_almacen.class.php");
include("../classes/control_usuario.class.php");
include("../classes/sede/sede.class.php");
permiso(31);

$user = new Usuario();
$almacen = new Almacen();
$perifericoAlmacen = new PerifericoAlmacen();
$albaranPeriferico = new AlbaranPeriferico();
$control_usuario = new Control_Usuario();
$sede = new Sede();

// Obtenemos la sede del usuario
$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$id_almacen_usuario = $_SESSION["AT_id_almacen"];
$id_sede_usuario = $control_usuario->dameSedeSegunUsuario($id_tipo_usuario,$id_almacen_usuario);

$esAdminGlobal = $control_usuario->esAdministradorGlobal($id_tipo_usuario);
$esAdminAlmacen = $control_usuario->esAdministradorAlmacen($id_tipo_usuario);
$esUsuarioBrasil = $control_usuario->esUsuarioBrasil($id_tipo_usuario,$id_sede_usuario);

if(isset($_POST["guardandoPeriferico"]) and $_POST["guardandoPeriferico"] == 1) {
    $id_periferico = $_POST["id_periferico"];
    $comentarios = $_POST["comentarios"];

    // Guardamos los comentarios
    $resultado = $perifericoAlmacen->guardarComentarios($id_periferico,$comentarios);
    if($resultado == 1){
        // Redirigimos al listado de Periféricos
        header("Location: listado_perifericos.php?realizandoBusqueda=1&id_periferico=".$id_periferico);
    }
    else {
        $mensaje_error = $perifericoAlmacen->getErrorMessage($resultado);
    }
}

$id_periferico = $_GET["id_periferico"];
// Cargamos los datos del Periférico
$perifericoAlmacen->cargaDatosPerifericoId($id_periferico);
$numero_serie = $perifericoAlmacen->numero_serie;
$tipo_periferico = $perifericoAlmacen->tipo_periferico;
$fecha_creacion = $perifericoAlmacen->fecha_creado;
$estado = $perifericoAlmacen->estado;
$comentarios = $perifericoAlmacen->comentarios;
$id_almacen = $perifericoAlmacen->id_almacen;
$esAlmacenBrasil = $almacen->esAlmacenBrasil($id_almacen);

// Puede modificar si:
// 1º Es ADMIN GLOBAL
// 2º Es ADMIN ALMACEN y coincide su SEDE
$res_sede_almacen = $almacen->dameSedeAlmacen($id_almacen);
$id_sede_almacen = $res_sede_almacen["id_sede"];
$esAlmacenAdminAlmacen = $esAdminAlmacen && ($id_sede_usuario == $id_sede_almacen);
// 3º Es USUARIO ALMACEN y coincide su ALMACEN
$coincidenAlmacenes = $id_almacen == $_SESSION["AT_id_almacen"];

$puedeModificar = $esAdminGlobal || $esAlmacenAdminAlmacen || $coincidenAlmacenes;

// Convertimos la fecha para mostrar la hora de Brasil para los usuarios de Brasil
// y para el Admin Global con selección de periférico brasileño
if($esUsuarioBrasil || ($esAdminGlobal && $esAlmacenBrasil)) $fecha_creacion = $user->fechaHoraBrasil($fecha_creacion);
else $fecha_creacion = $user->fechaHoraSpain($fecha_creacion);

// Obtenemos el nombre del almacen
$almacen->cargaDatosAlmacenId($id_almacen);
$nombre_almacen = $almacen->nombre;

// Cargamos el nombre del tipo del periférico
$datos_nombre_tipo = $perifericoAlmacen->dameNombreTipoPeriferico($tipo_periferico);
$nombre_tipo_periferico = $datos_nombre_tipo["nombre"];

$titulo_pagina = "Almacen Periféricos > Ficha del Periférico";
$pagina = "ficha_periferico";
include("../includes/header.php");
echo '<script type="text/javascript" src="../js/almacen_perifericos/almacen_perifericos.js"></script>';
?>

<div class="separador"></div>
<?php include("../includes/menu_almacen_perifericos.php"); ?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>

    <h3>Ficha del Perif&eacute;rico</h3>

    <form id="FormularioCreacionBasico" name="fichaPeriferico" action="ficha_periferico.php" method="post">
        <br/>
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Num. Serie</div>
            <label id="numero_serie" class="LabelInfoOP" style="width:750px;"><?php echo $numero_serie;?></label>
        </div>
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Tipo Perif&eacute;rico</div>
            <label id="nombre_tipo_periferico" class="LabelInfoOP" style="width:750px;"><?php echo $nombre_tipo_periferico;?></label>
        </div>
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Almacen</div>
            <label id="nombre_almacen_periferico" class="LabelInfoOP" style="width:750px;"><?php echo $nombre_almacen;?></label>
        </div>
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Estado</div>
            <label id="estado" class="LabelInfoOP" style="width:750px;"><?php echo $estado; ?></label>
        </div>
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Fecha Creaci&oacute;n</div>
            <label id="fecha_creacion" class="LabelInfoOP" style="width:750px;"><?php echo $fecha_creacion;?></label>
        </div>
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Comentarios</div>
            <?php
                // Comprobamos que el usuario pertenece al mismo almacen donde esta el periférico
                if($puedeModificar){ ?>
                    <textarea type="text" id="comentarios" name="comentarios" rows="5" class="textareaInput"><?php echo $comentarios; ?></textarea> 
            <?php
                }
                else { ?>
                    <label id="label_comentarios" class="LabelInfoOP" style="width:750px;">
                        <?php
                            if($comentarios != NULL) echo $comentarios;
                            else echo "-";
                        ?>
                    </label>
            <?php
                }
            ?>       
        </div>
        <br/>
        <?php
            // Comprobamos que el usuario pertenece al mismo almacen donde está el periférico
            if($puedeModificar){ ?>
                <div id="cambiar_estado" class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Cambiar estado a</div>
                    <div id="boton_estado">
                    <?php
                        if($estado == "AVERIADO"){ ?>
                            <input type="button" class="BotonEliminar" style="margin-left: 10px;" value="EN REPARACION" onclick="cambiarEstado(<?php echo $id_periferico;?>,'<?php echo $estado;?>')">
                    <?php
                        }
                        else if ($estado == "EN REPARACION"){ ?>
                            <input type="button" class="BotonEliminar" style="margin-left: 10px;" value="OPERATIVO" onclick="cambiarEstado(<?php echo $id_periferico;?>,'<?php echo $estado;?>')">
                    <?php
                        }
                        else { ?>
                            <input type="button" class="BotonEliminar" style="margin-left: 10px;" value="AVERIADO" onclick="cambiarEstado(<?php echo $id_periferico;?>,'<?php echo $estado;?>')">
                    <?php
                        }
                    ?>
                    </div>
                </div>
        <?php
            }
        ?>
        <br/>
        
        <div class="ContenedorCamposCreacionBasico">
        <?php
            if($puedeModificar){ ?>
                <input type="button" class="BotonEliminar" style="margin-left: 5px;" value="VOLVER" onclick="history.back();" />
                <input type="submit" class="BotonEliminar" style="margin-left: 5px;" value="GUARDAR"/>
                <input type="hidden" id="guardandoPeriferico" name="guardandoPeriferico" value="1"/>
                <input type="hidden" id="id_periferico" name="id_periferico" value="<?php echo $id_periferico; ?>"/>
        <?php
            }
            else{ ?>
                <input type="button" class="BotonEliminar" style="margin-left: 5px;" value="VOLVER" onclick="history.back();" />
        <?php
            }
        ?>            
        </div>
        <br/>
        <div id="mensaje_error" class="ContenedorCamposCreacionBasico" style="color: red;">
            <?php echo $mensaje_error;?>
        </div>
        <input type="hidden" id="id_usuario_hidden" name="id_usuario_hidden" value="<?php echo $_SESSION["AT_id_usuario"];?>">
    </form>
</div>
<?php include ("../includes/footer.php"); ?>
