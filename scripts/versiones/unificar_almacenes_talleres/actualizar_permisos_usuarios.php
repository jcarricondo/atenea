<?php 
set_time_limit(10000);
// En este script se añade los permisos de almacen para los usuarios de mantenimiento
$dir_raiz = $_SERVER["DOCUMENT_ROOT"]."/atenea";
$dir_host = $_SERVER["SERVER_NAME"];
$dir_classes = $dir_raiz."/classes";

include($dir_classes."/mysql.class.php");
include($dir_classes."/basicos/usuario.class.php");

$db = new MySql();
$user = new Usuario();

// Obtenemos todos los usuarios de la BBDD
$res_usuarios = $user->dameUsuariosActivos();
$num_usuarios = count($res_usuarios);

for($i=0;$i<$num_usuarios;$i++) {
    $id_usuario = $res_usuarios[$i]["id_usuario"];
    $usuario = $res_usuarios[$i]["usuario"];
    $id_tipo = $res_usuarios[$i]["id_tipo"];

    $esAdminMan = $id_tipo == 4;
    $esUserMan = $id_tipo == 7;
    $esMan = $esAdminMan || $esUserMan;

    // Establecemos los permisos en función del tipo de usuario
    if($esAdminMan){
        $array_permisos = array(21,22,23,24);
    }
    else if($esUserMan){
        $array_permisos = array(21,22,23);
    }

    // Si es de tipo MANTENIMIENTO añadimos los permisos
    if($esMan){
        for($j=0;$j<count($array_permisos);$j++){
            $id_permiso = $array_permisos[$j];

            $insertSql = sprintf("insert into usuarios_permisos (id_usuario,id_permiso,tipo) values (%s,%s,1)",
                        $db->makeValue($id_usuario, "int"),
                        $db->makeValue($id_permiso, "int"));
            $db->setConsulta($insertSql);
            if($db->ejecutarSoloConsulta()){
                echo "Se ha guardado el permiso [".$id_permiso."] para el usuario ".$usuario." <br/>";
            }
            else {
                echo "Se ha producido un error al guardar el permiso [".$id_permiso."] para el usuario ".$usuario." <br/>";
            }
        }
    }
}
?>

