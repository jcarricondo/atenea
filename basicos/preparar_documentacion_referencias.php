<?php
// Este fichero prepara la documentación adjunta de referencias para descargar
$nombre_dir_referencia = $id_referencia;
$dir_documentacion_referencia = $dir_actual.$barra_directorio.$nombre_dir_referencia;

// Comprobamos si la referencia tiene documentacion adjunto
$res_documentacion_adjunta = $ref->dameArchivosReferencia($id_referencia);
$ref_tiene_documentacion = !empty($res_documentacion_adjunta);

if($ref_tiene_documentacion){
    // Creamos el directorio donde irán las carpetas de documentación
    if (!file_exists($dir_documentacion_referencia)) mkdir($dir_documentacion_referencia, 0700);
    $dir_actual = $dir_documentacion_referencia;
    $dir_pdf = $dir_actual.$barra_directorio."PDF";
    $dir_dwg = $dir_actual.$barra_directorio."DWG";
    $dir_otros = $dir_actual.$barra_directorio."OTROS";

    for($doc=0;$doc<count($res_documentacion_adjunta);$doc++) {
        $nombre_archivo = $res_documentacion_adjunta[$doc]["nombre_archivo"];
        $res_path_info = pathinfo($nombre_archivo);
        $extension_archivo = $res_path_info["extension"];

        // Dependiendo de la extensión copiamos el archivo en una carpeta u otra
        if($extension_archivo == "pdf" || $extension_archivo == "PDF"){
            if(!file_exists($dir_pdf)) mkdir($dir_pdf, 0700);
            $dir_actual = $dir_pdf;
        }
        else if($extension_archivo == "dwg" || $extension_archivo == "DWG") {
            if(!file_exists($dir_dwg)) mkdir($dir_dwg, 0700);
            $dir_actual = $dir_dwg;
        }
        else {
            if (!file_exists($dir_otros)) mkdir($dir_otros, 0700);
            $dir_actual = $dir_otros;
        }

        // Copiamos el fichero de la carpeta "uploads" en la carpeta según el tipo de extensión
        $ruta_origen_fichero = $dir_uploads.$barra_directorio.$nombre_archivo;
        $ruta_destino_fichero = $dir_actual.$barra_directorio.$nombre_archivo;
        if(file_exists($ruta_origen_fichero))copy($ruta_origen_fichero, $ruta_destino_fichero);
    }
}
?>