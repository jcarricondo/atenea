<?php
// Este fichero prepara la documentación adjunta de los kits con sus referencias
for($n_kit=0;$n_kit<count($res_kits);$n_kit++){
    $id_kit = $res_kits[$n_kit]["id_kit"];
    $res_documentacion_mecanica = $comp->dameArchivosComponente($id_kit);
    $res_id_refs_kit = $comp->dameIdsReferenciaComponentePorProveedor($id_kit,$id_proveedor);
    $kit_con_documentacion = !empty($res_documentacion_mecanica);
    $kit_con_referencias = $ref->tieneDocumentacionAdjuntaComponente($res_id_refs_kit);
    $kit_no_vacio = ($kit_con_documentacion || $kit_con_referencias);

    if($kit_no_vacio){
        $kit->cargaDatosKitId($id_kit);
        $nombre_kit = $id_kit."_".$kit->kit;
        $version_kit = $kit->version;
        $dir_kit_actual = str_replace("/", "_",$dir_periferico_kits.$barra_directorio.$nombre_kit."_v".$version_kit);

        if(!file_exists($dir_kit_actual)) mkdir($dir_kit_actual, 0700);

        if($kit_con_documentacion){
            $dir_documentos_componente = $dir_kit_actual.$barra_directorio."DOCUMENTOS";
            include("../basicos/preparar_documentacion_mecanica.php");
        }
        if($kit_con_referencias){
            $dir_referencias_componente = $dir_kit_actual.$barra_directorio."REFERENCIAS";
            $dir_actual = $dir_referencias_componente;
            // Añadimos la documentación de las referencias de los kits
            for($n_ref_kit=0;$n_ref_kit<count($res_id_refs_kit);$n_ref_kit++) {
                $id_referencia = $res_id_refs_kit[$n_ref_kit]["id_referencia"];
                // Añadimos la documentación de las referencias del kit
                if (!file_exists($dir_referencias_componente)) mkdir($dir_referencias_componente, 0700);
                include("../basicos/preparar_documentacion_referencias.php");
                $dir_actual = $dir_referencias_componente;
            }
        }
    }
}
?>