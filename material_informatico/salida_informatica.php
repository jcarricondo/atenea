<?php
// PROCESO DE DESRECEPCIÓN DE MATERIAL INFORMÁTICO
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/sede/sede.class.php");
include("../classes/basicos/usuario.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/material_informatico/material_informatico.class.php");
include("../classes/material_informatico/albaran_informatico.class.php");
permiso(39);

$user = new Usuario();
$sede = new Sede();
$almacen = new Almacen();
$materialInformatico = new MaterialInformatico();
$albaranInformatico = new AlbaranInformatico();

// Obtenemos el nombre del almacen 
$id_almacen = 22;
$almacen->cargaDatosAlmacenId($id_almacen);
$nombre_almacen = $almacen->nombre;

if(isset($_POST["iniciarAlbaran"]) and $_POST["iniciarAlbaran"] == 1){
    $nombre_albaran = $_POST["nombre_albaran"];
    if($nombre_albaran != ""){
        // Guarda el registro de albarán y prepara las tablas para la desrecepción
        $tipo_albaran = "SALIDA";
        $motivo = $_POST["motivo"];
        $id_usuario = $_SESSION["AT_id_usuario"];
        $destino = $_POST["destino"];
        $observaciones = $_POST["observaciones"];

        if(empty($destino)) $destino = "-";

        $albaranInformatico->datosNuevoAlbaran($id_albaran,$nombre_albaran,$tipo_albaran,$motivo,$id_usuario,$id_almacen,$destino,$observaciones,$fecha,$activo);
        $resultado = $albaranInformatico->guardarAlbaran();
        if($resultado == 1){
            $id_albaran = $albaranInformatico->id_albaran;
            header("Location: salida_informatica.php?iniciarDesRecepcion=1&id_albaran=".$id_albaran);
        }
        else {
            // ERROR AL GUARDAR EL ALBARÁN
            $mensaje_error = $albaranInformatico->getErrorMessage($resultado);
        }
    }   
    else {
        echo '<script type="text/javascript">alert("Introduzca un nombre para el albarán")</script>';                
    }
}

$titulo_pagina = "Material Informática > Salida Informática";
$pagina = "salida_informatica";
include("../includes/header.php");
echo '<script type="text/javascript" src="../js/material_informatico/material_informatico.js"></script>';
?>

<div class="separador"></div> 
<?php include("../includes/menu_material_informatico.php"); ?>

