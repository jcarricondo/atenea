<?php 
// Este fichero modifica el cliente de basicos
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/cliente.class.php");
permiso(34);
// Comprobamos si el usuario puede modificar el basico
if(!permisoMenu(3)){ 
    $modificar = false;
    $solo_lectura = 'readonly="readonly"';
}
else {
    $modificar = true;
    $solo_lectura = '';
}

$cliente = new Cliente();

if (isset($_POST["guardandoCliente"]) and $_POST["guardandoCliente"] == 1){
	// Se reciben los datos del cliente
	$nombre = $_POST["nombre"];
	$direccion = $_POST["direccion"];
	$cp = $_POST["cp"];
	$ciudad = $_POST["ciudad"];
	$pais = $_POST["pais"];
	$telefono = $_POST["telefono"];
	$email = $_POST["email"];
	$id_cliente = $_GET["id"];
	
	$validacion = new Funciones();
	
	if (($nombre == '') or ($direccion == '') or ($telefono == '') or ($email == '') or ($ciudad == '') or ($pais == '') or ($cp == '')){
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
		$cliente->datosNuevoCliente($id_cliente,$nombre,$direccion,$cp,$ciudad,$pais,$telefono,$email);
		$resultado = $cliente->guardarCambios();
		if($resultado == 1) {
			header("Location: clientes.php?client=modificado");
		} else {
			$mensaje_error = $cliente->getErrorMessage($resultado);
		}
	}
} 
// Se cargan los datos buscando por el ID
$cliente->cargaDatosClienteId($_GET["id"]);
$nombre = $cliente->nombre;
$direccion = $cliente->direccion;
$cp = $cliente->cp;
$ciudad = $cliente->ciudad;
$pais = $cliente->pais;
$telefono = $cliente->telefono;
$email = $cliente->email;

// Titulo de pagina	
$titulo_pagina = "Básicos > Modifica Cliente";
$pagina = "mod_cliente";
include ("../includes/header.php");	
?>	

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>
    
    <h3> Modificacion de un cliente </h3>
    <form id="FormularioCreacionBasico" name="modificarCliente" action="mod_cliente.php?id=<?php echo $cliente->id_cliente;?>" method="post">
    	<br />
        <h5> Modifique los datos en el siguiente formulario </h5>
    	<div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Nombre *</div>
            <input type="text" id="nombre" name="nombre" class="CreacionBasicoInput" value="<?php echo $nombre;?>" <?php echo $solo_lectura; ?> />
        </div>    
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Direcci&oacute;n *</div>
           	<input type="text" id="direccion" name="direccion" class="CreacionBasicoInput" value="<?php echo $direccion;?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Codigo Postal *</div>
           	<input type="text" id="cp" name="cp" class="CreacionBasicoInput" value="<?php echo $cp;?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Ciudad *</div>
           	<input type="text" id="ciudad" name="ciudad" class="CreacionBasicoInput" value="<?php echo $ciudad;?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Pais *</div>
           	<input type="text" id="pais" name="pais" class="CreacionBasicoInput" value="<?php echo $pais;?>" <?php echo $solo_lectura; ?> />
        </div>
                                     
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Tel&eacute;fono *</div>
           	<input type="text" id="telefono" name="telefono" class="CreacionBasicoInput" value="<?php echo $telefono;?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
          	<div class="LabelCreacionBasico">Email *</div>
          	<input type="text" id="email" name="email" class="CreacionBasicoInput" value="<?php echo $email;?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorBotonCreacionBasico">
           	<input type="button" id="volver" name="volver" value="Volver" onClick="javascript:window.history.back()"/> 
            <?php 
                if($modificar) { ?>
                    <input type="hidden" id="guardandoCliente" name="guardandoCliente" value="1" />
                    <input type="submit" id="guardar" name="guardar" value="Continuar" />
            <?php 
                } 
            ?>
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