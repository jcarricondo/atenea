<?php
// Obtenemos todos los periféricos existentes
$listado_per->prepararConsulta();
$listado_per->realizarConsulta();
$resultado_todos_perifericos = $listado_per->perifericos;
foreach($resultado_todos_perifericos as $res_periferico) $todos_perifericos[] = intval($res_periferico["id_componente"]);

// Obtenemos sólo los periféricos de producción
$listado_per->prepararConsultaProduccion();
$listado_per->realizarConsulta();
$resultado_perifericos = $listado_per->perifericos;
$res_perifericos_produccion = array_column($resultado_perifericos, "id_componente");
foreach($res_perifericos_produccion as $periferico_produccion) $perifericos_produccion[] = intval($periferico_produccion);
?>

<div class="ContenedorCamposCreacionBasico">
    <div class="LabelCreacionBasico">Perif&eacute;ricos</div>
    <div class="CapaBuscadorDinamicoComponentes">
        <div id="CapaBotonPerifericos">
            <input type="button" id="BotonTodosPerifericos" name="BotonTodosPerifericos" class="BotonTodosComponentes" value="Mostrar todos los periféricos" onclick="MostrarTodosPerifericos()"/>
        </div>
        <label class="LabelBuscadorComponente">Buscar</label>
        <input type="text"
               id="BuscadorPerNewPlantilla"
               name="BuscadorPerNewPlantilla"
               class="BuscadorComponente"
               onkeyup="BuscadorDinamicoComponentes('produccion','BuscadorPerNewPlantilla','perifericos_no_asignados[]');"
               placeholder="Buscar perif&eacute;rico..." />
    </div>
</div>
<div class="ContenedorCamposCreacionBasico">
    <div class="LabelCreacionBasico"></div>
    <div class="contenedorComponentes">
        <table style="width:700px; height:208px; border:1px solid #fff;">
        <tr>
            <td id="listas_no_asignados" style="width:250px; border:1px solid #fff; padding-left:10px;">
                <select multiple="multiple" id="perifericos_no_asignados[]" name="perifericos_no_asignados[]" class="SelectMultiplePerOrigen">
                <?php
                    for($i=0;$i<count($todos_perifericos);$i++) {
                        $id_periferico = $todos_perifericos[$i];
                        $per->cargaDatosPerifericoId($id_periferico);
                        if(in_array($id_periferico,$perifericos_produccion)) {
                            $id_option = "pre-per-".$id_periferico."-option";
                            $display = "display: block;";
                        }
                        else {
                            $id_option = "";
                            $display = "display: none;";
                        }
                        echo '<option id="'.$id_option.'" style="'.$display.'" value="'.$per->id_componente.'">'.$per->periferico.'_v'.$per->version.'</option>';
                    }
                ?>
                </select>
            </td>
            <td style="border:1px solid #fff; vertical-align:middle">
                <table style="width:100%; border:1px solid #fff;">
                <tr>
                    <td style="border:1px solid #fff;"><input type="button" id="añadirPeriferico" name="añadirPeriferico" class="BotonEliminar" onclick="AddToSecondList()" value="AÑADIR" /></td>
                </tr>
                <tr>
                    <td style="border:1px solid #fff;"></td>
                </tr>
                <tr>
                    <td style="border:1px solid #fff;"><input type="button" id="quitarPeriferico" name="quitarPeriferico" class="BotonEliminar" onclick="DeleteSecondListItem()" value="QUITAR" /></td>
                </tr>
                </table>
            </td>
            <td id="lista_perº" style="width:250px; border:1px solid #fff;"><select multiple="multiple" id="perifericos[]" name="perifericos[]" class="SelectMultiplePerDestino"></select></td>
        </tr>
        </table>
    </div>
</div>
<br/>
<br/>
<br/>
