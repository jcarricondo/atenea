<?php 
// Este fichero prepara toda la documentación adjunta de un periférico para descargar
$dir_actual = getcwd();
$barra_directorio = $funciones->dameBarraDirectorio();
$dir_documentacion = $funciones->dameRutaDocumentacionBasicos();
$dir_mecanica = $dir_actual.$barra_directorio."mecanica";
$dir_uploads = $dir_actual.$barra_directorio."uploads";

// Cargamos los datos del periferico
$per->cargaDatosPerifericoId($id);
$nombre_periferico = $per->periferico;
$version_periferico = $per->version;
$res_kits = $comp->dameKitsComponenteSinRepetir($id);

$dir_documentacion_periferico = str_replace("/", "_",$dir_documentacion.$barra_directorio.$nombre_periferico."_v".$version_periferico);
$dir_documentos_periferico = $dir_documentacion_periferico.$barra_directorio."DOCUMENTOS";
$dir_periferico_referencias = $dir_documentacion_periferico.$barra_directorio."REFERENCIAS";

// Creamos el directorio del periférico donde irá toda su documentación
if(!file_exists($dir_documentacion_periferico)) mkdir($dir_documentacion_periferico, 0700);

// Comprobamos si el periférico tiene documentación
$periferico_con_documentacion = $comp->tieneDocumentacionAdjunta($id);

// Comprobamos si alguno de los kits del periférico tiene documentación o alguna de sus referencias
$res_kits = $comp->dameKitsComponenteSinRepetir($id);
if(!empty($res_kits)){
    $i=0;
    $kit_con_documentacion = false;
    $kit_con_referencias = false;
    while($i<count($res_kits) && !$kit_con_documentacion && !$kit_con_referencias){
        $id_kit = $res_kits[$i]["id_kit"];
        $kit_con_documentacion = $comp->tieneDocumentacionAdjunta($id_kit);
        $res_id_refs_kit = $comp->dameIdsReferenciaComponentePorProveedor($id_kit,$id_proveedor);
        $kit_con_referencias = $ref->tieneDocumentacionAdjuntaComponente($res_id_refs_kit);
        $i++;
    }
    $periferico_con_kits = $kit_con_documentacion || $kit_con_referencias;
}
else $periferico_con_kits = false;

// Comprobamos si el periférico tiene referencias con documentación
$res_id_refs_periferico = $comp->dameIdsReferenciaComponentePorProveedor($id,$id_proveedor);
$periferico_con_referencias = $ref->tieneDocumentacionAdjuntaComponente($res_id_refs_periferico);

if($periferico_con_documentacion){
    // Añadimos toda la documentación del periférico
    $res_documentacion_mecanica = $comp->dameArchivosComponente($id);
    $dir_documentos_componente = $dir_documentos_periferico;
    include("../basicos/preparar_documentacion_mecanica.php");
}

if($periferico_con_kits){
    $dir_periferico_kits = $dir_documentacion_periferico.$barra_directorio."KITS";
    if(!file_exists($dir_periferico_kits)) mkdir($dir_periferico_kits, 0700);
    include("../basicos/preparar_documentacion_kits.php");
}

if($periferico_con_referencias){
    // Añadimos la documentación de las referencias del periférico
    $dir_periferico_referencias = $dir_documentacion_periferico.$barra_directorio."REFERENCIAS";
    if(!file_exists($dir_periferico_referencias)) mkdir($dir_periferico_referencias, 0700);
    for($i=0;$i<count($res_id_refs_periferico);$i++) {
        $id_referencia = $res_id_refs_periferico[$i]["id_referencia"];
        // Añadimos la documentación de las referencias del periférico
        $dir_actual = $dir_periferico_referencias;
        include("../basicos/preparar_documentacion_referencias.php");
    }
}

// Generar el partlist
include("../basicos/preparar_part_list_periferico.php");

// Cambiamos el directorio para que pueda guardar la carpeta que hemos creado
chdir($dir_documentacion);

// Comprimimos la carpeta y generamos el zip
$filename = $nombre_periferico."_v".$version_periferico.".zip";
$zip = new PclZip($filename);
$zip->create($nombre_periferico."_v".$version_periferico);

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
$funciones->eliminarDir($dir_documentacion_periferico);
// Eliminamos el zip temporal
unlink($filename);
?>