<?php 
set_time_limit(10000);
// Este script convierte los almacenes de la tabla material_informatico en los almacenes actuales
$dir_raiz = $_SERVER["DOCUMENT_ROOT"]."/atenea";
$dir_host = $_SERVER["SERVER_NAME"];
$dir_classes = $dir_raiz."/classes";

include($dir_classes."/mysql.class.php");
include($dir_classes."/kint/Kint.class.php");

$db = new MySQL();

// Actualizamos el almacen
$updateSql = "update material_informatico set id_almacen=1";
$db->setConsulta($updateSql);
if($db->ejecutarSoloConsulta()){
    echo "Se ha actualizado correctamente al almacen [1] de todos los materiales";
}
else {
    echo "Se produjo un error al actualizar el almacen de los materiales";
}


?>

