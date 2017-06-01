<?php
// Fichero con las funciones para AJAX de referencias
include("../../classes/mysql.class.php");
include("../../classes/basicos/componente.class.php");
include("../../classes/basicos/referencia.class.php");
include("../../classes/basicos/referencia_heredada.class.php");
include("../../classes/basicos/referencia_compatible.class.php");

$db = new MySQL();
$comp = new Componente();
$ref = new Referencia();
$ref_heredada = new Referencia_Heredada();
$ref_compatible = new Referencia_Compatible();

$json = array();
if(isset($_GET["comp"])){
	switch($_GET["comp"]) {
        case "calcularCosteReferenciasHeredadas":
            $aux_array_ids = $_GET["ids"];
            $aux_array_piezas = $_GET["piezas"];
            $array_ids = explode(",",$aux_array_ids);
            $array_piezas = explode(",",$aux_array_piezas);

            for($i=0;$i<count($array_ids);$i++){
                $referencias_componente_final[$i]["id_referencia"] = intval($array_ids[$i]);
                $referencias_componente_final[$i]["piezas"] = floatval($array_piezas[$i]);
            }

            $referencias_componente_final_aux = $referencias_componente_final;

            // Comprobamos si las referencias tienen heredadas y multiplicamos sus piezas
            for($i=0;$i<count($referencias_componente_final);$i++){
                $raiz = $referencias_componente_final[$i]["id_referencia"];
                $piezas = $referencias_componente_final[$i]["piezas"];

                // Obtenemos el grafo ordenado por BFS (Anchura) y después todas las piezas necesarias de cada referencia
                $heredadas_por_nivel = $ref_heredada->dameTodasHeredadasNivel($raiz);
                $referencias_heredadas_referencia = $ref_heredada->dameTodasHeredadasPiezas($heredadas_por_nivel);

                // Si tiene heredadas las agrupamos al array de referencias final con sus piezas correspondientes
                if(!empty($referencias_heredadas_referencia)){
                    $cont = 0;
                    foreach($referencias_heredadas_referencia as $id_ref_heredada => $piezas_heredada){
                        $array_piezas_heredadas[$cont]["id_referencia"] = $id_ref_heredada;
                        $array_piezas_heredadas[$cont]["piezas"] = $piezas * $piezas_heredada;
                        $cont++;
                    }

                    // Agrupamos las referencias heredadas al array final
                    $referencias_componente_final_aux = $comp->agruparReferenciasComponentes($array_piezas_heredadas,$referencias_componente_final_aux);
                    unset($array_piezas_heredadas);
                }
            }

            $precio_total_ref = 0;
            $referencias_componente_final = $referencias_componente_final_aux;

            // Por cada referencia obtenemos su precio unitario
            for($i=0;$i<count($referencias_componente_final);$i++){
                $id_referencia = $referencias_componente_final[$i]["id_referencia"];
                $piezas = $referencias_componente_final[$i]["piezas"];
                $precio_total_ref = 0;

                $ref->cargaDatosReferenciaId($id_referencia);
                $pack_precio = floatval($ref->pack_precio);
                $unidades_paquete = intval($ref->unidades);

                if(empty($pack_precio) || empty($unidades_paquete)){
                    $precio_unidad_ref = 0;
                    $precio_total_ref = 0;
                }
                else{
                    $precio_unidad_ref = $pack_precio / $unidades_paquete;
                    $precio_total_ref = $piezas * $precio_unidad_ref;
                }
                $precio_total = $precio_total + $precio_total_ref;
            }

            $json = $precio_total;
            echo json_encode($json, JSON_FORCE_OBJECT);
        break;
        case "tieneHeredadas":
            $id_referencia = $_GET["id"];
            $res_heredadas = $ref_heredada->dameHeredadasPrincipales($id_referencia);
            $tiene_heredadas = !empty($res_heredadas);

            $json = $tiene_heredadas;
            echo json_encode($json, JSON_FORCE_OBJECT);
        break;
        default:

        break;
	}
}
?>

