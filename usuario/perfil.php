<?php
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/usuario.class.php");
include("../classes/control_usuario.class.php");

$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$validacion = new Funciones();
$usuarios = new Usuario();
$controlUser = new Control_Usuario();
$esAdministrador = $controlUser->esAdministrador($id_tipo_usuario);

if(isset($_POST["guardandoPerfil"]) and $_POST["guardandoPerfil"] == 1) {
	// Se reciben los datos
	$user = $_GET["user"];
	$passwd = $_POST["passwd"];
	$repita_pass = $_POST["repita_pass"];
	$email = $_POST["email"];
	$id_usuario = $_GET["id"];
    $id_almacen = $_POST["almacenes"];

    // No cambiamos el tipo de usuario por lo que no actualizamos los permisos
    $actualizar_permisos = false;
    $password_changed = false;
	
    if ($email == '') {
		echo '<script type="text/javascript">alert("Rellene los campos obligatorios")</script>';
	}
	else if (!$validacion->verificarEmail($email)){
		echo '<script type="text/javascript">alert("El email introducido no es correcto")</script>';
	}
    else {
        if(!empty($passwd) and !empty($repita_pass)){
            // Intento de modificar contraseña
            $usuarios->cargarDatosModificacion($id_usuario,$user,$email,$passwd,$repita_pass,$actualizar_permisos,$id_tipo_usuario,$id_almacen);
            $password_changed = true; 
        }
        else {
            // El usuario no modifica contraseña
            $usuarios->cargarDatosModificacion($id_usuario,$user,$email,"","",$actualizar_permisos,$id_tipo_usuario,$id_almacen);
        }    

        $resultado = $usuarios->guardarCambios();
        if($resultado == 1) {
            if($password_changed){
                header("Location: ../basicos/usuarios.php?user=modificado&password_changed=1");
            }
            else {
                header("Location: ../basicos/usuarios.php?user=modificado&password_changed=0");   
            }    
        } 
        else {
            $mensaje_error = $usuarios->getErrorMessage($resultado);
        }
    }
}

// Se carga el nombre, la contraseña y el email del usuario
$usuarios->cargaDatosUsuarioId($_GET["id"]);
$usuario = $usuarios->usuario;
$email = $usuarios->email;
$id_tipo = $usuarios->id_tipo;
$id_almacen = $usuarios->id_almacen;
$res_tipo_usuario = $usuarios->dameNombreTipoUsuario($id_tipo);
$tipo_user = $res_tipo_usuario["tipo"];

$titulo_pagina = "Perfil del Usuario";
include ("../includes/header.php");
?>

<div class="separador"></div>

<div id="CapaBotones">
	<div class="CapaBotonesContenedorContenido">
    	<a class="BotonMenu" href="../orden_produccion/OrdenesProduccion.php">O. Producci&oacute;n</a>
      	<a class="BotonMenu" href="../orden_compra/OrdenesCompra.php" >O. de Compra</a>
       	<a class="BotonMenu" href="../productos/productos.php" >Productos</a>
        <a class="BotonMenu" href="../basicos/proveedores.php" >B&aacute;sicos</a>
    </div>

    <?php include ("../includes/opciones_usuario.php"); ?>
</div>


<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>

    <h3> Modificacion de usuario </h3>

    <form id="FormularioCreacionBasico" name="modificarPerfil" action="perfil.php?id=<?php echo $usuarios->id_usuario;?>&user=<?php echo $usuarios->usuario;?>" method="post">
    	<br />
        <h5> Modifique la contraseña o el email en el siguiente formulario </h5>
    	<div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Nombre </div>
           	<label id="usuario" class="LabelPrecio"><?php echo $usuario;?></label>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Contraseña *</div>
            <input type="password" id="passwd" name="passwd" class="CreacionBasicoInput" value="<?php echo $passwd;?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Repita la contraseña *</div>
           	<input type="password" id="repita_pass" name="repita_pass" class="CreacionBasicoInput" value="<?php echo $repita_pass;?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Email *</div>
          	<input type="text" id="email" name="email" class="CreacionBasicoInput" value="<?php echo $email;?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Tipo Usuario *</div>
            <label id="tipo_usuario" class="LabelPrecio" style="width:245px;"><?php echo utf8_encode($tipo_user);?></label>
            <input type="hidden" id="almacenes" name="almacenes" value="<?php echo $id_almacen;?>" />
        </div>
        <br/>

        <div class="ContenedorBotonCreacionBasico">
           	<input type="button" id="volver" name="volver" value="Volver" onClick="javascript:window.history.back();"/>
            <input type="hidden" id="guardandoPerfil" name="guardandoPerfil" value="1" />
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

    <div class="ContenedorBotonCrear">
    	<?php
			if($mensaje != "") {	
				echo '<div class="mensaje">'.$mensaje.'</div>';
			}
		?>
	</div>
</div>
<?php include ("../includes/footer.php"); ?> 
