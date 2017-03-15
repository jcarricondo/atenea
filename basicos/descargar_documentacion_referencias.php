<?php 
// Este fichero prepara la documentación adjunta de referencias para descargar
$id_referencia = $_GET["id_referencia"];
$id_referencia_principal = $id_referencia;

// Obtenemos el directorio actual y creamos la carpeta que contendrá las carpetas con los archivos
$dir_actual = getcwd();
$barra_directorio = $funciones->dameBarraDirectorio();
$dir_documentacion = $funciones->dameRutaDocumentacionBasicos();
$dir_referencia_principal = $dir_documentacion.$barra_directorio.$id_referencia;
$dir_documentacion_heredadas = $dir_referencia_principal.$barra_directorio."DOCUMENTACION_HEREDADAS";
$dir_uploads = $dir_actual.$barra_directorio."uploads";

$dir_actual = $dir_documentacion;

// Incluimos toda la documentación de la referencia principal
include("../basicos/preparar_documentacion_referencias.php");

// Obtenemos todas las referencias heredadas de la referencia
$res_heredadas = $ref_heredada->dameTodasHeredadas($id_referencia);
for($i=0;$i<count($res_heredadas);$i++){
    $id_ref_heredada = $res_heredadas[$i]["id_ref_heredada"];
    $array_todas_referencias[]["id_referencia"] = $id_ref_heredada;
}
// Comprobamos si las referencias heredadas tienen documentación adjunta
$tiene_archivos = $ref->tieneDocumentacionAdjuntaReferencias($array_todas_referencias);
if($tiene_archivos){
    // Creamos el directorio de la documentación de las referencias heredadas
    if (!file_exists($dir_referencia_principal)) mkdir($dir_referencia_principal, 0700);
    if (!file_exists($dir_documentacion_heredadas)) mkdir($dir_documentacion_heredadas, 0700);
    for($i=0;$i<count($array_todas_referencias);$i++){
        $id_referencia = $array_todas_referencias[$i]["id_referencia"];
        $dir_actual = $dir_documentacion_heredadas;
        include("../basicos/preparar_documentacion_referencias.php");
    }
}

// Cambiamos el directorio para que pueda guardar la carpeta que hemos creado
chdir($dir_documentacion);

// Comprimimos la carpeta y generamos el zip
$filename = $id_referencia_principal.".zip";
$zip = new PclZip($filename);
$zip->create($id_referencia_principal);

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
$funciones->eliminarDir($dir_referencia_principal);
// Eliminamos el zip temporal
unlink($filename);
?>