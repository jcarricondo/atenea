<?php
// PROCESO DE DESRECEPCION DE SIMULADORES
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/basicos/centro_logistico.class.php");
include("../classes/basicos/usuario.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/almacen_simuladores/albaran_simulador.class.php");
include("../classes/control_usuario.class.php");
include("../classes/sede/sede.class.php");
include("../classes/log/log_almacen.class.php");
permiso(41);

$centroLogistico = new CentroLogistico();
$user = new Usuario();
$albaranSimulador = new AlbaranSimulador();
$almacen = new Almacen();
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
        // Guarda el registro de albarán y prepara las tablas para la desrecepción
        $tipo_albaran = "SALIDA";
        $id_centro_logistico = $_POST["id_centro_logistico"];
        $motivo = $_POST["motivo"];
        $id_usuario = $_SESSION["AT_id_usuario"];

        if($esAdminGlobal || $esAdminAlmacen){
            // ADMIN GLOBAL / BRASIL
            $id_almacen = $_POST["almacenes"];
        }
        else {
            $id_almacen = $_SESSION["AT_id_almacen"];
        }

        $albaranSimulador->datosNuevoAlbaran($id_albaran,$nombre_albaran,$tipo_albaran,$id_centro_logistico,$motivo,$id_usuario,$id_almacen,$fecha,$activo);
        $resultado = $albaranSimulador->guardarAlbaran();
        if($resultado == 1) {
            $id_albaran = $albaranSimulador->id_albaran;
            // Guardamos el log de creación de albarán
            $albaranSimulador->cargaDatosAlbaranId($id_albaran);
            $id_usuario = $albaranSimulador->id_usuario;

            $user->cargaDatosUsuarioId($id_usuario);
            $usuario = $user->usuario;
            $id_tipo_usuario = $user->id_tipo;
            $res_tipo_usuario = $user->dameNombreTipoUsuario($id_tipo_usuario);
            $tipo_usuario = $res_tipo_usuario["tipo"];
            $proceso = "CREACION ALBARAN DESRECEPCION SIMULADORES";
            $hubo_error = "NO";
            $error_des = "OK!";

            // Guardamos log de la operación
            $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_almacen,$id_albaran,0,"-","SIMULADOR","-","-",$hubo_error,$error_des,NULL);
            $res_log = $log->guardarLog();
            if($res_log != 1){
                $mensaje_error = $log->getErrorMessage($res_log);
            }
            header("Location: desrecepcion_simuladores.php?iniciarDesRecepcion=1&id_albaran=".$id_albaran);
        }
        else {
            // ERROR AL GUARDAR EL ALBARAN
            $mensaje_error = $albaranSimulador->getErrorMessage($resultado);
        }
    }   
    else {
        echo '<script type="text/javascript">alert("Introduzca un nombre para el albarán")</script>';                
    } 
}

$titulo_pagina = "Almacen Simuladores > Salida Simuladores";
$pagina = "desrecepcion_simuladores";
include("../includes/header.php");
echo '<script type="text/javascript" src="../js/almacen_simuladores/almacen_simuladores.js"></script>';
?>

<div class="separador"></div> 
<?php include("../includes/menu_almacen_simuladores.php"); ?>

