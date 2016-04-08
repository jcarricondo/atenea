<?php
// PROCESO DE DESRECEPCION DE MATERIAL
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/sede/sede.class.php");
include("../classes/basicos/proveedor.class.php");
include("../classes/basicos/centro_logistico.class.php");
include("../classes/basicos/usuario.class.php");
include("../classes/almacen/albaran.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/control_usuario.class.php");
include("../classes/log/log_almacen.class.php");
permiso(23);

$sede = new Sede();
$proveedor = new Proveedor();
$centroLogistico = new CentroLogistico();
$albaran = new Albaran();
$almacen = new Almacen();
$user = new Usuario();
$control_usuario = new Control_Usuario();
$log = new Log_Almacen();

$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$id_almacen_usuario = $_SESSION["AT_id_almacen"];
$id_sede_usuario = $control_usuario->dameSedeSegunUsuario($id_tipo_usuario,$id_almacen_usuario);
$esAdminGlobal = $control_usuario->esAdministradorGlobal($id_tipo_usuario);
$esAdminFab = $control_usuario->esAdministradorFab($id_tipo_usuario);
$esAdminMan = $control_usuario->esAdministradorMan($id_tipo_usuario);
$esUsuarioGes = $control_usuario->esUsuarioGes($id_tipo_usuario);
$esUsuarioBrasil = $control_usuario->esUsuarioBrasil($id_tipo_usuario,$id_sede_usuario);
$eligeCualquierAlmacen = $esAdminGlobal || $esUsuarioGes;
$eligeAlmacenesSede = $esAdminFab || $esAdminMan;

if(isset($_POST["iniciarAlbaran"]) and $_POST["iniciarAlbaran"] == 1){
    // Comprobamos que se rellenaron todos los datos
    $nombre_albaran = $_POST["nombre_albaran"];
    if($nombre_albaran != ""){
        // Guarda el registro de albaran y prepara las tablas para la desrecepción
        $tipo_albaran = "SALIDA";
        $motivo = $_POST["motivo"];
        $id_usuario = $_SESSION["AT_id_usuario"];
        $nombre_participante = $_POST["nombre_participante"];
        $id_tipo_participante = $albaran->dameTipoParticipante($nombre_participante);
        $tipo_correcto = $id_tipo_participante == 1 || $id_tipo_participante == 2;

        // Comprobamos que el id_tipo_participante es un PROVEEDOR o un CENTRO LOGISTICO
        if($tipo_correcto){
            if($id_tipo_participante == 1){
                $albaran->dameIdParticipante($id_tipo_participante,$nombre_participante);
                $id_participante = $albaran->id_participante["id_proveedor"];
            }
            else if($id_tipo_participante == 2){
                $albaran->dameIdParticipante($id_tipo_participante,$nombre_participante);
                $id_participante = $albaran->id_participante["id_centro_logistico"];
            }

            if($eligeCualquierAlmacen || $eligeAlmacenesSede){
                $id_almacen = $_POST["almacenes"];
            }
            else {
                $id_almacen = $id_almacen_usuario;
            }

            $albaran->datosNuevoAlbaran($id_albaran,$nombre_albaran,$tipo_albaran,$id_participante,$id_tipo_participante,$motivo,$id_usuario,$id_almacen,$fecha,$activo);
            $resultado = $albaran->guardarAlbaran();
            if($resultado == 1) {
                $id_albaran = $albaran->id_albaran;

                // Guardamos el log de creación de albarán
                $albaran->cargaDatosAlbaranId($id_albaran);
                $id_usuario = $albaran->id_usuario;

                $user->cargaDatosUsuarioId($id_usuario);
                $usuario = $user->usuario;
                $id_tipo_usuario = $user->id_tipo;
                $res_tipo_usuario = $user->dameNombreTipoUsuario($id_tipo_usuario);
                $tipo_usuario = $res_tipo_usuario["tipo"];
                $proceso = "CREACION ALBARAN DESRECEPCION PIEZAS";
                $hubo_error = "NO";
                $error_des = "OK!";

                // Guardamos log de la operación
                $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_almacen,$id_albaran,0,"-","REFERENCIA","-","-",$hubo_error,$error_des,NULL);
                $res_log = $log->guardarLog();
                if($res_log != 1){
                    $mensaje_error = $log->getErrorMessage($res_log);
                }
                header("Location: desrecepcion_material.php?iniciarDesrecepcion=1&id_albaran=".$id_albaran);
            }
            else {
                // ERROR AL GUARDAR EL ALBARAN
                $mensaje_error = $albaran->getErrorMessage($resultado);
            }
        }
        else {
            // ERROR EL ORIGEN NO ES CORRECTO
            $mensaje_error = $albaran->getErrorMessage($resultado);        
        }        
    }   
    else {
        echo '<script type="text/javascript">alert("Introduzca un nombre para el albarán")</script>';                
    }
}

