<?php
// Este fichero prepara toda la documentación adjunta de una plantilla para descargar
set_time_limit(36000);
$dir_actual = getcwd();
$barra_directorio = $funciones->dameBarraDirectorio();
$dir_documentacion = $funciones->dameRutaDocumentacionBasicos();
$dir_mecanica = $dir_actual.$barra_directorio."mecanica";
$dir_uploads = $dir_actual.$barra_directorio."uploads";

// Cargamos los datos de la plantilla
$plant->cargaDatosPlantillaProductoId($id);
$nombre_plantilla = strtoupper($plant->nombre);
$version_plantilla = $plant->version;
$nombre_final_plantilla = $funciones->quitarCaracteresNoPermitidosCarpeta($nombre_plantilla."_v".$version_plantilla);
$res_perifericos = $plant->damePerifericosPlantillaProductoSinRepeticiones($id);

$dir_documentacion_plantilla = $dir_documentacion.$barra_directorio.$nombre_final_plantilla;
$dir_perifericos = $dir_documentacion_plantilla.$barra_directorio."PERIFERICOS";

// Creamos el directorio de la plantilla donde irá toda su documentación
if(!file_exists($dir_documentacion_plantilla)) mkdir($dir_documentacion_plantilla, 0700);

// Ahora añadimos todos los periféricos con su documentación, la de sus kits y la de las referencias de ambos
if(!empty($res_perifericos)) {
    for($i=0;$i<count($res_perifericos);$i++) {
        $id_periferico = $res_perifericos[$i]["id_componente"];
        // Comprobamos si el periferico tiene documentación
        $periferico_con_documentacion = $comp->tieneDocumentacionAdjunta($id_periferico);

        // Comprobamos si alguno de los kits del periférico tiene documentación o alguna de sus referencias
        $res_kits = $comp->dameKitsComponenteSinRepetir($id_periferico);
        if(!empty($res_kits)){
            $j=0;
            $kit_con_documentacion = false;
            $kit_con_referencias = false;
            while($j<count($res_kits) && !$kit_con_documentacion && !$kit_con_referencias){
                $id_kit = $res_kits[$j]["id_kit"];
                $kit_con_documentacion = $comp->tieneDocumentacionAdjunta($id_kit);
                $res_id_refs_kit = $comp->dameIdsReferenciaComponentePorProveedor($id_kit,$id_proveedor);
                $todas_refs_id_kit = $ref_heredada->dameTodasReferenciasIncluidasHeredadas($res_id_refs_kit);
                $kit_con_referencias = $ref->tieneDocumentacionAdjuntaReferencias($todas_refs_id_kit);
                $j++;
            }
            $periferico_con_kits = $kit_con_documentacion || $kit_con_referencias;
        }
        else $periferico_con_kits = false;

        // Comprobamos si el periférico tiene referencias con documentación
        $res_id_refs_periferico = $comp->dameIdsReferenciaComponentePorProveedor($id_periferico,$id_proveedor);
        $todas_refs_periferico = $ref_heredada->dameTodasReferenciasIncluidasHeredadas($res_id_refs_periferico);
        $periferico_con_referencias = $ref->tieneDocumentacionAdjuntaReferencias($todas_refs_periferico);
        $periferico_no_vacio = $periferico_con_documentacion || $periferico_con_kits || $periferico_con_referencias;

        if($periferico_no_vacio){
            if(!file_exists($dir_perifericos)) mkdir($dir_perifericos, 0700);
            $per->cargaDatosPerifericoId($id_periferico);
            $nombre_periferico = $id_periferico."_".$per->periferico;
            $version_periferico = $per->version;
            $nombre_final_periferico = $funciones->quitarCaracteresNoPermitidosCarpeta($nombre_periferico."_v".$version_periferico);
            $dir_periferico_actual = $dir_perifericos.$barra_directorio.$nombre_final_periferico;

            if(!file_exists($dir_periferico_actual)) mkdir($dir_periferico_actual, 0700);
            if($periferico_con_documentacion){
                $dir_documentos_componente = $dir_periferico_actual.$barra_directorio."DOCUMENTOS";
                $res_documentacion_mecanica = $comp->dameArchivosComponente($id_periferico);
                include("../basicos/preparar_documentacion_mecanica.php");
            }
            if($periferico_con_kits){
                $dir_periferico_kits = $dir_periferico_actual.$barra_directorio."KITS";
                if(!file_exists($dir_periferico_kits)) mkdir($dir_periferico_kits, 0700);
                include("../basicos/preparar_documentacion_kits.php");
            }
            if($periferico_con_referencias){
                // Añadimos la documentación de las referencias del periférico
                $dir_periferico_referencias = $dir_periferico_actual.$barra_directorio."REFERENCIAS";
                $res_id_refs_periferico = $comp->dameIdsReferenciaComponentePorProveedor($id_periferico,$id_proveedor);
                $periferico_con_referencias = $ref->tieneDocumentacionAdjuntaReferencias($res_id_refs_periferico);
                if($periferico_con_referencias){
                    for($j=0;$j<count($res_id_refs_periferico);$j++) {
                        $id_referencia = $res_id_refs_periferico[$j]["id_referencia"];
                        // Añadimos la documentación de las referencias del periférico
                        if(!file_exists($dir_periferico_referencias)) mkdir($dir_periferico_referencias, 0700);
                        $dir_actual = $dir_periferico_referencias;
                        include("../basicos/preparar_documentacion_referencias.php");
                        include("../basicos/preparar_documentacion_referencias_heredadas.php");
                    }
                }
            }
        }
    }
}

// Generar el partlist de la plantilla
include("../basicos/preparar_part_list_plantilla.php");

// Cambiamos el directorio para que pueda guardar la carpeta que hemos creado
chdir($dir_documentacion);

// Comprimimos la carpeta y generamos el zip
$filename = $nombre_final_plantilla.".zip";
$zip = new PclZip($filename);
$zip->create($nombre_final_plantilla);

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
$funciones->eliminarDir($dir_documentacion_plantilla);
// Eliminamos el zip temporal
unlink($filename);
?>