<?php 
// Este fichero sirve para descargar los archivos adjuntos de la orden de compra
$archivo = $_GET["id"];
$ruta_completa = "archivos_adjuntos/".$archivo;
header("Location: archivos_adjuntos/".$archivo);
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$archivo);
$fp=fopen($ruta_completa, "r");
fpassthru($fp);
?>