$titulo_pagina = "Almacen > Salida Material";
$pagina = "desrecepcion_material";
include("../includes/header.php");
echo '<script type="text/javascript" src="../js/almacen/almacen.js"></script>';
?>

<div class="separador"></div> 
<?php include("../includes/menu_almacen_piezas.php"); ?>

<div id="ContenedorCentral">
    <div id="ContenedorSidebar">
        <?php include ("../includes/sidebar.php"); ?>
    </div>
    <h3>Salida Material</h3>

    <?php 
        if($_GET["iniciarDesrecepcion"] != 1){
            // Si es usuario de Brasil mostramos la fecha de Brasil
            $fecha_hoy = date('Y-m-d H:i:s');
            if($esUsuarioBrasil) $fecha_hoy = $user->fechaHoraBrasil($fecha_hoy);
            else $fecha_hoy = $user->fechaHoraSpain($fecha_hoy); ?>

            <form id="FormularioCreacionBasico" name="iniciarAlbaran" action="desrecepcion_material.php" method="post">
                <br/>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Usuario</div>
                    <label id="usuario" class="LabelInfoOP"><?php echo $ateneaUser->usuario;?></label>
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Fecha de desrecepción</div>
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
                    <div class="LabelCreacionBasico">Destino * </div>
                    <select id="nombre_participante" name="nombre_participante" class="CreacionBasicoInput">
                        <?php 
                            // Listado de Proveedores
                            $resultado_proveedores = $proveedor->dameProveedores();
                            for($i=0;$i<count($resultado_proveedores);$i++){
                                $nombre_proveedor = $resultado_proveedores[$i]["nombre_prov"];
                                echo '<option value="'.$nombre_proveedor.'">'.$nombre_proveedor.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(PROVEEDOR)</option>';
                            }    

                            $resultado_centros = $centroLogistico->dameCentrosLogisticos();
                            for($i=0;$i<count($resultado_centros);$i++){
                                $nombre_centro = $resultado_centros[$i]["centro_logistico"];
                                echo '<option value="'.$nombre_centro.'">'.$nombre_centro.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(CENTRO LOGISTICO)</option>';
                            }                    
                        ?>        
                    </select>
                </div>
                <?php
                    $res_almacenes = $almacen->dameAlmacenes();
                    if($eligeCualquierAlmacen) { ?>
                        <div class="ContenedorCamposCreacionBasico">
                            <div class="LabelCreacionBasico">Almacen *</div>
                            <select id="almacenes" name="almacenes" class="CreacionBasicoInput" onchange="cargaMotivos(this.value,'SALIDA')">
                            <?php
                                for($i=0;$i<count($res_almacenes);$i++) {
                                    $id_almacen = $res_almacenes[$i]["id_almacen"];
                                    $nombre = $res_almacenes[$i]["almacen"];
                                    echo '<option value="' . $id_almacen . '">' . $nombre . '</option>';
                                }
                            ?>
                            </select>
                        </div>
                <?php
                    }
                    else if($eligeAlmacenesSede) { ?>
                        <div class="ContenedorCamposCreacionBasico">
                            <div class="LabelCreacionBasico">Almacen * </div>
                            <select id="almacenes" name="almacenes" class="CreacionBasicoInput" onchange="cargaMotivos(this.value,'SALIDA')">
                            <?php
                                // Mostramos sólo los almacenes de su sede
                                $res_sede = $almacen->dameSedeAlmacen($id_almacen_usuario);
                                $id_sede = $res_sede["id_sede"];
                                $res_almacenes = $sede->dameAlmacenesSede($id_sede);
                                for($i=0;$i<count($res_almacenes);$i++) {
                                    $id_almacen = $res_almacenes[$i]["id_almacen"];
                                    $almacen->cargaDatosAlmacenId($id_almacen);
                                    $nombre_almacen = $almacen->nombre; ?>
                                    <option value="<?php echo $id_almacen; ?>" <?php if($id_almacen_usuario == $id_almacen) echo 'selected="selected"'; ?>>
                                        <?php echo $nombre_almacen; ?>
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
                    // Si puede operar sobre cualquier almacen mostramos los motivos predeterminados del almacen SMK
                    if($eligeCualquierAlmacen) $id_almacen_usuario = 1;
                    // Cargamos los motivos del albarán de salida
                    $res_motivos = $almacen->dameMotivosAlbaranSalida($id_almacen_usuario);
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
        else {
            ?>
            <script>
                // Función para avisar al usuario cuando intente abandonar la página sin cerrar el albarán
                window.onbeforeunload = function() {
                    return("Si abandona la página sin cerrar el albaran, quedarán registradas igualmente todas las operaciones realizadas sobre el mismo. ¿Esta seguro de salir?");
                }
            </script>
        <?php
            // Cargamos los datos del albarán
            $id_albaran = $_GET["id_albaran"];
            $albaran->cargaDatosAlbaranId($id_albaran);
            $nombre_albaran = $albaran->nombre_albaran;
            $motivo = $albaran->motivo;
            $id_tipo_participante = $albaran->id_tipo_participante;
            $id_participante = $albaran->id_participante;
            $fecha_creado = $albaran->fecha_creado;
            $id_almacen = $albaran->id_almacen;
            $almacen->cargaDatosAlmacenId($id_almacen);
            $nombre_almacen = utf8_decode($almacen->nombre);

            if($esUsuarioBrasil) $fecha_creado = $user->fechaHoraBrasil($fecha_creado);
            else $fecha_creado = $user->fechaHoraSpain($fecha_creado);

            if(empty($motivo)) $motivo = "-";

            if($id_tipo_participante == 1){
                // PROVEEDOR
                $proveedor->cargaDatosProveedorId($id_participante);
                $nombre_participante = $proveedor->nombre;
                // Guardamos el nombre del participante con la palabra "PROVEEDOR"
                $nombre_participante_descripcion = $nombre_participante." (PROVEEDOR)";
            }
            else{
                // CENTRO LOGISTICO
                $centroLogistico->cargaDatosCentroLogisticoId($id_participante);
                $nombre_participante = $centroLogistico->nombre;
                // Guardamos el nombre del participante con la palabra "CENTRO LOGISTICO"
                $nombre_participante_descripcion = $nombre_participante." (CENTRO LOGISTICO)";
            } ?>

            <form id="FormularioCreacionBasico" name="iniciarDesrecepcion" action="desrecepcion_material.php" method="post">
                <input type="hidden" id="metodo" name="metodo" value="DESRECEPCIONAR" />
                <input type="hidden" id="id_almacen" name="id_almacen" value="<?php echo $id_almacen;?>" />
                <br/>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Usuario</div>
                    <label id="usuario" class="LabelInfoOP" style="width:750px;"><?php echo $ateneaUser->usuario;?></label>
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Fecha de desrecepción</div>
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
                    <div class="LabelCreacionBasico">Destino</div>
                    <label id="nombre_participante" class="LabelInfoOP" style="width:750px;"><?php echo $nombre_participante_descripcion;?></label>
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Almacen</div>
                    <label id="almacenes" class="LabelInfoOP" style="width:750px;"><?php echo utf8_encode($nombre_almacen);?></label>
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Motivo</div>
                    <label id="motivo" class="LabelInfoOP" style="width:750px;"><?php echo $motivo;?></label>
                </div>
                <br/>
                <br/>

                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">ID REF *</div>
                    <input type="text" id="id_referencia" name="id_referencia" class="CreacionBasicoInput" value="<?php echo $id_referencia;?>" onkeypress="return soloNumeros(event)" onkeyup="cargaReferenciaIntro(event);" />
                    <input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="CARGAR" onclick="cargaReferencia()" />
                    <input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 10px;" value="BUSCADOR" onclick="javascript:Abrir_ventana('buscador_referencias_almacen.php')" />
                </div>
                <br/>

                <div id="capa_ref_buscador" class="ContenedorCamposCreacionBasico">
                    <table id="tabla_buscador" style="width: 1100px; min-width: 480px;">
                        <tr style="min-height: 30px;">
                            <th style="width:5%; text-align: center">ID REF</th>
                            <th style="width:25%;">NOMBRE</th>
                            <th style="width:10%;">PROVEEDOR</th>
                            <th style="width:10%;">REF. PROVEEDOR</th>
                            <th style="width:10%;">NOMBRE PIEZA</th>
                            <th style="width:10%; text-align: center;">PACK PRECIO</th>
                            <th style="width:5%; text-align: center;">UDS/P</th>
                            <th style="width:10%; text-align: center;">CANTIDAD</th>
                            <th style="width:5%; text-align: center;"></th>
                            <th style="width:10%; text-align: center;"></th>
                        </tr>
                        <tr style="height: 35px;">
                            <td style="width:5%; text-align: center"></td>
                            <td style="width:25%;"></td>
                            <td style="width:10%;"></td>
                            <td style="width:10%;"></td>
                            <td style="width:10%;"></td>
                            <td style="width:10%; text-align: center"></td>
                            <td style="width:5%; text-align: center"></td>
                            <td style="width:10%; text-align: center"></td>
                            <td style="width:5%; text-align: center;"></td>
                            <td style="width:10%; text-align: center;"></td>
                        </tr>
                    </table>
                    <div id="datos_referencia"></div>
                </div>
                <br/>
                <br/>

                <div id="capa_ref_log" class="ContenedorCamposCreacionBasico">
                    <div class="CajaReferencias" style="margin: 0px;">
                        <div id="CapaTablaIframe" style="overflow-x: hidden;">
                            <div id="datos_log" style="overflow-x: hidden;"></div>
                            <table id="tabla_log" style="width: 1100px; min-width: 480px; overflow-y:auto;">
                                <tr>
                                    <th style="width:5%; text-align: center">ID REF</th>
                                    <th style="width:25%;">NOMBRE</th>
                                    <th style="width:10%;">PROVEEDOR</th>
                                    <th style="width:10%;">REF. PROVEEDOR</th>
                                    <th style="width:10%;">NOMBRE PIEZA</th>
                                    <th style="width:10%; text-align: center;">PACK PRECIO</th>
                                    <th style="width:5%; text-align: center;">UDS/P</th>
                                    <th style="width:10%; text-align: center;">CANTIDAD</th>
                                    <th style="width:5%; text-align: center;">LOG</th>
                                    <th style="width:10%; text-align: center;"></th>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <br/>
                <br/>
                <br/>
                <input type="button" class="BotonEliminar" style="margin: 5px 5px 5px 12px;" value="CERRAR ALBARAN" onclick="javascript: cerrarAlbaran(<?php echo $id_albaran; ?>)" />
                <input type="hidden" id="id_albaran_global_des" value="<?php echo $id_albaran; ?>">
                <input type="hidden" id="id_usuario_session" value="<?php echo $_SESSION["AT_id_usuario"]; ?>">
                <br/>
                <br/>
            </form>
    <?php
        }
    ?>
</div>    
<?php include ("../includes/footer.php"); ?>