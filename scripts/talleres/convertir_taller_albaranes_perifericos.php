<?php 
set_time_limit(10000);
// Este script convierte los almacenes de la tabla albaranes_perifericos en los talleres actuales
include("../../classes/mysql.class.php");
include("../../classes/kint/Kint.class.php");
$db = new MySQL();

$consultaAlbaranes = "select * from albaranes_perifericos";
$db->setConsulta($consultaAlbaranes);
$db->ejecutarConsulta();
$res_albaranes = $db->getResultados();

for($i=0;$i<count($res_albaranes);$i++) {
    $id_albaran = $res_albaranes[$i]["id_albaran"];
    $id_taller = $res_albaranes[$i]["id_taller"];

    switch ($id_taller) {
        case "1";
            $id_taller_new = 3;
            break;
        case "2":
            $id_taller_new = 4;
            break;
        case "3":
            $id_taller_new = 0;
            break;
        case "4":
            $id_taller_new = 0;
            break;
        case "5":
            $id_taller_new = 8;
            break;
        case "6":
            $id_taller_new = 1;
            break;
        case "7":
            $id_taller_new = 5;
            break;
        case "8":
            $id_taller_new = 6;
            break;
        case "9":
            $id_taller_new = 7;
            break;
    }

    // Actualizamos el taller
    $updateSql = sprintf("update albaranes_perifericos set id_taller=%s where id_albaran=%s",
                    $db->makeValue($id_taller_new, "int"),
                    $db->makeValue($id_albaran, "int"));
    $db->setConsulta($updateSql);
    if($db->ejecutarSoloConsulta()){
        echo "Se ha actualizado correctamente el taller [".$id_taller."] a [".$id_taller_new."] del albaran [".$id_albaran."]</br>";
    }
    else {
        echo "Se produjo un error al actualizar el taller del albaran [".$id_albaran."] <br/>";
    }
}


?>

