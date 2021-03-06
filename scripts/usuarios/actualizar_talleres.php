<?php 
set_time_limit(10000);
// En este script se copia los id_almacen en el id_almacen de cada usuario
include("../../classes/mysql.class.php");
include("../../classes/basicos/usuario.class.php");

$db = new MySql();
$user = new Usuario();

// Obtenemos todos los usuarios de la BBDD
$res_usuarios = $user->dameUsuariosActivos();
$num_usuarios = count($res_usuarios);

for($i=0;$i<$num_usuarios;$i++) {
    $id_usuario = $res_usuarios[$i]["id_usuario"];
    $usuario = $res_usuarios[$i]["usuario"];

    // Cargamos los datos del usuario
    $user->cargaDatosUsuarioIdAnt($id_usuario);
    $id_almacen = $user->id_almacen;
    $actualizar = true;

    switch ($id_almacen) {
        case 0:
            $actualizar = false;
            break;
        case 1;
            $id_almacen = 3;
            break;
        case 2:
            $id_almacen = 4;
            break;
        case 3:
            $id_almacen = 0;
            break;
        case 4:
            $id_almacen = 0;
            break;
        case 5:
            $id_almacen = 8;
            break;
        case 6:
            $id_almacen = 9;
            break;
        case 7:
            $id_almacen = 5;
            break;
        case 8:
            $id_almacen = 6;
            break;
        case 9:
            $id_almacen = 7;
            break;
    }

    if($actualizar) {
        $update = sprintf("update usuarios set id_almacen=%s where activo=1 and id_usuario=%s",
            $db->makeValue($id_almacen, "int"),
            $db->makeValue($id_usuario, "int"));
        $db->setConsulta($update);
        if ($db->ejecutarSoloConsulta()) {
            echo "Se ha cambiado el id_almacen por [" . $id_almacen . "] para el usuario " . $usuario . "-[" . $id_usuario . "]";
            echo "<br/>";
        }
        else {
            echo "Se ha producido un error al cambiar el id_almacen";
            echo "<br/>";
        }
    }
}
?>

