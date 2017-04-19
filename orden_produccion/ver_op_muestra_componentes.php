<?php
// Este fichero muestra los componentes de una orden de producción
$ids_produccion_componente = $orden_produccion->dameIdsProduccionComponente($id_produccion);
$coste_producto = 0;
$coste_produccion = 0;

// Mostramos la tabla con las referencias de los componentes
for($i=0;$i<count($ids_produccion_componente);$i++){
    $id_produccion_componente = $ids_produccion_componente[$i]["id_produccion_componente"];
    $id_componente = $orden_produccion->dameIdComponentePorIdProduccionComponente($id_produccion_componente);
    $id_componente = $id_componente[0]["id_componente"];
    // Obtenemos el tipo del componente
    $id_tipo = $orden_produccion->dameTipoComponente($id_componente);
    $id_tipo = $id_tipo["id_tipo"];

    switch ($id_tipo) {
        case '1':
            // Deja de existir en Septiembre de 2016
        break;
        case '2':
            // PERIFERICO
            $periferico->cargaDatosPerifericoId($id_componente);
            $nombre_componente = "Periferico";
            $nombre_componente_principal = "Periferico";
            $titulo_componente = $periferico->periferico.'_v'.$periferico->version;
            $es_prototipo = ($periferico->prototipo == 1);
            $coste_total_componente = 0;
            // Obtenemos los kits del periférico
            $res_kits_periferico = $comp->dameKitsComponente($id_componente);
            if(!empty($res_kits_periferico)){
                foreach ($res_kits_periferico as $array_kits) $res_kits[] = $array_kits["id_kit"];
                //$res_kits = array_column($comp->dameKitsComponente($id_componente), "id_kit");
            }
        break;
        case '3':
            // Deja de existir en Septiembre de 2016
        break;
        case '4':
            // Deja de existir en Agosto de 2016
        break;
        case '5':
            // KIT
            $kit->cargaDatosKitId($id_componente);
            $nombre_componente = "Kit";
            $titulo_componente = $kit->kit.'_v'.$kit->version;
            $es_prototipo = ($kit->prototipo == 1);
            if($siguiente_es_kit_libre) {
                $nombre_componente_principal = $nombre_componente." Libre";
                $coste_total_componente = 0;
            }
        break;
        case '6':
            // KIT LIBRE
            $kit->cargaDatosKitId($id_componente);
            $nombre_componente = "Kit";
            $titulo_componente = $kit->kit.'_v'.$kit->version;
            $es_prototipo = ($kit->prototipo == 1);
            if($siguiente_es_kit_libre) {
                $nombre_componente_principal = $nombre_componente." Libre";
                $coste_total_componente = 0;
            }
            break;
        default:
            //
        break;
    }

    // Cargamos los datos de orden_producción_referencias
    $resultados = $orden_produccion->cargaDatosPorProduccionComponente($id_produccion,$id_produccion_componente); ?>
    <br/>
    <div class="ContenedorCamposCreacionBasico">
        <div class="LabelCreacionBasico">Referencias <?php echo $nombre_componente; ?></div>
        <div class="tituloComponente">
            <table id="tablaTituloPrototipo">
            <tr>
                <td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;"><span class="tituloComp"><?php echo $titulo_componente; ?></span></td>
                <td style="text-align:left; background:#fff; vertical-align:top; padding:0px 5px 0px 5px;">
                <?php
                    if ($es_prototipo) { ?>
                        <span class="ImagenPrototipo"><img src="../images/prototipo.jpg" width="20px" height="20px" alt="PROTOTIPO" title="PROTOTIPO"></span>
                <?php
                    }
                    else { ?>
                        <span class="ImagenPrototipo"><img src="../images/engranaje.gif" width="20px" height="20px" alt="PRODUCCION" title="PRODUCCION"></span>
                <?php
                    }
                ?>
                </td>
            </tr>
            </table>
        </div>
        <div class="CajaReferencias">
            <div id="CapaTablaIframe">
                <table>
                <tr>
                    <th style="text-align:center">ID_REF</th>
                    <th>NOMBRE</th>
                    <th>PROVEEDOR</th>
                    <th>REF PROV</th>
                    <th>NOMBRE PIEZA</th>
                    <th style="text-align:center">PIEZAS</th>
                    <th style="text-align:center">PACK PRECIO</th>
                    <th style="text-align:center">UDS/P</th>
                    <th style="text-align:center">TOTAL PAQS</th>
                    <th style="text-align:center">PRECIO UNIDAD</th>
                    <th style="text-align:center">PRECIO</th>
                </tr>
                <?php
                    $precio_componente = 0;
                    for($j = 0; $j<count($resultados); $j++) {
                        $id_referencia = $resultados[$j]["id_referencia"];
                        $uds_paquete = $resultados[$j]["uds_paquete"];
                        $piezas = $resultados[$j]["piezas"];
                        $total_paquetes = $resultados[$j]["total_paquetes"];
                        $pack_precio = $resultados[$j]["pack_precio"];

                        if ($pack_precio != 0 and $uds_paquete != 0) $precio_unidad = $pack_precio / $uds_paquete;
                        else $precio_unidad = 0;

                        $precio_referencia = $precio_unidad * $piezas;
                        $precio_componente = $precio_componente + $precio_referencia;
                        $referencia->cargaDatosReferenciaId($id_referencia); ?>

                        <tr>
                            <td style="text-align:center"><?php echo $id_referencia; ?></td>
                            <td>
                            <?php
                                if (strlen($referencia->referencia) > $max_caracteres_ref) {
                                    echo substr($referencia->referencia, 0, $max_caracteres_ref) . '...';
                                }
                                else echo $referencia->referencia;
                            ?>
                            </td>
                            <td>
                            <?php
                                if (strlen($referencia->nombre_proveedor) > $max_caracteres) {
                                    echo substr($referencia->nombre_proveedor, 0, $max_caracteres) . '...';
                                }
                                else echo $referencia->nombre_proveedor;
                            ?>
                            </td>
                            <td><?php $referencia->vincularReferenciaProveedor(); ?></td>
                            <td>
                            <?php
                                if (strlen($referencia->part_nombre) > $max_caracteres) {
                                    echo substr($referencia->part_nombre, 0, $max_caracteres) . '...';
                                }
                                else echo $referencia->part_nombre;
                            ?>
                            </td>
                            <td style="text-align:center"><?php echo number_format($piezas, 2, ',', '.'); ?></td>
                            <td style="text-align:center"><?php echo number_format($pack_precio, 2, ',', '.'); ?></td>
                            <td style="text-align:center"><?php echo number_format($uds_paquete, 2, ',', '.'); ?></td>
                            <td style="text-align:center"><?php echo number_format($total_paquetes, 2, ',', '.'); ?></td>
                            <td style="text-align:center"><?php echo number_format($precio_unidad, 2, ',', '.'); ?></td>
                            <td style="text-align:center"><?php echo number_format($precio_referencia, 2, ',', '.'); ?></td>
                        </tr>
                        <?php
                    }
                    $coste_total_componente = $coste_total_componente + $precio_componente;
                ?>
                </table>
            </div>
        </div>
    </div>
    <div class="ContenedorCamposCreacionBasico">
        <div class="LabelCreacionBasico">Coste <?php echo $nombre_componente; ?></div>
        <div class="tituloComponente">
            <table id="tablaTituloPrototipo">
                <tr>
                    <td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;"><span class="tituloComp"><?php echo number_format($precio_componente, 2, ',', '.').'€';?></span></td>
                </tr>
            </table>
        </div>
    </div>
<?php
    // Calculamos el siguiente id_tipo_componente para asignar el coste total del componente principal PERIFERICO o KIT LIBRE
    if($i+1 <= count($ids_produccion_componente)){
        $id_tipo_siguiente = $orden_produccion->dameIdTipoPorIdProduccionComponente($ids_produccion_componente[$i+1]["id_produccion_componente"]);
        $id_tipo_siguiente = $id_tipo_siguiente[0]["id_tipo_componente"];

        $siguiente_es_periferico = $id_tipo_siguiente == 2;
        $siguiente_es_kit_libre = $id_tipo_siguiente == 6;
        $no_hay_mas_componentes = $id_tipo_siguiente === NULL;
        $siguiente_es_principal = $siguiente_es_periferico || $siguiente_es_kit_libre || $no_hay_mas_componentes;

        if($siguiente_es_principal){
            $coste_producto = $coste_producto + $coste_total_componente; ?>

            <div class="ContenedorCamposCreacionBasico">
                <div class="LabelCreacionBasico">Coste Total <?php echo $nombre_componente_principal;?></div>
                <div class="tituloComponente">
                    <table id="tablaTituloPrototipo">
                        <tr>
                            <td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;"><span class="tituloComp"><?php echo number_format($coste_total_componente, 2, ',', '.').'€';?></span></td>
                        </tr>
                    </table>
                </div>
            </div>
            <br/>
    <?php
        }
        else array_shift($res_kits);
    }
}
?>