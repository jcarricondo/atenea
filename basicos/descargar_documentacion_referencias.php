<?php 
// Este fichero prepara la documentación adjunta de referencias para descargar
$id_referencia = $_GET["id_referencia"];

// Obtenemos el directorio actual y creamos la carpeta que contendrá las carpetas con los archivos
$dir_actual = getcwd();
$barra_directorio = $funciones->dameBarraDirectorio();
$dir_documentacion = $funciones->dameRutaDocumentacionBasicos();
$dir_uploads = $dir_actual.$barra_directorio."uploads";

$dir_actual = $dir_documentacion;
include("../basicos/preparar_documentacion_referencias.php");

// Cambiamos el directorio para que pueda guardar la carpeta que hemos creado
chdir($dir_documentacion);

// Comprimimos la carpeta y generamos el zip
$filename = $nombre_dir_referencia.".zip";
$zip = new PclZip($filename);
$zip->create($nombre_dir_referencia);

// Llamada para abrir o descargar el zip
header("Content-Type: application/zip");
header("Content-Disposition: attachment; filename=".$filename);
header("Expires: 0");
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".filesize($filename));
header("Pragma: public");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private, false");
header("Content-Description: File Transfer");
readfile($filename);

// Eliminamos la carpeta creada con sus archivos
$funciones->eliminarDir($dir_documentacion_referencia);
// Eliminamos el zip temporal
unlink($filename);
?>