<div id="ContenedorCentral">
    <div id="ContenedorSidebar">
        <?php include ("../includes/sidebar.php"); ?>
    </div>
    <h3>Salida Inform&aacute;tica</h3>

    <?php 
        if($_GET["iniciarDesRecepcion"] != 1){    
            $fecha_hoy =  date('Y-m-d H:i:s');
            $fecha_hoy_spain = $user->fechaHoraSpain($fecha_hoy); ?>       
            <form id="FormularioCreacionBasico" name="iniciarAlbaran" action="salida_informatica.php" method="post">
                <br/>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Usuario</div>
                    <label id="usuario" class="LabelInfoOP"><?php echo $ateneaUser->usuario;?></label>
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Almacen</div>
                    <label id="nombre_almacen" class="LabelInfoOP"><?php echo $nombre_almacen;?></label>
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Fecha de recepción</div>
                    <label id="fecha_actual" class="LabelInfoOP"><?php echo $fecha_hoy_spain;?></label>
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Tipo albarán</div>
                    <label id="tipo_albaran" class="LabelInfoOP">SALIDA</label>
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Nombre albarán *</div>
                    <input type="text" id="nombre_albaran" name="nombre_albaran" class="CreacionBasicoInput" value="<?php echo $nombre_albaran;?>" />
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Motivo * </div>
                    <select id="motivo" name="motivo"  class="CreacionBasicoInput">
                        <?php
                            $motivos_salida = array("SERVICIO REPARACION","MATERIAL ASIGNADO");
                            for($i=0;$i<count($motivos_salida);$i++){
                                echo '<option value="'.$motivos_salida[$i].'">'.$motivos_salida[$i].'</option>';
                            }    
                        ?>
                    </select>
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Destino</div>
                    <input type="text" id="destino" name="destino" class="CreacionBasicoInput" value="<?php echo $destino;?>" />
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Observaciones</div>
                    <textarea type="text" id="observaciones" name="observaciones" rows="10" class="textareaInput" style="resize: none;"><?php echo $observaciones;?></textarea>
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
            // Cargamos los datos del albarán
            $id_albaran = $_GET["id_albaran"];
            $albaranInformatico->cargaDatosAlbaranId($id_albaran);

            $nombre_albaran = $albaranInformatico->nombre_albaran;
            $motivo = $albaranInformatico->motivo;
            $id_almacen = $albaranInformatico->id_almacen;
            $destino = $albaranInformatico->origen_destino;
            $observaciones = $albaranInformatico->observaciones;
            $fecha_creado = $albaranInformatico->fecha_creado;

            // Convertimos la fecha para mostrar tambien la hora
            $fecha_creado = $user->fechaHoraSpain($fecha_creado);

            $almacen->cargaDatosAlmacenId($id_almacen);
            $nombre_almacen = $almacen->nombre;
    ?>
            <form id="FormularioCreacionBasico" name="finalizarAlbaran" action="salida_informatica.php" method="post">
                <input type="hidden" id="metodo" name="metodo" value="DESRECEPCIONAR" />
                <input type="hidden" id="id_almacen" name="id_almacen" value="<?php echo $id_almacen;?>" /> 
                <br/>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Usuario</div>
                    <label id="usuario" class="LabelInfoOP" style="width:750px;"><?php echo $ateneaUser->usuario;?></label>
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Almacen</div>
                    <label id="nombre_almacen" class="LabelInfoOP"><?php echo $nombre_almacen;?></label>
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
                    <div class="LabelCreacionBasico">Motivo</div>
                    <label id="motivo" class="LabelInfoOP" style="width:750px;"><?php echo $motivo;?></label>
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Almacen</div>
                    <label id="almacenes" class="LabelInfoOP" style="width:750px;"><?php echo $nombre_almacen;?></label>
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Destino</div>
                    <label id="destino" class="LabelInfoOP" style="width:750px;"><?php echo $destino;?></label>
                </div>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Observaciones</div>
                    <textarea type="text" id="observaciones" name="observaciones" rows="10" class="textareaInput" style="resize: none;" readonly="readonly"><?php echo $observaciones;?></textarea>
                </div>
                <br/>

                <div id="cargaMaterial">
                    <div class="ContenedorCamposCreacionBasico">
                        <div class="LabelCreacionBasico">NUM. SERIE *</div>
                        <input type="text" id="num_serie" name="num_serie" class="CreacionBasicoInput" value="<?php echo $num_serie;?>" />
                        <input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="CARGAR" onclick="cargaMaterial()" />
                    </div>
                    <div class="ContenedorCamposCreacionBasico">
                        <div id="error_codigo" style="height: 30px;"></div>
                    </div>
                    <div id="capa_material_buscador" class="ContenedorCamposCreacionBasico">
                        <table id="tabla_buscador" style="width: 1100px; min-width: 480px;">
                            <tr style="height: 30px;">
                                <th style="width:10%;">NUM. SERIE</th>
                                <th style="width:20%;">TIPO</th>
                                <th style="width:20%;">ESTADO</th>
                                <th style="width:10%; text-align: center;">AVERIADO</th>
                                <th style="width:20%; text-align: center;"></th>
                                <th style="width:20%; text-align: center;"></th>
                            </tr>
                            <tr style="height: 35px;">
                                <td style="width:10%;"></td>
                                <td style="width:20%;"></td>
                                <td style="width:20%;"></td>    
                                <td style="width:10%; text-align: center;"></td>
                                <td style="width:20%; text-align: center;"></td>
                                <td style="width:20%; text-align: center;"></td>
                            </tr>
                        </table>
                        <div id="datos_material"></div>
                    </div>
                </div>
                <br/>

                <div id="capa_material_log" class="ContenedorCamposCreacionBasico">
                    <div class="CajaReferencias" style="margin: 0px;">
                        <div id="CapaTablaIframe" style="overflow-x: hidden;">
                            <div id="datos_log" style="overflow-x: hidden;"></div>
                            <table id="tabla_log" style="width: 1100px; min-width: 480px; overflow-y:auto; ">
                                <tr style="height: 30px;">
                                    <th style="width:10%;">NUM. SERIE</th>
                                    <th style="width:20%;">TIPO</th>
                                    <th style="width:20%;">ESTADO</th>
                                    <th style="width:10%; text-align: center;">AVERIADO</th>
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