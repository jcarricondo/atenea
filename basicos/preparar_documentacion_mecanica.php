<?php
// Este fichero prepara la documentación adjunta mecánica de un componente para descargar
// Primero añadimos toda la documentación mecanica del periférico
for($mec=0;$mec<count($res_documentacion_mecanica);$mec++){
    if(!file_exists($dir_documentos_componente)) mkdir($dir_documentos_componente, 0700);
    $dir_actual = $dir_documentos_componente;
    $dir_pdf = $dir_actual.$barra_directorio."PDF";
    $dir_dwg = $dir_actual.$barra_directorio."DWG";
    $dir_otros = $dir_actual.$barra_directorio."OTROS";

    $nombre_archivo = $res_documentacion_mecanica[$mec]["nombre_archivo"];
    $res_path_info = pathinfo($nombre_archivo);
    $extension_archivo = $res_path_info["extension"];

    // Dependiendo de la extensión copiamos el archivo en una carpeta u otra
    if($extension_archivo == "pdf" || $extension_archivo == "PDF"){
        if(!file_exists($dir_pdf)) mkdir($dir_pdf, 0700);
        $dir_actual = $dir_pdf;
    }
    else if ($extension_archivo == "dwg" || $extension_archivo == "DWG"){
        if(!file_exists($dir_dwg)) mkdir($dir_dwg, 0700);
        $dir_actual = $dir_dwg;
    }
    else {
        if(!file_exists($dir_otros)) mkdir($dir_otros, 0700);
        $dir_actual = $dir_otros;
    }

    // Copiamos el fichero de la carpeta "mecánica" en la carpeta según el tipo de extensión
    $ruta_origen_fichero = $dir_mecanica.$barra_directorio.$nombre_archivo;
    $ruta_destino_fichero = $dir_actual.$barra_directorio.$nombre_archivo;
    if(file_exists($ruta_origen_fichero))copy($ruta_origen_fichero,$ruta_destino_fichero);
}
?>