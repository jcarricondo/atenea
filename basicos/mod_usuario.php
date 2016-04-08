<?php 
// Este fichero modifica un usuario de basicos
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/usuario.class.php");
include("../classes/sede/sede.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/control_usuario.class.php");

$usuarios = new Usuario();
$controlUser = new Control_Usuario();
$sede = new Sede();
$almacen = new Almacen();
$validacion = new Funciones();

$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$esAdministrador = $controlUser->esAdministrador($id_tipo_usuario);
$esAdminGlobal = $controlUser->esAdministradorGlobal($id_tipo_usuario);

if(isset($_POST["guardandoUsuario"]) and $_POST["guardandoUsuario"] == 1) {
	// Se reciben los datos
	$usuario = $_POST["usuario"];
	$email = $_POST["email"];
    $id_usuario = $_GET["id"];
    $tipo_usuario = $_POST["tipo_usuario"];
    $id_tipo_user_ant = $_POST["id_tipo_user_ant"];
    $id_almacen = $_POST["almacenes"];

    if($id_tipo_user_ant != $tipo_usuario){
        $actualizar_permisos = true;
    }
    else {
        $actualizar_permisos = false;
    }

    // Se comprueba si se cambió la contraseña al usuario
    $password = $_POST["passwd"];
    $repita_pass = $_POST["repita_pass"];

    $password_changed = false;

	if(($usuario == '') or ($email == '')){
		echo '<script type="text/javascript">alert("Rellene los campos obligatorios")</script>';
	}
	elseif (!$validacion->verificarEmail($email)){
		echo '<script type="text/javascript">alert("El email introducido no es correcto")</script>';
	}
	else {
        if(!empty($password) and !empty($repita_pass)){
            // Intento de modificar contraseña
            $usuarios->cargarDatosModificacion($id_usuario,$usuario,$email,$password,$repita_pass,$actualizar_permisos,$tipo_usuario,$id_almacen);
            $password_changed = true; 
        }
        else {
            // El usuario no modifica contraseña
    		$usuarios->cargarDatosModificacion($id_usuario,$usuario,$email,"","",$actualizar_permisos,$tipo_usuario,$id_almacen);
        }    

		$resultado = $usuarios->guardarCambios();
		if($resultado == 1) {
            if($password_changed){
                header("Location: usuarios.php?user=modificado&password_changed=1");
            }
            else {
                header("Location: usuarios.php?user=modificado&password_changed=0");   
            }    
		} 
		else {
			$mensaje_error = $usuarios->getErrorMessage($resultado);
		}
	}
}

// Se cargan los datos buscando por el ID
$usuarios->cargaDatosUsuarioId($_GET["id"]);
$usuario = $usuarios->usuario;
$email = $usuarios->email;
$id_tipo_user_ant = $usuarios->id_tipo;
$id_almacen_user = $usuarios->id_almacen;
$id_almacen_user_ant = $usuarios->id_almacen;
$passwd = "";
$repita_pass = "";

