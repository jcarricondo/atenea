<?php 
set_time_limit(10000);
// Este script convierte algunos campos nuevos en la añadidos a la tabla "ralleres_albaranes_referencias"

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

$consulta = "select * from almacenes_albaranes_referencias";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res_almacenes_referencias = $db->getResultados();

for($i=0;$i<count($res_almacenes_referencias);$i++) {
    // Tenemos que ajustar los campos nuevos:
    // METODO
    // ID_USUARIO
    // ID_ALMACEN
    // FECHA_CREADO

    $id = $res_almacenes_referencias[$i]["id"];
    $id_albaran = $res_almacenes_referencias[$i]["id_albaran"];
    $id_referencia = $res_almacenes_referencias[$i]["id_referencia"];

    $consultaAlbaran = sprintf("select tipo_albaran,id_usuario,id_almacen,fecha_creado from almacenes_albaranes where id_albaran=%s",
                            $db->makeValue($id_albaran, "int"));
    $db->setConsulta($consultaAlbaran);
    $db->ejecutarConsulta();
    $res_albaran = $db->getPrimerResultado();

    $tipo_albaran = $res_albaran["tipo_albaran"];
    $id_usuario = $res_albaran["id_usuario"];
    $id_almacen = $res_albaran["id_almacen"];
    $fecha_creado = $res_albaran["fecha_creado"];

    if($tipo_albaran == "ENTRADA") $metodo = "RECEPCIONAR";
    else $metodo = "DESRECEPCIONAR";

    // Actualizamos la tabla almacenes_albaranes_referencias
    $updateSql = sprintf("update almacenes_albaranes_referencias set metodo=%s, id_usuario=%s, id_almacen=%s, fecha_creado=%s where id=%s",
        $db->makeValue($metodo, "text"),
        $db->makeValue($id_usuario, "int"),
        $db->makeValue($id_almacen, "int"),
        $db->makeValue($fecha_creado, "date"),
        $db->makeValue($id, "int"));
    $db->setConsulta($updateSql);
    if($db->ejecutarSoloConsulta()){
        echo "---------------------------------------"; echo "<br/>";
        echo "ACTUALIZACIÓN ALBARAN [".$id_albaran."]"; echo "<br/>";
        echo "ID_REFERENCIA [".$id_referencia."]"; echo "<br/>";
        echo "---------------------------------------"; echo "<br/>";
        echo "METODO: ".$metodo; echo "<br/>";
        echo "ID_USER:".$id_usuario; echo "<br/>";
        echo "ID_ALMACEN: ".$id_almacen; echo "<br/>";
        echo "FECHA: ".$fecha_creado; echo "<br/>";
        echo "<br/>";
    }
    else {
        echo "Se produjo un error al actualizar los campos nuevos de la referencia [".$id_referencia."] del albaran [".$id_albaran."] de la tabla almacenes_albaranes_referencias <br/>";
    }
}

?>

