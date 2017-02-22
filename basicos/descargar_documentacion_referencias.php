<?php 
// Este fichero prepara la documentación adjunta de referencias para descargar
$id_referencia = $_GET["id_referencia"];

// Obtenemos el directorio actual y creamos la carpeta que contendrá las carpetas con los archivos
$dir_actual = getcwd();
$barra_directorio = $funciones->dameBarraDirectorio();
$dir_documentacion = $ref->dameRutaDocumentacion();
$dir_uploads = $dir_actual.$barra_directorio."uploads";

$dir_actual = $dir_documentacion;
$nombre_dir_referencia = "DOCUMENTACION_".$id_referencia;
$dir_documentacion_referencia = $dir_documentacion.$barra_directorio.$nombre_dir_referencia;

// Creamos el directorio donde irán las carpetas de documentación
if(!file_exists($dir_documentacion_referencia)) mkdir($dir_documentacion_referencia, 0700);
$dir_actual = $dir_documentacion_referencia;
$dir_pdf = $dir_actual.$barra_directorio."PDF";
$dir_dwg = $dir_actual.$barra_directorio."DWG";
$dir_otros = $dir_actual.$barra_directorio."OTROS";

// Creamos las carpetas de la documentacion adjunta en función del tipo de archivo
if(!file_exists($dir_pdf)) mkdir($dir_pdf, 0700);
if(!file_exists($dir_dwg)) mkdir($dir_dwg, 0700);
if(!file_exists($dir_otros)) mkdir($dir_otros, 0700);

// Obtenemos la documentación adjunta a la referencia
$res_documentacion_adjunta = $ref->dameArchivosReferencia($id_referencia);

for($i=0;$i<count($res_documentacion_adjunta);$i++){
    $nombre_archivo = $res_documentacion_adjunta[$i]["nombre_archivo"];
    $res_path_info = pathinfo($nombre_archivo);
    $extension_archivo = $res_path_info["extension"];

    // Dependiendo de la extensión copiamos el archivo en una carpeta u otra
    if($extension_archivo == "pdf" || $extension_archivo == "PDF") $dir_actual = $dir_pdf;
    else if ($extension_archivo == "dwg" || $extension_archivo == "DWG") $dir_actual = $dir_dwg;
    else $dir_actual = $dir_otros;

    // Copiamos el fichero de la carpeta "uploads" en la carpeta según el tipo de extensión
    $ruta_origen_fichero = $dir_uploads.$barra_directorio.$nombre_archivo;
    $ruta_destino_fichero = $dir_actual.$barra_directorio.$nombre_archivo;
    copy($ruta_origen_fichero,$ruta_destino_fichero);
}

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