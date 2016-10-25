<?php 
// Este fichero sirve para descargar el archivo perteneciente a las referencias
$archivo = $_GET["id"];
//$ruta_completa = "/des/basicos/mecanica/".$archivo;
//header("Location: /des/basicos/mecanica/".$archivo);
$ruta_completa = "uploads/".$archivo;
	
header("Location: uploads/".$archivo);
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$archivo);
$fp=fopen($ruta_completa, "r");
fpassthru($fp);
?>