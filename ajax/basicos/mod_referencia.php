<?php
// Fichero con las funciones para AJAX de la modificación de referencias
include("../../classes/mysql.class.php");
include("../../classes/basicos/referencia.class.php");
include("../../classes/basicos/referencia_heredada.class.php");

$db = new MySQL();
$ref_heredada = new Referencia_Heredada();

$json = array();
if(isset($_GET["comp"])){
	switch($_GET["comp"]) {
        case "removeReferenciasHeredadas":
            $id_ref = $_GET["id_ref"];
            $id_ref_principal = $_GET["id_ref_principal"];
            $aux_rhp = $_GET["rhp"];
            $aux_rht = $_GET["rht"];
            $rhp = explode(",",$aux_rhp);
            $rht = explode(",",$aux_rht);

            $res_heredadas_principales = $rhp;

            // Guardamos en un nuevo array las referencias heredadas principales sin contar la que se ha eliminado
            for($i=0;$i<count($res_heredadas_principales);$i++){
                if($id_ref != $res_heredadas_principales[$i]) $new_res_heredadas_principales[] = $res_heredadas_principales[$i];
            }

            // Una vez obtenidos las nuevas referencias heredadas principales, obtenemos las demas subheredadas
            for($i=0;$i<count($new_res_heredadas_principales);$i++){
                $id_ref_heredada_principal = $new_res_heredadas_principales[$i];
                $new_todas_heredadas[] = $id_ref_heredada_principal;

                // Obtenemos todos las demas referencias heredadas de cada heredada principal
                $aux_heredadas_principal = $ref_heredada->dameTodasHeredadas($id_ref_heredada_principal);

                if($aux_heredadas_principal != NULL) {
                    for ($j=0;$j<count($aux_heredadas_principal);$j++) $res_heredadas_principal_heredada[] = $aux_heredadas_principal[$j]["id_ref_heredada"];
                    // Agrupamos todas las referencias heredadas, eliminamos los duplicados y las ordenamos
                    $new_todas_heredadas = array_merge($new_todas_heredadas,$res_heredadas_principal_heredada);
                }
                $new_todas_heredadas = array_unique($new_todas_heredadas);
                sort($new_todas_heredadas,SORT_NUMERIC);
            }

            $array_heredadas = array("ref_heredadas_principales" => $new_res_heredadas_principales,
                                       "ref_heredadas_totales" => $new_todas_heredadas);

            $json = $array_heredadas;
            echo json_encode($json, JSON_FORCE_OBJECT);
        break;
        case "addReferenciasHeredadas":
            $id_ref = $_GET["id_ref"];
            $aux_rht = $_GET["rht"];
            if(!empty($aux_rht)) $rht = explode(",",$aux_rht);

            // Guardamos en el propio array de referencias herederas totales la referencia que se va a añadir
            $rht[] = $id_ref;

            // Obtenemos todas las referencias heredadas de la referencia añadida
            $res_heredadas_totales = $ref_heredada->dameTodasHeredadas($id_ref);
            if($res_heredadas_totales != NULL) {
                $res_heredadas_totales = array_column($res_heredadas_totales,'id_ref_heredada');
                $rht = array_merge($rht,$res_heredadas_totales);
            }

            $rht = array_unique($rht);
            sort($rht,SORT_NUMERIC);

            $json = $rht;
            echo json_encode($json, JSON_FORCE_OBJECT);
        break;
        default:

        break;
	}
}
?>

