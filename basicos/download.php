<?php 
// Este fichero sirve para descargar el archivo perteneciente al componente (PERIFERICO o KIT)
$archivo = $_GET["id"];
$ruta_completa = "mecanica/".$archivo;

// header("Location"): Envia el encabezado del navegador y devuelve el codigo de status. Redireccion del navegador
// header(Content-type: application/<aplicacion>): Sirve para mostrar un fichero de una aplicacion determinada
// header(Content-Disposition: attachment; filename="<nombre_fichero>"): Sirve para descargar un fichero con nombre especificado

header("Location: mecanica/".$archivo);
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$archivo);
$fp=fopen($ruta_completa, "r");
fpassthru($fp);
?>