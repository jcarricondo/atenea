<?php
// Pagina de LOGIN de ATENEA
session_start();
if(isset($_POST["sendLogin"])) {
	// Se carga la clase para la base de datos y el control de usuarios
	include("classes/mysql.class.php");
	include("classes/control_usuario.class.php");
	$db = new MySQL();
	$loginUser = new Control_Usuario();
	// Se recogen los datos del formulario
	$usuario = $_POST["usuario"];
	$pass = $_POST["password"];

	// Se realiza la validación de los datos del usuario
	$intento_login = $loginUser->login($usuario,$pass); // Proceso de validación, devuelve un código para identificar el resultado
	// Si el codigo de $intento_login es 1 la validación ha sido satisfactoria
	if($intento_login == 1) {
		// Se obtienen los datos del usuario
		$datosLogin = $loginUser->getDatosUsuario($usuario);
		// Se crean las variables de la sesión y se redirecciona a la página principal
		$_SESSION["AT_id_usuario"] = $datosLogin["id_usuario"];
		$_SESSION["AT_usuario"] = $datosLogin["usuario"];
		$_SESSION["AT_id_tipo_usuario"] = $datosLogin["id_tipo"];
		$_SESSION["AT_id_almacen"] = $datosLogin["id_almacen"];
		$_SESSION["AT_loginok"] = 1;
		// Obtenemos la sede del usuario 
		$_SESSION["AT_id_sede"] = $loginUser->dameSedeSegunUsuario($datosLogin["id_tipo"],$datosLogin["id_almacen"]);
		header("Location: principal/index.php");
	} else {
		// Obtiene el mensaje de error en base al códido de $intento_login
		$mensaje = $loginUser->getErrorMessage($intento_login);
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Atenea</title>
<link rel="stylesheet" type="text/css" media="all" href="css/login.css" />
</head>

<body>
<div id="contenedor-login">
    <form id="login" name="login" action="index.php" method="post">
    <img src="images/atenea.png" alt="Atenea" />
    <br />
    <div class="user">Usuario</div>
    <div class="pass">Contraseña</div>
    <br />
    <input type="text" id="usuario" name="usuario" class="user-input" value="<?php echo $usuario; ?>" />
    <input type="password" id="password" name="password" class="pass-input" value="" />
    <br />
    <div class="capa-button">
    	<input type="submit" id="sendLogin" name="sendLogin" class="button-submit" value="Acceder" />
    </div>
    </form>
</div>
<div class="capa-mensaje">
	<?php
	if($mensaje) { 
    	echo $mensaje;
	}
    ?>
</div>
</body>
</html>