<?php
// Este fichero elimina el kit de basicos
include("../includes/sesion.php");
include("../classes/basicos/kit.class.php");
permiso(4);

$kt = new Kit();

$kt->cargaDatosKitId($_GET["id"]);
$id_componente = $kt->id_componente;
$nombre = $kt->kit;
$referencia = $kt->referencia;
$descripcion = $kt->descripcion;
$version = $kt->version;
$referencias = $kt->referencias;
	
$kt->datosNuevoKit($id_componente,$nombre,$referencia,$descripcion,$version,$referencias,"",$id_tipo,"",$estado,$prototipo);
$resultado = $kt->eliminarReferenciasKit();
if ($resultado == 7) {
	// Una vez eliminadas las referencias del kit de la tabla componentes_referencias eliminamos el kit
	$resultado = $kt->eliminar();	
	if($resultado == 6) {
		$resultado = $kt->quitarArchivoKit($id_componente);
		if($resultado == 1) {
			header("Location: kits.php?operacion_kit=eliminado");
		}
		else {
			$mensaje_error = $kt->getErrorMessage($resultado);
		}
	} 
	else {
		$mensaje_error = $kt->getErrorMessage($resultado);
	}
} 
else {
	$mensaje_error = $kt->getErrorMessage($resultado);
}
?>
