<?php
// Fichero con las funciones de comprobacion para AJAX
include("../classes/mysql.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");
$db = new MySQL();
$op = new Orden_Produccion();

if (isset($_GET["comp"])){
	$alias = $_GET["alias"];
	switch($_GET["comp"]){
		case "comprobar_alias":
			if ($_GET["mantenimiento"] != true){
				if (!$op->compruebaAlias($alias)){
					echo '<span style="color:green"><b>ALIAS VALIDO</b></span>';
					echo '<input type="hidden" id="alias_validado" name="alias_validado" value="1"/>';
				} 
				else {
					echo '<span style="color:red"><b>El alias ya se encuentra registrado en la base de datos</b></span>';
					echo '<input type="hidden" id="alias_validado" name="alias_validado" value="0"/>';
				}
			}
			else {
				if ($alias == ""){
					echo '<span style="color:white"></span>';
					echo '<input type="hidden" id="alias_validado" name="alias_validado" value="1"/>';
				}
				else if (!$op->compruebaAlias($alias)){
					echo '<span style="color:green"><b>ALIAS VALIDO</b></span>';
					echo '<input type="hidden" id="alias_validado" name="alias_validado" value="1"/>';
				} 
				else {
					echo '<span style="color:red"><b>El alias ya se encuentra registrado en la base de datos</b></span>';
					echo '<input type="hidden" id="alias_validado" name="alias_validado" value="0"/>';
				}
			}
		break;	
		case "mod_comprueba_alias":
			$id_produccion = $_GET["id_produccion"];
			if (!$op->compruebaModAlias($alias,$id_produccion)){
				echo '<span style="color:green"><b>ALIAS VALIDO</b></span>';
				echo '<input type="hidden" id="alias_validado" name="alias_validado" value="1"/>';
			}
			else {
				echo '<span style="color:red"><b>El alias ya se encuentra registrado en la base de datos</b></span>';
				echo '<input type="hidden" id="alias_validado" name="alias_validado" value="0"/>';
			}
		break;


	}
}
?>