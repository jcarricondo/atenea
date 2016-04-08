<?php
// Este fichero modifica un centro logistico de basicos
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/centro_logistico.class.php");
permiso(34);
// Comprobamos si el usuario puede modificar el centro logistico
if(!permisoMenu(36)){ 
    $modificar = false;
    $solo_lectura = 'readonly="readonly"';
}
else {
    $modificar = true;
    $solo_lectura = '';
}

$centroLogistico = new CentroLogistico();
if(isset($_POST["guardandoCentroLogistico"]) and $_POST["guardandoCentroLogistico"] == 1) {
	// Se reciben los datos
	$nombre = $_POST["nombre"];
	$direccion = $_POST["direccion"];
	$telefono = $_POST["telefono"];
	$email = $_POST["email"];
	$ciudad = $_POST["ciudad"];
	$pais = $_POST["pais"];
	$forma_pago = $_POST["forma_pago"];
	$metodo_pago = $_POST["metodo_pago"];
	$tiempo_suministro = $_POST["tiempo_suministro"];
	$descripcion = $_POST["descripcion"];
	$provincia = $_POST["provincia"];
	$codigo_postal = $_POST["codigo_postal"];
	$persona_contacto = $_POST["persona_contacto"];
	$id_centro_logistico = $_GET["id"];
	
	$validacion = new Funciones();
	
	if (($nombre == '') or ($direccion == '') or ($telefono == '') or ($email == '') or ($ciudad == '') or ($pais == '')){
		echo '<script type="text/javascript">alert("Rellene los campos obligatorios")</script>';
	}
	elseif (!$validacion->verificarSoloDigitos($telefono)) {
		echo '<script type="text/javascript">alert("El telefono introducido no es correcto")</script>';
	}
	elseif (!$validacion->verificarEmail($email)){
		echo '<script type="text/javascript">alert("El email introducido no es correcto")</script>';
	}
	elseif (!$validacion->verificarSoloDigitos($codigo_postal)){
		echo '<script type="text/javascript">alert("El codigo postal introducido no es correcto")</script>';
	}
	else {
		$centroLogistico->datosNuevoCentroLogistico($id_centro_logistico,$nombre,$descripcion,$direccion,$ciudad,$pais,$telefono,$email,$forma_pago,$tiempo_suministro,$metodo_pago,$provincia,$codigo_postal,$persona_contacto);
		$resultado = $centroLogistico->guardarCambios();
		if($resultado == 1) {
			header("Location: centros_logisticos.php?centro_logistico=modificado");
		} 
		else {
			$mensaje_error = $centroLogistico->getErrorMessage($resultado);
		}
	}
}
// Se cargan los datos buscando por el ID
$centroLogistico->cargaDatosCentroLogisticoId($_GET["id"]);
$nombre = $centroLogistico->nombre;
$direccion = $centroLogistico->direccion;
$telefono = $centroLogistico->telefono;
$email = $centroLogistico->email;
$ciudad = $centroLogistico->ciudad;
$pais = $centroLogistico->pais;
$forma_pago = $centroLogistico->forma_pago;
$metodo_pago = $centroLogistico->metodo_pago;
$tiempo_suministro = $centroLogistico->tiempo_suministro;
$descripcion = $centroLogistico->descripcion;
$provincia = $centroLogistico->provincia;
$codigo_postal = $centroLogistico->codigo_postal;
$persona_contacto = $centroLogistico->persona_contacto;

