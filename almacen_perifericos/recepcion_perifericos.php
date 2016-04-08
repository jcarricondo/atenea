<?php
// PROCESO DE RECEPCION DE PERIFERICOS
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/basicos/centro_logistico.class.php");
include("../classes/basicos/usuario.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/almacen_perifericos/albaran_periferico.class.php");
include("../classes/control_usuario.class.php");
include("../classes/sede/sede.class.php");
include("../classes/log/log_almacen.class.php");
permiso(29);

$centroLogistico = new CentroLogistico();
$user = new Usuario();
$almacen = new Almacen();
$albaranPeriferico = new AlbaranPeriferico();
$control_usuario = new Control_Usuario();
$sede = new Sede();
$log = new Log_Almacen();

// Obtenemos la sede del usuario
$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$id_almacen_usuario = $_SESSION["AT_id_almacen"];
if(empty($id_almacen_usuario)) $id_almacen_usuario = 1;
$id_sede_usuario = $control_usuario->dameSedeSegunUsuario($id_tipo_usuario,$id_almacen_usuario);

$esAdminGlobal = $control_usuario->esAdministradorGlobal($id_tipo_usuario);
$esAdminAlmacen = $control_usuario->esAdministradorAlmacen($id_tipo_usuario);
$esUsuarioBrasil = $control_usuario->esUsuarioBrasil($id_tipo_usuario,$id_sede_usuario);

if(isset($_POST["iniciarAlbaran"]) and $_POST["iniciarAlbaran"] == 1){
    // Comprobamos que se rellenaron todos los datos
    $nombre_albaran = $_POST["nombre_albaran"];
    if($nombre_albaran != ""){
        // Guarda el registro de albaran y prepara las tablas para la recepcion
        $tipo_albaran = "ENTRADA";
        $id_centro_logistico = $_POST["id_centro_logistico"];
        $motivo = $_POST["motivo"];
        $id_usuario = $_SESSION["AT_id_usuario"];
        
        if($esAdminGlobal || $esAdminAlmacen){
            $id_almacen = $_POST["almacenes"];
        }
        else {
            $id_almacen = $_SESSION["AT_id_almacen"];
        }

        $albaranPeriferico->datosNuevoAlbaran($id_albaran,$nombre_albaran,$tipo_albaran,$id_centro_logistico,$motivo,$id_usuario,$id_almacen,$fecha,$activo);
        $resultado = $albaranPeriferico->guardarAlbaran();
        if($resultado == 1) {
            $id_albaran = $albaranPeriferico->id_albaran;
            // Guardamos el log de creación de albarán
            $albaranPeriferico->cargaDatosAlbaranId($id_albaran);
            $id_usuario = $albaranPeriferico->id_usuario;

            $user->cargaDatosUsuarioId($id_usuario);
            $usuario = $user->usuario;
            $id_tipo_usuario = $user->id_tipo;
            $res_tipo_usuario = $user->dameNombreTipoUsuario($id_tipo_usuario);
            $tipo_usuario = $res_tipo_usuario["tipo"];
            $proceso = "CREACION ALBARAN RECEPCION PERIFERICOS";
            $hubo_error = "NO";
            $error_des = "OK!";

            // Guardamos log de la operación
            $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_almacen,$id_albaran,0,"-","PERIFERICO","-","-",$hubo_error,$error_des,NULL);
            $res_log = $log->guardarLog();
            if($res_log != 1){
                $mensaje_error = $log->getErrorMessage($res_log);
            }
            header("Location: recepcion_perifericos.php?iniciarRecepcion=1&id_albaran=".$id_albaran);
        }
        else {
            // ERROR AL GUARDAR EL ALBARAN
            $mensaje_error = $albaranPeriferico->getErrorMessage($resultado);
        }
    }   
    else {
        echo '<script type="text/javascript">alert("Introduzca un nombre para el albarán")</script>';                
    } 
}

$titulo_pagina = "Almacen Periféricos > Entrada Periféricos";
$pagina = "recepcion_perifericos";
include("../includes/header.php");
echo '<script type="text/javascript" src="../js/almacen_perifericos/almacen_perifericos.js"></script>';
?>

<div class="separador"></div> 
<?php include("../includes/menu_almacen_perifericos.php"); ?>

