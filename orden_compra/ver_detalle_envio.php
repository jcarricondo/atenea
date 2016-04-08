<?php
session_start();
// Este fichero muestra un popup con las referencias del componente, conteniendo tambien las referencias de los kits e interfaces si tuviese.
//include("../includes/sesion.php");
include_once("../classes/mysql.class.php");
?>
<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
<div id="ContenedorCentral">
<h3>Registro de envío de email</h3>
<p style="font: 12px Verdana, Tahoma;">
<?php echo $_SESSION["salida_email"]; ?>
</p>
</div>