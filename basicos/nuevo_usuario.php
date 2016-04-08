<?php 
// Proceso para la creación de un nuevo usuario
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/usuario.class.php");
include("../classes/sede/sede.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/control_usuario.class.php");
permiso(5);

$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$controlUser = new Control_Usuario();
$esAdministrador = $controlUser->esAdministrador($id_tipo_usuario);
$esAdminGlobal = $controlUser->esAdministradorGlobal($id_tipo_usuario);

$sede = new Sede();
$almacen = new Almacen();
$validacion = new Funciones();
$usuarios = new Usuario();

if(isset($_POST["guardandoUsuario"]) and $_POST["guardandoUsuario"] == 1) {
	// Se reciben los datos
	$user = $_POST["user"];
	$pass = $_POST["pass"];
	$repita_pass = $_POST["repita_pass"];
	$email = $_POST["email"];
	$tipo_usuario = $_POST["tipo_usuario"];
    $id_almacen = $_POST["almacenes"];

    if($id_almacen == NULL) $id_almacen = 0;

	if(($user == '') or ($pass == '') or ($repita_pass == '') or ($email == '')){
		echo '<script type="text/javascript">alert("Rellene los campos obligatorios")</script>';
	}
	elseif (!$validacion->verificarEmail($email)){
		echo '<script type="text/javascript">alert("El email introducido no es correcto")</script>';
	}	
	else {
		$usuarios->datosNuevoUsuario(NULL,$user,$pass,$repita_pass,$email,$tipo_usuario,$id_almacen);
		$resultado = $usuarios->guardarCambios();
		if($resultado == 1) {
            header("Location: usuarios.php?user=creado");
		}
        else {
			$mensaje_error = $usuarios->getErrorMessage($resultado);
		}
	}
} 
else {
	$user = "";
	$pass = "";
	$repita_pass = "";
	$email = "";
	$tipo_usuario = "";
}
$titulo_pagina = "Básico > Nuevo usuario";
$pagina = "new_usuario";
include ('../includes/header.php');
echo '<script type="text/javascript" src="../js/basicos/usuarios.js"></script>';
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>
   	
    <h3> Creaci&oacute;n de un nuevo usuario </h3>
    <form id="FormularioCreacionBasico" name="crearUsuario" action="nuevo_usuario.php" method="post" class="" >
    	<br />
        <h5> Rellene los siguientes campos para la creaci&oacute;n de un nuevo usuario </h5>
    	<div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Nombre *</div>
            <input type="text" id="user" name="user" class="CreacionBasicoInput" autocomplete="off"  value="<?php echo $user;?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Contraseña *</div>
          	<input type="password" id="pass" name="pass" class="CreacionBasicoInput" autocomplete="off" value="<?php echo $pass;?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Repita la contraseña *</div>
           	<input type="password" id="repita_pass" name="repita_pass" class="CreacionBasicoInput" value="<?php echo $repita_pass;?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Email *</div>
          	<input type="text" id="email" name="email" class="CreacionBasicoInput" value="<?php echo $email;?>" />
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
                                    echo '<option value="1">ADMINISTRADOR SIMUMAK</option>';
                                    echo '<option value="2">ADMINISTRADOR DISEÑO</option>';
                                    echo '<option value="3">ADMINISTRADOR FABRICA</option>';
                                    echo '<option value="4">ADMINISTRADOR MANTENIMIENTO</option>';
                                    echo '<option value="8">ADMINISTRADOR GESTION</option>';
                                    echo '<option value="5">USUARIO DISEÑO</option>';
                                    echo '<option value="6">USUARIO FABRICA</option>';
                                    echo '<option value="7">USUARIO MANTENIMIENTO</option>';
                                    echo '<option value="9">USUARIO GESTION</option>';
                                break;
                                case '2':
                                    // ADMINISTRADOR DISEÑO
                                    echo '<option value="2">ADMINISTRADOR DISEÑO</option>';
                                    echo '<option value="5">USUARIO DISEÑO</option>';
                                break;
                                case '3':
                                    // ADMINISTRADOR FABRICA
                                    echo '<option value="3">ADMINISTRADOR FABRICA</option>';
                                    echo '<option value="6">USUARIO FABRICA</option>';
                                break;
                                case '4':
                                    // ADMINISTRADOR MANTENIMIENTO
                                    echo '<option value="4">ADMINISTRADOR MANTENIMIENTO</option>';
                                    echo '<option value="7">USUARIO MANTENIMIENTO</option>';
                                break;
                                case '8':
                                    // ADMINISTRADOR GESTIÓN
                                    echo '<option value="8">ADMINISTRADOR GESTION</option>';
                                    echo '<option value="9">USUARIO GESTION</option>';
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
                // Si es un usuario de fábrica o de mantenimiento, mostraremos sólo los almacenes de su sede
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
                    $res_almacenes = $almacen->dameAlmacenes();    
                }

                // Obtenemos los almacenes disponibles
                $capa_almacen = '<div class="LabelCreacionBasico">Almacen *</div>';
                $select_almacen = '<select id="almacenes" name="almacenes" class="CreacionBasicoInput">';

                // En función del tipo de usuario se vincula a un almacen
                switch ($id_tipo_usuario) {
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
                            $select_almacen .= '<option value="'.$id_almacen.'">'.$nombre.'</option>';
                        }
                        $select_almacen .= '</select>';
                        echo $capa_almacen.$select_almacen;
                    break;
                }
            ?>       
        </div>

        <br/>
        <div class="ContenedorBotonCreacionBasico">
           	<input type="button" id="volver" name="volver" value="Volver" onClick="javascript:window.history.back()"/> 
            <input type="hidden" id="guardandoUsuario" name="guardandoUsuario" value="1" />
            <input type="submit" id="guardar" name="guardar" value="Continuar" />
        </div>
        <br />
        <div class="mensajeCamposObligatorios">
        	* Campos obligatorios
        </div>
		<?php 
		if($mensaje_error != "") {
			echo '<div class="mensaje_error">'.$mensaje_error.'</div>'; 
		}
		?>
        <br />
        <?php echo '<script type="text/javascript"> window.setTimeout("cleanInputs()", 100);</script>'; ?>
    </form>
</div>    

<?php include ("../includes/footer.php"); ?>