<div id="ContenedorCentral">
    <div id="ContenedorSidebar">
        <?php include ("../includes/sidebar.php"); ?>
    </div>
    <h3>Entrada Perif&eacute;ricos</h3>
    <?php
        if($_GET["iniciarRecepcion"] != 1){
            // Convertimos la fecha en el caso que sea usuario de Brasil
            $fecha_hoy =  date('Y-m-d H:i:s');
            if($esUsuarioBrasil) $fecha_hoy = $user->fechaHoraBrasil($fecha_hoy);
            else $fecha_hoy = $user->fechaHoraSpain($fecha_hoy); ?>

            <form id="FormularioCreacionBasico" name="iniciarAlbaran" action="recepcion_perifericos.php" method="post">
                <br/>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Usuario</div>
                    <label id="usuario" class="LabelInfoOP"><?php echo $ateneaUser->usuario;?></label>
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Fecha de recepción</div>
                    <label id="fecha_actual" class="LabelInfoOP"><?php echo $fecha_hoy;?></label>
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Tipo albarán</div>
                    <label id="tipo_albaran" class="LabelInfoOP">ENTRADA</label>
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Nombre albarán * </div>
                    <input type="text" id="nombre_albaran" name="nombre_albaran" class="CreacionBasicoInput" value="<?php echo $nombre_albaran;?>" />
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Origen * </div>
                    <select id="id_centro_logistico" name="id_centro_logistico"  class="CreacionBasicoInput">
                        <?php 
                            // Listado de Centros Logisticos
                            $resultado_centros = $centroLogistico->dameCentrosLogisticos();
                            for($i=0;$i<count($resultado_centros);$i++){
                                $id_centro_logistico = $resultado_centros[$i]["id_centro_logistico"];
                                $nombre_centro = $resultado_centros[$i]["centro_logistico"];
                                echo '<option value="'.$id_centro_logistico.'">'.$nombre_centro.'</option>';
                            }                    
                        ?>        
                    </select>
                </div>
                <?php
                    // Cargamos todos los almacenes existentes segun la sede del usuario
                    if($esAdminGlobal) $res_almacenes = $almacen->dameAlmacenesMantenimiento();
                    else $res_almacenes = $sede->dameAlmacenesMantenimientoSede($id_sede_usuario);

                    if($esAdminGlobal || $esAdminAlmacen){ ?>
                        <div class="ContenedorCamposCreacionBasico">
                            <div class="LabelCreacionBasico">Almacen * </div>
                            <select id="almacenes" name="almacenes" class="CreacionBasicoInput">
                                <?php
                                    for($i=0;$i<count($res_almacenes);$i++){
                                        $id_almacen_sel = $res_almacenes[$i]["id_almacen"];
                                        $nombre = $res_almacenes[$i]["almacen"]; ?>
                                        <option value="<?php echo $id_almacen_sel;?>" <?php if($id_almacen_sel == $id_almacen_usuario) echo "selected='selected'"; ?>>
                                            <?php echo $nombre; ?>
                                        </option>
                                <?php
                                    }
                                ?>
                            </select>
                        </div>
                <?php
                    }
                    else { ?>                
                        <div class="ContenedorCamposCreacionBasico">
                            <div class="LabelCreacionBasico">Almacen</div>
                            <label id="almacenes" class="LabelInfoOP">
                                <?php 
                                    // Obtenemos el almacen del usuario
                                    $almacen->cargaDatosAlmacenId($_SESSION["AT_id_almacen"]);
                                    $nombre = $almacen->nombre;
                                    echo $nombre;
                                ?>
                            </label>
                        </div>
                <?php 
                    }
                ?>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Motivo * </div>
                    <select id="motivo" name="motivo"  class="CreacionBasicoInput">
                        <?php
                        $motivos_entrada = array("AJUSTE DESVIACION","COMPRA / SUMINISTRO","SERVICIO REPARACION");
                        for($i=0;$i<count($motivos_entrada);$i++){
                            echo '<option value="'.$motivos_entrada[$i].'">'.$motivos_entrada[$i].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <br/>
                <br/>
                <input type="submit" class="BotonEliminar" style="margin: 5px 5px 5px 12px;" value="CONTINUAR" />
                <input type="hidden" id="iniciarAlbaran" name="iniciarAlbaran" value="1">
                <br/>
                <br/>
                <?php
                    if($mensaje_error != "") {
                        echo '<div class="mensaje_error">'.$mensaje_error.'</div>';
                    }
                ?>
                <br />
            </form>
    <?php
        }
        else { ?>
            <script>
                // Función para avisar al usuario cuando intente abandonar la página sin cerrar el albarán
                window.onbeforeunload = function() {
                    return("Si abandona la página sin cerrar el albaran, quedarán registradas igualmente todas las operaciones realizadas sobre el mismo. ¿Esta seguro de salir?");
                }
            </script>
        <?php
            // Cargamos los datos del albaran
            $id_albaran = $_GET["id_albaran"];
            $albaranPeriferico->cargaDatosAlbaranId($id_albaran);

            $nombre_albaran = $albaranPeriferico->nombre_albaran;
            $id_centro_logistico = $albaranPeriferico->id_centro_logistico;
            $motivo = $albaranPeriferico->motivo;
            $id_almacen = $albaranPeriferico->id_almacen;
            $fecha_creado = $albaranPeriferico->fecha_creado;

            if($esUsuarioBrasil) $fecha_creado = $user->fechaHoraBrasil($fecha_creado);
            else $fecha_creado = $user->fechaHoraSpain($fecha_creado);

            $almacen->cargaDatosAlmacenId($id_almacen);
            $nombre_almacen = $almacen->nombre;
         
            // CENTRO LOGISTICO
            $centroLogistico->cargaDatosCentroLogisticoId($id_centro_logistico);
            $nombre_centro = $centroLogistico->nombre; ?>
            <form id="FormularioCreacionBasico" name="finalizarAlbaran" action="recepcion_perifericos.php" method="post">
                <input type="hidden" id="metodo" name="metodo" value="RECEPCIONAR" />
                <input type="hidden" id="id_almacen" name="id_almacen" value="<?php echo $id_almacen;?>" /> 
                <br/>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Usuario</div>
                    <label id="usuario" class="LabelInfoOP" style="width:750px;"><?php echo $ateneaUser->usuario;?></label>
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Fecha de recepción</div>
                    <label id="fecha_actual" class="LabelInfoOP" style="width:750px;"><?php echo $fecha_creado;?></label>
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Tipo albarán</div>
                    <label id="tipo_albaran" class="LabelInfoOP" style="width:750px;">ENTRADA</label>
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Nombre albarán</div>
                    <label id="nombre_albaran" class="LabelInfoOP" style="width:750px;"><?php echo $nombre_albaran;?></label>
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Origen</div>
                    <label id="nombre_participante" class="LabelInfoOP" style="width:750px;"><?php echo $nombre_centro;?></label>
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Motivo</div>
                    <label id="motivo" class="LabelInfoOP" style="width:750px;"><?php echo $motivo;?></label>
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Almacen</div>
                    <label id="almacenes" class="LabelInfoOP" style="width:750px;"><?php echo $nombre_almacen;?></label>
                </div>
                <br/>
                <br/>

                <div id="cargaPeriferico">
                    <div class="ContenedorCamposCreacionBasico">
                        <div class="LabelCreacionBasico">NUM. SERIE *</div>
                        <input type="text" id="num_serie" name="num_serie" class="CreacionBasicoInput" value="<?php echo $num_serie;?>" />
                        <input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="CARGAR" onclick="cargaPeriferico()" />
                    </div>
                    <div class="ContenedorCamposCreacionBasico">
                        <div id="error_codigo" style="height: 30px;"></div>
                    </div>
                    <br/>
                    <div id="capa_periferico_buscador" class="ContenedorCamposCreacionBasico">
                        <table id="tabla_buscador" style="width: 1100px; min-width: 480px;">
                            <tr style="height: 30px;">
                                <th style="width:20%;">NUM. SERIE</th>
                                <th style="width:20%;">TIPO</th>
                                <th style="width:20%; text-align: center;">AVERIADO</th>
                                <th style="width:20%; text-align: center;"></th>
                                <th style="width:20%; text-align: center;"></th>
                            </tr>
                            <tr style="height: 35px;">
                                <td style="width:20%;"></td>
                                <td style="width:20%;"></td>
                                <td style="width:20%; text-align: center;"></td>
                                <td style="width:20%; text-align: center;"></td>
                                <td style="width:20%; text-align: center;"></td>
                            </tr>
                        </table>
                        <div id="datos_periferico"></div>
                    </div>
                </div>
                <br/>
                <br/>

                <div id="capa_periferico_log" class="ContenedorCamposCreacionBasico">
                    <div class="CajaReferencias" style="margin: 0px;">
                        <div id="CapaTablaIframe" style="overflow-x: hidden;">
                            <div id="datos_log" style="overflow-x: hidden;"></div>
                            <table id="tabla_log" style="width: 1100px; min-width: 480px; overflow-y:auto; ">
                                <tr style="height: 30px;">
                                    <th style="width:20%;">NUM. SERIE</th>
                                    <th style="width:20%;">TIPO</th>
                                    <th style="width:20%; text-align: center;">AVERIADO</th>
                                    <th style="width:20%; text-align: center;">LOG</th>
                                    <th style="width:20%; text-align: center;"></th>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <br/>
                <br/>
                <br/>
                <input type="button" class="BotonEliminar" style="margin: 5px 5px 5px 12px;" value="CERRAR ALBARAN" onclick="javascript: cerrarAlbaran(<?php echo $id_albaran; ?>)" />
                <input type="hidden" id="id_albaran_global" value="<?php echo $id_albaran; ?>">
                <br/>
                <br/>
                <div id="mensaje_error"></div>
            </form>
    <?php
        }
    ?>
</div>    
<?php include ("../includes/footer.php"); ?>