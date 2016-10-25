<?php 
set_time_limit(10000);
// Este script convierte los almacenes de la tabla perifericos en los almacenes actuales
$dir_raiz = $_SERVER["DOCUMENT_ROOT"]."/atenea";
$dir_host = $_SERVER["SERVER_NAME"];
$dir_classes = $dir_raiz."/classes";

include($dir_classes."/mysql.class.php");
include($dir_classes."/kint/Kint.class.php");

$db = new MySQL();

$consultaPerifericos = "select * from perifericos";
$db->setConsulta($consultaPerifericos);
$db->ejecutarConsulta();
$res_perifericos = $db->getResultados();

for($i=0;$i<count($res_perifericos);$i++) {
    $id_periferico = $res_perifericos[$i]["id_periferico"];
    $id_almacen = $res_perifericos[$i]["id_almacen"];

    switch ($id_almacen) {
        case "1";
            $id_almacen_new = 3;
            break;
        case "2":
            $id_almacen_new = 4;
            break;
        case "3":
            $id_almacen_new = 0;
            break;
        case "4":
            $id_almacen_new = 1;
            break;
        case "5":
            $id_almacen_new = 8;
            break;
        case "6":
            $id_almacen_new = 1;
            break;
        case "7":
            $id_almacen_new = 5;
            break;
        case "8":
            $id_almacen_new = 6;
            break;
        case "9":
            $id_almacen_new = 7;
            break;
    }

    // Actualizamos el almacen
    $updateSql = sprintf("update perifericos set id_almacen=%s where id_periferico=%s",
                    $db->makeValue($id_almacen_new, "int"),
                    $db->makeValue($id_periferico, "int"));
    $db->setConsulta($updateSql);
    if($db->ejecutarSoloConsulta()){
        echo "Se ha actualizado correctamente el almacen [".$id_almacen."] a [".$id_almacen_new."] del periferico [".$id_periferico."]</br>";
    }
    else {
        echo "Se produjo un error al actualizar el almacen del periferico [".$id_periferico."] <br/>";
    }
}


?>

