<?php
// Obtenemos todos los kits existentes
$listado_kit->prepararConsulta();
$listado_kit->realizarConsulta();
$resultado_todos_kits = $listado_kit->kits;
foreach($resultado_todos_kits as $res_kit) $todos_kits[] = intval($res_kit["id_componente"]);

// Obtenemos sólo los kits de producción
$listado_kit->prepararConsultaProduccion();
$listado_kit->realizarConsulta();
$resultado_kits = $listado_kit->kits;
$res_kits_produccion = array_column($resultado_kits, "id_componente");
foreach($res_kits_produccion as $kit_produccion) $kits_produccion[] = intval($kit_produccion);
?>

<div class="ContenedorCamposCreacionBasico">
    <div class="LabelCreacionBasico">Kits</div>
    <div class="CapaBuscadorDinamicoComponentes">
        <div id="CapaBotonKits">
            <input type="button" id="BotonTodosKits" name="BotonTodosKits" class="BotonTodosComponentes" value="Mostrar todos los kits" onclick="MostrarTodosKits()"/>
        </div>
        <label class="LabelBuscadorComponente">Buscar</label>
        <input type="text"
               id="BuscadorKitNewPlantilla"
               name="BuscadorKitNewPlantilla"
               class="BuscadorComponente"
               onkeyup="BuscadorDinamicoComponentes('produccion','BuscadorKitNewPlantilla','kits_no_asignados[]');"
               placeholder="Buscar kit..." />
    </div>
</div>
<div class="ContenedorCamposCreacionBasico">
    <div class="LabelCreacionBasico"></div>
    <div class="contenedorComponentes">
        <table style="width:700px; height:208px; border:1px solid #fff;">
        <tr>
            <td id="listas_kits_no_asignados" style="width:250px; border:1px solid #fff; padding-left:10px;">
                <select multiple="multiple" id="kits_no_asignados[]" name="kits_no_asignados[]" class="SelectMultipleKitOrigen">
                <?php
                    for($i=0;$i<count($todos_kits);$i++) {
                        $id_kit = $todos_kits[$i];
                        $kit->cargaDatosKitId($id_kit);
                        if(in_array($id_kit,$kits_produccion)) {
                            $id_option = "pre-kit-".$id_kit."-option";
                            $display = "display: block;";
                        }
                        else {
                            $id_option = "";
                            $display = "display: none;";
                        }
                        echo '<option id="'.$id_option.'" style="'.$display.'" value="'.$kit->id_componente.'">'.$kit->kit.'_v'.$kit->version.'</option>';
                    }
                ?>
                </select>
            </td>
            <td style="border:1px solid #fff; vertical-align:middle">
                <table style="width:100%; border:1px solid #fff;">
                <tr>
                    <td style="border:1px solid #fff;"><input type="button" id="añadirKit" name="añadirKit" class="BotonEliminar" onclick="AddKitsToSecondList()" value="AÑADIR" /></td>
                </tr>
                <tr>
                    <td style="border:1px solid #fff;"></td>
                </tr>
                <tr>
                    <td style="border:1px solid #fff;"><input type="button" id="quitarKit" name="quitarKit" class="BotonEliminar" onclick="DeleteKitsSecondListItem()" value="QUITAR" /></td>
                </tr>
                </table>
            </td>
            <td id="lista" style="width:250px; border:1px solid #fff;"><select multiple="multiple" id="kits[]" name="kits[]" class="SelectMultipleKitDestino"></select></td>
        </tr>
        </table>
    </div>
</div>
<br/>
<br/>
<br/>
