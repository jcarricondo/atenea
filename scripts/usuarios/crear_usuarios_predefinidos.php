<?php 
set_time_limit(10000);
// Script parar crear los diferentes tipos de usuario para TEST
$dir_raiz = $_SERVER["DOCUMENT_ROOT"]."/atenea";
$dir_host = $_SERVER["SERVER_NAME"];
$dir_classes = $dir_raiz."/classes";

include($dir_classes."/mysql.class.php");
include($dir_classes."/basicos/usuario.class.php");
include($dir_classes."/kint/Kint.class.php");

$db = new MySQL();
$user = new Usuario();

/*
 USUARIOS QUE SE CREARAN PARA TEST:
[tipo_user][almacen]
[1][0]ADM_SMK:                    ADMINISTRADOR SIMUMAK
[2][0]ADM_DIS:                    ADMINISTRADOR DISEÑO
[3][1]ADM_FAB_SMK:                ADMINISTRADOR FABRICA CON SEDE SIMUMAK Y ALMACEN SMK-ESPAÑA
[3][2]ADM_FAB_TORO:               ADMINISTRADOR FABRICA CON SEDE TORO Y ALMACEN TORO
[4][3]ADM_MAN_SAO:                ADMINISTRADOR MANTENIMIENTO CON SEDE BRASIL Y ALMACEN SAO_PAOLO
[4][4]ADM_MAN_STA:                ADMINISTRADOR MANTENIMIENTO CON SEDE BRASIL Y ALMACEN SANTA MARIA
[4][5]ADM_MAN_SUM:                ADMINISTRADOR MANTENIMIENTO CON SEDE BRASIL Y ALMACEN SUMARE
[4][6]ADM_MAN_EXP:                ADMINISTRADOR MANTENIMIENTO CON SEDE BRASIL Y ALMACEN EXPLORAÇAO
[4][7]ADM_MAN_ALFA:               ADMINISTRADOR MANTENIMIENTO CON SEDE BRASIL Y ALMACEN ALFANDEGA
[4][8]ADM_MAN_NAN:                ADMINISTRADOR MANTENIMIENTO CON SEDE FRANCIA Y ALMACEN NANTES
[4][1]ADM_MAN_MAD:                ADMINISTRADOR MANTENIMIENTO CON SEDE SIMUMAK Y ALMACEN SMK-ESPAÑA
[8][0]ADM_GES:                    ADMINISTRADOR GESTION
[5][0]USR_DIS:                    USUARIO DISEÑO
[6][1]USR_FAB_SMK:                USUARIO FABRICA CON SEDE SIMUMAK Y ALMACEN SMK-ESPAÑA
[6][2]USR_FAB_TORO:               USUARIO FABRICA CON SEDE TORO Y ALMACEN TORO
[7][3]USR_MAN_SAO:                USUARIO MANTENIMIENTO CON SEDE BRASIL Y ALMACEN SAO PAOLO
[7][4]USR_MAN_STA:                USUARIO MANTENIMIENTO CON SEDE BRASIL Y ALMACEN SANTA MARIA
[7][5]USR_MAN_SUM:                USUARIO MANTENIMIENTO CON SEDE BRASIL Y ALMACEN SUMARE
[7][6]USR_MAN_EXP:                USUARIO MANTENIMIENTO CON SEDE BRASIL Y ALMACEN EXPLORAÇAO
[7][7]USR_MAN_ALFA:               USUARIO MANTENIMIENTO CON SEDE BRASIL Y ALMACEN ALFANDEGA
[7][8]USR_MAN_NAN:                USUARIO MANTENIMIENTO CON SEDE FRANCIA Y ALMACEN NANTES
[7][1]USR_MAN_MAD:                USUARIO MANTENIMIENTO CON SEDE SIMUMAK Y ALMACEN SMK-ESPAÑA
[9][0]USR_GES:                    USUARIO GESTION
*/

echo '<br/>CREACION DE USUARIOS<br/>';

$array_tipo_usuario = array(1,2,3,3,4,4,4,4,4,4,4,8,5,6,6,7,7,7,7,7,7,7,9);
    $array_almacenes = array(0,0,1,2,3,4,5,6,7,8,1,0,0,1,2,3,4,5,6,7,8,1,0);
              $pass = "1234";

// Generamos los diferentes campos en función del tipo de usuario
for($i=0;$i<count($array_tipo_usuario);$i++){
    $id_tipo = $array_tipo_usuario[$i];
    $id_almacen = $array_almacenes[$i];

    switch ($id_almacen){
        case 1:
            $abr_almacen = "SMK";
            break;
        case 2:
            $abr_almacen = "TORO";
            break;
        case 3:
            $abr_almacen = "SAO";
            break;
        case 4:
            $abr_almacen = "STA";
            break;
        case 5:
            $abr_almacen = "SUM";
            break;
        case 6:
            $abr_almacen = "EXP";
            break;
        case 7:
            $abr_almacen = "ALFA";
            break;
        case 8:
            $abr_almacen = "NAN";
            break;
        default:

            break;
    }

    $abr_almacen_email = strtolower($abr_almacen);
    $dominio = "@atenea.es";

    switch($id_tipo){
        case 1:
            $nombre = "ADM_SMK";
            $email = "admsmk".$dominio;
            break;
        case 2:
            $nombre = "ADM_DIS";
            $email = "admdis".$dominio;
            break;
        case 3:
            $nombre = "ADM_FAB_".$abr_almacen;
            $email = "admfab".$abr_almacen_email.$dominio;
            break;
        case 4:
            $nombre = "ADM_MAN_".$abr_almacen;
            $email = "admman".$abr_almacen_email.$dominio;
            break;
        case 5:
            $nombre = "USR_DIS";
            $email = "usrdis".$dominio;
            break;
        case 6:
            $nombre = "USR_FAB_".$abr_almacen;
            $email = "usrfab".$abr_almacen_email.$dominio;
            break;
        case 7:
            $nombre = "USR_MAN_".$abr_almacen;
            $email = "usrman".$abr_almacen_email.$dominio;
            break;
        case 8:
            $nombre = "ADM_GES";
            $email = "admges".$dominio;
            break;
        case 9:
            $nombre = "USR_GES";
            $email = "usrges".$dominio;
            break;
    }

    echo "USUARIO: ".$nombre; echo "<br/>";
    echo "Email: ".$email; echo "<br/>";
    echo "Pass: ".$pass; echo "<br/>";
    echo "Tipo: [".$id_tipo."][".$nombre."]"; echo "<br/>";
    echo "Almacen: [".$id_almacen."]"; echo "<br/>";
    echo "<br/>";

    // Guardamos el usuario con sus correspondientes permisos
    $user->datosNuevoUsuario(NULL, $nombre, $pass, $pass, $email, $id_tipo, $id_almacen);
    $res = $user->guardarCambios();
    if($res == 1){
        echo "Se ha guardado el usuario [".$nombre."] con almacen [".$id_almacen."]<br/><br/>";
    }
    else {
        $i=count($array_tipo_usuario);
        echo "Se ha producido un error al guardar el usuario [".$nombre." con almacen [".$id_almacen."]<br/><br/>";
    }
}

?>

