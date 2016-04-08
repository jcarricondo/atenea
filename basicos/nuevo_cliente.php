<?php 
// Este fichero crea un nuevo cliente
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/cliente.class.php");
permiso(2);

if (isset($_POST["guardandoCliente"]) and $_POST["guardandoCliente"] == 1){
	// Se reciben los datos del formulario del nuevo cliente
	$nombre = $_POST["nombre"];
	$direccion = $_POST["direccion"];
	$cp = $_POST["cp"];
	$ciudad = $_POST["ciudad"];
	$pais = $_POST["pais"];
	$telefono = $_POST["telefono"];
	$email = $_POST["email"];
	
	$validacion = new Funciones();
	
	if (($nombre == '') or ($direccion == '') or ($cp == '') or ($telefono == '') or ($email == '') or ($ciudad == '') or ($pais == '')){
		echo '<script type="text/javascript">alert("Rellene los campos obligatorios")</script>';
	}
	elseif (!$validacion->verificarSoloDigitos($cp)) {
		echo '<script type="text/javascript">alert("El codigo postal introducido no es correcto")</script>';
	}
	elseif (!$validacion->verificarSoloDigitos($telefono)){
		echo '<script type="text/javascript">alert("El telefono introducido no es correcto")</script>';
	}
	elseif (!$validacion->verificarEmail($email)){
		echo '<script type="text/javascript">alert("El email introducido no es correcto")</script>';
	}
	else {
		$cliente = new Cliente();
		$cliente->datosNuevoCliente(NULL,$nombre,$direccion,$cp,$ciudad,$pais,$telefono,$email);
		$resultado = $cliente->guardarCambios();
		if($resultado == 1) {
			header("Location: clientes.php?client=creado");
		} else {
			$mensaje_error = $cliente->getErrorMessage($resultado);
		}
	}
} 
else {
	$nombre = "";
	$direccion = "";
	$cp = "";
	$ciudad = "";
	$pais = "";
	$telefono = "";
	$email = "";
}	

$titulo_pagina = "Básicos > Nuevo Cliente";
$pagina = "new_cliente";
include ("../includes/header.php");	
?>	

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>
    
    <h3> Creaci&oacute;n de un nuevo cliente </h3>
    <form id="FormularioCreacionBasico" name="crearCliente" action="nuevo_cliente.php" method="post">
    	<br />
        <h5> Rellene los siguientes campos para la creaci&oacute;n de un nuevo cliente </h5>
    	<div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Nombre *</div>
            <input type="text" id="nombre" name="nombre" class="CreacionBasicoInput" value="<?php echo $nombre;?>" />
        </div>    
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Direcci&oacute;n *</div>
           	<input type="text" id="direccion" name="direccion" class="CreacionBasicoInput" value="<?php echo $direccion;?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Codigo Postal *</div>
           	<input type="text" id="cp" name="cp" class="CreacionBasicoInput" value="<?php echo $cp;?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Ciudad *</div>
           	<input type="text" id="ciudad" name="ciudad" class="CreacionBasicoInput" value="<?php echo $ciudad;?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Pais *</div>
           	<input type="text" id="pais" name="pais" class="CreacionBasicoInput" value="<?php echo $pais;?>" />
        </div>
                                     
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Tel&eacute;fono *</div>
           	<input type="text" id="telefono" name="telefono" class="CreacionBasicoInput" value="<?php echo $telefono;?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
          	<div class="LabelCreacionBasico">Email *</div>
          	<input type="text" id="email" name="email" class="CreacionBasicoInput" value="<?php echo $email;?>" />
        </div>
        <div class="ContenedorBotonCreacionBasico">
           	<input type="button" id="volver" name="volver" value="Volver" onClick="javascript:window.history.back()"/> 
            <input type="hidden" id="guardandoCliente" name="guardandoCliente" value="1" />
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
    </form>
</div>    
<?php include ("../includes/footer.php"); ?>