// Se cargan los datos del centro logistico
$titulo_pagina = "Básico > Modifica Centro Logístico";
$pagina = "mod_centro_logistico";
include ('../includes/header.php');
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>

    <h3> Modificación de centro logístico </h3>
    
    <form id="FormularioCreacionBasico" name="modificarCentroLogistico" action="mod_centro_logistico.php?id=<?php echo $centroLogistico->id_centro_logistico; ?>" method="post">
    	<br />
        <h5> Modifique los datos en el siguiente formulario</h5>
    	<div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Nombre *</div>
            <input type="text" id="nombre" name="nombre" class="CreacionBasicoInput" value="<?php echo $nombre; ?>" <?php echo $solo_lectura; ?> />
        </div>    
        <div class="ContenedorCamposCreacionBasico">
          	<div class="LabelCreacionBasico">Direcci&oacute;n *</div>
           	<input type="text" id="direccion" name="direccion" class="CreacionBasicoInput" value="<?php echo $direccion; ?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Tel&eacute;fono *</div>
           	<input type="text" id="telefono" name="telefono" class="CreacionBasicoInput" value="<?php echo $telefono; ?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
          	<div class="LabelCreacionBasico">Email *</div>
           	<input type="text" id="email" name="email" class="CreacionBasicoInput" value="<?php echo $email; ?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Ciudad *</div>
            <input type="text" id="ciudad" name="ciudad" class="CreacionBasicoInput" value="<?php echo $ciudad; ?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Pais *</div>
           	<input type="text" id="pais" name="pais" class="CreacionBasicoInput" value="<?php echo $pais; ?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Forma de pago</div>
            <?php 
                if($modificar){ ?>
                    <select id="forma_pago" name="forma_pago" class="CreacionBasicoInput">
                      <option value="0">Selecciona</option>
                        <option value="1"<?php if($forma_pago == 1) { echo ' selected="selected"'; } ?>>Transferencia bancaria</option>
                        <option value="2"<?php if($forma_pago == 2) { echo ' selected="selected"'; } ?>>Tarjeta de crédito/débito</option>
                        <option value="3"<?php if($forma_pago == 3) { echo ' selected="selected"'; } ?>>PayPal</option>
                        <option value="4"<?php if($forma_pago == 4) { echo ' selected="selected"'; } ?>>Recibo domiciliado</option>
                    </select>
            <?php 
                }
                else { 
                    if($forma_pago == 1) { ?>
                        <input type="text" id="forma_pago" name="forma_pago" class="CreacionBasicoInput" value="Transferencia bancaria" <?php echo $solo_lectura; ?> />
                <?php    
                    } 
                    else if ($forma_pago == 2) { ?>
                        <input type="text" id="forma_pago" name="forma_pago" class="CreacionBasicoInput" value="Tarjeta de crédito/débito" <?php echo $solo_lectura; ?> />
                <?php    
                    } 
                    else if ($forma_pago == 3) { ?>
                        <input type="text" id="forma_pago" name="forma_pago" class="CreacionBasicoInput" value="PayPal" <?php echo $solo_lectura; ?> /> 
                <?php    
                    } 
                    else if ($forma_pago == 4) { ?>
                        <input type="text" id="forma_pago" name="forma_pago" class="CreacionBasicoInput" value="Recibo domiciliado" <?php echo $solo_lectura; ?> /> 
                <?php 
                    }
                    else { ?>
                        <input type="text" id="forma_pago" name="forma_pago" class="CreacionBasicoInput" value="" <?php echo $solo_lectura; ?> /> 
                <?php  
                    }  
                }
            ?>     
        </div> 
        <div class="ContenedorCamposCreacionBasico">   
         	<div class="LabelCreacionBasico">M&eacute;todo de pago</div>
          <?php 
              if($modificar){ ?>
                  <select id="metodo_pago" name="metodo_pago" class="CreacionBasicoInput">
                      <option value="0">Selecciona</option>
                      <option value="1"<?php if($metodo_pago == 1) { echo ' selected="selected"'; } ?>>Pago previo</option>
                      <option value="2"<?php if($metodo_pago == 2) { echo ' selected="selected"'; } ?>>30 días</option>
                      <option value="3"<?php if($metodo_pago == 3) { echo ' selected="selected"'; } ?>>60 días</option>
                      <option value="4"<?php if($metodo_pago == 4) { echo ' selected="selected"'; } ?>>90 días</option>
                  </select>
          <?php 
              }
              else { 
                  if($metodo_pago == 1) { ?>
                        <input type="text" id="metodo_pago" name="metodo_pago" class="CreacionBasicoInput" value="Pago previo" <?php echo $solo_lectura; ?> />
                <?php    
                    } 
                    else if ($metodo_pago == 2) { ?>
                        <input type="text" id="metodo_pago" name="metodo_pago" class="CreacionBasicoInput" value="30 días" <?php echo $solo_lectura; ?> />
                <?php    
                    } 
                    else if ($metodo_pago == 3) { ?>
                        <input type="text" id="metodo_pago" name="metodo_pago" class="CreacionBasicoInput" value="60 días" <?php echo $solo_lectura; ?> /> 
                <?php    
                    } 
                    else if ($metodo_pago == 4) { ?>
                        <input type="text" id="metodo_pago" name="metodo_pago" class="CreacionBasicoInput" value="90 días" <?php echo $solo_lectura; ?> /> 
                <?php 
                    }
                    else { ?>
                        <input type="text" id="metodo_pago" name="metodo_pago" class="CreacionBasicoInput" value="" <?php echo $solo_lectura; ?> /> 
                <?php 
                    }
              }
            ?>
        </div>    
        <div class="ContenedorCamposCreacionBasico">    
           	<div class="LabelCreacionBasico">Tiempo suministro</div>
            <?php 
              if($modificar){ ?>
                  <select id="tiempo_suministro" name="tiempo_suministro" class="CreacionBasicoInput">  
                    <option value="0">Selecciona</option>
                      <option value="1"<?php if($tiempo_suministro == 1) { echo ' selected="selected"'; } ?>>7 días</option>
                      <option value="2"<?php if($tiempo_suministro == 2) { echo ' selected="selected"'; } ?>>14 días</option>
                      <option value="3"<?php if($tiempo_suministro == 3) { echo ' selected="selected"'; } ?>>30 días</option>
                      <option value="4"<?php if($tiempo_suministro == 4) { echo ' selected="selected"'; } ?>>60 días</option>
                      <option value="5"<?php if($tiempo_suministro == 5) { echo ' selected="selected"'; } ?>>90 días</option>
                  </select>
            <?php 
              }
              else { 
                  if($tiempo_suministro == 1) { ?>
                        <input type="text" id="tiempo_suministro" name="tiempo_suministro" class="CreacionBasicoInput" value="7 días" <?php echo $solo_lectura; ?> />
                <?php    
                    } 
                    else if ($tiempo_suministro == 2) { ?>
                        <input type="text" id="tiempo_suministro" name="tiempo_suministro" class="CreacionBasicoInput" value="14 días" <?php echo $solo_lectura; ?> />
                <?php    
                    } 
                    else if ($tiempo_suministro == 3) { ?>
                        <input type="text" id="tiempo_suministro" name="tiempo_suministro" class="CreacionBasicoInput" value="30 días" <?php echo $solo_lectura; ?> /> 
                <?php    
                    } 
                    else if ($tiempo_suministro == 4) { ?>
                        <input type="text" id="tiempo_suministro" name="tiempo_suministro" class="CreacionBasicoInput" value="60 días" <?php echo $solo_lectura; ?> /> 
                <?php 
                    }
                    else if ($tiempo_suministro == 5) { ?>
                        <input type="text" id="tiempo_suministro" name="tiempo_suministro" class="CreacionBasicoInput" value="90 días" <?php echo $solo_lectura; ?> /> 
                <?php 
                    }
                    else { ?>
                        <input type="text" id="tiempo_suministro" name="tiempo_suministro" class="CreacionBasicoInput" value="" <?php echo $solo_lectura; ?> /> 
                <?php
                    }
              }
            ?>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Provincia </div>
           	<input type="text" id="provincia" name="provincia" class="CreacionBasicoInput" value="<?php echo $provincia; ?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Código Postal *</div>
           	<input type="text" id="codigo_postal" name="codigo_postal" class="CreacionBasicoInput" value="<?php echo $codigo_postal; ?>" <?php echo $solo_lectura; ?> />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Persona de Contacto</div>
           	<input type="text" id="persona_contacto" name="persona_contacto" class="CreacionBasicoInput" value="<?php echo $persona_contacto; ?>" <?php echo $solo_lectura; ?> />
        </div>   
        <div class="ContenedorCamposCreacionBasico">    
           	<div class="LabelCreacionBasico">Descripcion</div>
          	<textarea type="text" id="descripcion" name="descripcion" rows="5" class="textareaInput" <?php echo $solo_lectura; ?>><?php echo $descripcion; ?></textarea>	
        </div>   
        <div class="ContenedorBotonCreacionBasico">
          	<input type="button" id="volver" name="volver" value="Volver" onclick="javascript:window.history.back()" /> 
            <?php 
                if($modificar) { ?>
                    <input type="hidden" id="guardandoCentroLogistico" name="guardandoCentroLogistico" value="1" />
                    <input type="submit" id="guardar" name="guardar" value="Continuar" />
            <?php
                }
            ?>
        </div>
        <?php 
			if($mensaje_error != "") {
				echo '<div class="mensaje_error">'.$mensaje_error.'</div>'; 
			}
		?>
        <br />
    </form>
</div>
<?php include ('../includes/footer.php');  ?>
