<?php 
set_time_limit(10000);
// Este script convierte algunos campos nuevos añadidos a la tabla "ralleres_albaranes_log"

// LOC
// $dir_raiz = $_SERVER["DOCUMENT_ROOT"];
// DEV - PRE - PRO
$dir_raiz = $_SERVER["DOCUMENT_ROOT"]."/atenea";
$dir_host = $_SERVER["SERVER_NAME"];
$dir_classes = $dir_raiz."/classes";

include($dir_classes."/mysql.class.php");
include($dir_classes."/almacen/almacen.class.php");
// include($dir_classes."/kint/Kint.class.php");

$db = new MySQL();
$almacen = new Almacen();

$consulta = "select * from almacenes_albaranes_log";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res_almacenes_log = $db->getResultados();

for($i=0;$i<count($res_almacenes_log);$i++) {
    // Tenemos que ajustar los campos nuevos:
    // ID_USUARIO
    // ID_ALMACEN

    $id = $res_almacenes_log[$i]["id"];
    $id_albaran = $res_almacenes_log[$i]["id_albaran"];

    $consultaAlbaran = sprintf("select id_usuario,id_almacen from almacenes_albaranes where id_albaran=%s",
                            $db->makeValue($id_albaran, "int"));
    $db->setConsulta($consultaAlbaran);
    $db->ejecutarConsulta();
    $res_albaran = $db->getPrimerResultado();

    $id_usuario = $res_albaran["id_usuario"];
    $id_almacen = $res_albaran["id_almacen"];

    // Actualizamos la tabla almacenes_albaranes_log
    $updateSql = sprintf("update almacenes_albaranes_log set id_usuario=%s, id_almacen=%s where id=%s",
        $db->makeValue($id_usuario, "int"),
        $db->makeValue($id_almacen, "int"),
        $db->makeValue($id, "int"));
    $db->setConsulta($updateSql);
    if($db->ejecutarSoloConsulta()){
        echo "---------------------------------------"; echo "<br/>";
        echo "ACTUALIZACIÓN ALBARAN [".$id_albaran."]"; echo "<br/>";
        echo "ID_LOG [".$id."]"; echo "<br/>";
        echo "---------------------------------------"; echo "<br/>";
        echo "ID_USER:".$id_usuario; echo "<br/>";
        echo "ID_ALMACEN: ".$id_almacen; echo "<br/>";
        echo "<br/>";
    }
    else {
        echo "Se produjo un error al actualizar los campos nuevos del log [".$id."] del albaran [".$id_albaran."] de la tabla almacenes_albaranes_log <br/>";
    }
}

?>

