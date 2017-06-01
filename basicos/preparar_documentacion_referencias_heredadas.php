<?php
// Este fichero prepara la documentación adjunta de referencias heredadas para descargar
$dir_documentacion_heredadas = $dir_documentacion_referencia.$barra_directorio."DOCUMENTACION_HEREDADAS";

// Obtenemos todas las referencias heredadas de la referencia
unset($array_todas_referencias);
$res_heredadas = $ref_heredada->dameTodasHeredadas($id_referencia);
$res_heredadas = $ref_heredada->eliminarReferenciasHeredadasDuplicadas($res_heredadas);

for($her=0;$her<count($res_heredadas);$her++){
    $id_ref_heredada = $res_heredadas[$her]["id_ref_heredada"];

    // Filtramos las referencias por proveedor
    if($id_proveedor != "0"){
        $ref->cargaDatosReferenciaId($id_ref_heredada);
        $id_proveedor_heredada = $ref->proveedor;
        if($id_proveedor == $id_proveedor_heredada){
            $array_todas_referencias[]["id_referencia"] = $id_ref_heredada;
        };
    }
    else $array_todas_referencias[]["id_referencia"] = $id_ref_heredada;
}

// Comprobamos si las referencias heredadas tienen documentación adjunta
$tiene_archivos = $ref->tieneDocumentacionAdjuntaReferencias($array_todas_referencias);
if($tiene_archivos){
    // Creamos el directorio de la documentación de las referencias heredadas
    if (!file_exists($dir_documentacion_referencia)) mkdir($dir_documentacion_referencia, 0700);
    if (!file_exists($dir_documentacion_heredadas)) mkdir($dir_documentacion_heredadas, 0700);
    for($her=0;$her<count($array_todas_referencias);$her++){
        $id_referencia = $array_todas_referencias[$her]["id_referencia"];
        $dir_actual = $dir_documentacion_heredadas;
        include("../basicos/preparar_documentacion_referencias.php");
    }
}
?>