<div id="ContenedorCentral">
    <div id="ContenedorSidebar">
        <?php include ("../includes/sidebar.php"); ?>
    </div>
    <h3>Salida Simuladores</h3>

    <?php 
        if($_GET["iniciarDesRecepcion"] != 1){
            // Convertimos la fecha en el caso que sea usuario de Brasil
            $fecha_hoy =  date('Y-m-d H:i:s');
            if($esUsuarioBrasil) $fecha_hoy = $user->fechaHoraBrasil($fecha_hoy);
            else $fecha_hoy = $user->fechaHoraSpain($fecha_hoy); ?>

            <form id="FormularioCreacionBasico" name="iniciarAlbaran" action="desrecepcion_simuladores.php" method="post">
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
                    <label id="tipo_albaran" class="LabelInfoOP">SALIDA</label>
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Nombre albarán * </div>
                    <input type="text" id="nombre_albaran" name="nombre_albaran" class="CreacionBasicoInput" value="<?php echo $nombre_albaran;?>" />
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Origen * </div>
                    <select id="id_centro_logistico" name="id_centro_logistico"  class="CreacionBasicoInput">
                        <?php 
                            // Listado de Centros Logísticos
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
                    if($esAdminGlobal) {
                        $res_almacenes = $almacen->dameAlmacenesMantenimiento();
                        $id_almacen_usuario = $almacen->damePrimerAlmacenMantenimiento();
                    }
                    else $res_almacenes = $sede->dameAlmacenesMantenimientoSede($id_sede_usuario);

                    if($esAdminGlobal || $esAdminAlmacen){ ?>
                        <div class="ContenedorCamposCreacionBasico">
                            <div class="LabelCreacionBasico">Almacen * </div>
                            <select id="almacenes" name="almacenes" class="CreacionBasicoInput" onchange="cargaMotivos(this.value,'SALIDA')">
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
                    // Cargamos los motivos del albarán de salida
                    $res_motivos = $albaranSimulador->dameMotivosAlbaranSalidaSimuladores($id_almacen_usuario);
                ?>
                <div id="capa_motivo" class="ContenedorCamposCreacionBasico">
                <?php
                    if(!empty($res_motivos)) { ?>
                        <div class="LabelCreacionBasico">Motivo * </div>
                        <select id="motivo" name="motivo" class="CreacionBasicoInput">
                        <?php
                            for($i=0;$i<count($res_motivos);$i++) {
                                $nombre_motivo = $res_motivos[$i]["motivo"];?>
                                <option value="<?php echo $nombre_motivo; ?>"><?php echo $nombre_motivo ?></option>
                        <?php
                            }
                        ?>
                        </select>
                        <?php
                    }
                    else { ?>
                        <input type="hidden" id="motivo" name="motivo" value=""/>
                <?php
                    }
                ?>
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
            // Cargamos los datos del albarán
            $id_albaran = $_GET["id_albaran"];
            $albaranSimulador->cargaDatosAlbaranId($id_albaran);
            $nombre_albaran = $albaranSimulador->nombre_albaran;
            $id_centro_logistico = $albaranSimulador->id_centro_logistico;
            $motivo = $albaranSimulador->motivo;
            $id_almacen = $albaranSimulador->id_almacen;
            $fecha_creado = $albaranSimulador->fecha_creado;

            if($esUsuarioBrasil) $fecha_creado = $user->fechaHoraBrasil($fecha_creado);
            else $fecha_creado = $user->fechaHoraSpain($fecha_creado);

            $almacen->cargaDatosAlmacenId($id_almacen);
            $nombre_almacen = $almacen->nombre;
         
            // CENTRO LOGISTICO
            $centroLogistico->cargaDatosCentroLogisticoId($id_centro_logistico);
            $nombre_centro = $centroLogistico->nombre; ?>

            <form id="FormularioCreacionBasico" name="finalizarAlbaran" action="desrecepcion_simuladores.php" method="post">
                <input type="hidden" id="metodo" name="metodo" value="DESRECEPCIONAR" />
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
                    <label id="tipo_albaran" class="LabelInfoOP" style="width:750px;">SALIDA</label>
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

                <div id="cargaSimulador">
                    <div class="ContenedorCamposCreacionBasico">
                        <div class="LabelCreacionBasico">NUM. SERIE *</div>
                        <input type="text" id="num_serie" name="num_serie" class="CreacionBasicoInput" value="<?php echo $num_serie;?>" />
                        <input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="CARGAR" onclick="cargaSimulador()" />
                    </div>
                    <div class="ContenedorCamposCreacionBasico">
                        <div id="error_codigo" style="height: 30px;"></div>
                    </div>
                    <br/>
                    <div id="capa_simulador_buscador" class="ContenedorCamposCreacionBasico">
                        <table id="tabla_buscador" style="width: 1100px; min-width: 480px;">
                            <tr style="height: 30px;">
                                <th style="width:25%;">NUM. SERIE</th>
                                <th style="width:25%; text-align: center;">AVERIADO</th>
                                <th style="width:25%; text-align: center;"></th>
                                <th style="width:25%; text-align: center;"></th>
                            </tr>
                            <tr style="height: 35px;">
                                <td style="width:25%;"></td>
                                <td style="width:25%; text-align: center;"></td>
                                <td style="width:25%; text-align: center;"></td>
                                <td style="width:25%; text-align: center;"></td>
                            </tr>
                        </table>
                        <div id="datos_simulador"></div>
                    </div>
                </div>
                <br/>
                <br/>

                <div id="capa_simulador_log" class="ContenedorCamposCreacionBasico">
                    <div class="CajaReferencias" style="margin: 0px;">
                        <div id="CapaTablaIframe" style="overflow-x: hidden;">
                            <div id="datos_log" style="overflow-x: hidden;"></div>
                            <table id="tabla_log" style="width: 1100px; min-width: 480px; overflow-y:auto; ">
                                <tr style="height: 30px;">
                                    <th style="width:25%;">NUM. SERIE</th>
                                    <th style="width:25%; text-align: center;">AVERIADO</th>
                                    <th style="width:25%; text-align: center;">LOG</th>
                                    <th style="width:25%; text-align: center;"></th>
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