// Titulo de pagina
$titulo_pagina = "Básico > Modificación usuario";
$pagina = "mod_usuario";
include ('../includes/header.php');
echo '<script type="text/javascript" src="../js/basicos/usuarios.js"></script>';
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>
   	
    <h3> Modificación usuario </h3>
    <form id="FormularioCreacionBasico" name="modificarUsuario" action="mod_usuario.php?id=<?php echo $usuarios->id_usuario;?>" method="post">
    	<br />
        <h5> Modifique los datos en el siguiente formulario </h5>
    	<div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Nombre *</div>
            <input type="text" id="usuario" name="usuario" class="CreacionBasicoInput" value="<?php echo $usuario;?>" />
        </div>    

        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Email *</div>
          	<input type="text" id="email" name="email" class="CreacionBasicoInput" value="<?php echo $email;?>"/>
        </div>
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Password</div>
            <input type="password" id="passwd" name="passwd" class="CreacionBasicoInput" value="<?php echo $passwd;?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Repita password</div>
            <input type="password" id="repita_pass" name="repita_pass" class="CreacionBasicoInput" value="<?php echo $repita_pass;?>" />
        </div>
        <?php
            if($esAdministrador){ ?>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Tipo Usuario *</div>
                    <select id="tipo_usuario" name="tipo_usuario" class="CreacionBasicoInput" onchange="javascript:obtenerTipoUsuario(this.value,<?php echo $esAdminGlobal;?>);">
                        <?php 
                            switch ($id_tipo_usuario) {
                                case '1':
                                    // ADMINISTRADOR SIMUMAK
                                    echo '<option value="1"'; if($id_tipo_user_ant == 1) echo ' selected="selected"'; echo '>ADMINISTRADOR SIMUMAK</option>';
                                    echo '<option value="2"'; if($id_tipo_user_ant == 2) echo ' selected="selected"'; echo '>ADMINISTRADOR DISEÑO</option>';
                                    echo '<option value="3"'; if($id_tipo_user_ant == 3) echo ' selected="selected"'; echo '>ADMINISTRADOR FABRICA</option>';
                                    echo '<option value="4"'; if($id_tipo_user_ant == 4) echo ' selected="selected"'; echo '>ADMINISTRADOR MANTENIMIENTO</option>';
                                    echo '<option value="8"'; if($id_tipo_user_ant == 8) echo ' selected="selected"'; echo '>ADMINISTRADOR GESTION</option>';
                                    echo '<option value="5"'; if($id_tipo_user_ant == 5) echo ' selected="selected"'; echo '>USUARIO DISEÑO</option>';
                                    echo '<option value="6"'; if($id_tipo_user_ant == 6) echo ' selected="selected"'; echo '>USUARIO FABRICA</option>';
                                    echo '<option value="7"'; if($id_tipo_user_ant == 7) echo ' selected="selected"'; echo '>USUARIO MANTENIMIENTO</option>';
                                    echo '<option value="9"'; if($id_tipo_user_ant == 9) echo ' selected="selected"'; echo '>USUARIO GESTION</option>';
                                break;
                                case '2':
                                    // ADMINISTRADOR DISEÑO
                                    echo '<option value="2"'; if($id_tipo_user_ant == 2) echo ' selected="selected"'; echo '>ADMINISTRADOR DISEÑO</option>';
                                    echo '<option value="5"'; if($id_tipo_user_ant == 5) echo ' selected="selected"'; echo '>USUARIO DISEÑO</option>';
                                break;
                                case '3':
                                    // ADMINISTRADOR FABRICA
                                    echo '<option value="3"'; if($id_tipo_user_ant == 3) echo ' selected="selected"'; echo '>ADMINISTRADOR FABRICA</option>';
                                    echo '<option value="6"'; if($id_tipo_user_ant == 6) echo ' selected="selected"'; echo '>USUARIO FABRICA</option>';
                                break;
                                case '4':
                                    // ADMINISTRADOR MANTENIMIENTO
                                    echo '<option value="4"'; if($id_tipo_user_ant == 4) echo ' selected="selected"'; echo '>ADMINISTRADOR MANTENIMIENTO</option>';
                                    echo '<option value="7"'; if($id_tipo_user_ant == 7) echo ' selected="selected"'; echo '>USUARIO MANTENIMIENTO</option>';
                                break;
                                case '8':
                                    // ADMINISTRADOR GESTION
                                    echo '<option value="8"'; if($id_tipo_user_ant == 8) echo ' selected="selected"'; echo '>ADMINISTRADOR GESTION</option>';
                                    echo '<option value="9"'; if($id_tipo_user_ant == 9) echo ' selected="selected"'; echo '>USUARIO GESTION</option>';
                                break;
                                default:
                                    # code...
                                break;
                            }
                        ?>
                    </select>
                </div>
        <?php                
            }
        ?>
        <div id="capaAlmacen" class="ContenedorCamposCreacionBasico">
            <?php 
                // Si es un usuario de fábrica, mostraremos sólo los almacenes de su sede
                $esAdminFab = $controlUser->esAdministradorFab($id_tipo_usuario);
                $esAdminMan = $controlUser->esAdministradorMan($id_tipo_usuario);
                if(($esAdminFab || $esAdminMan) && $id_tipo_usuario != 1){
                    $id_almacen_user = $_SESSION["AT_id_almacen"];
                    $id_sede = $almacen->dameSedeAlmacen($id_almacen_user);
                    $id_sede = $id_sede["id_sede"];
                    if($esAdminFab){
                        $res_almacenes = $sede->dameAlmacenesFabricaSede($id_sede);
                    }
                    if($esAdminMan){
                        $res_almacenes = $sede->dameAlmacenesMantenimientoSede($id_sede);
                    }
                }
                else {
                    $esUserFab = $controlUser->esUsuarioFab($id_tipo_user_ant); 
                    $esUserMan = $controlUser->esUsuarioMan($id_tipo_user_ant); 
                    if($esUserFab) $res_almacenes = $almacen->dameAlmacenesFabrica();
                    if($esUserMan) $res_almacenes = $almacen->dameAlmacenesMantenimiento(); 
                }

                $capa_almacen = '<div class="LabelCreacionBasico">Almacen *</div>';
                $select_almacen = '<select id="almacenes" name="almacenes" class="CreacionBasicoInput">';

                // En función del tipo de usuario se vincula a un almacen
                switch ($id_tipo_user_ant) {
                    case '1':
                        // ADMINISTRADOR SIMUMAK
                        echo '<input type="hidden" id="almacenes" name="almacenes" value="0" />';
                        break;
                    case '2':
                        // ADMINISTRADOR DISEÑO
                        echo '<input type="hidden" id="almacenes" name="almacenes" value="0" />';
                        break;
                    case '5':
                        // USUARIO DISEÑO
                        echo '<input type="hidden" id="almacenes" name="almacenes" value="0" />';
                        break;
                    case '8':
                        // ADMINISTRADOR GESTION
                        echo '<input type="hidden" id="almacenes" name="almacenes" value="0" />';
                        break;
                    case '9':
                        // USUARIO GESTION
                        echo '<input type="hidden" id="almacenes" name="almacenes" value="0" />';
                        break;
                    default:
                        // ADMINISTRADOR FABRICA // ADMINISTRADOR MANTENIMIENTO // USUARIO FABRICA // USUARIO MANTENIMIENTO
                        for($i=0;$i<count($res_almacenes);$i++){
                            $id_almacen = $res_almacenes[$i]["id_almacen"];
                            $nombre = $res_almacenes[$i]["almacen"];
                            $select_almacen .= '<option value="'.$id_almacen.'"';
                            if($id_almacen == $id_almacen_user_ant) $select_almacen .= ' selected="selected"';
                            $select_almacen .= ' >'.$nombre.'</option>';
                        }
                        $select_almacen .= '</select>';
                        echo $capa_almacen.$select_almacen;
                        break;
                }
            ?>       
        </div>

        <div class="ContenedorBotonCreacionBasico">
           	<input type="button" id="volver" name="volver" value="Volver" onClick="javascript:window.history.back()"/> 
            <input type="hidden" id="guardandoUsuario" name="guardandoUsuario" value="1" />
            <input type="hidden" id="id_tipo_user_ant" name="id_tipo_user_ant" value=<?php echo $id_tipo_user_ant;?> />
            <input type="submit" id="guardar" name="guardar" value="Continuar" />
        </div>
        <br />
        <?php 
		if($mensaje_error != "") {
            echo '<div class="mensaje_error">'.$mensaje_error.'</div>'; 
		}
		?>
        <br />
    </form>
</div>   
<?php include ("../includes/footer.php"); ?>