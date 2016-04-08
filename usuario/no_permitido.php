<?php 
include("../includes/sesion.php");
$titulo_pagina = "Acceso no permitido";
include ('../includes/header.php');
?>

<div class="separador"></div> 

<div id="CapaBotones">
    
	<?php include ("../includes/opciones_usuario.php"); ?>

</div>


<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>
   	
    <h3> Acceso no permitido </h3>
</div>    

<?php include ("../includes/footer.php"); ?>
