<?php 
// Este fichero prepara toda la documentación adjunta de una plantilla para descargar
$dir_actual = getcwd();
$barra_directorio = $funciones->dameBarraDirectorio();
$dir_documentacion = $funciones->dameRutaDocumentacionBasicos();
$dir_mecanica = $dir_actual.$barra_directorio."mecanica";
$dir_uploads = $dir_actual.$barra_directorio."uploads";

// Cargamos los datos de la plantilla
$plant->cargaDatosPlantillaProductoId($id);
$nombre_plantilla = $plant->nombre;
$version_plantilla = $plant->version;
$res_perifericos = $plant->damePerifericosPlantillaProductoSinRepeticiones($id); d($res_perifericos);

$dir_documentacion_plantilla = $dir_documentacion.$barra_directorio.$nombre_plantilla."_v".$version_plantilla;
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
        $res_kits = $comp->dameKitsComponenteSinRepetir($id_periferico); // d($res_kits);
        if(!empty($res_kits)){
            $j=0;
            $kit_con_documentacion = false;
            $kit_con_referencias = false;
            while($j<count($res_kits) && !$kit_con_documentacion && !$kit_con_referencias){
                $id_kit = $res_kits[$j]["id_kit"]; //d($id_kit);
                $kit_con_documentacion = $comp->tieneDocumentacionAdjunta($id_kit); // d($kit_con_documentacion);
                $res_id_refs_kit = $comp->dameIdsReferenciaComponentePorProveedor($id_kit,$id_proveedor);
                $kit_con_referencias = $ref->tieneDocumentacionAdjuntaComponente($res_id_refs_kit); // d($kit_con_referencias);
                $j++;
            }
            $periferico_con_kits = $kit_con_documentacion || $kit_con_referencias;
        }
        else $periferico_con_kits = false;

        // Comprobamos si el periférico tiene referencias con documentación
        $res_id_refs_periferico = $comp->dameIdsReferenciaComponentePorProveedor($id_periferico,$id_proveedor);
        $periferico_con_referencias = $ref->tieneDocumentacionAdjuntaComponente($res_id_refs_periferico);

        $periferico_no_vacio = $periferico_con_documentacion || $periferico_con_kits || $periferico_con_referencias;
        if($periferico_no_vacio){
            if(!file_exists($dir_perifericos)) mkdir($dir_perifericos, 0700);
            $per->cargaDatosPerifericoId($id_periferico);
            $nombre_periferico = $per->periferico;
            $version_periferico = $per->version;
            $dir_periferico_actual = $dir_perifericos.$barra_directorio.$nombre_periferico."_v".$version_periferico;

            if(!file_exists($dir_periferico_actual)) mkdir($dir_periferico_actual, 0700);
            if($periferico_con_documentacion){
                $res_documentacion_mecanica = $comp->dameArchivosComponente($id_periferico);
                $dir_documentos_componente = $dir_periferico_actual.$barra_directorio."DOCUMENTOS";
                include("../basicos/preparar_documentacion_mecanica.php");
            }
            if($periferico_con_kits){

            }
            if($periferico_con_referencias){

            }

            // d($id_periferico);
            // d($periferico_con_documentacion);
            // d($periferico_con_kits);
            // d($periferico_con_referencias);
        }
    }
}



// Generar el partlist
// include("../basicos/preparar_part_list_plantilla.php");




/*






// Añadimos toda la documentación del periférico
$res_documentacion_mecanica = $comp->dameArchivosComponente($id);
$dir_documentos_componente = $dir_documentos_periferico;
include("../basicos/preparar_documentacion_mecanica.php");

// Ahora añadimos todos los kits con su documentación y la de sus referencias
if(!empty($res_kits)){
    for($i=0;$i<count($res_kits);$i++){
        $id_kit = $res_kits[$i]["id_kit"];
        $res_documentacion_mecanica = $comp->dameArchivosComponente($id_kit);
        $res_id_refs_kit = $comp->dameIdsReferenciaComponentePorProveedor($id_kit,$id_proveedor);
        $kit_con_documentacion = !empty($res_documentacion_mecanica);
        $kit_con_referencias = $ref->tieneDocumentacionAdjuntaComponente($res_id_refs_kit);
        $kit_no_vacio = ($kit_con_documentacion || $kit_con_referencias);

        if($kit_no_vacio){
            if(!file_exists($dir_kits)) mkdir($dir_kits, 0700);
            $kit->cargaDatosKitId($id_kit);
            $nombre_kit = $kit->kit;
            $version_kit = $kit->version;
            $dir_kit_actual = $dir_kits.$barra_directorio.$nombre_kit."_v".$version_kit;

            $dir_actual = $dir_kit_actual;
            if(!file_exists($dir_actual)) mkdir($dir_kit_actual, 0700);

            if($kit_con_documentacion){
                $dir_documentos_componente = $dir_kit_actual.$barra_directorio."DOCUMENTOS";
                include("../basicos/preparar_documentacion_mecanica.php");
                $dir_actual = $dir_kit_actual;
            }

            if($kit_con_referencias){
                $dir_referencias_componente = $dir_kit_actual.$barra_directorio."REFERENCIAS";
                $dir_actual = $dir_referencias_componente;
                // Añadimos la documentación de las referencias de los kits
                for($j=0;$j<count($res_id_refs_kit);$j++) {
                    $id_referencia = $res_id_refs_kit[$j]["id_referencia"];
                    // Añadimos la documentación de las referencias del kit
                    if (!file_exists($dir_referencias_componente)) mkdir($dir_referencias_componente, 0700);
                    include("../basicos/preparar_documentacion_referencias.php");
                    $dir_actual = $dir_referencias_componente;
                }
            }
        }
    }
}

// Añadimos la documentación de las referencias del periférico
$res_id_refs_periferico = $comp->dameIdsReferenciaComponentePorProveedor($id,$id_proveedor);
$periferico_con_referencias = $ref->tieneDocumentacionAdjuntaComponente($res_id_refs_periferico);
if($periferico_con_referencias){
    for($i=0;$i<count($res_id_refs_periferico);$i++) {
        $id_referencia = $res_id_refs_periferico[$i]["id_referencia"];
        // Añadimos la documentación de las referencias del periférico
        if (!file_exists($dir_referencias)) mkdir($dir_referencias, 0700);
        $dir_actual = $dir_referencias;
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
*/
?>