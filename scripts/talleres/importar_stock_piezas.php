<?php 
set_time_limit(10000);
// En este script se importan todos los stock de los almacenes en stock_talleres
include("../../classes/mysql.class.php");
include("../../classes/taller/taller.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySql();
$taller = new Taller();

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
            $id_taller = 3;
            break;
        case "2":
            $id_taller = 4;
            break;
        case "3":
            $id_taller = 0;
            break;
        case "4":
            $id_taller = 0;
            break;
        case "5":
            $id_taller = 8;
            break;
        case "6":
            $id_taller = 1;
            break;
        case "7":
            $id_taller = 5;
            break;
        case "8":
            $id_taller = 6;
            break;
        case "9":
            $id_taller = 7;
            break;
    }

    $taller->cargaDatosTallerId($id_taller);
    $nombre_taller = $taller->nombre;

    // Guardamos las piezas de stock en su correspondiente taller
    $insertStock = sprintf("insert into stock_talleres (id_referencia,piezas,id_taller) values(%s,%s,%s)",
                        $db->makeValue($id_referencia,"int"),
                        $db->makeValue($piezas,"float"),
                        $db->makeValue($id_taller,"int"));
    $db->setConsulta($insertStock);

    if($db->ejecutarSoloConsulta()) {
        echo "Se han guardado correctamente [".$piezas."] piezas de la referencia ID_REF[".$id_referencia."] en el taller [".$nombre_taller."]"; echo "</br>";
    }
    else {
        echo "Se ha producido un error al guardar las piezas de almacen en el ID[".$id."]";
    }
}


?>

