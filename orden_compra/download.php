<?php 
// Este fichero sirve para descargar las facturas de la orden de compra
$archivo = $_GET["id"];
$ruta_completa = "facturas/".$archivo;
header("Location: facturas/".$archivo);
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$archivo);
$fp=fopen($ruta_completa, "r");
fpassthru($fp);
?>