<?php
// Fichero con las funciones para AJAX de la modificación de referencias
include("../../classes/mysql.class.php");
include("../../classes/basicos/referencia.class.php");
include("../../classes/basicos/referencia_heredada.class.php");
include("../../classes/basicos/referencia_compatible.class.php");

$db = new MySQL();
$ref = new Referencia();
$ref_heredada = new Referencia_Heredada();
$ref_compatible = new Referencia_Compatible();

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
                foreach($res_heredadas_totales as $refs_heredadas) $heredadas_totales[] = intval($refs_heredadas["id_ref_heredada"]);
                //$res_heredadas_totales = array_column($res_heredadas_totales,'id_ref_heredada');
                $rht = array_merge($rht,$heredadas_totales);
            }

            $rht = array_unique($rht);
            sort($rht,SORT_NUMERIC);

            $json = $rht;
            echo json_encode($json, JSON_FORCE_OBJECT);
        break;
        case "dameBanderaMotivoCompatibilidad":
            $id_referencia = $_GET["id_referencia"];
            $id_motivo_compatibilidad = $ref->dameIdMotivoCompatibilidad($id_referencia);

            // Obtenemos el nombre de la imagen en función de su motivo de compatibilidad
            $nombre_imagen = $ref_compatible->dameNombreImagenMotivoCompatibilidad($id_motivo_compatibilidad);
            $pais_imagen = $ref_compatible->damePaisImagenMotivoCompatibilidad($id_motivo_compatibilidad);
            $ruta_imagen = "../images/banderas/".$nombre_imagen;

            $array_imagen = array("ruta_imagen" => $ruta_imagen,
                                  "pais_imagen" => $pais_imagen);

            $json = $array_imagen;
            echo json_encode($json, JSON_FORCE_OBJECT);
        break;
        case "removeReferenciasAntecesores":
            $id_ref = $_GET["id_ref"];                  // ID REF Antecesor a eliminar
            $aux_rat = $_GET["rat"];                    // IDs de las refs antecesores totales actuales de la ref principal
            $rat = explode(",",$aux_rat);               // Array con las refs antecesores totales actuales de la ref principal

            // Obtenemos todos las referencias antecesores de la referencia que vamos a eliminar
            $refs_antecesores_eliminada[] = $id_ref;
            $aux_refs_antecesores_eliminada = $ref_heredada->dameTodosAntecesores($id_ref);
            for($i=0;$i<count($aux_refs_antecesores_eliminada);$i++){
                $refs_antecesores_eliminada[] = $aux_refs_antecesores_eliminada[$i]["id_referencia"];
            }

            $new_rat = array_diff($rat,$refs_antecesores_eliminada);

            $json = $new_rat;
            echo json_encode($json, JSON_FORCE_OBJECT);
            break;
        default:

        break;
	}
}
?>

