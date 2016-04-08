<?php 
set_time_limit(10000);
// En este script se importan los albaranes de piezas en los almacenes
$dir_raiz = $_SERVER["DOCUMENT_ROOT"]."/atenea";
$dir_host = $_SERVER["SERVER_NAME"];
$dir_classes = $dir_raiz."/classes";

include($dir_classes."/mysql.class.php");
include($dir_classes."/kint/Kint.class.php");

$db = new MySql();

// Obtenemos todos los albaranes de piezas
$consultaAlbaranes = "select * from albaranes_piezas";
$db->setConsulta($consultaAlbaranes);
$db->ejecutarConsulta();
$res_albaranes = $db->getResultados();

for($i=0;$i<count($res_albaranes);$i++) {

    $id_albaran = $res_albaranes[$i]["id_albaran"];
    $nombre_albaran = $res_albaranes[$i]["nombre_albaran"];
    $tipo_albaran = $res_albaranes[$i]["tipo_albaran"];
    $id_participante = $res_albaranes[$i]["id_participante"];
    $id_tipo_participante = $res_albaranes[$i]["id_tipo_participante"];
    $motivo = $res_albaranes[$i]["motivo"];
    $id_usuario = $res_albaranes[$i]["id_usuario"];
    $id_almacen = $res_albaranes[$i]["id_almacen"];
    $fecha_creado = $res_albaranes[$i]["fecha_creado"];
    $activo = $res_albaranes[$i]["activo"];

    switch ($id_almacen) {
        case "1";
            $id_almacen = 3;
            break;
        case "2":
            $id_almacen = 4;
            break;
        case "3":
            $id_almacen = 0;
            break;
        case "4":
            $id_almacen = 0;
            break;
        case "5":
            $id_almacen = 8;
            break;
        case "6":
            $id_almacen = 1;
            break;
        case "7":
            $id_almacen = 5;
            break;
        case "8":
            $id_almacen = 6;
            break;
        case "9":
            $id_almacen = 7;
            break;
    }

    // Guardamos los albaranes y sus referencias
    $insertAlbaranes = sprintf("insert into almacenes_albaranes (nombre_albaran,tipo_albaran,id_participante,id_tipo_participante,
                                  motivo,id_usuario,id_almacen,fecha_creado,activo) values(%s,%s,%s,%s,%s,%s,%s,%s,%s)",
                        $db->makeValue($nombre_albaran,"text"),
                        $db->makeValue($tipo_albaran,"text"),
                        $db->makeValue($id_participante,"int"),
                        $db->makeValue($id_tipo_participante,"int"),
                        $db->makeValue($motivo, "text"),
                        $db->makeValue($id_usuario,"int"),
                        $db->makeValue($id_almacen,"int"),
                        $db->makeValue($fecha_creado,"date"),
                        $db->makeValue($activo,"int"));
    $db->setConsulta($insertAlbaranes);

    if($db->ejecutarSoloConsulta()){
        echo "Se ha guardado correctamente el albaran [".$id_albaran."]_".$nombre_albaran."</br>";

        $id_albaran_new = $db->getUltimoID();

        // Obtenemos las referencia del albaran
        $consultaReferencias = sprintf("select * from albaranes_piezas_referencias where id_albaran=%s",
                                    $db->makeValue($id_albaran,"int"));
        $db->setConsulta($consultaReferencias);
        $db->ejecutarConsulta();
        $res_referencias = $db->getResultados();

        for($j=0;$j<count($res_referencias);$j++){
            $id_referencia = $res_referencias[$j]["id_referencia"];
            $nombre_referencia = $res_referencias[$j]["nombre_referencia"];
            $nombre_proveedor = $res_referencias[$j]["nombre_proveedor"];
            $referencia_proveedor = $res_referencias[$j]["referencia_proveedor"];
            $nombre_pieza = $res_referencias[$j]["nombre_pieza"];
            $pack_precio = $res_referencias[$j]["pack_precio"];
            $unidades_paquete = $res_referencias[$j]["unidades_paquete"];
            $cantidad = $res_referencias[$j]["cantidad"];
            $activo_ref = $res_referencias[$j]["activo"];

            $insertReferencias = sprintf("insert into almacenes_albaranes_referencias (id_albaran,id_referencia,nombre_referencia,nombre_proveedor,
                                            referencia_proveedor,nombre_pieza,pack_precio,unidades_paquete,cantidad,activo)
                                              values(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
                $db->makeValue($id_albaran_new,"int"),
                $db->makeValue($id_referencia,"int"),
                $db->makeValue($nombre_referencia,"text"),
                $db->makeValue($nombre_proveedor,"text"),
                $db->makeValue($referencia_proveedor,"text"),
                $db->makeValue($nombre_pieza,"text"),
                $db->makeValue($pack_precio,"float"),
                $db->makeValue($unidades_paquete,"int"),
                $db->makeValue($cantidad,"float"),
                $db->makeValue($activo_ref,"int"));
            $db->setConsulta($insertReferencias);
            if($db->ejecutarSoloConsulta()){
                // OK
                echo "Se ha guardado correctamente la referencia [".$id_referencia."] del albaran [".$id_albaran."]_".$nombre_albaran."</br>";
            }
            else {
                echo "Se produjo un error al insertar las referencias del albaran de piezas [".$id_albaran."] <br/>";
            }
        }

        // Obtenemos los movimientos del albaran
        $consultaMovimientos = sprintf("select * from albaranes_piezas_log where id_albaran=%s",
            $db->makeValue($id_albaran,"int"));
        $db->setConsulta($consultaMovimientos);
        $db->ejecutarConsulta();
        $res_movimientos = $db->getResultados();

        // Guardamos los movimientos
        for($j=0;$j<count($res_movimientos);$j++){
            $id_referencia = $res_movimientos[$j]["id_referencia"];
            $piezas = $res_movimientos[$j]["piezas"];
            $metodo = $res_movimientos[$j]["metodo"];
            $fecha_creado_mov = $res_movimientos[$j]["fecha_creado"];
            $activo_mov = $res_movimientos[$j]["activo"];

            $insertMovimientos = sprintf("insert into almacenes_albaranes_log (id_albaran,id_referencia,id_produccion,piezas,metodo,fecha_creado,activo)
                                              values(%s,%s,0,%s,%s,%s,%s)",
                                        $db->makeValue($id_albaran_new,"int"),
                                        $db->makeValue($id_referencia,"int"),
                                        $db->makeValue($piezas,"float"),
                                        $db->makeValue($metodo,"text"),
                                        $db->makeValue($fecha_creado_mov,"date"),
                                        $db->makeValue($activo_mov,"int"));
            $db->setConsulta($insertMovimientos);
            if($db->ejecutarSoloConsulta()){
                // OK
                echo "Se ha guardado correctamente el movimiento con ID_REF: [".$id_referencia."] PIEZAS: [".$piezas."] del albaran [".$id_albaran."]_".$nombre_albaran."</br>";
            }
            else {
                echo "Se produjo un error al insertar los movimientos del albaran de piezas [".$id_albaran."] <br/>";
            }
        }
    }
    else {
        echo "Se produjo un error al insertar el albaran de piezas [".$id_albaran."] <br/>";
    }
}


?>

