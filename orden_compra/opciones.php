<?php
// Fichero que contiene las distintas opciones de las Ordenes de Compra
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
permiso(14);

$pagina = "opciones";
$titulo_pagina="ÓRDENES DE COMPRA > Opciones";
include ("../includes/header.php");
echo '<script type="text/javascript" src="../js/orden_compra/ordenes_compra.js"></script>';

// Se consultan los datos de configuración
$db = new MySQL();
// Guardamos las opciones para el envio de pedidos por email. 
if(isset($_POST["guardar"])) {
	$updateAsunto = sprintf("update orden_compra_opciones set valor=%s where clave=%s",
		$db->makeValue($_POST["asunto"], "text"),
		$db->makeValue("asunto_email", "text"));
	$db->setConsulta($updateAsunto);
	$db->ejecutarSoloConsulta();
	$updateTexto = sprintf("update orden_compra_opciones set valor=%s where clave=%s",
		$db->makeValue($_POST["texto"], "text"),
		$db->makeValue("texto_email", "text"));
	$db->setConsulta($updateTexto);
	$db->ejecutarSoloConsulta();
	$mensaje_error = "Los cambios se han realizado";
}
else if (isset($_POST["guardar_obs"])) {
    // Guardamos las observaciones de las fra-request de las Ordenes de Compra
    $updateClave = sprintf("update orden_compra_opciones set valor=%s where clave=%s",
        $db->makeValue($_POST["titulo_observaciones"], "text"),
        $db->makeValue("titulo_observaciones", "text"));
    $db->setConsulta($updateClave);
    $db->ejecutarSoloConsulta();
    $updateValor = sprintf("update orden_compra_opciones set valor=%s where clave=%s",
        $db->makeValue(nl2br($_POST["texto_observaciones"]), "text"),
        $db->makeValue("texto_observaciones", "text"));
    $db->setConsulta($updateValor);
    $db->ejecutarSoloConsulta();
    $mensaje_error = "Los cambios se han realizado";
}

// Cargamos el contenido de las opciones de email
$consultaAsunto = "select valor from orden_compra_opciones where clave='asunto_email'";
$consultaTexto = "select valor from orden_compra_opciones where clave='texto_email'";
$db->setConsulta($consultaAsunto);
$db->ejecutarConsulta();
$datoAsunto = $db->getPrimerResultado();
$asunto = $datoAsunto["valor"];
$db->setConsulta($consultaTexto);
$db->ejecutarConsulta();
$datoTexto = $db->getPrimerResultado();
$texto = $datoTexto["valor"];

// Cargamos el contenido de las observaciones de pdf
$consultaTitulo = "select valor from orden_compra_opciones where clave='titulo_observaciones'";
$consultaTextoObs = "select valor from orden_compra_opciones where clave='texto_observaciones'";
$db->setConsulta($consultaTitulo);
$db->ejecutarConsulta();
$datoTitulo = $db->getPrimerResultado();
$titulo_observaciones = $datoTitulo["valor"];
$db->setConsulta($consultaTextoObs);
$db->ejecutarConsulta();
$datoTextoObs = $db->getPrimerResultado();
$texto_observaciones = $datoTextoObs["valor"];
?>

<div class="separador"></div> 
<?php include("../includes/menu_oc.php");?> 

<div id="ContenedorCentral">
	<div id="ContenedorSidebar"><?php include ("../includes/sidebar.php"); ?></div>
    
  	<h3> Opciones de &Oacute;rdenes de Compra</h3>
    <form id="FormularioCreacionBasico" name="modificarOpciones" action="opciones.php" method="post">
    	<br />
        <h5>Opciones para el envío de pedidos por Email</h5>
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Asunto</div>
            <input type="text" id="asunto" name="asunto" class="CreacionBasicoInput" value="<?php echo $asunto;?>" />
        </div>    
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Texto</div>
          	<textarea id="texto" name="texto" class="textareaInput" onKeyUp="return maximaLongitud(this,3000)" /><?php echo $texto;?></textarea>
        </div>
        <div class="ContenedorBotonCreacionBasico">
            <input type="submit" id="guardar" name="guardar" value="Guardar" />
        </div>
    
        <h5>Opciones para incluir observaciones en el pdf de las ordenes de compra</h5>
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Titulo</div>
            <input type="text" id="titulo_observaciones" name="titulo_observaciones" class="CreacionBasicoInput" value="<?php echo $titulo_observaciones;?>" />
        </div>    
        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Texto</div>
            <textarea id="texto_observaciones" name="texto_observaciones" class="textareaInput" onKeyUp="return maximaLongitud(this,3000)" /><?php echo preg_replace('/<br \/>/','', $texto_observaciones);?></textarea>
        </div>
        <div class="ContenedorBotonCreacionBasico">
            <input type="submit" id="guardar_obs" name="guardar_obs" value="Guardar" />
        </div>
        
        <?php 
		if($mensaje_error != "") {
			echo '<div class="mensaje_error"><span style="color: green;">'.$mensaje_error.'</span></div>'; 
		}
		?>
        <br />
    </form>
</div>     
<?php include ("../includes/footer.php"); ?>