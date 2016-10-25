<?php
// Fichero con las funciones de comprobación para AJAX del taller
include("../../classes/mysql.class.php");
include("../../classes/basicos/referencia.class.php");
include("../../classes/basicos/usuario.class.php");
include("../../classes/taller/taller.class.php");
include("../../classes/taller/recepcion_material.class.php");
include("../../classes/taller/albaran.class.php");
include("../../classes/orden_produccion/orden_produccion.class.php");
include("../../classes/orden_compra/orden_compra.class.php");
include("../../classes/orden_compra/listado_ordenes_compra.class.php");
include("../../classes/sede/sede.class.php");
include("../../classes/log/log_taller.class.php");

$db = new MySQL();
$ref = new Referencia();
$rm = new RecepcionMaterial();
$rs = new RecepcionMaterial();
$alb = new Albaran();
$op = new Orden_Produccion();
$listadoOP = new listadoOrdenesCompra();
$listado_orden_compra = new listadoOrdenesCompra();
$taller = new Taller();
$sede = new Sede();
$log = new Log_Taller();
$user = new Usuario();

$json = array();
$error = false;
if(isset($_GET["comp"])){
	switch($_GET["comp"]) {
        // CARGA LA REFERENCIA DESDE ENTRADA/SALIDA DE MATERIAL DE TALLER
        case "cargaReferencia":
            $id_referencia = $_GET["id_referencia"];
            $ref->cargaDatosReferenciaId($id_referencia);
            $nombre_referencia = $ref->referencia;
            $nombre_proveedor = $ref->nombre_proveedor;
            $referencia_proveedor = $ref->vincularReferenciaProveedorVar();
            $nombre_pieza = $ref->part_nombre;
            $pack_precio = $ref->pack_precio;
            $unidades_paquete = $ref->unidades;

            $metodo = $_GET["metodo"];
            $id_taller = $_GET["id_taller"];

            echo '<table id="tabla_buscador" style="max-width: 1100px; min-width: 480px;">
                <tr>
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
                <tr>
                    <td style="width:5%; text-align: center">' . $id_referencia . '</td>
                    <td style="width:25%;">' . $nombre_referencia . '</td>
                    <td style="width:10%;">' . $nombre_proveedor . '</td>
                    <td style="width:10%;">' . $referencia_proveedor . '</td>
                    <td style="width:10%;">' . $nombre_pieza . '</td>
                    <td style="width:10%; text-align: center;">' . $pack_precio . '</td>
                    <td style="width:5%; text-align: center;">' . $unidades_paquete . '</td>
                    <td style="width:10%; text-align: center"><input type="text" id="cantidad_referencia" style="width:50px; text-align:center;" onblur="extractNumber(this,2,true);" onkeyup="extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" /></td>
                    <td style="width:5%; text-align: center;"></td>';

            if($metodo == "RECEPCIONAR") {
                echo '<td style="width:10%; text-align: center;"><input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="RECEPCIONAR" onclick="recepcionarReferencia(' . $id_referencia . ',' . $id_taller . ')" /></td>';
            }
            else {
                echo '<td style="width:10%; text-align: center;"><input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="DESRECEPCIONAR" onclick="desrecepcionarReferencia(' . $id_referencia . ',' . $id_taller . ')" /></td>';
            }
            echo '</tr></table>';

            // Cantidad y precio se calculan mediante js
            echo '<div id="datos_referencia">';
            echo '<input type="hidden" id="id_referencia_hidden" value="' . $id_referencia . '" />';
            echo '<input type="hidden" id="nombre_referencia_hidden" value="' . $nombre_referencia . '" />';
            echo '<input type="hidden" id="nombre_proveedor_hidden" value="' . $nombre_proveedor . '" />';
            echo '<input type="hidden" id="referencia_proveedor_hidden" value="' . htmlspecialchars($referencia_proveedor) . '" />';
            echo '<input type="hidden" id="nombre_pieza_hidden" value="' . $nombre_pieza . '" />';
            echo '<input type="hidden" id="pack_precio_hidden" value="' . $pack_precio . '" />';
            echo '<input type="hidden" id="unidades_paquete_hidden" value="' . $unidades_paquete . '" />';
            echo '<input type="hidden" id="cantidad_referencia_hidden" value="" />';
            echo '</div>';
            break;
        // PROCESO DE RECEPCION DE UNA PIEZA EN UN TALLER
        case "recepcionar":
            // Si es un taller de mantenimiento recepcionamos en STOCK
            // Si es un taller de fábrica tenemos que recepcionar primero en la OP más antigua
            // Si todas las OP iniciadas están completas entonces guardamos en STOCK
            $id_referencia = $_GET["id_referencia"];
            $piezas = $_GET["piezas"];
            $id_taller = $_GET["id_taller"];

            // Cargamos los datos de la referencia
            $ref->cargaDatosReferenciaId($id_referencia);
            $nombre_referencia = $ref->referencia;
            $nombre_proveedor = $ref->nombre_proveedor;
            $referencia_proveedor = $ref->part_proveedor_referencia;
            $nombre_pieza = $ref->part_nombre;
            $pack_precio = $ref->pack_precio;
            $unidades_paquete = $ref->unidades;

            // Obtenemos el albarán al que pertenece la operación
            $id_albaran = $alb->dameUltimoAlbaran($id_taller);
            $id_albaran = $id_albaran["id_albaran"];

            // Obtenemos la sede a la que pertenece ese taller
            $id_sede = $taller->dameSedeTaller($id_taller);
            $id_sede = $id_sede["id_sede"];

            // Obtenemos las órdenes de producción iniciadas de esa sede
            $op->dameOPIniciadasReferencia($id_referencia,$id_sede);
            $op_iniciadas = $op->ids_produccion;
            $hay_ordenes = !empty($op_iniciadas);

            // Recorremos las OP iniciadas de esa referencia y vamos recepcionando en la más antigua hasta que se agoten las piezas
            $i = 0;
            $piezas_a_recepcionar = $piezas;
            while($i<count($op_iniciadas) && ($piezas_a_recepcionar > 0) && $hay_ordenes) {
                $id_produccion = $op_iniciadas[$i]["id_produccion"];

                // Obtenemos el registro de Orden Compra Referencias que queremos actualizar
                $registro_ocr = $rm->dameRegistroOCR($id_produccion, $id_referencia);
                $id = $registro_ocr["id"];
                $piezas_totales = $registro_ocr["total_piezas"];
                $piezas_recibidas = $registro_ocr["piezas_recibidas"];
                $piezas_por_entrar = $piezas_totales - $piezas_recibidas;

                if($piezas_a_recepcionar > 0) {
                    // RECEPCIONAR
                    if($piezas_por_entrar < $piezas_a_recepcionar) {
                        // Guardamos las piezas que se van a recepcionar en la OP
                        // Estas piezas se guardarán en albaranes_log
                        $piezas_recepcionadas_op = $piezas_totales - $piezas_recibidas;
                        // Completamos las piezas de esa OP
                        $piezas_recibidas = $piezas_totales;
                        // Descontamos las piezas de la OP completada
                        $piezas_a_recepcionar = $piezas_a_recepcionar - $piezas_por_entrar;
                    }
                    else {
                        $piezas_recibidas = $piezas_recibidas + $piezas_a_recepcionar;
                        // Guardamos las piezas que se van a recepcionar en la OP
                        $piezas_recepcionadas_op = $piezas_a_recepcionar;
                        $piezas_a_recepcionar = 0;
                    }
                }

                // Recepcionamos las piezas para esa OP
                $resultado_recepcion = $rm->recepcionarPorId($id, $id_referencia, $piezas_recibidas);
                if($resultado_recepcion != 0) {
                    // Guardamos el log de la referencia del albaran
                    $resultado = $alb->guardarLogReferencia($id_albaran, $id_referencia, $id_produccion, $piezas_recepcionadas_op, "RECEPCIONAR");
                    if($resultado != 1) {
                        // ERROR RECEPCION
                        $error = true;
                        $error_des = "Se produjo un error al guardar el movimiento de recepción en la OP[".$id_produccion."]";
                        $i = count($op_iniciadas);
                    }
                }
                else {
                    // ERROR RECEPCION
                    $error = true;
                    $error_des = "Se produjo un error al recepcionar piezas en la OP[".$id_produccion."]";
                    $i = count($op_iniciadas);
                }
                $i++;
            }

            if(!$error){
                if($piezas_a_recepcionar > 0){
                    // Si se recepcionaron todas las OP y siguen sobrando piezas las guardamos en STOCK
                    // Tenemos que ver si esa referencia tiene piezas en STOCK
                    $piezas_stock = $rs->damePiezasReferenciaStock($id_referencia, $id_taller);

                    if($piezas_stock != NULL) {
                        // ACTUALIZAR PIEZAS DE LA REFERENCIA DE STOCK
                        $piezas_stock = $piezas_stock + $piezas_a_recepcionar;
                        $resultado = $rs->actualizaPiezasStock($id_referencia, $piezas_a_recepcionar, $id_taller);
                        if($resultado == 1) {
                            // Guardamos en el log la recepcion en STOCK
                            $resultado = $alb->guardarLogReferencia($id_albaran, $id_referencia, 0, $piezas_a_recepcionar, "RECEPCIONAR");
                            if($resultado == 1) {
                                $error_des = "OK!";
                            }
                            else {
                                $error = true;
                                $error_des = $alb->getErrorMessage($resultado);
                            }
                        }
                        else {
                            $error = true;
                            $error_des = "Se produjo un error al recepcionar piezas en STOCK";
                        }
                    }
                    else {
                        // INSERTAR PIEZAS DE LA REFERENCIA DE STOCK
                        $resultado = $rs->insertaPiezasStock($id_referencia, $piezas_a_recepcionar, $id_taller);
                        if($resultado == 1) {
                            $resultado = $alb->guardarLogReferencia($id_albaran, $id_referencia, 0, $piezas_a_recepcionar, "RECEPCIONAR");
                            if($resultado == 1){
                                $error_des = "OK!";
                            }
                            else {
                                $error = true;
                                $error_des = $alb->getErrorMessage($resultado);
                            }
                        }
                        else {
                            $error = true;
                            $error_des = "Se produjo un error al recepcionar piezas en STOCK";
                        }
                    }
                }

                // Guardamos la referencia en albaran referencias
                $alb->datosNuevaReferenciaAlbaran($id_albaran, $id_referencia, $nombre_referencia, $nombre_proveedor, $referencia_proveedor, $nombre_pieza, $pack_precio, $unidades_paquete, $piezas, $activo);
                $resultado = $alb->guardarReferenciasAlbaran();
                if($resultado == 1) {
                    $error_des = "OK!";
                }
                else {
                    // ERROR al guardar la referencia
                    $error = true;
                    $error_des = $alb->getErrorMessage($resultado);
                }
            }

            // Preparamos los datos del LOG
            $alb->cargaDatosAlbaranId($id_albaran);
            $id_usuario = $alb->id_usuario;

            $user->cargaDatosUsuarioId($id_usuario);
            $usuario = $user->usuario;
            $id_tipo_usuario = $user->id_tipo;
            $res_tipo_usuario = $user->dameNombreTipoUsuario($id_tipo_usuario);
            $tipo_usuario = $res_tipo_usuario["tipo"];
            $proceso = "ENTRADA MATERIAL";
            if($error) $hubo_error = "SI";
            else $hubo_error = "NO";

            // Guardamos log de la operación
            $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_taller,$id_albaran,$id_referencia,$piezas,"REFERENCIA","-","-",$hubo_error,$error_des,NULL);
            $res_log = $log->guardarLog();
            if($res_log != 1){
                $error = true;
                $error_des = $log->getErrorMessage($res_log);
            }

            // Preparamos el array json para devolver los datos de la operación
            $array_movimiento = array("id_referencia_hidden" => $id_referencia,
                "nombre_referencia_hidden" => $nombre_referencia,
                "nombre_proveedor_hidden" => $nombre_proveedor,
                "referencia_proveedor_hidden" => $referencia_proveedor,
                "nombre_pieza_hidden" => $nombre_pieza,
                "pack_precio_hidden" => $pack_precio,
                "unidades_paquete_hidden" => $unidades_paquete,
                "cantidad_referencia_hidden" => $piezas,
                "id_albaran_hidden" => $id_albaran,
                "error" => $error,
                "error_des" => $error_des);

            $json = array("mov" => $array_movimiento);
            echo json_encode($json, JSON_FORCE_OBJECT);
            break;
        // DESHACE LA OPERACION DE RECEPCIÓN DE REFERENCIA PARA UN TALLER
        case "deshacerRecepcion":
            $id_referencia = $_GET["id_referencia"];
            $id_albaran = $_GET["id_albaran"];
            $id_taller = $_GET["id_taller"];
            $piezas_totales = 0;

            // Obtenemos los datos del log del albarán para esa referencia
            $datos_log = $alb->dameDatosLogReferencia($id_albaran, $id_referencia);
            for($i=0;$i<count($datos_log);$i++) {
                $id_produccion = $datos_log[$i]["id_produccion"];
                $piezas = $datos_log[$i]["piezas"];
                $piezas_totales = $piezas_totales + $piezas;

                // Comprobamos si es una OP o STOCK
                if($id_produccion == 0) {
                    // Descontar de STOCK
                    $resultado = $rm->quitarDelStock($id_referencia, $piezas, $id_taller);
                    if($resultado == 1) {
                        // Desactivamos el registro del albaran_log
                        $resultado = $alb->desactivarLogReferencia($id_albaran, $id_referencia, $id_produccion);
                        if($resultado != 1) {
                            // ERROR AL DESACTIVAR LOG DEL ALBARAN
                            $i = count($datos_log);
                            $error = true;
                            $error_des = $alb->getErrorMessage($resultado);
                        }
                        else $error_des = "OK!";
                    }
                    else {
                        $error = true;
                        $error_des = "Se produjo un error al deshacer la recepción";
                    }
                }
                else {
                    // Obtenemos la Orden de Compra a actualizar
                    $registro_ocr = $rm->dameRegistroOCR($id_produccion, $id_referencia);

                    $id = $registro_ocr["id"];
                    $piezas_recibidas = $registro_ocr["piezas_recibidas"];
                    // Restamos las piezas para deshacer los cambios
                    $piezas_recibidas = $piezas_recibidas - $piezas;

                    // Actualizamos la Orden de Compra
                    $resultado_deshacer = $rm->recepcionarPorId($id, $id_referencia, $piezas_recibidas);
                    if($resultado_deshacer != 0) {
                        // Desactivamos el albaran log
                        $resultado = $alb->desactivarLogReferencia($id_albaran, $id_referencia, $id_produccion);
                        if($resultado != 1) {
                            // ERROR RECEPCION
                            $i = count($datos_log);
                            $error = true;
                            $error_des = $alb->getErrorMessage($resultado);
                        }
                        else $error_des = "OK!";
                    }
                    else {
                        $error = true;
                        $error_des = "Se produjo un error al deshacer la recepción";
                    }
                }
            }
            // Desactivamos la referencia de albaranes_referencias
            $resultado = $alb->desactivarReferencia($id_albaran, $id_referencia);
            if($resultado != 1) {
                $error = true;
                $error_des = $alb->getErrorMessage($resultado);
            }
            else $error_des = "OK!";

            // Preparamos los datos del LOG
            $alb->cargaDatosAlbaranId($id_albaran);
            $id_usuario = $alb->id_usuario;

            $user->cargaDatosUsuarioId($id_usuario);
            $usuario = $user->usuario;
            $id_tipo_usuario = $user->id_tipo;
            $res_tipo_usuario = $user->dameNombreTipoUsuario($id_tipo_usuario);
            $tipo_usuario = $res_tipo_usuario["tipo"];
            $proceso = "DESHACER ENTRADA MATERIAL";
            if($error) $hubo_error = "SI";
            else $hubo_error = "NO";

            // Guardamos log de la operación
            $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_taller,$id_albaran,$id_referencia,$piezas_totales,"REFERENCIA","-","-",$hubo_error,$error_des,NULL);
            $res_log = $log->guardarLog();
            if($res_log != 1){
                $error = true;
                $error_des = $log->getErrorMessage($res_log);
            }

            // Preparamos el array json para devolver los datos de la operación
            $array_movimiento = array(
                "error" => $error,
                "error_des" => $error_des);

            $json = array("mov" => $array_movimiento);
            echo json_encode($json, JSON_FORCE_OBJECT);
            break;
        // PROCESO DE DESRECEPCIÓN DE UNA PIEZA EN UN TALLER
        case "desrecepcionar":
            // Si es un taller de mantenimiento desrecepcionamos de STOCK
            // Si es un taller de fábrica tenemos que desrecepcionar primero de STOCK
            // Si no hay mas piezas en STOCK descuenta de la OP mas moderna
            $id_referencia = $_GET["id_referencia"];
            $piezas = $_GET["piezas"];
            $id_taller = $_GET["id_taller"];

            // Cargamos los datos de la referencia
            $ref->cargaDatosReferenciaId($id_referencia);

            $nombre_referencia = $ref->referencia;
            $nombre_proveedor = $ref->nombre_proveedor;
            $referencia_proveedor = $ref->part_proveedor_referencia;
            $nombre_pieza = $ref->part_nombre;
            $pack_precio = $ref->pack_precio;
            $unidades_paquete = $ref->unidades;

            // Obtenemos el albarán al que pertenece la operación
            $id_albaran = $alb->dameUltimoAlbaran($id_taller);
            $id_albaran = $id_albaran["id_albaran"];

            // Obtenemos la sede a la que pertenece ese taller
            $id_sede = $taller->dameSedeTaller($id_taller);
            $id_sede = $id_sede["id_sede"];

            // Desrecepcionamos primero de STOCK y luego de las OP iniciadas
            // Tenemos que ver si esa referencia tiene piezas en stock
            $piezas_stock = $rs->damePiezasReferenciaStock($id_referencia,$id_taller);
            if(empty($piezas_stock)) $piezas_stock = 0;
            $hay_piezas_stock = !empty($piezas_stock);
            $error_stock = false;
            $error = false;
            $piezas_a_desrecepcionar = $piezas;

            $op->dameOPIniciadasReferenciaModernas($id_referencia,$id_sede);
            $op_iniciadas = $op->ids_produccion;
            $num_op = count($op_iniciadas);

            // Comprobamos que hay piezas en STOCK o en las ops para desrecepcionar
            $hay_piezas_recibidas_ops = $rm->hayPiezasRecibidasOPsIniciadas($id_referencia,$id_taller);
            $hay_piezas_recibidas = $hay_piezas_recibidas_ops || $hay_piezas_stock;

            $total_piezas_recibidas = $rm->damePiezasRecibidasOPsIniciadas($id_referencia,$id_taller);
            $total_piezas_recibidas = $total_piezas_recibidas + $piezas_stock; 
            // Comprobamos que no se intenta desrecepcionar más piezas de las recibidas en las OP y STOCK
            $superarDesrecepcion = $total_piezas_recibidas < $piezas;

            if($hay_piezas_recibidas) {
                if(!$superarDesrecepcion) {
                    if (!empty($piezas_stock)) {
                        // DESRECEPCIONAR
                        if ($piezas_stock < $piezas_a_desrecepcionar) {
                            if (!empty($num_op)) {
                                $resultado = $rs->retiraPiezaStock($id_referencia, $id_taller);
                                if ($resultado == 1) {
                                    // Guardamos en el log la recepción en STOCK
                                    $resultado = $alb->guardarLogReferencia($id_albaran, $id_referencia, 0, $piezas_stock, "DESRECEPCIONAR");
                                    // Calculamos las piezas que quedan por desrecepcionar
                                    $piezas_a_desrecepcionar = $piezas_a_desrecepcionar - $piezas_stock;
                                    if ($resultado != 1) {
                                        $error = true;
                                        $error_des = "Se produjo un error al guardar el movimiento de desrecepción del STOCK";
                                    }
                                }
                                else {
                                    $error = true;
                                    $error_des = "Se produjo un error al retirar piezas de STOCK";
                                }
                            }
                            else {
                                $error = true;
                                $error_des = "No hay suficientes piezas para desrecepcionar de stock y no hay órdenes de producción vinculadas";
                            }
                        }
                        else {
                            $resultado = $rs->descontarPiezasStock($id_referencia, $piezas_a_desrecepcionar, $id_taller);
                            if ($resultado == 1) {
                                // Guardamos en el log la recepción en STOCK
                                $resultado = $alb->guardarLogReferencia($id_albaran, $id_referencia, 0, $piezas_a_desrecepcionar, "DESRECEPCIONAR");
                                $piezas_a_desrecepcionar = 0;
                                if ($resultado != 1) {
                                    $error = true;
                                    $error_des = "Se produjo un error al guardar el movimiento de desrecepción del STOCK";
                                }
                            } else {
                                $error = true;
                                $error_des = "Se produjo un error al retirar piezas de STOCK";
                            }
                        }
                    }

                    if(!$error) {
                        // Recorremos las op iniciadas de esa referencia y vamos desrecepcionando de la más nueva hasta que se agoten las piezas
                        $i = 0;
                        $error = (empty($piezas_stock) && empty($num_op));
                        $error_ops_sin_piezas = true;
                        while ($i < count($op_iniciadas) && ($piezas_a_desrecepcionar > 0)) {
                            $id_produccion = $op_iniciadas[$i]["id_produccion"];

                            // Obtenemos el registro de Orden Compra Referencias que queremos actualizar
                            $registro_ocr = $rm->dameRegistroOCR($id_produccion, $id_referencia);
                            $id = $registro_ocr["id"];
                            $piezas_totales = $registro_ocr["total_piezas"];
                            $piezas_recibidas = $registro_ocr["piezas_recibidas"];

                            // Si quedan piezas (introducidas por el usuario) por desrecepcionar
                            if ($piezas_a_desrecepcionar > 0) {
                                // Si no se recibió ninguna pieza en esa OP, no desrecepcionamos
                                if (!empty($piezas_recibidas)) {
                                    // DESRECEPCIONAR
                                    $error_ops_sin_piezas = false;
                                    if ($piezas_recibidas < $piezas_a_desrecepcionar) {
                                        // Guardamos las piezas que se van a desrecepcionar en la OP
                                        // Estas piezas se guardarán en albaranes_log
                                        $piezas_desrecepcionadas_op = $piezas_recibidas;
                                        // Actualizamos las piezas a desrecepcionar
                                        $piezas_a_desrecepcionar = $piezas_a_desrecepcionar - $piezas_recibidas;
                                        $piezas_recibidas = 0;
                                    } else {
                                        $piezas_recibidas = $piezas_recibidas - $piezas_a_desrecepcionar;
                                        // Guardamos las piezas que se van a desrecepcionar en la OP
                                        $piezas_desrecepcionadas_op = $piezas_a_desrecepcionar;
                                        $piezas_a_desrecepcionar = 0;
                                    }

                                    // Desrecepcionamos las piezas para esa OP
                                    $resultado_recepcion = $rm->recepcionarPorId($id, $id_referencia, $piezas_recibidas);
                                    if ($resultado_recepcion == 1) {
                                        // Guardamos el log de la referencia del albarán
                                        $resultado = $alb->guardarLogReferencia($id_albaran, $id_referencia, $id_produccion, $piezas_desrecepcionadas_op, "DESRECEPCIONAR");
                                        if ($resultado != 1) {
                                            $error = true;
                                            $error_des = "Se produjo un error al guardar el movimiento de desrecepcion de una OP";
                                            $i = count($op_iniciadas);
                                        }
                                    } else {
                                        $error = true;
                                        $error_des = "Se produjo un error al retirar piezas de una OP";
                                        $i = count($op_iniciadas);
                                    }
                                }
                            }
                            $i++;
                        }
                        if (!$error) {
                            // Guardamos la referencia en albarán referencias
                            $id_albaran = $alb->dameUltimoAlbaran($id_taller);
                            $id_albaran = $id_albaran["id_albaran"];

                            $alb->datosNuevaReferenciaAlbaran($id_albaran, $id_referencia, $nombre_referencia, $nombre_proveedor, $referencia_proveedor, $nombre_pieza, $pack_precio, $unidades_paquete, $piezas, $activo);
                            $resultado = $alb->guardarReferenciasAlbaran();
                            if ($resultado == 1) {
                                // OK
                                $error_des = "OK!";
                            } else {
                                // ERROR al guardar la referencia
                                $error = true;
                                $error_des = "Se produjo un error al guardar la referencia de desrecepción";
                            }
                        } else {
                            // ERROR DESRECEPCION PIEZAS SIN STOCK
                            $error = true;
                            $error_des = "No hay suficientes piezas para desrecepcionar de stock y no hay órdenes de producción vinculadas";
                        }
                    }
                }
                else {
                    $error = true;
                    $error_des = "Se esta intentando desrecepcionar más piezas de las recibidas\n\n Piezas recibidas: ".$total_piezas_recibidas."\n Piezas a desrecepcionar: ".$piezas;
                }
            }
            else {
                // ERROR NO HAY PIEZAS PARA DESRECEPCIONAR
                $error = true;
                $error_des = "No hay piezas para desrecepcionar";
            }

            // Preparamos los datos del LOG
            $alb->cargaDatosAlbaranId($id_albaran);
            $id_usuario = $alb->id_usuario;

            $user->cargaDatosUsuarioId($id_usuario);
            $usuario = $user->usuario;
            $id_tipo_usuario = $user->id_tipo;
            $res_tipo_usuario = $user->dameNombreTipoUsuario($id_tipo_usuario);
            $tipo_usuario = $res_tipo_usuario["tipo"];
            $proceso = "SALIDA MATERIAL";
            if($error) $hubo_error = "SI";
            else $hubo_error = "NO";

            // Guardamos log de la operación
            $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_taller,$id_albaran,$id_referencia,$piezas,"REFERENCIA","-","-",$hubo_error,$error_des,NULL);
            $res_log = $log->guardarLog();
            if($res_log != 1){
                $error = true;
                $error_des = $log->getErrorMessage($res_log);
            }

            $array_movimiento = array("id_referencia_hidden" => $id_referencia,
                                        "nombre_referencia_hidden" => $nombre_referencia,
                                        "nombre_proveedor_hidden" => $nombre_proveedor,
                                        "referencia_proveedor_hidden" => $referencia_proveedor,
                                        "nombre_pieza_hidden" => $nombre_pieza,
                                        "pack_precio_hidden" => $pack_precio,
                                        "unidades_paquete_hidden" => $unidades_paquete,
                                        "cantidad_referencia_hidden" => $piezas,
                                        "id_albaran_hidden" => $id_albaran,
                                        "error" => $error,
                                        "error_des" => $error_des);

            $json = array("mov" => $array_movimiento);
            echo json_encode($json, JSON_FORCE_OBJECT);
            break;
        // DESHACE LA OPERACION DE DESRECEPCIÓN DE REFERENCIA PARA UN TALLER
        case "deshacerDesRecepcion":
            $id_referencia = $_GET["id_referencia"];
            $id_albaran = $_GET["id_albaran"];
            $id_taller = $_GET["id_taller"];
            $piezas_totales =0;

            // Obtenemos los datos del log del albarán para esa referencia
            $datos_log = $alb->dameDatosLogReferencia($id_albaran, $id_referencia);
            for($i=0;$i<count($datos_log);$i++) {
                $id_produccion = $datos_log[$i]["id_produccion"];
                $piezas = $datos_log[$i]["piezas"];
                $piezas_totales = $piezas_totales + $piezas;

                // Comprobamos si es una OP o STOCK
                if($id_produccion == 0) {
                    // Reponer STOCK
                    $resultado = $rm->guardarStock($id_referencia, $piezas, $id_taller);
                    if($resultado == 1) {
                        // Desactivamos el registro del albaran_log
                        $resultado = $alb->desactivarLogReferencia($id_albaran, $id_referencia, $id_produccion);
                        if($resultado != 1) {
                            // ERROR AL DESACTIVAR LOG DEL ALBARAN
                            $i = count($datos_log);
                            $error = true;
                            $error_des = $alb->getErrorMessage($resultado);
                        }
                    }
                    else {
                        $error = true;
                        $error_des = "Se produjo un error al deshacer la desrecepción";
                    }
                }
                else {
                    // Obtenemos la Orden de Compra a actualizar
                    $registro_ocr = $rm->dameRegistroOCR($id_produccion,$id_referencia);

                    $id = $registro_ocr["id"];
                    $piezas_recibidas = $registro_ocr["piezas_recibidas"];
                    // Restamos las piezas para deshacer los cambios
                    $piezas_recibidas = $piezas_recibidas + $piezas;

                    // Actualizamos la Orden de Compra
                    $resultado_deshacer = $rm->recepcionarPorId($id,$id_referencia,$piezas_recibidas);
                    if($resultado_deshacer != 0) {
                        // Desactivamos el albaran log
                        $resultado = $alb->desactivarLogReferencia($id_albaran,$id_referencia,$id_produccion);
                        if($resultado != 1) {
                            // ERROR RECEPCION
                            $i = count($datos_log);
                            $error = true;
                            $error_des = $alb->getErrorMessage($resultado);
                        }
                    }
                }
            }
            // Desactivamos la referencia de albaranes_referencias
            $resultado = $alb->desactivarReferencia($id_albaran,$id_referencia);
            if($resultado != 1) {
                $error = true;
                $error_des = $alb->getErrorMessage($resultado);
            }
            else $error_des = "OK!";

            // Preparamos los datos del LOG
            $alb->cargaDatosAlbaranId($id_albaran);
            $id_usuario = $alb->id_usuario;

            $user->cargaDatosUsuarioId($id_usuario);
            $usuario = $user->usuario;
            $id_tipo_usuario = $user->id_tipo;
            $res_tipo_usuario = $user->dameNombreTipoUsuario($id_tipo_usuario);
            $tipo_usuario = $res_tipo_usuario["tipo"];
            $proceso = "DESHACER SALIDA MATERIAL";
            if($error) $hubo_error = "SI";
            else $hubo_error = "NO";

            // Guardamos log de la operación
            $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_taller,$id_albaran,$id_referencia,$piezas_totales,"REFERENCIA","-","-",$hubo_error,$error_des,NULL);
            $res_log = $log->guardarLog();
            if($res_log != 1){
                $error = true;
                $error_des = $log->getErrorMessage($res_log);
            }

            // Preparamos el array json para devolver los datos de la operación
            $array_movimiento = array(
                "error" => $error,
                "error_des" => $error_des);

            $json = array("mov" => $array_movimiento);
            echo json_encode($json, JSON_FORCE_OBJECT);
            break;
        case "recepcionarDesrecepcionar":
            $id_taller = $_GET["id_taller"];
            $id_usuario = $_GET["id_usuario"];

            // Obtenemos la sede a la que pertenece ese taller
            $id_sede = $taller->dameSedeTaller($id_taller);
            $id_sede = $id_sede["id_sede"];

            // Obtenemos los campos pasados por url
            $id_referencia = $_GET["id_referencia"];
            $id_produccion = $_GET["id_produccion"];
            $unidades_entrada = $_GET["unidades_entrada"];
            $unidades = floatval($unidades_entrada);
            $unidades_stock = 0;
            $error = false;

            // Comprobamos si se realizará el proceso sobre una orden de producción o sobre stock
            if($id_produccion != 0) {
                // Obtenemos el registro de Orden Compra Referencias que queremos actualizar
                $registro_ocr = $rm->dameRegistroOCR($id_produccion, $id_referencia);
                $id = $registro_ocr["id"];
                $piezas_totales = $registro_ocr["total_piezas"];
                $piezas_recibidas = $registro_ocr["piezas_recibidas"];
                $piezas_por_entrar = $piezas_totales - $piezas_recibidas;

                if($unidades > 0) {
                    // RECEPCIONAR
                    if($piezas_por_entrar < $unidades) {
                        // Completamos las piezas de esa OP
                        $piezas_recibidas = $piezas_totales;
                        // Descontamos las piezas de la OP completada
                        $unidades = $unidades - $piezas_por_entrar;
                    }
                    else {
                        $piezas_recibidas = $piezas_recibidas + $unidades;
                        $unidades = 0;
                    }
                }
                else {
                    // DESRECEPCIONAR
                    $unidades = abs($unidades);
                    if($piezas_recibidas < $unidades) {
                        $error_desrecepcion = true;
                    }
                    else {
                        $piezas_recibidas = $piezas_recibidas - $unidades;
                        $unidades = 0;
                    }
                }

                if(!$error_desrecepcion) {
                    $res = $rm->actualizaPiezasRecibidasPorId($id, $piezas_recibidas, $unidades_stock, $proceso_stock, $id_referencia, $id_taller);
                    if ($res == 1) {
                        // Si sobran unidades tras la recepcion se asignarán a las otras OP iniciadas.
                        // En el caso de que todas las OP queden completadas, las piezas restantes se guardan en STOCK
                        if ($unidades > 0) {
                            $piezas_recepcionadas = $piezas_totales;
                            // Tendremos que obtener las OP iniciadas de esa referencia y rellenar las piezas hasta que se agoten.
                            $op->dameOPIniciadasReferencia($id_referencia, $id_sede);
                            $ids_produccion = $op->ids_produccion;
                            $i = 0;
                            $encontrado = ($unidades <= 0);
                            while ($i < count($ids_produccion) and !$encontrado) {
                                // Obtenemos los códigos de las OP
                                $op->cargaDatosProduccionId($ids_produccion[$i]["id_produccion"]);
                                $alias = $op->alias_op;

                                if ($ids_produccion[$i]["id_produccion"] != $id_produccion) {
                                    // Cogemos las OP iniciadas menos la OP que rellenamos anteriormente
                                    // Obtenemos el registro de Orden Compra Referencias que queremos actualizar
                                    $registro_ocr = $rm->dameRegistroOCR($ids_produccion[$i]["id_produccion"], $id_referencia);
                                    $id = $registro_ocr["id"];
                                    $piezas_totales = $registro_ocr["total_piezas"];
                                    $piezas_recibidas = $registro_ocr["piezas_recibidas"];
                                    $piezas_por_entrar = $piezas_totales - $piezas_recibidas;

                                    if ($piezas_por_entrar < $unidades) {
                                        // Completamos las piezas de esa OP
                                        $piezas_recibidas = $piezas_totales;
                                        $piezas_recepcionadas = $piezas_totales;
                                        $unidades = $unidades - $piezas_por_entrar;
                                        $res = $rm->actualizaPiezasRecibidasPorId($id, $piezas_recibidas, 0, $proceso_stock, $id_referencia, $id_taller);
                                        if ($res == 1) $error_des = "OK!";
                                        else {
                                            $error = true;
                                            $error_des = "Se produjo un error al actualizar las piezas de una OCR en ajuste de material";
                                        }
                                    }
                                    else {
                                        $piezas_recibidas = $piezas_recibidas + $unidades;
                                        $piezas_recepcionadas = $unidades;
                                        $unidades = 0;
                                        $res = $rm->actualizaPiezasRecibidasPorId($id, $piezas_recibidas, 0, $proceso_stock, $id_referencia, $id_taller);
                                        if ($res == 1) $error_des = "OK!";
                                        else {
                                            $error = true;
                                            $error_des = "Se produjo un error al actualizar las piezas de una OCR en ajuste de material";
                                        }
                                    }
                                    $piezas_recepcionadas = $piezas_recibidas;
                                }
                                $encontrado = ($unidades <= 0);
                                $i++;
                            }
                            // Si todavia quedan unidades las guardamos en STOCK
                            if ($unidades > 0) {
                                $piezas_stock = $rs->damePiezasReferenciaStock($id_referencia, $id_taller);
                                if ($piezas_stock != NULL) {
                                    $res = $rs->actualizaPiezasStock($id_referencia, $unidades, $id_taller);
                                    if ($res == 1) $error_des = "OK!";
                                    else {
                                        $error = true;
                                        $error_des = "Se produjo un error al registrar las piezas en STOCK";
                                    }
                                }
                                else {
                                    $res = $rs->insertaPiezasStock($id_referencia, $unidades, $id_taller);
                                    if ($res == 1) $error_des = "OK!";
                                    else {
                                        $error = true;
                                        $error_des = "Se produjo un error al registrar las piezas en STOCK";
                                    }
                                }
                            }
                        }
                        else {
                            // DESRECEPCION
                            $error_des = "OK!";
                        }
                    }
                    else {
                        $error = true;
                        $error_des = "Se produjo un error al actualizar las piezas de una OCR en ajuste de material";
                    }
                }
                else {
                    $error = true;
                    $error_des = "Se esta intentando desrecepcionar más piezas de las recibidas\n\n Piezas recibidas: ".$piezas_recibidas."\n Piezas a desrecepcionar: ".$unidades;
                }
            }
            else {
                // STOCK
                // En caso de que el ajuste se haga en STOCK sólo se reajustará el mismo sin adaptar las OP
                $piezas_stock = $rs->damePiezasReferenciaStock($id_referencia, $id_taller);

                if($piezas_stock != NULL) {
                    if($unidades > 0) {
                        // RECEPCIONAR
                        $piezas_stock = $piezas_stock + $unidades;
                        $res = $rs->actualizaPiezasStock($id_referencia, $unidades, $id_taller);
                        if($res == 1) $error_des = "OK!";
                        else {
                            $error = true;
                            $error_des = "Se produjo un error al registrar las piezas en STOCK";
                        }
                    }
                    else {
                        // DESRECEPCIONAR
                        if($piezas_stock < abs($unidades)) {
                            $error = true;
                            $error_des = "No hay suficientes piezas para desrecepcionar de stock";
                        }
                        else {
                            $res = $rs->actualizaPiezasStock($id_referencia, $unidades, $id_taller);
                            if($res == 1) $error_des = "OK!";
                            else {
                                $error = true;
                                $error_des = "Se produjo un error al registrar las piezas en STOCK";
                            }
                        }
                    }
                }
                else {
                    if($unidades > 0) {
                        $res = $rs->insertaPiezasStock($id_referencia, $unidades, $id_taller);
                        if($res == 1) $error_des = "OK!";
                        else {
                            $error = true;
                            $error_des = "Se produjo un error al registrar las piezas en STOCK";
                        }
                    }
                    else {
                        $error = true;
                        $error_des = "No hay piezas para desrecepcionar";
                    }
                }
            }

            $user->cargaDatosUsuarioId($id_usuario);
            $usuario = $user->usuario;
            $id_tipo_usuario = $user->id_tipo;
            $res_tipo_usuario = $user->dameNombreTipoUsuario($id_tipo_usuario);
            $tipo_usuario = $res_tipo_usuario["tipo"];
            $proceso = "AJUSTE MATERIAL";
            if($error) $hubo_error = "SI";
            else $hubo_error = "NO";

            // Guardamos log de la operación
            $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_taller,0,$id_referencia,$unidades_entrada,"REFERENCIA","-","-",$hubo_error,$error_des,NULL);
            $res_log = $log->guardarLog();
            if($res_log != 1){
                $error = true;
                $error_des = $log->getErrorMessage($res_log);
            }

            // Preparamos el array json para devolver los datos de la operación
            $array_movimiento = array(
                "error" => $error,
                "error_des" => $error_des);

            $json = array("mov" => $array_movimiento);
            echo json_encode($json, JSON_FORCE_OBJECT);
            break;
        case "cargaOpsPorSede":
            $json = array();

            // Obtenemos la sede
            $id_sede = $_GET["id_sede"];
            $listadoOP->prepararOPIniciadas($id_sede);
            $listadoOP->realizarConsultaOP();
            $resultados_op = $listadoOP->orden_produccion;

            // Cargamos los datos de las órdenes de producción iniciadas de esa sede y las guardamos en un array para incluirlo en la variable JSON
            for($i=0;$i<count($resultados_op);$i++) {
                $id_produccion = $resultados_op[$i]["id_produccion"];
                $op->cargaDatosProduccionId($id_produccion);

                $array_ops[] = array("id_produccion" => $op->id_produccion,
                                        "alias_op" => $op->alias_op,
                                        "codigo" => $op->codigo);
            }

            $json = array("ops" => $array_ops);
            echo json_encode($json, JSON_FORCE_OBJECT);
            break;
        case "cargarOrdenesCompra":
        	$ajuste = $_GET["ajuste"];
            $ids_produccion = $_GET["id_produccion"];
            $nombre_proveedor = $_GET["proveedor"];
            $id_sede = $_GET["id_sede"];

            // No se seleccionó proveedor
            if($ids_produccion == "") {
                if($ajuste == 1){
                    // Buscador de ajuste de material
                    $respuesta .= '<input type="text" id="" name="orden_compra" class="BuscadorInput" style="width:200px;" maxlength="50" value="'.$_SESSION["orden_compra_ajuste_taller"].'"/>';
                }
                else {
                    // Buscador de listado de material
                    $respuesta .= '<input type="text" id="" name="orden_compra" class="BuscadorInput" style="width:200px;" maxlength="50" value="'.$_SESSION["orden_compra_taller_material"].'"/>';
                }
            }
            else {
                $orden_produccion = $_GET["id_produccion"];
                $orden_produccion = explode(",", $orden_produccion);
                $proveedor = $_GET["proveedor"];

                $listado_orden_compra->serValoresBusquedaProvOP($orden_produccion, $proveedor,$id_sede);
                $listado_orden_compra->realizarConsulta();
                $resultadosBusqueda = $listado_orden_compra->ordenes_compra;

                // Si no hay OCS que mostrar cambiamos el select por el input
                if(count($resultadosBusqueda) != 0) {
                    $respuesta .= '<select id="orden_compra" name="orden_compra" class="BuscadorInput" style="width:200px;">
									<option></option>';

                    for ($i = 0; $i < count($resultadosBusqueda); $i++) {
                        $respuesta .= '<option value="' . $resultadosBusqueda[$i]["numero_pedido"] . '">' . $resultadosBusqueda[$i]["numero_pedido"] . '(' . $resultadosBusqueda[$i]["nombre_prov"] . ')</option>';
                    }
                    $respuesta .= '</select>';
                }
                else {
                    if($ajuste == 1){
                        // Buscador de ajuste de material
                        $respuesta .= '<input type="text" id="" name="orden_compra" class="BuscadorInput" style="width:200px;" maxlength="50" value="'.$_SESSION["orden_compra_ajuste_taller"].'"/>';
                    }
                    else {
                        // Buscador de listado de material
                        $respuesta .= '<input type="text" id="" name="orden_compra" class="BuscadorInput" style="width:200px;" maxlength="50" value="'.$_SESSION["orden_compra_taller_material"].'"/>';
                    }
                }
            }
			echo $respuesta;
            break;
        case "cargaTalleres":
        	$id_sede = $_GET["id_sede"];
            $id_pagina = $_GET["id_pagina"];
        	$respuesta .= '<select id="talleres" name="talleres" class="BuscadorInput" style="width: 200px;">';

            // Obtenemos los talleres de esa sede
            $res_talleres = $sede->dameTalleresSede($id_sede);
            for($i=0;$i<count($res_talleres);$i++){
                $id_taller = $res_talleres[$i]["id_taller"];
                $nombre = $res_talleres[$i]["taller"];
                $respuesta .= '<option value="'.$id_taller.'">'.$nombre.'</option>';
            }
            
            $respuesta .= '</select>';
			echo $respuesta;            
            break;
        case "cargaMotivos":
            $json = array();
            // Obtenemos el taller y el tipo de albarán
            $id_taller = $_GET["id_taller"];
            $tipo_albaran = $_GET["tipo_albaran"];

            if($tipo_albaran == "ENTRADA") $res_motivos = $taller->dameMotivosAlbaranEntrada($id_taller);
            else if($tipo_albaran == "SALIDA") $res_motivos = $taller->dameMotivosAlbaranSalida($id_taller);
            else $res_motivos = $taller->dameMotivosAlbaran($id_taller);

            // Cargamos los motivos y los guardamos en un array para incluirlo en la variable JSON
            for($i=0;$i<count($res_motivos);$i++) $array_motivos[] = $res_motivos[$i]["motivo"];

            $json = $array_motivos;
            echo json_encode($json, JSON_FORCE_OBJECT);
            break;
        default:
        case "cargaMotivosBuscador":
            $json = array();
            // Obtenemos la sede
            $id_sede = $_GET["id_sede"];
            // Obtenemos los motivos según la sede
            $res_motivos = $sede->dameMotivosAlbaranSede($id_sede);
            // Cargamos los motivos y los guardamos en un array para incluirlo en la variable JSON
            for($i=0;$i<count($res_motivos);$i++) $array_motivos[] = $res_motivos[$i]["motivo"];

            $json = $array_motivos;
            echo json_encode($json, JSON_FORCE_OBJECT);
            break;
        default:

        break;

	}
}
?>

