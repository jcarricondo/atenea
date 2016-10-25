<?php 
set_time_limit(10000);
// En este script se importan todos los stock de los almacenes en stock_almacenes
$dir_raiz = $_SERVER["DOCUMENT_ROOT"]."/atenea";
$dir_host = $_SERVER["SERVER_NAME"];
$dir_classes = $dir_raiz."/classes";

include($dir_classes."/mysql.class.php");
include($dir_classes."/almacen/almacen.class.php");
include($dir_classes."/kint/Kint.class.php");

$db = new MySql();
$almacen = new Almacen();

// Obtenemos todos los stock de piezas de los almacenes
$consultaStock = "select * from stock";
$db->setConsulta($consultaStock);
$db->ejecutarConsulta();
$res_stock = $db->getResultados();

for($i=0;$i<count($res_stock);$i++) {
    // Obtenemos el stock de los almacenes
    $id = $res_stock[$i]["id"];
    $id_referencia = $res_stock[$i]["id_referencia"];
    $piezas = $res_stock[$i]["piezas"];
    $id_almacen = $res_stock[$i]["id_almacen"];

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

    $almacen->cargaDatosAlmacenId($id_almacen);
    $nombre_almacen = $almacen->nombre;

    // Guardamos las piezas de stock en su correspondiente almacen
    $insertStock = sprintf("insert into stock_almacenes (id_referencia,piezas,id_almacen) values(%s,%s,%s)",
                        $db->makeValue($id_referencia,"int"),
                        $db->makeValue($piezas,"float"),
                        $db->makeValue($id_almacen,"int"));
    $db->setConsulta($insertStock);

    if($db->ejecutarSoloConsulta()) {
        echo "Se han guardado correctamente [".$piezas."] piezas de la referencia ID_REF[".$id_referencia."] en el almacen [".$nombre_almacen."]"; echo "</br>";
    }
    else {
        echo "Se ha producido un error al guardar las piezas de almacen en el ID[".$id."]";
    }
}


?>

